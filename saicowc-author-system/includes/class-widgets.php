<?php
/**
 * Clase Widgets - Widgets del plugin
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
 * Clase Widgets_Manager
 *
 * Registra todos los widgets del plugin
 *
 * @since 1.0.0
 */
class Widgets_Manager {

    /**
     * Constructor
     *
     * @since 1.0.0
     */
    public function __construct() {
        add_action('widgets_init', array($this, 'register_widgets'));
    }

    /**
     * Registrar widgets
     *
     * @since 1.0.0
     */
    public function register_widgets() {
        register_widget(__NAMESPACE__ . '\\Widget_Top_Authors');
        register_widget(__NAMESPACE__ . '\\Widget_Following_Authors');
        register_widget(__NAMESPACE__ . '\\Widget_Following_Products');
    }
}

/**
 * Widget: Top Autores
 *
 * @since 1.0.0
 */
class Widget_Top_Authors extends \WP_Widget {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'saicowc_top_authors',
            __('SaicoWC - Top Autores', 'saicowc-author'),
            array(
                'description' => __('Muestra los autores con más seguidores', 'saicowc-author'),
                'classname' => 'saicowc-widget-top-authors',
            )
        );
    }

    /**
     * Front-end display
     *
     * @param array $args Widget arguments
     * @param array $instance Saved values
     */
    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Top Autores', 'saicowc-author');
        $limit = !empty($instance['limit']) ? absint($instance['limit']) : 5;

        echo $args['before_widget'];

        if (!empty($title)) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }

        $follow = saicowc_author_system()->follow;
        $authors = $follow->get_top_authors($limit);

        if (!empty($authors)) {
            echo '<div class="saicowc-widget-top-authors-list">';

            foreach ($authors as $index => $author) {
                $gamification = saicowc_author_system()->gamification;
                ?>
                <div class="widget-author-item">
                    <span class="author-rank">#<?php echo esc_html($index + 1); ?></span>
                    <a href="<?php echo esc_url($author['url']); ?>" class="author-link">
                        <img src="<?php echo esc_url($author['avatar']); ?>" alt="<?php echo esc_attr($author['display_name']); ?>" class="author-avatar">
                        <div class="author-details">
                            <span class="author-name"><?php echo esc_html($author['display_name']); ?></span>
                            <?php if ($author['badge']): ?>
                            <span class="author-badge-mini">
                                <?php echo $gamification->get_badge_svg($author['badge']['level'], 16); ?>
                            </span>
                            <?php endif; ?>
                            <span class="author-followers"><?php echo esc_html(number_format($author['followers_count'])); ?> seguidores</span>
                        </div>
                    </a>
                </div>
                <?php
            }

            echo '</div>';
        } else {
            echo '<p>' . esc_html__('No hay autores todavía.', 'saicowc-author') . '</p>';
        }

        echo $args['after_widget'];
    }

    /**
     * Back-end widget form
     *
     * @param array $instance Saved values
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Top Autores', 'saicowc-author');
        $limit = !empty($instance['limit']) ? absint($instance['limit']) : 5;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php esc_html_e('Título:', 'saicowc-author'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('limit')); ?>">
                <?php esc_html_e('Número de autores:', 'saicowc-author'); ?>
            </label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('limit')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('limit')); ?>" type="number"
                   min="1" max="20" value="<?php echo esc_attr($limit); ?>">
        </p>
        <?php
    }

    /**
     * Update widget
     *
     * @param array $new_instance New values
     * @param array $old_instance Old values
     * @return array Updated values
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = !empty($new_instance['title']) ? sanitize_text_field($new_instance['title']) : '';
        $instance['limit'] = !empty($new_instance['limit']) ? absint($new_instance['limit']) : 5;
        return $instance;
    }
}

/**
 * Widget: Mis Autores Seguidos
 *
 * @since 1.0.0
 */
