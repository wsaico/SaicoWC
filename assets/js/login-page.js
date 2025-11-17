/**
 * Login Page JavaScript
 * Sistema de tabs, labels flotantes y login con AJAX
 */

(function($) {
    'use strict';

    $(document).ready(function() {

        // ====================================================================
        // SISTEMA DE TABS
        // ====================================================================
        $('.login-tab').on('click', function(e) {
            e.preventDefault();

            const targetTab = $(this).data('tab');

            // Cambiar tab activo
            $('.login-tab').removeClass('active');
            $(this).addClass('active');

            // Mostrar contenido correspondiente
            $('.tab-content').removeClass('active');
            $(`#tab-${targetTab}`).addClass('active');

            // Limpiar mensajes
            $('#saico-login-messages').hide();
        });

        // Link "¿Olvidaste tu contraseña?" abre formulario de recuperación
        $('.forgot-password').on('click', function(e) {
            e.preventDefault();

            // Mostrar formulario de recuperación
            $('.tab-content').removeClass('active');
            $('#tab-recover').addClass('active');

            // Limpiar mensajes
            $('#saico-login-messages').hide();
        });

        // Link "Volver al inicio de sesión" desde recuperar contraseña
        $('.login-footer a[data-tab="login"]').on('click', function(e) {
            e.preventDefault();

            // Mostrar formulario de login
            $('.tab-content').removeClass('active');
            $('#tab-login').addClass('active');

            // Activar tab de login
            $('.login-tab').removeClass('active');
            $('.login-tab[data-tab="login"]').addClass('active');

            // Limpiar mensajes
            $('#saico-login-messages').hide();
        });

        // ====================================================================
        // TOGGLE PASSWORD VISIBILITY
        // ====================================================================
        $('.toggle-password').on('click', function() {
            const $button = $(this);
            const $input = $button.siblings('.form-input');
            const $eyeOpen = $button.find('.eye-open');
            const $eyeClosed = $button.find('.eye-closed');

            if ($input.attr('type') === 'password') {
                $input.attr('type', 'text');
                $eyeOpen.hide();
                $eyeClosed.show();
            } else {
                $input.attr('type', 'password');
                $eyeOpen.show();
                $eyeClosed.hide();
            }
        });

        // ====================================================================
        // VALIDACIÓN DE FORMULARIOS
        // ====================================================================
        function validateForm($form) {
            let isValid = true;

            $form.find('.form-input[required]').each(function() {
                if ($(this).val().trim() === '') {
                    isValid = false;
                    $(this).addClass('input-error');
                } else {
                    $(this).removeClass('input-error');
                }
            });

            return isValid;
        }

        // Limpiar error al escribir
        $('.form-input').on('input', function() {
            $(this).removeClass('input-error');
        });

        // ====================================================================
        // MOSTRAR MENSAJES
        // ====================================================================
        function showMessage(message, type) {
            const $messagesDiv = $('#saico-login-messages').length ? $('#saico-login-messages') : $('#saico-login-messages-modal');

            $messagesDiv
                .removeClass('success error')
                .addClass(type)
                .html(message)
                .slideDown(300);

            if (type === 'success') {
                setTimeout(function() {
                    $messagesDiv.slideUp(300);
                }, 5000);
            }

            // Scroll al mensaje
            $('html, body').animate({
                scrollTop: $messagesDiv.offset().top - 100
            }, 300);
        }

        // ====================================================================
        // SUBMIT LOGIN (página y modal)
        // ====================================================================
        $('#saico-custom-login-form, #saico-custom-login-form-modal').on('submit', function(e) {
            e.preventDefault();

            const $form = $(this);

            if (!validateForm($form)) {
                showMessage('Por favor, completa todos los campos.', 'error');
                return;
            }

            const $submitBtn = $form.find('button[type="submit"]');
            const $btnText = $submitBtn.find('.btn-text');
            const $btnLoader = $submitBtn.find('.btn-loader');

            // Deshabilitar botón
            $submitBtn.prop('disabled', true);
            $btnText.hide();
            $btnLoader.show();

            // Verificar honeypot
            if ($('input[name="saico_hp_field"]').val() !== '') {
                showMessage('Error de seguridad. Intenta nuevamente.', 'error');
                $submitBtn.prop('disabled', false);
                $btnText.show();
                $btnLoader.hide();
                return;
            }

            // Ejecutar reCAPTCHA si está disponible
            if (typeof grecaptcha !== 'undefined' && window.saicoRecaptchaSiteKey) {
                grecaptcha.ready(function() {
                    grecaptcha.execute(window.saicoRecaptchaSiteKey, {action: 'login'})
                        .then(function(token) {
                            if ($form.find('#saico_recaptcha_token').length) {
                                $form.find('#saico_recaptcha_token').val(token);
                            } else if ($form.find('#saico_recaptcha_token_modal').length) {
                                $form.find('#saico_recaptcha_token_modal').val(token);
                            }
                            submitLogin($form, $submitBtn, $btnText, $btnLoader);
                        });
                });
            } else {
                submitLogin($form, $submitBtn, $btnText, $btnLoader);
            }
        });

        function submitLogin($form, $submitBtn, $btnText, $btnLoader) {
            $.ajax({
                url: saicoLogin.ajaxUrl,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'saico_custom_login',
                    username: ($form.find('#saico_user_login').val() || $form.find('#saico_modal_user_login').val() || ''),
                    password: ($form.find('#saico_user_password').val() || $form.find('#saico_modal_user_password').val() || ''),
                    remember: ($form.find('#saico_remember_me').is(':checked') || $form.find('#saico_modal_remember_me').is(':checked')) ? '1' : '0',
                    recaptcha_token: ($form.find('#saico_recaptcha_token').val() || $form.find('#saico_recaptcha_token_modal').val() || ''),
                    nonce: $form.find('input[name="saico_login_nonce"]').val()
                },
                success: function(response) {
                    if (response.success) {
                        showMessage(response.data.message || '¡Inicio de sesión exitoso! Redirigiendo...', 'success');
                        setTimeout(function() {
                            window.location.href = response.data.redirect || saicoLogin.homeUrl;
                        }, 1000);
                    } else {
                        showMessage(response.data.message || 'Error al iniciar sesión. Verifica tus credenciales.', 'error');
                        $submitBtn.prop('disabled', false);
                        $btnText.show();
                        $btnLoader.hide();
                    }
                },
                error: function() {
                    showMessage('Error de conexión. Por favor, intenta nuevamente.', 'error');
                    $submitBtn.prop('disabled', false);
                    $btnText.show();
                    $btnLoader.hide();
                }
            });
        }

        // ====================================================================
        // SUBMIT REGISTRO (página y modal)
        // ====================================================================
        $('#saico-custom-register-form, #saico-custom-register-form-modal').on('submit', function(e) {
            e.preventDefault();

            const $form = $(this);

            if (!validateForm($form)) {
                showMessage('Por favor, completa todos los campos.', 'error');
                return;
            }

            const $submitBtn = $form.find('.btn-register');
            const $btnText = $submitBtn.find('.btn-text');
            const $btnLoader = $submitBtn.find('.btn-loader');

            $submitBtn.prop('disabled', true);
            $btnText.hide();
            $btnLoader.show();

            // Verificar honeypot
            if ($('input[name="saico_hp_field_reg"]').val() !== '') {
                showMessage('Error de seguridad. Intenta nuevamente.', 'error');
                $submitBtn.prop('disabled', false);
                $btnText.show();
                $btnLoader.hide();
                return;
            }

            $.ajax({
                url: saicoLogin.ajaxUrl,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'saico_custom_register',
                    username: ($form.find('#saico_reg_username').val() || $form.find('#saico_modal_reg_username').val() || ''),
                    email: ($form.find('#saico_reg_email').val() || $form.find('#saico_modal_reg_email').val() || ''),
                    password: ($form.find('#saico_reg_password').val() || $form.find('#saico_modal_reg_password').val() || ''),
                    nonce: $form.find('input[name="saico_register_nonce"]').val()
                },
                success: function(response) {
                    if (response.success) {
                        showMessage(response.data.message || '¡Cuenta creada exitosamente! Redirigiendo...', 'success');
                        setTimeout(function() {
                            window.location.href = response.data.redirect || saicoLogin.homeUrl;
                        }, 1500);
                    } else {
                        showMessage(response.data.message || 'Error al crear la cuenta.', 'error');
                        $submitBtn.prop('disabled', false);
                        $btnText.show();
                        $btnLoader.hide();
                    }
                },
                error: function() {
                    showMessage('Error de conexión. Por favor, intenta nuevamente.', 'error');
                    $submitBtn.prop('disabled', false);
                    $btnText.show();
                    $btnLoader.hide();
                }
            });
        });

        // ====================================================================
        // SUBMIT RECUPERAR CONTRASEÑA (página y modal)
        // ====================================================================
        $('#saico-custom-recover-form, #saico-custom-recover-form-modal').on('submit', function(e) {
            e.preventDefault();

            const $form = $(this);

            if (!validateForm($form)) {
                showMessage('Por favor, ingresa tu email.', 'error');
                return;
            }

            const $submitBtn = $form.find('.btn-recover');
            const $btnText = $submitBtn.find('.btn-text');
            const $btnLoader = $submitBtn.find('.btn-loader');

            $submitBtn.prop('disabled', true);
            $btnText.hide();
            $btnLoader.show();

            $.ajax({
                url: saicoLogin.ajaxUrl,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'saico_custom_recover',
                    email: ($form.find('#saico_recover_email').val() || $form.find('#saico_modal_recover_email').val() || ''),
                    nonce: $form.find('input[name="saico_recover_nonce"]').val()
                },
                success: function(response) {
                    if (response.success) {
                        showMessage(response.data.message || 'Revisa tu email para recuperar tu contraseña.', 'success');
                        $form[0].reset();
                    } else {
                        showMessage(response.data.message || 'Error al procesar la solicitud.', 'error');
                    }
                    $submitBtn.prop('disabled', false);
                    $btnText.show();
                    $btnLoader.hide();
                },
                error: function() {
                    showMessage('Error de conexión. Por favor, intenta nuevamente.', 'error');
                    $submitBtn.prop('disabled', false);
                    $btnText.show();
                    $btnLoader.hide();
                }
            });
        });

        // ====================================================================
        // PREVENIR AUTOFILL DE HONEYPOT
        // ====================================================================
        $('input[name="saico_hp_field"], input[name="saico_hp_field_reg"]').val('');

        setTimeout(function() {
            $('input[name="saico_hp_field"], input[name="saico_hp_field_reg"]').each(function() {
                if ($(this).val() !== '') {
                    $(this).val('');
                }
            });
        }, 1000);

    });

})(jQuery);

