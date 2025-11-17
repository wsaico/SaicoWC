<?php
/**
 * Clase Settings - Configuración del plugin
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
 * Clase Settings
 *
 * Maneja la configuración del plugin
 *
 * @since 1.0.0
 */
class Settings {

    /**
     * Constructor
     *
     * @since 1.0.0
     */
    public function __construct() {
        add_action('admin_init', array($this, 'register_settings'));
    }

    /**
     * Registrar configuraciones
     *
     * @since 1.0.0
     */
    public function register_settings() {
        register_setting(
            'saicowc_author_settings_group',
            'saicowc_author_settings',
            array($this, 'sanitize_settings')
        );
    }

    /**
     * Sanitizar configuraciones
     *
     * @since 1.0.0
     * @param array $input Input
     * @return array Sanitizado
     */
    public function sanitize_settings($input) {
        $sanitized = array();

        // Puntos
        $sanitized['points_publish_product'] = isset($input['points_publish_product']) ? absint($input['points_publish_product']) : 10;
        $sanitized['points_product_sold'] = isset($input['points_product_sold']) ? absint($input['points_product_sold']) : 5;
        $sanitized['points_new_follower'] = isset($input['points_new_follower']) ? absint($input['points_new_follower']) : 2;
        $sanitized['points_featured_product'] = isset($input['points_featured_product']) ? absint($input['points_featured_product']) : 15;

        // Notificaciones
        $sanitized['enable_notifications'] = isset($input['enable_notifications']) ? (bool) $input['enable_notifications'] : false;
        $sanitized['enable_email_new_product'] = isset($input['enable_email_new_product']) ? (bool) $input['enable_email_new_product'] : false;
        $sanitized['enable_email_level_up'] = isset($input['enable_email_level_up']) ? (bool) $input['enable_email_level_up'] : false;

        // SMTP (opcional para futura implementación)
        $sanitized['smtp_host'] = isset($input['smtp_host']) ? sanitize_text_field($input['smtp_host']) : '';
        $sanitized['smtp_port'] = isset($input['smtp_port']) ? absint($input['smtp_port']) : 587;
        $sanitized['smtp_username'] = isset($input['smtp_username']) ? sanitize_text_field($input['smtp_username']) : '';
        $sanitized['smtp_password'] = isset($input['smtp_password']) ? $input['smtp_password'] : '';
        $sanitized['smtp_from_email'] = isset($input['smtp_from_email']) ? sanitize_email($input['smtp_from_email']) : '';
        $sanitized['smtp_from_name'] = isset($input['smtp_from_name']) ? sanitize_text_field($input['smtp_from_name']) : '';

        // Niveles de badges (mantener estructura)
        if (isset($input['badge_levels']) && is_array($input['badge_levels'])) {
            $sanitized['badge_levels'] = $input['badge_levels'];
        }

        return $sanitized;
    }

