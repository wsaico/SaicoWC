<?php
/**
 * Clase Notifications - Sistema de notificaciones por email
 *
 * @package SaicoWC_Author_System
 * @since 1.0.0
 */

namespace SaicoWC\AuthorSystem;

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Clase Notifications
 *
 * Maneja el envío de notificaciones por email
 *
 * @since 1.0.0
 */
class Notifications {

    /**
     * Meta key para preferencias de notificación
     *
     * @var string
     */
    const META_NOTIFY_PREFS = '_saicowc_notify_preferences';

    /**
     * Constructor
     *
     * @since 1.0.0
     */
    public function __construct() {
        $this->init_hooks();
    }

    /**
     * Inicializar hooks
     *
     * @since 1.0.0
     */
    private function init_hooks() {
        // Notificar cuando autor seguido publica producto
        add_action('transition_post_status', array($this, 'notify_new_product'), 20, 3);

        // Notificar cuando autor sube de nivel
        add_action('saicowc_author_level_up', array($this, 'notify_level_up'), 10, 4);

        // Cron para envío de notificaciones en cola
        add_action('saicowc_author_send_notifications', array($this, 'send_queued_notifications'));

        // Programar cron si no está programado
        if (!wp_next_scheduled('saicowc_author_send_notifications')) {
            wp_schedule_event(time(), 'hourly', 'saicowc_author_send_notifications');
        }
    }

    /**
     * Notificar cuando un autor seguido publica un producto
     *
     * @since 1.0.0
     * @param string $new_status Nuevo estado
     * @param string $old_status Estado anterior
     * @param \WP_Post $post Post
     */
    public function notify_new_product($new_status, $old_status, $post) {
        if ($post->post_type !== 'product') {
            return;
        }

        if ($new_status !== 'publish' || $old_status === 'publish') {
            return;
        }

        // Verificar si las notificaciones están habilitadas
        if (!Core::get_option('enable_email_new_product', true)) {
            return;
        }

        $author_id = $post->post_author;
        $follow = saicowc_author_system()->follow;
        $followers = $follow->get_followers($author_id);

        if (empty($followers)) {
            return;
        }

        // Encolar notificaciones
        foreach ($followers as $follower_id) {
            // Verificar preferencias del usuario
            if (!$this->user_wants_notification($follower_id, 'new_product')) {
                continue;
            }

            $this->queue_notification($follower_id, 'new_product', array(
                'author_id' => $author_id,
                'product_id' => $post->ID,
            ));
        }
    }

    /**
     * Notificar cuando un autor sube de nivel
     *
     * @since 1.0.0
     * @param int $author_id ID del autor
     * @param string $new_level Nuevo nivel
     * @param string $old_level Nivel anterior
     * @param int $new_points Puntos totales
     */
    public function notify_level_up($author_id, $new_level, $old_level, $new_points) {
        // Verificar si las notificaciones están habilitadas
        if (!Core::get_option('enable_email_level_up', true)) {
            return;
        }

        // Verificar preferencias del autor
        if (!$this->user_wants_notification($author_id, 'level_up')) {
            return;
        }

        // Enviar email al autor
        $this->send_level_up_email($author_id, $new_level, $new_points);
    }

    /**
     * Enviar email de nuevo producto a seguidor
     *
     * @since 1.0.0
     * @param int $user_id ID del usuario
     * @param array $data Datos del producto y autor
     */
    private function send_new_product_email($user_id, $data) {
        $user = get_userdata($user_id);
        $author = get_userdata($data['author_id']);
        $product = wc_get_product($data['product_id']);

        if (!$user || !$author || !$product) {
            return;
        }

        $to = $user->user_email;
        $subject = sprintf(
            /* translators: %s: nombre del autor */
            __('%s ha publicado un nuevo producto', 'saicowc-author'),
            $author->display_name
        );

        $message = $this->get_email_template('new-product', array(
            'user_name' => $user->display_name,
            'author_name' => $author->display_name,
            'author_url' => get_author_posts_url($data['author_id']),
            'product_name' => $product->get_name(),
            'product_url' => get_permalink($data['product_id']),
            'product_image' => wp_get_attachment_url($product->get_image_id()),
        ));

        $headers = array('Content-Type: text/html; charset=UTF-8');

        wp_mail($to, $subject, $message, $headers);
    }

    /**
     * Enviar email de subida de nivel
     *
     * @since 1.0.0
     * @param int $author_id ID del autor
     * @param string $new_level Nuevo nivel
     * @param int $points Puntos totales
     */
    private function send_level_up_email($author_id, $new_level, $points) {
        $user = get_userdata($author_id);

        if (!$user) {
            return;
        }

        $gamification = saicowc_author_system()->gamification;
        $badge_data = $gamification->get_author_badge($author_id);

        $to = $user->user_email;
        $subject = sprintf(
            /* translators: %s: título del nivel */
            __('¡Felicidades! Ahora eres %s', 'saicowc-author'),
            $badge_data['title']
        );

        $message = $this->get_email_template('level-up', array(
            'user_name' => $user->display_name,
            'badge_title' => $badge_data['title'],
            'badge_level' => $new_level,
            'points' => $points,
            'next_level' => isset($badge_data['next_level']) ? $badge_data['next_level'] : null,
            'next_level_points' => isset($badge_data['next_level_points']) ? $badge_data['next_level_points'] : null,
        ));

        $headers = array('Content-Type: text/html; charset=UTF-8');

        wp_mail($to, $subject, $message, $headers);
    }

