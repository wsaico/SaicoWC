<?php
/**
 * Template: Resultados de Búsqueda
 *
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

get_header();
?>

<div class="saico-busqueda">
    <div class="saico-contenedor">

        <!-- Header de Búsqueda -->
        <header class="saico-busqueda-header">
            <h1 class="busqueda-titulo">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                Resultados de búsqueda para: <span class="termino">"<?php echo get_search_query(); ?>"</span>
            </h1>

            <div class="busqueda-meta">
                <span><?php echo esc_html($wp_query->found_posts); ?> resultados encontrados</span>
            </div>

            <!-- Formulario de búsqueda -->
            <div class="busqueda-form-wrapper">
                <form role="search" method="get" class="busqueda-form" action="<?php echo esc_url(home_url('/')); ?>">
                    <input type="search"
                           class="busqueda-input"
                           placeholder="Buscar..."
                           value="<?php echo get_search_query(); ?>"
                           name="s"
                           required>
                    <button type="submit" class="busqueda-submit">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        Buscar
                    </button>
                </form>
            </div>
        </header>

        <div class="saico-blog-layout">

            <!-- Contenido Principal -->
            <main class="saico-blog-main">
                <?php if (have_posts()) : ?>

                <!-- Tabs de tipo de contenido -->
                <div class="busqueda-tabs">
                    <?php
                    // Contar tipos de posts
                    $post_types = array();
                    $temp_query = clone $wp_query;

                    if ($temp_query->have_posts()) {
                        while ($temp_query->have_posts()) {
                            $temp_query->the_post();
                            $tipo = get_post_type();
                            if (!isset($post_types[$tipo])) {
                                $post_types[$tipo] = 0;
                            }
                            $post_types[$tipo]++;
                        }
                        wp_reset_postdata();
                    }

                    // Mostrar tabs si hay múltiples tipos
                    if (count($post_types) > 1) :
                    ?>
                    <div class="tabs-nav">
                        <button class="tab-btn activo" data-tipo="todos">
                            Todos (<?php echo $wp_query->found_posts; ?>)
                        </button>
                        <?php foreach ($post_types as $tipo => $cantidad) :
                            $obj_tipo = get_post_type_object($tipo);
                            if ($obj_tipo) :
                        ?>
                        <button class="tab-btn" data-tipo="<?php echo esc_attr($tipo); ?>">
                            <?php echo esc_html($obj_tipo->labels->name); ?> (<?php echo $cantidad; ?>)
                        </button>
                        <?php
                            endif;
                        endforeach;
                        ?>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="saico-posts-lista">
                    <?php
                    while (have_posts()) :
                        the_post();
                        $post_type = get_post_type();
                    ?>

                    <article id="post-<?php the_ID(); ?>" <?php post_class('saico-resultado-item'); ?> data-tipo="<?php echo esc_attr($post_type); ?>">

                        <?php if ($post_type === 'product' && class_exists('WooCommerce')) : ?>
                            <!-- Producto -->
                            <a href="<?php the_permalink(); ?>" class="resultado-imagen">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('medium'); ?>
                                <?php else : ?>
                                    <div class="sin-imagen producto">
                                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <circle cx="9" cy="21" r="1"></circle>
                                            <circle cx="20" cy="21" r="1"></circle>
                                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </a>

                            <div class="resultado-contenido">
                                <div class="resultado-tipo producto-badge">Producto</div>
                                <h2 class="resultado-titulo">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>
                                <?php
                                global $product;
                                if ($product) :
                                ?>
                                <div class="resultado-precio"><?php echo $product->get_price_html(); ?></div>
                                <?php endif; ?>
                                <div class="resultado-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?></div>
                            </div>

                        <?php else : ?>
                            <!-- Post normal -->
                            <a href="<?php the_permalink(); ?>" class="resultado-imagen">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('medium'); ?>
                                <?php else : ?>
                                    <div class="sin-imagen">
                                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                            <polyline points="14 2 14 8 20 8"></polyline>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                            </a>

                            <div class="resultado-contenido">
                                <?php
                                $tipo_obj = get_post_type_object($post_type);
                                if ($tipo_obj) :
                                ?>
                                <div class="resultado-tipo"><?php echo esc_html($tipo_obj->labels->singular_name); ?></div>
                                <?php endif; ?>

                                <h2 class="resultado-titulo">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>

                                <div class="resultado-meta">
                                    <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                                        <?php echo esc_html(get_the_date()); ?>
                                    </time>
                                    <span class="separador">•</span>
                                    <span>Por <?php the_author(); ?></span>
                                </div>

                                <div class="resultado-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 30, '...'); ?></div>
                            </div>
                        <?php endif; ?>

                    </article>

                    <?php endwhile; ?>
                </div>

                <!-- Paginación -->
                <?php
                the_posts_pagination(array(
                    'mid_size'  => 2,
                    'prev_text' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="15 18 9 12 15 6"></polyline></svg>',
                    'next_text' => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="9 18 15 12 9 6"></polyline></svg>',
                    'class' => 'saico-paginacion',
                ));
                ?>

                <?php else : ?>

                <!-- Sin resultados -->
                <div class="saico-sin-resultados">
                    <svg width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        <line x1="8" y1="11" x2="14" y2="11"></line>
                    </svg>
                    <h2>No se encontraron resultados</h2>
                    <p>No pudimos encontrar nada para "<?php echo get_search_query(); ?>"</p>
                    <p>Intenta con otros términos de búsqueda.</p>

                    <!-- Sugerencias -->
                    <div class="busqueda-sugerencias">
                        <h3>Sugerencias:</h3>
                        <ul>
                            <li>Verifica la ortografía de las palabras</li>
                            <li>Intenta con palabras clave diferentes</li>
                            <li>Usa términos más generales</li>
                        </ul>
                    </div>
                </div>

                <?php endif; ?>
            </main>

            <!-- Sidebar -->
            <?php get_sidebar(); ?>

        </div>
    </div>
</div>

<style>
.saico-busqueda {
    padding: var(--saico-spacing-2xl) 0;
    min-height: 100vh;
}

.saico-busqueda-header {
    text-align: center;
    padding: var(--saico-spacing-2xl) 0;
    margin-bottom: var(--saico-spacing-xl);
    background-color: var(--saico-bg-primario);
    border-radius: var(--saico-radius-xl);
    box-shadow: var(--saico-shadow-md);
}

.busqueda-titulo {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--saico-spacing-md);
    font-size: var(--saico-font-3xl);
    font-weight: var(--saico-font-weight-bold);
    margin-bottom: var(--saico-spacing-md);
    color: var(--saico-texto-primario);
}

.busqueda-titulo svg {
    color: var(--saico-primario);
    stroke-width: 2px;
}

.busqueda-titulo .termino {
    color: var(--saico-primario);
}

.busqueda-meta {
    margin-bottom: var(--saico-spacing-lg);
    color: var(--saico-texto-secundario);
    font-size: var(--saico-font-sm);
}

.busqueda-form-wrapper {
    max-width: 600px;
    margin: 0 auto;
    padding: 0 var(--saico-spacing-lg);
}

.busqueda-form {
    display: flex;
    gap: var(--saico-spacing-sm);
}

.busqueda-input {
    flex: 1;
}

.busqueda-submit {
    display: inline-flex;
    align-items: center;
    gap: var(--saico-spacing-sm);
}

.busqueda-tabs {
    margin-bottom: var(--saico-spacing-xl);
}

.tabs-nav {
    display: flex;
    gap: var(--saico-spacing-sm);
    flex-wrap: wrap;
    justify-content: center;
    padding: var(--saico-spacing-lg);
    background-color: var(--saico-bg-primario);
    border-radius: var(--saico-radius-lg);
    box-shadow: var(--saico-shadow-sm);
}

.tab-btn {
    padding: var(--saico-spacing-sm) var(--saico-spacing-lg);
    background-color: var(--saico-bg-secundario);
    border: 2px solid transparent;
    border-radius: var(--saico-radius-md);
    color: var(--saico-texto-secundario);
    font-weight: var(--saico-font-weight-semibold);
    cursor: pointer;
    transition: all var(--saico-transition-fast);
}

.tab-btn:hover {
    background-color: var(--saico-bg-terciario);
    border-color: var(--saico-primario-light);
}

.tab-btn.activo {
    background-color: var(--saico-primario);
    border-color: var(--saico-primario);
    color: white;
}

.saico-resultado-item {
    display: flex;
    gap: var(--saico-spacing-lg);
    padding: var(--saico-spacing-lg);
    background-color: var(--saico-bg-primario);
    border-radius: var(--saico-radius-lg);
    box-shadow: var(--saico-shadow-sm);
    transition: all var(--saico-transition-base);
    margin-bottom: var(--saico-spacing-lg);
}

.saico-resultado-item:hover {
    box-shadow: var(--saico-shadow-lg);
    transform: translateY(-4px);
}

.resultado-imagen {
    flex-shrink: 0;
    width: 200px;
    aspect-ratio: 1;
    border-radius: var(--saico-radius-md);
    overflow: hidden;
    background-color: var(--saico-bg-secundario);
}

.resultado-imagen img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.sin-imagen {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, var(--saico-bg-secundario), var(--saico-bg-terciario));
    color: var(--saico-texto-terciario);
}

.resultado-contenido {
    flex: 1;
}

.resultado-tipo {
    display: inline-block;
    padding: 2px var(--saico-spacing-sm);
    background-color: var(--saico-bg-terciario);
    color: var(--saico-texto-terciario);
    font-size: var(--saico-font-xs);
    font-weight: var(--saico-font-weight-semibold);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-radius: var(--saico-radius-sm);
    margin-bottom: var(--saico-spacing-sm);
}

.producto-badge {
    background-color: var(--saico-primario-light);
    color: var(--saico-primario);
}

.resultado-titulo {
    font-size: var(--saico-font-xl);
    font-weight: var(--saico-font-weight-bold);
    margin-bottom: var(--saico-spacing-sm);
}

.resultado-titulo a {
    color: var(--saico-texto-primario);
    text-decoration: none;
    transition: color var(--saico-transition-fast);
}

.resultado-titulo a:hover {
    color: var(--saico-primario);
}

.resultado-meta {
    display: flex;
    align-items: center;
    gap: var(--saico-spacing-sm);
    font-size: var(--saico-font-sm);
    color: var(--saico-texto-terciario);
    margin-bottom: var(--saico-spacing-sm);
}

.resultado-meta .separador {
    color: var(--saico-borde-medio);
}

.resultado-precio {
    font-size: var(--saico-font-lg);
    font-weight: var(--saico-font-weight-bold);
    color: var(--saico-primario);
    margin-bottom: var(--saico-spacing-sm);
}

.resultado-excerpt {
    color: var(--saico-texto-secundario);
    line-height: var(--saico-line-height-relaxed);
}

.saico-sin-resultados {
    text-align: center;
    padding: var(--saico-spacing-3xl);
    background-color: var(--saico-bg-primario);
    border-radius: var(--saico-radius-xl);
    box-shadow: var(--saico-shadow-md);
}

.saico-sin-resultados svg {
    color: var(--saico-texto-terciario);
    margin-bottom: var(--saico-spacing-lg);
    stroke-width: 1.5px;
}

.saico-sin-resultados h2 {
    font-size: var(--saico-font-2xl);
    margin-bottom: var(--saico-spacing-md);
    color: var(--saico-texto-primario);
}

.saico-sin-resultados p {
    color: var(--saico-texto-secundario);
    margin-bottom: var(--saico-spacing-sm);
}

.busqueda-sugerencias {
    max-width: 400px;
    margin: var(--saico-spacing-xl) auto 0;
    text-align: left;
    padding: var(--saico-spacing-lg);
    background-color: var(--saico-bg-secundario);
    border-radius: var(--saico-radius-md);
}

.busqueda-sugerencias h3 {
    font-size: var(--saico-font-base);
    font-weight: var(--saico-font-weight-semibold);
    margin-bottom: var(--saico-spacing-md);
    color: var(--saico-texto-primario);
}

.busqueda-sugerencias ul {
    list-style-position: inside;
    color: var(--saico-texto-secundario);
}

.busqueda-sugerencias li {
    margin-bottom: var(--saico-spacing-sm);
}

@media (max-width: 768px) {
    .saico-resultado-item {
        flex-direction: column;
    }

    .resultado-imagen {
        width: 100%;
    }

    .busqueda-titulo {
        font-size: var(--saico-font-2xl);
    }
}

@media (max-width: 480px) {
    .busqueda-form {
        flex-direction: column;
    }

    .busqueda-submit {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
(function($) {
    'use strict';

    $(document).ready(function() {
        // Filtrar por tipo de contenido
        $('.tab-btn').on('click', function() {
            const $btn = $(this);
            const tipo = $btn.data('tipo');

            // Actualizar botones
            $('.tab-btn').removeClass('activo');
            $btn.addClass('activo');

            // Filtrar resultados
            if (tipo === 'todos') {
                $('.saico-resultado-item').show();
            } else {
                $('.saico-resultado-item').hide();
                $('.saico-resultado-item[data-tipo="' + tipo + '"]').show();
            }
        });
    });
})(jQuery);
</script>

<?php
get_footer();
