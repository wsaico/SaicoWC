<?php
/**
 * Modal Login/Registro - Réplica exacta de eSaicoWC
 */

if (!defined('ABSPATH')) exit;
?>

<div id="saico-login-modal" class="saico-modal saico-login-modal">
    <div class="saico-modal-overlay" onclick="saicoCerrarModal('saico-login-modal')"></div>
    <div class="saico-modal-contenido login-modal-content">
        <button class="modal-cerrar" onclick="saicoCerrarModal('saico-login-modal')">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>

        <!-- Tabs -->
        <div class="login-tabs">
            <button class="login-tab activo" data-tab="login" onclick="saicoSwitchLoginTab('login')">
                Iniciar Sesión
            </button>
            <button class="login-tab" data-tab="register" onclick="saicoSwitchLoginTab('register')">
                Registrarse
            </button>
        </div>

        <!-- Tab Content: Login -->
        <div id="login-tab-content" class="login-tab-content activo">
            <h3>Bienvenido de nuevo</h3>
            <p class="tab-subtitle">Ingresa tus credenciales para continuar</p>

            <form class="woocommerce-form woocommerce-form-login login" method="post">
                <?php do_action('woocommerce_login_form_start'); ?>

                <div class="form-group">
                    <label for="username">Usuario o correo electrónico <span class="required">*</span></label>
                    <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" required />
                </div>

                <div class="form-group">
                    <label for="password">Contraseña <span class="required">*</span></label>
                    <input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" required />
                </div>

                <?php do_action('woocommerce_login_form'); ?>

                <div class="form-row form-remember">
                    <label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
                        <input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" />
                        <span>Recordarme</span>
                    </label>

                    <a href="<?php echo esc_url(wp_lostpassword_url()); ?>" class="forgot-password">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>

                <?php wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce'); ?>
                <button type="submit" class="woocommerce-button button woocommerce-form-login__submit saico-btn-primario" name="login" value="<?php esc_attr_e('Log in', 'woocommerce'); ?>">
                    Iniciar Sesión
                </button>

                <?php do_action('woocommerce_login_form_end'); ?>
            </form>
        </div>

        <!-- Tab Content: Register -->
        <div id="register-tab-content" class="login-tab-content">
            <h3>Crear cuenta nueva</h3>
            <p class="tab-subtitle">Completa el formulario para registrarte</p>

            <?php if (get_option('woocommerce_enable_myaccount_registration') === 'yes') : ?>

            <form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action('woocommerce_register_form_tag'); ?>>

                <?php do_action('woocommerce_register_form_start'); ?>

                <div class="form-group">
                    <label for="reg_username">Usuario <span class="required">*</span></label>
                    <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" required />
                </div>

                <div class="form-group">
                    <label for="reg_email">Correo electrónico <span class="required">*</span></label>
                    <input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" required />
                </div>

                <?php if ('no' === get_option('woocommerce_registration_generate_password')) : ?>

                <div class="form-group">
                    <label for="reg_password">Contraseña <span class="required">*</span></label>
                    <input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" required />
                </div>

                <?php else : ?>

                <p class="password-info">Se enviará un enlace a tu correo electrónico para establecer una nueva contraseña.</p>

                <?php endif; ?>

                <?php do_action('woocommerce_register_form'); ?>

                <?php wp_nonce_field('woocommerce-register', 'woocommerce-register-nonce'); ?>
                <button type="submit" class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit saico-btn-primario" name="register" value="<?php esc_attr_e('Register', 'woocommerce'); ?>">
                    Crear Cuenta
                </button>

                <?php do_action('woocommerce_register_form_end'); ?>

            </form>

            <?php else : ?>

            <p class="registration-disabled">El registro de nuevos usuarios no está permitido actualmente.</p>

            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* Modal Login Base */
.saico-login-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 99999;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 20px;
}

.saico-login-modal.activo {
    display: flex;
}

.saico-modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(4px);
}

