/**
 * Producto Single - Saico WC
 * Funcionalidad de la página individual del producto
 */

(function($) {
    'use strict';

    /**
     * Inicialización
     */
    $(document).ready(function() {
        initTabs();
        initGaleria();
        initMostrarMas();
        initScrollToReviews();
    });

    /**
     * Tabs (Descripción y Reviews)
     */
    function initTabs() {
        $('.saico-tab-btn').on('click', function() {
            const $btn = $(this);
            const tabId = $btn.data('tab');

            // Actualizar botones
            $('.saico-tab-btn').removeClass('activo');
            $btn.addClass('activo');

            // Actualizar paneles
            $('.saico-tab-panel').removeClass('activo');
            $('#' + tabId).addClass('activo');

            // Scroll suave al contenido del tab
            $('html, body').animate({
                scrollTop: $('.saico-producto-tabs').offset().top - 100
            }, 300);
        });
    }

    /**
     * Galería de imágenes
     */
    function initGaleria() {
        const $imagenPrincipal = $('.saico-producto-galeria > img');
        const $miniaturas = $('.saico-producto-galeria-miniaturas img');

        if (!$imagenPrincipal.length || !$miniaturas.length) {
            return;
        }

        // Click en miniaturas
        $miniaturas.on('click', function() {
            const $miniatura = $(this);
            const nuevaSrc = $miniatura.attr('src').replace('-150x150', '-large');

            // Actualizar imagen principal
            $imagenPrincipal.attr('src', nuevaSrc);

            // Actualizar clase activa
            $miniaturas.removeClass('activa');
            $miniatura.addClass('activa');

            // Animación de fade
            $imagenPrincipal.css('opacity', '0');
            setTimeout(function() {
                $imagenPrincipal.css('opacity', '1');
            }, 150);
        });

        // Marcar primera miniatura como activa
        $miniaturas.first().addClass('activa');

        // Lightbox opcional (si se implementa)
        $imagenPrincipal.on('click', function() {
            // abrirLightbox($(this).attr('src'));
        });
    }

    /**
     * Botón "Mostrar más" en descripción larga
     */
    function initMostrarMas() {
        // Usar selector más específico para solo la descripción del producto
        const $descripcion = $('#descripcion .producto-descripcion');
        const alturaMaxima = 400;

        if ($descripcion.length && $descripcion.height() > alturaMaxima) {
            // Verificar que no se haya inicializado ya
            if ($descripcion.hasClass('mostrar-mas-init')) {
                return;
            }

            $descripcion.addClass('mostrar-mas-init');

            $descripcion.css({
                'max-height': alturaMaxima + 'px',
                'overflow': 'hidden',
                'position': 'relative'
            });

            // Agregar degradado
            const $degradado = $('<div>', {
                class: 'descripcion-degradado',
                css: {
                    'position': 'absolute',
                    'bottom': 0,
                    'left': 0,
                    'right': 0,
                    'height': '100px',
                    'background': 'linear-gradient(to bottom, transparent, var(--saico-bg-primario))',
                    'pointer-events': 'none'
                }
            });

            $descripcion.append($degradado);

            // Botón mostrar más
            const $btnMostrarMas = $('<button>', {
                class: 'saico-btn saico-btn-outline saico-mostrar-mas-btn',
                text: 'Mostrar más',
                css: {
                    'margin-top': 'var(--saico-spacing-md)',
                    'display': 'block',
                    'margin-left': 'auto',
                    'margin-right': 'auto'
                }
            });

            $descripcion.after($btnMostrarMas);

            // Toggle
            $btnMostrarMas.on('click', function() {
                if ($descripcion.css('max-height') === alturaMaxima + 'px') {
                    $descripcion.css('max-height', 'none');
                    $degradado.hide();
                    $btnMostrarMas.text('Mostrar menos');
                } else {
                    $descripcion.css('max-height', alturaMaxima + 'px');
                    $degradado.show();
                    $btnMostrarMas.text('Mostrar más');

                    // Scroll a la descripción
                    $('html, body').animate({
                        scrollTop: $descripcion.offset().top - 100
                    }, 300);
                }
            });
        }
    }

    /**
     * Scroll automático a reviews al hacer clic en rating
     */
    function initScrollToReviews() {
        $('.woocommerce-review-link, .star-rating').on('click', function(e) {
            e.preventDefault();

            // Activar tab de reviews
            $('.saico-tab-btn[data-tab="reviews"]').trigger('click');

            // Scroll a la sección
            setTimeout(function() {
                $('html, body').animate({
                    scrollTop: $('#reviews').offset().top - 100
                }, 500);
            }, 100);
        });
    }

    /**
     * Scroll suave a elementos con ancla
     */
    $('a[href^="#"]').on('click', function(e) {
        const target = $(this).attr('href');

        if ($(target).length) {
            e.preventDefault();

            $('html, body').animate({
                scrollTop: $(target).offset().top - 100
            }, 500);
        }
    });

    /**
     * Contador de cantidad con validación
     */
    $('.quantity input[type="number"]').on('change', function() {
        const $input = $(this);
        const min = parseInt($input.attr('min')) || 1;
        const max = parseInt($input.attr('max')) || 9999;
        let valor = parseInt($input.val());

        if (valor < min) {
            $input.val(min);
        } else if (valor > max) {
            $input.val(max);
            mostrarMensaje('Cantidad máxima disponible: ' + max, 'info');
        }
    });

    /**
     * Agregar al carrito con animación
     */
    $('.single_add_to_cart_button').on('click', function() {
        const $btn = $(this);

        // Agregar clase de carga
        $btn.addClass('loading');

        // Simular animación de producto volando al carrito
        // (esto requiere coordenadas del botón y del icono del carrito)
        setTimeout(function() {
            animarAgregarCarrito();
        }, 500);
    });

    /**
     * Animación de producto agregado al carrito
     */
    function animarAgregarCarrito() {
        const $imagenProducto = $('.saico-producto-galeria img').first();
        const $carrito = $('.saico-header-carrito');

        if (!$imagenProducto.length || !$carrito.length) {
            return;
        }

        // Clonar imagen
        const $imagenClonada = $imagenProducto.clone();
        const posImagen = $imagenProducto.offset();
        const posCarrito = $carrito.offset();

        $imagenClonada.css({
            position: 'fixed',
            top: posImagen.top,
            left: posImagen.left,
            width: $imagenProducto.width(),
            height: $imagenProducto.height(),
            zIndex: 9999,
            opacity: 1,
            transition: 'all 0.8s cubic-bezier(0.4, 0.0, 0.2, 1)',
            pointerEvents: 'none'
        });

        $('body').append($imagenClonada);

        // Animar
        setTimeout(function() {
            $imagenClonada.css({
                top: posCarrito.top,
                left: posCarrito.left,
                width: '30px',
                height: '30px',
                opacity: 0
            });
        }, 10);

        // Remover después de la animación
        setTimeout(function() {
            $imagenClonada.remove();

            // Animar contador del carrito
            $carrito.find('.carrito-contador').addClass('pulse');
            setTimeout(function() {
                $carrito.find('.carrito-contador').removeClass('pulse');
            }, 600);
        }, 800);
    }

    /**
     * Copiar enlace del producto
     */
    window.saicoCompartirProducto = function(tipo) {
        const url = window.location.href;
        const titulo = document.title;

        switch (tipo) {
            case 'facebook':
                window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(url), '_blank', 'width=600,height=400');
                break;
            case 'twitter':
                window.open('https://twitter.com/intent/tweet?url=' + encodeURIComponent(url) + '&text=' + encodeURIComponent(titulo), '_blank', 'width=600,height=400');
                break;
            case 'linkedin':
                window.open('https://www.linkedin.com/shareArticle?mini=true&url=' + encodeURIComponent(url) + '&title=' + encodeURIComponent(titulo), '_blank', 'width=600,height=400');
                break;
            case 'copiar':
                copiarAlPortapapeles(url);
                break;
            default:
                // Web Share API
                if (navigator.share) {
                    navigator.share({
                        title: titulo,
                        url: url
                    }).catch(function(error) {
                        // Error al compartir - fallback ya manejado
                    });
                } else {
                    copiarAlPortapapeles(url);
                }
        }
    };

    /**
     * Abrir modal de compartir
     */
    window.saicoAbrirModalCompartir = function() {
        saicoAbrirModal('saico-share-modal');
    };

    /**
     * Copiar al portapapeles
     */
    function copiarAlPortapapeles(texto) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(texto).then(function() {
                mostrarMensaje('Enlace copiado al portapapeles', 'success');
            }).catch(function(err) {
                // Error al copiar - fallback ya manejado
            });
        } else {
            // Fallback
            const $temp = $('<input>');
            $('body').append($temp);
            $temp.val(texto).select();
            document.execCommand('copy');
            $temp.remove();
            mostrarMensaje('Enlace copiado al portapapeles', 'success');
        }
    }

    /**
     * Mostrar mensaje toast
     */
    function mostrarMensaje(mensaje, tipo = 'info') {
        $('.saico-toast').remove();

        const $toast = $('<div>', {
            class: 'saico-toast saico-toast-' + tipo,
            text: mensaje
        });

        $('body').append($toast);

        setTimeout(function() {
            $toast.addClass('mostrar');
        }, 10);

        setTimeout(function() {
            $toast.removeClass('mostrar');
            setTimeout(function() {
                $toast.remove();
            }, 300);
        }, 3000);
    }

    /**
     * Lazy load de imágenes de galería
     */
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const $img = $(entry.target);
                    const src = $img.data('src');
                    if (src) {
                        $img.attr('src', src);
                        $img.removeAttr('data-src');
                        observer.unobserve(entry.target);
                    }
                }
            });
        });

        $('.saico-producto-galeria-miniaturas img[data-src]').each(function() {
            observer.observe(this);
        });
    }

    /**
     * Agregar animación de pulso al contador del carrito
     */
    $(document.body).on('added_to_cart', function() {
        $('.carrito-contador').addClass('pulse');
        setTimeout(function() {
            $('.carrito-contador').removeClass('pulse');
        }, 600);
    });

    /**
     * =============================
     * BOTÓN ANIMADO DE DESCARGA
     * =============================
     */
    $(document).ready(function() {
        const $animatedButton = $('#saico-animated-download-button');
        const $progressBar = $('.button-progress');
        const $countdownNumber = $('.countdown-number');
        const $downloadContainer = $('.saico-real-download-container');


        if ($animatedButton.length) {
            // Ocultar contenedor de descarga al inicio
            $downloadContainer.hide();


            $animatedButton.on('click', function(e) {
                e.preventDefault();

                if ($(this).hasClass('loading')) {
                    return;
                }

                const loadingTime = (typeof saicoData !== 'undefined' && saicoData.animatedButtonTime) ? saicoData.animatedButtonTime : 10;
                const timeInSeconds = Math.max(1, parseFloat(loadingTime));


                $(this).addClass('loading');
                $countdownNumber.show().text(timeInSeconds.toFixed(1));

                $progressBar.css({
                    'width': '0%',
                    'transition': 'width ' + timeInSeconds + 's linear'
                });

                // Forzar reflow
                $progressBar[0].offsetWidth;
                $progressBar.css('width', '100%');

                // Contador regresivo
                let currentValue = timeInSeconds * 10;
                const countdownInterval = setInterval(() => {
                    currentValue--;
                    if (currentValue >= 0) {
                        $countdownNumber.text((currentValue / 10).toFixed(1));
                    } else {
                        clearInterval(countdownInterval);
                    }
                }, 100);

                $(this).prop('disabled', true).css('cursor', 'wait');

                setTimeout(() => {
                    $animatedButton.css('cursor', 'default');
                    $animatedButton.fadeOut(300, function() {
                        $downloadContainer.fadeIn(300);
                    });

                    // Incrementar contador de vistas si existe el endpoint
                    if (typeof ajaxurl !== 'undefined') {
                        const productId = $('body').find('[data-product-id]').data('product-id');
                        if (productId) {
                            $.ajax({
                                url: ajaxurl,
                                type: 'POST',
                                data: {
                                    action: 'saico_increment_view',
                                    product_id: productId
                                }
                            });
                        }
                    }
                }, timeInSeconds * 1000);
            });
        }
    });

})(jQuery);
