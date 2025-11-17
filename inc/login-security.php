<?php
/**
 * Login Security System
 * Sistema de seguridad avanzado para login personalizado
 * - Anti brute force
 * - Rate limiting
 * - reCAPTCHA v3
 * - Honeypot
 * - Sanitización y validación
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * ============================================================================
 * CONFIGURACIÓN
 * ============================================================================
 */

// Intentos máximos antes de bloquear
define('SAICO_MAX_LOGIN_ATTEMPTS', 5);

// Tiempo de bloqueo en segundos (15 minutos)
define('SAICO_LOCKOUT_TIME', 900);

// Tiempo para resetear contador de intentos (1 hora)
define('SAICO_RESET_TIME', 3600);

/**
 * ============================================================================
 * AJAX HANDLER PARA LOGIN PERSONALIZADO
 * ============================================================================
 */
function saico_handle_custom_login() {
    // Verificar nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'saico_custom_login')) {
        wp_send_json_error(array(
            'message' => 'Error de seguridad. Por favor, recarga la página.'
        ));
    }

    // Obtener datos sanitizados
    $username = isset($_POST['username']) ? sanitize_user($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $remember = isset($_POST['remember']) && $_POST['remember'] === '1';
    $recaptcha_token = isset($_POST['recaptcha_token']) ? sanitize_text_field($_POST['recaptcha_token']) : '';

    // Validar campos requeridos
    if (empty($username) || empty($password)) {
        wp_send_json_error(array(
            'message' => 'Por favor, completa todos los campos.'
        ));
    }

    // Verificar si la IP está bloqueada
    $ip_address = saico_get_user_ip();
    if (saico_is_ip_locked($ip_address)) {
        $remaining_time = saico_get_lockout_remaining_time($ip_address);
        wp_send_json_error(array(
            'message' => sprintf(
                'Demasiados intentos fallidos. Intenta nuevamente en %s.',
                saico_format_time($remaining_time)
            )
        ));
    }

    // Verificar reCAPTCHA si está configurado
    $recaptcha_secret = get_theme_mod('recaptcha_secret_key');
    if (!empty($recaptcha_secret) && !empty($recaptcha_token)) {
        $recaptcha_valid = saico_verify_recaptcha($recaptcha_token, $recaptcha_secret);

        if (!$recaptcha_valid) {
            wp_send_json_error(array(
                'message' => 'Verificación de seguridad fallida. Por favor, intenta nuevamente.'
            ));
        }
    }

    // Intentar autenticar
    $credentials = array(
        'user_login'    => $username,
        'user_password' => $password,
        'remember'      => $remember
    );

    $user = wp_signon($credentials, is_ssl());

    // Verificar resultado
    if (is_wp_error($user)) {
        // Registrar intento fallido
        saico_record_failed_login($ip_address, $username);

        // Obtener intentos restantes
        $attempts = saico_get_failed_attempts($ip_address);
        $remaining = SAICO_MAX_LOGIN_ATTEMPTS - $attempts;

        $error_message = 'Usuario o contraseña incorrectos.';

        if ($remaining > 0 && $remaining <= 3) {
            $error_message .= sprintf(' Te quedan %d intentos.', $remaining);
        }

        wp_send_json_error(array(
            'message' => $error_message
        ));
    }

    // Login exitoso - limpiar intentos fallidos
    saico_clear_failed_attempts($ip_address);

    // Log de actividad (opcional)
    do_action('saico_successful_login', $user->ID, $ip_address);

    // Determinar URL de redirección
    $redirect_url = home_url();

    if (user_can($user, 'manage_options')) {
        $redirect_url = admin_url();
    } elseif (class_exists('WooCommerce')) {
        $redirect_url = wc_get_page_permalink('myaccount');
    }

    // Aplicar filtro para personalizar redirección
    $redirect_url = apply_filters('saico_login_redirect', $redirect_url, $user);

    wp_send_json_success(array(
        'message' => '¡Bienvenido de vuelta! Redirigiendo...',
        'redirect' => $redirect_url
    ));
}
add_action('wp_ajax_nopriv_saico_custom_login', 'saico_handle_custom_login');

