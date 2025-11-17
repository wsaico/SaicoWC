<?php
/**
 * Plugin Name: SaicoWC Author Follow & Badges
 * Plugin URI: https://wsaico.com
 * Description: Sistema profesional de seguir autores y gamificación con badges para WooCommerce. Integrado con theme SaicoWC.
 * Version: 1.0.0
 * Author: Wilber Saico
 * Author URI: https://wsaico.com
 * Text Domain: saicowc-author
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 8.5
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package SaicoWC_Author_System
 * @version 1.0.0
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

namespace SaicoWC\AuthorSystem {

// Definir constantes del plugin
define('SAICOWC_AUTHOR_VERSION', '1.0.0');
define('SAICOWC_AUTHOR_FILE', __FILE__);
define('SAICOWC_AUTHOR_PATH', plugin_dir_path(__FILE__));
define('SAICOWC_AUTHOR_URL', plugin_dir_url(__FILE__));
define('SAICOWC_AUTHOR_BASENAME', plugin_basename(__FILE__));

/**
 * Clase principal del plugin
 * Patrón Singleton para asegurar una única instancia
 *
 * @since 1.0.0
 */
final class Plugin {

    /**
     * Instancia única del plugin
     *
     * @var Plugin
     */
    private static $instance = null;

    /**
     * Instancia de la clase Core
     *
     * @var Core
     */
    public $core;

    /**
     * Instancia de la clase Follow
     *
     * @var Follow
     */
    public $follow;

    /**
     * Instancia de la clase Gamification
     *
     * @var Gamification
     */
    public $gamification;

    /**
     * Instancia de la clase AJAX
     *
     * @var AJAX
     */
    public $ajax;

    /**
     * Obtener instancia única (Singleton)
     *
     * @return Plugin
     */
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor privado para Singleton
     */
    private function __construct() {
        $this->init_hooks();
        $this->load_dependencies();
        $this->init_components();
    }

    /**
     * Prevenir clonación
     */
    private function __clone() {}

    /**
     * Prevenir unserialización
     */
    public function __wakeup() {}

    /**
     * Inicializar hooks principales
     *
     * @since 1.0.0
     */
    private function init_hooks() {
        // Hook de activación
        register_activation_hook(SAICOWC_AUTHOR_FILE, array($this, 'activate'));

        // Hook de desactivación
        register_deactivation_hook(SAICOWC_AUTHOR_FILE, array($this, 'deactivate'));

        // Inicializar plugin después de WordPress
        add_action('plugins_loaded', array($this, 'on_plugins_loaded'), 10);

        // Cargar traducciones
        add_action('init', array($this, 'load_textdomain'));
    }

    /**
     * Cargar dependencias del plugin
     *
     * @since 1.0.0
     */
    private function load_dependencies() {
        // Clases core
        require_once SAICOWC_AUTHOR_PATH . 'includes/class-core.php';
        require_once SAICOWC_AUTHOR_PATH . 'includes/class-follow.php';
        require_once SAICOWC_AUTHOR_PATH . 'includes/class-gamification.php';
        require_once SAICOWC_AUTHOR_PATH . 'includes/class-ajax.php';
        require_once SAICOWC_AUTHOR_PATH . 'includes/class-notifications.php';
        require_once SAICOWC_AUTHOR_PATH . 'includes/class-shortcodes.php';
        require_once SAICOWC_AUTHOR_PATH . 'includes/class-widgets.php';

        // Admin
        if (is_admin()) {
            require_once SAICOWC_AUTHOR_PATH . 'admin/class-admin.php';
            require_once SAICOWC_AUTHOR_PATH . 'admin/class-settings.php';
        }
    }

    /**
     * Inicializar componentes del plugin
     *
     * @since 1.0.0
     */
    private function init_components() {
        $this->core = new Core();
        $this->follow = new Follow();
        $this->gamification = new Gamification();
        $this->ajax = new AJAX();

        // Inicializar componentes adicionales
        new Notifications();
        new Shortcodes();
        new Widgets_Manager();

        // Admin
        if (is_admin()) {
            new Admin();
        }
    }

