<?php
/**
 * Reproductor Moderno y Minimalista - Audio/MIDI
 *
 * @package SaicoWC
 * @version 2.0.0
 */

defined('ABSPATH') || exit;

global $product;

if (!$product) {
    return;
}

$producto_id = $product->get_id();
$producto_titulo = $product->get_name();

// Detectar audio desde campo ACF
$tiene_audio = false;
$audio_url = '';

if (function_exists('get_field')) {
    $audio_field = get_field('product_audio', $producto_id);

    if (is_array($audio_field) && isset($audio_field['url'])) {
        $audio_url = $audio_field['url'];
    } elseif (is_numeric($audio_field)) {
        $audio_url = wp_get_attachment_url($audio_field);
    } elseif (is_string($audio_field)) {
        $audio_url = $audio_field;
    }

    $tiene_audio = !empty($audio_url) && filter_var($audio_url, FILTER_VALIDATE_URL);
}

// Detectar MIDI desde archivos descargables
$tiene_midi = false;
$midi_url = '';

if (!$tiene_audio && function_exists('saico_get_midi_file_url')) {
    $midi_url = saico_get_midi_file_url($producto_id);
    $tiene_midi = !empty($midi_url);
}

// Detectar video de YouTube desde campo ACF
$tiene_video = false;
$video_embed_url = '';

if (function_exists('get_field') && function_exists('saico_get_youtube_embed_url')) {
    $video_url = get_field('product_youtube_video', $producto_id);
    if (!empty($video_url)) {
        $video_embed_url = saico_get_youtube_embed_url($video_url);
        $tiene_video = !empty($video_embed_url);
    }
}

if (!$tiene_audio && !$tiene_midi && !$tiene_video) {
    return;
}
?>

<div class="saico-player-modern">
    <?php if ($tiene_audio): ?>
        <audio id="saicoAudioPlayer" preload="metadata">
            <source src="<?php echo esc_url($audio_url); ?>" type="audio/mpeg">
        </audio>

        <div class="player-wrapper">
            <!-- Botón Play Principal -->
            <button class="player-play-main" id="playPauseBtn" aria-label="Reproducir">
                <div class="play-icon">
                    <svg class="icon-play" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                    <svg class="icon-pause" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M6 4h4v16H6V4zm8 0h4v16h-4V4z"/>
                    </svg>
                </div>
            </button>

            <!-- Contenido del Player -->
            <div class="player-content">
                <div class="player-header">
                    <div class="player-info">
                        <span class="player-label">Vista Previa</span>
                        <h4 class="player-title"><?php echo esc_html($producto_titulo); ?></h4>
                    </div>
                    <div class="player-time">
                        <span id="currentTime">0:00</span>
                        <span class="time-divider">/</span>
                        <span id="totalTime">0:00</span>
                    </div>
                </div>

                <!-- Barra de Progreso -->
                <div class="player-progress-wrapper">
                    <div class="progress-track" id="progressBar">
                        <div class="progress-fill" id="progressFilled"></div>
                        <div class="progress-thumb" id="progressThumb"></div>
                    </div>
                </div>

                <!-- Controles Secundarios -->
                <div class="player-controls-secondary">
                    <button class="control-btn" id="volumeBtn" aria-label="Volumen">
                        <svg class="icon-volume-high" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
                            <path d="M15.54 8.46a5 5 0 0 1 0 7.07"></path>
                        </svg>
                        <svg class="icon-volume-mute" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon>
                            <line x1="23" y1="9" x2="17" y2="15"></line>
                            <line x1="17" y1="9" x2="23" y2="15"></line>
                        </svg>
                    </button>
                    <div class="volume-slider-wrapper">
                        <input type="range" id="volumeSlider" min="0" max="100" value="70" class="volume-slider">
                    </div>
                </div>
            </div>
        </div>

    <?php elseif ($tiene_midi): ?>
        <div class="saico-midi-container">
            <?php if (shortcode_exists('midiplay')): ?>
                <?php echo do_shortcode('[midiplay]'); ?>
            <?php else: ?>
                <div class="midi-fallback">
                    <p>Plugin MIDI no disponible</p>
                    <a href="<?php echo esc_url($midi_url); ?>" class="btn-download-midi" download>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        Descargar MIDI
                    </a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php if ($tiene_video): ?>
    <!-- Sección de Video de YouTube -->
    <div class="saico-video-section">
        <!-- Botón para mostrar/ocultar video -->
        <button class="saico-video-toggle" id="saicoVideoToggle" aria-expanded="false">
            <div class="video-toggle-icon">
                <svg class="icon-play-video" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"></path>
                    <polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02" fill="currentColor"></polygon>
                </svg>
                <svg class="icon-close-video" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </div>
            <div class="video-toggle-content">
                <span class="video-toggle-label">Ver Video</span>
                <span class="video-toggle-title"><?php echo esc_html($producto_titulo); ?></span>
            </div>
            <div class="video-toggle-chevron">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </div>
        </button>

        <!-- Contenedor del video (oculto por defecto) -->
        <div class="saico-video-container" id="saicoVideoContainer" style="display: none;">
            <div class="saico-video-wrapper" id="saicoVideoWrapper">
                <!-- El iframe se cargará dinámicamente al hacer click -->
            </div>
        </div>
    </div>

    <script>
    (function() {
        const toggleBtn = document.getElementById('saicoVideoToggle');
        const videoContainer = document.getElementById('saicoVideoContainer');
        const videoWrapper = document.getElementById('saicoVideoWrapper');
        const videoUrl = <?php echo json_encode($video_embed_url); ?>;
        const videoTitle = <?php echo json_encode($producto_titulo . ' - Video'); ?>;
        let videoLoaded = false;

        if (toggleBtn && videoContainer && videoWrapper) {
            toggleBtn.addEventListener('click', function() {
                const isExpanded = toggleBtn.getAttribute('aria-expanded') === 'true';

                if (!isExpanded) {
                    // Mostrar video
                    toggleBtn.setAttribute('aria-expanded', 'true');
                    toggleBtn.classList.add('active');

                    // Cambiar texto del botón
                    const label = toggleBtn.querySelector('.video-toggle-label');
                    if (label) label.textContent = 'Ocultar Video';

                    // Mostrar contenedor con animación
                    videoContainer.style.display = 'block';

                    // Trigger reflow para animación
                    void videoContainer.offsetWidth;
                    videoContainer.classList.add('show');

                    // Cargar iframe solo la primera vez
                    if (!videoLoaded) {
                        const iframe = document.createElement('iframe');
                        iframe.src = videoUrl + '&autoplay=1';
                        iframe.title = videoTitle;
                        iframe.frameBorder = '0';
                        iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
                        iframe.allowFullscreen = true;
                        iframe.loading = 'lazy';

                        videoWrapper.appendChild(iframe);
                        videoLoaded = true;
                    }

                    // Scroll suave al video
                    setTimeout(function() {
                        videoContainer.scrollIntoView({
                            behavior: 'smooth',
                            block: 'nearest'
                        });
                    }, 100);

                } else {
                    // Ocultar video
                    toggleBtn.setAttribute('aria-expanded', 'false');
                    toggleBtn.classList.remove('active');

                    // Cambiar texto del botón
                    const label = toggleBtn.querySelector('.video-toggle-label');
                    if (label) label.textContent = 'Ver Video';

                    // Ocultar con animación
                    videoContainer.classList.remove('show');

                    setTimeout(function() {
                        videoContainer.style.display = 'none';
                    }, 300);
                }
            });
        }
    })();
    </script>
<?php endif; ?>
