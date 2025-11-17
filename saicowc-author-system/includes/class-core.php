<?php
/**
 * Clase Core - Funcionalidad central del plugin
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
 * Clase Core
 *
 * Maneja la funcionalidad central del plugin incluyendo:
 * - Carga de assets (CSS/JS)
 * - Integración con theme SaicoWC
 * - Renderizado de templates
 *
 * @since 1.0.0
 */
class Core {

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
        // Enqueue assets
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'), 20);

        // Integración con theme SaicoWC
        add_action('wp', array($this, 'integrate_with_theme'), 20);

        // Agregar badge al nombre del autor (everywhere)
        add_filter('the_author', array($this, 'add_badge_to_author_name'), 10, 1);
        add_filter('get_the_author_display_name', array($this, 'add_badge_to_author_name_html'), 10, 2);
    }

    /**
     * Cargar assets (CSS y JavaScript)
     *
     * @since 1.0.0
     */
    public function enqueue_assets() {
        $version = SAICOWC_AUTHOR_VERSION;

        // CSS
        wp_enqueue_style(
            'saicowc-author-system',
            SAICOWC_AUTHOR_URL . 'assets/css/author-system.css',
            array(),
            $version
        );

        // JavaScript
        wp_enqueue_script(
            'saicowc-author-system',
            SAICOWC_AUTHOR_URL . 'assets/js/author-system.js',
            array('jquery'),
            $version,
            true
        );

        // Localizar script con datos necesarios
        wp_localize_script('saicowc-author-system', 'saicowcAuthorData', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('saicowc_author_nonce'),
            'i18n' => array(
                'follow' => __('Seguir', 'saicowc-author'),
                'following' => __('Siguiendo', 'saicowc-author'),
                'unfollow' => __('Dejar de seguir', 'saicowc-author'),
                'loading' => __('Cargando...', 'saicowc-author'),
                'error' => __('Error. Inténtalo de nuevo.', 'saicowc-author'),
                'login_required' => __('Debes iniciar sesión para seguir autores', 'saicowc-author'),
            ),
        ));
    }

    /**
     * Integración con theme SaicoWC
     * Añade botones de seguir y badges en las ubicaciones correctas
     *
     * @since 1.0.0
     */
    public function integrate_with_theme() {
        // Single Product - Después del nombre del autor en stats-author-info
        add_action('woocommerce_single_product_summary', array($this, 'render_single_product_integration'), 6);

        // Página de Autor - Después del nombre en autor-nombre
        add_action('saico_author_header_name', array($this, 'render_author_page_integration'), 20);

        // Si el hook del theme no existe, usar alternativas
        if (!has_action('saico_author_header_name')) {
            // Fallback para página de autor
            add_filter('the_author_posts_link', array($this, 'add_follow_button_to_author_link'), 20, 1);
        }
    }

    /**
     * Renderizar integración en single product
     *
     * @since 1.0.0
     */
    public function render_single_product_integration() {
        if (!is_singular('product')) {
            return;
        }

        global $product;
        if (!$product) {
            return;
        }

        $author_id = get_post_field('post_author', $product->get_id());

        if (!$author_id) {
            return;
        }

        // Este contenido se añadirá mediante JavaScript en la ubicación correcta
        echo '<div id="saicowc-author-integration-data" data-author-id="' . esc_attr($author_id) . '" style="display:none;"></div>';
    }

    /**
     * Renderizar integración en página de autor
     *
     * @since 1.0.0
     */
    public function render_author_page_integration() {
        if (!is_author()) {
            return;
        }

        $author_id = get_queried_object_id();

        if (!$author_id) {
            return;
        }

        $this->render_template('author-page-integration', array(
            'author_id' => $author_id,
        ));
    }

    /**
     * Añadir badge al nombre del autor (solo texto)
     *
     * @since 1.0.0
     * @param string $display_name Nombre del autor
     * @return string
     */
    public function add_badge_to_author_name($display_name) {
        return $display_name;
    }

    /**
     * Añadir badge al nombre del autor (HTML)
     *
     * @since 1.0.0
     * @param string $display_name Nombre del autor
     * @param int $user_id ID del usuario
     * @return string
     */
    public function add_badge_to_author_name_html($display_name, $user_id) {
        if (!$user_id) {
            return $display_name;
        }

        $badge_html = $this->get_author_badge_html($user_id, 24);

        return $display_name . ' ' . $badge_html;
    }

    /**
     * Añadir botón de seguir al link del autor (fallback)
     *
     * @since 1.0.0
     * @param string $link Link del autor
     * @return string
     */
    public function add_follow_button_to_author_link($link) {
        return $link;
    }

    /**
     * Obtener HTML del badge del autor
     *
     * @since 1.0.0
     * @param int $author_id ID del autor
     * @param int $size Tamaño del badge (default: 32)
     * @return string HTML del badge
     */
    public function get_author_badge_html($author_id, $size = 32) {
        $gamification = saicowc_author_system()->gamification;
        $badge_data = $gamification->get_author_badge($author_id);

        if (!$badge_data) {
            return '';
        }

        ob_start();
        $this->render_template('author-badge', array(
            'badge_data' => $badge_data,
            'author_id' => $author_id,
            'size' => $size,
        ));
        return ob_get_clean();
    }

    /**
     * Obtener HTML del botón de seguir
     *
     * @since 1.0.0
     * @param int $author_id ID del autor
     * @return string HTML del botón
     */
    public function get_follow_button_html($author_id) {
        ob_start();
        $this->render_template('follow-button', array(
            'author_id' => $author_id,
        ));
        return ob_get_clean();
    }

    /**
     * Renderizar template
     *
     * @since 1.0.0
     * @param string $template_name Nombre del template (sin .php)
     * @param array $args Argumentos para pasar al template
     */
    public function render_template($template_name, $args = array()) {
        // Permitir override desde el theme
        $template_path = locate_template(array(
            'saicowc-author-system/' . $template_name . '.php',
        ));

        // Si no existe en el theme, usar el del plugin
        if (!$template_path) {
            $template_path = SAICOWC_AUTHOR_PATH . 'templates/' . $template_name . '.php';
        }

        // Verificar que existe
        if (!file_exists($template_path)) {
            return;
        }

        // Extraer argumentos
        if (!empty($args)) {
            extract($args); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract
        }

        /**
         * Filtro para modificar los argumentos del template
         *
         * @since 1.0.0
         * @param array $args Argumentos del template
         * @param string $template_name Nombre del template
         */
        $args = apply_filters('saicowc_author_template_args', $args, $template_name);

        /**
         * Acción antes de cargar el template
         *
         * @since 1.0.0
         * @param string $template_name Nombre del template
         * @param array $args Argumentos del template
         */
        do_action('saicowc_author_before_template', $template_name, $args);

        // Cargar template
        include $template_path;

        /**
         * Acción después de cargar el template
         *
         * @since 1.0.0
         * @param string $template_name Nombre del template
         * @param array $args Argumentos del template
         */
        do_action('saicowc_author_after_template', $template_name, $args);
    }

    /**
     * Obtener opciones del plugin
     *
     * @since 1.0.0
     * @param string $key Clave de la opción (opcional)
     * @param mixed $default Valor por defecto
     * @return mixed
     */
    public static function get_option($key = null, $default = null) {
        $options = get_option('saicowc_author_settings', array());

        if ($key === null) {
            return $options;
        }

        return isset($options[$key]) ? $options[$key] : $default;
    }

    /**
     * Guardar opciones del plugin
     *
     * @since 1.0.0
     * @param string $key Clave de la opción
     * @param mixed $value Valor
     * @return bool
     */
    public static function update_option($key, $value) {
        $options = get_option('saicowc_author_settings', array());
        $options[$key] = $value;
        return update_option('saicowc_author_settings', $options);
    }

    /**
     * Sanitizar datos de entrada
     *
     * @since 1.0.0
     * @param mixed $data Datos a sanitizar
     * @param string $type Tipo de sanitización
     * @return mixed
     */
    public static function sanitize($data, $type = 'text') {
        switch ($type) {
            case 'int':
                return absint($data);

            case 'email':
                return sanitize_email($data);

            case 'url':
                return esc_url_raw($data);

            case 'html':
                return wp_kses_post($data);

            case 'array':
                return is_array($data) ? array_map('sanitize_text_field', $data) : array();

            case 'text':
            default:
                return sanitize_text_field($data);
        }
    }

    /**
     * Verificar nonce de seguridad
     *
     * @since 1.0.0
     * @param string $nonce Nonce a verificar
     * @param string $action Acción del nonce (default: 'saicowc_author_nonce')
     * @return bool
     */
    public static function verify_nonce($nonce, $action = 'saicowc_author_nonce') {
        return wp_verify_nonce($nonce, $action);
    }

    /**
     * Logger de errores (solo en modo debug)
     *
     * @since 1.0.0
     * @param string $message Mensaje de error
     * @param string $level Nivel de error (info, warning, error)
     */
    public static function log($message, $level = 'info') {
        if (!defined('WP_DEBUG') || !WP_DEBUG) {
            return;
        }

        if (function_exists('error_log')) {
            error_log(sprintf('[SaicoWC Author System] [%s] %s', strtoupper($level), $message));
        }
    }
}
