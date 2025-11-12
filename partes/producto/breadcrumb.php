<?php
/**
 * Template Part: Breadcrumb de Producto
 *
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

if (function_exists('woocommerce_breadcrumb')) :
?>

<nav class="saico-breadcrumb" aria-label="breadcrumb">
    <?php
    woocommerce_breadcrumb(array(
        'delimiter'   => '<span class="breadcrumb-separador">/</span>',
        'wrap_before' => '<ol class="breadcrumb-lista">',
        'wrap_after'  => '</ol>',
        'before'      => '<li class="breadcrumb-item">',
        'after'       => '</li>',
        'home'        => _x('Inicio', 'breadcrumb', 'saico-wc'),
    ));
    ?>
</nav>

<style>
.saico-breadcrumb {
    padding: var(--saico-spacing-md) 0;
    margin-bottom: var(--saico-spacing-lg);
}

.breadcrumb-lista {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: var(--saico-spacing-sm);
    list-style: none;
    margin: 0;
    padding: 0;
    font-size: var(--saico-font-sm);
    color: var(--saico-texto-secundario);
}

.breadcrumb-item {
    display: flex;
    align-items: center;
    gap: var(--saico-spacing-sm);
}

.breadcrumb-item a {
    color: var(--saico-texto-secundario);
    text-decoration: none;
    transition: color var(--saico-transition-fast);
}

.breadcrumb-item a:hover {
    color: var(--saico-primario);
    text-decoration: underline;
}

.breadcrumb-separador {
    color: var(--saico-texto-terciario);
    font-size: var(--saico-font-xs);
}

@media (max-width: 768px) {
    .saico-breadcrumb {
        padding: var(--saico-spacing-sm) 0;
        margin-bottom: var(--saico-spacing-md);
    }

    .breadcrumb-lista {
        font-size: 12px;
    }
}
</style>

<?php endif; ?>
