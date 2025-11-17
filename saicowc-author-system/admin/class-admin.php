<?php
/**
 * Clase Admin - Panel de administración
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
 * Clase Admin
 *
 * Maneja el panel de administración del plugin
 *
 * @since 1.0.0
 */
class Admin {

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
        // Menú de administración
        add_action('admin_menu', array($this, 'add_admin_menu'));

        // Enqueue admin assets
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));

        // Agregar link de configuración en la página de plugins
        add_filter('plugin_action_links_' . SAICOWC_AUTHOR_BASENAME, array($this, 'add_settings_link'));

        // Dashboard del autor en perfil de usuario
        add_action('show_user_profile', array($this, 'add_author_dashboard'));
        add_action('edit_user_profile', array($this, 'add_author_dashboard'));
    }

    /**
     * Añadir menú de administración
     *
     * @since 1.0.0
     */
    public function add_admin_menu() {
        add_menu_page(
            __('SaicoWC Author System', 'saicowc-author'),
            __('Author System', 'saicowc-author'),
            'manage_options',
            'saicowc-author-system',
            array($this, 'render_dashboard'),
            'dashicons-star-filled',
            56
        );

        add_submenu_page(
            'saicowc-author-system',
            __('Dashboard', 'saicowc-author'),
            __('Dashboard', 'saicowc-author'),
            'manage_options',
            'saicowc-author-system',
            array($this, 'render_dashboard')
        );

        add_submenu_page(
            'saicowc-author-system',
            __('Configuración', 'saicowc-author'),
            __('Configuración', 'saicowc-author'),
            'manage_options',
            'saicowc-author-settings',
            array($this, 'render_settings')
        );

        add_submenu_page(
            'saicowc-author-system',
            __('Top Autores', 'saicowc-author'),
            __('Top Autores', 'saicowc-author'),
            'manage_options',
            'saicowc-author-top',
            array($this, 'render_top_authors')
        );
    }

    /**
     * Enqueue admin assets
     *
     * @since 1.0.0
     * @param string $hook Hook
     */
    public function enqueue_admin_assets($hook) {
        // Solo cargar en páginas del plugin
        if (strpos($hook, 'saicowc-author') === false && strpos($hook, 'profile') === false && strpos($hook, 'user-edit') === false) {
            return;
        }

        wp_enqueue_style(
            'saicowc-author-admin',
            SAICOWC_AUTHOR_URL . 'assets/css/admin.css',
            array(),
            SAICOWC_AUTHOR_VERSION
        );

        wp_enqueue_script(
            'saicowc-author-admin',
            SAICOWC_AUTHOR_URL . 'assets/js/admin.js',
            array('jquery'),
            SAICOWC_AUTHOR_VERSION,
            true
        );
    }

    /**
     * Añadir link de configuración
     *
     * @since 1.0.0
     * @param array $links Links
     * @return array
     */
    public function add_settings_link($links) {
        $settings_link = '<a href="' . admin_url('admin.php?page=saicowc-author-settings') . '">' . __('Configuración', 'saicowc-author') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    /**
     * Renderizar dashboard
     *
     * @since 1.0.0
     */
    public function render_dashboard() {
        $follow = saicowc_author_system()->follow;
        $gamification = saicowc_author_system()->gamification;

        // Estadísticas generales
        $total_followers = 0;
        $total_points = 0;
        $total_authors = 0;

        $users = get_users(array('number' => 1000));
        foreach ($users as $user) {
            $followers = $follow->get_followers_count($user->ID);
            if ($followers > 0) {
                $total_followers += $followers;
                $total_authors++;
            }
            $total_points += $gamification->get_points($user->ID);
        }

        ?>
        <div class="wrap saicowc-author-admin">
            <h1><?php esc_html_e('SaicoWC Author System - Dashboard', 'saicowc-author'); ?></h1>

            <div class="saicowc-admin-stats">
                <div class="stat-card">
                    <div class="stat-icon">
                        <span class="dashicons dashicons-groups"></span>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo esc_html(number_format($total_followers)); ?></h3>
                        <p><?php esc_html_e('Total Seguidores', 'saicowc-author'); ?></p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <span class="dashicons dashicons-awards"></span>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo esc_html(number_format($total_points)); ?></h3>
                        <p><?php esc_html_e('Total Puntos', 'saicowc-author'); ?></p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <span class="dashicons dashicons-admin-users"></span>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo esc_html(number_format($total_authors)); ?></h3>
                        <p><?php esc_html_e('Autores Activos', 'saicowc-author'); ?></p>
                    </div>
                </div>
            </div>

            <div class="saicowc-admin-sections">
                <div class="admin-section">
                    <h2><?php esc_html_e('Top 10 Autores', 'saicowc-author'); ?></h2>
                    <?php $this->render_top_authors_table(10); ?>
                </div>

                <div class="admin-section">
                    <h2><?php esc_html_e('Acceso Rápido', 'saicowc-author'); ?></h2>
                    <div class="quick-links">
                        <a href="<?php echo admin_url('admin.php?page=saicowc-author-settings'); ?>" class="button button-primary">
                            <span class="dashicons dashicons-admin-settings"></span>
                            <?php esc_html_e('Configuración', 'saicowc-author'); ?>
                        </a>
                        <a href="<?php echo admin_url('admin.php?page=saicowc-author-top'); ?>" class="button">
                            <span class="dashicons dashicons-chart-area"></span>
                            <?php esc_html_e('Ver Ranking', 'saicowc-author'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Renderizar página de configuración
     *
     * @since 1.0.0
     */
    public function render_settings() {
        $settings = new Settings();
        $settings->render_settings_page();
    }

    /**
     * Renderizar top autores
     *
     * @since 1.0.0
     */
    public function render_top_authors() {
        ?>
        <div class="wrap saicowc-author-admin">
            <h1><?php esc_html_e('Top Autores', 'saicowc-author'); ?></h1>
            <?php $this->render_top_authors_table(50); ?>
        </div>
        <?php
    }

    /**
     * Renderizar tabla de top autores
     *
     * @since 1.0.0
     * @param int $limit Límite
     */
    private function render_top_authors_table($limit = 10) {
        $follow = saicowc_author_system()->follow;
        $authors = $follow->get_top_authors($limit);

        if (empty($authors)) {
            echo '<p>' . esc_html__('No hay autores todavía.', 'saicowc-author') . '</p>';
            return;
        }

        ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php esc_html_e('Ranking', 'saicowc-author'); ?></th>
                    <th><?php esc_html_e('Autor', 'saicowc-author'); ?></th>
                    <th><?php esc_html_e('Badge', 'saicowc-author'); ?></th>
                    <th><?php esc_html_e('Seguidores', 'saicowc-author'); ?></th>
                    <th><?php esc_html_e('Puntos', 'saicowc-author'); ?></th>
                    <th><?php esc_html_e('Productos', 'saicowc-author'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $rank = 1;
                foreach ($authors as $author):
                    $gamification = saicowc_author_system()->gamification;
                ?>
                <tr>
                    <td><strong>#<?php echo esc_html($rank); ?></strong></td>
                    <td>
                        <a href="<?php echo esc_url(get_edit_user_link($author['user_id'])); ?>">
                            <img src="<?php echo esc_url($author['avatar']); ?>" alt="" style="width:32px;height:32px;border-radius:50%;vertical-align:middle;margin-right:8px;">
                            <?php echo esc_html($author['display_name']); ?>
                        </a>
                    </td>
                    <td>
                        <?php if ($author['badge']): ?>
                        <span title="<?php echo esc_attr($author['badge']['title']); ?>">
                            <?php echo $gamification->get_badge_svg($author['badge']['level'], 24); ?>
                            <?php echo esc_html($author['badge']['title']); ?>
                        </span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo esc_html(number_format($author['followers_count'])); ?></td>
                    <td><?php echo esc_html(number_format($author['points'])); ?></td>
                    <td><?php echo esc_html(number_format($author['products_count'])); ?></td>
                </tr>
                <?php
                $rank++;
                endforeach;
                ?>
            </tbody>
        </table>
        <?php
    }

    /**
     * Añadir dashboard del autor en el perfil de usuario
     *
     * @since 1.0.0
     * @param \WP_User $user Usuario
     */
    public function add_author_dashboard($user) {
        $follow = saicowc_author_system()->follow;
        $gamification = saicowc_author_system()->gamification;

        $followers_count = $follow->get_followers_count($user->ID);
        $following_count = $follow->get_following_count($user->ID);
        $points = $gamification->get_points($user->ID);
        $badge = $gamification->get_author_badge($user->ID);
        $products_count = count_user_posts($user->ID, 'product');

        ?>
        <h2><?php esc_html_e('Author System - Estadísticas', 'saicowc-author'); ?></h2>
        <table class="form-table">
            <tr>
                <th><?php esc_html_e('Badge Actual', 'saicowc-author'); ?></th>
                <td>
                    <?php if ($badge): ?>
                    <div style="display:flex;align-items:center;gap:12px;">
                        <?php echo $gamification->get_badge_svg($badge['level'], 48); ?>
                        <div>
                            <strong><?php echo esc_html($badge['title']); ?></strong>
                            <p style="margin:4px 0 0;"><?php echo esc_html(number_format($points)); ?> puntos</p>
                            <?php if (isset($badge['next_level'])): ?>
                            <p style="margin:4px 0 0;color:#666;">
                                <?php
                                printf(
                                    /* translators: %d: puntos restantes */
                                    esc_html__('%d puntos para el siguiente nivel', 'saicowc-author'),
                                    $badge['next_level_points'] - $points
                                );
                                ?>
                            </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th><?php esc_html_e('Seguidores', 'saicowc-author'); ?></th>
                <td><strong><?php echo esc_html(number_format($followers_count)); ?></strong></td>
            </tr>
            <tr>
                <th><?php esc_html_e('Siguiendo', 'saicowc-author'); ?></th>
                <td><strong><?php echo esc_html(number_format($following_count)); ?></strong></td>
            </tr>
            <tr>
                <th><?php esc_html_e('Productos Publicados', 'saicowc-author'); ?></th>
                <td><strong><?php echo esc_html(number_format($products_count)); ?></strong></td>
            </tr>
        </table>
        <?php
    }
}
