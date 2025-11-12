/**
 * Reproductor Moderno - Controles y Funcionalidad
 *
 * @package SaicoWC
 * @version 2.0.0
 */
(function() {
    'use strict';

    // Esperar a que el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPlayer);
    } else {
        initPlayer();
    }

    function initPlayer() {
        const audio = document.getElementById('saicoAudioPlayer');
        const playPauseBtn = document.getElementById('playPauseBtn');
        const progressBar = document.getElementById('progressBar');
        const progressFilled = document.getElementById('progressFilled');
        const progressThumb = document.getElementById('progressThumb');
        const currentTimeEl = document.getElementById('currentTime');
        const totalTimeEl = document.getElementById('totalTime');
        const volumeBtn = document.getElementById('volumeBtn');
        const volumeSlider = document.getElementById('volumeSlider');

        if (!audio || !playPauseBtn) {
            return;
        }

        // Volumen inicial
        audio.volume = 0.7;

        // Play/Pause
        playPauseBtn.addEventListener('click', function() {
            if (audio.paused) {
                audio.play();
                playPauseBtn.classList.add('playing');
            } else {
                audio.pause();
                playPauseBtn.classList.remove('playing');
            }
        });

        // Actualizar progreso
        audio.addEventListener('timeupdate', function() {
            const percent = (audio.currentTime / audio.duration) * 100;

            if (progressFilled) {
                progressFilled.style.width = percent + '%';
            }

            if (progressThumb) {
                progressThumb.style.left = percent + '%';
            }

            if (currentTimeEl) {
                currentTimeEl.textContent = formatTime(audio.currentTime);
            }
        });

        // Duración cargada
        audio.addEventListener('loadedmetadata', function() {
            if (totalTimeEl) {
                totalTimeEl.textContent = formatTime(audio.duration);
            }
        });

        // Click en barra de progreso
        if (progressBar) {
            progressBar.addEventListener('click', function(e) {
                const rect = progressBar.getBoundingClientRect();
                const percent = (e.clientX - rect.left) / rect.width;
                audio.currentTime = percent * audio.duration;
            });
        }

        // Control de volumen
        if (volumeSlider) {
            volumeSlider.addEventListener('input', function() {
                audio.volume = this.value / 100;
                updateVolumeIcon();
            });
        }

        // Botón mute
        if (volumeBtn) {
            volumeBtn.addEventListener('click', function() {
                if (audio.volume > 0) {
                    audio.volume = 0;
                    if (volumeSlider) volumeSlider.value = 0;
                    volumeBtn.classList.add('muted');
                } else {
                    audio.volume = 0.7;
                    if (volumeSlider) volumeSlider.value = 70;
                    volumeBtn.classList.remove('muted');
                }
            });
        }

        // Actualizar icono de volumen
        function updateVolumeIcon() {
            if (volumeBtn) {
                if (audio.volume === 0) {
                    volumeBtn.classList.add('muted');
                } else {
                    volumeBtn.classList.remove('muted');
                }
            }
        }

        // Cuando termina el audio
        audio.addEventListener('ended', function() {
            playPauseBtn.classList.remove('playing');
            audio.currentTime = 0;
        });

        // Formatear tiempo
        function formatTime(seconds) {
            if (isNaN(seconds)) return '0:00';

            const mins = Math.floor(seconds / 60);
            const secs = Math.floor(seconds % 60);
            return mins + ':' + (secs < 10 ? '0' : '') + secs;
        }

        // Atajos de teclado
        document.addEventListener('keydown', function(e) {
            // Solo si el player está visible
            if (!audio || audio.paused === undefined) return;

            switch(e.key) {
                case ' ':
                case 'k':
                    // Espacio o K para play/pause
                    if (e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
                        e.preventDefault();
                        playPauseBtn.click();
                    }
                    break;
                case 'ArrowLeft':
                    // Flecha izquierda: retroceder 5s
                    e.preventDefault();
                    audio.currentTime = Math.max(0, audio.currentTime - 5);
                    break;
                case 'ArrowRight':
                    // Flecha derecha: avanzar 5s
                    e.preventDefault();
                    audio.currentTime = Math.min(audio.duration, audio.currentTime + 5);
                    break;
                case 'ArrowUp':
                    // Flecha arriba: subir volumen
                    e.preventDefault();
                    audio.volume = Math.min(1, audio.volume + 0.1);
                    if (volumeSlider) volumeSlider.value = audio.volume * 100;
                    updateVolumeIcon();
                    break;
                case 'ArrowDown':
                    // Flecha abajo: bajar volumen
                    e.preventDefault();
                    audio.volume = Math.max(0, audio.volume - 0.1);
                    if (volumeSlider) volumeSlider.value = audio.volume * 100;
                    updateVolumeIcon();
                    break;
                case 'm':
                    // M para mute
                    e.preventDefault();
                    if (volumeBtn) volumeBtn.click();
                    break;
            }
        });
    }
})();
