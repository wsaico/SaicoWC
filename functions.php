<?php
/**
 * Saico WC Theme Functions
 * Theme profesional optimizado para WooCommerce
 * Versión 1.0.0
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}



// Definir constantes del theme
define('SAICO_VERSION', '1.1.0');
define('SAICO_DIR', get_template_directory());
define('SAICO_URI', get_template_directory_uri());
define('SAICO_DEBUG', false);

/**
 * ============================================================================
 * MÓDULOS DEL THEME
 * ============================================================================
 * Sistema modular optimizado que carga solo lo necesario
 */

// Sistema de enqueue optimizado (consolidado)
require_once SAICO_DIR . '/inc/enqueue.php';

// Funciones globales reutilizables (singleton con caché)
require_once SAICO_DIR . '/inc/funciones-globales.php';

// Customizer para personalizaciones
require_once SAICO_DIR . '/inc/customizer.php';

// Funciones de productos relacionados (con caché)
require_once SAICO_DIR . '/inc/productos-relacionados.php';

// AJAX handlers
require_once SAICO_DIR . '/inc/ajax.php';

// Compatibilidad WooCommerce
require_once SAICO_DIR . '/inc/woocommerce.php';

// Sistema de AdSense sanitizado
require_once SAICO_DIR . '/inc/adsense.php';

// Metaboxes para control de sidebar
require_once SAICO_DIR . '/inc/metaboxes.php';

// Sistema de login seguro personalizado
require_once SAICO_DIR . '/inc/login-security.php';

// Sistema de SEO Fallback Description
require_once SAICO_DIR . '/inc/seo-fallback-description.php';

// Campos ACF personalizados para productos
require_once SAICO_DIR . '/inc/acf-fields.php';

// Sistema de login/registro AJAX
require_once SAICO_DIR . '/inc/ajax-login.php';

// Social Login (OAuth Google & Facebook)
require_once SAICO_DIR . '/inc/social-login.php';

/**
 * ============================================================================
 * CONFIGURACIÓN DEL THEME
 * ============================================================================
 */
function saico_setup() {
    // Soporte para traducción
    load_theme_textdomain('saico-wc', SAICO_DIR . '/languages');

    // Feeds automáticos
    add_theme_support('automatic-feed-links');

    // Título dinámico
    add_theme_support('title-tag');

    // Imágenes destacadas
    add_theme_support('post-thumbnails');

    // HTML5
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));

    // Logo personalizado
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 300,
        'flex-height' => true,
        'flex-width'  => true,
    ));

    // Soporte para menús
    add_theme_support('menus');

    // Soporte WooCommerce
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');

    // Editor de bloques
    add_theme_support('wp-block-styles');
    add_theme_support('responsive-embeds');
    add_theme_support('align-wide');

    // Tamaños de imagen personalizados
    add_image_size('saico-producto-thumb', 300, 300, true);
    add_image_size('saico-producto-medium', 600, 600, true);
    add_image_size('saico-producto-large', 900, 900, true);
    add_image_size('saico-hero', 1200, 675, true);

    // Registrar menús de navegación
    register_nav_menus(array(
        'primario' => __('Menú Principal', 'saico-wc'),
        'footer' => __('Menú Footer', 'saico-wc'),
        'movil' => __('Menú Móvil', 'saico-wc'),
    ));
}
add_action('after_setup_theme', 'saico_setup');

/**
 * ============================================================================
 * REGISTRAR ÁREAS DE WIDGETS
 * ============================================================================
 */
