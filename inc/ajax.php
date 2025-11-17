<?php
/**
 * Handlers AJAX del theme
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * ============================================================================
 * BÚSQUEDA AJAX
 * ============================================================================
 */

/**
 * Búsqueda de productos en tiempo real
 */
function saico_ajax_buscar_productos() {
    check_ajax_referer('saico_nonce', 'nonce');

    $termino = isset($_POST['termino']) ? sanitize_text_field($_POST['termino']) : '';

    if (strlen($termino) < 2) {
        wp_send_json_error('Término muy corto');
    }

    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 5,
        's' => $termino,
        'post_status' => 'publish'
    );

    $query = new WP_Query($args);
    $resultados = array();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            global $product;

            $resultados[] = array(
                'titulo' => get_the_title(),
                'url' => get_permalink(),
                'imagen' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'),
                'precio' => $product->get_price_html()
            );
        }
        wp_reset_postdata();
    }

    wp_send_json_success($resultados);
}
add_action('wp_ajax_buscar_productos', 'saico_ajax_buscar_productos');
add_action('wp_ajax_nopriv_buscar_productos', 'saico_ajax_buscar_productos');

/**
 * ============================================================================
 * SISTEMA DE LIKES
 * ============================================================================
 */

/**
 * Toggle like en producto
 */
function saico_ajax_toggle_like() {
    check_ajax_referer('saico_nonce', 'nonce');

    $producto_id = isset($_POST['producto_id']) ? intval($_POST['producto_id']) : 0;

    if (!$producto_id) {
        wp_send_json_error('ID inválido');
    }

    // Obtener likes actuales
    $likes = (int) get_post_meta($producto_id, '_likes', true);

    // Verificar si el usuario ya dio like (por IP o user ID)
    $user_id = get_current_user_id();
    $user_ip = $_SERVER['REMOTE_ADDR'];
    $likes_data = get_post_meta($producto_id, '_likes_data', true);

    if (!is_array($likes_data)) {
        $likes_data = array();
    }

    $identificador = $user_id ? 'user_' . $user_id : 'ip_' . $user_ip;

    // Toggle like
    if (isset($likes_data[$identificador])) {
        // Ya tiene like, remover
        unset($likes_data[$identificador]);
        $likes = max(0, $likes - 1);
        $tiene_like = false;
    } else {
        // No tiene like, agregar
        $likes_data[$identificador] = time();
        $likes++;
        $tiene_like = true;
    }

    // Actualizar meta
    update_post_meta($producto_id, '_likes', $likes);
    update_post_meta($producto_id, '_likes_data', $likes_data);

    // Limpiar caché
    Saico_Funciones_Globales::limpiar_cache_producto($producto_id);

    wp_send_json_success(array(
        'likes' => $likes,
        'tiene_like' => $tiene_like
    ));
}
add_action('wp_ajax_toggle_like', 'saico_ajax_toggle_like');
add_action('wp_ajax_nopriv_toggle_like', 'saico_ajax_toggle_like');

/**
 * ============================================================================
 * FILTROS DE PRODUCTOS
 * ============================================================================
 */

/**
 * Filtrar productos por tipo (gratis, premium, nuevo, popular)
 */