// ====================================================================
// FUNCIONES GLOBALES PARA LOGIN SOCIAL
// ====================================================================

function saicoGoogleLogin() {
    // Verificar si saicoSocialLogin está disponible
    if (typeof saicoSocialLogin === 'undefined') {
        alert('Error: Configuración de social login no disponible');
        return;
    }

    jQuery.ajax({
        url: saicoSocialLogin.ajaxUrl,
        type: 'POST',
        data: {
            action: 'saico_google_oauth_start',
            nonce: saicoSocialLogin.nonce
        },
        success: function(response) {
            if (response.success && response.data.auth_url) {
                // Redirigir a la página de autorización de Google
                window.location.href = response.data.auth_url;
            } else {
                alert(response.data.message || 'Error al iniciar login con Google. Verifica la configuración en Apariencia > Personalizar > Login & Seguridad.');
            }
        },
        error: function() {
            alert('Error de conexión al iniciar login con Google');
        }
    });
}

function saicoFacebookLogin() {
    // Verificar si saicoSocialLogin está disponible
    if (typeof saicoSocialLogin === 'undefined') {
        alert('Error: Configuración de social login no disponible');
        return;
    }

    jQuery.ajax({
        url: saicoSocialLogin.ajaxUrl,
        type: 'POST',
        data: {
            action: 'saico_facebook_oauth_start',
            nonce: saicoSocialLogin.nonce
        },
        success: function(response) {
            if (response.success && response.data.auth_url) {
                // Redirigir a la página de autorización de Facebook
                window.location.href = response.data.auth_url;
            } else {
                alert(response.data.message || 'Error al iniciar login con Facebook. Verifica la configuración en Apariencia > Personalizar > Login & Seguridad.');
            }
        },
        error: function() {
            alert('Error de conexión al iniciar login con Facebook');
        }
    });
}
