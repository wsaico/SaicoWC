<?php
/**
 * Configuración de campos ACF para productos
 *
 * @package SaicoWC
 * @version 1.0.0
 */

// Prevenir acceso directo
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registrar campos personalizados ACF para productos
 */
function saico_register_acf_product_fields() {
    // Verificar que ACF esté instalado
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_product_media',
        'title' => 'Contenido Multimedia del Producto',
        'fields' => array(
            array(
                'key' => 'field_product_audio',
                'label' => 'Audio del Producto (MP3)',
                'name' => 'product_audio',
                'type' => 'file',
                'instructions' => 'Sube un archivo de audio MP3 para reproducción en el producto. Este audio se mostrará como vista previa.',
                'required' => 0,
                'conditional_logic' => 0,
                'return_format' => 'array',
                'library' => 'all',
                'mime_types' => 'mp3,wav,ogg',
            ),
            array(
                'key' => 'field_product_youtube_video',
                'label' => 'Video de YouTube',
                'name' => 'product_youtube_video',
                'type' => 'url',
                'instructions' => 'Ingresa la URL completa del video de YouTube (ej: https://www.youtube.com/watch?v=VIDEO_ID). El video se mostrará debajo de los reproductores de audio/MIDI.',
                'required' => 0,
                'conditional_logic' => 0,
                'placeholder' => 'https://www.youtube.com/watch?v=...',
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'product',
                ),
            ),
        ),
        'menu_order' => 10,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => 'Campos para contenido multimedia del producto',
    ));
}
add_action('acf/init', 'saico_register_acf_product_fields');

/**
 * Extraer ID de video de YouTube desde URL
 *
 * @param string $url URL del video de YouTube
 * @return string|false ID del video o false si no es válido
 */
function saico_extract_youtube_id($url) {
    if (empty($url)) {
        return false;
    }

    // Patrones para detectar URLs de YouTube
    $patterns = array(
        '/youtube\.com\/watch\?v=([^\&\?\/]+)/',
        '/youtube\.com\/embed\/([^\&\?\/]+)/',
        '/youtu\.be\/([^\&\?\/]+)/',
        '/youtube\.com\/v\/([^\&\?\/]+)/',
    );

    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
    }

    return false;
}

/**
 * Obtener URL embed de YouTube
 *
 * @param string $url URL del video de YouTube
 * @return string|false URL embed o false si no es válido
 */
function saico_get_youtube_embed_url($url) {
    $video_id = saico_extract_youtube_id($url);

    if (!$video_id) {
        return false;
    }

    return 'https://www.youtube.com/embed/' . $video_id . '?rel=0&modestbranding=1';
}
