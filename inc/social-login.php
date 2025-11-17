<?php
/**
 * Social Login (OAuth) - Google y Facebook
 * Implementación de autenticación social usando OAuth 2.0
 *
 * @package SaicoWC
 * @version 1.0.0
 */

if (!defined('ABSPATH')) exit;

/**
 * Inicializar Google OAuth Client
 */
function saico_init_google_client() {
    $client_id = get_theme_mod('google_client_id');
    $client_secret = get_theme_mod('google_client_secret');

    if (empty($client_id) || empty($client_secret)) {
        return false;
    }

    return array(
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'redirect_uri' => home_url('/login/')
    );
}

/**
 * AJAX: Iniciar Google OAuth
 */
function saico_ajax_google_oauth_start() {
    $config = saico_init_google_client();

    if (!$config) {
        wp_send_json_error(array(
            'message' => 'El login con Google requiere configuración. Ve a Apariencia > Personalizar > Login & Seguridad.'
        ));
        return;
    }

    // Generar state token para seguridad CSRF
    $state = wp_create_nonce('saico_google_oauth');
    set_transient('saico_google_oauth_state_' . $state, true, 600); // 10 minutos

    // URL de autorización de Google
    $auth_url = add_query_arg(array(
        'client_id' => $config['client_id'],
        'redirect_uri' => $config['redirect_uri'],
        'response_type' => 'code',
        'scope' => 'email profile',
        'state' => $state,
        'access_type' => 'online',
        'prompt' => 'select_account'
    ), 'https://accounts.google.com/o/oauth2/v2/auth');

    wp_send_json_success(array(
        'auth_url' => $auth_url
    ));
}
add_action('wp_ajax_saico_google_oauth_start', 'saico_ajax_google_oauth_start');
add_action('wp_ajax_nopriv_saico_google_oauth_start', 'saico_ajax_google_oauth_start');

/**
 * Manejar callback de Google OAuth
 */
function saico_handle_google_oauth_callback() {
    if (!isset($_GET['code']) || !isset($_GET['state'])) {
        return;
    }

    // Verificar state token
    $state = sanitize_text_field($_GET['state']);
    if (!get_transient('saico_google_oauth_state_' . $state)) {
        wp_die('Token de seguridad inválido');
    }
    delete_transient('saico_google_oauth_state_' . $state);

    $config = saico_init_google_client();
    if (!$config) {
        wp_die('Configuración de Google OAuth incompleta');
    }

    $code = sanitize_text_field($_GET['code']);

    // Intercambiar código por token
    $token_response = wp_remote_post('https://oauth2.googleapis.com/token', array(
        'body' => array(
            'code' => $code,
            'client_id' => $config['client_id'],
            'client_secret' => $config['client_secret'],
            'redirect_uri' => $config['redirect_uri'],
            'grant_type' => 'authorization_code'
        )
    ));

    if (is_wp_error($token_response)) {
        wp_die('Error al obtener token: ' . $token_response->get_error_message());
    }

    $token_data = json_decode(wp_remote_retrieve_body($token_response), true);

    if (!isset($token_data['access_token'])) {
        wp_die('No se pudo obtener el token de acceso');
    }

    // Obtener información del usuario
    $user_info_response = wp_remote_get('https://www.googleapis.com/oauth2/v2/userinfo', array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $token_data['access_token']
        )
    ));

    if (is_wp_error($user_info_response)) {
        wp_die('Error al obtener información del usuario');
    }

    $user_info = json_decode(wp_remote_retrieve_body($user_info_response), true);

    // Crear o autenticar usuario
    $user_id = saico_create_or_authenticate_social_user($user_info['email'], $user_info['name'], 'google', $user_info['id']);

    if ($user_id) {
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id, true);
        do_action('wp_login', get_userdata($user_id)->user_login, get_userdata($user_id));

        wp_redirect(home_url());
        exit;
    }

    wp_die('Error al crear/autenticar usuario');
}
add_action('template_redirect', 'saico_handle_google_oauth_callback');

/**
 * AJAX: Iniciar Facebook OAuth
 */
function saico_ajax_facebook_oauth_start() {
    $app_id = get_theme_mod('facebook_app_id');
    $app_secret = get_theme_mod('facebook_app_secret');

    if (empty($app_id) || empty($app_secret)) {
        wp_send_json_error(array(
            'message' => 'El login con Facebook requiere configuración. Ve a Apariencia > Personalizar > Login & Seguridad.'
        ));
        return;
    }

    // Generar state token
    $state = wp_create_nonce('saico_facebook_oauth');
    set_transient('saico_facebook_oauth_state_' . $state, true, 600);

    // URL de autorización de Facebook
    $auth_url = add_query_arg(array(
        'client_id' => $app_id,
        'redirect_uri' => home_url('/login/'),
        'state' => $state,
        'scope' => 'email,public_profile',
        'response_type' => 'code'
    ), 'https://www.facebook.com/v18.0/dialog/oauth');

    wp_send_json_success(array(
        'auth_url' => $auth_url
    ));
}
add_action('wp_ajax_saico_facebook_oauth_start', 'saico_ajax_facebook_oauth_start');
add_action('wp_ajax_nopriv_saico_facebook_oauth_start', 'saico_ajax_facebook_oauth_start');

