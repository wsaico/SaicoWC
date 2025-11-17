<?php
/**
 * Metaboxes para configuración de páginas
 * Control de sidebar y otras opciones de layout
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Agregar metabox para opciones de sidebar
 */
function saico_add_sidebar_metabox() {
    // Para páginas
    add_meta_box(
        'saico_sidebar_options',
        __('Opciones de Sidebar', 'saico-wc'),
        'saico_sidebar_metabox_callback',
        'page',
        'side',
        'default'
    );

    // Para posts
    add_meta_box(
        'saico_sidebar_options',
        __('Opciones de Sidebar', 'saico-wc'),
        'saico_sidebar_metabox_callback',
        'post',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'saico_add_sidebar_metabox');

/**
 * Callback para mostrar el metabox
 */
function saico_sidebar_metabox_callback($post) {
    // Agregar nonce para seguridad
    wp_nonce_field('saico_sidebar_metabox', 'saico_sidebar_nonce');

    // Obtener valor actual
    $sidebar_option = get_post_meta($post->ID, '_saico_sidebar_option', true);

    // Si no hay valor, usar 'default' (mostrar sidebar si está activo)
    if (empty($sidebar_option)) {
        $sidebar_option = 'default';
    }
    ?>

    <div class="saico-metabox-field">
        <p>
            <label>
                <input type="radio" name="saico_sidebar_option" value="default" <?php checked($sidebar_option, 'default'); ?>>
                <strong><?php _e('Predeterminado', 'saico-wc'); ?></strong>
                <br>
                <small><?php _e('Mostrar sidebar si hay widgets activos', 'saico-wc'); ?></small>
            </label>
        </p>

        <p>
            <label>
                <input type="radio" name="saico_sidebar_option" value="with-sidebar" <?php checked($sidebar_option, 'with-sidebar'); ?>>
                <strong><?php _e('Con Sidebar', 'saico-wc'); ?></strong>
                <br>
                <small><?php _e('Siempre mostrar sidebar (forzado)', 'saico-wc'); ?></small>
            </label>
        </p>

        <p>
            <label>
                <input type="radio" name="saico_sidebar_option" value="no-sidebar" <?php checked($sidebar_option, 'no-sidebar'); ?>>
                <strong><?php _e('Sin Sidebar', 'saico-wc'); ?></strong>
                <br>
                <small><?php _e('Ancho completo, sin sidebar', 'saico-wc'); ?></small>
            </label>
        </p>
    </div>

    <style>
    .saico-metabox-field {
        padding: 10px 0;
    }
    .saico-metabox-field p {
        margin: 0 0 15px 0;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        background: #f9f9f9;
    }
    .saico-metabox-field label {
        display: block;
        cursor: pointer;
    }
    .saico-metabox-field input[type="radio"] {
        margin-right: 8px;
    }
    .saico-metabox-field small {
        color: #666;
        display: block;
        margin-top: 5px;
        margin-left: 24px;
    }
    .saico-metabox-field p:hover {
        background: #fff;
        border-color: #2271b1;
    }
    </style>
    <?php
}

/**
 * Guardar metabox
 */
function saico_save_sidebar_metabox($post_id) {
    // Verificar nonce
    if (!isset($_POST['saico_sidebar_nonce']) || !wp_verify_nonce($_POST['saico_sidebar_nonce'], 'saico_sidebar_metabox')) {
        return;
    }

    // Verificar autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Verificar permisos
    if (isset($_POST['post_type']) && 'page' === $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id)) {
            return;
        }
    } else {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }

    // Guardar el valor
    if (isset($_POST['saico_sidebar_option'])) {
        $sidebar_option = sanitize_text_field($_POST['saico_sidebar_option']);

        // Validar que sea una opción válida
        $valid_options = array('default', 'with-sidebar', 'no-sidebar');
        if (in_array($sidebar_option, $valid_options)) {
            update_post_meta($post_id, '_saico_sidebar_option', $sidebar_option);
        }
    }
}
add_action('save_post', 'saico_save_sidebar_metabox');

/**
 * Función helper para verificar si debe mostrar sidebar
 *
 * @return bool
 */
function saico_should_show_sidebar() {
    // Solo para páginas y posts individuales
    if (!is_singular()) {
        // Para archivos, usar lógica por defecto
        return is_active_sidebar('sidebar-principal');
    }

    $post_id = get_the_ID();
    $sidebar_option = get_post_meta($post_id, '_saico_sidebar_option', true);

    // Si no hay opción definida, usar 'default'
    if (empty($sidebar_option)) {
        $sidebar_option = 'default';
    }

    switch ($sidebar_option) {
        case 'with-sidebar':
            // Forzar sidebar
            return true;

        case 'no-sidebar':
            // Sin sidebar
            return false;

        case 'default':
        default:
            // Mostrar solo si hay widgets activos
            return is_active_sidebar('sidebar-principal');
    }
}

/**
 * Obtener clase CSS para el layout
 *
 * @return string
 */
function saico_get_layout_class() {
    if (saico_should_show_sidebar()) {
        return 'con-sidebar';
    }
    return 'sin-sidebar';
}
