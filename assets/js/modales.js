/**
 * Modales - Saico WC
 * Sistema de modales reutilizable
 */

(function($) {
    'use strict';

    /**
     * Abrir modal
     */
    window.saicoAbrirModal = function(idModal) {
        const $modal = $('#' + idModal);

        if (!$modal.length) {
            return;
        }

        // Mostrar modal
        $modal.addClass('activo');

        // Prevenir scroll del body
        $('body').addClass('modal-abierto');

        // Focus trap
        activarFocusTrap($modal);

        // Trigger event
        $modal.trigger('modal:abierto');
    };

    /**
     * Cerrar modal
     */
    window.saicoCerrarModal = function(idModal) {
        const $modal = idModal ? $('#' + idModal) : $('.saico-modal.activo');

        if (!$modal.length) {
            return;
        }

        // Ocultar modal
        $modal.removeClass('activo');

        // Restaurar scroll del body si no hay más modales
        if ($('.saico-modal.activo').length === 0) {
            $('body').removeClass('modal-abierto');
        }

        // Desactivar focus trap
        desactivarFocusTrap($modal);

        // Trigger event
        $modal.trigger('modal:cerrado');
    };

    /**
     * Limpiar estado modal - Función de emergencia para móvil
     */
    window.saicoLimpiarEstadoModal = function() {
        // Cerrar todos los modales activos
        $('.saico-modal.activo').removeClass('activo');
        
        // Remover clase modal-abierto del body
        $('body').removeClass('modal-abierto');
        
        // Remover cualquier backdrop huérfano
        $('.modal-backdrop').remove();
        
        console.log('Estado modal limpiado');
    };

    /**
     * Toggle modal
     */
    window.saicoToggleModal = function(idModal) {
        const $modal = $('#' + idModal);

        if ($modal.hasClass('activo')) {
            saicoCerrarModal(idModal);
        } else {
            saicoAbrirModal(idModal);
        }
    };

    /**
     * Crear modal dinámicamente
     */
    window.saicoCrearModal = function(opciones) {
        const defaults = {
            id: 'modal-' + Date.now(),
            titulo: '',
            contenido: '',
            clasesAdicionales: '',
            mostrarCerrar: true,
            footer: null,
            ancho: 'md', // sm, md, lg, xl, full
            onAbrir: null,
            onCerrar: null
        };

        const config = $.extend({}, defaults, opciones);

        // Crear estructura del modal
        const $modal = $('<div>', {
            id: config.id,
            class: 'saico-modal ' + config.clasesAdicionales
        });

        const $backdrop = $('<div>', {
            class: 'modal-backdrop'
        });

        const $contenido = $('<div>', {
            class: 'modal-contenido modal-' + config.ancho
        });

        // Header
        if (config.titulo || config.mostrarCerrar) {
            const $header = $('<div>', {
                class: 'modal-header'
            });

            if (config.titulo) {
                $header.append($('<h3>', {
                    class: 'modal-titulo',
                    text: config.titulo
                }));
            }

            if (config.mostrarCerrar) {
                const $btnCerrar = $('<button>', {
                    class: 'modal-cerrar',
                    html: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>',
                    click: function() {
                        saicoCerrarModal(config.id);
                    }
                });

                $header.append($btnCerrar);
            }

            $contenido.append($header);
        }

        // Body
        const $body = $('<div>', {
            class: 'modal-body',
            html: config.contenido
        });
        $contenido.append($body);

        // Footer
        if (config.footer) {
            const $footer = $('<div>', {
                class: 'modal-footer',
                html: config.footer
            });
            $contenido.append($footer);
        }

        // Ensamblar
        $modal.append($backdrop);
        $modal.append($contenido);

        // Agregar al DOM
        $('body').append($modal);

        // Event listeners
        $backdrop.on('click', function() {
            saicoCerrarModal(config.id);
        });

        // Callbacks
        if (config.onAbrir) {
            $modal.on('modal:abierto', config.onAbrir);
        }

        if (config.onCerrar) {
            $modal.on('modal:cerrado', config.onCerrar);
        }

        return config.id;
    };

    /**
     * Modal de confirmación
     */
    window.saicoConfirmar = function(opciones) {
        const defaults = {
            titulo: '¿Estás seguro?',
            mensaje: '',
            textoConfirmar: 'Confirmar',
            textoCancelar: 'Cancelar',
            tipoConfirmar: 'primario', // primario, secundario, error
            onConfirmar: null,
            onCancelar: null
        };

        const config = $.extend({}, defaults, opciones);

        const footer = `
            <button class="saico-btn saico-btn-outline" id="btnCancelar">
                ${config.textoCancelar}
            </button>
            <button class="saico-btn saico-btn-${config.tipoConfirmar}" id="btnConfirmar">
                ${config.textoConfirmar}
            </button>
        `;

        const idModal = saicoCrearModal({
            titulo: config.titulo,
            contenido: '<p>' + config.mensaje + '</p>',
            footer: footer,
            ancho: 'sm',
            clasesAdicionales: 'modal-confirmacion',
            onCerrar: function() {
                // Remover modal del DOM después de cerrar
                setTimeout(function() {
                    $('#' + idModal).remove();
                }, 300);
            }
        });

        // Abrir modal
        saicoAbrirModal(idModal);

        // Event listeners
        $('#btnCancelar').on('click', function() {
            saicoCerrarModal(idModal);
            if (config.onCancelar) {
                config.onCancelar();
            }
        });

        $('#btnConfirmar').on('click', function() {
            saicoCerrarModal(idModal);
            if (config.onConfirmar) {
                config.onConfirmar();
            }
        });
    };

    /**
     * Modal de alerta
     */
    window.saicoAlerta = function(mensaje, tipo = 'info') {
        const iconos = {
            success: '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline>',
            error: '<circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line>',
            warning: '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line>',
            info: '<circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line>'
        };

        const contenido = `
            <div class="alerta-icono alerta-${tipo}">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    ${iconos[tipo] || iconos.info}
                </svg>
            </div>
            <p class="alerta-mensaje">${mensaje}</p>
        `;

        const footer = `
            <button class="saico-btn saico-btn-primario" id="btnAceptar">
                Aceptar
            </button>
        `;

        const idModal = saicoCrearModal({
            contenido: contenido,
            footer: footer,
            ancho: 'sm',
            clasesAdicionales: 'modal-alerta',
            onCerrar: function() {
                setTimeout(function() {
                    $('#' + idModal).remove();
                }, 300);
            }
        });

        saicoAbrirModal(idModal);

        $('#btnAceptar').on('click', function() {
            saicoCerrarModal(idModal);
        });
    };

    /**
     * Focus trap (accesibilidad)
     */
    function activarFocusTrap($modal) {
        const focusableElements = $modal.find('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
        const primerElemento = focusableElements.first();
        const ultimoElemento = focusableElements.last();

        $modal.data('focusTrap', function(e) {
            if (e.key === 'Tab') {
                if (e.shiftKey) {
                    if ($(document.activeElement).is(primerElemento)) {
                        e.preventDefault();
                        ultimoElemento.focus();
                    }
                } else {
                    if ($(document.activeElement).is(ultimoElemento)) {
                        e.preventDefault();
                        primerElemento.focus();
                    }
                }
            }
        });

        $(document).on('keydown', $modal.data('focusTrap'));

        // Focus al primer elemento
        setTimeout(function() {
            primerElemento.focus();
        }, 100);
    }

    /**
     * Desactivar focus trap
     */
    function desactivarFocusTrap($modal) {
        const focusTrap = $modal.data('focusTrap');
        if (focusTrap) {
            $(document).off('keydown', focusTrap);
        }
    }

    /**
     * Inicializar event listeners
     */
    $(document).ready(function() {
        // Cerrar modales con ESC
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                saicoCerrarModal();
            }
        });

        // Delegación de eventos para botones de abrir/cerrar
        $(document).on('click', '[data-modal-abrir]', function(e) {
            e.preventDefault();
            const idModal = $(this).data('modal-abrir');
            saicoAbrirModal(idModal);
        });

        $(document).on('click', '[data-modal-cerrar]', function(e) {
            e.preventDefault();
            const idModal = $(this).data('modal-cerrar') || null;
            saicoCerrarModal(idModal);
        });

        $(document).on('click', '[data-modal-toggle]', function(e) {
            e.preventDefault();
            const idModal = $(this).data('modal-toggle');
            saicoToggleModal(idModal);
        });

        // Cerrar modal al hacer clic en el backdrop
        $(document).on('click', '.modal-backdrop', function() {
            const $modal = $(this).closest('.saico-modal');
            const idModal = $modal.attr('id');
            saicoCerrarModal(idModal);
        });

        // Cerrar modal con botón X
        $(document).on('click', '.modal-cerrar', function() {
            const $modal = $(this).closest('.saico-modal');
            const idModal = $modal.attr('id');
            saicoCerrarModal(idModal);
        });

        // Detectar y corregir problemas de estado modal en móvil
        function verificarEstadoModal() {
            // Si el body tiene la clase modal-abierto pero no hay modales activos
            if ($('body').hasClass('modal-abierto') && $('.saico-modal.activo').length === 0) {
                console.warn('Estado modal inconsistente detectado - limpiando...');
                saicoLimpiarEstadoModal();
            }
            
            // Si hay backdrops huérfanos sin modal activo
            if ($('.modal-backdrop').length > 0 && $('.saico-modal.activo').length === 0) {
                console.warn('Backdrops huérfanos detectados - limpiando...');
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-abierto');
            }
        }

        // Verificar estado modal en móvil cada 2 segundos
        if (window.innerWidth <= 768) {
            setInterval(verificarEstadoModal, 2000);
        }

        // Verificar estado modal al cambiar orientación o redimensionar
        $(window).on('resize orientationchange', function() {
            setTimeout(verificarEstadoModal, 500);
        });

        // Verificar estado modal al cargar la página
        setTimeout(verificarEstadoModal, 1000);
        
        // Fix agresivo para móvil - limpiar cualquier estado problemático
        if (window.innerWidth <= 768) {
            setTimeout(function() {
                // Forzar limpieza completa
                $('body').removeClass('modal-abierto');
                $('.modal-backdrop').remove();
                $('.saico-modal').removeClass('activo');
                
                // Asegurar fondo correcto
                $('body').css('background-color', 'var(--saico-bg-primario)');
                $('body').css('overflow', 'visible');
                
                console.log('Limpieza agresiva móvil ejecutada');
            }, 2000);
        }
    });

})(jQuery);

