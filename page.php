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
        <div class="saico-pagina-layout">

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

            <!-- Sidebar (si está activo) -->
            <?php if (is_active_sidebar('sidebar-principal')) : ?>
                <?php get_sidebar(); ?>
            <?php endif; ?>

        </div>
    </div>
</div>

<style>
.saico-pagina {
    padding: var(--saico-spacing-2xl) 0;
    min-height: 100vh;
}

.saico-pagina-layout {
    display: grid;
    grid-template-columns: 1fr;
    gap: var(--saico-spacing-2xl);
}

.saico-pagina-layout.con-sidebar {
    grid-template-columns: 1fr 350px;
}

.saico-pagina-contenido {
    background-color: var(--saico-bg-primario);
    border-radius: var(--saico-radius-xl);
    box-shadow: var(--saico-shadow-md);
    padding: var(--saico-spacing-2xl);
}

.pagina-imagen-destacada {
    margin-bottom: var(--saico-spacing-xl);
    border-radius: var(--saico-radius-lg);
    overflow: hidden;
}

.pagina-imagen-destacada img {
    width: 100%;
    height: auto;
}

.pagina-header {
    margin-bottom: var(--saico-spacing-xl);
    padding-bottom: var(--saico-spacing-lg);
    border-bottom: 2px solid var(--saico-borde-claro);
}

.pagina-titulo {
    font-size: var(--saico-font-4xl);
    font-weight: var(--saico-font-weight-bold);
    color: var(--saico-texto-primario);
    margin: 0;
}

.pagina-contenido-texto {
    font-size: var(--saico-font-base);
    line-height: var(--saico-line-height-relaxed);
    color: var(--saico-texto-secundario);
}

.pagina-contenido-texto h2,
.pagina-contenido-texto h3,
.pagina-contenido-texto h4,
.pagina-contenido-texto h5,
.pagina-contenido-texto h6 {
    color: var(--saico-texto-primario);
    margin-top: var(--saico-spacing-xl);
    margin-bottom: var(--saico-spacing-md);
    font-weight: var(--saico-font-weight-bold);
}

.pagina-contenido-texto p {
    margin-bottom: var(--saico-spacing-md);
}

.pagina-contenido-texto ul,
.pagina-contenido-texto ol {
    margin-bottom: var(--saico-spacing-md);
    padding-left: var(--saico-spacing-xl);
}

.pagina-contenido-texto li {
    margin-bottom: var(--saico-spacing-sm);
}

.pagina-contenido-texto img {
    max-width: 100%;
    height: auto;
    border-radius: var(--saico-radius-md);
    margin: var(--saico-spacing-lg) 0;
}

.pagina-contenido-texto a {
    color: var(--saico-primario);
    text-decoration: underline;
}

.pagina-contenido-texto a:hover {
    color: var(--saico-primario-hover);
}

.pagina-contenido-texto blockquote {
    padding: var(--saico-spacing-lg);
    margin: var(--saico-spacing-lg) 0;
    background-color: var(--saico-bg-secundario);
    border-left: 4px solid var(--saico-primario);
    border-radius: var(--saico-radius-md);
    font-style: italic;
}

.pagina-contenido-texto code {
    padding: 2px 6px;
    background-color: var(--saico-bg-terciario);
    border-radius: var(--saico-radius-sm);
    font-family: var(--saico-font-mono);
    font-size: 0.9em;
}

.pagina-contenido-texto pre {
    padding: var(--saico-spacing-lg);
    background-color: var(--saico-bg-oscuro);
    color: var(--saico-texto-blanco);
    border-radius: var(--saico-radius-md);
    overflow-x: auto;
    margin-bottom: var(--saico-spacing-md);
}

.pagina-contenido-texto pre code {
    padding: 0;
    background: none;
    color: inherit;
}

.pagina-contenido-texto table {
    width: 100%;
    margin-bottom: var(--saico-spacing-md);
    border-collapse: collapse;
}

.pagina-contenido-texto table th,
.pagina-contenido-texto table td {
    padding: var(--saico-spacing-sm) var(--saico-spacing-md);
    border: 1px solid var(--saico-borde-claro);
    text-align: left;
}

.pagina-contenido-texto table th {
    background-color: var(--saico-bg-secundario);
    font-weight: var(--saico-font-weight-semibold);
}

.page-links {
    margin-top: var(--saico-spacing-xl);
    padding-top: var(--saico-spacing-lg);
    border-top: 2px solid var(--saico-borde-claro);
}

.page-links a {
    display: inline-block;
    padding: var(--saico-spacing-sm) var(--saico-spacing-md);
    background-color: var(--saico-bg-secundario);
    color: var(--saico-texto-primario);
    text-decoration: none;
    border-radius: var(--saico-radius-md);
    margin-right: var(--saico-spacing-sm);
    transition: all var(--saico-transition-fast);
}

.page-links a:hover {
    background-color: var(--saico-primario);
    color: white;
}

@media (max-width: 992px) {
    .saico-pagina-layout.con-sidebar {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .saico-pagina {
        padding: var(--saico-spacing-xl) 0;
    }

    .saico-pagina-contenido {
        padding: var(--saico-spacing-lg);
    }

    .pagina-titulo {
        font-size: var(--saico-font-3xl);
    }
}

@media (max-width: 480px) {
    .saico-pagina-contenido {
        padding: var(--saico-spacing-md);
    }

    .pagina-titulo {
        font-size: var(--saico-font-2xl);
    }
}
</style>

<?php
// Agregar clase con-sidebar si el sidebar está activo
if (is_active_sidebar('sidebar-principal')) {
    ?>
    <script>
    document.querySelector('.saico-pagina-layout').classList.add('con-sidebar');
    </script>
    <?php
}
?>

<?php
get_footer();
