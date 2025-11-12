<?php
/**
 * Sistema de enqueue consolidado y optimizado
 * Carga condicional de assets solo donde se necesitan
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Función principal de enqueue - TODO en una sola función organizada
 */
function saico_enqueue_assets() {

    // ========================================================================
    // CSS GLOBAL - Se carga en TODAS las páginas
    // ========================================================================

    // Google Fonts - Noto Sans JP
    $font_family = get_theme_mod('saico_font_family', 'Noto Sans JP');
    if ($font_family) {
        $font_url = 'https://fonts.googleapis.com/css2?family=' . urlencode($font_family) . ':wght@300;400;500;600;700;800&display=swap';
        wp_enqueue_style('saico-google-fonts', $font_url, array(), null);
    }

    // Variables CSS (máxima prioridad)
    wp_enqueue_style(
        'saico-variables',
        SAICO_URI . '/assets/css/variables.css',
        array(),
        SAICO_VERSION
    );

    // Estilos base
    wp_enqueue_style(
        'saico-base',
        SAICO_URI . '/assets/css/base.css',
        array('saico-variables'),
        SAICO_VERSION
    );

    // Style.css principal
    wp_enqueue_style(
        'saico-main',
        get_stylesheet_uri(),
        array('saico-variables', 'saico-base'),
        SAICO_VERSION
    );

    // Header
    wp_enqueue_style(
        'saico-header',
        SAICO_URI . '/assets/css/header.css',
        array('saico-variables'),
        SAICO_VERSION
    );

    // Footer
    wp_enqueue_style(
        'saico-footer',
        SAICO_URI . '/assets/css/footer.css',
        array('saico-variables'),
        SAICO_VERSION
    );

    // ========================================================================
    // CSS CONDICIONAL - Solo en páginas específicas
    // ========================================================================

    // WooCommerce
    $es_woo = class_exists('WooCommerce') && (
        is_shop() ||
        is_product_category() ||
        is_product_tag() ||
        is_product() ||
        is_front_page() ||
        is_search() ||
        is_archive() ||
        is_account_page() ||
        is_cart() ||
        is_checkout()
    );

    if ($es_woo) {
        // Productos relacionados
        wp_enqueue_style('saico-relacionados', SAICO_URI . '/assets/css/relacionados.css', array('saico-variables'), SAICO_VERSION);

        // Paginación
        wp_enqueue_style('saico-paginacion', SAICO_URI . '/assets/css/paginacion.css', array('saico-variables'), SAICO_VERSION);

        // Títulos de productos - TODO: crear archivo
        // wp_enqueue_style('saico-titulos-producto', SAICO_URI . '/assets/css/titulos-producto.css', array('saico-variables'), SAICO_VERSION);

        // Cards minimalistas (SOLO este diseño según requisitos)
        wp_enqueue_style('saico-cards-min', SAICO_URI . '/assets/css/cards-minimalistas.css', array('saico-variables'), SAICO_VERSION);

        // Compatibilidad WC
        wp_enqueue_style('saico-wc-compat', SAICO_URI . '/assets/css/wc-compatibilidad.css', array('saico-variables'), SAICO_VERSION);
    }

    // Front Page
    if (is_front_page()) {
        wp_enqueue_style('saico-frontpage', SAICO_URI . '/assets/css/frontpage.css', array('saico-variables'), SAICO_VERSION);
    }

    // Blog
    if (is_singular('post') || (is_home() && !is_front_page()) || (is_archive() && (is_author() || is_date() || is_category() || is_tag()))) {
        wp_enqueue_style('saico-blog', SAICO_URI . '/assets/css/blog.css', array('saico-variables'), SAICO_VERSION);
    }

    // Single Product
    if (function_exists('is_product') && is_product()) {
        wp_enqueue_style('saico-producto-single', SAICO_URI . '/assets/css/producto-single.css', array('saico-variables'), SAICO_VERSION);
        wp_enqueue_style('saico-reproductor-producto', SAICO_URI . '/assets/css/reproductor-producto.css', array('saico-variables'), SAICO_VERSION);
        wp_enqueue_style('saico-estadisticas-producto', SAICO_URI . '/assets/css/estadisticas-producto.css', array('saico-variables'), SAICO_VERSION);
        // wp_enqueue_style('saico-sidebar-producto', SAICO_URI . '/assets/css/sidebar-producto.css', array('saico-variables'), SAICO_VERSION);
        // wp_enqueue_style('saico-modal-relacionados', SAICO_URI . '/assets/css/modal-relacionados.css', array(), SAICO_VERSION);
    }

    // Checkout
    if (function_exists('is_checkout') && is_checkout()) {
        wp_enqueue_style('saico-checkout', SAICO_URI . '/assets/css/checkout.css', array('saico-variables'), SAICO_VERSION);
    }

    // Sidebar tienda
    if (is_active_sidebar('sidebar-tienda') && (is_shop() || is_product_category() || is_product_tag() || is_search())) {
        wp_enqueue_style('saico-sidebar', SAICO_URI . '/assets/css/sidebar.css', array('saico-variables'), SAICO_VERSION);
    }

    // Widgets
    if (is_active_sidebar('sidebar-tienda') || is_active_sidebar('footer-1') || is_active_sidebar('footer-2')) {
        wp_enqueue_style('saico-widgets', SAICO_URI . '/assets/css/widgets.css', array('saico-variables'), SAICO_VERSION);
    }

    // ========================================================================
    // JAVASCRIPT GLOBAL - Se carga en TODAS las páginas (FOOTER)
    // ========================================================================

    // jQuery (WordPress core)
    wp_enqueue_script('jquery');

    // Sistema Global de Audio (para reproducción en cards)
    wp_enqueue_script(
        'saico-global-audio',
        SAICO_URI . '/assets/js/global-audio.js',
        array('jquery'),
        SAICO_VERSION,
        true
    );

    // Sistema de modales (global - usado por header para login/registro)
    wp_enqueue_script(
        'saico-modales',
        SAICO_URI . '/assets/js/modales.js',
        array('jquery'),
        SAICO_VERSION,
        true
    );

    // Header (navegación, menú móvil, búsqueda)
    wp_enqueue_script(
        'saico-header',
        SAICO_URI . '/assets/js/header.js',
        array('jquery', 'saico-modales'),
        SAICO_VERSION,
        true
    );

    // Localizar datos para header
    wp_localize_script('saico-header', 'saicoData', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('saico_nonce'),
        'cartUrl' => class_exists('WooCommerce') ? wc_get_cart_url() : home_url('/carrito/'),
        'shopUrl' => class_exists('WooCommerce') ? wc_get_page_permalink('shop') : home_url('/tienda/'),
        'myAccountUrl' => class_exists('WooCommerce') ? wc_get_page_permalink('myaccount') : home_url('/mi-cuenta/'),
        'isLoggedIn' => is_user_logged_in(),
        'animatedButtonTime' => get_theme_mod('animated_button_time', 10)
    ));

    // Lazy loading - TODO: crear archivo
    // wp_enqueue_script(
    //     'saico-lazy',
    //     SAICO_URI . '/assets/js/lazy.js',
    //     array(),
    //     SAICO_VERSION,
    //     true
    // );

    // ========================================================================
    // JAVASCRIPT CONDICIONAL - Solo en páginas específicas
    // ========================================================================

    // WooCommerce
    if ($es_woo) {
        // Scripts de cards minimalistas (depende del sistema global de audio)
        wp_enqueue_script('saico-cards-min', SAICO_URI . '/assets/js/cards-minimalistas.js', array('jquery', 'saico-global-audio'), SAICO_VERSION, true);

        // Scripts nativos WooCommerce
        if (function_exists('is_woocommerce')) {
            wp_enqueue_script('wc-add-to-cart');
            wp_enqueue_script('woocommerce');
        }
    }

    // Front Page
    if (is_front_page()) {
        wp_enqueue_script('saico-frontpage', SAICO_URI . '/assets/js/frontpage.js', array('jquery'), SAICO_VERSION, true);
        wp_enqueue_script('saico-hero-player', SAICO_URI . '/assets/js/hero-player.js', array('jquery'), SAICO_VERSION, true);
    }

    // Author Page
    if (is_author()) {
        wp_enqueue_style('saico-author', SAICO_URI . '/assets/css/author.css', array(), SAICO_VERSION);
        wp_enqueue_script('saico-author', SAICO_URI . '/assets/js/author.js', array('jquery'), SAICO_VERSION, true);
    }

    // Single Product
    if (function_exists('is_product') && is_product()) {
        // WaveSurfer.js para audio waveform
        wp_enqueue_script('wavesurfer', 'https://unpkg.com/wavesurfer.js@7.7.3/dist/wavesurfer.min.js', array(), '7.7.3', true);

        // Script de producto
        wp_enqueue_script('saico-producto', SAICO_URI . '/assets/js/producto-single.js', array('jquery', 'wavesurfer'), SAICO_VERSION, true);

        // Reproductor de audio/MIDI
        wp_enqueue_script('saico-reproductor-producto', SAICO_URI . '/assets/js/reproductor-producto.js', array('jquery'), SAICO_VERSION, true);

        // Sistema de reviews con estrellas
        wp_enqueue_script('saico-product-reviews', SAICO_URI . '/assets/js/product-reviews.js', array('jquery'), SAICO_VERSION, true);

        // Sistema de likes
        wp_enqueue_script('saico-likes-producto', SAICO_URI . '/assets/js/likes-producto.js', array('jquery'), SAICO_VERSION, true);
        wp_localize_script('saico-likes-producto', 'saicoLikes', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('saico_likes_nonce')
        ));

        // Localizar saicoData para producto-single.js
        wp_localize_script('saico-producto', 'saicoData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('saico_nonce'),
            'animatedButtonTime' => get_theme_mod('animated_button_time', 10),
            'modalEnabled' => get_theme_mod('enable_download_modal', true),
            'pageViewEnabled' => get_theme_mod('enable_download_page_view', false)
        ));

        // Script de sidebar - TODO: crear archivo
        // wp_enqueue_script('saico-sidebar-producto', SAICO_URI . '/assets/js/sidebar-producto.js', array('jquery'), SAICO_VERSION, true);

        // Scripts sociales (like, compartir)
        wp_enqueue_script('saico-social', SAICO_URI . '/assets/js/social.js', array('jquery'), SAICO_VERSION, true);

        // Localizar para AJAX de productos relacionados (modales.js ya está cargado globalmente)
        wp_localize_script('saico-modales', 'saicoProducto', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('saico_producto_nonce'),
            'productoId' => get_the_ID()
        ));
    }

    // Author page
    if (is_author()) {
        wp_enqueue_script('saico-autor', SAICO_URI . '/assets/js/autor-tabs.js', array('jquery'), SAICO_VERSION, true);
    }

    // Checkout
    if (function_exists('is_checkout') && is_checkout()) {
        wp_enqueue_script('saico-checkout', SAICO_URI . '/assets/js/checkout.js', array('jquery'), SAICO_VERSION, true);
    }

    // Sidebar toggle
    if (is_active_sidebar('sidebar-tienda') && (is_shop() || is_product_category() || is_product_tag())) {
        wp_enqueue_script('saico-sidebar-toggle', SAICO_URI . '/assets/js/sidebar-toggle.js', array('jquery'), SAICO_VERSION, true);
    }

    // ========================================================================
    // LIBRERÍAS EXTERNAS
    // ========================================================================

    // Font Awesome
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');

    // Google Fonts (opcional)
    $fuente = get_theme_mod('saico_fuente_global', 'Inter');
    if ($fuente !== 'sistema') {
        wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=' . $fuente . ':wght@300;400;500;600;700&display=swap', array(), null);
    }
}
add_action('wp_enqueue_scripts', 'saico_enqueue_assets');

