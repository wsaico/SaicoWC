/**
 * Social - Saico WC
 * Funcionalidad de acciones sociales (like, compartir)
 */

(function($) {
    'use strict';

    /**
     * Compartir producto (simple con Web Share API o fallback)
     */
    window.saicoCompartirProducto = function(url, titulo) {
        if (navigator.share) {
            navigator.share({
                title: titulo,
                url: url
            }).catch(function(error) {
                copiarAlPortapapeles(url);
            });
        } else {
            copiarAlPortapapeles(url);
        }
    };

    /**
     * Compartir en redes sociales
     */
    window.saicoCompartir = function(tipo, url, titulo) {
        url = url || window.location.href;
        titulo = titulo || document.title;

        const anchoVentana = 600;
        const altoVentana = 400;
        const left = (screen.width / 2) - (anchoVentana / 2);
        const top = (screen.height / 2) - (altoVentana / 2);
        const opciones = `width=${anchoVentana},height=${altoVentana},left=${left},top=${top},toolbar=no,menubar=no,scrollbars=yes,resizable=yes`;

        let urlCompartir = '';

        switch (tipo) {
            case 'facebook':
                urlCompartir = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
                break;

            case 'twitter':
                urlCompartir = `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(titulo)}`;
                break;

            case 'linkedin':
                urlCompartir = `https://www.linkedin.com/shareArticle?mini=true&url=${encodeURIComponent(url)}&title=${encodeURIComponent(titulo)}`;
                break;

            case 'whatsapp':
                urlCompartir = `https://api.whatsapp.com/send?text=${encodeURIComponent(titulo + ' ' + url)}`;
                break;

            case 'telegram':
                urlCompartir = `https://t.me/share/url?url=${encodeURIComponent(url)}&text=${encodeURIComponent(titulo)}`;
                break;

            case 'email':
                urlCompartir = `mailto:?subject=${encodeURIComponent(titulo)}&body=${encodeURIComponent(url)}`;
                break;

            case 'copiar':
                copiarAlPortapapeles(url);
                return;

            default:
                // Usar Web Share API si está disponible
                if (navigator.share) {
                    navigator.share({
                        title: titulo,
                        url: url
                    }).catch(function(error) {
                        copiarAlPortapapeles(url);
                    });
                    return;
                } else {
                    copiarAlPortapapeles(url);
                    return;
                }
        }

        if (urlCompartir) {
            window.open(urlCompartir, 'compartir', opciones);
        }
    };

    /**
     * Copiar al portapapeles
     */
    function copiarAlPortapapeles(texto) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(texto)
                .then(function() {
                    mostrarMensaje('Enlace copiado al portapapeles', 'success');
                })
                .catch(function(err) {
                    copiarConFallback(texto);
                });
        } else {
            copiarConFallback(texto);
        }
    }

    /**
     * Fallback para copiar al portapapeles
     */
    function copiarConFallback(texto) {
        const $temp = $('<textarea>');
        $temp.val(texto);
        $temp.css({
            position: 'fixed',
            top: -1000,
            left: -1000
        });

        $('body').append($temp);
        $temp.select();

        try {
            const exitoso = document.execCommand('copy');
            if (exitoso) {
                mostrarMensaje('Enlace copiado al portapapeles', 'success');
            } else {
                mostrarMensaje('No se pudo copiar el enlace', 'error');
            }
        } catch (err) {
            mostrarMensaje('No se pudo copiar el enlace', 'error');
        }

        $temp.remove();
    }

    /**
     * Toggle like con animación
     */
    window.saicoToggleLike = function(e, btn) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        const $btn = $(btn);
        const productoId = $btn.data('producto-id');

        if (!productoId || !saicoData || !saicoData.ajaxUrl) {
            return;
        }

        // Desactivar botón temporalmente
        $btn.prop('disabled', true);

        $.ajax({
            url: saicoData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'toggle_like',
                nonce: saicoData.nonce,
                producto_id: productoId
            },
            success: function(response) {
                if (response.success) {
                    const liked = response.data.liked;
                    const likes = response.data.likes;

                    // Actualizar UI
                    $btn.toggleClass('liked', liked);

                    // Actualizar contador (buscar en wrapper parent)
                    const $contador = $btn.closest('.saico-like-wrapper').find('.saico-like-count');
                    if ($contador.length) {
                        $contador.text(likes);

                        // Animación de pulso
                        $contador.addClass('pulse');
                        setTimeout(function() {
                            $contador.removeClass('pulse');
                        }, 300);
                    }

                    // Animación del icono
                    const $icono = $btn.find('svg, i');
                    $icono.addClass('bounce');
                    setTimeout(function() {
                        $icono.removeClass('bounce');
                    }, 500);

                    // Mensaje
                    const mensaje = liked ? 'Agregado a favoritos' : 'Removido de favoritos';
                    mostrarMensaje(mensaje, liked ? 'success' : 'info');
                } else {
                    mostrarMensaje('Error al procesar la acción', 'error');
                }
            },
            error: function(xhr, status, error) {
                mostrarMensaje('Error de conexión', 'error');
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });
    };

    /**
     * Mostrar mensaje toast
     */
    function mostrarMensaje(mensaje, tipo = 'info') {
        // Remover mensajes anteriores
        $('.saico-toast').remove();

        // Crear toast
        const $toast = $('<div>', {
            class: `saico-toast saico-toast-${tipo}`,
            html: `
                <div class="toast-contenido">
                    <svg class="toast-icono" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        ${getIconoTipo(tipo)}
                    </svg>
                    <span class="toast-mensaje">${mensaje}</span>
                </div>
            `
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

        // Click para cerrar
        $toast.on('click', function() {
            $(this).removeClass('mostrar');
            setTimeout(function() {
                $toast.remove();
            }, 300);
        });
    }

    /**
     * Obtener icono según tipo de mensaje
     */
    function getIconoTipo(tipo) {
        switch (tipo) {
            case 'success':
                return '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline>';
            case 'error':
                return '<circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line>';
            case 'warning':
                return '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line>';
            default:
                return '<circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line>';
        }
    }

    /**
     * Inicializar event listeners
     */
    $(document).ready(function() {
        // Event delegation para likes
        $(document).on('click', '.like-btn, .saico-like-btn, .heart-like-btn', function(e) {
            saicoToggleLike(e, this);
        });

        // Event delegation para compartir
        $(document).on('click', '[data-compartir]', function(e) {
            e.preventDefault();
            const tipo = $(this).data('compartir');
            const url = $(this).data('url') || window.location.href;
            const titulo = $(this).data('titulo') || document.title;
            saicoCompartir(tipo, url, titulo);
        });
    });

    /**
     * Exponer funciones globales
     */
    window.saicoMostrarMensaje = mostrarMensaje;

})(jQuery);

