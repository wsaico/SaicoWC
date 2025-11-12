<?php
/**
 * Template: Post Individual
 *
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

get_header();
?>

<div class="saico-blog">
    <div class="saico-contenedor">
        <div class="saico-blog-layout">

            <!-- Contenido Principal -->
            <main class="saico-blog-main">
                <?php
                while (have_posts()) :
                    the_post();
                ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class('saico-post-single'); ?>>

                    <!-- Imagen Destacada -->
                    <?php if (has_post_thumbnail()) : ?>
                    <div class="post-single-imagen">
                        <?php the_post_thumbnail('large'); ?>
                    </div>
                    <?php endif; ?>

                    <!-- Header del Post -->
                    <header class="post-single-header">
                        <h1 class="post-single-titulo"><?php the_title(); ?></h1>

                        <div class="post-single-meta">
                            <!-- Autor -->
                            <div class="post-meta-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <span>Por <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>"><?php the_author(); ?></a></span>
                            </div>

                            <!-- Fecha -->
                            <div class="post-meta-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                    <?php echo esc_html(get_the_date()); ?>
                                </time>
                            </div>

                            <!-- Categoría -->
                            <?php
                            $categorias = get_the_category();
                            if ($categorias) :
                            ?>
                            <div class="post-meta-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                                </svg>
                                <a href="<?php echo esc_url(get_category_link($categorias[0]->term_id)); ?>">
                                    <?php echo esc_html($categorias[0]->name); ?>
                                </a>
                            </div>
                            <?php endif; ?>

                            <!-- Comentarios -->
                            <?php if (comments_open() || get_comments_number()) : ?>
                            <div class="post-meta-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                </svg>
                                <span><?php echo esc_html(get_comments_number()); ?> comentarios</span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </header>

                    <!-- Contenido del Post -->
                    <div class="post-single-contenido">
                        <?php the_content(); ?>

                        <?php
                        wp_link_pages(array(
                            'before' => '<div class="page-links">' . esc_html__('Páginas:', 'saico-wc'),
                            'after'  => '</div>',
                        ));
                        ?>
                    </div>

                    <!-- Footer del Post -->
                    <footer class="post-single-footer">
                        <!-- Tags -->
                        <?php
                        $tags = get_the_tags();
                        if ($tags) :
                        ?>
                        <div class="post-tags">
                            <?php foreach ($tags as $tag) : ?>
                                <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="post-tag">
                                    #<?php echo esc_html($tag->name); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Compartir -->
                        <div class="post-compartir">
                            <span class="post-compartir-texto">Compartir:</span>
                            <div class="post-compartir-btns">
                                <button class="compartir-btn facebook"
                                        onclick="saicoCompartir('facebook')"
                                        title="Compartir en Facebook">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                                    </svg>
                                </button>
                                <button class="compartir-btn twitter"
                                        onclick="saicoCompartir('twitter')"
                                        title="Compartir en Twitter">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path>
                                    </svg>
                                </button>
                                <button class="compartir-btn linkedin"
                                        onclick="saicoCompartir('linkedin')"
                                        title="Compartir en LinkedIn">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path>
                                        <rect x="2" y="9" width="4" height="12"></rect>
                                        <circle cx="4" cy="4" r="2"></circle>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </footer>

                </article>

                <!-- Navegación entre posts -->
                <?php
                $prev_post = get_previous_post();
                $next_post = get_next_post();

                if ($prev_post || $next_post) :
                ?>
                <nav class="post-navegacion">
                    <?php if ($prev_post) : ?>
                    <a href="<?php echo esc_url(get_permalink($prev_post)); ?>" class="post-nav-link">
                        <span class="post-nav-label">← Anterior</span>
                        <span class="post-nav-titulo"><?php echo esc_html(get_the_title($prev_post)); ?></span>
                    </a>
                    <?php else : ?>
                    <div></div>
                    <?php endif; ?>

                    <?php if ($next_post) : ?>
                    <a href="<?php echo esc_url(get_permalink($next_post)); ?>" class="post-nav-link">
                        <span class="post-nav-label">Siguiente →</span>
                        <span class="post-nav-titulo"><?php echo esc_html(get_the_title($next_post)); ?></span>
                    </a>
                    <?php endif; ?>
                </nav>
                <?php endif; ?>

                <!-- Comentarios -->
                <?php
                if (comments_open() || get_comments_number()) {
                    comments_template();
                }
                ?>

                <?php endwhile; ?>
            </main>

            <!-- Sidebar -->
            <?php get_sidebar(); ?>

        </div>
    </div>
</div>

<?php
get_footer();
