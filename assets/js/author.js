/**
 * Página de Autor
 * Maneja tabs y carga dinámica de contenido
 *
 * @package SaicoWC
 * @version 1.0.0
 */

(function($) {
    'use strict';

    /**
     * Inicializar todo cuando el DOM esté listo
     */
    $(document).ready(function() {
        initTabs();
        initCargarMasProductos();
        initCargarMasPosts();
    });

    /**
     * ========================================================================
     * TABS
     * ========================================================================
     */
    function initTabs() {
        $('.autor-tab').on('click', function() {
            const $btn = $(this);
            const tab = $btn.data('tab');

            // Cambiar tab activo
            $('.autor-tab').removeClass('activo');
            $btn.addClass('activo');

            // Cambiar contenido activo
            $('.autor-tab-content').removeClass('activo');
            $('#tab' + tab.charAt(0).toUpperCase() + tab.slice(1)).addClass('activo');
        });
    }

    /**
     * ========================================================================
     * CARGAR MÁS PRODUCTOS
     * ========================================================================
     */
    function initCargarMasProductos() {
        $('#cargarMasProductos').on('click', function() {
            const $btn = $(this);
            const autorId = $btn.data('autor');
            const paginaActual = parseInt($btn.data('pag'));
            const maxPaginas = parseInt($btn.data('max'));

            // Validar que hay más páginas
            if (paginaActual >= maxPaginas) {
                return;
            }

            // Deshabilitar botón
            $btn.prop('disabled', true).text('Cargando...');

            // AJAX
            $.ajax({
                url: saicoData.ajaxurl,
                type: 'POST',
                data: {
                    action: 'saico_cargar_autor_productos',
                    nonce: saicoData.nonce,
                    autor_id: autorId,
                    pagina: paginaActual + 1
                },
                success: function(response) {
                    if (response.success) {
                        // Agregar productos
                        $('#autorProductos').append(response.data.html);

                        // Actualizar página actual
                        $btn.data('pag', paginaActual + 1);

                        // Verificar si hay más
                        if (!response.data.tiene_mas) {
                            $btn.fadeOut();
                        } else {
                            $btn.prop('disabled', false).html('Cargar Más Productos<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12l7 7 7-7"/></svg>');
                        }
                    } else {
                        $btn.prop('disabled', false).text('Error al cargar');
                    }
                },
                error: function(xhr, status, error) {
                    $btn.prop('disabled', false).text('Error de conexión');
                }
            });
        });
    }

    /**
     * ========================================================================
     * CARGAR MÁS POSTS
     * ========================================================================
     */
    function initCargarMasPosts() {
        $('#cargarMasPosts').on('click', function() {
            const $btn = $(this);
            const autorId = $btn.data('autor');
            const paginaActual = parseInt($btn.data('pag'));
            const maxPaginas = parseInt($btn.data('max'));

            // Validar que hay más páginas
            if (paginaActual >= maxPaginas) {
                return;
            }

            // Deshabilitar botón
            $btn.prop('disabled', true).text('Cargando...');

            // AJAX
            $.ajax({
                url: saicoData.ajaxurl,
                type: 'POST',
                data: {
                    action: 'saico_cargar_autor_posts',
                    nonce: saicoData.nonce,
                    autor_id: autorId,
                    pagina: paginaActual + 1
                },
                success: function(response) {
                    if (response.success) {
                        // Agregar posts
                        $('#autorPosts').append(response.data.html);

                        // Actualizar página actual
                        $btn.data('pag', paginaActual + 1);

                        // Verificar si hay más
                        if (!response.data.tiene_mas) {
                            $btn.fadeOut();
                        } else {
                            $btn.prop('disabled', false).html('Cargar Más Artículos<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12l7 7 7-7"/></svg>');
                        }
                    } else {
                        $btn.prop('disabled', false).text('Error al cargar');
                    }
                },
                error: function(xhr, status, error) {
                    $btn.prop('disabled', false).text('Error de conexión');
                }
            });
        });
    }

})(jQuery);
