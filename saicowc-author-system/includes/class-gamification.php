<?php
/**
 * Clase Gamification - Sistema de puntos y badges
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
 * Clase Gamification
 *
 * Maneja el sistema de puntos y badges
 *
 * @since 1.0.0
 */
class Gamification {

    /**
     * Meta key para puntos del autor
     *
     * @var string
     */
    const META_POINTS = '_saicowc_author_points';

    /**
     * Meta key para nivel de badge del autor
     *
     * @var string
     */
    const META_BADGE_LEVEL = '_saicowc_author_badge_level';

    /**
     * Niveles de badges
     *
     * @var array
     */
    private $badge_levels = array(
        'bronze' => array(
            'min' => 0,
            'max' => 50,
            'title' => 'Autor Novato',
            'color' => '#CD7F32',
            'icon' => 'star',
        ),
        'silver' => array(
            'min' => 51,
            'max' => 200,
            'title' => 'Autor Establecido',
            'color' => '#C0C0C0',
            'icon' => 'star-aura',
        ),
        'gold' => array(
            'min' => 201,
            'max' => 500,
            'title' => 'Autor Destacado',
            'color' => '#FFD700',
            'icon' => 'crown',
        ),
        'platinum' => array(
            'min' => 501,
            'max' => 1000,
            'title' => 'Autor Elite',
            'color' => '#E5E4E2',
            'icon' => 'crown-gem',
        ),
        'diamond' => array(
            'min' => 1001,
            'max' => 999999,
            'title' => 'Autor Leyenda',
            'color' => '#B9F2FF',
            'icon' => 'diamond',
        ),
    );

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
        // Puntos al publicar producto
        add_action('transition_post_status', array($this, 'on_product_published'), 10, 3);

        // Puntos al vender producto
        add_action('woocommerce_order_status_completed', array($this, 'on_product_sold'), 10, 1);

