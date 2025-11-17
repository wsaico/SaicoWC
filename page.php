<?php
/**
 * Template: Página Individual
 *
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

get_header();
?>

<div class="saico-pagina">
    <div class="saico-contenedor">
        <div class="saico-pagina-layout <?php echo saico_get_layout_class(); ?>">

            <!-- Contenido Principal -->
            <main class="saico-pagina-main">
                <?php
                while (have_posts()) :
                    the_post();
                ?>

                <article id="page-<?php the_ID(); ?>" <?php post_class('saico-pagina-contenido'); ?>>

                    <!-- Imagen Destacada -->
                    <?php if (has_post_thumbnail()) : ?>
                    <div class="pagina-imagen-destacada">
                        <?php the_post_thumbnail('large'); ?>
                    </div>
                    <?php endif; ?>

                    <!-- Header -->
                    <header class="pagina-header">
                        <h1 class="pagina-titulo"><?php the_title(); ?></h1>
                    </header>

                    <!-- Contenido -->
                    <div class="pagina-contenido-texto">
                        <?php the_content(); ?>

                        <?php
                        wp_link_pages(array(
                            'before' => '<div class="page-links">' . esc_html__('Páginas:', 'saico-wc'),
                            'after'  => '</div>',
                        ));
                        ?>
                    </div>

                </article>

                <!-- Comentarios (si están habilitados) -->
                <?php
                if (comments_open() || get_comments_number()) {
                    comments_template();
                }
                ?>

                <?php endwhile; ?>
            </main>

            <!-- Sidebar (según configuración) -->
            <?php if (saico_should_show_sidebar()) : ?>
                <?php get_sidebar(); ?>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php
get_footer();