.login-modal-content {
    position: relative;
    z-index: 1;
    background: white;
    border-radius: 16px;
    max-width: 460px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    padding: 32px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    animation: slideUp 0.3s ease-out;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(40px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modal-cerrar {
    position: absolute;
    top: 16px;
    right: 16px;
    width: 36px;
    height: 36px;
    border: none;
    background: #f3f4f6;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    z-index: 10;
}

.modal-cerrar:hover {
    background: #e5e7eb;
    transform: rotate(90deg);
}

.modal-cerrar svg {
    color: #6b7280;
}

/* Tabs */
.login-tabs {
    display: flex;
    gap: 8px;
    margin-bottom: 24px;
    border-bottom: 2px solid #e5e7eb;
}

.login-tab {
    flex: 1;
    padding: 12px 20px;
    background: none;
    border: none;
    border-bottom: 3px solid transparent;
    font-size: 15px;
    font-weight: 500;
    color: #6b7280;
    cursor: pointer;
    transition: all 0.2s;
    margin-bottom: -2px;
}

.login-tab.activo {
    color: #10b981;
    border-bottom-color: #10b981;
}

.login-tab:hover:not(.activo) {
    color: #374151;
}

/* Tab Content */
.login-tab-content {
    display: none;
}

.login-tab-content.activo {
    display: block;
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.login-tab-content h3 {
    font-size: 24px;
    font-weight: 700;
    color: #1f2937;
    margin: 0 0 8px 0;
}

.tab-subtitle {
    font-size: 14px;
    color: #6b7280;
    margin: 0 0 24px 0;
}

/* Form Groups */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    margin-bottom: 8px;
}

.form-group .required {
    color: #ef4444;
}

.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="password"] {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.2s;
    background: white;
}

.form-group input:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

/* Remember & Forgot */
.form-remember {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}

.form-remember label {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    font-size: 14px;
    color: #374151;
    margin: 0;
}

.form-remember input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.forgot-password {
    font-size: 14px;
    color: #10b981;
    text-decoration: none;
    transition: color 0.2s;
}

.forgot-password:hover {
    color: #059669;
    text-decoration: underline;
}

/* Submit Button */
.saico-btn-primario {
    width: 100%;
    padding: 14px 24px;
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.saico-btn-primario:hover {
    background: linear-gradient(135deg, #059669, #047857);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
}

.saico-btn-primario:active {
    transform: translateY(0);
}

/* Password Info */
.password-info {
    padding: 12px;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 8px;
    font-size: 13px;
    color: #166534;
    margin-bottom: 20px;
}

/* Registration Disabled */
.registration-disabled {
    padding: 20px;
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 8px;
    color: #991b1b;
    text-align: center;
}

/* Responsive */
@media (max-width: 768px) {
    .login-modal-content {
        padding: 24px;
        border-radius: 12px;
        max-height: 95vh;
    }

    .login-tab-content h3 {
        font-size: 20px;
    }

    .form-group input[type="text"],
    .form-group input[type="email"],
    .form-group input[type="password"] {
        padding: 10px 14px;
    }

    .saico-btn-primario {
        padding: 12px 20px;
    }
}

/* WooCommerce Messages in Modal */
.login-modal-content .woocommerce-error,
.login-modal-content .woocommerce-message,
.login-modal-content .woocommerce-info {
    margin-bottom: 20px;
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 14px;
}

.login-modal-content .woocommerce-error {
    background: #fef2f2;
    border-left: 4px solid #ef4444;
    color: #991b1b;
}

.login-modal-content .woocommerce-message {
    background: #f0fdf4;
    border-left: 4px solid #10b981;
    color: #166534;
}
</style>

<script>
// Funciones del modal de login
window.saicoAbrirModalLogin = function(tab) {
    saicoAbrirModal('saico-login-modal');

    if (tab) {
        saicoSwitchLoginTab(tab);
    }
};

window.saicoSwitchLoginTab = function(tab) {
    // Remover clase activo de todos los tabs
    document.querySelectorAll('.login-tab').forEach(function(el) {
        el.classList.remove('activo');
    });

    document.querySelectorAll('.login-tab-content').forEach(function(el) {
        el.classList.remove('activo');
    });

    // Activar tab seleccionado
    const tabBtn = document.querySelector('.login-tab[data-tab="' + tab + '"]');
    const tabContent = document.getElementById(tab + '-tab-content');

    if (tabBtn && tabContent) {
        tabBtn.classList.add('activo');
        tabContent.classList.add('activo');
    }
};
</script>