class Widget_Following_Authors extends \WP_Widget {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'saicowc_following_authors',
            __('SaicoWC - Mis Autores Seguidos', 'saicowc-author'),
            array(
                'description' => __('Muestra los autores que sigues', 'saicowc-author'),
                'classname' => 'saicowc-widget-following-authors',
            )
        );
    }

    /**
     * Front-end display
     *
     * @param array $args Widget arguments
     * @param array $instance Saved values
     */
    public function widget($args, $instance) {
        if (!is_user_logged_in()) {
            return;
        }

        $title = !empty($instance['title']) ? $instance['title'] : __('Autores que Sigo', 'saicowc-author');

        echo $args['before_widget'];

        if (!empty($title)) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }

        $user_id = get_current_user_id();
        $follow = saicowc_author_system()->follow;
        $following_ids = $follow->get_following($user_id);

        if (!empty($following_ids)) {
            echo '<div class="saicowc-widget-following-list">';

            foreach ($following_ids as $author_id) {
                $author = get_userdata($author_id);
                if (!$author) continue;

                $gamification = saicowc_author_system()->gamification;
                $badge = $gamification->get_author_badge($author_id);
                ?>
                <div class="widget-author-item">
                    <a href="<?php echo esc_url(get_author_posts_url($author_id)); ?>" class="author-link">
                        <?php echo get_avatar($author_id, 40); ?>
                        <div class="author-details">
                            <span class="author-name"><?php echo esc_html($author->display_name); ?></span>
                            <?php if ($badge): ?>
                            <span class="author-badge-mini">
                                <?php echo $gamification->get_badge_svg($badge['level'], 16); ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    </a>
                </div>
                <?php
            }

            echo '</div>';
        } else {
            echo '<p>' . esc_html__('No sigues a ningún autor todavía.', 'saicowc-author') . '</p>';
        }

        echo $args['after_widget'];
    }

    /**
     * Back-end widget form
     *
     * @param array $instance Saved values
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Autores que Sigo', 'saicowc-author');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php esc_html_e('Título:', 'saicowc-author'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }

    /**
     * Update widget
     *
     * @param array $new_instance New values
     * @param array $old_instance Old values
     * @return array Updated values
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = !empty($new_instance['title']) ? sanitize_text_field($new_instance['title']) : '';
        return $instance;
    }
}

/**
 * Widget: Últimos Productos de Autores Seguidos
 *
 * @since 1.0.0
 */
class Widget_Following_Products extends \WP_Widget {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'saicowc_following_products',
            __('SaicoWC - Productos de Autores Seguidos', 'saicowc-author'),
            array(
                'description' => __('Muestra productos recientes de autores que sigues', 'saicowc-author'),
                'classname' => 'saicowc-widget-following-products',
            )
        );
    }

    /**
     * Front-end display
     *
     * @param array $args Widget arguments
     * @param array $instance Saved values
     */
    public function widget($args, $instance) {
        if (!is_user_logged_in()) {
            return;
        }

        $title = !empty($instance['title']) ? $instance['title'] : __('Nuevos de Autores que Sigo', 'saicowc-author');
        $limit = !empty($instance['limit']) ? absint($instance['limit']) : 5;

        echo $args['before_widget'];

        if (!empty($title)) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }

        $user_id = get_current_user_id();
        $follow = saicowc_author_system()->follow;
        $products = $follow->get_following_products($user_id, $limit);

        if (!empty($products)) {
            echo '<div class="saicowc-widget-products-list">';

            foreach ($products as $product) {
                $author_id = get_post_field('post_author', $product->get_id());
                ?>
                <div class="widget-product-item">
                    <a href="<?php echo esc_url($product->get_permalink()); ?>" class="product-link">
                        <?php echo $product->get_image('thumbnail'); ?>
                        <div class="product-details">
                            <span class="product-name"><?php echo esc_html($product->get_name()); ?></span>
                            <span class="product-author"><?php echo esc_html(get_the_author_meta('display_name', $author_id)); ?></span>
                            <?php if ($product->get_price_html()): ?>
                            <span class="product-price"><?php echo $product->get_price_html(); ?></span>
                            <?php endif; ?>
                        </div>
                    </a>
                </div>
                <?php
            }

            echo '</div>';
        } else {
            echo '<p>' . esc_html__('No hay productos nuevos.', 'saicowc-author') . '</p>';
        }

        echo $args['after_widget'];
    }

    /**
     * Back-end widget form
     *
     * @param array $instance Saved values
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Nuevos de Autores que Sigo', 'saicowc-author');
        $limit = !empty($instance['limit']) ? absint($instance['limit']) : 5;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                <?php esc_html_e('Título:', 'saicowc-author'); ?>
            </label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('limit')); ?>">
                <?php esc_html_e('Número de productos:', 'saicowc-author'); ?>
            </label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('limit')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('limit')); ?>" type="number"
                   min="1" max="10" value="<?php echo esc_attr($limit); ?>">
        </p>
        <?php
    }

    /**
     * Update widget
     *
     * @param array $new_instance New values
     * @param array $old_instance Old values
     * @return array Updated values
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = !empty($new_instance['title']) ? sanitize_text_field($new_instance['title']) : '';
        $instance['limit'] = !empty($new_instance['limit']) ? absint($new_instance['limit']) : 5;
        return $instance;
    }
}
