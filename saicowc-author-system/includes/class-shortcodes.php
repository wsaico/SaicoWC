<?php
/**
 * Clase Shortcodes - Shortcodes del plugin
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
 * Clase Shortcodes
 *
 * Registra y maneja todos los shortcodes del plugin
 *
 * @since 1.0.0
 */
class Shortcodes {

    /**
     * Constructor
     *
     * @since 1.0.0
     */
    public function __construct() {
        $this->register_shortcodes();
    }

    /**
     * Registrar shortcodes
     *
     * @since 1.0.0
     */
    private function register_shortcodes() {
        add_shortcode('saicowc_follow_button', array($this, 'follow_button'));
        add_shortcode('saicowc_author_badge', array($this, 'author_badge'));
        add_shortcode('saicowc_author_stats', array($this, 'author_stats'));
        add_shortcode('saicowc_following_list', array($this, 'following_list'));
        add_shortcode('saicowc_top_authors', array($this, 'top_authors'));
    }

    /**
     * Shortcode: Botón de seguir
     * [saicowc_follow_button author_id="X"]
     *
     * @since 1.0.0
     * @param array $atts Atributos
     * @return string HTML
     */
    public function follow_button($atts) {
        $atts = shortcode_atts(array(
            'author_id' => 0,
        ), $atts);

        $author_id = absint($atts['author_id']);

        if (!$author_id) {
            return '';
        }

        $core = saicowc_author_system()->core;
        return $core->get_follow_button_html($author_id);
    }

    /**
     * Shortcode: Badge del autor
     * [saicowc_author_badge author_id="X" size="32"]
     *
     * @since 1.0.0
     * @param array $atts Atributos
     * @return string HTML
     */
    public function author_badge($atts) {
        $atts = shortcode_atts(array(
            'author_id' => 0,
            'size' => 32,
        ), $atts);

        $author_id = absint($atts['author_id']);
        $size = absint($atts['size']);

        if (!$author_id) {
            return '';
        }

        $core = saicowc_author_system()->core;
        return $core->get_author_badge_html($author_id, $size);
    }

