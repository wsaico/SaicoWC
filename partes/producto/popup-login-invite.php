<?php
/**
 * Popup de Invitación a Login/Registro
 * Se muestra a usuarios no logueados para incentivar el registro
 * Beneficio: Descargas directas sin temporizadores ni esperas
 */

if (!defined('ABSPATH')) exit;

// Obtener URL de la página de login
$login_page_id = get_option('saico_custom_login_page_id');
if (!$login_page_id) {
    $pages = get_pages(array(
        'meta_key' => '_wp_page_template',
        'meta_value' => 'page-login.php'
    ));
    if (!empty($pages)) {
        $login_page_id = $pages[0]->ID;
    }
}
$login_url = $login_page_id ? get_permalink($login_page_id) : wp_login_url(get_permalink());
?>

<!-- Popup Principal de Invitación a Login -->
<div id="saico-login-invite-popup" class="saico-login-invite-popup" style="display: none;">
    <div class="login-invite-overlay"></div>

    <div class="login-invite-card">
        <!-- Icono -->
        <div class="invite-icon">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M13 10V3L4 14h7v7l9-11h-7z"></path>
            </svg>
        </div>

        <!-- Contenido -->
        <h3>¡Descarga Directa Disponible!</h3>
        <p class="invite-description">
            Regístrate o inicia sesión para descargar sin esperas ni temporizadores.
            ¡Es gratis y solo toma segundos!
        </p>

        <!-- Beneficios -->
        <div class="invite-benefits">
            <div class="benefit-item">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>Descargas instantáneas</span>
            </div>
            <div class="benefit-item">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>Sin temporizadores</span>
            </div>
            <div class="benefit-item">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span>Acceso ilimitado</span>
            </div>
        </div>

        <!-- Botones -->
        <div class="invite-buttons">
            <button type="button" class="invite-btn invite-btn-primary" onclick="saicoOpenModalLoginFromInvite('login')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                    <polyline points="10 17 15 12 10 7"></polyline>
                    <line x1="15" y1="12" x2="3" y2="12"></line>
                </svg>
                Iniciar Sesión
            </button>
            <button type="button" class="invite-btn invite-btn-secondary" onclick="saicoContinuarConEspera()">
                Continuar con espera
            </button>
        </div>

        <!-- Nota pequeña -->
        <p class="invite-note">
            ¿No tienes cuenta? <button type="button" class="invite-link-btn" onclick="saicoOpenModalLoginFromInvite('register')">Regístrate gratis</button>
        </p>

        <!-- Botón cerrar -->
        <button class="invite-close" onclick="saicoCloseLoginInvite()" aria-label="Cerrar">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>
</div>

<!-- Popup Pequeño - Esquina Inferior Derecha -->
<div id="saico-corner-login-popup" class="saico-corner-login-popup" style="display: none;">
    <div class="corner-popup-card">
        <!-- Botón cerrar -->
        <button class="corner-close" onclick="saicoCloseCornerPopup()" aria-label="Cerrar">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>

        <!-- Contenido compacto -->
        <h4>Has cerrado sesión</h4>
        <p>Inicia sesión para comenzar la descarga</p>

        <!-- Botones horizontales -->
        <div class="corner-buttons">
            <button type="button" class="corner-btn corner-btn-primary" onclick="saicoOpenModalLoginFromInvite('login')">
                Iniciar sesión
            </button>
            <button type="button" class="corner-btn corner-btn-secondary" onclick="saicoOpenModalLoginFromInvite('register')">
                Registrarse
            </button>
        </div>
    </div>
</div>

<style>
/* ========================================================================
   POPUP PRINCIPAL - Centro de Pantalla
   ======================================================================== */
.saico-login-invite-popup {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 10000;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
    box-sizing: border-box;
}

.saico-login-invite-popup.active {
    display: flex;
}

.login-invite-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(4px);
    animation: saicoFadeIn 0.3s ease;
}

.login-invite-card {
    position: relative;
    background: white;
    border-radius: 20px;
    padding: 40px 32px;
    max-width: 480px;
    width: 100%;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    text-align: center;
    animation: saicoScaleIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.invite-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 24px;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 24px rgba(16, 185, 129, 0.3);
}

.invite-icon svg {
    stroke: white;
}

.login-invite-card h3 {
    font-size: 26px;
    font-weight: 700;
    color: #1f2937;
    margin: 0 0 12px;
}

