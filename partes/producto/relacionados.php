<?php
/**
 * Template Part: Productos Relacionados
 *
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

global $product;

if (!$product) {
    return;
}

$producto_id = $product->get_id();
$limite = get_theme_mod('saico_relacionados_limite', 4);

// Obtener productos relacionados usando la función del theme
$productos_relacionados = saico_obtener_productos_relacionados($producto_id, $limite);

if (!$productos_relacionados || !$productos_relacionados->have_posts()) {
    return;
}
?>

<section class="saico-productos-relacionados">
    <div class="saico-contenedor">
        <h2>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
            </svg>
            Productos Relacionados
        </h2>

        <div class="productos-grid">
            <?php
            while ($productos_relacionados->have_posts()) {
                $productos_relacionados->the_post();
                wc_get_template_part('loop/card-producto', 'min');
            }
            wp_reset_postdata();
            ?>
        </div>

        <?php if ($productos_relacionados->max_num_pages > 1) : ?>
        <div class="saico-relacionados-ver-mas">
            <button class="saico-btn saico-btn-outline"
                    id="saicoVerMasRelacionados"
                    data-producto-id="<?php echo esc_attr($producto_id); ?>"
                    data-pagina="1"
                    data-max="<?php echo esc_attr($productos_relacionados->max_num_pages); ?>">
                Ver más productos relacionados
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </button>
        </div>
        <?php endif; ?>
    </div>
</section>

<script>
(function($) {
    'use strict';

    $(document).ready(function() {
        $('#saicoVerMasRelacionados').on('click', function() {
            const $btn = $(this);
            const productoId = $btn.data('producto-id');
            const paginaActual = parseInt($btn.data('pagina'));
            const paginaSiguiente = paginaActual + 1;
            const maxPages = parseInt($btn.data('max'));

            if (paginaSiguiente > maxPages) {
                $btn.hide();
                return;
            }

            $btn.addClass('loading').prop('disabled', true);

            $.ajax({
                url: saicoData.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'cargar_relacionados',
                    nonce: saicoData.nonce,
                    producto_id: productoId,
                    pagina: paginaSiguiente,
                    limite: <?php echo esc_js($limite); ?>
                },
                success: function(response) {
                    if (response.success) {
                        $('.saico-productos-relacionados .productos-grid').append(response.data.html);
                        $btn.data('pagina', paginaSiguiente);

                        if (paginaSiguiente >= maxPages) {
                            $btn.hide();
                        }
                    }
                },
                error: function() {
                    // Error al cargar productos relacionados
                },
                complete: function() {
                    $btn.removeClass('loading').prop('disabled', false);
                }
            });
        });
    });
})(jQuery);
</script>

<style>
.saico-productos-relacionados {
    padding: var(--saico-spacing-2xl) 0;
    background-color: var(--saico-bg-secundario);
    border-radius: var(--saico-radius-xl);
    margin-top: var(--saico-spacing-3xl);
}

.saico-productos-relacionados h2 {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--saico-spacing-md);
    font-size: var(--saico-font-3xl);
    font-weight: var(--saico-font-weight-bold);
    text-align: center;
    margin-bottom: var(--saico-spacing-xl);
    color: var(--saico-texto-primario);
    position: relative;
    padding-bottom: var(--saico-spacing-md);
}

.saico-productos-relacionados h2::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 4px;
    background: linear-gradient(90deg, var(--saico-primario), var(--saico-acento));
    border-radius: var(--saico-radius-full);
}

.saico-productos-relacionados h2 svg {
    width: 28px;
    height: 28px;
    stroke-width: 2px;
    color: var(--saico-primario);
}

.saico-productos-relacionados .productos-grid {
    padding: 0 var(--saico-spacing-lg);
}

.saico-relacionados-ver-mas {
    text-align: center;
    margin-top: var(--saico-spacing-xl);
}

#saicoVerMasRelacionados {
    display: inline-flex;
    align-items: center;
    gap: var(--saico-spacing-sm);
}

#saicoVerMasRelacionados svg {
    transition: transform var(--saico-transition-fast);
}

#saicoVerMasRelacionados:hover svg {
    transform: translateY(4px);
}

#saicoVerMasRelacionados.loading {
    opacity: 0.6;
    pointer-events: none;
}

#saicoVerMasRelacionados.loading::after {
    content: '';
    width: 16px;
    height: 16px;
    margin-left: var(--saico-spacing-sm);
    border: 2px solid currentColor;
    border-top-color: transparent;
    border-radius: 50%;
    animation: spinner-rotate 0.6s linear infinite;
}

@keyframes spinner-rotate {
    to { transform: rotate(360deg); }
}

@media (max-width: 768px) {
    .saico-productos-relacionados {
        padding: var(--saico-spacing-xl) 0;
        margin-top: var(--saico-spacing-2xl);
    }

    .saico-productos-relacionados h2 {
        font-size: var(--saico-font-2xl);
    }

    .saico-productos-relacionados .productos-grid {
        padding: 0;
    }
}
</style>