        // Badge SVG inline
        add_action('wp_footer', array($this, 'add_svg_sprites'), 999);
    }

    /**
     * Añadir puntos al publicar producto
     *
     * @since 1.0.0
     * @param string $new_status Nuevo estado
     * @param string $old_status Estado anterior
     * @param \WP_Post $post Post
     */
    public function on_product_published($new_status, $old_status, $post) {
        if ($post->post_type !== 'product') {
            return;
        }

        if ($new_status !== 'publish' || $old_status === 'publish') {
            return;
        }

        $author_id = $post->post_author;
        $points = Core::get_option('points_publish_product', 10);

        $this->add_points($author_id, $points, 'publish_product');
    }

    /**
     * Añadir puntos al vender producto
     *
     * @since 1.0.0
     * @param int $order_id ID de la orden
     */
    public function on_product_sold($order_id) {
        $order = wc_get_order($order_id);

        if (!$order) {
            return;
        }

        $items = $order->get_items();
        $processed_authors = array();

        foreach ($items as $item) {
            $product_id = $item->get_product_id();
            $author_id = get_post_field('post_author', $product_id);

            // Evitar procesar el mismo autor múltiples veces
            if (in_array($author_id, $processed_authors)) {
                continue;
            }

            $points = Core::get_option('points_product_sold', 5);
            $this->add_points($author_id, $points, 'product_sold');

            $processed_authors[] = $author_id;
        }
    }

    /**
     * Añadir puntos a un autor
     *
     * @since 1.0.0
     * @param int $author_id ID del autor
     * @param int $points Puntos a añadir
     * @param string $reason Razón (publish_product, product_sold, new_follower, etc)
     * @return int Nuevos puntos totales
     */
    public function add_points($author_id, $points, $reason = '') {
        if (!$author_id || $points <= 0) {
            return 0;
        }

        $current_points = $this->get_points($author_id);
        $old_level = $this->get_badge_level($current_points);

        $new_points = $current_points + $points;
        update_user_meta($author_id, self::META_POINTS, $new_points);

        // Verificar si subió de nivel
        $new_level = $this->get_badge_level($new_points);

        if ($old_level !== $new_level) {
            update_user_meta($author_id, self::META_BADGE_LEVEL, $new_level);

            /**
             * Acción cuando un autor sube de nivel
             *
             * @since 1.0.0
             * @param int $author_id ID del autor
             * @param string $new_level Nuevo nivel
             * @param string $old_level Nivel anterior
             * @param int $new_points Puntos totales
             */
            do_action('saicowc_author_level_up', $author_id, $new_level, $old_level, $new_points);
        }

        // Limpiar caché
        wp_cache_delete('author_points_' . $author_id, 'saicowc_author');
        wp_cache_delete('author_badge_' . $author_id, 'saicowc_author');

        /**
         * Acción cuando se añaden puntos a un autor
         *
         * @since 1.0.0
         * @param int $author_id ID del autor
         * @param int $points Puntos añadidos
         * @param int $new_points Puntos totales
         * @param string $reason Razón
         */
        do_action('saicowc_author_points_added', $author_id, $points, $new_points, $reason);

        return $new_points;
    }

    /**
     * Obtener puntos de un autor
     *
     * @since 1.0.0
     * @param int $author_id ID del autor
     * @return int Puntos
     */
    public function get_points($author_id) {
        if (!$author_id) {
            return 0;
        }

        $cache_key = 'author_points_' . $author_id;
        $cached = wp_cache_get($cache_key, 'saicowc_author');

        if ($cached !== false) {
            return (int) $cached;
        }

        $points = (int) get_user_meta($author_id, self::META_POINTS, true);

        wp_cache_set($cache_key, $points, 'saicowc_author', HOUR_IN_SECONDS);

        return $points;
    }

    /**
     * Obtener nivel de badge según puntos
     *
     * @since 1.0.0
     * @param int $points Puntos
     * @return string Nivel (bronze, silver, gold, platinum, diamond)
     */
    public function get_badge_level($points) {
        foreach ($this->badge_levels as $level => $data) {
            if ($points >= $data['min'] && $points <= $data['max']) {
                return $level;
            }
        }

        return 'bronze';
    }

    /**
     * Obtener datos del badge de un autor
     *
     * @since 1.0.0
     * @param int $author_id ID del autor
     * @return array|false Datos del badge o false
     */
    public function get_author_badge($author_id) {
        if (!$author_id) {
            return false;
        }

        $cache_key = 'author_badge_' . $author_id;
        $cached = wp_cache_get($cache_key, 'saicowc_author');

        if ($cached !== false) {
            return $cached;
        }

        $points = $this->get_points($author_id);
        $level = $this->get_badge_level($points);

        if (!isset($this->badge_levels[$level])) {
            return false;
        }

        $badge_data = $this->badge_levels[$level];
        $badge_data['level'] = $level;
        $badge_data['points'] = $points;

        // Calcular progreso al siguiente nivel
        $next_level = $this->get_next_level($level);
        if ($next_level) {
            $badge_data['next_level'] = $next_level;
            $badge_data['next_level_points'] = $this->badge_levels[$next_level]['min'];
            $badge_data['progress'] = $this->calculate_progress($points, $level);
        } else {
            $badge_data['next_level'] = null;
            $badge_data['progress'] = 100;
        }

        wp_cache_set($cache_key, $badge_data, 'saicowc_author', HOUR_IN_SECONDS);

        return $badge_data;
    }

    /**
     * Obtener siguiente nivel
     *
     * @since 1.0.0
     * @param string $current_level Nivel actual
     * @return string|false Siguiente nivel o false
     */
    private function get_next_level($current_level) {
        $levels = array_keys($this->badge_levels);
        $current_index = array_search($current_level, $levels);

        if ($current_index === false || !isset($levels[$current_index + 1])) {
            return false;
        }

        return $levels[$current_index + 1];
    }

    /**
     * Calcular progreso al siguiente nivel
     *
     * @since 1.0.0
     * @param int $points Puntos actuales
     * @param string $current_level Nivel actual
     * @return int Porcentaje de progreso (0-100)
     */
    private function calculate_progress($points, $current_level) {
        if (!isset($this->badge_levels[$current_level])) {
            return 0;
        }

        $current_min = $this->badge_levels[$current_level]['min'];
        $current_max = $this->badge_levels[$current_level]['max'];

        if ($points >= $current_max) {
            return 100;
        }

        $range = $current_max - $current_min;
        $progress = $points - $current_min;

        return (int) (($progress / $range) * 100);
    }

    /**
     * Obtener SVG del badge
     *
     * @since 1.0.0
     * @param string $level Nivel del badge
     * @param int $size Tamaño (default: 32)
     * @return string SVG
     */
    public function get_badge_svg($level, $size = 32) {
        if (!isset($this->badge_levels[$level])) {
            return '';
        }

        $data = $this->badge_levels[$level];
        $color = $data['color'];
        $icon = $data['icon'];

        $svg = '';

        switch ($icon) {
            case 'star':
                $svg = $this->get_star_svg($color, $size);
                break;

            case 'star-aura':
                $svg = $this->get_star_aura_svg($color, $size);
                break;

            case 'crown':
                $svg = $this->get_crown_svg($color, $size);
                break;

            case 'crown-gem':
                $svg = $this->get_crown_gem_svg($color, $size);
                break;

            case 'diamond':
                $svg = $this->get_diamond_svg($color, $size);
                break;
        }

        return $svg;
    }

    /**
     * SVG Estrella simple (Bronce)
     *
     * @since 1.0.0
     * @param string $color Color
     * @param int $size Tamaño
     * @return string SVG
     */
    private function get_star_svg($color, $size) {
        return sprintf(
            '<svg width="%d" height="%d" viewBox="0 0 24 24" fill="%s" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>',
            $size,
            $size,
            esc_attr($color)
        );
    }

    /**
     * SVG Estrella con aura (Plata)
     *
     * @since 1.0.0
     * @param string $color Color
     * @param int $size Tamaño
     * @return string SVG
     */
    private function get_star_aura_svg($color, $size) {
        return sprintf(
            '<svg width="%d" height="%d" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="12" cy="12" r="10" stroke="%s" stroke-width="0.5" opacity="0.3"/>
                <circle cx="12" cy="12" r="8" stroke="%s" stroke-width="0.5" opacity="0.2"/>
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" fill="%s"/>
            </svg>',
            $size,
            $size,
            esc_attr($color),
            esc_attr($color),
            esc_attr($color)
        );
    }

    /**
     * SVG Corona simple (Oro)
     *
     * @since 1.0.0
     * @param string $color Color
     * @param int $size Tamaño
     * @return string SVG
     */
    private function get_crown_svg($color, $size) {
        return sprintf(
            '<svg width="%d" height="%d" viewBox="0 0 24 24" fill="%s" xmlns="http://www.w3.org/2000/svg">
                <path d="M2 20h20v2H2v-2zm2-8l4 2 4-6 4 6 4-2v8H4v-8z"/>
                <circle cx="12" cy="4" r="2" fill="%s"/>
                <circle cx="4" cy="12" r="2" fill="%s"/>
                <circle cx="20" cy="12" r="2" fill="%s"/>
            </svg>',
            $size,
            $size,
            esc_attr($color),
            esc_attr($color),
            esc_attr($color),
            esc_attr($color)
        );
    }

    /**
     * SVG Corona con gemas (Platino)
     *
     * @since 1.0.0
     * @param string $color Color
     * @param int $size Tamaño
     * @return string SVG
     */
    private function get_crown_gem_svg($color, $size) {
        return sprintf(
            '<svg width="%d" height="%d" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M2 20h20v2H2v-2zm2-8l4 2 4-6 4 6 4-2v8H4v-8z" fill="%s"/>
                <circle cx="12" cy="4" r="2" fill="#4ADE80"/>
                <circle cx="4" cy="12" r="2" fill="#60A5FA"/>
                <circle cx="20" cy="12" r="2" fill="#F472B6"/>
                <path d="M12 6l1.5 3h-3l1.5-3z" fill="#FDE047"/>
            </svg>',
            $size,
            $size,
            esc_attr($color)
        );
    }

    /**
     * SVG Diamante brillante (Diamante)
     *
     * @since 1.0.0
     * @param string $color Color
     * @param int $size Tamaño
     * @return string SVG
     */
    private function get_diamond_svg($color, $size) {
        return sprintf(
            '<svg width="%d" height="%d" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <linearGradient id="diamondGradient" x1="0%%" y1="0%%" x2="100%%" y2="100%%">
                        <stop offset="0%%" style="stop-color:%s;stop-opacity:1"/>
                        <stop offset="50%%" style="stop-color:#FFFFFF;stop-opacity:0.8"/>
                        <stop offset="100%%" style="stop-color:%s;stop-opacity:1"/>
                    </linearGradient>
                </defs>
                <path d="M12 2l-7 7h14l-7-7z" fill="url(#diamondGradient)"/>
                <path d="M5 9l7 13 7-13H5z" fill="url(#diamondGradient)"/>
                <path d="M12 2l-2 7h4l-2-7z" fill="#FFFFFF" opacity="0.5"/>
            </svg>',
            $size,
            $size,
            esc_attr($color),
            esc_attr($color)
        );
    }

    /**
     * Añadir sprites SVG al footer
     *
     * @since 1.0.0
     */
    public function add_svg_sprites() {
        // Los SVG se renderizan inline según necesidad
    }

    /**
     * Obtener configuración de niveles
     *
     * @since 1.0.0
     * @return array Niveles configurados
     */
    public function get_badge_levels() {
        return $this->badge_levels;
    }
}