    /**
     * Obtener template de email
     *
     * @since 1.0.0
     * @param string $template Nombre del template
     * @param array $args Argumentos para el template
     * @return string HTML del email
     */
    private function get_email_template($template, $args = array()) {
        extract($args); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract

        ob_start();

        $template_path = SAICOWC_AUTHOR_PATH . 'templates/emails/' . $template . '.php';

        if (file_exists($template_path)) {
            include $template_path;
        } else {
            // Template por defecto
            $this->default_email_template($args);
        }

        return ob_get_clean();
    }

    /**
     * Template de email por defecto
     *
     * @since 1.0.0
     * @param array $args Argumentos
     */
    private function default_email_template($args) {
        $site_name = get_bloginfo('name');
        $site_url = home_url('/');

        echo '<!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #fff; padding: 30px; border: 1px solid #e5e7eb; }
                .footer { background: #f9fafb; padding: 20px; text-align: center; border-radius: 0 0 8px 8px; font-size: 14px; color: #6b7280; }
                .button { display: inline-block; background: #667eea; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>' . esc_html($site_name) . '</h1>
                </div>
                <div class="content">';

        // Contenido específico según argumentos
        if (isset($args['product_name'])) {
            echo '<h2>Nuevo producto publicado</h2>';
            echo '<p>Hola ' . esc_html($args['user_name'] ?? '') . ',</p>';
            echo '<p>' . esc_html($args['author_name'] ?? '') . ' ha publicado un nuevo producto:</p>';
            echo '<h3>' . esc_html($args['product_name']) . '</h3>';
            echo '<a href="' . esc_url($args['product_url'] ?? '#') . '" class="button">Ver Producto</a>';
        } elseif (isset($args['badge_title'])) {
            echo '<h2>¡Felicidades por tu nuevo nivel!</h2>';
            echo '<p>Hola ' . esc_html($args['user_name'] ?? '') . ',</p>';
            echo '<p>Has alcanzado un nuevo nivel: <strong>' . esc_html($args['badge_title']) . '</strong></p>';
            echo '<p>Puntos totales: ' . esc_html($args['points'] ?? 0) . '</p>';
        }

        echo '</div>
                <div class="footer">
                    <p>&copy; ' . date('Y') . ' ' . esc_html($site_name) . '. Todos los derechos reservados.</p>
                    <p><a href="' . esc_url($site_url) . '">Visitar sitio web</a></p>
                </div>
            </div>
        </body>
        </html>';
    }

    /**
     * Verificar si un usuario quiere recibir un tipo de notificación
     *
     * @since 1.0.0
     * @param int $user_id ID del usuario
     * @param string $type Tipo de notificación
     * @return bool
     */
    private function user_wants_notification($user_id, $type) {
        $preferences = get_user_meta($user_id, self::META_NOTIFY_PREFS, true);

        if (!is_array($preferences)) {
            // Por defecto todas las notificaciones están habilitadas
            return true;
        }

        return isset($preferences[$type]) ? (bool) $preferences[$type] : true;
    }

    /**
     * Encolar notificación para envío posterior
     *
     * @since 1.0.0
     * @param int $user_id ID del usuario
     * @param string $type Tipo de notificación
     * @param array $data Datos adicionales
     */
    private function queue_notification($user_id, $type, $data = array()) {
        $queue = get_option('saicowc_author_notification_queue', array());

        $queue[] = array(
            'user_id' => $user_id,
            'type' => $type,
            'data' => $data,
            'queued_at' => time(),
        );

        update_option('saicowc_author_notification_queue', $queue);
    }

    /**
     * Enviar notificaciones en cola (vía cron)
     *
     * @since 1.0.0
     */
    public function send_queued_notifications() {
        $queue = get_option('saicowc_author_notification_queue', array());

        if (empty($queue)) {
            return;
        }

        $processed = array();

        foreach ($queue as $index => $notification) {
            // Procesar según tipo
            switch ($notification['type']) {
                case 'new_product':
                    $this->send_new_product_email($notification['user_id'], $notification['data']);
                    break;
            }

            $processed[] = $index;

            // Procesar máximo 50 por ejecución para evitar timeouts
            if (count($processed) >= 50) {
                break;
            }
        }

        // Remover notificaciones procesadas
        foreach ($processed as $index) {
            unset($queue[$index]);
        }

        update_option('saicowc_author_notification_queue', array_values($queue));
    }
}
