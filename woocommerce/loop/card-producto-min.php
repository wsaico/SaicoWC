<?php
/**
 * Card de Producto Minimalista - Diseño Horizontal
 * Replica el diseño original: imagen 72px izquierda + contenido derecha
 *
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

global $product;

if (!$product || !is_a($product, 'WC_Product')) {
    return;
}

// Información básica del producto
$producto_id = $product->get_id();
$titulo = $product->get_name();
$url = get_permalink($producto_id);
$es_gratis = !$product->get_price() || $product->get_price() == 0;

// Imagen del producto
$imagen_url = get_the_post_thumbnail_url($producto_id, 'woocommerce_thumbnail');
$tiene_imagen = !empty($imagen_url) && $imagen_url !== wc_placeholder_img_src('woocommerce_thumbnail');
if (!$tiene_imagen) {
    $imagen_url = wc_placeholder_img_src('woocommerce_thumbnail');
}

// Categoría principal
$categorias = get_the_terms($producto_id, 'product_cat');
$categoria_nombre = $categorias && !is_wp_error($categorias) ? $categorias[0]->name : '';
$categoria_link = $categorias && !is_wp_error($categorias) ? get_term_link($categorias[0]) : '';
$categoria_id = $categorias && !is_wp_error($categorias) ? $categorias[0]->term_id : 0;

// Obtener colores dinámicos para la categoría
$categoria_color = function_exists('saico_get_categoria_color') ? saico_get_categoria_color($categoria_id) : array('bg' => 'rgba(59, 130, 246, 0.1)', 'text' => '#1d4ed8');

// Información del autor
$autor_id = get_post_field('post_author', $producto_id);
$autor_nombre = get_the_author_meta('display_name', $autor_id);

// Tiempo relativo
$post_date = get_the_date('U', $producto_id);
$current_time = current_time('timestamp');
$time_diff = $current_time - $post_date;

if ($time_diff < 3600) {
    $tiempo = floor($time_diff / 60) . ' ' . (floor($time_diff / 60) == 1 ? 'minuto' : 'minutos');
} elseif ($time_diff < 86400) {
    $tiempo = floor($time_diff / 3600) . ' ' . (floor($time_diff / 3600) == 1 ? 'hora' : 'horas');
} elseif ($time_diff < 2592000) {
    $tiempo = floor($time_diff / 86400) . ' ' . (floor($time_diff / 86400) == 1 ? 'día' : 'días');
} elseif ($time_diff < 31536000) {
    $tiempo = floor($time_diff / 2592000) . ' ' . (floor($time_diff / 2592000) == 1 ? 'mes' : 'meses');
} else {
    $tiempo = floor($time_diff / 31536000) . ' ' . (floor($time_diff / 31536000) == 1 ? 'año' : 'años');
}

// Contadores
$descargas_reales = (int) get_post_meta($producto_id, 'somdn_dlcount', true);
$descargas = !$es_gratis && $descargas_reales == 0 ? mt_rand(50, 500) : $descargas_reales;
$vistas = (int) get_post_meta($producto_id, '_view_count', true);
$likes = (int) get_post_meta($producto_id, '_likes_count', true);

// Badges
$es_destacado = $product->is_featured();
$es_nuevo = (strtotime($product->get_date_created()) > strtotime('-30 days'));
$ventas = (int) get_post_meta($producto_id, 'total_sales', true);
$es_popular = $ventas > 10;

// Descripción con filtro SEO fallback
$descripcion = apply_filters('woocommerce_product_description', $product->get_description(), $product);
if (empty($descripcion)) {
    $descripcion = get_the_content($producto_id);
}
$descripcion = wp_trim_words($descripcion, 10);

// Audio
$tiene_audio = false;
$audio_url = '';
if (function_exists('get_field')) {
    $audio_field = get_field('product_audio', $producto_id);

    // Si ACF retorna un array (configurado como archivo), obtener la URL
    if (is_array($audio_field) && isset($audio_field['url'])) {
        $audio_url = $audio_field['url'];
    } elseif (is_numeric($audio_field)) {
        // Si retorna un ID, obtener la URL del attachment
        $audio_url = wp_get_attachment_url($audio_field);
    } elseif (is_string($audio_field)) {
        // Si retorna directamente la URL
        $audio_url = $audio_field;
    }

    $tiene_audio = !empty($audio_url) && filter_var($audio_url, FILTER_VALIDATE_URL);
}

// MIDI (fallback si no hay audio)
$tiene_midi = false;
$midi_url = '';
if (!$tiene_audio && function_exists('saico_get_midi_file_url')) {
    $midi_url = saico_get_midi_file_url($producto_id);
    $tiene_midi = !empty($midi_url);
}

// Gradiente aleatorio basado en ID
$gradiente_num = ($producto_id % 8) + 1;
$gradiente_class = 'gradient-' . $gradiente_num;

?>
<li class="producto-card <?php echo $es_destacado ? 'destacado' : ($es_nuevo ? 'nuevo' : ($es_popular ? 'popular' : '')); ?>" data-product-id="<?php echo esc_attr($producto_id); ?>">

    <!-- Tooltip - Solo visible al hover -->
    <div class="tooltip">
        <div class="tooltip-titulo"><?php echo esc_html($titulo); ?></div>
        <?php if (!empty($descripcion)): ?>
        <div class="tooltip-descripcion"><?php echo esc_html($descripcion); ?></div>
        <div class="tooltip-link">
            <a href="<?php echo esc_url($url); ?>" class="tooltip-link-btn" title="Ver detalles de <?php echo esc_attr($titulo); ?>" aria-label="Ver más detalles del producto <?php echo esc_attr($titulo); ?>">Ver más...</a>
        </div>
        <?php endif; ?>
        <div class="tooltip-badges">
            <?php if ($es_destacado): ?>
            <span class="status-badge destacado">Destacado</span>
            <?php endif; ?>
            <?php if ($es_nuevo): ?>
            <span class="status-badge nuevo">Nuevo</span>
            <?php endif; ?>
            <?php if ($es_popular): ?>
            <span class="status-badge popular">Popular</span>
            <?php endif; ?>
        </div>
        <div class="tooltip-stats">
            <div class="tooltip-stat">
                <span class="tooltip-label">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                        <line x1="7" y1="7" x2="7" y2="7"></line>
                    </svg>
                    Tipo:
                </span>
                <span class="tooltip-value"><?php echo $es_gratis ? 'Gratis' : 'Premium'; ?></span>
            </div>
            <div class="tooltip-stat">
                <span class="tooltip-label">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    Descargas:
                </span>
                <span class="tooltip-value"><?php echo number_format($descargas); ?></span>
            </div>
            <div class="tooltip-stat">
                <span class="tooltip-label">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    Vistas:
                </span>
                <span class="tooltip-value"><?php echo number_format($vistas); ?></span>
            </div>
            <div class="tooltip-stat">
                <span class="tooltip-label">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="12" height="12">
                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                    </svg>
                    Me gusta:
                </span>
                <span class="tooltip-value"><?php echo number_format($likes); ?></span>
            </div>
        </div>
    </div>

    <!-- Imagen del producto (72x72px) -->
    <div class="producto-imagen <?php echo esc_attr($gradiente_class); ?>">
        <?php if ($tiene_imagen): ?>
            <img src="<?php echo esc_url($imagen_url); ?>" alt="<?php echo esc_attr($titulo); ?>" class="producto-imagen-thumb" />
        <?php endif; ?>

        <?php if ($tiene_audio): ?>
            <!-- Botón de reproducción de audio compatible con sistema global -->
            <button class="audio-play-btn woo-product-play-button" data-audio="<?php echo esc_url($audio_url); ?>" data-producto-id="<?php echo esc_attr($producto_id); ?>" title="Reproducir audio de vista previa">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="5 3 19 12 5 21 5 3" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
            <!-- Elemento de audio oculto -->
            <audio class="woo-product-audio" preload="metadata" style="display: none;" src="<?php echo esc_url($audio_url); ?>">
                <source src="<?php echo esc_url($audio_url); ?>" type="audio/mpeg">
            </audio>
        <?php elseif ($tiene_midi && shortcode_exists('midiplay_grid')): ?>
            <!-- Shortcode para reproducir MIDI -->
            <div class="midi-grid-container">
                <?php echo do_shortcode('[midiplay_grid]'); ?>
            </div>
        <?php endif; ?>

        <?php if (!$es_gratis): ?>
            <span class="producto-badge badge-pro">
                <svg viewBox="0 0 640 512" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path d="M528 448H112c-8.8 0-16 7.2-16 16v32c0 8.8 7.2 16 16 16h416c8.8 0 16-7.2 16-16v-32c0-8.8-7.2-16-16-16zm64-320c-26.5 0-48 21.5-48 48 0 7.1 1.6 13.7 4.4 19.8L476 239.2c-15.4 9.2-35.3 4-44.2-11.6L350.3 85C361 76.2 368 63 368 48c0-26.5-21.5-48-48-48s-48 21.5-48 48c0 15 7 28.2 17.7 37l-81.5 142.6c-8.9 15.6-28.9 20.8-44.2 11.6l-72.3-43.4c2.7-6 4.4-12.7 4.4-19.8 0-26.5-21.5-48-48-48S0 149.5 0 176s21.5 48 48 48c2.6 0 5.2-.4 7.7-.8L128 416h384l72.3-192.8c2.5.4 5.1.8 7.7.8 26.5 0 48-21.5 48-48s-21.5-48-48-48z"/>
                </svg>
            </span>
        <?php endif; ?>
    </div>

    <!-- Contenido del producto -->
    <div class="producto-contenido">
        <?php if (!empty($categoria_nombre)): ?>
        <a href="<?php echo esc_url($categoria_link); ?>" class="categoria-badge" style="background: <?php echo esc_attr($categoria_color['bg']); ?>; color: <?php echo esc_attr($categoria_color['text']); ?>;" title="Ver productos de la categoría <?php echo esc_attr($categoria_nombre); ?>" aria-label="Explorar categoría <?php echo esc_attr($categoria_nombre); ?>"><?php echo esc_html($categoria_nombre); ?></a>
        <?php endif; ?>
        <h3 class="producto-titulo"><a href="<?php echo esc_url($url); ?>" title="Ver producto: <?php echo esc_attr($titulo); ?>" aria-label="Ir a la página del producto <?php echo esc_attr($titulo); ?>"><?php echo esc_html($titulo); ?></a></h3>
        <div class="producto-stats">
            <div class="card-stat-item card-stat-autor">
                <svg class="card-stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <span class="card-stat-label">Por:</span>
                <span class="card-stat-value"><?php echo esc_html($autor_nombre); ?></span>
            </div>
            <div class="card-stat-item card-stat-tiempo">
                <svg class="card-stat-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <span class="card-stat-label">Hace:</span>
                <span class="card-stat-value"><?php echo esc_html($tiempo); ?></span>
            </div>
        </div>
    </div>

    <!-- Acciones del card -->
    <div class="card-acciones">
        <a href="<?php echo esc_url($url); ?>" class="btn-icono" title="Ver producto <?php echo esc_attr($titulo); ?>" aria-label="Ir al producto <?php echo esc_attr($titulo); ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M7 17L17 7M17 7H7M17 7V17" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
    </div>
</li>
