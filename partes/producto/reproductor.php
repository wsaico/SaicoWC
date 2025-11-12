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

if (!$tiene_audio && !$tiene_midi) {
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
