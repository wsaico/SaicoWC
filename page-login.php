<?php
/**
 * Template Name: Página de Login Personalizada
 * Login moderno con tabs y Material Design
 *
 * @version 2.0.0
 */

defined('ABSPATH') || exit;

// Redirigir si ya está logueado
if (is_user_logged_in()) {
    wp_redirect(home_url());
    exit;
}

get_header();
?>

<div class="saico-login-page">
    <div class="saico-login-container">

        <!-- Panel Izquierdo - Branding -->
        <div class="saico-login-panel saico-login-info">
            <div class="login-info-content">

                <div class="login-logo">
                    <?php
                    $custom_logo_id = get_theme_mod('custom_logo');
                    if ($custom_logo_id) {
                        $logo_url = wp_get_attachment_image_url($custom_logo_id, 'full');
                        echo '<img src="' . esc_url($logo_url) . '" alt="' . get_bloginfo('name') . '">';
                    } else {
                        echo '<h1>' . get_bloginfo('name') . '</h1>';
                    }
                    ?>
                </div>

                <div class="login-info-text">
                    <h2><?php echo get_theme_mod('login_welcome_title', '¡Bienvenido de vuelta!'); ?></h2>
                    <p><?php echo get_theme_mod('login_welcome_text', 'Inicia sesión para acceder a tu cuenta y disfrutar de todos nuestros servicios.'); ?></p>
                </div>

                <div class="login-features">
                    <div class="login-feature">
                        <div class="feature-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
                        </div>
                        <div class="feature-text">
                            <h4>Seguro</h4>
                            <p>Protección avanzada</p>
                        </div>
                    </div>

                    <div class="login-feature">
                        <div class="feature-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </div>
                        <div class="feature-text">
                            <h4>Rápido</h4>
                            <p>Acceso instantáneo</p>
                        </div>
                    </div>

                    <div class="login-feature">
                        <div class="feature-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <div class="feature-text">
                            <h4>Confiable</h4>
                            <p>Miles de usuarios</p>
                        </div>
                    </div>
                </div>

                <div class="login-info-bg">
                    <div class="bg-circle bg-circle-1"></div>
                    <div class="bg-circle bg-circle-2"></div>
                    <div class="bg-circle bg-circle-3"></div>
                </div>
            </div>
        </div>

        <!-- Panel Derecho - Formularios -->
        <div class="saico-login-panel saico-login-form-panel">
            <div class="login-form-container">

                <!-- Tabs -->
                <div class="login-tabs">
                    <button type="button" class="login-tab active" data-tab="login">Iniciar Sesión</button>
                    <?php if (get_option('users_can_register')) : ?>
                    <button type="button" class="login-tab" data-tab="register">Registrarse</button>
                    <?php endif; ?>
                </div>

                <!-- Mensajes -->
                <div id="saico-login-messages" class="login-messages" style="display: none;"></div>

                <!-- TAB: LOGIN -->
                <div id="tab-login" class="tab-content active">
                    <form id="saico-custom-login-form" class="saico-login-form" method="post" novalidate>

                        <div class="form-group">
                            <div class="form-input-wrapper">
                                <input
                                    type="text"
                                    id="saico_user_login"
                                    name="saico_user_login"
                                    class="form-input"
                                    placeholder=" "
                                    required
                                    autocomplete="username"
                                >
                                <label for="saico_user_login" class="form-label">Usuario o Email</label>
                                <span class="form-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="password-field form-input-wrapper">
                                <input
                                    type="password"
                                    id="saico_user_password"
                                    name="saico_user_password"
                                    class="form-input"
                                    placeholder=" "
                                    required
                                    autocomplete="current-password"
                                >
                                <label for="saico_user_password" class="form-label">Contraseña</label>
                                <span class="form-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>
                                </span>
                                <button type="button" class="toggle-password" aria-label="Mostrar/Ocultar">
                                    <svg class="eye-open" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <svg class="eye-closed" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" style="display: none;">
                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                        <line x1="1" y1="1" x2="23" y2="23"></line>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="form-options">
                            <label class="checkbox-label">
                                <input type="checkbox" id="saico_remember_me" name="saico_remember_me" value="1">
                                <span class="checkbox-custom"></span>
                                <span class="checkbox-text">Recordarme</span>
                            </label>

                            <a href="#" class="forgot-password" data-tab="recover">¿Olvidaste tu contraseña?</a>
                        </div>

                        <input type="text" name="saico_hp_field" style="display: none;" tabindex="-1" autocomplete="off">
                        <?php wp_nonce_field('saico_custom_login', 'saico_login_nonce'); ?>
                        <input type="hidden" id="saico_recaptcha_token" name="saico_recaptcha_token">

                        <button type="submit" class="btn-login" id="saico-login-submit">
                            <span class="btn-text">Iniciar Sesión</span>
                            <span class="btn-loader" style="display: none;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <circle cx="12" cy="12" r="10"></circle>
                                </svg>
                            </span>
                        </button>
                    </form>

                    <?php
                    $google_enabled = get_theme_mod('enable_google_login', false);
                    $facebook_enabled = get_theme_mod('enable_facebook_login', false);

                    if ($google_enabled || $facebook_enabled) :
                    ?>
                    <div class="login-separator"><span>o continúa con</span></div>

                    <div class="social-login">
                        <?php if ($google_enabled) : ?>
                        <button type="button" class="social-btn social-google" onclick="saicoGoogleLogin()">
                            <svg width="20" height="20" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            Google
                        </button>
                        <?php endif; ?>

                        <?php if ($facebook_enabled) : ?>
                        <button type="button" class="social-btn social-facebook" onclick="saicoFacebookLogin()">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="#1877f2">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            Facebook
                        </button>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- TAB: REGISTRO -->
                <?php if (get_option('users_can_register')) : ?>
                <div id="tab-register" class="tab-content">
                    <form id="saico-custom-register-form" class="saico-register-form" method="post" novalidate>

                        <div class="form-group">
                            <div class="form-input-wrapper">
                                <input type="text" id="saico_reg_username" name="saico_reg_username" class="form-input" placeholder=" " required>
                                <label for="saico_reg_username" class="form-label">Usuario</label>
                                <span class="form-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="12" cy="7" r="4"></circle>
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="form-input-wrapper">
                                <input type="email" id="saico_reg_email" name="saico_reg_email" class="form-input" placeholder=" " required>
                                <label for="saico_reg_email" class="form-label">Email</label>
                                <span class="form-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                        <polyline points="22,6 12,13 2,6"></polyline>
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="password-field form-input-wrapper">
                                <input type="password" id="saico_reg_password" name="saico_reg_password" class="form-input" placeholder=" " required>
                                <label for="saico_reg_password" class="form-label">Contraseña</label>
                                <span class="form-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                    </svg>
                                </span>
                                <button type="button" class="toggle-password">
                                    <svg class="eye-open" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <svg class="eye-closed" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" style="display: none;">
                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                        <line x1="1" y1="1" x2="23" y2="23"></line>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <input type="text" name="saico_hp_field_reg" style="display: none;" tabindex="-1" autocomplete="off">
                        <?php wp_nonce_field('saico_custom_register', 'saico_register_nonce'); ?>

                        <button type="submit" class="btn-register">
                            <span class="btn-text">Crear Cuenta</span>
                            <span class="btn-loader" style="display: none;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <circle cx="12" cy="12" r="10"></circle>
                                </svg>
                            </span>
                        </button>
                    </form>
                </div>
                <?php endif; ?>

                <!-- TAB: RECUPERAR CONTRASEÑA -->
                <div id="tab-recover" class="tab-content">
                    <form id="saico-custom-recover-form" class="saico-recover-form" method="post" novalidate>

                        <div class="form-group">
                            <div class="form-input-wrapper">
                                <input type="email" id="saico_recover_email" name="saico_recover_email" class="form-input" placeholder=" " required>
                                <label for="saico_recover_email" class="form-label">Email</label>
                                <span class="form-icon">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                        <polyline points="22,6 12,13 2,6"></polyline>
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <?php wp_nonce_field('saico_custom_recover', 'saico_recover_nonce'); ?>

                        <button type="submit" class="btn-recover">
                            <span class="btn-text">Recuperar Contraseña</span>
                            <span class="btn-loader" style="display: none;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <circle cx="12" cy="12" r="10"></circle>
                                </svg>
                            </span>
                        </button>

                        <div class="login-footer">
                            <p><a href="#" data-tab="login">Volver al inicio de sesión</a></p>
                        </div>
                    </form>
                </div>

                <!-- reCAPTCHA Badge -->
                <div class="recaptcha-badge">
                    <small>
                        Protegido por reCAPTCHA de Google.
                        <a href="https://policies.google.com/privacy" target="_blank">Privacidad</a> y
                        <a href="https://policies.google.com/terms" target="_blank">Términos</a>
                    </small>
                </div>

            </div>
        </div>

    </div>

    <!-- Partículas de fondo -->
    <div class="login-particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>
</div>

<?php
get_footer();