function saico_ajax_filtrar_productos() {
    check_ajax_referer('saico_nonce', 'nonce');

    $filtro = isset($_POST['filtro']) ? sanitize_text_field($_POST['filtro']) : 'todos';
    $pagina = isset($_POST['pagina']) ? intval($_POST['pagina']) : 1;
    $por_pagina = function_exists('saico_productos_por_pagina') ? saico_productos_por_pagina() : 12;

    $args = array(
        'post_type' => 'product',
        'posts_per_page' => $por_pagina,
        'paged' => $pagina,
        'post_status' => 'publish'
    );

    // Aplicar filtro
    switch ($filtro) {
        case 'gratis':
            $args['meta_query'] = array(
                'relation' => 'OR',
                array(
                    'key' => '_price',
                    'value' => '0',
                    'compare' => '='
                ),
                array(
                    'key' => '_price',
                    'compare' => 'NOT EXISTS'
                )
            );
            break;

        case 'premium':
            $args['meta_query'] = array(
                array(
                    'key' => '_price',
                    'value' => '0',
                    'compare' => '>',
                    'type' => 'NUMERIC'
                )
            );
            break;

        case 'nuevo':
            $args['date_query'] = array(
                array(
                    'after' => '30 days ago'
                )
            );
            $args['orderby'] = 'date';
            $args['order'] = 'DESC';
            break;

        case 'popular':
            $args['meta_key'] = 'somdn_dlcount';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
            break;
    }

    $query = new WP_Query($args);

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
            'tiene_mas' => $pagina < $query->max_num_pages,
            'total' => $query->found_posts
        ));
    } else {
        wp_send_json_error('No se encontraron productos');
    }
}
add_action('wp_ajax_saico_ajax_filtrar_productos', 'saico_ajax_filtrar_productos');
add_action('wp_ajax_nopriv_saico_ajax_filtrar_productos', 'saico_ajax_filtrar_productos');

/**
 * ============================================================================
 * INFINITE SCROLL
 * ============================================================================
 */

/**
 * Cargar más productos (infinite scroll)
 */
function saico_ajax_infinite_scroll() {
    check_ajax_referer('saico_ajax_nonce', 'nonce');

    $pagina = isset($_POST['pagina']) ? intval($_POST['pagina']) : 1;
    $por_pagina = function_exists('saico_productos_por_pagina') ? saico_productos_por_pagina() : 12;

    $args = array(
        'post_type' => 'product',
        'posts_per_page' => $por_pagina,
        'paged' => $pagina,
        'post_status' => 'publish'
    );

    $query = new WP_Query($args);

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
add_action('wp_ajax_infinite_scroll', 'saico_ajax_infinite_scroll');
add_action('wp_ajax_nopriv_infinite_scroll', 'saico_ajax_infinite_scroll');

/**
 * ============================================================================
 * PRODUCTOS RELACIONADOS
 * ============================================================================
 */

/**
 * Cargar más productos relacionados vía AJAX
 */
function saico_ajax_cargar_relacionados() {
    check_ajax_referer('saico_nonce', 'nonce');

    $producto_id = isset($_POST['producto_id']) ? intval($_POST['producto_id']) : 0;
    $pagina = isset($_POST['pagina']) ? intval($_POST['pagina']) : 1;
    $limite = isset($_POST['limite']) ? intval($_POST['limite']) : 4;

    if (!$producto_id) {
        wp_send_json_error('ID de producto inválido');
    }

    // Usar función del theme para obtener relacionados
    $productos_relacionados = saico_obtener_productos_relacionados($producto_id, $limite, $pagina);

    if (!$productos_relacionados || !$productos_relacionados->have_posts()) {
        wp_send_json_error('No hay más productos relacionados');
    }

    ob_start();

    while ($productos_relacionados->have_posts()) {
        $productos_relacionados->the_post();
        wc_get_template_part('loop/card-producto', 'min');
    }

    wp_reset_postdata();

    $html = ob_get_clean();

    wp_send_json_success(array(
        'html' => $html,
        'tiene_mas' => $pagina < $productos_relacionados->max_num_pages,
        'max_pages' => $productos_relacionados->max_num_pages
    ));
}
add_action('wp_ajax_cargar_relacionados', 'saico_ajax_cargar_relacionados');
add_action('wp_ajax_nopriv_cargar_relacionados', 'saico_ajax_cargar_relacionados');

/**
 * ============================================================================
 * BÚSQUEDA PARA HEADER
 * ============================================================================
 */

/**
 * Búsqueda de productos para el header (con HTML)
 */
function saico_buscar_productos() {
    check_ajax_referer('saico_nonce', 'nonce');

    $query = isset($_POST['query']) ? sanitize_text_field($_POST['query']) : '';

    if (strlen($query) < 2) {
        wp_send_json_error('Término muy corto');
    }

    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 6,
        's' => $query,
        'post_status' => 'publish'
    );

    $productos = new WP_Query($args);

    if (!$productos->have_posts()) {
        wp_send_json_success(array('html' => '<div class="saico-search-empty">No se encontraron productos</div>'));
    }

    ob_start();
    echo '<div class="saico-search-items">';

    while ($productos->have_posts()) {
        $productos->the_post();
        global $product;

        if (!$product) continue;

        $thumbnail = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail');
        ?>
        <a href="<?php echo get_permalink(); ?>" class="saico-search-item">
            <?php if ($thumbnail) : ?>
                <img src="<?php echo esc_url($thumbnail); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" class="saico-search-thumb">
            <?php else : ?>
                <div class="saico-search-no-thumb">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <path d="m21 15-5-5L5 21"></path>
                    </svg>
                </div>
            <?php endif; ?>
            <div class="saico-search-info">
                <h4><?php echo get_the_title(); ?></h4>
                <div class="saico-search-price"><?php echo $product->get_price_html(); ?></div>
            </div>
        </a>
        <?php
    }

    echo '</div>';

    if ($productos->found_posts > 6) {
        echo '<div class="saico-search-footer">';
        echo '<a href="' . esc_url(wc_get_page_permalink('shop') . '?s=' . urlencode($query)) . '">Ver todos los resultados (' . $productos->found_posts . ')</a>';
        echo '</div>';
    }

    wp_reset_postdata();

    $html = ob_get_clean();
    wp_send_json_success(array('html' => $html));
}
add_action('wp_ajax_saico_buscar_productos', 'saico_buscar_productos');
add_action('wp_ajax_nopriv_saico_buscar_productos', 'saico_buscar_productos');

