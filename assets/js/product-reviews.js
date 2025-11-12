/**
 * Product Reviews - Sistema de Estrellas Interactivo
 *
 * @package SaicoWC
 * @version 1.0.0
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        const $starsInput = $('#starsInput');
        const $ratingSelect = $('#rating');

        if (!$starsInput.length || !$ratingSelect.length) {
            return;
        }

        const $stars = $starsInput.find('.star');
        let selectedRating = 0;

        // Click en estrella
        $stars.on('click', function() {
            selectedRating = parseInt($(this).data('rating'));
            $ratingSelect.val(selectedRating);
            updateStarsDisplay(selectedRating);
        });

        // Hover en estrellas
        $stars.on('mouseenter', function() {
            const hoverRating = parseInt($(this).data('rating'));
            updateStarsDisplay(hoverRating);
        });

        // Mouse sale del contenedor de estrellas
        $starsInput.on('mouseleave', function() {
            updateStarsDisplay(selectedRating);
        });

        // Actualizar visualización de estrellas
        function updateStarsDisplay(rating) {
            $stars.each(function() {
                const starRating = parseInt($(this).data('rating'));
                const $star = $(this);

                if (starRating <= rating) {
                    $star.addClass('active');
                } else {
                    $star.removeClass('active');
                }
            });
        }

        // Validación del formulario
        const $reviewForm = $('#commentform');

        if ($reviewForm.length) {
            $reviewForm.on('submit', function(e) {
                if (selectedRating === 0) {
                    e.preventDefault();
                    alert('Por favor, selecciona una calificación de estrellas.');
                    $starsInput[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return false;
                }
            });
        }
    });

})(jQuery);
