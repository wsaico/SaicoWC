<?php
/**
 * Hero - Producto Destacado Premium (Diseño Deslumbrante)
 *
 * @package SaicoWC
 * @version 3.0.0
 */

defined('ABSPATH') || exit;

$featured_product_id = get_theme_mod('saico_hero_featured_product', '');

if (!$featured_product_id) {
    return;
}

$product = wc_get_product($featured_product_id);

if (!$product || !is_a($product, 'WC_Product')) {
    return;
}

$product_id = $product->get_id();
$title = $product->get_name();
$url = get_permalink($product_id);
$image = get_the_post_thumbnail_url($product_id, 'medium');
$price = $product->get_price();
$is_free = ($price == 0 || $price == '');

// Autor
$post = get_post($product_id);
$author_id = $post->post_author;
$author_name = get_the_author_meta('display_name', $author_id);
$author_avatar = get_avatar_url($author_id, array('size' => 40));

// Categoría
$categories = get_the_terms($product_id, 'product_cat');
$category_name = '';
if ($categories && !is_wp_error($categories)) {
    $category_name = $categories[0]->name;
}

// Rating
$rating = $product->get_average_rating();
$rating_count = $product->get_rating_count();

// Audio/MIDI
$audio_field = get_field('audio', $product_id);
$audio_url = '';
if (is_array($audio_field) && isset($audio_field['url'])) {
    $audio_url = $audio_field['url'];
} elseif (is_numeric($audio_field)) {
    $audio_url = wp_get_attachment_url($audio_field);
} elseif (is_string($audio_field)) {
    $audio_url = $audio_field;
}
$tiene_audio = !empty($audio_url) && filter_var($audio_url, FILTER_VALIDATE_URL);

// Gradiente dinámico
$gradiente_num = ($product_id % 8) + 1;
?>

<div class="hero-featured-premium" data-aos="fade-left" data-aos-duration="800">
    <!-- Partículas de fondo animadas -->
    <div class="featured-particles">
        <span class="particle"></span>
        <span class="particle"></span>
        <span class="particle"></span>
        <span class="particle"></span>
    </div>

    <!-- Contenedor horizontal con efecto 3D -->
    <div class="featured-3d-wrapper featured-horizontal">

        <!-- Imagen destacada con efectos premium -->
        <div class="featured-image-premium featured-image-horizontal gradient-<?php echo $gradiente_num; ?>">
            <?php if ($image): ?>
                <div class="image-wrapper">
                    <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy">
                    <div class="image-overlay"></div>
                </div>
            <?php else: ?>
                <div class="image-placeholder-premium">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <polyline points="21 15 16 10 5 21"></polyline>
                    </svg>
                </div>
            <?php endif; ?>

            <!-- Badges flotantes con glassmorphism -->
            <div class="featured-floating-badges">
                <?php if ($rating > 0): ?>
                <div class="badge-rating">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                    </svg>
                    <span><?php echo number_format($rating, 1); ?></span>
                </div>
                <?php endif; ?>

                <?php if ($category_name): ?>
                <div class="badge-category">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                        <line x1="7" y1="7" x2="7.01" y2="7"></line>
                    </svg>
                    <span><?php echo esc_html($category_name); ?></span>
                </div>
                <?php endif; ?>

                <?php if ($is_free): ?>
                <div class="badge-free">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                    <span>GRATIS</span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Botón play premium con efectos -->
            <?php if ($tiene_audio): ?>
            <button class="play-btn-premium" data-product-id="<?php echo esc_attr($product_id); ?>">
                <span class="play-ripple"></span>
                <span class="play-ripple ripple-2"></span>
                <svg class="icon-play" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <polygon points="5 3 19 12 5 21 5 3"></polygon>
                </svg>
                <svg class="icon-pause" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" style="display:none;">
                    <rect x="6" y="4" width="4" height="16"></rect>
                    <rect x="14" y="4" width="4" height="16"></rect>
                </svg>
            </button>
            <audio class="hero-audio-player" data-product-id="<?php echo esc_attr($product_id); ?>" preload="metadata">
                <source src="<?php echo esc_url($audio_url); ?>" type="audio/mpeg">
            </audio>
            <?php endif; ?>

            <!-- Barra de progreso del audio -->
            <?php if ($tiene_audio): ?>
            <div class="audio-progress-bar">
                <div class="progress-fill"></div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Info premium con efectos glassmorphism - HORIZONTAL -->
        <div class="featured-info-premium featured-info-horizontal">
            <!-- Header con autor -->
            <div class="featured-author">
                <img src="<?php echo esc_url($author_avatar); ?>" alt="<?php echo esc_attr($author_name); ?>" class="author-avatar">
                <div class="author-info">
                    <span class="author-name"><?php echo esc_html($author_name); ?></span>
                    <span class="author-role">Creador</span>
                </div>
                <?php if ($rating > 0): ?>
                <div class="mobile-rating">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                    </svg>
                    <span><?php echo number_format($rating, 1); ?></span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Título destacado -->
            <h3 class="featured-title-premium">
                <a href="<?php echo esc_url($url); ?>">
                    <?php echo esc_html($title); ?>
                </a>
            </h3>

            <!-- Precio y CTA con efectos -->
            <div class="featured-cta-section">
                <?php if (!$is_free): ?>
                <div class="featured-price">
                    <span class="price-label">Precio</span>
                    <span class="price-value"><?php echo wc_price($price); ?></span>
                </div>
                <?php endif; ?>

                <a href="<?php echo esc_url($url); ?>" class="btn-featured-premium">
                    <span class="btn-shimmer"></span>
                    <span class="btn-content">
                        <?php if ($is_free): ?>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                            <span>Descargar Gratis</span>
                        <?php else: ?>
                            <span>Descargar</span>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                <polyline points="7 10 12 15 17 10"/>
                                <line x1="12" y1="15" x2="12" y2="3"/>
                            </svg>
                        <?php endif; ?>
                    </span>
                </a>
            </div>
        </div>
    </div>

    <!-- Glow effect -->
    <div class="featured-glow"></div>
</div>