/**
 * ============================================================================
 * OPTIMIZACIONES DE PAGESPEED (SEGURAS - NO ROMPEN FUNCIONALIDADES)
 * ============================================================================
 */

/**
 * 1. Remover recursos innecesarios de WordPress
 */
function saico_remover_assets_innecesarios() {
    // Remover emojis (no usados)
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');

    // Remover block library CSS (no usamos Gutenberg en frontend)
    if (!is_admin() && !is_singular('post')) {
        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('wp-block-library-theme');
        wp_dequeue_style('classic-theme-styles');
        wp_dequeue_style('global-styles');
    }

    // Remover generadores y meta tags innecesarios
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
}
add_action('wp_enqueue_scripts', 'saico_remover_assets_innecesarios', 100);

/**
 * 2. Deshabilitar embeds de WordPress
 */
function saico_disable_embeds() {
    wp_deregister_script('wp-embed');
}
add_action('wp_footer', 'saico_disable_embeds');

/**
 * 3. Preconnect a recursos externos (Google Fonts, CDNs)
 */
function saico_add_resource_hints() {
    // Preconnect a Google Fonts
    echo '<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";

    // DNS prefetch para CDNs
    echo '<link rel="dns-prefetch" href="//cdnjs.cloudflare.com">' . "\n";
    echo '<link rel="dns-prefetch" href="//unpkg.com">' . "\n";
}
add_action('wp_head', 'saico_add_resource_hints', 1);