    /**
     * Renderizar página de configuración
     *
     * @since 1.0.0
     */
    public function render_settings_page() {
        // Guardar configuración si se envió el formulario
        if (isset($_POST['saicowc_author_save_settings'])) {
            check_admin_referer('saicowc_author_settings_nonce');

            $settings = array();

            // Puntos
            $settings['points_publish_product'] = isset($_POST['points_publish_product']) ? absint($_POST['points_publish_product']) : 10;
            $settings['points_product_sold'] = isset($_POST['points_product_sold']) ? absint($_POST['points_product_sold']) : 5;
            $settings['points_new_follower'] = isset($_POST['points_new_follower']) ? absint($_POST['points_new_follower']) : 2;
            $settings['points_featured_product'] = isset($_POST['points_featured_product']) ? absint($_POST['points_featured_product']) : 15;

            // Notificaciones
            $settings['enable_notifications'] = isset($_POST['enable_notifications']);
            $settings['enable_email_new_product'] = isset($_POST['enable_email_new_product']);
            $settings['enable_email_level_up'] = isset($_POST['enable_email_level_up']);

            // SMTP
            $settings['smtp_host'] = isset($_POST['smtp_host']) ? sanitize_text_field($_POST['smtp_host']) : '';
            $settings['smtp_port'] = isset($_POST['smtp_port']) ? absint($_POST['smtp_port']) : 587;
            $settings['smtp_username'] = isset($_POST['smtp_username']) ? sanitize_text_field($_POST['smtp_username']) : '';
            $settings['smtp_password'] = isset($_POST['smtp_password']) ? $_POST['smtp_password'] : '';
            $settings['smtp_from_email'] = isset($_POST['smtp_from_email']) ? sanitize_email($_POST['smtp_from_email']) : '';
            $settings['smtp_from_name'] = isset($_POST['smtp_from_name']) ? sanitize_text_field($_POST['smtp_from_name']) : '';

            update_option('saicowc_author_settings', $settings);

            echo '<div class="notice notice-success"><p>' . esc_html__('Configuración guardada correctamente', 'saicowc-author') . '</p></div>';
        }

        $settings = get_option('saicowc_author_settings', array());
        ?>
        <div class="wrap saicowc-author-admin">
            <h1><?php esc_html_e('Configuración - Author System', 'saicowc-author'); ?></h1>

            <form method="post" action="">
                <?php wp_nonce_field('saicowc_author_settings_nonce'); ?>

                <div class="saicowc-settings-tabs">
                    <nav class="nav-tab-wrapper">
                        <a href="#points" class="nav-tab nav-tab-active"><?php esc_html_e('Puntos', 'saicowc-author'); ?></a>
                        <a href="#notifications" class="nav-tab"><?php esc_html_e('Notificaciones', 'saicowc-author'); ?></a>
                        <a href="#smtp" class="nav-tab"><?php esc_html_e('SMTP', 'saicowc-author'); ?></a>
                        <a href="#badges" class="nav-tab"><?php esc_html_e('Badges', 'saicowc-author'); ?></a>
                    </nav>

                    <!-- Tab: Puntos -->
                    <div id="points" class="settings-tab-content active">
                        <h2><?php esc_html_e('Configuración de Puntos', 'saicowc-author'); ?></h2>
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="points_publish_product"><?php esc_html_e('Puntos por publicar producto', 'saicowc-author'); ?></label>
                                </th>
                                <td>
                                    <input type="number" id="points_publish_product" name="points_publish_product"
                                           value="<?php echo esc_attr($settings['points_publish_product'] ?? 10); ?>"
                                           min="0" max="1000" class="small-text">
                                    <p class="description"><?php esc_html_e('Puntos otorgados cuando un autor publica un nuevo producto', 'saicowc-author'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="points_product_sold"><?php esc_html_e('Puntos por producto vendido', 'saicowc-author'); ?></label>
                                </th>
                                <td>
                                    <input type="number" id="points_product_sold" name="points_product_sold"
                                           value="<?php echo esc_attr($settings['points_product_sold'] ?? 5); ?>"
                                           min="0" max="1000" class="small-text">
                                    <p class="description"><?php esc_html_e('Puntos otorgados cuando se vende un producto del autor', 'saicowc-author'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="points_new_follower"><?php esc_html_e('Puntos por nuevo seguidor', 'saicowc-author'); ?></label>
                                </th>
                                <td>
                                    <input type="number" id="points_new_follower" name="points_new_follower"
                                           value="<?php echo esc_attr($settings['points_new_follower'] ?? 2); ?>"
                                           min="0" max="1000" class="small-text">
                                    <p class="description"><?php esc_html_e('Puntos otorgados cuando un usuario sigue al autor', 'saicowc-author'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="points_featured_product"><?php esc_html_e('Puntos por producto destacado', 'saicowc-author'); ?></label>
                                </th>
                                <td>
                                    <input type="number" id="points_featured_product" name="points_featured_product"
                                           value="<?php echo esc_attr($settings['points_featured_product'] ?? 15); ?>"
                                           min="0" max="1000" class="small-text">
                                    <p class="description"><?php esc_html_e('Puntos otorgados cuando un producto es marcado como destacado (opcional)', 'saicowc-author'); ?></p>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- Tab: Notificaciones -->
                    <div id="notifications" class="settings-tab-content">
                        <h2><?php esc_html_e('Configuración de Notificaciones', 'saicowc-author'); ?></h2>
                        <table class="form-table">
                            <tr>
                                <th scope="row"><?php esc_html_e('Sistema de notificaciones', 'saicowc-author'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="enable_notifications" value="1"
                                               <?php checked($settings['enable_notifications'] ?? true); ?>>
                                        <?php esc_html_e('Habilitar sistema de notificaciones', 'saicowc-author'); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e('Email - Nuevo producto', 'saicowc-author'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="enable_email_new_product" value="1"
                                               <?php checked($settings['enable_email_new_product'] ?? true); ?>>
                                        <?php esc_html_e('Enviar email cuando autor seguido publica un producto', 'saicowc-author'); ?>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php esc_html_e('Email - Subida de nivel', 'saicowc-author'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="enable_email_level_up" value="1"
                                               <?php checked($settings['enable_email_level_up'] ?? true); ?>>
                                        <?php esc_html_e('Enviar email cuando autor sube de nivel', 'saicowc-author'); ?>
                                    </label>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- Tab: SMTP -->
                    <div id="smtp" class="settings-tab-content">
                        <h2><?php esc_html_e('Configuración SMTP (Opcional)', 'saicowc-author'); ?></h2>
                        <p class="description"><?php esc_html_e('Configura un servidor SMTP para enviar emails. Deja en blanco para usar la función wp_mail() por defecto.', 'saicowc-author'); ?></p>
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="smtp_host"><?php esc_html_e('Host SMTP', 'saicowc-author'); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="smtp_host" name="smtp_host"
                                           value="<?php echo esc_attr($settings['smtp_host'] ?? ''); ?>" class="regular-text">
                                    <p class="description"><?php esc_html_e('Ejemplo: smtp.gmail.com', 'saicowc-author'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="smtp_port"><?php esc_html_e('Puerto SMTP', 'saicowc-author'); ?></label>
                                </th>
                                <td>
                                    <input type="number" id="smtp_port" name="smtp_port"
                                           value="<?php echo esc_attr($settings['smtp_port'] ?? 587); ?>" class="small-text">
                                    <p class="description"><?php esc_html_e('Común: 587 (TLS) o 465 (SSL)', 'saicowc-author'); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="smtp_username"><?php esc_html_e('Usuario SMTP', 'saicowc-author'); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="smtp_username" name="smtp_username"
                                           value="<?php echo esc_attr($settings['smtp_username'] ?? ''); ?>" class="regular-text">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="smtp_password"><?php esc_html_e('Contraseña SMTP', 'saicowc-author'); ?></label>
                                </th>
                                <td>
                                    <input type="password" id="smtp_password" name="smtp_password"
                                           value="<?php echo esc_attr($settings['smtp_password'] ?? ''); ?>" class="regular-text">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="smtp_from_email"><?php esc_html_e('Email remitente', 'saicowc-author'); ?></label>
                                </th>
                                <td>
                                    <input type="email" id="smtp_from_email" name="smtp_from_email"
                                           value="<?php echo esc_attr($settings['smtp_from_email'] ?? ''); ?>" class="regular-text">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="smtp_from_name"><?php esc_html_e('Nombre remitente', 'saicowc-author'); ?></label>
                                </th>
                                <td>
                                    <input type="text" id="smtp_from_name" name="smtp_from_name"
                                           value="<?php echo esc_attr($settings['smtp_from_name'] ?? ''); ?>" class="regular-text">
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- Tab: Badges -->
                    <div id="badges" class="settings-tab-content">
                        <h2><?php esc_html_e('Niveles de Badges', 'saicowc-author'); ?></h2>
                        <p class="description"><?php esc_html_e('Los niveles de badges están configurados por defecto. Esta sección es informativa.', 'saicowc-author'); ?></p>

                        <table class="wp-list-table widefat fixed striped">
                            <thead>
                                <tr>
                                    <th><?php esc_html_e('Nivel', 'saicowc-author'); ?></th>
                                    <th><?php esc_html_e('Título', 'saicowc-author'); ?></th>
                                    <th><?php esc_html_e('Rango de Puntos', 'saicowc-author'); ?></th>
                                    <th><?php esc_html_e('Icono', 'saicowc-author'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $gamification = saicowc_author_system()->gamification;
                                $levels = $gamification->get_badge_levels();
                                foreach ($levels as $level => $data):
                                ?>
                                <tr>
                                    <td><strong><?php echo esc_html(ucfirst($level)); ?></strong></td>
                                    <td><?php echo esc_html($data['title']); ?></td>
                                    <td><?php echo esc_html($data['min'] . ' - ' . $data['max']); ?> puntos</td>
                                    <td><?php echo $gamification->get_badge_svg($level, 32); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <p class="submit">
                    <button type="submit" name="saicowc_author_save_settings" class="button button-primary">
                        <?php esc_html_e('Guardar Cambios', 'saicowc-author'); ?>
                    </button>
                </p>
            </form>
        </div>

        <script>
        jQuery(document).ready(function($) {
            // Tabs functionality
            $('.nav-tab').on('click', function(e) {
                e.preventDefault();
                var target = $(this).attr('href');

                $('.nav-tab').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');

                $('.settings-tab-content').removeClass('active');
                $(target).addClass('active');
            });
        });
        </script>

        <style>
        .settings-tab-content { display: none; padding: 20px; background: #fff; border: 1px solid #ccc; border-top: 0; }
        .settings-tab-content.active { display: block; }
        .saicowc-settings-tabs { margin-top: 20px; }
        </style>
        <?php
    }
}