/**
 * ============================================================================
 * AJAX HANDLER PARA REGISTRO
 * ============================================================================
 */
function saico_handle_custom_register() {
    // Verificar nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'saico_custom_register')) {
        wp_send_json_error(array(
            'message' => 'Error de seguridad. Por favor, recarga la página.'
        ));
    }

    // Obtener datos sanitizados
    $username = isset($_POST['username']) ? sanitize_user($_POST['username']) : '';
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Validar campos requeridos
    if (empty($username) || empty($email) || empty($password)) {
        wp_send_json_error(array(
            'message' => 'Por favor, completa todos los campos.'
        ));
    }

    // Validar formato de email
    if (!is_email($email)) {
        wp_send_json_error(array(
            'message' => 'Por favor, ingresa un email válido.'
        ));
    }

    // Validar longitud de contraseña
    if (strlen($password) < 6) {
        wp_send_json_error(array(
            'message' => 'La contraseña debe tener al menos 6 caracteres.'
        ));
    }

    // Verificar si el usuario ya existe
    if (username_exists($username)) {
        wp_send_json_error(array(
            'message' => 'Este nombre de usuario ya está en uso.'
        ));
    }

    // Verificar si el email ya existe
    if (email_exists($email)) {
        wp_send_json_error(array(
            'message' => 'Este email ya está registrado.'
        ));
    }

    // Verificar rate limiting por IP
    $ip_address = saico_get_user_ip();
    if (saico_is_registration_rate_limited($ip_address)) {
        wp_send_json_error(array(
            'message' => 'Demasiados intentos de registro. Por favor, intenta más tarde.'
        ));
    }

    // Crear usuario
    $user_id = wp_create_user($username, $password, $email);

    if (is_wp_error($user_id)) {
        wp_send_json_error(array(
            'message' => $user_id->get_error_message()
        ));
    }

    // Registrar intento de registro
    saico_record_registration_attempt($ip_address);

    // Auto-login después del registro
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id);

    // Enviar email de bienvenida (opcional)
    wp_new_user_notification($user_id, null, 'user');

    // Log de actividad
    do_action('saico_successful_registration', $user_id, $ip_address);

    // Determinar URL de redirección
    $redirect_url = home_url();
    if (class_exists('WooCommerce')) {
        $redirect_url = wc_get_page_permalink('myaccount');
    }

    $redirect_url = apply_filters('saico_register_redirect', $redirect_url, $user_id);

    wp_send_json_success(array(
        'message' => '¡Cuenta creada exitosamente! Bienvenido.',
        'redirect' => $redirect_url
    ));
}
add_action('wp_ajax_nopriv_saico_custom_register', 'saico_handle_custom_register');

/**
 * ============================================================================
 * AJAX HANDLER PARA RECUPERAR CONTRASEÑA
 * ============================================================================
 */
