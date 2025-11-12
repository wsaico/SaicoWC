<?php
/**
 * Template: Página de Autor
 * Muestra información del autor, sus productos y posts
 *
 * @package SaicoWC
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

get_header();

// Obtener datos del autor
$author_id = get_queried_object_id();
$author = get_userdata($author_id);

if (!$author) {
    wp_redirect(home_url());
    exit;
}

// Contar productos y posts del autor
$productos_count = count_user_posts($author_id, 'product', true);
$posts_count = count_user_posts($author_id, 'post', true);
?>

<div class="saico-autor-page">
    <div class="saico-contenedor">

        <!-- Header del Autor -->
        <header class="autor-header">
            <div class="autor-header-bg"></div>

            <div class="autor-header-content">
                <!-- Avatar -->
                <div class="autor-avatar">
                    <?php echo get_avatar($author_id, 120, '', $author->display_name, array('class' => 'avatar-img')); ?>

                    <?php if (get_user_meta($author_id, 'verified_seller', true)): ?>
                    <span class="autor-badge-verificado" title="Vendedor Verificado">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </span>
                    <?php endif; ?>
                </div>

                <!-- Info del Autor -->
                <div class="autor-info">
                    <h1 class="autor-nombre"><?php echo esc_html($author->display_name); ?></h1>

                    <?php if ($author->user_url): ?>
                    <a href="<?php echo esc_url($author->user_url); ?>" class="autor-web" target="_blank" rel="noopener">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="2" y1="12" x2="22" y2="12"></line>
                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                        </svg>
                        <?php echo esc_html(parse_url($author->user_url, PHP_URL_HOST)); ?>
                    </a>
                    <?php endif; ?>

                    <?php if ($author->description): ?>
                    <p class="autor-bio"><?php echo wp_kses_post($author->description); ?></p>
                    <?php endif; ?>

                    <!-- Stats -->
                    <div class="autor-stats">
                        <div class="autor-stat">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                            </svg>
                            <span class="stat-valor"><?php echo esc_html($productos_count); ?></span>
                            <span class="stat-label">Productos</span>
                        </div>

                        <div class="autor-stat">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                            </svg>
                            <span class="stat-valor"><?php echo esc_html($posts_count); ?></span>
                            <span class="stat-label">Artículos</span>
                        </div>

                        <div class="autor-stat">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                            </svg>
                            <span class="stat-valor"><?php echo esc_html(human_time_diff(strtotime($author->user_registered), current_time('timestamp'))); ?></span>
                            <span class="stat-label">Miembro desde</span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Tabs -->
        <div class="autor-tabs">
            <button class="autor-tab activo" data-tab="productos">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
                Productos (<?php echo esc_html($productos_count); ?>)
            </button>

            <button class="autor-tab" data-tab="posts">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                </svg>
                Artículos (<?php echo esc_html($posts_count); ?>)
            </button>
        </div>

        <!-- Contenido de Tabs -->
        <div class="autor-content">

            <!-- Tab: Productos -->
            <div id="tabProductos" class="autor-tab-content activo">
                <?php
                $productos = new WP_Query(array(
                    'post_type' => 'product',
                    'author' => $author_id,
                    'posts_per_page' => 12,
                    'post_status' => 'publish',
                    'orderby' => 'date',
                    'order' => 'DESC'
                ));

                if ($productos->have_posts()):
                ?>
                <ul class="products" id="autorProductos">
                    <?php
                    while ($productos->have_posts()):
                        $productos->the_post();
                        wc_get_template_part('loop/card-producto', 'min');
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </ul>

                <?php if ($productos->max_num_pages > 1): ?>
                <div class="autor-cargar-mas">
                    <button
                        id="cargarMasProductos"
                        class="saico-btn saico-btn-secundario"
                        data-autor="<?php echo esc_attr($author_id); ?>"
                        data-pag="1"
                        data-max="<?php echo esc_attr($productos->max_num_pages); ?>"
                    >
                        Cargar Más Productos
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 5v14M5 12l7 7 7-7"/>
                        </svg>
                    </button>
                </div>
                <?php endif; ?>

                <?php else: ?>
                <div class="autor-sin-contenido">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    <h3>Sin productos</h3>
                    <p>Este autor aún no ha publicado productos.</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Tab: Posts -->
            <div id="tabPosts" class="autor-tab-content">
                <?php
                $posts = new WP_Query(array(
                    'post_type' => 'post',
                    'author' => $author_id,
                    'posts_per_page' => 10,
                    'post_status' => 'publish',
                    'orderby' => 'date',
                    'order' => 'DESC'
                ));

                if ($posts->have_posts()):
                ?>
                <div class="saico-posts-lista" id="autorPosts">
                    <?php
                    while ($posts->have_posts()):
                        $posts->the_post();
                    ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('saico-post-card'); ?>>
                        <!-- Imagen -->
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
                            <!-- Meta -->
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

                            <!-- Título -->
                            <h2 class="post-card-titulo">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>

                            <!-- Excerpt -->
                            <div class="post-card-excerpt">
                                <?php echo wp_trim_words(get_the_excerpt(), 30, '...'); ?>
                            </div>

                            <!-- Leer más -->
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
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </div>

                <?php if ($posts->max_num_pages > 1): ?>
                <div class="autor-cargar-mas">
                    <button
                        id="cargarMasPosts"
                        class="saico-btn saico-btn-secundario"
                        data-autor="<?php echo esc_attr($author_id); ?>"
                        data-pag="1"
                        data-max="<?php echo esc_attr($posts->max_num_pages); ?>"
                    >
                        Cargar Más Artículos
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 5v14M5 12l7 7 7-7"/>
                        </svg>
                    </button>
                </div>
                <?php endif; ?>

                <?php else: ?>
                <div class="autor-sin-contenido">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                    </svg>
                    <h3>Sin artículos</h3>
                    <p>Este autor aún no ha publicado artículos.</p>
                </div>
                <?php endif; ?>
            </div>

        </div>

    </div>
</div>

<?php get_footer(); ?>
