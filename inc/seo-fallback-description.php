<?php
/**
 * Sistema de Fallback Description - SEO Automático
 * Genera descripciones dinámicas cuando el campo está vacío
 *
 * @package SaicoWC
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * ============================================================================
 * HOOK PRINCIPAL - Inyectar descripción fallback
 * ============================================================================
 */
add_filter('woocommerce_product_description', 'saico_inject_fallback_description', 10, 1);

function saico_inject_fallback_description($description) {
    global $product;

    // Si no hay producto, retornar tal cual
    if (!$product || !is_a($product, 'WC_Product')) {
        return $description;
    }

    // Si ya tiene descripción, no hacer nada
    if (!empty(trim(strip_tags($description)))) {
        return $description;
    }

    // Verificar si el fallback está habilitado
    $fallback_enabled = get_theme_mod('enable_seo_fallback_description', true);

    if (!$fallback_enabled) {
        return $description;
    }

    // Generar descripción fallback
    $fallback_description = saico_generate_fallback_description($product);

    // Aplicar filtro para permitir personalización adicional
    $fallback_description = apply_filters('saico_fallback_description', $fallback_description, $product);

    return $fallback_description;
}

/**
 * ============================================================================
 * GENERADOR DE DESCRIPCIÓN FALLBACK
 * ============================================================================
 */
function saico_generate_fallback_description($product) {
    // Obtener template desde el customizer
    $template = get_theme_mod('seo_fallback_template', saico_get_default_fallback_template());

    // Si está vacío, usar template por defecto
    if (empty(trim($template))) {
        $template = saico_get_default_fallback_template();
    }

    // Reemplazar todos los ganchos/placeholders
    $description = saico_replace_description_placeholders($template, $product);

    // Sanitizar y formatear
    $description = wpautop($description); // Convertir saltos de línea a <p>

    return $description;
}

/**
 * ============================================================================
 * TEMPLATE POR DEFECTO
 * ============================================================================
 */
function saico_get_default_fallback_template() {
    return "Descarga {titulo} - {tipo} y de alta calidad. Disponible en {categorias}. {descripcion_corta}

Características principales:
• Tipo: {tipo}
• Categoría: {categoria_principal}
• Formato: Digital
• Descarga: Inmediata

¡Obtén {titulo} ahora mismo de forma {tipo_minuscula}!";
}

/**
 * ============================================================================
 * REEMPLAZAR PLACEHOLDERS/GANCHOS
 * ============================================================================
 */
function saico_replace_description_placeholders($template, $product) {
    $product_id = $product->get_id();

    // Obtener todos los datos del producto
    $placeholders = saico_get_product_placeholders($product);

    // Reemplazar cada placeholder en el template
    foreach ($placeholders as $placeholder => $value) {
        // Formato: {placeholder}
        $template = str_replace('{' . $placeholder . '}', $value, $template);
    }

    return $template;
}

/**
 * ============================================================================
 * OBTENER TODOS LOS PLACEHOLDERS DISPONIBLES
 * ============================================================================
 */
function saico_get_product_placeholders($product) {
    $product_id = $product->get_id();

    // Determinar si es gratis o de pago
    $price = $product->get_price();
    $is_free = empty($price) || $price == 0;
    $tipo = $is_free ? 'Gratis' : 'Premium';

    // Obtener categorías
    $categories = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'names'));
    $categoria_principal = !empty($categories) ? $categories[0] : 'Digital';
    $todas_categorias = !empty($categories) ? implode(', ', $categories) : 'productos digitales';

    // Obtener tags
    $tags = wp_get_post_terms($product_id, 'product_tag', array('fields' => 'names'));
    $todas_tags = !empty($tags) ? implode(', ', $tags) : '';

    // Obtener atributos
    $attributes = saico_get_product_attributes_list($product);

    // Descripción corta
    $short_description = $product->get_short_description();
    $short_description = !empty($short_description) ? strip_tags($short_description) : '';

    // Precio formateado
    $precio_formateado = $is_free ? 'Gratis' : wc_price($price);

    // Fecha de publicación
    $fecha_publicacion = get_the_date('d/m/Y', $product_id);

    // Autor (si está disponible)
    $author_id = get_post_field('post_author', $product_id);
    $author_name = get_the_author_meta('display_name', $author_id);

    // SKU
    $sku = $product->get_sku();

    // Stock status
    $stock_status = $product->get_stock_status();
    $disponibilidad = ($stock_status === 'instock') ? 'Disponible' : 'No disponible';

    // Rating
    $rating = $product->get_average_rating();
    $rating_text = $rating > 0 ? number_format($rating, 1) . ' estrellas' : '';

    // Reviews count
    $review_count = $product->get_review_count();
    $reviews_text = $review_count > 0 ? $review_count . ' opiniones' : '';

    // Nombre del sitio
    $sitename = get_bloginfo('name');

    // Array de placeholders
    $placeholders = array(
        // BÁSICOS
        'titulo' => $product->get_name(),
        'titulo_minuscula' => strtolower($product->get_name()),
        'titulo_mayuscula' => strtoupper($product->get_name()),

        // TIPO Y PRECIO
        'tipo' => $tipo,
        'tipo_minuscula' => strtolower($tipo),
        'precio' => $precio_formateado,
        'precio_numero' => $price,

        // CATEGORÍAS
        'categoria_principal' => $categoria_principal,
        'categoria' => $categoria_principal, // Alias
        'categorias' => $todas_categorias,

        // TAGS
        'tags' => $todas_tags,
        'etiquetas' => $todas_tags,

        // DESCRIPCIONES
        'descripcion_corta' => $short_description,
        'resumen' => $short_description,

        // ATRIBUTOS
        'atributos' => $attributes,

        // FECHAS
        'fecha' => $fecha_publicacion,
        'fecha_publicacion' => $fecha_publicacion,
        'año' => date('Y'),

        // AUTOR
        'autor' => $author_name,

        // DATOS TÉCNICOS
        'sku' => $sku,
        'disponibilidad' => $disponibilidad,

        // RATINGS Y REVIEWS
        'rating' => $rating_text,
        'calificacion' => $rating_text,
        'reviews' => $reviews_text,
        'opiniones' => $reviews_text,

        // SITIO
        'sitio' => $sitename,
        'sitioweb' => $sitename,

        // URL
        'url' => get_permalink($product_id),

        // SEO ADICIONAL
        'formato' => 'Digital',
        'descarga' => 'Inmediata',
    );

    // Permitir extensión de placeholders mediante filtro
    $placeholders = apply_filters('saico_description_placeholders', $placeholders, $product);

    return $placeholders;
}