/**
 * Obtener contador del carrito
 */
function saico_get_cart_count() {
    check_ajax_referer('saico_nonce', 'nonce');

    if (!class_exists('WooCommerce')) {
        wp_send_json_error('WooCommerce no está activo');
    }

    $count = WC()->cart->get_cart_contents_count();

    wp_send_json_success(array('count' => $count));
}
add_action('wp_ajax_saico_get_cart_count', 'saico_get_cart_count');
add_action('wp_ajax_nopriv_saico_get_cart_count', 'saico_get_cart_count');

/**
 * ============================================================================
 * PÁGINA DE AUTOR
 * ============================================================================
 */

/**
 * Cargar más productos del autor
 */
function saico_cargar_autor_productos() {
    check_ajax_referer('saico_nonce', 'nonce');

    $autor_id = isset($_POST['autor_id']) ? intval($_POST['autor_id']) : 0;
    $pagina = isset($_POST['pagina']) ? intval($_POST['pagina']) : 1;

    if (!$autor_id) {
        wp_send_json_error('ID de autor inválido');
    }

    $args = array(
        'post_type' => 'product',
        'author' => $autor_id,
        'posts_per_page' => 12,
        'paged' => $pagina,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC'
    );

    $query = new WP_Query($args);

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
            'tiene_mas' => $pagina < $query->max_num_pages,
            'pagina_actual' => $pagina,
            'total_paginas' => $query->max_num_pages
        ));
    } else {
        wp_send_json_error('No se encontraron más productos');
    }
}
add_action('wp_ajax_saico_cargar_autor_productos', 'saico_cargar_autor_productos');
add_action('wp_ajax_nopriv_saico_cargar_autor_productos', 'saico_cargar_autor_productos');

/**
 * Cargar más posts del autor
 */