.invite-description {
    font-size: 15px;
    color: #6b7280;
    line-height: 1.6;
    margin: 0 0 28px;
}

.invite-benefits {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 28px;
    text-align: left;
}

.benefit-item {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 14px;
    color: #374151;
    font-weight: 500;
}

.benefit-item svg {
    flex-shrink: 0;
}

.invite-buttons {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 16px;
}

.invite-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 14px 24px;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
}

.invite-btn-primary {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
}

.invite-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
}

.invite-btn-secondary {
    background: #f3f4f6;
    color: #6b7280;
}

.invite-btn-secondary:hover {
    background: #e5e7eb;
    color: #374151;
}

.invite-note {
    font-size: 13px;
    color: #9ca3af;
    margin: 0;
}

.invite-note a,
.invite-link-btn {
    color: #10b981;
    text-decoration: none;
    font-weight: 600;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
    font-size: inherit;
    font-family: inherit;
}

.invite-note a:hover,
.invite-link-btn:hover {
    text-decoration: underline;
    color: #059669;
}

.invite-close {
    position: absolute;
    top: 16px;
    right: 16px;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: #f3f4f6;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.invite-close:hover {
    background: #e5e7eb;
    transform: rotate(90deg);
}

.invite-close svg {
    stroke: #6b7280;
}

/* ========================================================================
   POPUP DE ESQUINA - Inferior Derecha (Estilo Imagen de Referencia)
   ======================================================================== */
.saico-corner-login-popup {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
    display: none;
}

.saico-corner-login-popup.active {
    display: block;
    animation: saicoSlideInRight 0.3s ease;
}

.corner-popup-card {
    position: relative;
    background: white;
    border-radius: 12px;
    padding: 20px 24px;
    max-width: 360px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    border: 1px solid #e5e7eb;
}

.corner-popup-card h4 {
    font-size: 16px;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 6px;
    line-height: 1.3;
}

.corner-popup-card p {
    font-size: 13px;
    color: #6b7280;
    line-height: 1.5;
    margin: 0 0 16px;
}

.corner-buttons {
    display: flex;
    gap: 8px;
}