    /**
     * Shortcode: Estadísticas del autor
     * [saicowc_author_stats author_id="X"]
     *
     * @since 1.0.0
     * @param array $atts Atributos
     * @return string HTML
     */
    public function author_stats($atts) {
        $atts = shortcode_atts(array(
            'author_id' => 0,
        ), $atts);

        $author_id = absint($atts['author_id']);

        if (!$author_id) {
            $author_id = get_current_user_id();
        }

        if (!$author_id) {
            return '';
        }

        $follow = saicowc_author_system()->follow;
        $gamification = saicowc_author_system()->gamification;

        $followers_count = $follow->get_followers_count($author_id);
        $points = $gamification->get_points($author_id);
        $badge = $gamification->get_author_badge($author_id);
        $products_count = count_user_posts($author_id, 'product');

        ob_start();
        ?>
        <div class="saicowc-author-stats-shortcode">
            <div class="stats-grid">
                <div class="stat-item">
                    <span class="stat-label"><?php esc_html_e('Seguidores', 'saicowc-author'); ?></span>
                    <span class="stat-value"><?php echo esc_html(number_format($followers_count)); ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-label"><?php esc_html_e('Puntos', 'saicowc-author'); ?></span>
                    <span class="stat-value"><?php echo esc_html(number_format($points)); ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-label"><?php esc_html_e('Productos', 'saicowc-author'); ?></span>
                    <span class="stat-value"><?php echo esc_html(number_format($products_count)); ?></span>
                </div>
            </div>
            <?php if ($badge): ?>
            <div class="author-badge-display">
                <span class="badge-label"><?php esc_html_e('Nivel:', 'saicowc-author'); ?></span>
                <span class="badge-title"><?php echo esc_html($badge['title']); ?></span>
                <?php if (isset($badge['progress']) && $badge['progress'] < 100): ?>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: <?php echo esc_attr($badge['progress']); ?>%"></div>
                </div>
                <span class="progress-text">
                    <?php
                    printf(
                        /* translators: %d: puntos restantes */
                        esc_html__('%d puntos para el siguiente nivel', 'saicowc-author'),
                        $badge['next_level_points'] - $points
                    );
                    ?>
                </span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Shortcode: Lista de autores seguidos
     * [saicowc_following_list]
     *
     * @since 1.0.0
     * @param array $atts Atributos
     * @return string HTML
     */
    public function following_list($atts) {
        if (!is_user_logged_in()) {
            return '<p>' . esc_html__('Debes iniciar sesión para ver tus autores seguidos.', 'saicowc-author') . '</p>';
        }

        $user_id = get_current_user_id();
        $follow = saicowc_author_system()->follow;
        $gamification = saicowc_author_system()->gamification;

        $following_ids = $follow->get_following($user_id);

        if (empty($following_ids)) {
            return '<p>' . esc_html__('No sigues a ningún autor todavía.', 'saicowc-author') . '</p>';
        }

        ob_start();
        ?>
        <div class="saicowc-following-list">
            <?php foreach ($following_ids as $author_id):
                $author = get_userdata($author_id);
                if (!$author) continue;

                $badge = $gamification->get_author_badge($author_id);
                $followers_count = $follow->get_followers_count($author_id);
                $products_count = count_user_posts($author_id, 'product');
            ?>
            <div class="following-item">
                <a href="<?php echo esc_url(get_author_posts_url($author_id)); ?>" class="author-avatar">
                    <?php echo get_avatar($author_id, 64); ?>
                </a>
                <div class="author-info">
                    <div class="author-name-badge">
                        <a href="<?php echo esc_url(get_author_posts_url($author_id)); ?>">
                            <?php echo esc_html($author->display_name); ?>
                        </a>
                        <?php if ($badge): ?>
                        <span class="author-badge" title="<?php echo esc_attr($badge['title']); ?>">
                            <?php echo $gamification->get_badge_svg($badge['level'], 20); ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    <div class="author-meta">
                        <span><?php echo esc_html(number_format($followers_count)); ?> <?php esc_html_e('seguidores', 'saicowc-author'); ?></span>
                        <span><?php echo esc_html(number_format($products_count)); ?> <?php esc_html_e('productos', 'saicowc-author'); ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Shortcode: Top autores
     * [saicowc_top_authors limit="10"]
     *
     * @since 1.0.0
     * @param array $atts Atributos
     * @return string HTML
     */
    public function top_authors($atts) {
        $atts = shortcode_atts(array(
            'limit' => 10,
        ), $atts);

        $limit = absint($atts['limit']);
        $limit = min($limit, 50); // Máximo 50

        $follow = saicowc_author_system()->follow;
        $authors = $follow->get_top_authors($limit);

        if (empty($authors)) {
            return '<p>' . esc_html__('No hay autores todavía.', 'saicowc-author') . '</p>';
        }

        ob_start();
        ?>
        <div class="saicowc-top-authors">
            <?php
            $rank = 1;
            foreach ($authors as $author):
            ?>
            <div class="top-author-item">
                <span class="author-rank">#<?php echo esc_html($rank); ?></span>
                <a href="<?php echo esc_url($author['url']); ?>" class="author-avatar">
                    <img src="<?php echo esc_url($author['avatar']); ?>" alt="<?php echo esc_attr($author['display_name']); ?>">
                </a>
                <div class="author-info">
                    <div class="author-name-badge">
                        <a href="<?php echo esc_url($author['url']); ?>">
                            <?php echo esc_html($author['display_name']); ?>
                        </a>
                        <?php if ($author['badge']): ?>
                        <span class="author-badge" title="<?php echo esc_attr($author['badge']['title']); ?>">
                            <?php
                            $gamification = saicowc_author_system()->gamification;
                            echo $gamification->get_badge_svg($author['badge']['level'], 20);
                            ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    <div class="author-meta">
                        <span><?php echo esc_html(number_format($author['followers_count'])); ?> <?php esc_html_e('seguidores', 'saicowc-author'); ?></span>
                        <span><?php echo esc_html(number_format($author['points'])); ?> <?php esc_html_e('puntos', 'saicowc-author'); ?></span>
                    </div>
                </div>
            </div>
            <?php
            $rank++;
            endforeach;
            ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
