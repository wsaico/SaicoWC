/**
 * Sistema Global de Audio Optimizado - eSaico Theme
 * Centraliza todas las funciones de audio para mejor rendimiento
 * 
 * @package eSaico
 * @version 1.0.0
 */
(function($) {
    'use strict';
    
    /**
     * CLASE PRINCIPAL PARA MANEJO DE AUDIO
     */
    class EsaicoAudioManager {
        constructor() {
            this.currentlyPlaying = null;
            this.audioElements = new Map();
            this.isInitialized = false;
            
            this.init();
        }
        
        /**
         * Inicializar el sistema de audio
         */
        init() {
            if (this.isInitialized) return;

            this.bindEvents();
            this.preloadAudioElements();
            this.isInitialized = true;
        }
        
        /**
         * Vincular eventos de audio
         */
        bindEvents() {
            // Usar delegación de eventos para mejor rendimiento
            $(document).on('click', '.woo-product-play-button', (e) => {
                this.handlePlayButton(e);
            });
            
            // Manejar cuando un audio termina
            $(document).on('ended', '.woo-product-audio', (e) => {
                this.handleAudioEnded(e);
            });
            
            // Manejar errores de audio
            $(document).on('error', '.woo-product-audio', (e) => {
                this.handleAudioError(e);
            });
            
            // Pausar audio al cambiar de página (para SPAs)
            $(window).on('beforeunload', () => {
                this.stopAllAudio();
            });
        }
        
        /**
         * Precargar elementos de audio para mejor rendimiento
         */
        preloadAudioElements() {
            $('.woo-product-audio').each((index, element) => {
                const $audio = $(element);
                const productId = $audio.closest('.woo-product-card, .story-card, .product-item, .producto-card, .hero-featured-item').data('product-id');

                if (productId) {
                    this.audioElements.set(productId, element);

                    // Configurar audio para mejor rendimiento
                    element.preload = 'metadata';
                    element.volume = 0.8;
                }
            });
        }
        
        /**
         * Manejar clic en botón de reproducción
         */
        handlePlayButton(e) {
            e.stopPropagation();
            e.preventDefault();
            e.stopImmediatePropagation();

            const $button = $(e.currentTarget);

            // Buscar el audio en toda la tarjeta de producto (funciona para product-card, story-card, product-item, producto-card y hero-featured-item)
            const $audioContainer = $button.closest('.product-card, .story-card, .product-item, .producto-card, .hero-featured-item').find('.woo-product-audio');

            if (!$audioContainer.length) {
                return;
            }

            const audioElement = $audioContainer[0];

            // Si el audio no tiene src configurado, obtenerlo del data-audio del botón
            if (!audioElement.src) {
                const audioUrl = $button.data('audio');
                if (audioUrl) {
                    audioElement.src = audioUrl;
                } else {
                    return;
                }
            }

            // Si hay otro audio reproduciéndose, pausarlo
            if (this.currentlyPlaying && this.currentlyPlaying !== audioElement) {
                this.pauseAudio(this.currentlyPlaying);
            }

            // Toggle play/pause
            if (audioElement.paused) {
                this.playAudio(audioElement, $button);
            } else {
                this.pauseAudio(audioElement, $button);
            }
        }
        
        /**
         * Reproducir audio
         */
        async playAudio(audioElement, $button) {
            try {
                // Añadir clase de carga
                $button.addClass('loading');

                const playPromise = audioElement.play();

                if (playPromise !== undefined) {
                    await playPromise;

                    // Éxito en la reproducción
                    $button.removeClass('loading').addClass('playing');
                    this.currentlyPlaying = audioElement;

                    // Actualizar ícono a pause
                    this.updateButtonIcon($button, 'pause');

                    // Disparar evento personalizado
                    $(audioElement).trigger('esaico:audio:play');
                }
            } catch (error) {
                $button.removeClass('loading');
                this.showAudioError($button);
            }
        }
        
        /**
         * Pausar audio
         */
        pauseAudio(audioElement, $button = null) {
            if (!audioElement) return;

            audioElement.pause();

            if ($button) {
                $button.removeClass('playing loading');
                // Actualizar ícono a play
                this.updateButtonIcon($button, 'play');
            } else {
                // Buscar el botón asociado
                const $associatedButton = $(audioElement).siblings('.woo-product-play-button');
                $associatedButton.removeClass('playing loading');
                this.updateButtonIcon($associatedButton, 'play');
            }

            if (this.currentlyPlaying === audioElement) {
                this.currentlyPlaying = null;
            }

            // Disparar evento personalizado
            $(audioElement).trigger('esaico:audio:pause');
        }
        
        /**
         * Manejar cuando un audio termina
         */
        handleAudioEnded(e) {
            const audioElement = e.currentTarget;
            const $button = $(audioElement).siblings('.woo-product-play-button');

            $button.removeClass('playing');
            this.updateButtonIcon($button, 'play');
            this.currentlyPlaying = null;

            // Disparar evento personalizado
            $(audioElement).trigger('esaico:audio:ended');
        }
        
        /**
         * Manejar errores de audio
         */
        handleAudioError(e) {
            const audioElement = e.currentTarget;
            const $button = $(audioElement).siblings('.woo-product-play-button');

            this.showAudioError($button);
        }
        
        /**
         * Mostrar error de audio
         */
        showAudioError($button) {
            $button.addClass('error').removeClass('playing loading');

            // Remover clase de error después de 3 segundos
            setTimeout(() => {
                $button.removeClass('error');
            }, 3000);
        }

        /**
         * Actualizar ícono del botón play/pause
         */
        updateButtonIcon($button, state) {
            const $svg = $button.find('svg');

            if (state === 'play') {
                // Ícono play
                $svg.html('<polygon points="5,3 19,12 5,21"/>');
            } else if (state === 'pause') {
                // Ícono pause
                $svg.html('<rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/>');
            }
        }
        
        /**
         * Detener todos los audios
         */
        stopAllAudio() {
            $('.woo-product-audio').each((index, element) => {
                if (!element.paused) {
                    this.pauseAudio(element);
                }
            });
        }
        
        /**
         * Obtener audio por ID de producto
         */
        getAudioByProductId(productId) {
            return this.audioElements.get(productId);
        }
        
        /**
         * Verificar si hay audio reproduciéndose
         */
        isPlaying() {
            return this.currentlyPlaying !== null && !this.currentlyPlaying.paused;
        }
        
        /**
         * Obtener elemento de audio actual
         */
        getCurrentAudio() {
            return this.currentlyPlaying;
        }
        
        /**
         * Destruir instancia (para limpieza)
         */
        destroy() {
            this.stopAllAudio();
            $(document).off('click', '.woo-product-play-button');
            $(document).off('ended', '.woo-product-audio');
            $(document).off('error', '.woo-product-audio');
            this.audioElements.clear();
            this.currentlyPlaying = null;
            this.isInitialized = false;
        }
    }
    
    /**
     * FUNCIONES DE UTILIDAD GLOBALES
     */
    
    /**
     * Debounce function para optimizar rendimiento
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
     * Verificar soporte de audio del navegador
     */
    function checkAudioSupport() {
        const audio = document.createElement('audio');
        return {
            mp3: audio.canPlayType('audio/mpeg'),
            ogg: audio.canPlayType('audio/ogg'),
            wav: audio.canPlayType('audio/wav'),
            m4a: audio.canPlayType('audio/mp4')
        };
    }
    
    /**
     * INICIALIZACIÓN GLOBAL
     */
    let audioManager;
    
    $(document).ready(function() {
        // Verificar soporte de audio
        const audioSupport = checkAudioSupport();

        // Inicializar manager de audio
        audioManager = new EsaicoAudioManager();

        // Hacer disponible globalmente
        window.EsaicoAudio = audioManager;

        // Reinicializar después de cargas AJAX
        $(document).on('esaico:products:loaded', function() {
            if (audioManager) {
                audioManager.preloadAudioElements();
            }
        });
    });
    
    /**
     * API PÚBLICA
     */
    window.EsaicoAudioAPI = {
        play: function(productId) {
            if (audioManager) {
                const audio = audioManager.getAudioByProductId(productId);
                if (audio) {
                    const $button = $(audio).siblings('.woo-product-play-button');
                    audioManager.playAudio(audio, $button);
                }
            }
        },
        
        pause: function(productId) {
            if (audioManager) {
                const audio = audioManager.getAudioByProductId(productId);
                if (audio) {
                    audioManager.pauseAudio(audio);
                }
            }
        },
        
        stopAll: function() {
            if (audioManager) {
                audioManager.stopAllAudio();
            }
        },
        
        isPlaying: function() {
            return audioManager ? audioManager.isPlaying() : false;
        },
        
        getCurrentAudio: function() {
            return audioManager ? audioManager.getCurrentAudio() : null;
        }
    };
    
})(jQuery);