function saico_cargar_autor_posts() {
    check_ajax_referer('saico_nonce', 'nonce');

    $autor_id = isset($_POST['autor_id']) ? intval($_POST['autor_id']) : 0;
    $pagina = isset($_POST['pagina']) ? intval($_POST['pagina']) : 1;

    if (!$autor_id) {
        wp_send_json_error('ID de autor inválido');
    }

    $args = array(
        'post_type' => 'post',
        'author' => $autor_id,
        'posts_per_page' => 10,
        'paged' => $pagina,
        'post_status' => 'publish',
        'orderby' => 'date',
        'order' => 'DESC'
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        ob_start();

        while ($query->have_posts()) {
            $query->the_post();

            // Template del post card
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('saico-post-card'); ?>>
                <a href="<?php the_permalink(); ?>" class="post-card-imagen">
                    <?php if (has_post_thumbnail()): ?>
                        <?php the_post_thumbnail('medium_large'); ?>
                    <?php else: ?>
                        <div class="post-card-sin-imagen">
                            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                        </div>
                    <?php endif; ?>
                </a>

                <div class="post-card-contenido">
                    <div class="post-card-meta">
                        <?php
                        $categorias = get_the_category();
                        if ($categorias):
                        ?>
                        <a href="<?php echo esc_url(get_category_link($categorias[0]->term_id)); ?>" class="post-categoria">
                            <?php echo esc_html($categorias[0]->name); ?>
                        </a>
                        <span class="post-card-meta-separador"></span>
                        <?php endif; ?>

                        <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                            <?php echo esc_html(get_the_date()); ?>
                        </time>
                    </div>

                    <h2 class="post-card-titulo">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h2>

                    <div class="post-card-excerpt">
                        <?php echo wp_trim_words(get_the_excerpt(), 30, '...'); ?>
                    </div>

                    <a href="<?php the_permalink(); ?>" class="post-card-leer-mas">
                        Leer más
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                </div>
            </article>
            <?php
        }

        wp_reset_postdata();
        $html = ob_get_clean();

        wp_send_json_success(array(
            'html' => $html,
            'tiene_mas' => $pagina < $query->max_num_pages,
            'pagina_actual' => $pagina,
            'total_paginas' => $query->max_num_pages
        ));
    } else {
        wp_send_json_error('No se encontraron más posts');
    }
}
add_action('wp_ajax_saico_cargar_autor_posts', 'saico_cargar_autor_posts');
add_action('wp_ajax_nopriv_saico_cargar_autor_posts', 'saico_cargar_autor_posts');

/**
 * Toggle Like/Unlike en productos
 */
function saico_toggle_like() {
    check_ajax_referer('saico_likes_nonce', 'nonce');

    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

    if (!$product_id) {
        wp_send_json_error(array('message' => 'Datos inválidos'));
    }

    $user_id = get_current_user_id();
    $likes_count = (int) get_post_meta($product_id, '_likes_count', true);
    $liked = false;

    if ($user_id) {
        // Usuario logueado: guardar en user meta
        $user_likes = get_user_meta($user_id, '_product_likes', true);
        if (!is_array($user_likes)) {
            $user_likes = array();
        }

        if (in_array($product_id, $user_likes)) {
            // Quitar like
            $user_likes = array_diff($user_likes, array($product_id));
            $likes_count = max(0, $likes_count - 1);
            $liked = false;
        } else {
            // Agregar like
            $user_likes[] = $product_id;
            $likes_count++;
            $liked = true;
        }

        update_user_meta($user_id, '_product_likes', array_values($user_likes));
    } else {
        // Usuario no logueado: usar cookies
        $cookie_likes = isset($_COOKIE['saico_likes']) ? json_decode(stripslashes($_COOKIE['saico_likes']), true) : array();
        if (!is_array($cookie_likes)) {
            $cookie_likes = array();
        }

        if (in_array($product_id, $cookie_likes)) {
            // Quitar like
            $cookie_likes = array_diff($cookie_likes, array($product_id));
            $likes_count = max(0, $likes_count - 1);
            $liked = false;
        } else {
            // Agregar like
            $cookie_likes[] = $product_id;
            $likes_count++;
            $liked = true;
        }

        // Setear cookie por 1 año
        setcookie('saico_likes', json_encode(array_values($cookie_likes)), time() + (365 * 24 * 60 * 60), '/');
    }

    // Actualizar contador en BD
    update_post_meta($product_id, '_likes_count', $likes_count);

    wp_send_json_success(array(
        'liked' => $liked,
        'likes_count' => $likes_count,
        'message' => $liked ? '¡Te encanta este producto!' : 'Ya no te encanta este producto'
    ));
}
add_action('wp_ajax_saico_toggle_like', 'saico_toggle_like');
add_action('wp_ajax_nopriv_saico_toggle_like', 'saico_toggle_like');
