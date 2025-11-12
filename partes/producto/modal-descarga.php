<?php
/**
 * Modal de Descarga - Muestra opciones de descarga después de cuenta regresiva
 */

if (!defined('ABSPATH')) exit;

global $product;
if (!$product) return;

$product_id = $product->get_id();
$countdown_time = get_theme_mod('animated_button_time', 10);
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
            <div class="countdown-circle">
                <svg class="countdown-ring" width="140" height="140">
                    <circle class="countdown-ring-bg" cx="70" cy="70" r="60"></circle>
                    <circle class="countdown-ring-progress" cx="70" cy="70" r="60"></circle>
                </svg>
                <span id="saico-countdown-number"><?php echo esc_html($countdown_time); ?></span>
            </div>
            <p>Tu descarga comenzará en unos segundos</p>

            <!-- AdSense - Durante espera (usuario cautivo) -->
            <div class="modal-adsense-container modal-adsense-waiting">
                <?php
                $ad_waiting = get_theme_mod('adsense_modal_waiting', '');
                if (!empty($ad_waiting)) {
                    // Mostrar código AdSense directamente sin filtros
                    echo $ad_waiting;
                } else {
                    echo '<div class="ad-placeholder"><p>AdSense - Durante Espera</p><small>Configure en Personalizar → AdSense</small></div>';
                }
                ?>
            </div>
        </div>

        <!-- PASO 2: Links de descarga -->
        <div class="saico-modal-paso saico-modal-paso2" style="display: none;">
            <h3>¡Tu descarga está lista!</h3>
            <p class="modal-subtitle">Elige tu servidor de descarga preferido</p>

            <!-- AdSense - Antes de links (posición premium) -->
            <div class="modal-adsense-container modal-adsense-before">
                <?php
                $ad_before = get_theme_mod('adsense_modal_before_links', '');
                if (!empty($ad_before)) {
                    // Mostrar código AdSense directamente sin filtros
                    echo $ad_before;
                }
                ?>
            </div>

            <div class="modal-download-links">
                <?php
                if (shortcode_exists('download_now_page')) {
                    echo do_shortcode('[download_now_page]');
                } else {
                    echo '<p>Configure el plugin "Download Now for WooCommerce" para mostrar links de descarga</p>';
                }
                ?>
            </div>

            <!-- AdSense - Después de links -->
            <div class="modal-adsense-container modal-adsense-after">
                <?php
                $ad_after = get_theme_mod('adsense_modal_after_links', '');
                if (!empty($ad_after)) {
                    // Mostrar código AdSense directamente sin filtros
                    echo $ad_after;
                }
                ?>
            </div>

            <!-- Botón para cerrar modal -->
            <button class="modal-close-btn" onclick="saicoCerrarModal('saico-download-modal')">
                Cerrar
            </button>
        </div>
    </div>
</div>

<style>
.saico-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
}

.saico-modal.activo {
    display: flex;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.saico-modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(8px);
}

.saico-modal-contenido {
    position: relative;
    background: white;
    border-radius: 20px;
    padding: 40px;
    max-width: 600px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 25px 80px rgba(0, 0, 0, 0.4);
    z-index: 1;
    animation: slideUp 0.4s ease;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(50px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.saico-modal-cerrar {
    position: absolute;
    top: 16px;
    right: 16px;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #f3f4f6;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    z-index: 10;
}

.saico-modal-cerrar:hover {
    background: #e5e7eb;
    transform: rotate(90deg);
}

.saico-modal-paso {
    text-align: center;
}

.saico-modal-paso h3 {
    font-size: 26px;
    margin-bottom: 12px;
    color: #1f2937;
    font-weight: 700;
}

.modal-subtitle {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 20px;
}

/* Countdown Circle con SVG animado */
.countdown-circle {
    position: relative;
    width: 140px;
    height: 140px;
    margin: 24px auto;
}

.countdown-ring {
    transform: rotate(-90deg);
}

.countdown-ring-bg {
    fill: none;
    stroke: #e5e7eb;
    stroke-width: 8;
}

.countdown-ring-progress {
    fill: none;
    stroke: url(#gradient);
    stroke-width: 8;
    stroke-linecap: round;
    stroke-dasharray: 377;
    stroke-dashoffset: 377;
    transition: stroke-dashoffset 1s linear;
}

#saico-countdown-number {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 48px;
    font-weight: 700;
    color: #10b981;
}

/* AdSense containers */
.modal-adsense-container {
    margin: 24px 0;
    padding: 16px;
    background: #f9fafb;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    min-height: 90px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-adsense-before {
    background: #fef3c7;
    border: 2px dashed #f59e0b;
    margin-bottom: 16px;
}

.modal-adsense-waiting {
    margin-top: 20px;
}

.ad-placeholder {
    text-align: center;
    color: #9ca3af;
}

.ad-placeholder p {
    margin: 0;
    font-weight: 600;
    font-size: 14px;
}

.ad-placeholder small {
    font-size: 12px;
}

.modal-download-links {
    margin: 20px 0;
}

/* Botón cerrar */
.modal-close-btn {
    margin-top: 24px;
    padding: 12px 32px;
    background: #f3f4f6;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    color: #6b7280;
    cursor: pointer;
    transition: all 0.2s;
}

.modal-close-btn:hover {
    background: #e5e7eb;
    color: #1f2937;
}

body.modal-abierto {
    overflow: hidden;
}

/* Responsive */
@media (max-width: 640px) {
    .saico-modal-contenido {
        padding: 24px;
        max-width: 95%;
    }

    .saico-modal-paso h3 {
        font-size: 22px;
    }

    .countdown-circle {
        width: 120px;
        height: 120px;
    }

    #saico-countdown-number {
        font-size: 40px;
    }
}
</style>

<!-- Gradiente SVG para el anillo de progreso -->
<svg width="0" height="0" style="position: absolute;">
    <defs>
        <linearGradient id="gradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#10b981;stop-opacity:1" />
            <stop offset="100%" style="stop-color:#059669;stop-opacity:1" />
        </linearGradient>
    </defs>
</svg>

<script>
jQuery(document).ready(function($) {
    // Abrir modal al hacer clic en botón de descarga
    $('#saico-download-button').on('click', function(e) {
        e.preventDefault();
        saicoAbrirModal('saico-download-modal');
        iniciarCuentaRegresiva();
    });

    function iniciarCuentaRegresiva() {
        const $numero = $('#saico-countdown-number');
        const $paso1 = $('.saico-modal-paso1');
        const $paso2 = $('.saico-modal-paso2');
        const $progressRing = $('.countdown-ring-progress');

        let count = parseInt($numero.text(), 10);
        const initialCount = count;
        const circumference = 2 * Math.PI * 60; // 2 * PI * radio (60)

        $paso1.show();
        $paso2.hide();

        // Inicializar progreso
        $progressRing.css('stroke-dasharray', circumference);
        $progressRing.css('stroke-dashoffset', circumference);

        const interval = setInterval(function() {
            count--;
            $numero.text(count);

            // Actualizar círculo de progreso
            const progress = ((initialCount - count) / initialCount);
            const offset = circumference - (progress * circumference);
            $progressRing.css('stroke-dashoffset', offset);

            if (count <= 0) {
                clearInterval(interval);

                // Animación de éxito
                $progressRing.css('stroke-dashoffset', 0);

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
