<?php
/**
 * Clase AJAX - Manejo de peticiones AJAX
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
 * Clase AJAX
 *
 * Maneja todas las peticiones AJAX del plugin
 *
 * @since 1.0.0
 */
class AJAX {

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
        // Follow/Unfollow
        add_action('wp_ajax_saicowc_follow_author', array($this, 'follow_author'));
        add_action('wp_ajax_saicowc_unfollow_author', array($this, 'unfollow_author'));

        // Get author stats
        add_action('wp_ajax_saicowc_get_author_stats', array($this, 'get_author_stats'));
        add_action('wp_ajax_nopriv_saicowc_get_author_stats', array($this, 'get_author_stats'));

        // Get following list
        add_action('wp_ajax_saicowc_get_following_list', array($this, 'get_following_list'));

        // Get top authors
        add_action('wp_ajax_saicowc_get_top_authors', array($this, 'get_top_authors'));
        add_action('wp_ajax_nopriv_saicowc_get_top_authors', array($this, 'get_top_authors'));
    }

    /**
     * Seguir autor via AJAX
     *
     * @since 1.0.0
     */
    public function follow_author() {
        // Verificar nonce
        if (!check_ajax_referer('saicowc_author_nonce', 'nonce', false)) {
            wp_send_json_error(array(
                'message' => __('Verificación de seguridad fallida', 'saicowc-author'),
            ), 403);
        }

        // Verificar que el usuario está logueado
        if (!is_user_logged_in()) {
            wp_send_json_error(array(
                'message' => __('Debes iniciar sesión para seguir autores', 'saicowc-author'),
            ), 401);
        }

        // Obtener y sanitizar datos
        $author_id = isset($_POST['author_id']) ? absint($_POST['author_id']) : 0;
        $user_id = get_current_user_id();

        if (!$author_id) {
            wp_send_json_error(array(
                'message' => __('ID de autor inválido', 'saicowc-author'),
            ), 400);
        }

        // Seguir autor
        $follow = saicowc_author_system()->follow;
        $result = $follow->follow_author($user_id, $author_id);

        if (is_wp_error($result)) {
            wp_send_json_error(array(
                'message' => $result->get_error_message(),
            ), 400);
        }

        // Respuesta exitosa
        wp_send_json_success(array(
            'message' => __('Ahora sigues a este autor', 'saicowc-author'),
            'is_following' => true,
            'followers_count' => $follow->get_followers_count($author_id),
        ));
    }

    /**
     * Dejar de seguir autor via AJAX
     *
     * @since 1.0.0
     */
    public function unfollow_author() {
        // Verificar nonce
        if (!check_ajax_referer('saicowc_author_nonce', 'nonce', false)) {
            wp_send_json_error(array(
                'message' => __('Verificación de seguridad fallida', 'saicowc-author'),
            ), 403);
        }

        // Verificar que el usuario está logueado
        if (!is_user_logged_in()) {
            wp_send_json_error(array(
                'message' => __('Debes iniciar sesión', 'saicowc-author'),
            ), 401);
        }

        // Obtener y sanitizar datos
        $author_id = isset($_POST['author_id']) ? absint($_POST['author_id']) : 0;
        $user_id = get_current_user_id();

        if (!$author_id) {
            wp_send_json_error(array(
                'message' => __('ID de autor inválido', 'saicowc-author'),
            ), 400);
        }

        // Dejar de seguir
        $follow = saicowc_author_system()->follow;
        $result = $follow->unfollow_author($user_id, $author_id);

        if (is_wp_error($result)) {
            wp_send_json_error(array(
                'message' => $result->get_error_message(),
            ), 400);
        }

        // Respuesta exitosa
        wp_send_json_success(array(
            'message' => __('Has dejado de seguir a este autor', 'saicowc-author'),
            'is_following' => false,
            'followers_count' => $follow->get_followers_count($author_id),
        ));
    }

    /**
     * Obtener estadísticas de un autor via AJAX
     *
     * @since 1.0.0
     */
    public function get_author_stats() {
        // Verificar nonce
        if (!check_ajax_referer('saicowc_author_nonce', 'nonce', false)) {
            wp_send_json_error(array(
                'message' => __('Verificación de seguridad fallida', 'saicowc-author'),
            ), 403);
        }

        // Obtener ID del autor
        $author_id = isset($_GET['author_id']) ? absint($_GET['author_id']) : 0;

        if (!$author_id) {
            wp_send_json_error(array(
                'message' => __('ID de autor inválido', 'saicowc-author'),
            ), 400);
        }

        // Obtener datos
        $follow = saicowc_author_system()->follow;
        $gamification = saicowc_author_system()->gamification;

        $user_id = get_current_user_id();

        $stats = array(
            'author_id' => $author_id,
            'followers_count' => $follow->get_followers_count($author_id),
            'is_following' => $user_id ? $follow->is_following($user_id, $author_id) : false,
            'points' => $gamification->get_points($author_id),
            'badge' => $gamification->get_author_badge($author_id),
            'products_count' => count_user_posts($author_id, 'product'),
        );

        wp_send_json_success($stats);
    }

    /**
     * Obtener lista de autores seguidos via AJAX
     *
     * @since 1.0.0
     */
    public function get_following_list() {
        // Verificar nonce
        if (!check_ajax_referer('saicowc_author_nonce', 'nonce', false)) {
            wp_send_json_error(array(
                'message' => __('Verificación de seguridad fallida', 'saicowc-author'),
            ), 403);
        }

        // Verificar que el usuario está logueado
        if (!is_user_logged_in()) {
            wp_send_json_error(array(
                'message' => __('Debes iniciar sesión', 'saicowc-author'),
            ), 401);
        }

        $user_id = get_current_user_id();
        $follow = saicowc_author_system()->follow;
        $gamification = saicowc_author_system()->gamification;

        $following_ids = $follow->get_following($user_id);
        $following_data = array();

        foreach ($following_ids as $author_id) {
            $author = get_userdata($author_id);

            if (!$author) {
                continue;
            }

            $following_data[] = array(
                'author_id' => $author_id,
                'display_name' => $author->display_name,
                'avatar' => get_avatar_url($author_id, array('size' => 48)),
                'url' => get_author_posts_url($author_id),
                'followers_count' => $follow->get_followers_count($author_id),
                'points' => $gamification->get_points($author_id),
                'badge' => $gamification->get_author_badge($author_id),
                'products_count' => count_user_posts($author_id, 'product'),
            );
        }

        wp_send_json_success(array(
            'following' => $following_data,
            'count' => count($following_data),
        ));
    }

    /**
     * Obtener top autores via AJAX
     *
     * @since 1.0.0
     */
    public function get_top_authors() {
        // Verificar nonce
        if (!check_ajax_referer('saicowc_author_nonce', 'nonce', false)) {
            wp_send_json_error(array(
                'message' => __('Verificación de seguridad fallida', 'saicowc-author'),
            ), 403);
        }

        $limit = isset($_GET['limit']) ? absint($_GET['limit']) : 10;
        $limit = min($limit, 50); // Máximo 50

        $follow = saicowc_author_system()->follow;
        $authors = $follow->get_top_authors($limit);

        wp_send_json_success(array(
            'authors' => $authors,
            'count' => count($authors),
        ));
    }
}
