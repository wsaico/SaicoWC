/**
 * JavaScript para Cards Minimalistas - Saico WC
 * Maneja like, audio, compartir y lazy loading
 */

(function($) {
    'use strict';

    /**
     * ========================================================================
     * SISTEMA DE LIKES
     * ========================================================================
     */
    window.saicoToggleLike = function(e, btn) {
        e.preventDefault();
        e.stopPropagation();

        const $btn = $(btn);
        const productoId = $btn.data('producto-id');

        if (!productoId) {
            return;
        }

        // Animación de loading
        $btn.addClass('loading').prop('disabled', true);

        $.ajax({
            url: saicoData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'toggle_like',
                producto_id: productoId,
                nonce: saicoData.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Actualizar estado visual
                    if (response.data.tiene_like) {
                        $btn.addClass('activo');
                    } else {
                        $btn.removeClass('activo');
                    }

                    // Actualizar contador
                    const $contador = $btn.find('.like-contador');
                    if ($contador.length) {
                        $contador.text(response.data.likes);
                    }

                    // Animación de corazón
                    $btn.addClass('pulse');
                    setTimeout(function() {
                        $btn.removeClass('pulse');
                    }, 600);
                } else {
                    mostrarMensaje('Error al procesar like', 'error');
                }
            },
            error: function(xhr, status, error) {
                mostrarMensaje('Error de conexión', 'error');
            },
            complete: function() {
                $btn.removeClass('loading').prop('disabled', false);
            }
        });
    };

    /**
     * ========================================================================
     * REPRODUCTOR DE AUDIO
     * ========================================================================
     */
    window.saicoReproducirAudio = function(audioUrl, btn) {
        const $btn = $(btn);

        // Si hay un audio reproduciéndose, detenerlo
        if (window.currentAudio && !window.currentAudio.paused) {
            window.currentAudio.pause();
            $('.audio-reproducir-btn').removeClass('reproduciendo');
        }

        // Si este botón ya está reproduciendo, pausar
        if ($btn.hasClass('reproduciendo')) {
            if (window.currentAudio) {
                window.currentAudio.pause();
            }
            $btn.removeClass('reproduciendo');
            return;
        }

        // Crear o reutilizar elemento de audio
        if (!window.currentAudio) {
            window.currentAudio = new Audio();
        }

        window.currentAudio.src = audioUrl;
        window.currentAudio.play();

        $btn.addClass('reproduciendo');

        // Event listeners
        window.currentAudio.onended = function() {
            $btn.removeClass('reproduciendo');
        };

        window.currentAudio.onerror = function() {
            $btn.removeClass('reproduciendo');
            mostrarMensaje('Error al cargar el audio', 'error');
        };
    };

    /**
     * ========================================================================
     * COMPARTIR PRODUCTO
     * ========================================================================
     */
    window.saicoCompartir = function(e, url, titulo) {
        e.preventDefault();
        e.stopPropagation();

        // Si el navegador soporta Web Share API (móvil)
        if (navigator.share) {
            navigator.share({
                title: titulo,
                url: url
            }).catch(function(error) {
                // Error al compartir - fallback ya manejado
            });
        } else {
            // Fallback: copiar al portapapeles
            copiarAlPortapapeles(url);
            mostrarMensaje('Link copiado al portapapeles', 'exito');
        }
    };

    /**
     * Copiar texto al portapapeles
     */
    function copiarAlPortapapeles(texto) {
        const $temp = $('<input>');
        $('body').append($temp);
        $temp.val(texto).select();
        document.execCommand('copy');
        $temp.remove();
    }

    /**
     * ========================================================================
     * AGREGAR AL CARRITO
     * ========================================================================
     */
    window.saicoAgregarCarrito = function(e, productoNombre) {
        e.preventDefault();
        const $btn = $(e.target).closest('.btn-agregar-carrito');

        // Animación
        $btn.addClass('agregado');

        setTimeout(function() {
            $btn.removeClass('agregado');
        }, 2000);

        // Mostrar mensaje
        mostrarMensaje(`${productoNombre} agregado al carrito`, 'exito');
    };

    /**
     * ========================================================================
     * LAZY LOADING DE IMÁGENES
     * ========================================================================
     */
    function initLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        const src = img.dataset.src;

                        if (src) {
                            img.src = src;
                            img.classList.add('loaded');
                            observer.unobserve(img);
                        }
                    }
                });
            });

            document.querySelectorAll('.producto-imagen img[data-src]').forEach(function(img) {
                imageObserver.observe(img);
            });
        } else {
            // Fallback para navegadores que no soportan IntersectionObserver
            $('.producto-imagen img[data-src]').each(function() {
                const $img = $(this);
                $img.attr('src', $img.data('src'));
            });
        }
    }

    /**
     * ========================================================================
     * MOSTRAR MENSAJES/NOTIFICACIONES
     * ========================================================================
     */
    function mostrarMensaje(mensaje, tipo) {
        // Crear o reutilizar contenedor de notificaciones
        let $notificaciones = $('#saico-notificaciones');

        if (!$notificaciones.length) {
            $notificaciones = $('<div id="saico-notificaciones"></div>');
            $('body').append($notificaciones);
        }

        // Crear notificación
        const $notificacion = $(`
            <div class="saico-notificacion saico-notificacion-${tipo}">
                <span>${mensaje}</span>
            </div>
        `);

        $notificaciones.append($notificacion);

        // Animación de entrada
        setTimeout(function() {
            $notificacion.addClass('visible');
        }, 10);

        // Auto-remover después de 3 segundos
        setTimeout(function() {
            $notificacion.removeClass('visible');
            setTimeout(function() {
                $notificacion.remove();
            }, 300);
        }, 3000);
    }

    /**
     * ========================================================================
     * ANIMACIONES AL HACER HOVER
     * ========================================================================
     */
    $('.producto-card').on('mouseenter', function() {
        $(this).find('.producto-imagen img').css('transform', 'scale(1.1)');
    }).on('mouseleave', function() {
        $(this).find('.producto-imagen img').css('transform', 'scale(1)');
    });

    /**
     * ========================================================================
     * KEYBOARD NAVIGATION
     * ========================================================================
     */
    $('.producto-card').on('keydown', function(e) {
        // Enter o espacio para abrir producto
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            const link = $(this).find('.producto-titulo a').attr('href');
            if (link) {
                window.location.href = link;
            }
        }
    });

    /**
     * ========================================================================
     * INICIALIZACIÓN
     * ========================================================================
     */
    $(document).ready(function() {
        // Iniciar lazy loading
        initLazyLoading();

        // Hacer cards clickeables
        $('.producto-card').on('click', function(e) {
            // Verificar si el click fue en un botón o elemento interactivo
            const $target = $(e.target);
            const isButton = $target.closest('.btn-icono, .audio-reproducir-btn, .audio-play-btn, .woo-product-play-button, .categoria-badge, button, svg, .midi-grid-container').length > 0;

            // Si es un botón, NO hacer nada (dejar que otros handlers lo manejen)
            if (isButton) {
                return; // NO usar "return false" porque bloquea otros eventos
            }

            // Si no es un botón, navegar al producto
            const link = $(this).find('.producto-titulo a').attr('href');
            if (link) {
                window.location.href = link;
            }
        });

        // CSS para notificaciones (si no existe)
        if (!$('#saico-notificaciones-styles').length) {
            $('head').append(`
                <style id="saico-notificaciones-styles">
                    #saico-notificaciones {
                        position: fixed;
                        top: 20px;
                        right: 20px;
                        z-index: 10000;
                        pointer-events: none;
                    }
                    .saico-notificacion {
                        background: white;
                        padding: 16px 24px;
                        border-radius: 8px;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                        margin-bottom: 12px;
                        opacity: 0;
                        transform: translateX(100px);
                        transition: all 0.3s ease;
                        pointer-events: auto;
                    }
                    .saico-notificacion.visible {
                        opacity: 1;
                        transform: translateX(0);
                    }
                    .saico-notificacion-exito {
                        border-left: 4px solid #10b981;
                        color: #10b981;
                    }
                    .saico-notificacion-error {
                        border-left: 4px solid #ef4444;
                        color: #ef4444;
                    }
                    .saico-notificacion-info {
                        border-left: 4px solid #3b82f6;
                        color: #3b82f6;
                    }
                    @keyframes pulse {
                        0%, 100% { transform: scale(1); }
                        50% { transform: scale(1.2); }
                    }
                    .btn-icono.pulse {
                        animation: pulse 0.6s ease;
                    }
                    .btn-icono.loading {
                        opacity: 0.6;
                        pointer-events: none;
                    }
                </style>
            `);
        }

    });

    /**
     * ========================================================================
     * RECARGA DINÁMICA (para infinite scroll o AJAX)
     * ========================================================================
     */
    window.saicoReinicializarCards = function() {
        initLazyLoading();
    };

})(jQuery);
