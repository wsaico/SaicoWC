<?php
/**
 * Front Page - Completamente nuevo y limpio
 */

defined('ABSPATH') || exit;
get_header();
?>

<main class="pagina-inicio">

    <!-- Hero Moderno - 2 Columnas -->
    <section class="hero-inicio">
        <div class="hero-contenedor">
            <div class="hero-grid">
                <!-- Columna Izquierda: Contenido -->
                <div class="hero-contenido">
                    <span class="hero-etiqueta">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
                        </svg>
                        <?php echo esc_html(get_theme_mod('saico_hero_badge', 'Nuevos productos disponibles')); ?>
                    </span>

                    <h1 class="hero-h1">
                        <?php echo esc_html(get_theme_mod('saico_hero_titulo', 'Descarga Digital')); ?>
                        <span class="hero-acento"><?php echo esc_html(get_theme_mod('saico_hero_acento', 'Extraordinaria')); ?></span>
                    </h1>

                    <p class="hero-descripcion">
                        <?php echo esc_html(get_theme_mod('saico_hero_descripcion', 'Descubre nuestra colección premium de productos digitales de alta calidad.')); ?>
                    </p>

                    <div class="hero-ctas">
                        <a href="<?php echo esc_url(get_theme_mod('saico_hero_boton1_url', '#productos')); ?>" class="cta-principal">
                            <span class="cta-shimmer"></span>
                            <?php echo esc_html(get_theme_mod('saico_hero_boton1_texto', 'Explorar Productos')); ?>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </a>

                        <a href="<?php echo esc_url(get_theme_mod('saico_hero_boton2_url', '#')); ?>" class="cta-secundario">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polygon points="5 3 19 12 5 21 5 3"></polygon>
                            </svg>
                            <?php echo esc_html(get_theme_mod('saico_hero_boton2_texto', 'Ver Demo')); ?>
                        </a>
                    </div>

                    <!-- Estadísticas Mejoradas -->
                    <div class="hero-stats">
                        <?php
                        $icon_map = array(
                            'box' => '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>',
                            'download' => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line>',
                            'star' => '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>',
                            'users' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>',
                            'heart' => '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>',
                            'check' => '<polyline points="20 6 9 17 4 12"></polyline>',
                        );

                        for ($i = 1; $i <= 3; $i++):
                            $numero = get_theme_mod("saico_hero_stat{$i}_numero", '');
                            $etiqueta = get_theme_mod("saico_hero_stat{$i}_etiqueta", '');
                            $icono = get_theme_mod("saico_hero_stat{$i}_icono", 'box');

                            if ($numero && $etiqueta):
                                $svg_path = isset($icon_map[$icono]) ? $icon_map[$icono] : $icon_map['box'];
                        ?>
                        <div class="stat-item" data-aos="fade-up" data-aos-delay="<?php echo $i * 100; ?>">
                            <div class="stat-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <?php echo $svg_path; ?>
                                </svg>
                            </div>
                            <div class="stat-content">
                                <strong class="stat-numero"><?php echo esc_html($numero); ?></strong>
                                <span class="stat-label"><?php echo esc_html($etiqueta); ?></span>
                            </div>
                        </div>
                        <?php endif; endfor; ?>
                    </div>
                </div>

                <!-- Columna Derecha: Top 3 Productos Destacados (Solo Desktop) -->
                <?php
                $featured_product_id = get_theme_mod('saico_hero_featured_product', '');
                if ($featured_product_id) {
                    get_template_part('partes/hero-featured-product');
                } else {
                    // Mostrar top 3 productos más populares
                    $top_products = new WP_Query(array(
                        'post_type' => 'product',
                        'posts_per_page' => 3,
                        'post_status' => 'publish',
                        'meta_key' => '_vistas',
                        'orderby' => 'meta_value_num',
                        'order' => 'DESC'
                    ));

                    if ($top_products->have_posts()) : ?>
                        <div class="hero-featured-list">
                            <div class="hero-featured-header">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                </svg>
                                <h3>Top 3 Populares</h3>
                            </div>

                            <div class="hero-featured-items">
                                <?php
                                $position = 1;
                                while ($top_products->have_posts()) : $top_products->the_post();
                                    $product = wc_get_product(get_the_ID());
                                    $producto_id = get_the_ID();
                                    $vistas = get_post_meta($producto_id, '_view_count', true) ?: 0;
                                    $descargas = get_post_meta($producto_id, 'somdn_dlcount', true) ?: 0;

                                    // Verificar imagen
                                    $tiene_imagen = has_post_thumbnail();
                                    $imagen_url = get_the_post_thumbnail_url($producto_id, 'thumbnail');

                                    // Gradiente basado en ID
                                    $gradiente_num = ($producto_id % 8) + 1;
                                    $gradiente_class = 'gradient-' . $gradiente_num;

                                    // Audio
                                    $tiene_audio = false;
                                    $audio_url = '';
                                    if (function_exists('get_field')) {
                                        $audio_field = get_field('product_audio', $producto_id);
                                        if (is_array($audio_field) && isset($audio_field['url'])) {
                                            $audio_url = $audio_field['url'];
                                        } elseif (is_numeric($audio_field)) {
                                            $audio_url = wp_get_attachment_url($audio_field);
                                        } elseif (is_string($audio_field)) {
                                            $audio_url = $audio_field;
                                        }
                                        $tiene_audio = !empty($audio_url) && filter_var($audio_url, FILTER_VALIDATE_URL);
                                    }
                                ?>
                                    <article class="hero-featured-item" data-position="<?php echo $position; ?>" data-product-id="<?php echo esc_attr($producto_id); ?>">
                                        <div class="hero-item-rank">
                                            <span><?php echo $position; ?></span>
                                        </div>

                                        <div class="hero-item-image <?php echo !$tiene_imagen ? esc_attr($gradiente_class) : ''; ?>">
                                            <?php if ($tiene_imagen) : ?>
                                                <a href="<?php the_permalink(); ?>">
                                                    <img src="<?php echo esc_url($imagen_url); ?>" alt="<?php echo esc_attr(get_the_title()); ?>" />
                                                </a>
                                            <?php endif; ?>

                                            <?php if ($tiene_audio) : ?>
                                                <button class="hero-audio-play-btn woo-product-play-button"
                                                        data-audio="<?php echo esc_url($audio_url); ?>"
                                                        data-producto-id="<?php echo esc_attr($producto_id); ?>"
                                                        title="Reproducir audio">
                                                    <svg viewBox="0 0 24 24" fill="currentColor">
                                                        <polygon points="5 3 19 12 5 21 5 3"/>
                                                    </svg>
                                                </button>
                                                <audio class="woo-product-audio" preload="metadata" style="display: none;" src="<?php echo esc_url($audio_url); ?>">
                                                    <source src="<?php echo esc_url($audio_url); ?>" type="audio/mpeg">
                                                </audio>
                                            <?php endif; ?>
                                        </div>

                                        <div class="hero-item-content">
                                            <h4 class="hero-item-title">
                                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                            </h4>

                                            <div class="hero-item-meta">
                                                <span class="hero-item-stat">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                        <circle cx="12" cy="12" r="3"></circle>
                                                    </svg>
                                                    <?php echo number_format($vistas); ?>
                                                </span>
                                                <span class="hero-item-stat">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                        <polyline points="7 10 12 15 17 10"></polyline>
                                                        <line x1="12" y1="15" x2="12" y2="3"></line>
                                                    </svg>
                                                    <?php echo number_format($descargas); ?>
                                                </span>
                                            </div>

                                            <div class="hero-item-price">
                                                <?php echo $product->get_price_html(); ?>
                                            </div>
                                        </div>
                                    </article>
                                <?php
                                    $position++;
                                endwhile;
                                wp_reset_postdata();
                                ?>
                            </div>
                        </div>
                    <?php endif;
                }
                ?>
            </div>
        </div>
    </section>

    <!-- Categorías -->
    <?php
    $cats = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => true,
        'number' => 8,
        'orderby' => 'count',
        'order' => 'DESC'
    ));
    if (!empty($cats) && !is_wp_error($cats)):
    ?>
    <section class="categorias-inicio">
        <div class="contenedor-ancho">
            <div class="categorias-header">
                <h2><?php echo esc_html(saico_categorias_titulo()); ?></h2>
                <p class="categorias-subtitle"><?php echo esc_html(saico_categorias_subtitulo()); ?></p>
            </div>
            <div class="cats-grid">
                <?php
                // Iconos personalizados por categoría
                $category_icons = array(
                    'music' => '<path d="M9 18V5l12-2v13M9 18c0 1.657-1.343 3-3 3s-3-1.343-3-3 1.343-3 3-3 3 1.343 3 3zm12-2c0 1.657-1.343 3-3 3s-3-1.343-3-3 1.343-3 3-3 3 1.343 3 3z"/>',
                    'accessories' => '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>',
                    'clothing' => '<path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M8.5 3H12l4 7-4 7H8.5L5 10z"/>',
                    'hoodies' => '<path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>',
                    'tshirts' => '<path d="M20.38 8.57l-1.23 1.85a8 8 0 0 1-6.22 3.08h-.06a8 8 0 0 1-6.22-3.08L5.42 8.57c-.23-.34-.06-.77.36-.77h.54a2 2 0 0 0 1.78-1.1l1.74-3.47A2 2 0 0 1 11.62 2h.76a2 2 0 0 1 1.78 1.1l1.74 3.47a2 2 0 0 0 1.78 1.1h.54c.42 0 .59.43.36.77z"/><rect x="8" y="11" width="8" height="11" rx="1"/>',
                    'decor' => '<rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/>',
                    'default' => '<path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>'
                );

                foreach ($cats as $index => $c):
                    $cat_slug = $c->slug;
                    $icon_path = isset($category_icons[$cat_slug]) ? $category_icons[$cat_slug] : $category_icons['default'];
                    $gradient_num = ($index % 8) + 1;
                ?>
                <a href="<?php echo esc_url(get_term_link($c)); ?>" class="cat-card" data-gradient="<?php echo $gradient_num; ?>">
                    <div class="cat-icon-wrapper">
                        <div class="cat-icon-bg gradient-<?php echo $gradient_num; ?>"></div>
                        <svg class="cat-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <?php echo $icon_path; ?>
                        </svg>
                    </div>
                    <div class="cat-content">
                        <h3 class="cat-title"><?php echo esc_html($c->name); ?></h3>
                        <span class="cat-count"><?php echo $c->count; ?> <?php echo $c->count == 1 ? 'producto' : 'productos'; ?></span>
                    </div>
                    <svg class="cat-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Productos -->
    <section class="productos-inicio">
        <div class="contenedor-ancho">
            <div class="productos-header">
                <h2><?php echo esc_html(saico_productos_titulo()); ?></h2>
                <div class="filtros">
                    <button class="filtro activo" data-filtro="todos">Todos</button>
                    <button class="filtro" data-filtro="gratis">Gratis</button>
                    <button class="filtro" data-filtro="premium">Premium</button>
                    <button class="filtro" data-filtro="nuevo">Nuevos</button>
                    <button class="filtro" data-filtro="popular">Populares</button>
                </div>
            </div>

            <ul class="products columns-4" id="productosGrid">
            <?php
            $prods = new WP_Query(array(
                'post_type' => 'product',
                'posts_per_page' => 12,
                'post_status' => 'publish'
            ));

            if ($prods->have_posts()) {
                while ($prods->have_posts()) {
                    $prods->the_post();
                    wc_get_template_part('loop/card-producto', 'min');
                }
                wp_reset_postdata();
            }
            ?>
        </ul>

            <?php if ($prods->max_num_pages > 1): ?>
            <div class="cargar-mas">
                <button class="btn-cargar" id="cargarMas" data-pag="1" data-max="<?php echo $prods->max_num_pages; ?>">
                    Cargar Más
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 5v14M5 12l7 7 7-7"/>
                    </svg>
                </button>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- CTA (Llamada a la Acción) -->
    <?php if (saico_cta_activar()): ?>
    <section class="cta-section">
        <div class="contenedor-ancho">
            <div class="cta-container">
                <div class="cta-content">
                    <h2 class="cta-title"><?php echo esc_html(saico_cta_titulo()); ?></h2>
                    <p class="cta-description"><?php echo esc_html(saico_cta_descripcion()); ?></p>
                    <a href="<?php echo esc_url(saico_cta_boton_url()); ?>" class="cta-button">
                        <?php echo esc_html(saico_cta_boton_texto()); ?>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
                <div class="cta-decoration">
                    <div class="cta-circle cta-circle-1"></div>
                    <div class="cta-circle cta-circle-2"></div>
                    <div class="cta-circle cta-circle-3"></div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

</main>

<?php get_footer(); ?>