.corner-btn {
    flex: 1;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 9px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

.corner-btn-primary {
    background: #10b981;
    color: white;
}

.corner-btn-primary:hover {
    background: #059669;
}

.corner-btn-secondary {
    background: #f3f4f6;
    color: #374151;
}

.corner-btn-secondary:hover {
    background: #e5e7eb;
}

.corner-close {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 24px;
    height: 24px;
    border-radius: 6px;
    background: transparent;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.corner-close:hover {
    background: #f3f4f6;
}

.corner-close svg {
    stroke: #9ca3af;
}

.corner-close:hover svg {
    stroke: #374151;
}

/* ========================================================================
   ANIMACIONES
   ======================================================================== */
@keyframes saicoFadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes saicoScaleIn {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes saicoSlideInRight {
    from {
        opacity: 0;
        transform: translateX(100px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* ========================================================================
   RESPONSIVE
   ======================================================================== */
@media (max-width: 640px) {
    .login-invite-card {
        padding: 32px 24px;
        border-radius: 16px;
    }

    .invite-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        margin-bottom: 20px;
    }

    .invite-icon svg {
        width: 36px;
        height: 36px;
    }

    .login-invite-card h3 {
        font-size: 22px;
    }

    .invite-description {
        font-size: 14px;
        margin-bottom: 24px;
    }

    .invite-benefits {
        gap: 10px;
        margin-bottom: 24px;
    }

    .benefit-item {
        font-size: 13px;
    }

    .invite-btn {
        padding: 12px 20px;
        font-size: 14px;
    }

    .invite-note {
        font-size: 12px;
    }

    /* Ocultar popup de esquina en móvil */
    .saico-corner-login-popup {
        display: none !important;
    }
}
</style>

<script>
// ========================================================================
// POPUP PRINCIPAL - Centro de Pantalla
// ========================================================================
window.saicoShowLoginInvite = function() {
    const popup = document.getElementById('saico-login-invite-popup');
    if (popup) {
        popup.classList.add('active');
        popup.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
};

window.saicoCloseLoginInvite = function() {
    const popup = document.getElementById('saico-login-invite-popup');
    if (popup) {
        popup.classList.remove('active');
        popup.style.display = 'none';
        document.body.style.overflow = '';

        // NO activar temporizador al cerrar el popup
        // El usuario debe elegir explícitamente "Continuar con espera"
        // Cerrar con X = cancelar todo
    }
};

// ========================================================================
// POPUP DE ESQUINA - Inferior Derecha
// ========================================================================
window.saicoShowCornerPopup = function() {
    const isMobile = window.matchMedia('(max-width: 640px)').matches;
    if (isMobile) return;
    const cornerPopup = document.getElementById('saico-corner-login-popup');
    if (cornerPopup) {
        cornerPopup.classList.add('active');
        cornerPopup.style.display = 'block';
    }
};

window.saicoCloseCornerPopup = function() {
    const cornerPopup = document.getElementById('saico-corner-login-popup');
    if (cornerPopup) {
        cornerPopup.classList.remove('active');
        cornerPopup.style.display = 'none';
    }
};

// Función para abrir modal de login desde popups
window.saicoOpenModalLoginFromInvite = function(tab) {
    // Cerrar popup de invitación
    saicoCloseLoginInvite();
    saicoCloseCornerPopup();

    // Abrir modal de login con el tab especificado
    setTimeout(function() {
        // Resetear el modal primero
        if (typeof saicoResetLoginModal === 'function') {
            saicoResetLoginModal();
        }

        // Abrir modal
        saicoAbrirModal('saico-login-modal');

        // Cambiar al tab especificado si no es login (que ya es el default)
        if (tab === 'register') {
            setTimeout(function() {
                jQuery('.saico-login-modal .login-tab').removeClass('active');
                jQuery('.saico-login-modal .login-tab[data-tab="register"]').addClass('active');

                jQuery('.saico-login-modal .tab-content').removeClass('active');
                jQuery('#tab-register-modal').addClass('active');
            }, 100);
        }
    }, 300);
};

// Función cuando el usuario elige "Continuar con espera"
window.saicoContinuarConEspera = function() {
    // Cerrar popups
    saicoCloseLoginInvite();
    saicoCloseCornerPopup();

    // Detectar qué sistema de descarga está activo
    const pageViewEnabled = typeof saicoPageViewContinuarConEspera === 'function';
    const modalEnabled = typeof saicoModalContinuarConEspera === 'function';
    const animatedButtonEnabled = typeof saicoAnimatedButtonContinuarConEspera === 'function';

    // Priorizar en este orden: vista de página > modal > botón animado
    if (pageViewEnabled) {
        setTimeout(function() {
            saicoPageViewContinuarConEspera();
        }, 300);
    } else if (modalEnabled) {
        setTimeout(function() {
            saicoModalContinuarConEspera();
        }, 300);
    } else if (animatedButtonEnabled) {
        setTimeout(function() {
            saicoAnimatedButtonContinuarConEspera();
        }, 300);
    } else {
        console.error('No se encontró ningún sistema de descarga activo');
    }
};

// Cerrar con overlay del popup principal
jQuery(document).ready(function($) {
    $('.login-invite-overlay').on('click', function() {
        saicoCloseLoginInvite();
    });

    // ========================================================================
    // AUTO-MOSTRAR POPUP DE ESQUINA PARA USUARIOS NO LOGUEADOS
    // ========================================================================
    // Mostrar el popup de esquina automáticamente después de 2 segundos
    // Solo para usuarios NO logueados
    setTimeout(function() {
        const isLoggedIn = (typeof saicoData !== 'undefined' && saicoData.isLoggedIn) ? saicoData.isLoggedIn : false;

        // Si NO está logueado, mostrar el popup de esquina
        if (!isLoggedIn) {
            saicoShowCornerPopup();
        }
    }, 2000);

    // Prevenir que el popup de esquina se cierre permanentemente
    // Solo permitir cierre temporal (se volverá a mostrar si refresca la página)
    const originalCloseCornerPopup = window.saicoCloseCornerPopup;
    window.saicoCloseCornerPopup = function() {
        const cornerPopup = document.getElementById('saico-corner-login-popup');
        if (cornerPopup) {
            cornerPopup.classList.remove('active');
            cornerPopup.style.display = 'none';

            // Volver a mostrar después de 30 segundos si sigue sin loguearse
            setTimeout(function() {
                const isLoggedIn = (typeof saicoData !== 'undefined' && saicoData.isLoggedIn) ? saicoData.isLoggedIn : false;
                if (!isLoggedIn) {
                    saicoShowCornerPopup();
                }
            }, 30000); // 30 segundos
        }
    };
});
</script>