/**
 * Estilos CSS necesarios para modales
 * (Agregar a modal-relacionados.css o crear modal.css)
 */
const estilosModales = `
/* Prevenir scroll cuando modal está abierto */
body.modal-abierto {
    overflow: hidden;
}

/* Modal base */
.saico-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: var(--saico-z-modal);
    display: none;
    align-items: center;
    justify-content: center;
    padding: var(--saico-spacing-lg);
}

.saico-modal.activo {
    display: flex;
}

/* Backdrop */
.modal-backdrop {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(4px);
    animation: fade-in 0.3s ease-out;
}

/* Contenido del modal */
.modal-contenido {
    position: relative;
    z-index: 1;
    background-color: var(--saico-bg-primario);
    border-radius: var(--saico-radius-xl);
    box-shadow: var(--saico-shadow-2xl);
    overflow: hidden;
    animation: slide-up 0.4s ease-out;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
}

/* Tamaños */
.modal-sm { max-width: 400px; width: 100%; }
.modal-md { max-width: 600px; width: 100%; }
.modal-lg { max-width: 900px; width: 100%; }
.modal-xl { max-width: 1200px; width: 100%; }
.modal-full { max-width: 95vw; width: 100%; }

/* Header */
.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--saico-spacing-lg);
    border-bottom: 2px solid var(--saico-borde-claro);
}

.modal-titulo {
    font-size: var(--saico-font-xl);
    font-weight: var(--saico-font-weight-bold);
    margin: 0;
}

.modal-cerrar {
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
}

.modal-cerrar:hover {
    background-color: var(--saico-bg-secundario);
}

/* Body */
.modal-body {
    padding: var(--saico-spacing-lg);
    overflow-y: auto;
    flex: 1;
}

/* Footer */
.modal-footer {
    display: flex;
    gap: var(--saico-spacing-md);
    justify-content: flex-end;
    padding: var(--saico-spacing-lg);
    border-top: 2px solid var(--saico-borde-claro);
}

/* Modal de alerta */
.modal-alerta .alerta-icono {
    text-align: center;
    margin-bottom: var(--saico-spacing-lg);
}

.modal-alerta .alerta-success { color: var(--saico-exito); }
.modal-alerta .alerta-error { color: var(--saico-error); }
.modal-alerta .alerta-warning { color: var(--saico-advertencia); }
.modal-alerta .alerta-info { color: var(--saico-info); }

.modal-alerta .alerta-mensaje {
    text-align: center;
    font-size: var(--saico-font-lg);
    color: var(--saico-texto-primario);
}

/* Animaciones */
@keyframes fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slide-up {
    from {
        opacity: 0;
        transform: translateY(50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .modal-contenido {
        max-height: 95vh;
        border-radius: var(--saico-radius-lg);
    }

    .modal-sm,
    .modal-md,
    .modal-lg,
    .modal-xl {
        max-width: 100%;
    }

    /* Prevenir fondos oscuros inesperados en móvil */
    body:not(.modal-abierto) {
        background-color: var(--saico-bg-primario) !important;
        overflow: visible !important;
    }

    /* Asegurar que los backdrops solo aparezcan con modales activos */
    .modal-backdrop {
        display: none;
    }

    .saico-modal.activo .modal-backdrop {
        display: block;
    }
}
`;