    /**
     * Ejecutar al cargar plugins
     *
     * @since 1.0.0
     */
    public function on_plugins_loaded() {
        // Verificar dependencias
        if (!$this->check_dependencies()) {
            add_action('admin_notices', array($this, 'dependency_notice'));
            return;
        }

        /**
         * Hook cuando el plugin está completamente cargado
         *
         * @since 1.0.0
         */
        do_action('saicowc_author_system_loaded');
    }

    /**
     * Verificar dependencias del plugin
     *
     * @since 1.0.0
     * @return bool
     */
    private function check_dependencies() {
        // Verificar WordPress
        if (version_compare(get_bloginfo('version'), '5.8', '<')) {
            return false;
        }

        // Verificar PHP
        if (version_compare(PHP_VERSION, '7.4', '<')) {
            return false;
        }

        // Verificar WooCommerce
        if (!class_exists('WooCommerce')) {
            return false;
        }

        return true;
    }

    /**
     * Mostrar aviso de dependencias faltantes
     *
     * @since 1.0.0
     */
    public function dependency_notice() {
        $message = sprintf(
            /* translators: %s: plugin name */
            __('<strong>%s</strong> requiere WordPress 5.8+, PHP 7.4+ y WooCommerce 5.0+', 'saicowc-author'),
            'SaicoWC Author Follow & Badges'
        );

        printf('<div class="notice notice-error"><p>%s</p></div>', wp_kses_post($message));
    }

    /**
     * Cargar traducciones
     *
     * @since 1.0.0
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'saicowc-author',
            false,
            dirname(SAICOWC_AUTHOR_BASENAME) . '/languages'
        );
    }

    /**
     * Activación del plugin
     *
     * @since 1.0.0
     */
    public function activate() {
        // Verificar permisos
        if (!current_user_can('activate_plugins')) {
            return;
        }

        // Crear opciones por defecto
        $default_options = array(
            'points_publish_product' => 10,
            'points_product_sold' => 5,
            'points_new_follower' => 2,
            'points_featured_product' => 15,
            'enable_notifications' => true,
            'enable_email_new_product' => true,
            'enable_email_level_up' => true,
            'badge_levels' => array(
                'bronze' => array('min' => 0, 'max' => 50, 'title' => __('Autor Novato', 'saicowc-author')),
                'silver' => array('min' => 51, 'max' => 200, 'title' => __('Autor Establecido', 'saicowc-author')),
                'gold' => array('min' => 201, 'max' => 500, 'title' => __('Autor Destacado', 'saicowc-author')),
                'platinum' => array('min' => 501, 'max' => 1000, 'title' => __('Autor Elite', 'saicowc-author')),
                'diamond' => array('min' => 1001, 'max' => 999999, 'title' => __('Autor Leyenda', 'saicowc-author')),
            ),
        );

        add_option('saicowc_author_settings', $default_options);

        // Crear versión
        add_option('saicowc_author_version', SAICOWC_AUTHOR_VERSION);

        // Flush rewrite rules
        flush_rewrite_rules();

        /**
         * Hook de activación
         *
         * @since 1.0.0
         */
        do_action('saicowc_author_system_activated');
    }

    /**
     * Desactivación del plugin
     *
     * @since 1.0.0
     */
    public function deactivate() {
        // Verificar permisos
        if (!current_user_can('activate_plugins')) {
            return;
        }

        // Limpiar cron jobs
        wp_clear_scheduled_hook('saicowc_author_send_notifications');

        // Flush rewrite rules
        flush_rewrite_rules();

        /**
         * Hook de desactivación
         *
         * @since 1.0.0
         */
        do_action('saicowc_author_system_deactivated');
    }
}

} // Fin del namespace SaicoWC\AuthorSystem

namespace { // Namespace global

/**
 * Función helper para obtener la instancia del plugin
 * Esta función está en el namespace global para ser accesible desde cualquier parte
 *
 * @since 1.0.0
 * @return \SaicoWC\AuthorSystem\Plugin
 */
function saicowc_author_system() {
    return \SaicoWC\AuthorSystem\Plugin::instance();
}

} // Fin del namespace global

// Inicializar el plugin
saicowc_author_system();
