<?php
/**
 * Template: Sidebar
 *
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

if (!is_active_sidebar('sidebar-principal')) {
    return;
}
?>

<aside class="saico-sidebar" id="saicoSidebar">
    <!-- Botón cerrar (solo móvil) -->
    <div class="saico-sidebar-cerrar">
        <button type="button" aria-label="Cerrar sidebar" onclick="cerrarSidebar()">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>

    <div class="saico-sidebar-widgets">
        <?php dynamic_sidebar('sidebar-principal'); ?>
    </div>
</aside>

<!-- Botón toggle sidebar (solo móvil) -->
<button class="saico-sidebar-toggle" id="saicoSidebarToggle" aria-label="Abrir sidebar">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor">
        <line x1="3" y1="12" x2="21" y2="12"></line>
        <line x1="3" y1="6" x2="21" y2="6"></line>
        <line x1="3" y1="18" x2="21" y2="18"></line>
    </svg>
</button>

<!-- Overlay -->
<div class="saico-sidebar-overlay" id="saicoSidebarOverlay"></div>

<script>
(function($) {
    'use strict';

    $(document).ready(function() {
        const $sidebar = $('#saicoSidebar');
        const $toggle = $('#saicoSidebarToggle');
        const $overlay = $('#saicoSidebarOverlay');
        const $cerrar = $('.saico-sidebar-cerrar button');

        // Abrir sidebar
        $toggle.on('click', function() {
            $sidebar.addClass('activo');
            $overlay.addClass('activo');
            $('body').addClass('sidebar-abierto');
        });

        // Cerrar sidebar
        function cerrarSidebar() {
            $sidebar.removeClass('activo');
            $overlay.removeClass('activo');
            $('body').removeClass('sidebar-abierto');
        }

        $cerrar.on('click', cerrarSidebar);
        $overlay.on('click', cerrarSidebar);

        // Cerrar con ESC
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && $sidebar.hasClass('activo')) {
                cerrarSidebar();
            }
        });
    });
})(jQuery);
</script>

<style>
/* Prevenir scroll cuando sidebar está abierto en móvil */
body.sidebar-abierto {
    overflow: hidden;
}

.saico-sidebar-cerrar {
    display: none;
}

@media (max-width: 992px) {
    .saico-sidebar-cerrar {
        display: flex;
        justify-content: flex-end;
        margin-bottom: var(--saico-spacing-lg);
    }

    .saico-sidebar-cerrar button {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: none;
        border: none;
        border-radius: var(--saico-radius-md);
        cursor: pointer;
        transition: background-color var(--saico-transition-fast);
        color: var(--saico-texto-secundario);
    }

    .saico-sidebar-cerrar button:hover {
        background-color: var(--saico-bg-secundario);
        color: var(--saico-texto-primario);
    }
}
</style>