function saico_handle_custom_recover() {
    // Verificar nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'saico_custom_recover')) {
        wp_send_json_error(array(
            'message' => 'Error de seguridad. Por favor, recarga la página.'
        ));
    }

    // Obtener email sanitizado
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';

    // Validar campo requerido
    if (empty($email)) {
        wp_send_json_error(array(
            'message' => 'Por favor, ingresa tu email.'
        ));
    }

    // Validar formato de email
    if (!is_email($email)) {
        wp_send_json_error(array(
            'message' => 'Por favor, ingresa un email válido.'
        ));
    }

    // Verificar rate limiting por IP
    $ip_address = saico_get_user_ip();
    if (saico_is_recovery_rate_limited($ip_address)) {
        wp_send_json_error(array(
            'message' => 'Demasiados intentos de recuperación. Por favor, intenta más tarde.'
        ));
    }

    // Verificar si el email existe
    $user = get_user_by('email', $email);

    if (!$user) {
        // Por seguridad, no revelamos si el email existe o no
        wp_send_json_success(array(
            'message' => 'Si el email existe, recibirás instrucciones para recuperar tu contraseña.'
        ));
    }

    // Registrar intento de recuperación
    saico_record_recovery_attempt($ip_address);

    // Generar token de recuperación
    $reset_key = get_password_reset_key($user);

    if (is_wp_error($reset_key)) {
        wp_send_json_error(array(
            'message' => 'No se pudo procesar la solicitud. Por favor, intenta más tarde.'
        ));
    }

    // Construir URL de reset
    $reset_url = network_site_url("wp-login.php?action=rp&key=$reset_key&login=" . rawurlencode($user->user_login), 'login');

    // Enviar email
    $email_subject = sprintf('[%s] Recuperación de contraseña', get_bloginfo('name'));
    $email_message = sprintf(
        "Hola %s,\n\n" .
        "Has solicitado recuperar tu contraseña en %s.\n\n" .
        "Para crear una nueva contraseña, haz clic en el siguiente enlace:\n%s\n\n" .
        "Si no solicitaste esto, ignora este correo.\n\n" .
        "Este enlace expirará en 24 horas.\n\n" .
        "Saludos,\n%s",
        $user->display_name,
        get_bloginfo('name'),
        $reset_url,
        get_bloginfo('name')
    );

    $email_sent = wp_mail($email, $email_subject, $email_message);

    if (!$email_sent) {
        wp_send_json_error(array(
            'message' => 'No se pudo enviar el email. Por favor, intenta más tarde.'
        ));
    }

    // Log de actividad
    do_action('saico_password_recovery_requested', $user->ID, $ip_address);

    wp_send_json_success(array(
        'message' => 'Revisa tu email. Te hemos enviado instrucciones para recuperar tu contraseña.'
    ));
}
add_action('wp_ajax_nopriv_saico_custom_recover', 'saico_handle_custom_recover');

/**
 * ============================================================================
 * FUNCIONES DE SEGURIDAD
 * ============================================================================
 */

/**
 * Obtener dirección IP del usuario
 */
function saico_get_user_ip() {
    $ip = '';

    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    // Sanitizar IP
    $ip = filter_var($ip, FILTER_VALIDATE_IP);

    return $ip ? $ip : '0.0.0.0';
}

/**
 * Verificar si una IP está bloqueada
 */
function saico_is_ip_locked($ip) {
    $transient_key = 'saico_login_lock_' . md5($ip);
    $locked_until = get_transient($transient_key);

    return $locked_until !== false && time() < $locked_until;
}

/**
 * Obtener tiempo restante de bloqueo
 */
function saico_get_lockout_remaining_time($ip) {
    $transient_key = 'saico_login_lock_' . md5($ip);
    $locked_until = get_transient($transient_key);

    if ($locked_until === false) {
        return 0;
    }

    $remaining = $locked_until - time();
    return max(0, $remaining);
}

/**
 * Registrar intento fallido de login
 */
function saico_record_failed_login($ip, $username) {
    $transient_key = 'saico_login_attempts_' . md5($ip);
    $attempts = get_transient($transient_key);

    if ($attempts === false) {
        $attempts = 1;
    } else {
        $attempts++;
    }

    // Guardar intentos por 1 hora
    set_transient($transient_key, $attempts, SAICO_RESET_TIME);

    // Si excede el límite, bloquear IP
    if ($attempts >= SAICO_MAX_LOGIN_ATTEMPTS) {
        $lock_key = 'saico_login_lock_' . md5($ip);
        $lock_until = time() + SAICO_LOCKOUT_TIME;
        set_transient($lock_key, $lock_until, SAICO_LOCKOUT_TIME);

        // Log de seguridad
        do_action('saico_ip_locked', $ip, $username, $attempts);

        // Opcional: enviar notificación al admin
        $admin_email = get_option('admin_email');
        $subject = 'IP bloqueada por intentos fallidos de login';
        $message = sprintf(
            "La IP %s ha sido bloqueada por %d intentos fallidos de login.\nÚltimo intento: %s\nTiempo de bloqueo: %s\n",
            $ip,
            $attempts,
            $username,
            saico_format_time(SAICO_LOCKOUT_TIME)
        );

        wp_mail($admin_email, $subject, $message);
    }

    return $attempts;
}

