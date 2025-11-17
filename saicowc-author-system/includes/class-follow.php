<?php
/**
 * Clase Follow - Sistema de seguir autores
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
 * Clase Follow
 *
 * Maneja todo el sistema de seguir/dejar de seguir autores
 * Usa user_meta para almacenar relaciones
 *
 * @since 1.0.0
 */
class Follow {

    /**
     * Meta key para autores que sigue el usuario
     *
     * @var string
     */
    const META_FOLLOWING = '_saicowc_following_authors';

    /**
     * Meta key para seguidores del autor
     *
     * @var string
     */
    const META_FOLLOWERS = '_saicowc_author_followers';

    /**
     * Constructor
     *
     * @since 1.0.0
     */
    public function __construct() {
        // No se necesitan hooks aquí, todo se maneja via AJAX
    }

    /**
     * Seguir a un autor
     *
     * @since 1.0.0
     * @param int $user_id ID del usuario que sigue
     * @param int $author_id ID del autor a seguir
     * @return bool|WP_Error
     */
    public function follow_author($user_id, $author_id) {
        // Validaciones
        if (!$user_id || !$author_id) {
            return new \WP_Error('invalid_ids', __('IDs de usuario inválidos', 'saicowc-author'));
        }

        if ($user_id == $author_id) {
            return new \WP_Error('self_follow', __('No puedes seguirte a ti mismo', 'saicowc-author'));
        }

        // Verificar que el autor existe
        $author = get_userdata($author_id);
        if (!$author) {
            return new \WP_Error('author_not_found', __('Autor no encontrado', 'saicowc-author'));
        }

        // Verificar si ya sigue al autor
        if ($this->is_following($user_id, $author_id)) {
            return new \WP_Error('already_following', __('Ya sigues a este autor', 'saicowc-author'));
        }

        // Obtener lista actual de autores que sigue
        $following = $this->get_following($user_id);

        // Añadir nuevo autor
        $following[] = $author_id;
        $following = array_unique($following);

        // Guardar en user_meta
        update_user_meta($user_id, self::META_FOLLOWING, $following);

        // Actualizar lista de seguidores del autor
        $followers = $this->get_followers($author_id);
        $followers[] = $user_id;
        $followers = array_unique($followers);
        update_user_meta($author_id, self::META_FOLLOWERS, $followers);

        // Limpiar caché
        $this->clear_cache($user_id, $author_id);

        // Otorgar puntos al autor por nuevo seguidor
        $gamification = saicowc_author_system()->gamification;
        $points = Core::get_option('points_new_follower', 2);
        $gamification->add_points($author_id, $points, 'new_follower');

        /**
         * Acción cuando un usuario sigue a un autor
         *
         * @since 1.0.0
         * @param int $user_id ID del usuario
         * @param int $author_id ID del autor
         */
        do_action('saicowc_author_followed', $user_id, $author_id);

        return true;
    }

    /**
     * Dejar de seguir a un autor
     *
     * @since 1.0.0
     * @param int $user_id ID del usuario
     * @param int $author_id ID del autor
     * @return bool|WP_Error
     */
    public function unfollow_author($user_id, $author_id) {
        // Validaciones
        if (!$user_id || !$author_id) {
            return new \WP_Error('invalid_ids', __('IDs de usuario inválidos', 'saicowc-author'));
        }

        // Verificar si sigue al autor
        if (!$this->is_following($user_id, $author_id)) {
            return new \WP_Error('not_following', __('No sigues a este autor', 'saicowc-author'));
        }

        // Obtener lista actual de autores que sigue
        $following = $this->get_following($user_id);

        // Remover autor
        $following = array_diff($following, array($author_id));

        // Guardar en user_meta
        update_user_meta($user_id, self::META_FOLLOWING, $following);

        // Actualizar lista de seguidores del autor
        $followers = $this->get_followers($author_id);
        $followers = array_diff($followers, array($user_id));
        update_user_meta($author_id, self::META_FOLLOWERS, $followers);

        // Limpiar caché
        $this->clear_cache($user_id, $author_id);

        /**
         * Acción cuando un usuario deja de seguir a un autor
         *
         * @since 1.0.0
         * @param int $user_id ID del usuario
         * @param int $author_id ID del autor
         */
        do_action('saicowc_author_unfollowed', $user_id, $author_id);

        return true;
    }

    /**
     * Verificar si un usuario sigue a un autor
     *
     * @since 1.0.0
     * @param int $user_id ID del usuario
     * @param int $author_id ID del autor
     * @return bool
     */
    public function is_following($user_id, $author_id) {
        if (!$user_id || !$author_id) {
            return false;
        }

        $cache_key = 'following_' . $user_id . '_' . $author_id;
        $cached = wp_cache_get($cache_key, 'saicowc_author');

        if ($cached !== false) {
            return (bool) $cached;
        }

        $following = $this->get_following($user_id);
        $is_following = in_array($author_id, $following);

        wp_cache_set($cache_key, $is_following, 'saicowc_author', HOUR_IN_SECONDS);

        return $is_following;
    }

