/**
 * Front Page JavaScript
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        initFiltros();
        initCargarMas();
    });

    /**
     * Filtros de productos
     */
    function initFiltros() {
        $('.filtro').on('click', function() {
            const $btn = $(this);
            const filtro = $btn.data('filtro');

            // Actualizar botón activo
            $('.filtro').removeClass('activo');
            $btn.addClass('activo');

            // Mostrar loading
            const $grid = $('#productosGrid');
            $grid.css('opacity', '0.5');

            // AJAX para filtrar
            $.ajax({
                url: saicoData.ajaxurl,
                type: 'POST',
                data: {
                    action: 'saico_ajax_filtrar_productos',
                    nonce: saicoData.nonce,
                    filtro: filtro,
                    pagina: 1
                },
                success: function(response) {
                    if (response.success) {
                        $grid.html(response.data.html).css('opacity', '1');

                        // Actualizar botón cargar más
                        const $btnCargar = $('#cargarMas');
                        if (response.data.tiene_mas) {
                            $btnCargar.show().data('pag', 1).data('filtro', filtro);
                        } else {
                            $btnCargar.hide();
                        }
                    } else {
                        $grid.css('opacity', '1');
                        alert('Error al filtrar productos');
                    }
                },
                error: function() {
                    $grid.css('opacity', '1');
                    alert('Error de conexión');
                }
            });
        });
    }

    /**
     * Cargar más productos
     */
    function initCargarMas() {
        $('#cargarMas').on('click', function() {
            const $btn = $(this);
            const paginaActual = parseInt($btn.data('pag'));
            const maxPaginas = parseInt($btn.data('max'));
            const filtro = $btn.data('filtro') || 'todos';

            if (paginaActual >= maxPaginas) {
                return;
            }

            // Deshabilitar botón
            $btn.prop('disabled', true).text('Cargando...');

            // AJAX para cargar más
            $.ajax({
                url: saicoData.ajaxurl,
                type: 'POST',
                data: {
                    action: 'saico_ajax_filtrar_productos',
                    nonce: saicoData.nonce,
                    filtro: filtro,
                    pagina: paginaActual + 1
                },
                success: function(response) {
                    if (response.success) {
                        $('#productosGrid').append(response.data.html);
                        $btn.data('pag', paginaActual + 1);

                        // Verificar si hay más páginas
                        if (!response.data.tiene_mas) {
                            $btn.hide();
                        } else {
                            $btn.prop('disabled', false).html('Cargar Más<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12l7 7 7-7"/></svg>');
                        }
                    } else {
                        $btn.prop('disabled', false).html('Cargar Más<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12l7 7 7-7"/></svg>');
                        alert('Error al cargar productos');
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).html('Cargar Más<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12l7 7 7-7"/></svg>');
                    alert('Error de conexión');
                }
            });
        });
    }

})(jQuery);
