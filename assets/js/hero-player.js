/**
 * Hero Featured Product - Audio Player
 *
 * @package SaicoWC
 * @version 1.0.0
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // Manejar clic en botón play del hero
        $('.play-btn-hero').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const $button = $(this);
            const productId = $button.data('product-id');
            const $audio = $('.hero-audio-player[data-product-id="' + productId + '"]');

            if (!$audio.length) {
                return;
            }

            const audio = $audio[0];

            if ($button.hasClass('playing')) {
                // Pausar
                audio.pause();
                $button.removeClass('playing');
            } else {
                // Detener otros reproductores del hero
                $('.play-btn-hero').not($button).removeClass('playing');
                $('.hero-audio-player').each(function() {
                    this.pause();
                    this.currentTime = 0;
                });

                // Reproducir
                audio.play();
                $button.addClass('playing');
            }
        });

        // Cuando el audio termina
        $('.hero-audio-player').on('ended', function() {
            const productId = $(this).data('product-id');
            $('.play-btn-hero[data-product-id="' + productId + '"]').removeClass('playing');
            this.currentTime = 0;
        });

        // Manejar errores
        $('.hero-audio-player').on('error', function() {
            const productId = $(this).data('product-id');
            $('.play-btn-hero[data-product-id="' + productId + '"]').removeClass('playing');
        });
    });

})(jQuery);