function saico_widgets_init() {
    // Sidebar principal
    register_sidebar(array(
        'name'          => __('Sidebar Principal', 'saico-wc'),
        'id'            => 'sidebar-principal',
        'description'   => __('Aparece en páginas de blog y posts', 'saico-wc'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-titulo">',
        'after_title'   => '</h3>',
    ));

    // Sidebar tienda
    register_sidebar(array(
        'name'          => __('Sidebar Tienda', 'saico-wc'),
        'id'            => 'sidebar-tienda',
        'description'   => __('Aparece en la tienda y archivo de productos', 'saico-wc'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-titulo">',
        'after_title'   => '</h3>',
    ));

    // Footer columnas (4 áreas)
    for ($i = 1; $i <= 4; $i++) {
        register_sidebar(array(
            'name'          => sprintf(__('Footer Columna %d', 'saico-wc'), $i),
            'id'            => 'footer-' . $i,
            'description'   => sprintf(__('Área de widgets para columna %d del footer', 'saico-wc'), $i),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="widget-titulo">',
            'after_title'   => '</h4>',
        ));
    }
}
add_action('widgets_init', 'saico_widgets_init');

/**
 * ============================================================================
 * DATOS ESTRUCTURADOS JSON-LD PARA SEO
 * ============================================================================
 */
function saico_producto_schema($product) {
    if (!$product || !is_a($product, 'WC_Product')) {
        return;
    }

    $product_id = $product->get_id();
    // Aplicar filtro SEO fallback para descripción
    $product_description = apply_filters('woocommerce_product_description', $product->get_description(), $product);
    $schema = array(
        '@context' => 'https://schema.org/',
        '@type' => 'Product',
        'name' => $product->get_name(),
        'description' => wp_strip_all_tags($product_description ?: $product->get_short_description()),
        'sku' => $product->get_sku(),
        'url' => get_permalink($product_id),
        'image' => wp_get_attachment_url($product->get_image_id()),
    );

    // Precio
    if (!$product->is_type('variable') && $product->get_price()) {
        $schema['offers'] = array(
            '@type' => 'Offer',
            'price' => $product->get_price(),
            'priceCurrency' => get_woocommerce_currency(),
            'availability' => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'seller' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
            ),
        );
    }

    // Rating
    $rating_count = $product->get_rating_count();
    if ($rating_count > 0) {
        $schema['aggregateRating'] = array(
            '@type' => 'AggregateRating',
            'ratingValue' => $product->get_average_rating(),
            'reviewCount' => $rating_count,
        );
    }

    // Autor
    $author_id = get_post_field('post_author', $product_id);
    if ($author_id) {
        $schema['brand'] = array(
            '@type' => 'Person',
            'name' => get_the_author_meta('display_name', $author_id),
        );
    }

    // Categoría
    $categories = get_the_terms($product_id, 'product_cat');
    if ($categories && !is_wp_error($categories)) {
        $category = reset($categories);
        $schema['category'] = $category->name;
    }

    echo '<script type="application/ld+json">' . wp_json_encode($schema) . '</script>' . "\n";
}

/**
 * ============================================================================
 * BREADCRUMBS
 * ============================================================================
 */
function saico_breadcrumbs() {
    if (is_front_page()) {
        return;
    }

    // Iconos SVG modernos
    $separador = '<span class="breadcrumb-separador"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg></span>';
    $home_icon = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>';

    echo '<nav class="saico-breadcrumb" aria-label="breadcrumb"><ol class="breadcrumb-lista">';
    echo '<li class="breadcrumb-item breadcrumb-home"><a href="' . esc_url(home_url()) . '">' . $home_icon . ' Inicio</a></li>' . $separador;

    if (is_singular('product')) {
        global $post;
        $terms = get_the_terms($post->ID, 'product_cat');
        if ($terms && !is_wp_error($terms)) {
            $term = array_shift($terms);
            echo '<li class="breadcrumb-item"><a href="' . esc_url(get_term_link($term)) . '">' . esc_html($term->name) . '</a></li>' . $separador;
        }
        echo '<li class="breadcrumb-item">' . esc_html(get_the_title()) . '</li>';
    } elseif (is_tax('product_cat')) {
        $term = get_queried_object();
        if ($term->parent) {
            $parent = get_term($term->parent, 'product_cat');
            echo '<li class="breadcrumb-item"><a href="' . esc_url(get_term_link($parent)) . '">' . esc_html($parent->name) . '</a></li>' . $separador;
        }
        echo '<li class="breadcrumb-item">' . esc_html($term->name) . '</li>';
    } elseif (is_post_type_archive('product') || is_shop()) {
        echo '<li class="breadcrumb-item">Tienda</li>';
    } elseif (is_category()) {
        echo '<li class="breadcrumb-item">' . esc_html(single_cat_title('', false)) . '</li>';
    } elseif (is_single()) {
        $category = get_the_category();
        if ($category) {
            echo '<li class="breadcrumb-item"><a href="' . esc_url(get_category_link($category[0]->term_id)) . '">' . esc_html($category[0]->name) . '</a></li>' . $separador;
        }
        echo '<li class="breadcrumb-item">' . esc_html(get_the_title()) . '</li>';
    } elseif (is_page()) {
        echo '<li class="breadcrumb-item">' . esc_html(get_the_title()) . '</li>';
    } elseif (is_search()) {
        echo '<li class="breadcrumb-item">Búsqueda: ' . esc_html(get_search_query()) . '</li>';
    } elseif (is_404()) {
        echo '<li class="breadcrumb-item">404 - Página no encontrada</li>';
    } elseif (is_author()) {
        echo '<li class="breadcrumb-item">Autor: ' . esc_html(get_the_author()) . '</li>';
    }

    echo '</ol></nav>';
}

/**
 * ============================================================================
 * COLORES DINÁMICOS PARA CATEGORÍAS
 * ============================================================================
 * Genera colores únicos para cada categoría basados en su ID
 */
function saico_get_categoria_color($categoria_id) {
    // Paleta de colores moderna con buen contraste
    $colores = array(
        array('bg' => 'rgba(59, 130, 246, 0.1)', 'text' => '#1d4ed8'),   // Azul
        array('bg' => 'rgba(16, 185, 129, 0.1)', 'text' => '#059669'),   // Verde
        array('bg' => 'rgba(239, 68, 68, 0.1)', 'text' => '#dc2626'),    // Rojo
        array('bg' => 'rgba(245, 158, 11, 0.1)', 'text' => '#d97706'),   // Naranja
        array('bg' => 'rgba(168, 85, 247, 0.1)', 'text' => '#7c3aed'),   // Púrpura
        array('bg' => 'rgba(236, 72, 153, 0.1)', 'text' => '#db2777'),   // Rosa
        array('bg' => 'rgba(14, 165, 233, 0.1)', 'text' => '#0284c7'),   // Cyan
        array('bg' => 'rgba(132, 204, 22, 0.1)', 'text' => '#65a30d'),   // Lima
        array('bg' => 'rgba(251, 146, 60, 0.1)', 'text' => '#ea580c'),   // Naranja oscuro
        array('bg' => 'rgba(99, 102, 241, 0.1)', 'text' => '#4f46e5'),   // Índigo
        array('bg' => 'rgba(20, 184, 166, 0.1)', 'text' => '#0d9488'),   // Teal
        array('bg' => 'rgba(244, 63, 94, 0.1)', 'text' => '#e11d48'),    // Rosa oscuro
    );

    // Usar módulo del ID para seleccionar color de forma consistente
    $index = $categoria_id % count($colores);
    return $colores[$index];
}

/**
 * ============================================================================
 * CONTADOR DE VISTAS DE PRODUCTOS
 * ============================================================================
 */
function saico_incrementar_vistas() {
    if (is_singular('product')) {
        global $post;
        $vistas = (int) get_post_meta($post->ID, '_vistas', true);
        update_post_meta($post->ID, '_vistas', $vistas + 1);
    }
}
add_action('wp_footer', 'saico_incrementar_vistas');

/**
 * ============================================================================
 * LIMITAR EXCERPT
 * ============================================================================
 */
function saico_excerpt_length($length) {
    return 20;
}
add_filter('excerpt_length', 'saico_excerpt_length');

/**
 * ============================================================================
 * DESHABILITAR EMOJIS PARA PERFORMANCE
 * ============================================================================
 */
function saico_deshabilitar_emojis() {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
}
add_action('init', 'saico_deshabilitar_emojis');

/**
 * ============================================================================
 * HELPER FUNCTIONS PARA HEADER
 * ============================================================================
 */

/**
 * Obtener placeholder de búsqueda
 */
function saico_get_search_placeholder() {
    return get_theme_mod('header_search_placeholder', 'Buscar productos...');
}

/**
 * Verificar si CTA está habilitado
 */
function saico_get_header_cta_show() {
    return get_theme_mod('header_cta_enabled', true);
}

/**
 * Obtener texto del CTA
 */
function saico_get_header_cta_text() {
    return get_theme_mod('header_cta_text', 'Únete Gratis');
}

/**
 * Obtener URL del CTA
 */
function saico_get_header_cta_url() {
    return get_theme_mod('header_cta_url', wc_get_page_permalink('myaccount'));
}

/**
 * Obtener URL de archivo MIDI del producto
 * Busca en los archivos descargables del producto
 *
 * @param int $product_id ID del producto
 * @return string|false URL del archivo MIDI o false si no existe
 */
function saico_get_midi_file_url($product_id) {
    if (!function_exists('wc_get_product')) {
        return false;
    }

    $product = wc_get_product($product_id);

    if (!$product || !method_exists($product, 'get_downloads')) {
        return false;
    }

    $downloads = $product->get_downloads();

    foreach ($downloads as $download) {
        $file_url = $download->get_file();
        $file_extension = strtolower(pathinfo($file_url, PATHINFO_EXTENSION));

        if (in_array($file_extension, array('mid', 'midi'))) {
            return $file_url;
        }
    }

    return false;
/**
 * ============================================================================
 * OPTIMIZACIONES SEO PARA PRODUCCIÓN
 * ============================================================================
 */

/**
 * Agregar meta tags Open Graph y Twitter Cards
 */
function saico_add_meta_tags() {
    if (is_singular('product')) {
        global $product;
        if (!$product) return;

        $title = $product->get_name();
        // Aplicar filtro SEO fallback para descripción
        $product_description = apply_filters('woocommerce_product_description', $product->get_description(), $product);
        $description = wp_strip_all_tags($product_description ?: $product->get_short_description());
        $image = wp_get_attachment_url($product->get_image_id());
        $url = get_permalink($product->get_id());

        // Open Graph
        echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr(substr($description, 0, 160)) . '">' . "\n";
        echo '<meta property="og:image" content="' . esc_url($image) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
        echo '<meta property="og:type" content="product">' . "\n";
        echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";

        // Twitter Cards
        echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr(substr($description, 0, 160)) . '">' . "\n";
        echo '<meta name="twitter:image" content="' . esc_url($image) . '">' . "\n";

        // Product specific meta
        if ($product->get_price()) {
            echo '<meta property="product:price:amount" content="' . esc_attr($product->get_price()) . '">' . "\n";
            echo '<meta property="product:price:currency" content="' . esc_attr(get_woocommerce_currency()) . '">' . "\n";
        }
    } elseif (is_front_page()) {
        // Open Graph para página principal
        echo '<meta property="og:title" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr(get_bloginfo('description')) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url(home_url('/')) . '">' . "\n";
        echo '<meta property="og:type" content="website">' . "\n";
        echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
    }
}
add_action('wp_head', 'saico_add_meta_tags', 1);

/**
 * Agregar schema markup para organización
 */
function saico_add_organization_schema() {
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => get_bloginfo('name'),
        'url' => home_url('/'),
        'description' => get_bloginfo('description'),
        'logo' => wp_get_attachment_url(get_theme_mod('custom_logo')),
        'sameAs' => array()
    );

    // Redes sociales del footer
    $social_links = array(
        get_theme_mod('saico_facebook_url'),
        get_theme_mod('saico_twitter_url'),
        get_theme_mod('saico_instagram_url'),
        get_theme_mod('saico_youtube_url')
    );

    foreach ($social_links as $link) {
        if (!empty($link)) {
            $schema['sameAs'][] = $link;
        }
    }

    echo '<script type="application/ld+json">' . wp_json_encode($schema) . '</script>' . "\n";
}
add_action('wp_head', 'saico_add_organization_schema');

/**
 * Optimizar títulos SEO
 */
function saico_seo_title($title) {
    if (is_singular('product')) {
        global $product;
        if ($product) {
            $title = $product->get_name() . ' | ' . get_bloginfo('name');
        }
    } elseif (is_post_type_archive('product')) {
        $title = 'Tienda | ' . get_bloginfo('name');
    }

    return $title;
}
add_filter('wp_title', 'saico_seo_title');
add_filter('document_title_parts', function($parts) {
    if (is_singular('product')) {
        global $product;
        if ($product) {
            $parts['title'] = $product->get_name();
        }
    }
    return $parts;
});

/**
 * Agregar meta description
 */
function saico_add_meta_description() {
    $description = '';

    if (is_singular('product')) {
        global $product;
        if ($product) {
            // Aplicar filtro SEO fallback para descripción
            $product_description = apply_filters('woocommerce_product_description', $product->get_description(), $product);
            $description = wp_strip_all_tags($product_description ?: $product->get_short_description());
            $description = substr($description, 0, 160);
        }
    } elseif (is_front_page()) {
        $description = get_bloginfo('description');
    } elseif (is_category() || is_tag()) {
        $description = get_the_archive_description();
    }

    if (!empty($description)) {
        echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
    }
}
add_action('wp_head', 'saico_add_meta_description', 1);

/**
 * Optimizar robots meta tag
 */
function saico_add_robots_meta() {
    $robots = 'index, follow';

    if (is_search()) {
        $robots = 'noindex, follow';
    } elseif (is_404()) {
        $robots = 'noindex, nofollow';
    }

    echo '<meta name="robots" content="' . esc_attr($robots) . '">' . "\n";
}
add_action('wp_head', 'saico_add_robots_meta', 1);

/**
 * Agregar canonical URL
 */
function saico_add_canonical_url() {
    if (is_singular()) {
        $canonical = get_permalink();
    } elseif (is_front_page()) {
        $canonical = home_url('/');
    } elseif (is_category() || is_tag() || is_tax()) {
        $canonical = get_term_link(get_queried_object());
    } elseif (is_post_type_archive()) {
        $canonical = get_post_type_archive_link(get_queried_object()->name);
    } elseif (is_author()) {
        $canonical = get_author_posts_url(get_queried_object_id());
    } elseif (is_search()) {
        $canonical = get_search_link();
    }

    if (isset($canonical) && !is_wp_error($canonical)) {
        echo '<link rel="canonical" href="' . esc_url($canonical) . '">' . "\n";
    }
}
add_action('wp_head', 'saico_add_canonical_url');

/**
 * Optimizar sitemap XML básico
 */
function saico_add_sitemap_url() {
    echo '<link rel="sitemap" type="application/xml" href="' . esc_url(home_url('/sitemap.xml')) . '">' . "\n";
}
add_action('wp_head', 'saico_add_sitemap_url');

/**
 * ============================================================================
 * OPTIMIZACIONES DE SEGURIDAD
 * ============================================================================
 */

/**
 * Mejorar headers de seguridad
 */
function saico_add_security_headers() {
    if (!is_admin()) {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }
}
add_action('send_headers', 'saico_add_security_headers');

/**
 * Sanitizar y validar datos de entrada en AJAX
 */
function saico_sanitize_ajax_data($data, $required_fields = array()) {
    $sanitized = array();

    foreach ($data as $key => $value) {
        if (is_array($value)) {
            $sanitized[$key] = saico_sanitize_ajax_data($value);
        } else {
            $sanitized[$key] = sanitize_text_field($value);
        }
    }

    // Verificar campos requeridos
    foreach ($required_fields as $field) {
        if (empty($sanitized[$field])) {
            wp_die(__('Datos requeridos faltantes', 'saico-wc'));
        }
    }

    return $sanitized;
}

/**
 * Verificar nonce en AJAX
 */
function saico_verify_ajax_nonce($nonce_action = 'saico_nonce') {
    if (!wp_verify_nonce($_POST['nonce'] ?? '', $nonce_action)) {
        wp_die(__('Verificación de seguridad fallida', 'saico-wc'));
    }
}

/**
 * ============================================================================
 * OPTIMIZACIONES DE PERFORMANCE
 * ============================================================================
 */

/**
 * Lazy loading para imágenes
 */
function saico_add_lazy_loading($content) {
    if (is_admin() || is_feed()) {
        return $content;
    }

    // Reemplazar imágenes con lazy loading
    $content = preg_replace_callback(
        '/<img([^>]+)src=["\']([^"\']+)["\']([^>]*)>/i',
        function($matches) {
            $before_src = $matches[1];
            $src = $matches[2];
            $after_src = $matches[3];

            // Skip si ya tiene loading o data-src
            if (strpos($before_src . $after_src, 'loading=') !== false ||
                strpos($before_src . $after_src, 'data-src') !== false) {
                return $matches[0];
            }

            return '<img' . $before_src . 'data-src="' . $src . '" src="data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 1 1\'%3E%3C/svg%3E" loading="lazy"' . $after_src . '>';
        },
        $content
    );

    return $content;
}
add_filter('the_content', 'saico_add_lazy_loading');
add_filter('post_thumbnail_html', 'saico_add_lazy_loading');

/**
 * Optimizar queries de productos
 */
function saico_optimize_product_queries($query) {
    if (!is_admin() && $query->is_main_query()) {
        if (is_post_type_archive('product') || is_shop()) {
            $query->set('posts_per_page', get_theme_mod('saico_products_per_page', 12));
            $query->set('meta_query', array(
                'relation' => 'OR',
                array(
                    'key' => '_visibility',
                    'value' => array('catalog', 'visible'),
                    'compare' => 'IN'
                ),
                array(
                    'key' => '_visibility',
                    'compare' => 'NOT EXISTS'
                )
            ));
        }
    }
}
add_action('pre_get_posts', 'saico_optimize_product_queries');

/**
 * Limpiar transients expirados
 */
function saico_clean_expired_transients() {
    global $wpdb;

    $expired = $wpdb->get_col(
        "SELECT option_name FROM {$wpdb->options}
         WHERE option_name LIKE '_transient_timeout_saico_%'
         AND option_value < " . time()
    );

    foreach ($expired as $transient) {
        $key = str_replace('_transient_timeout_', '', $transient);
        delete_transient($key);
    }
}
add_action('wp_scheduled_delete', 'saico_clean_expired_transients');

/**
 * ============================================================================
 * PREPARACIÓN PARA PRODUCCIÓN
 * ============================================================================
 */

/**
 * Actualizar versión del theme
 */
function saico_update_theme_version() {
    $theme = wp_get_theme();
    $version = $theme->get('Version');

    // Incrementar versión para producción
    $new_version = '1.1.0';

    if (version_compare($version, $new_version, '<')) {
        // Aquí iría la lógica para actualizar la versión en style.css
        // Por ahora solo registramos que estamos listos para producción
        update_option('saico_production_ready', true);
    }
}
add_action('after_setup_theme', 'saico_update_theme_version');

/**
 * Verificar compatibilidad con WordPress y WooCommerce
 */
function saico_check_compatibility() {
    $errors = array();

    // Verificar WordPress
    if (version_compare(get_bloginfo('version'), '5.0', '<')) {
        $errors[] = 'Requiere WordPress 5.0 o superior';
    }

    // Verificar WooCommerce
    if (!class_exists('WooCommerce')) {
        $errors[] = 'Requiere WooCommerce instalado y activado';
    } elseif (version_compare(WC()->version, '5.0', '<')) {
        $errors[] = 'Requiere WooCommerce 5.0 o superior';
    }

    // Verificar PHP
    if (version_compare(PHP_VERSION, '7.4', '<')) {
        $errors[] = 'Requiere PHP 7.4 o superior';
    }

    if (!empty($errors)) {
        add_action('admin_notices', function() use ($errors) {
            echo '<div class="notice notice-error"><p><strong>Saico WC Theme:</strong> ' . implode('<br>', $errors) . '</p></div>';
        });
    }
}
add_action('admin_init', 'saico_check_compatibility');

/**
 * Limpiar código para producción
 */
function saico_production_cleanup() {
    // Remover query strings de assets en producción
    if (!SAICO_DEBUG) {
        add_filter('script_loader_src', 'saico_remove_query_strings', 15, 1);
        add_filter('style_loader_src', 'saico_remove_query_strings', 15, 1);
    }
}

function saico_remove_query_strings($src) {
    if (strpos($src, 'ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_action('wp_enqueue_scripts', 'saico_production_cleanup', 999);
}