/**
 * Obtener intentos fallidos
 */
function saico_get_failed_attempts($ip) {
    $transient_key = 'saico_login_attempts_' . md5($ip);
    $attempts = get_transient($transient_key);

    return $attempts !== false ? (int) $attempts : 0;
}

/**
 * Limpiar intentos fallidos
 */
function saico_clear_failed_attempts($ip) {
    $transient_key = 'saico_login_attempts_' . md5($ip);
    delete_transient($transient_key);

    $lock_key = 'saico_login_lock_' . md5($ip);
    delete_transient($lock_key);
}

/**
 * Verificar reCAPTCHA v3
 */
function saico_verify_recaptcha($token, $secret_key) {
    if (empty($token) || empty($secret_key)) {
        return false;
    }

    $response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', array(
        'body' => array(
            'secret' => $secret_key,
            'response' => $token,
            'remoteip' => saico_get_user_ip()
        )
    ));

    if (is_wp_error($response)) {
        return false;
    }

    $body = wp_remote_retrieve_body($response);
    $result = json_decode($body, true);

    // Verificar éxito y score (mínimo 0.5 para v3)
    if (isset($result['success']) && $result['success'] === true) {
        $score = isset($result['score']) ? (float) $result['score'] : 0;
        return $score >= 0.5;
    }

    return false;
}

/**
 * Formatear tiempo en formato legible
 */
function saico_format_time($seconds) {
    if ($seconds < 60) {
        return $seconds . ' segundos';
    } elseif ($seconds < 3600) {
        $minutes = floor($seconds / 60);
        return $minutes . ' minuto' . ($minutes > 1 ? 's' : '');
    } else {
        $hours = floor($seconds / 3600);
        return $hours . ' hora' . ($hours > 1 ? 's' : '');
    }
}

/**
 * ============================================================================
 * PERSONALIZAR WP-LOGIN.PHP POR DEFECTO
 * ============================================================================
 */

/**
 * Personalizar logo del login
 */
function saico_login_logo() {
    $custom_logo_id = get_theme_mod('custom_logo');

    if ($custom_logo_id) {
        $logo_url = wp_get_attachment_image_url($custom_logo_id, 'full');
        ?>
        <style type="text/css">
            #login h1 a, .login h1 a {
                background-image: url(<?php echo esc_url($logo_url); ?>);
                background-size: contain;
                background-position: center;
                width: 100%;
                height: 80px;
            }
        </style>
        <?php
    }
}
add_action('login_enqueue_scripts', 'saico_login_logo');

/**
 * Cambiar URL del logo
 */
function saico_login_logo_url() {
    return home_url();
}
add_filter('login_headerurl', 'saico_login_logo_url');

/**
 * Cambiar título del logo
 */
function saico_login_logo_url_title() {
    return get_bloginfo('name');
}
add_filter('login_headertext', 'saico_login_logo_url_title');

/**
 * Estilos personalizados para wp-login.php
 */
