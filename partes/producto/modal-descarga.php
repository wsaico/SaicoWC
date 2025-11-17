<?php
/**
 * Modal de Descarga - Muestra opciones de descarga después de cuenta regresiva
 */

if (!defined('ABSPATH')) exit;

global $product;
if (!$product) return;

$product_id = $product->get_id();
$countdown_time = get_theme_mod('animated_button_time', 10);
$is_logged_in = is_user_logged_in();
?>

<div id="saico-download-modal" class="saico-modal">
    <div class="saico-modal-overlay" onclick="saicoCerrarModal('saico-download-modal')"></div>

    <div class="saico-modal-contenido">
        <!-- Botón cerrar -->
        <button class="saico-modal-cerrar" onclick="saicoCerrarModal('saico-download-modal')" aria-label="Cerrar">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>

        <!-- PASO 1: Cuenta regresiva -->
        <div class="saico-modal-paso saico-modal-paso1">
            <h3>Preparando tu descarga...</h3>
            <p class="modal-product-title"><?php echo esc_html($product->get_name()); ?></p>

            <!-- Temporizador lineal elegante -->
            <div class="countdown-linear">
                <div class="countdown-number-wrapper">
                    <span id="saico-countdown-number"><?php echo esc_html($countdown_time); ?></span>
                    <span class="countdown-label">segundos</span>
                </div>
                <div class="countdown-bar-container">
                    <div class="countdown-bar-progress"></div>
                </div>
            </div>

            <!-- AdSense - Durante espera (usuario cautivo) -->
            <?php
            // Usar función sanitizada de AdSense
            saico_get_adsense('adsense_modal_waiting', 'modal-waiting');
            ?>
        </div>

        <!-- PASO 2: Links de descarga -->
        <div class="saico-modal-paso saico-modal-paso2" style="display: none;">
            <h3>¡Tu descarga está lista!</h3>
            <p class="modal-product-title"><?php echo esc_html($product->get_name()); ?></p>

            <!-- AdSense - Antes de links (posición premium) -->
            <?php
            // Usar función sanitizada de AdSense
            saico_get_adsense('adsense_modal_before_links', 'modal-before');
            ?>

            <div class="modal-download-links">
                <?php
                if (shortcode_exists('download_now_page')) {
                    echo do_shortcode('[download_now_page]');
                } else {
                    echo '<p>Configure el plugin "Download Now for WooCommerce" para mostrar links de descarga</p>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos específicos del modal de descarga */
.saico-modal-paso {
    text-align: center;
    width: 100%;
    max-width: 100%;
    overflow: hidden;
    box-sizing: border-box;
}

.saico-modal-paso h3 {
    font-size: 26px;
    margin-bottom: 12px;
    color: #1f2937;
    font-weight: 700;
    word-wrap: break-word;
}

.modal-product-title {
    font-size: 15px;
    color: #10b981;
    font-weight: 600;
    margin: 8px 0 20px;
    word-wrap: break-word;
    overflow-wrap: break-word;
    max-width: 100%;
}

/* Temporizador lineal elegante */
.countdown-linear {
    margin: 30px auto;
    max-width: 100%;
    width: 100%;
    box-sizing: border-box;
}

.countdown-number-wrapper {
    display: flex;
    align-items: baseline;
    justify-content: center;
    gap: 8px;
    margin-bottom: 16px;
}

#saico-countdown-number {
    font-size: 64px;
    font-weight: 800;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1;
    letter-spacing: -2px;
}

.countdown-label {
    font-size: 14px;
    color: #6b7280;
    font-weight: 500;
}

.countdown-bar-container {
    width: 100%;
    height: 8px;
    background: #e5e7eb;
    border-radius: 100px;
    overflow: hidden;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.06);
}

