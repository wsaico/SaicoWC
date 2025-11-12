/**
 * Portada / Front Page - Saico WC
 * Funcionalidad de filtros, cargar más, y animaciones
 */

(function($) {
    'use strict';

    // Variables globales
    let filtroActual = 'todos';
    let paginaActual = 1;
    let cargando = false;

    /**
     * Inicialización
     */
    $(document).ready(function() {
        initFiltrosProductos();
        initCargarMas();
        initInfiniteScroll();
        initAnimacionesEntrada();
    });

    /**
     * Filtros de Productos
     */
    function initFiltrosProductos() {
        $('.filtro-btn').on('click', function(e) {
            e.preventDefault();

            if (cargando) return;

            const $btn = $(this);
            const filtro = $btn.data('filtro');

            // Si ya está activo, no hacer nada
            if ($btn.hasClass('activo')) return;

            // Actualizar UI
            $('.filtro-btn').removeClass('activo');
            $btn.addClass('activo');

            // Actualizar filtro actual
            filtroActual = filtro;
            paginaActual = 1;

            // Filtrar productos
            filtrarProductos(filtro);
        });
    }

    /**
     * Filtrar productos vía AJAX
     */
    function filtrarProductos(filtro) {
        if (!saicoData || !saicoData.ajaxUrl) {
            return;
        }

        cargando = true;

        // Mostrar indicador de carga
        const $grid = $('#saicoProductosGrid');
        $grid.addClass('filtrando');

        $.ajax({
            url: saicoData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'filtrar_productos',
                nonce: saicoData.nonce,
                filtro: filtro,
                pagina: 1,
                posts_per_page: saicoData.productsPorPagina || 12
            },
            success: function(response) {
                if (response.success) {
                    // Reemplazar contenido
                    $grid.html(response.data.html);
                    $grid.removeClass('filtrando');

                    // Actualizar botón "Cargar más"
                    const $btnCargarMas = $('#saicoCargarMas');
                    const maxPages = response.data.max_pages || 1;

                    if (maxPages > 1) {
                        $btnCargarMas.data('max', maxPages);
                        $btnCargarMas.data('pagina', 1);
                        $btnCargarMas.removeClass('oculto');
                        $('.saico-cargar-mas-contenedor').show();
                    } else {
                        $btnCargarMas.addClass('oculto');
                        $('.saico-cargar-mas-contenedor').hide();
                    }

                    // Reiniciar animaciones
                    initAnimacionesEntrada();

                    // Scroll suave hacia los productos
                    $('html, body').animate({
                        scrollTop: $grid.offset().top - 100
                    }, 500);
                } else {
                    mostrarMensaje('Error al cargar productos', 'error');
                }
            },
            error: function(xhr, status, error) {
                mostrarMensaje('Error de conexión. Por favor, intenta de nuevo.', 'error');
                $grid.removeClass('filtrando');
            },
            complete: function() {
                cargando = false;
            }
        });
    }

    /**
     * Botón Cargar Más
     */
    function initCargarMas() {
        $('#saicoCargarMas').on('click', function(e) {
            e.preventDefault();

            if (cargando) return;

            const $btn = $(this);
            const paginaSiguiente = parseInt($btn.data('pagina')) + 1;
            const maxPages = parseInt($btn.data('max'));

            if (paginaSiguiente > maxPages) {
                $btn.addClass('oculto');
                return;
            }

            cargarMasProductos(paginaSiguiente, $btn);
        });
    }

    /**
     * Cargar más productos vía AJAX
     */
    function cargarMasProductos(pagina, $btn) {
        if (!saicoData || !saicoData.ajaxUrl) return;

        cargando = true;
        $btn.addClass('cargando');

        $.ajax({
            url: saicoData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'filtrar_productos',
                nonce: saicoData.nonce,
                filtro: filtroActual,
                pagina: pagina,
                posts_per_page: saicoData.productsPorPagina || 12
            },
            success: function(response) {
                if (response.success) {
                    // Agregar nuevos productos al grid
                    const $grid = $('#saicoProductosGrid');
                    $grid.append(response.data.html);

                    // Actualizar página actual
                    paginaActual = pagina;
                    $btn.data('pagina', pagina);

                    // Ocultar botón si llegamos al final
                    const maxPages = parseInt($btn.data('max'));
                    if (pagina >= maxPages) {
                        $btn.addClass('oculto');
                    }

                    // Animar nuevos productos
                    initAnimacionesEntrada();
                } else {
                    mostrarMensaje('No se pudieron cargar más productos', 'error');
                }
            },
            error: function(xhr, status, error) {
                mostrarMensaje('Error de conexión', 'error');
            },
            complete: function() {
                cargando = false;
                $btn.removeClass('cargando');
            }
        });
    }

    /**
     * Infinite Scroll (opcional)
     */
    function initInfiniteScroll() {
        // Solo activar si existe el trigger
        if (!$('.saico-infinite-scroll-trigger').length) return;

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting && !cargando) {
                    const $btn = $('#saicoCargarMas');
                    const paginaSiguiente = parseInt($btn.data('pagina')) + 1;
                    const maxPages = parseInt($btn.data('max'));

                    if (paginaSiguiente <= maxPages) {
                        cargarMasProductos(paginaSiguiente, $btn);
                    }
                }
            });
        }, {
            rootMargin: '100px'
        });

        $('.saico-infinite-scroll-trigger').each(function() {
            observer.observe(this);
        });
    }

    /**
     * Animaciones de entrada para productos
     */
    function initAnimacionesEntrada() {
        const $productos = $('.producto-card:not(.animado)');

        $productos.each(function(index) {
            const $card = $(this);

            // Agregar clase para animar
            setTimeout(function() {
                $card.addClass('fade-in animado');
            }, index * 50); // Delay escalonado
        });
    }

    /**
     * Mostrar mensaje toast
     */
    function mostrarMensaje(mensaje, tipo = 'info') {
        // Eliminar mensaje anterior si existe
        $('.saico-toast').remove();

        const $toast = $('<div>', {
            class: 'saico-toast saico-toast-' + tipo,
            text: mensaje
        });

        $('body').append($toast);

        // Mostrar con animación
        setTimeout(function() {
            $toast.addClass('mostrar');
        }, 10);

        // Ocultar después de 3 segundos
        setTimeout(function() {
            $toast.removeClass('mostrar');
            setTimeout(function() {
                $toast.remove();
            }, 300);
        }, 3000);
    }

    /**
     * Animaciones al hacer scroll (opcional)
     */
    function initAnimacionesScroll() {
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, {
            threshold: 0.1
        });

        document.querySelectorAll('.categoria-card').forEach(function(el) {
            observer.observe(el);
        });
    }

    /**
     * Contador animado para stats del hero
     */
    function animarContadores() {
        $('.stat-numero').each(function() {
            const $numero = $(this);
            const valorFinal = $numero.text();

            // Solo animar números
            if (!/^\d+$/.test(valorFinal)) return;

            const valorNumerico = parseInt(valorFinal);
            let valorActual = 0;
            const duracion = 2000; // 2 segundos
            const incremento = valorNumerico / (duracion / 16); // 60 FPS

            const intervalo = setInterval(function() {
                valorActual += incremento;

                if (valorActual >= valorNumerico) {
                    valorActual = valorNumerico;
                    clearInterval(intervalo);
                }

                $numero.text(Math.floor(valorActual));
            }, 16);
        });
    }

    /**
     * Iniciar animaciones cuando el hero sea visible
     */
    $(window).on('load', function() {
        if ($('.saico-hero').length) {
            setTimeout(function() {
                animarContadores();
            }, 500);
        }

        initAnimacionesScroll();
    });

    /**
     * Prevenir clicks múltiples en botones
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    /**
     * Actualizar contador de productos visibles
     */
    function actualizarContadorProductos() {
        const total = $('.producto-card').length;
        if ($('.productos-contador').length) {
            $('.productos-contador').text(total + ' productos');
        }
    }

    /**
     * Resetear filtros (opcional)
     */
    window.saicoResetFiltros = function() {
        $('.filtro-btn[data-filtro="todos"]').trigger('click');
    };

    /**
     * Exponer funciones globales
     */
    window.saicoFiltrarProductos = filtrarProductos;
    window.saicoMostrarMensaje = mostrarMensaje;

})(jQuery);
