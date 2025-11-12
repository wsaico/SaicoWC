<?php
/**
 * Card de Estadísticas y Acciones Sociales
 *
 * @package SaicoWC
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

global $product;

if (!$product) {
    return;
}

$producto_id = $product->get_id();
$post = get_post($producto_id);
$author_id = $post->post_author;
$author_name = get_the_author_meta('display_name', $author_id);
$author_avatar = get_avatar_url($author_id, array('size' => 40));
$author_url = get_author_posts_url($author_id);
$author_products_count = count_user_posts($author_id, 'product');

// Estadísticas
$descargas = (int) get_post_meta($producto_id, '_download_count', true);
$vistas = (int) get_post_meta($producto_id, '_view_count', true);
$fecha_publicacion = get_the_date('d/m/Y', $producto_id);

// Likes - Funciona con y sin login (cookies para usuarios no registrados)
$likes_count = (int) get_post_meta($producto_id, '_likes_count', true);
$user_id = get_current_user_id();
$user_has_liked = false;

if ($user_id) {
    // Usuario logueado: verificar en user meta
    $user_likes = get_user_meta($user_id, '_product_likes', true);
    if (is_array($user_likes)) {
        $user_has_liked = in_array($producto_id, $user_likes);
    }
} else {
    // Usuario no logueado: verificar en cookie
    if (isset($_COOKIE['saico_likes'])) {
        $cookie_likes = json_decode(stripslashes($_COOKIE['saico_likes']), true);
        if (is_array($cookie_likes)) {
            $user_has_liked = in_array($producto_id, $cookie_likes);
        }
    }
}
?>

<div class="saico-stats-card">
    <!-- Header con autor -->
    <div class="stats-header">
        <img src="<?php echo esc_url($author_avatar); ?>" alt="<?php echo esc_attr($author_name); ?>" class="stats-author-avatar">
        <div class="stats-author-info">
            <span class="stats-label">Publicado por</span>
            <a href="<?php echo esc_url($author_url); ?>" class="stats-author-name"><?php echo esc_html($author_name); ?></a>
            <span class="stats-author-count">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                </svg>
                <?php echo esc_html($author_products_count); ?> <?php echo $author_products_count === 1 ? 'producto' : 'productos'; ?>
            </span>
        </div>
        <div class="stats-header-right">
            <span class="stats-date"><?php echo esc_html($fecha_publicacion); ?></span>
            <a href="<?php echo esc_url($author_url); ?>" class="btn-view-author">
                Ver perfil
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="stats-grid">
        <div class="stat-item">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
            </div>
            <div class="stat-info">
                <span class="stat-value"><?php echo number_format($vistas); ?></span>
                <span class="stat-label">Vistas</span>
            </div>
        </div>

        <div class="stat-item">
            <div class="stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
            </div>
            <div class="stat-info">
                <span class="stat-value"><?php echo number_format($descargas); ?></span>
                <span class="stat-label">Descargas</span>
            </div>
        </div>

        <div class="stat-item">
            <div class="stat-icon stat-icon-likes">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                </svg>
            </div>
            <div class="stat-info">
                <span class="stat-value" id="likesCount"><?php echo number_format($likes_count); ?></span>
                <span class="stat-label">Me encanta</span>
            </div>
        </div>
    </div>

    <!-- Acciones sociales -->
    <div class="stats-actions">
        <button class="stats-action-btn btn-like <?php echo $user_has_liked ? 'liked' : ''; ?>"
                id="likeButton"
                data-product-id="<?php echo esc_attr($producto_id); ?>">
            <svg class="icon-like" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
            </svg>
            <span><?php echo $user_has_liked ? 'Te encanta' : 'Me encanta'; ?></span>
        </button>

        <button class="stats-action-btn btn-share" onclick="saicoAbrirModalCompartir();">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="18" cy="5" r="3"/>
                <circle cx="6" cy="12" r="3"/>
                <circle cx="18" cy="19" r="3"/>
                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
                <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
            </svg>
            <span>Compartir</span>
        </button>
    </div>
</div>
