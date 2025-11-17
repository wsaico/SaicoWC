<?php
/**
 * Template: Archivo (Categorías, Tags, Fechas)
 *
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

get_header();
?>

<div class="saico-blog">
    <div class="saico-contenedor">

        <!-- Título del Archivo -->
        <header class="saico-archivo-header">
            <h1 class="archivo-titulo">
                <?php
                if (is_category()) {
                    single_cat_title();
                } elseif (is_tag()) {
                    single_tag_title();
                } elseif (is_author()) {
                    echo 'Autor: ' . get_the_author();
                } elseif (is_day()) {
                    echo 'Archivo: ' . get_the_date();
                } elseif (is_month()) {
                    echo 'Archivo: ' . get_the_date('F Y');
                } elseif (is_year()) {
                    echo 'Archivo: ' . get_the_date('Y');
                } else {
                    echo 'Archivo';
                }
                ?>
            </h1>

            <?php
            $descripcion = get_the_archive_description();
            if ($descripcion) :
            ?>
            <div class="archivo-descripcion">
                <?php echo wp_kses_post($descripcion); ?>
            </div>
            <?php endif; ?>

            <div class="archivo-meta">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                </svg>
                <span><?php echo esc_html($wp_query->found_posts); ?> artículos</span>
            </div>
        </header>

        <div class="saico-blog-layout <?php echo saico_get_layout_class(); ?>">

            <!-- Contenido Principal -->
            <main class="saico-blog-main">
                <?php if (have_posts()) : ?>

                <div class="saico-posts-lista">
                    <?php
                    while (have_posts()) :
                        the_post();
                    ?>

                    <article id="post-<?php the_ID(); ?>" <?php post_class('saico-post-card'); ?>>
                        <!-- Imagen Destacada -->
                        <a href="<?php the_permalink(); ?>" class="post-card-imagen">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('medium_large'); ?>
                            <?php else : ?>
                                <div class="post-card-sin-imagen">
                                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                        <polyline points="21 15 16 10 5 21"></polyline>
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </a>

                        <!-- Contenido -->
                        <div class="post-card-contenido">
                            <!-- Meta -->
                            <div class="post-card-meta">
                                <?php
                                $categorias = get_the_category();
                                if ($categorias) :
                                ?>
                                <a href="<?php echo esc_url(get_category_link($categorias[0]->term_id)); ?>" class="post-categoria">
                                    <?php echo esc_html($categorias[0]->name); ?>
                                </a>
                                <span class="post-card-meta-separador"></span>
                                <?php endif; ?>

                                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                    <?php echo esc_html(get_the_date()); ?>
                                </time>

                                <?php if (comments_open() || get_comments_number()) : ?>
                                <span class="post-card-meta-separador"></span>
                                <span><?php echo esc_html(get_comments_number()); ?> comentarios</span>
                                <?php endif; ?>
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

                    <?php endwhile; ?>
                </div>

                <!-- Paginación -->
                <?php
                the_posts_pagination(array(
                    'mid_size'  => 2,
                    'prev_text' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="15 18 9 12 15 6"></polyline></svg>',
                    'next_text' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="9 18 15 12 9 6"></polyline></svg>',
                    'before_page_number' => '',
                    'class' => 'saico-paginacion',
                ));
                ?>

                <?php else : ?>

                <!-- No se encontraron posts -->
                <div class="saico-sin-posts">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <h2>No se encontraron artículos</h2>
                    <p>Intenta buscar con otros términos.</p>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="saico-btn saico-btn-primario">
                        Volver al inicio
                    </a>
                </div>

                <?php endif; ?>
            </main>

            <!-- Sidebar (si está activo) -->
            <?php if (saico_should_show_sidebar()) : ?>
                <?php get_sidebar(); ?>
            <?php endif; ?>

        </div>
    </div>
</div>

<style>
.saico-archivo-header {
    text-align: center;
    padding: var(--saico-spacing-2xl) 0;
    margin-bottom: var(--saico-spacing-xl);
    border-bottom: 2px solid var(--saico-borde-claro);
}

.archivo-titulo {
    font-size: var(--saico-font-4xl);
    font-weight: var(--saico-font-weight-bold);
    margin-bottom: var(--saico-spacing-md);
    color: var(--saico-texto-primario);
}

.archivo-descripcion {
    max-width: 600px;
    margin: 0 auto var(--saico-spacing-md);
    color: var(--saico-texto-secundario);
    line-height: var(--saico-line-height-relaxed);
}

.archivo-meta {
    display: inline-flex;
    align-items: center;
    gap: var(--saico-spacing-sm);
    padding: var(--saico-spacing-sm) var(--saico-spacing-md);
    background-color: var(--saico-bg-secundario);
    border-radius: var(--saico-radius-full);
    color: var(--saico-texto-secundario);
    font-size: var(--saico-font-sm);
    font-weight: var(--saico-font-weight-medium);
}

.archivo-meta svg {
    width: 20px;
    height: 20px;
    stroke-width: 2px;
}

.saico-sin-posts {
    text-align: center;
    padding: var(--saico-spacing-3xl);
    background-color: var(--saico-bg-primario);
    border-radius: var(--saico-radius-xl);
    box-shadow: var(--saico-shadow-md);
}

.saico-sin-posts svg {
    color: var(--saico-texto-terciario);
    margin-bottom: var(--saico-spacing-lg);
}

.saico-sin-posts h2 {
    font-size: var(--saico-font-2xl);
    margin-bottom: var(--saico-spacing-md);
    color: var(--saico-texto-primario);
}

.saico-sin-posts p {
    color: var(--saico-texto-secundario);
    margin-bottom: var(--saico-spacing-lg);
}

.post-categoria {
    display: inline-block;
    padding: 2px var(--saico-spacing-sm);
    background-color: var(--saico-primario-light);
    color: var(--saico-primario);
    font-size: var(--saico-font-xs);
    font-weight: var(--saico-font-weight-semibold);
    border-radius: var(--saico-radius-sm);
    text-decoration: none;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.post-categoria:hover {
    background-color: var(--saico-primario);
    color: white;
}

@media (max-width: 768px) {
    .archivo-titulo {
        font-size: var(--saico-font-3xl);
    }

    .saico-archivo-header {
        padding: var(--saico-spacing-xl) 0;
    }
}
</style>

<?php
get_footer();
