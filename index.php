<?php
/**
 * Template principal de fallback
 */

get_header(); ?>

<div class="saico-contenedor">
    <main class="saico-contenido-principal">
        <?php
        if (have_posts()) :
            while (have_posts()) : the_post();
                get_template_part('partes/contenido', get_post_type());
            endwhile;

            the_posts_pagination(array(
                'prev_text' => '<i class="fas fa-arrow-left"></i> Anterior',
                'next_text' => 'Siguiente <i class="fas fa-arrow-right"></i>',
            ));
        else :
            echo '<p>No se encontraron publicaciones.</p>';
        endif;
        ?>
    </main>

    <?php get_sidebar(); ?>
</div>

<?php get_footer();