/**
 * Manejar callback de Facebook OAuth
 */
function saico_handle_facebook_oauth_callback() {
    if (!isset($_GET['code']) || !isset($_GET['state']) || !str_contains($_SERVER['REQUEST_URI'], 'login')) {
        return;
    }

    // Verificar que no sea un callback de Google
    if (get_transient('saico_google_oauth_state_' . sanitize_text_field($_GET['state']))) {
        return;
    }

    // Verificar state token
    $state = sanitize_text_field($_GET['state']);
    if (!get_transient('saico_facebook_oauth_state_' . $state)) {
        wp_die('Token de seguridad inválido');
    }
    delete_transient('saico_facebook_oauth_state_' . $state);

    $app_id = get_theme_mod('facebook_app_id');
    $app_secret = get_theme_mod('facebook_app_secret');

    if (empty($app_id) || empty($app_secret)) {
        wp_die('Configuración de Facebook OAuth incompleta');
    }

    $code = sanitize_text_field($_GET['code']);

    // Intercambiar código por token
    $token_response = wp_remote_get(add_query_arg(array(
        'client_id' => $app_id,
        'client_secret' => $app_secret,
        'redirect_uri' => home_url('/login/'),
        'code' => $code
    ), 'https://graph.facebook.com/v18.0/oauth/access_token'));

    if (is_wp_error($token_response)) {
        wp_die('Error al obtener token: ' . $token_response->get_error_message());
    }

    $token_data = json_decode(wp_remote_retrieve_body($token_response), true);

    if (!isset($token_data['access_token'])) {
        wp_die('No se pudo obtener el token de acceso');
    }

    // Obtener información del usuario
    $user_info_response = wp_remote_get(add_query_arg(array(
        'fields' => 'id,name,email',
        'access_token' => $token_data['access_token']
    ), 'https://graph.facebook.com/v18.0/me'));

    if (is_wp_error($user_info_response)) {
        wp_die('Error al obtener información del usuario');
    }

    $user_info = json_decode(wp_remote_retrieve_body($user_info_response), true);

    if (!isset($user_info['email'])) {
        wp_die('No se pudo obtener el email del usuario. Asegúrate de otorgar permisos de email.');
    }

    // Crear o autenticar usuario
    $user_id = saico_create_or_authenticate_social_user($user_info['email'], $user_info['name'], 'facebook', $user_info['id']);

    if ($user_id) {
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id, true);
        do_action('wp_login', get_userdata($user_id)->user_login, get_userdata($user_id));

        wp_redirect(home_url());
        exit;
    }

    wp_die('Error al crear/autenticar usuario');
}
add_action('template_redirect', 'saico_handle_facebook_oauth_callback', 5);

/**
 * Crear o autenticar usuario desde login social
 */
function saico_create_or_authenticate_social_user($email, $name, $provider, $provider_id) {
    if (!is_email($email)) {
        return false;
    }

    // Verificar si el usuario ya existe por email
    $user = get_user_by('email', $email);

    if ($user) {
        // Usuario existe, actualizar meta del proveedor social
        update_user_meta($user->ID, 'saico_social_' . $provider . '_id', $provider_id);
        return $user->ID;
    }

    // Usuario no existe, crear uno nuevo
    $username = sanitize_user(current(explode('@', $email)), true);

    // Asegurar que el username sea único
    $base_username = $username;
    $counter = 1;
    while (username_exists($username)) {
        $username = $base_username . $counter;
        $counter++;
    }

    // Crear usuario
    $user_id = wp_create_user($username, wp_generate_password(20, true, true), $email);

    if (is_wp_error($user_id)) {
        return false;
    }

    // Actualizar nombre y rol
    wp_update_user(array(
        'ID' => $user_id,
        'display_name' => $name,
        'first_name' => $name,
        'role' => 'customer'
    ));

    // Guardar meta del proveedor social
    update_user_meta($user_id, 'saico_social_' . $provider . '_id', $provider_id);
    update_user_meta($user_id, 'saico_social_login', $provider);

    // Ejecutar acciones de WooCommerce
    do_action('woocommerce_created_customer', $user_id);

    return $user_id;
}

/**
 * Localizar scripts con URLs AJAX
 */
function saico_localize_social_login_scripts() {
    if (wp_script_is('saico-login-page', 'enqueued')) {
        wp_localize_script('saico-login-page', 'saicoSocialLogin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('saico_social_login')
        ));
    }
}
add_action('wp_enqueue_scripts', 'saico_localize_social_login_scripts', 20);