function saico_custom_login_styles() {
    $primary_color = get_theme_mod('saico_primary_color', '#667eea');
    ?>
    <style type="text/css">
        body.login {
            background: linear-gradient(135deg, <?php echo esc_attr($primary_color); ?> 0%, #764ba2 100%);
        }

        .login form {
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .login #login_error,
        .login .message,
        .login .success {
            border-radius: 8px;
        }

        .wp-core-ui .button-primary {
            background: <?php echo esc_attr($primary_color); ?>;
            border-color: <?php echo esc_attr($primary_color); ?>;
            box-shadow: none;
            text-shadow: none;
            border-radius: 8px;
            padding: 8px 16px;
            transition: all 0.3s;
        }

        .wp-core-ui .button-primary:hover,
        .wp-core-ui .button-primary:focus {
            background: <?php echo esc_attr($primary_color); ?>;
            filter: brightness(1.1);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .login #backtoblog a,
        .login #nav a {
            color: white !important;
            text-decoration: none;
        }

        .login #backtoblog a:hover,
        .login #nav a:hover {
            text-decoration: underline;
        }

        .login form .input,
        .login input[type="text"],
        .login input[type="password"] {
            border-radius: 8px;
            padding: 10px;
            font-size: 16px;
        }

        .login form .input:focus,
        .login input[type="text"]:focus,
        .login input[type="password"]:focus {
            border-color: <?php echo esc_attr($primary_color); ?>;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }
    </style>
    <?php
}
add_action('login_enqueue_scripts', 'saico_custom_login_styles');

/**
 * Verificar rate limiting para registro
 */
function saico_is_registration_rate_limited($ip) {
    $transient_key = 'saico_register_attempts_' . md5($ip);
    $attempts = get_transient($transient_key);

    // Permitir máximo 3 registros por hora por IP
    return $attempts !== false && $attempts >= 3;
}

/**
 * Registrar intento de registro
 */
function saico_record_registration_attempt($ip) {
    $transient_key = 'saico_register_attempts_' . md5($ip);
    $attempts = get_transient($transient_key);

    if ($attempts === false) {
        $attempts = 1;
    } else {
        $attempts++;
    }

    // Guardar por 1 hora
    set_transient($transient_key, $attempts, 3600);

    return $attempts;
}

/**
 * Verificar rate limiting para recuperación
 */
function saico_is_recovery_rate_limited($ip) {
    $transient_key = 'saico_recovery_attempts_' . md5($ip);
    $attempts = get_transient($transient_key);

    // Permitir máximo 5 intentos de recuperación por hora por IP
    return $attempts !== false && $attempts >= 5;
}

/**
 * Registrar intento de recuperación
 */
function saico_record_recovery_attempt($ip) {
    $transient_key = 'saico_recovery_attempts_' . md5($ip);
    $attempts = get_transient($transient_key);

    if ($attempts === false) {
        $attempts = 1;
    } else {
        $attempts++;
    }

    // Guardar por 1 hora
    set_transient($transient_key, $attempts, 3600);

    return $attempts;
}

/**
 * ============================================================================
 * LOCALIZAR SCRIPTS PARA LOGIN PAGE
 * ============================================================================
 */
// Localización trasladada a inc/enqueue.php para evitar duplicaciones

/**
 * ============================================================================
 * REDIRECCIÓN AUTOMÁTICA A LOGIN PERSONALIZADO
 * ============================================================================
 */
function saico_redirect_to_custom_login() {
    // Solo si existe la página de login personalizada
    $login_page_id = get_option('saico_custom_login_page_id');

    if (!$login_page_id) {
        // Buscar página con template page-login.php
        $pages = get_pages(array(
            'meta_key' => '_wp_page_template',
            'meta_value' => 'page-login.php'
        ));

        if (!empty($pages)) {
            $login_page_id = $pages[0]->ID;
            update_option('saico_custom_login_page_id', $login_page_id);
        }
    }

    if ($login_page_id) {
        $login_url = get_permalink($login_page_id);

        // Redirigir a login personalizado
        global $pagenow;
        if ($pagenow === 'wp-login.php' && !isset($_GET['action'])) {
            wp_redirect($login_url);
            exit;
        }
    }
}
add_action('init', 'saico_redirect_to_custom_login');

/**
 * ============================================================================
 * ESTILOS DINÁMICOS DEL LOGIN PERSONALIZADO
 * ============================================================================
 */
function saico_login_custom_colors() {
    if (!is_page_template('page-login.php')) {
        return;
    }

    $primary_color = get_theme_mod('saico_primary_color', '#667eea');
    $bg_gradient_1 = get_theme_mod('login_bg_gradient_1', '#f5f7fa');
    $bg_gradient_2 = get_theme_mod('login_bg_gradient_2', '#c3cfe2');
    $panel_color = get_theme_mod('login_panel_color', '#667eea');
    $link_color = get_theme_mod('login_link_color', '#4f46e5');
    $panel_text_color = get_theme_mod('login_panel_text_color', '#ffffff');
    $form_text_color = get_theme_mod('login_form_text_color', '#1f2937');

    ?>
    <style type="text/css">
        /* Fondo general */
        .saico-login-page {
            background: linear-gradient(135deg, <?php echo esc_attr($bg_gradient_1); ?> 0%, <?php echo esc_attr($bg_gradient_2); ?> 100%) !important;
        }

        /* Panel izquierdo */
        .saico-login-info {
            background: linear-gradient(135deg, <?php echo esc_attr($panel_color); ?> 0%, <?php echo esc_attr($primary_color); ?> 100%) !important;
        }

        /* Texto del panel izquierdo */
        .login-info-text h2,
        .login-info-text p,
        .login-feature h4,
        .login-feature p {
            color: <?php echo esc_attr($panel_text_color); ?> !important;
        }

        /* Texto de formularios */
        .form-input,
        .checkbox-text,
        .login-footer p,
        .recaptcha-badge small {
            color: <?php echo esc_attr($form_text_color); ?> !important;
        }

        /* Tabs activos */
        .login-tab.active,
        .login-tab:hover {
            color: <?php echo esc_attr($primary_color); ?> !important;
        }

        .login-tab.active::after {
            background: <?php echo esc_attr($primary_color); ?> !important;
        }

        /* Inputs en focus */
        .form-input:focus {
            border-color: <?php echo esc_attr($primary_color); ?> !important;
            box-shadow: 0 0 0 4px <?php echo esc_attr($primary_color); ?>1a !important;
        }

        .form-input:focus + .form-label,
        .form-input:not(:placeholder-shown) + .form-label {
            color: <?php echo esc_attr($primary_color); ?> !important;
        }

        .form-input:focus ~ .form-icon {
            color: <?php echo esc_attr($primary_color); ?> !important;
        }

        /* Checkbox */
        .checkbox-label input[type="checkbox"]:checked + .checkbox-custom {
            background-color: <?php echo esc_attr($primary_color); ?> !important;
            border-color: <?php echo esc_attr($primary_color); ?> !important;
        }

        /* Enlaces - Con !important para sobrescribir */
        a.forgot-password,
        .login-footer a,
        .recaptcha-badge a {
            color: <?php echo esc_attr($link_color); ?> !important;
        }

        a.forgot-password:hover,
        .login-footer a:hover,
        .recaptcha-badge a:hover {
            color: <?php echo esc_attr($link_color); ?>dd !important;
        }

        /* Botones */
        .btn-login,
        .btn-register,
        .btn-recover {
            background: linear-gradient(135deg, <?php echo esc_attr($primary_color); ?> 0%, <?php echo esc_attr($panel_color); ?> 100%) !important;
            box-shadow: 0 4px 15px <?php echo esc_attr($primary_color); ?>4d !important;
        }

        .btn-login:hover,
        .btn-register:hover,
        .btn-recover:hover {
            box-shadow: 0 6px 20px <?php echo esc_attr($primary_color); ?>66 !important;
        }

        /* Toggle password */
        .toggle-password:hover {
            color: <?php echo esc_attr($primary_color); ?> !important;
        }

        /* Botones sociales */
        .social-btn:hover {
            border-color: <?php echo esc_attr($primary_color); ?> !important;
        }
    </style>
    <?php
}
add_action('wp_head', 'saico_login_custom_colors');
