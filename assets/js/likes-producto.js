/**
 * Sistema de Likes con Base de Datos y Cookies (sin login requerido)
 *
 * @package SaicoWC
 * @version 2.0.0
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        const $likeButton = $('#likeButton');
        const $likesCount = $('#likesCount');

        if (!$likeButton.length) {
            return;
        }

        $likeButton.on('click', function(e) {
            e.preventDefault();

            const productId = $(this).data('product-id');
            const $button = $(this);

            // Deshabilitar botón mientras procesa
            $button.prop('disabled', true);

            $.ajax({
                url: saicoLikes.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'saico_toggle_like',
                    nonce: saicoLikes.nonce,
                    product_id: productId
                },
                success: function(response) {
                    if (response.success) {
                        // Actualizar estado del botón
                        $button.toggleClass('liked');

                        // Actualizar texto
                        const newText = response.data.liked ? 'Te encanta' : 'Me encanta';
                        $button.find('span').text(newText);

                        // Actualizar contador
                        $likesCount.text(response.data.likes_count.toLocaleString());

                        // Animación
                        if (response.data.liked) {
                            $button.find('.icon-like').css('animation', 'none');
                            setTimeout(function() {
                                $button.find('.icon-like').css('animation', '');
                            }, 10);
                        }
                    } else {
                        alert(response.data.message || 'Error al procesar tu solicitud.');
                    }
                },
                error: function() {
                    alert('Error de conexión. Por favor, intenta de nuevo.');
                },
                complete: function() {
                    $button.prop('disabled', false);
                }
            });
        });
    });

})(jQuery);
