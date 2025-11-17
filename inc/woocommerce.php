<?php
/**
 * Compatibilidad y personalizaciones de WooCommerce
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Deshabilitar estilos por defecto de WooCommerce
 */
add_filter('woocommerce_enqueue_styles', '__return_empty_array');

/**
 * Cambiar número de productos por página
 */
function saico_productos_por_pagina() {
    return get_theme_mod('saico_productos_por_pagina', 12);
}
add_filter('loop_shop_per_page', 'saico_productos_por_pagina', 20);

/**
 * Cambiar columnas de productos en tienda
 */
function saico_columnas_productos() {
    return get_theme_mod('saico_columnas_productos', 4);
}
add_filter('loop_shop_columns', 'saico_columnas_productos');

/**
 * Deshabilitar zoom, lightbox y slider de galería (usamos nuestro sistema)
 */
function saico_deshabilitar_galeria_wc() {
    remove_theme_support('wc-product-gallery-zoom');
    remove_theme_support('wc-product-gallery-lightbox');
    remove_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'saico_deshabilitar_galeria_wc', 100);

/**
 * Remover acciones por defecto de WooCommerce
 */
function saico_remover_acciones_wc() {
    // Remover breadcrumb por defecto (usamos el nuestro)
    remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);

    // Remover wrappers por defecto
    remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
    remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

    // Remover sidebar por defecto
    remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

    // Remover resultado y ordenamiento (los personalizamos)
    remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
    remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
}
add_action('init', 'saico_remover_acciones_wc');

/**
 * Agregar wrappers personalizados
 */
// Wrappers personalizados deshabilitados para evitar duplicación con templates

/**
 * Localizar templates personalizados de WooCommerce
 */
function saico_localizar_template_wc($template, $template_name, $template_path) {
    $theme_path = SAICO_DIR . '/woocommerce/';

    if (file_exists($theme_path . $template_name)) {
        $template = $theme_path . $template_name;
    }

    return $template;
}
add_filter('woocommerce_locate_template', 'saico_localizar_template_wc', 10, 3);

/**
 * Modificar textos de WooCommerce
 */
function saico_textos_wc($translated) {
    $translated = str_replace('Añadir al carrito', 'Agregar', $translated);
    $translated = str_replace('Add to cart', 'Agregar', $translated);
    return $translated;
}
add_filter('gettext', 'saico_textos_wc');
add_filter('ngettext', 'saico_textos_wc');

/**
 * Habilitar comentarios para productos (reviews)
 */
function saico_habilitar_comentarios_productos($open, $post_id) {
    $post = get_post($post_id);
    if ($post->post_type == 'product') {
        return true;
    }
    return $open;
}
add_filter('comments_open', 'saico_habilitar_comentarios_productos', 10, 2);

/**
 * Agregar clase CSS al body en páginas WooCommerce
 */
function saico_body_class_wc($classes) {
    // Agregar clase para diseño minimalista de cards
    $classes[] = 'design-minimalist';

    if (is_shop() || is_product_category() || is_product_tag()) {
        $classes[] = 'saico-tienda';
    }

    if (is_product()) {
        $classes[] = 'saico-producto-single';
    }

    if (is_cart()) {
        $classes[] = 'saico-carrito';
    }

    if (is_checkout()) {
        $classes[] = 'saico-checkout';
    }

    return $classes;
}
add_filter('body_class', 'saico_body_class_wc');

/**
 * Personalizar fragmentos AJAX del carrito
 */
function saico_fragmentos_carrito($fragments) {
    ob_start();
    ?>
    <span class="saico-carrito-contador"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
    <?php
    $fragments['.saico-carrito-contador'] = ob_get_clean();

    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'saico_fragmentos_carrito');

/**
 * Agregar mensaje de producto agregado al carrito
 */
function saico_mensaje_agregado_carrito($message, $products) {
    // Si $products es un array, tomar el primer producto
    if (is_array($products)) {
        $product_id = reset($products);
    } else {
        $product_id = $products;
    }

    // Validar que el producto existe
    $producto = wc_get_product($product_id);
    if (!$producto || !is_a($producto, 'WC_Product')) {
        return $message; // Retornar mensaje original si no es válido
    }

    $titulo = $producto->get_name();

    $message = sprintf(
        '<div class="saico-mensaje-carrito">
            <i class="fas fa-check-circle"></i>
            <strong>%s</strong> agregado al carrito
        </div>',
        esc_html($titulo)
    );

    return $message;
}
add_filter('wc_add_to_cart_message_html', 'saico_mensaje_agregado_carrito', 10, 2);
