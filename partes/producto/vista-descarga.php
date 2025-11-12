<?php
/**
 * Vista Por Página - Muestra links de descarga con temporizador
 */

if (!defined('ABSPATH')) exit;

global $product;
if (!$product) return;

// Verificar si la vista por página está habilitada
$page_view_enabled = get_theme_mod('enable_download_page_view', false);
$countdown_time = get_theme_mod('animated_button_time', 10);
?>

<div id="saico-download-view" class="saico-download-view" style="display: none;">
    <div class="download-view-container">
        <!-- Header -->
        <div class="download-view-header">
            <button id="saico-download-back-btn" class="back-btn">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Volver al producto
            </button>
        </div>

        <!-- PASO 1: Temporizador COMPACTO -->
        <div class="download-step download-step1">
            <h2>Preparando tu descarga...</h2>
            <div class="timer-circle">
                <svg class="timer-svg" width="160" height="160">
                    <defs>
                        <linearGradient id="timerGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:#10b981;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#059669;stop-opacity:1" />
                        </linearGradient>
                    </defs>
                    <circle class="timer-bg" cx="80" cy="80" r="70" fill="none" stroke="#e5e7eb" stroke-width="8"></circle>
                    <circle class="timer-progress" cx="80" cy="80" r="70" fill="none" stroke="url(#timerGradient)" stroke-width="8" stroke-linecap="round" stroke-dasharray="439.8" stroke-dashoffset="439.8"></circle>
                </svg>
                <span class="timer-number" id="saico-page-countdown"><?php echo esc_html($countdown_time); ?></span>
            </div>
            <p>Tu descarga estará lista en unos segundos</p>

            <!-- ANUNCIO ADSENSE - Posición estratégica durante la espera -->
            <div class="adsense-container adsense-waiting">
                <?php
                $ad_code = get_theme_mod('adsense_download_waiting', '');
                if (!empty($ad_code)) {
                    // Mostrar código AdSense sin escapar para que se ejecute correctamente
                    echo saico_display_adsense_code($ad_code);
                } else {
                    echo '<div class="ad-placeholder">
                        <p>ESPACIO PUBLICITARIO</p>
                        <small>Configure AdSense en Personalizar → AdSense</small>
                    </div>';
                }
                ?>
            </div>
        </div>

        <!-- PASO 2: Links de descarga COMPACTO -->
        <div class="download-step download-step2" style="display: none;">
            <h2>¡Tu descarga está lista!</h2>
            <p class="download-subtitle">Elige tu servidor de descarga preferido</p>

            <!-- ANUNCIO ADSENSE - Encima de los links (posición premium) -->
            <div class="adsense-container adsense-before-links">
                <?php
                $ad_code_before = get_theme_mod('adsense_before_download_links', '');
                if (!empty($ad_code_before)) {
                    // Mostrar código AdSense sin escapar para que se ejecute correctamente
                    echo saico_display_adsense_code($ad_code_before);
                } else {
                    echo '<div class="ad-placeholder">
                        <p>ESPACIO PUBLICITARIO PREMIUM</p>
                        <small>Configure AdSense en Personalizar → AdSense</small>
                    </div>';
                }
                ?>
            </div>

            <div class="download-links-container">
                <?php
                if (shortcode_exists('download_now_page')) {
                    echo do_shortcode('[download_now_page]');
                } else {
                    echo '<p>Configure el plugin "Download Now for WooCommerce"</p>';
                }
                ?>
            </div>

            <!-- ANUNCIO ADSENSE - Después de los links -->
            <div class="adsense-container adsense-after-links">
                <?php
                $ad_code_after = get_theme_mod('adsense_after_download_links', '');
                if (!empty($ad_code_after)) {
                    // Mostrar código AdSense sin escapar para que se ejecute correctamente
                    echo saico_display_adsense_code($ad_code_after);
                } else {
                    echo '<div class="ad-placeholder">
                        <p>ESPACIO PUBLICITARIO</p>
                        <small>Configure AdSense en Personalizar → AdSense</small>
                    </div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<style>
.saico-download-view {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: #fff;
    z-index: 9999;
    overflow-y: auto;
}

.download-view-container {
    max-width: 700px;
    margin: 0 auto;
    padding: 20px;
}

.download-view-header {
    margin-bottom: 20px;
}

.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: #f3f4f6;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
}

.back-btn:hover {
    background: #e5e7eb;
}