    /**
     * Obtener autores que sigue un usuario
     *
     * @since 1.0.0
     * @param int $user_id ID del usuario
     * @return array Array de IDs de autores
     */
    public function get_following($user_id) {
        if (!$user_id) {
            return array();
        }

        $following = get_user_meta($user_id, self::META_FOLLOWING, true);

        if (!is_array($following)) {
            $following = array();
        }

        // Filtrar valores inválidos
        $following = array_filter($following, function($id) {
            return is_numeric($id) && $id > 0;
        });

        return array_values(array_map('intval', $following));
    }

    /**
     * Obtener seguidores de un autor
     *
     * @since 1.0.0
     * @param int $author_id ID del autor
     * @return array Array de IDs de seguidores
     */
    public function get_followers($author_id) {
        if (!$author_id) {
            return array();
        }

        $followers = get_user_meta($author_id, self::META_FOLLOWERS, true);

        if (!is_array($followers)) {
            $followers = array();
        }

        // Filtrar valores inválidos
        $followers = array_filter($followers, function($id) {
            return is_numeric($id) && $id > 0;
        });

        return array_values(array_map('intval', $followers));
    }

    /**
     * Obtener número de seguidores de un autor
     *
     * @since 1.0.0
     * @param int $author_id ID del autor
     * @return int Número de seguidores
     */
    public function get_followers_count($author_id) {
        if (!$author_id) {
            return 0;
        }

        $cache_key = 'followers_count_' . $author_id;
        $cached = wp_cache_get($cache_key, 'saicowc_author');

        if ($cached !== false) {
            return (int) $cached;
        }

        $followers = $this->get_followers($author_id);
        $count = count($followers);

        wp_cache_set($cache_key, $count, 'saicowc_author', HOUR_IN_SECONDS);

        return $count;
    }

    /**
     * Obtener número de autores que sigue un usuario
     *
     * @since 1.0.0
     * @param int $user_id ID del usuario
     * @return int Número de autores
     */
    public function get_following_count($user_id) {
        if (!$user_id) {
            return 0;
        }

        $following = $this->get_following($user_id);
        return count($following);
    }

    /**
     * Obtener productos recientes de autores seguidos
     *
     * @since 1.0.0
     * @param int $user_id ID del usuario
     * @param int $limit Límite de productos (default: 10)
     * @return array Array de productos
     */
    public function get_following_products($user_id, $limit = 10) {
        if (!$user_id) {
            return array();
        }

        $following = $this->get_following($user_id);

        if (empty($following)) {
            return array();
        }

        $args = array(
            'post_type' => 'product',
            'posts_per_page' => $limit,
            'post_status' => 'publish',
            'author__in' => $following,
            'orderby' => 'date',
            'order' => 'DESC',
        );

        $query = new \WP_Query($args);
        $products = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $products[] = wc_get_product(get_the_ID());
            }
            wp_reset_postdata();
        }

        return $products;
    }

    /**
     * Obtener top autores por número de seguidores
     *
     * @since 1.0.0
     * @param int $limit Límite de autores (default: 10)
     * @return array Array de autores con datos
     */
    public function get_top_authors($limit = 10) {
        global $wpdb;

        $cache_key = 'top_authors_' . $limit;
        $cached = get_transient($cache_key);

        if ($cached !== false) {
            return $cached;
        }

        // Obtener todos los autores con seguidores
        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT user_id, meta_value
            FROM {$wpdb->usermeta}
            WHERE meta_key = %s
            ORDER BY CAST(meta_value AS UNSIGNED) DESC
            LIMIT %d",
            self::META_FOLLOWERS,
            $limit
        ));

        $authors = array();

        foreach ($results as $result) {
            $user_id = $result->user_id;
            $user = get_userdata($user_id);

            if (!$user) {
                continue;
            }

            $followers = maybe_unserialize($result->meta_value);
            $followers_count = is_array($followers) ? count($followers) : 0;

            $gamification = saicowc_author_system()->gamification;

            $authors[] = array(
                'user_id' => $user_id,
                'display_name' => $user->display_name,
                'avatar' => get_avatar_url($user_id, array('size' => 64)),
                'url' => get_author_posts_url($user_id),
                'followers_count' => $followers_count,
                'points' => $gamification->get_points($user_id),
                'badge' => $gamification->get_author_badge($user_id),
                'products_count' => count_user_posts($user_id, 'product'),
            );
        }

        // Guardar en caché por 1 hora
        set_transient($cache_key, $authors, HOUR_IN_SECONDS);

        return $authors;
    }

    /**
     * Limpiar caché
     *
     * @since 1.0.0
     * @param int $user_id ID del usuario
     * @param int $author_id ID del autor
     */
    private function clear_cache($user_id, $author_id) {
        wp_cache_delete('following_' . $user_id . '_' . $author_id, 'saicowc_author');
        wp_cache_delete('followers_count_' . $author_id, 'saicowc_author');

        // Limpiar transient de top authors
        for ($i = 5; $i <= 50; $i += 5) {
            delete_transient('top_authors_' . $i);
        }
    }
}