/**
 * ============================================================================
 * OBTENER LISTA DE ATRIBUTOS
 * ============================================================================
 */
function saico_get_product_attributes_list($product) {
    $attributes = $product->get_attributes();
    $attributes_list = array();

    if (empty($attributes)) {
        return '';
    }

    foreach ($attributes as $attribute) {
        if (!$attribute->get_visible()) {
            continue;
        }

        $name = wc_attribute_label($attribute->get_name());

        if ($attribute->is_taxonomy()) {
            $values = wc_get_product_terms($product->get_id(), $attribute->get_name(), array('fields' => 'names'));
        } else {
            $values = $attribute->get_options();
        }

        if (!empty($values)) {
            $attributes_list[] = $name . ': ' . implode(', ', $values);
        }
    }

    return implode(' | ', $attributes_list);
}

/**
 * ============================================================================
 * FUNCIÓN AUXILIAR - Obtener lista de placeholders disponibles
 * ============================================================================
 */
function saico_get_available_placeholders_list() {
    return array(
        'Básicos' => array(
            '{titulo}' => 'Título del producto',
            '{titulo_minuscula}' => 'Título en minúsculas',
            '{titulo_mayuscula}' => 'Título en mayúsculas',
        ),
        'Tipo y Precio' => array(
            '{tipo}' => 'Tipo de producto (Gratis/Premium)',
            '{tipo_minuscula}' => 'Tipo en minúsculas',
            '{precio}' => 'Precio formateado',
            '{precio_numero}' => 'Precio solo número',
        ),
        'Categorías' => array(
            '{categoria_principal}' => 'Categoría principal',
            '{categoria}' => 'Alias de categoría principal',
            '{categorias}' => 'Todas las categorías',
        ),
        'Tags' => array(
            '{tags}' => 'Todas las etiquetas',
            '{etiquetas}' => 'Alias de tags',
        ),
        'Descripciones' => array(
            '{descripcion_corta}' => 'Descripción corta del producto',
            '{resumen}' => 'Alias de descripción corta',
        ),
        'Atributos' => array(
            '{atributos}' => 'Lista de atributos',
        ),
        'Fechas' => array(
            '{fecha}' => 'Fecha de publicación',
            '{fecha_publicacion}' => 'Alias de fecha',
            '{año}' => 'Año actual',
        ),
        'Autor' => array(
            '{autor}' => 'Nombre del autor',
        ),
        'Datos Técnicos' => array(
            '{sku}' => 'SKU del producto',
            '{disponibilidad}' => 'Estado de disponibilidad',
        ),
        'Ratings y Reviews' => array(
            '{rating}' => 'Calificación promedio',
            '{calificacion}' => 'Alias de rating',
            '{reviews}' => 'Número de opiniones',
            '{opiniones}' => 'Alias de reviews',
        ),
        'Sitio' => array(
            '{sitio}' => 'Nombre del sitio',
            '{sitioweb}' => 'Alias de sitio',
            '{url}' => 'URL del producto',
        ),
        'SEO Adicional' => array(
            '{formato}' => 'Formato (Digital)',
            '{descarga}' => 'Tipo de descarga (Inmediata)',
        ),
    );
}

/**
 * ============================================================================
 * SHORTCODE PARA TESTING - [seo_preview_description]
 * ============================================================================
 */
add_shortcode('seo_preview_description', 'saico_preview_description_shortcode');

function saico_preview_description_shortcode($atts) {
    global $product;

    if (!$product || !is_a($product, 'WC_Product')) {
        return '<p><em>Este shortcode solo funciona en páginas de producto.</em></p>';
    }

    $fallback = saico_generate_fallback_description($product);

    return '<div class="seo-description-preview" style="background: #f0f0f0; padding: 20px; border-left: 4px solid #10b981; margin: 20px 0;">
        <h4 style="margin-top: 0;">Vista Previa - Descripción SEO Generada:</h4>
        ' . $fallback . '
    </div>';
}