.download-step {
    text-align: center;
    padding: 20px;
    animation: fadeInUp 0.4s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.download-step h2 {
    font-size: 28px;
    margin-bottom: 12px;
    color: #1f2937;
    font-weight: 700;
}

.download-subtitle {
    font-size: 15px;
    color: #6b7280;
    margin-bottom: 24px;
}

.download-step > p {
    font-size: 14px;
    color: #6b7280;
    margin: 16px 0;
}

.timer-circle {
    position: relative;
    width: 160px;
    height: 160px;
    margin: 30px auto;
}

.timer-svg {
    transform: rotate(-90deg);
    filter: drop-shadow(0 4px 12px rgba(16, 185, 129, 0.2));
}

.timer-progress {
    transition: stroke-dashoffset 1s linear;
}

.timer-number {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 52px;
    font-weight: 700;
    color: #10b981;
    text-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}

.download-links-container {
    margin: 20px auto;
    max-width: 100%;
}

/* ESTILOS PARA ADSENSE - Optimizado para clics */
.adsense-container {
    margin: 24px auto;
    padding: 16px;
    background: #f9fafb;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    max-width: 728px;
    min-height: 90px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.adsense-waiting {
    margin-top: 30px;
}

.adsense-before-links {
    margin: 30px auto 20px;
    background: #fff;
    border: 2px dashed #10b981;
}

.adsense-after-links {
    margin: 30px auto;
}

.ad-placeholder {
    text-align: center;
    padding: 30px;
    color: #9ca3af;
}

.ad-placeholder p {
    font-weight: 600;
    margin-bottom: 8px;
}

/* Responsive */
@media (max-width: 768px) {
    .download-view-container {
        padding: 16px;
    }

    .download-step {
        padding: 16px;
    }

    .download-step h2 {
        font-size: 20px;
    }

    .timer-circle {
        width: 100px;
        height: 100px;
    }

    .timer-number {
        font-size: 28px;
    }

    .adsense-container {
        margin: 16px auto;
        padding: 12px;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Solo ejecutar si la vista por página está habilitada
    const pageViewEnabled = <?php echo $page_view_enabled ? 'true' : 'false'; ?>;

    if (!pageViewEnabled) {
        return; // Salir si no está habilitado
    }

    let isDownloadViewActive = false;

    // Función para obtener parámetro de URL
    function getUrlParameter(name) {
        const regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        const results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

    // Función para actualizar URL
    function updateUrl(param, value) {
        const url = new URL(window.location);
        if (value) {
            url.searchParams.set(param, value);
        } else {
            url.searchParams.delete(param);
        }
        window.history.pushState({downloadView: !!value}, '', url);
    }

    // Función para cambiar vista
    function switchToDownloadView() {
        $('#saico-download-view').fadeIn(300);
        isDownloadViewActive = true;
        $('html, body').scrollTop(0);
        initCountdown();
    }

    function switchToMainView() {
        $('#saico-download-view').fadeOut(300);
        isDownloadViewActive = false;
        updateUrl('download', '');
    }

    // Iniciar temporizador
    function initCountdown() {
        const $number = $('#saico-page-countdown');
        const $step1 = $('.download-step1');
        const $step2 = $('.download-step2');
        const $progress = $('.timer-progress');

        let count = parseInt($number.text(), 10);
        const initialTime = count;
        const circumference = 439.8; // 2 * PI * 70 (radio del círculo)

        $step1.show();
        $step2.hide();

        // Inicializar progreso
        $progress.css('stroke-dashoffset', circumference);

        const interval = setInterval(function() {
            count--;
            $number.text(count);

            // Actualizar círculo de progreso
            const progress = ((initialTime - count) / initialTime);
            const offset = circumference - (progress * circumference);
            $progress.css('stroke-dashoffset', offset);

            if (count <= 0) {
                clearInterval(interval);

                // Completar animación
                $progress.css('stroke-dashoffset', 0);

                setTimeout(function() {
                    $step1.fadeOut(300, function() {
                        $step2.fadeIn(300);
                    });
                }, 500);
            }
        }, 1000);
    }

    // Click en botón de descarga
    $('#saico-download-button').on('click', function(e) {
        e.preventDefault();
        updateUrl('download', 'links');
        switchToDownloadView();
    });

    // Click en botón volver
    $('#saico-download-back-btn').on('click', function(e) {
        e.preventDefault();
        switchToMainView();
    });

    // Manejar navegación del navegador (atrás/adelante)
    $(window).on('popstate', function() {
        const downloadParam = getUrlParameter('download');
        if (downloadParam === 'links' && !isDownloadViewActive) {
            switchToDownloadView();
        } else if (downloadParam !== 'links' && isDownloadViewActive) {
            $('#saico-download-view').hide();
            isDownloadViewActive = false;
        }
    });

    // Verificar si debe mostrar la vista al cargar
    if (getUrlParameter('download') === 'links') {
        $('#saico-download-view').show();
        isDownloadViewActive = true;
        initCountdown();
    }
});
</script>
