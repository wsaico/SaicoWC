<?php
/**
 * Template Part: Tabs de Producto (Descripción y Reviews)
 *
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

global $product;

if (!$product) {
    return;
}

$producto_id = $product->get_id();
$descripcion = $product->get_description();
$reviews_habilitados = comments_open($producto_id);
?>

<div class="saico-producto-tabs-wrapper">
    <div class="saico-producto-tabs">
        <!-- Navegación de tabs -->
        <div class="saico-tabs-nav">
            <button class="saico-tab-btn activo" data-tab="descripcion">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
                <span>Descripción</span>
            </button>

            <?php if ($reviews_habilitados) : ?>
            <button class="saico-tab-btn" data-tab="reviews">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                </svg>
                <span>Reseñas (<?php echo $product->get_review_count(); ?>)</span>
            </button>
            <?php endif; ?>

            <?php
            // Hook para agregar más tabs personalizados
            do_action('saico_producto_tabs_nav', $producto_id);
            ?>
        </div>

        <!-- Contenido de tabs -->
        <div class="saico-tabs-contenido">
            <!-- Tab: Descripción -->
            <div class="saico-tab-panel activo" id="descripcion">
                <?php if ($descripcion) : ?>
                    <div class="producto-descripcion">
                        <?php echo wp_kses_post($descripcion); ?>
                    </div>
                <?php else : ?>
                    <p class="sin-descripcion">No hay descripción disponible para este producto.</p>
                <?php endif; ?>

                <?php
                // Información adicional (atributos, etc.)
                do_action('woocommerce_product_additional_information', $product);
                ?>
            </div>

            <!-- Tab: Reviews -->
            <?php if ($reviews_habilitados) : ?>
            <div class="saico-tab-panel" id="reviews">
                <?php comments_template(); ?>
            </div>
            <?php endif; ?>

            <?php
            // Hook para contenido de tabs personalizados
            do_action('saico_producto_tabs_contenido', $producto_id);
            ?>
        </div>
    </div>
</div>

<style>
/* Contenedor principal con caja */
.saico-producto-tabs-wrapper {
    margin-top: var(--saico-spacing-xl);
    background: var(--saico-bg-primario);
    border-radius: var(--saico-radius-xl);
    box-shadow: var(--saico-shadow-md);
    overflow: hidden;
}

.saico-producto-tabs {
    width: 100%;
}

.saico-tabs-nav {
    display: flex;
    gap: 0;
    border-bottom: 2px solid var(--saico-borde-claro);
    background: var(--saico-bg-secundario);
}

.saico-tab-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--saico-spacing-sm);
    padding: var(--saico-spacing-lg);
    background: transparent;
    border: none;
    border-bottom: 3px solid transparent;
    font-size: var(--saico-font-base);
    font-weight: var(--saico-font-weight-semibold);
    color: var(--saico-texto-secundario);
    cursor: pointer;
    transition: all var(--saico-transition-fast);
    position: relative;
    margin-bottom: -2px;
}

.saico-tab-btn svg {
    width: 20px;
    height: 20px;
    stroke-width: 2px;
}

.saico-tab-btn:hover {
    color: var(--saico-texto-primario);
    background-color: var(--saico-bg-primario);
}

.saico-tab-btn.activo {
    color: var(--saico-primario);
    border-bottom-color: var(--saico-primario);
    background-color: var(--saico-bg-primario);
}

.saico-tabs-contenido {
    padding: var(--saico-spacing-2xl);
    min-height: 300px;
}

.saico-tab-panel {
    display: none;
    animation: fade-in 0.3s ease-out;
}

.saico-tab-panel.activo {
    display: block;
}

@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.producto-descripcion {
    font-size: var(--saico-font-base);
    line-height: var(--saico-line-height-relaxed);
    color: var(--saico-texto-primario);
}

.producto-descripcion h2,
.producto-descripcion h3,
.producto-descripcion h4 {
    color: var(--saico-texto-primario);
    margin-top: var(--saico-spacing-2xl);
    margin-bottom: var(--saico-spacing-md);
    font-weight: var(--saico-font-weight-bold);
}

.producto-descripcion h2 {
    font-size: var(--saico-font-2xl);
}

.producto-descripcion h3 {
    font-size: var(--saico-font-xl);
}

.producto-descripcion h4 {
    font-size: var(--saico-font-lg);
}

.producto-descripcion p {
    margin-bottom: var(--saico-spacing-lg);
    color: var(--saico-texto-primario);
}

.producto-descripcion ul,
.producto-descripcion ol {
    margin-bottom: var(--saico-spacing-lg);
    padding-left: var(--saico-spacing-2xl);
    color: var(--saico-texto-primario);
}

.producto-descripcion li {
    margin-bottom: var(--saico-spacing-sm);
}

.producto-descripcion strong {
    font-weight: var(--saico-font-weight-bold);
    color: var(--saico-texto-primario);
}

.producto-descripcion a {
    color: var(--saico-primario);
    text-decoration: underline;
    transition: color var(--saico-transition-fast);
}

.producto-descripcion a:hover {
    color: var(--saico-primario-hover);
}

.producto-descripcion img {
    max-width: 100%;
    height: auto;
    border-radius: var(--saico-radius-md);
    margin: var(--saico-spacing-xl) 0;
}

.producto-descripcion blockquote {
    margin: var(--saico-spacing-xl) 0;
    padding: var(--saico-spacing-lg);
    border-left: 4px solid var(--saico-primario);
    background: var(--saico-bg-secundario);
    border-radius: var(--saico-radius-md);
    color: var(--saico-texto-primario);
}

.sin-descripcion {
    padding: var(--saico-spacing-2xl);
    text-align: center;
    color: var(--saico-texto-terciario);
    font-style: italic;
}

@media (max-width: 768px) {
    .saico-tabs-contenido {
        padding: var(--saico-spacing-xl);
    }

    .saico-tab-btn {
        padding: var(--saico-spacing-md);
        font-size: var(--saico-font-sm);
    }

    .saico-tab-btn svg {
        width: 18px;
        height: 18px;
    }
}

@media (max-width: 480px) {
    .saico-tabs-contenido {
        padding: var(--saico-spacing-lg);
    }

    .saico-tab-btn span {
        display: none;
    }

    .saico-tab-btn svg {
        margin: 0;
    }

    .saico-tab-btn {
        padding: var(--saico-spacing-md);
    }
}
</style>
