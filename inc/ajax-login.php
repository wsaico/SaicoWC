<?php
/**
 * AJAX Login/Register Handlers
 * Maneja login y registro sin recargar la página
 *
 * @package SaicoWC
 * @version 1.0.0
 */

if (!defined('ABSPATH')) exit;

/**
 * AJAX Login Handler
 */
function saico_ajax_login_handler() {
    // Verificar nonce
    if (!isset($_POST['woocommerce-login-nonce']) || !wp_verify_nonce($_POST['woocommerce-login-nonce'], 'woocommerce-login')) {
        wp_send_json_error('Nonce inválido');
        return;
    }

    // Validar campos requeridos
    if (empty($_POST['username']) || empty($_POST['password'])) {
        wp_send_json_error('Por favor completa todos los campos');
        return;
    }

    $username = sanitize_user($_POST['username']);
    $password = $_POST['password'];
    $remember = isset($_POST['rememberme']) ? true : false;

    // Intentar login
    $creds = array(
        'user_login'    => $username,
        'user_password' => $password,
        'remember'      => $remember,
    );

    $user = wp_signon($creds, is_ssl());

    if (is_wp_error($user)) {
        wp_send_json_error($user->get_error_message());
        return;
    }

    // Login exitoso
    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID, $remember);

    // Ejecutar acciones de WooCommerce
    do_action('woocommerce_login_process');
    do_action('wp_login', $user->user_login, $user);

    wp_send_json_success(array(
        'message' => '¡Bienvenido de nuevo!',
        'redirect' => false, // No redirigir, manejar en frontend
    ));
}
add_action('wc_ajax_saico_ajax_login', 'saico_ajax_login_handler');
add_action('wp_ajax_saico_ajax_login', 'saico_ajax_login_handler');
add_action('wp_ajax_nopriv_saico_ajax_login', 'saico_ajax_login_handler');

/**
 * AJAX Register Handler
 */
function saico_ajax_register_handler() {
    // Verificar nonce
    if (!isset($_POST['woocommerce-register-nonce']) || !wp_verify_nonce($_POST['woocommerce-register-nonce'], 'woocommerce-register')) {
        wp_send_json_error('Nonce inválido');
        return;
    }

    // Verificar que el registro esté habilitado
    if (get_option('woocommerce_enable_myaccount_registration') !== 'yes') {
        wp_send_json_error('El registro no está habilitado');
        return;
    }

    // Validar campos requeridos
    if (empty($_POST['username']) || empty($_POST['email'])) {
        wp_send_json_error('Por favor completa todos los campos');
        return;
    }

    $username = sanitize_user($_POST['username']);
    $email = sanitize_email($_POST['email']);
    $password = !empty($_POST['password']) ? $_POST['password'] : wp_generate_password();

    // Validar email
    if (!is_email($email)) {
        wp_send_json_error('Por favor ingresa un correo electrónico válido');
        return;
    }

    // Verificar si el usuario ya existe
    if (username_exists($username)) {
        wp_send_json_error('El nombre de usuario ya está en uso');
        return;
    }

    if (email_exists($email)) {
        wp_send_json_error('El correo electrónico ya está registrado');
        return;
    }

    // Crear usuario
    $user_id = wp_create_user($username, $password, $email);

    if (is_wp_error($user_id)) {
        wp_send_json_error($user_id->get_error_message());
        return;
    }

    // Actualizar rol a cliente de WooCommerce
    $user = get_user_by('id', $user_id);
    $user->set_role('customer');

    // Ejecutar acciones de WooCommerce
    do_action('woocommerce_created_customer', $user_id);
    do_action('woocommerce_registration_redirect', $user);

    // Auto-login después de registro
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id);
    do_action('wp_login', $user->user_login, $user);

    // Enviar email de bienvenida si está configurado
    if (get_option('woocommerce_registration_generate_password') === 'yes') {
        wp_new_user_notification($user_id, null, 'user');
    }

    wp_send_json_success(array(
        'message' => '¡Cuenta creada exitosamente!',
        'redirect' => false,
    ));
}
add_action('wc_ajax_saico_ajax_register', 'saico_ajax_register_handler');
add_action('wp_ajax_saico_ajax_register', 'saico_ajax_register_handler');
add_action('wp_ajax_nopriv_saico_ajax_register', 'saico_ajax_register_handler');
