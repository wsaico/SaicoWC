<?php
/**
 * Sistema de productos relacionados con caché optimizado
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Obtener productos relacionados con sistema de caché
 *
 * @param int $producto_id ID del producto actual
 * @param int $limite Número de productos a obtener
 * @param int $pagina Número de página para paginación
 * @return WP_Query
 */
function saico_obtener_productos_relacionados($producto_id, $limite = 4, $pagina = 1) {

    // Clave de transient
    $transient_key = 'saico_relacionados_' . $producto_id . '_' . $limite . '_' . $pagina;

    // Intentar obtener del caché
    $query_cacheada = get_transient($transient_key);

    if (false !== $query_cacheada) {
        return $query_cacheada;
    }

    // Obtener categorías y etiquetas del producto actual
    $categorias = wp_get_post_terms($producto_id, 'product_cat', array('fields' => 'ids'));
    $etiquetas = wp_get_post_terms($producto_id, 'product_tag', array('fields' => 'ids'));

    // Construir tax_query
    $tax_query = array('relation' => 'OR');

    if (!empty($categorias)) {
        $tax_query[] = array(
            'taxonomy' => 'product_cat',
            'field' => 'term_id',
            'terms' => $categorias,
        );
    }

    if (!empty($etiquetas)) {
        $tax_query[] = array(
            'taxonomy' => 'product_tag',
            'field' => 'term_id',
            'terms' => $etiquetas,
        );
    }

    // Args de la query optimizada
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => $limite,
        'paged' => $pagina,
        'post__not_in' => array($producto_id),
        'post_status' => 'publish',
        'orderby' => 'rand',
        'tax_query' => $tax_query,
        // Optimizaciones
        'no_found_rows' => false,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
        'fields' => 'ids'
    );

    // Ejecutar query
    $query = new WP_Query($args);

    // Cachear por 1 hora
    set_transient($transient_key, $query, HOUR_IN_SECONDS);

    return $query;
}

/**
 * AJAX: Cargar más productos relacionados
 */
function saico_ajax_cargar_mas_relacionados() {
    check_ajax_referer('saico_producto_nonce', 'nonce');

    $producto_id = isset($_POST['producto_id']) ? intval($_POST['producto_id']) : 0;
    $pagina = isset($_POST['pagina']) ? intval($_POST['pagina']) : 1;
    $limite = isset($_POST['limite']) ? intval($_POST['limite']) : 4;

    if (!$producto_id) {
        wp_send_json_error('ID de producto inválido');
    }

    $query = saico_obtener_productos_relacionados($producto_id, $limite, $pagina);

    if ($query->have_posts()) {
        ob_start();

        while ($query->have_posts()) {
            $query->the_post();
            global $product;

            if ($product && is_a($product, 'WC_Product')) {
                wc_get_template_part('loop/card-producto', 'min');
            }
        }

        wp_reset_postdata();

        $html = ob_get_clean();

        wp_send_json_success(array(
            'html' => $html,
            'tiene_mas' => $pagina < $query->max_num_pages
        ));
    } else {
        wp_send_json_error('No hay más productos');
    }
}
add_action('wp_ajax_cargar_mas_relacionados', 'saico_ajax_cargar_mas_relacionados');
add_action('wp_ajax_nopriv_cargar_mas_relacionados', 'saico_ajax_cargar_mas_relacionados');

/**
 * Limpiar transients de productos relacionados cuando se actualiza un producto
 */
function saico_limpiar_transients_relacionados($producto_id) {
    global $wpdb;

    // Eliminar todos los transients que contengan el ID del producto
    $wpdb->query(
        $wpdb->prepare(
            "DELETE FROM $wpdb->options WHERE option_name LIKE %s",
            '%saico_relacionados_' . $producto_id . '%'
        )
    );
}
add_action('save_post_product', 'saico_limpiar_transients_relacionados');