/**
 * Estilos CSS necesarios para social.js
 * (Agregar a un archivo CSS o inline)
 */
const estilosSocial = `
/* Toast notifications */
.saico-toast {
    position: fixed;
    bottom: var(--saico-spacing-2xl);
    right: var(--saico-spacing-lg);
    min-width: 300px;
    padding: var(--saico-spacing-md) var(--saico-spacing-lg);
    background-color: var(--saico-bg-oscuro);
    color: white;
    border-radius: var(--saico-radius-lg);
    box-shadow: var(--saico-shadow-2xl);
    z-index: var(--saico-z-tooltip);
    opacity: 0;
    transform: translateX(400px);
    transition: all var(--saico-transition-base);
    cursor: pointer;
}

.saico-toast.mostrar {
    opacity: 1;
    transform: translateX(0);
}

.toast-contenido {
    display: flex;
    align-items: center;
    gap: var(--saico-spacing-md);
}

.toast-icono {
    flex-shrink: 0;
    stroke-width: 2px;
}

.toast-mensaje {
    font-size: var(--saico-font-sm);
    font-weight: var(--saico-font-weight-medium);
}

.saico-toast-success {
    background-color: var(--saico-exito);
}

.saico-toast-error {
    background-color: var(--saico-error);
}

.saico-toast-warning {
    background-color: var(--saico-advertencia);
}

.saico-toast-info {
    background-color: var(--saico-info);
}

/* Animaciones */
.pulse {
    animation: pulse 0.3s ease-in-out;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.2); }
}

.bounce {
    animation: bounce 0.5s ease-in-out;
}

@keyframes bounce {
    0%, 100% { transform: scale(1); }
    25% { transform: scale(1.3); }
    50% { transform: scale(0.9); }
    75% { transform: scale(1.1); }
}

/* Responsive */
@media (max-width: 480px) {
    .saico-toast {
        right: var(--saico-spacing-md);
        left: var(--saico-spacing-md);
        min-width: auto;
    }
}
`;