.countdown-bar-progress {
    height: 100%;
    width: 0%;
    background: linear-gradient(90deg, #10b981 0%, #059669 100%);
    border-radius: 100px;
    transition: width 1s linear;
    box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
}

/* Estilos de AdSense gestionados desde inc/adsense.php */

.modal-download-links {
    margin: 20px 0;
    width: 100%;
    max-width: 100%;
    overflow: hidden;
    box-sizing: border-box;
}

.modal-download-links a,
.modal-download-links button {
    max-width: 100%;
    word-wrap: break-word;
    box-sizing: border-box;
}

/* Responsive - Estilos específicos del modal de descarga */
@media (max-width: 768px) {
    .saico-modal-paso {
        padding: 0;
    }

    .saico-modal-paso h3 {
        font-size: 20px;
        margin-bottom: 8px;
    }

    .modal-product-title {
        font-size: 13px;
        margin: 6px 0 16px;
    }

    .countdown-linear {
        margin: 20px auto;
    }

    #saico-countdown-number {
        font-size: 40px;
        letter-spacing: -1px;
    }

    .countdown-label {
        font-size: 11px;
    }

    .countdown-bar-container {
        height: 6px;
    }

    .modal-download-links {
        margin: 16px 0;
    }
}

@media (max-width: 480px) {
    .saico-modal-paso h3 {
        font-size: 18px;
    }

    .modal-product-title {
        font-size: 12px;
    }

    #saico-countdown-number {
        font-size: 36px;
    }

    .countdown-label {
        font-size: 10px;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    let isLoggedIn = <?php echo $is_logged_in ? 'true' : 'false'; ?>;
    let userWantsDownload = false; // Flag para saber si el usuario quiere descargar

    // Abrir modal al hacer clic en botón de descarga
    $('#saico-download-button').on('click', function(e) {
        e.preventDefault();

        // Si el usuario NO está logueado, mostrar popup de invitación primero
        if (!isLoggedIn) {
            userWantsDownload = true; // Marcar que quiere descargar

            if (typeof saicoShowLoginInvite === 'function') {
                saicoShowLoginInvite();
            }
            return;
        }

        // Si está logueado, abrir modal y saltar directamente a los links
        saicoAbrirModal('saico-download-modal');
        mostrarLinksDirectamente();
    });

    // Función para usuarios no logueados que eligen continuar con espera
    window.saicoModalContinuarConEspera = function() {
        userWantsDownload = false; // Reset flag
        saicoAbrirModal('saico-download-modal');
        iniciarCuentaRegresiva();
    };

    // Función llamada DESPUÉS de login exitoso
    window.saicoAfterLoginSuccess = function() {
        isLoggedIn = true;

        // Solo proceder si el usuario había intentado descargar antes de loguearse
        if (userWantsDownload) {
            userWantsDownload = false; // Reset flag

            // Esperar un momento para que el modal de login se cierre
            setTimeout(function() {
                saicoAbrirModal('saico-download-modal');
                mostrarLinksDirectamente();
            }, 400);
        }
    };

    function mostrarLinksDirectamente() {
        const $paso1 = $('.saico-modal-paso1');
        const $paso2 = $('.saico-modal-paso2');

        $paso1.hide();
        $paso2.show();
    }

    function iniciarCuentaRegresiva() {
        const $numero = $('#saico-countdown-number');
        const $paso1 = $('.saico-modal-paso1');
        const $paso2 = $('.saico-modal-paso2');
        const $progressBar = $('.countdown-bar-progress');

        let count = parseInt($numero.text(), 10);
        const initialCount = count;

        $paso1.show();
        $paso2.hide();

        // Inicializar barra de progreso
        $progressBar.css('width', '0%');

        const interval = setInterval(function() {
            count--;
            $numero.text(count);

            // Actualizar barra de progreso lineal
            const progress = ((initialCount - count) / initialCount) * 100;
            $progressBar.css('width', progress + '%');

            if (count <= 0) {
                clearInterval(interval);

                // Completar animación de barra
                $progressBar.css('width', '100%');

                setTimeout(function() {
                    $paso1.fadeOut(300, function() {
                        $paso2.fadeIn(300);
                    });
                }, 500);
            }
        }, 1000);
    }
});
</script>