/**
 * 4. Preload de recursos críticos
 */
function saico_preload_critical_assets() {
    // Preload CSS crítico
    echo '<link rel="preload" href="' . SAICO_URI . '/assets/css/variables.css" as="style">' . "\n";
    echo '<link rel="preload" href="' . SAICO_URI . '/assets/css/base.css" as="style">' . "\n";
    echo '<link rel="preload" href="' . SAICO_URI . '/assets/css/header.css" as="style">' . "\n";

    // Preload del logo
    $custom_logo_id = get_theme_mod('custom_logo');
    if ($custom_logo_id) {
        $logo_url = wp_get_attachment_image_url($custom_logo_id, 'full');
        if ($logo_url) {
            echo '<link rel="preload" href="' . esc_url($logo_url) . '" as="image">' . "\n";
        }
    }

    // Preload específico por página
    if (is_front_page()) {
        echo '<link rel="preload" href="' . SAICO_URI . '/assets/css/frontpage.css" as="style">' . "\n";
    }

    if (is_product()) {
        echo '<link rel="preload" href="' . SAICO_URI . '/assets/css/producto-single.css" as="style">' . "\n";
    }
}
add_action('wp_head', 'saico_preload_critical_assets', 2);

/**
 * 5. Lazy loading nativo para imágenes
 */
function saico_add_lazy_loading($attr) {
    if (!is_admin()) {
        $attr['loading'] = 'lazy';
        $attr['decoding'] = 'async';
    }
    return $attr;
}
add_filter('wp_get_attachment_image_attributes', 'saico_add_lazy_loading');

/**
 * 6. Defer SOLO para scripts NO críticos (SEGURO)
 * Scripts críticos NO tienen defer para no romper funcionalidades
 */
function saico_add_defer_to_safe_scripts($tag, $handle, $src) {
    // Scripts SEGUROS que pueden tener defer (no críticos, sin funciones globales en onclick)
    $defer_scripts = array(
        'saico-frontpage',      // Solo efectos visuales front-page
        'saico-hero-player',    // Solo hero section
        'saico-author',         // Solo author page
        'saico-autor',          // Solo author tabs
        'saico-sidebar-toggle', // Mejora progresiva
        'saico-checkout',       // Checkout (se carga después)
        'saico-lazy'            // Lazy loading (si existe)
    );

    // NO tocar estos scripts críticos (deben cargarse normalmente):
    // - jquery (base de todo)
    // - saico-modales (funciones globales: saicoAbrirModal, saicoCerrarModal)
    // - saico-header (menú hamburguesa)
    // - saico-global-audio (reproductor de audio)
    // - saico-cards-min (cards de productos)
    // - saico-producto (single product con modales y descargas)
    // - saico-reproductor-producto (reproductor single)
    // - saico-product-reviews (reviews)
    // - saico-likes-producto (sistema de likes)
    // - saico-social (compartir social)
    // - wavesurfer (librería externa necesaria)

    if (in_array($handle, $defer_scripts)) {
        return str_replace(' src', ' defer src', $tag);
    }

    return $tag;
}
add_filter('script_loader_tag', 'saico_add_defer_to_safe_scripts', 10, 3);

/**
 * 7. Cargar Google Fonts con display=swap (mejor performance)
 * Ya está implementado en la línea 287 con &display=swap
 */

