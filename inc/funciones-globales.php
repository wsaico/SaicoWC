<?php
/**
 * Funciones Globales Optimizadas - Saico Theme
 * Centraliza funciones reutilizables con sistema de caché
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Clase Singleton para funciones globales con caché
 */
class Saico_Funciones_Globales {

    private static $instancia = null;
    private static $cache_producto = array();

    public static function obtener_instancia() {
        if (null === self::$instancia) {
            self::$instancia = new self();
        }
        return self::$instancia;
    }

    private function __construct() {}

    /**
     * ========================================================================
     * FUNCIONES DE AUDIO
     * ========================================================================
     */

    /**
     * Obtener información de audio del producto
     */
    public static function obtener_audio_producto($producto_id) {
        $cache_key = 'audio_' . $producto_id;

        if (isset(self::$cache_producto[$cache_key])) {
            return self::$cache_producto[$cache_key];
        }

        $tiene_audio = false;
        $url_audio = '';

        // ACF
        if (function_exists('get_field')) {
            $url_audio = get_field('product_audio', $producto_id);
            $tiene_audio = !empty($url_audio);
        }

        $resultado = array(
            'tiene_audio' => $tiene_audio,
            'url' => $url_audio
        );

        self::$cache_producto[$cache_key] = $resultado;
        return $resultado;
    }

    /**
     * Obtener URL del archivo MIDI del producto
     */
    public static function obtener_midi_producto($producto_id) {
        $cache_key = 'midi_' . $producto_id;

        if (isset(self::$cache_producto[$cache_key])) {
            return self::$cache_producto[$cache_key];
        }

        $url_midi = false;

        if (function_exists('wc_get_product')) {
            $producto = wc_get_product($producto_id);
            if ($producto && method_exists($producto, 'get_downloads')) {
                $descargas = $producto->get_downloads();
                foreach ($descargas as $descarga) {
                    $url_archivo = $descarga->get_file();
                    $extension = strtolower(pathinfo($url_archivo, PATHINFO_EXTENSION));
                    if (in_array($extension, array('mid', 'midi'))) {
                        $url_midi = $url_archivo;
                        break;
                    }
                }
            }
        }

        self::$cache_producto[$cache_key] = $url_midi;
        return $url_midi;
    }

    /**
     * ========================================================================
     * FUNCIONES DE IMAGEN
     * ========================================================================
     */

    /**
     * Obtener información completa de imagen del producto
     */
    public static function obtener_imagen_producto($producto_id, $tamano = 'woocommerce_thumbnail') {
        $cache_key = 'imagen_' . $producto_id . '_' . $tamano;

        if (isset(self::$cache_producto[$cache_key])) {
            return self::$cache_producto[$cache_key];
        }

        $url_imagen = get_the_post_thumbnail_url($producto_id, $tamano);
        $tiene_imagen = !empty($url_imagen) && $url_imagen !== (function_exists('wc_placeholder_img_src') ? wc_placeholder_img_src($tamano) : '');

        if (!$tiene_imagen) {
            $url_imagen = function_exists('wc_placeholder_img_src') ? wc_placeholder_img_src($tamano) : '';
        }

        $resultado = array(
            'tiene_imagen' => $tiene_imagen,
            'url' => $url_imagen,
            'html' => '<img src="' . esc_url($url_imagen) . '" alt="' . esc_attr(get_the_title($producto_id)) . '" />'
        );

        self::$cache_producto[$cache_key] = $resultado;
        return $resultado;
    }

    /**
     * ========================================================================
     * FUNCIONES DE CATEGORÍA
     * ========================================================================
     */

    /**
     * Obtener categoría principal del producto
     */
    public static function obtener_categoria_producto($producto_id) {
        $cache_key = 'categoria_' . $producto_id;

        if (isset(self::$cache_producto[$cache_key])) {
            return self::$cache_producto[$cache_key];
        }

        $categorias = get_the_terms($producto_id, 'product_cat');
        $nombre = '';
        $enlace = '';
        $slug = '';

        if ($categorias && !is_wp_error($categorias)) {
            $categoria = $categorias[0];
            $nombre = $categoria->name;
            $enlace = get_term_link($categoria);
            $slug = $categoria->slug;
        }

        $resultado = array(
            'nombre' => $nombre,
            'enlace' => $enlace,
            'slug' => $slug
        );

        self::$cache_producto[$cache_key] = $resultado;
        return $resultado;
    }

    /**
     * ========================================================================
     * FUNCIONES DE CONTADORES
     * ========================================================================
     */

    /**
     * Obtener contadores del producto (vistas, descargas, likes)
     */
    public static function obtener_contadores_producto($producto_id) {
        $cache_key = 'contadores_' . $producto_id;

        if (isset(self::$cache_producto[$cache_key])) {
            return self::$cache_producto[$cache_key];
        }

        $descargas = (int) get_post_meta($producto_id, 'somdn_dlcount', true);
        $vistas = (int) get_post_meta($producto_id, '_vistas', true);
        $likes = (int) get_post_meta($producto_id, '_likes', true);

        $resultado = array(
            'descargas' => $descargas,
            'vistas' => $vistas,
            'likes' => $likes
        );

        self::$cache_producto[$cache_key] = $resultado;
        return $resultado;
    }

    /**
     * ========================================================================
     * FUNCIONES DE BADGES
     * ========================================================================
     */

    /**
     * Determinar badges del producto (nuevo, popular, destacado)
     */
    public static function obtener_badges_producto($producto_id, $producto = null) {
        $cache_key = 'badges_' . $producto_id;

        if (isset(self::$cache_producto[$cache_key])) {
            return self::$cache_producto[$cache_key];
        }

        if (!$producto && function_exists('wc_get_product')) {
            $producto = wc_get_product($producto_id);
        }

        // Nuevo (últimos 30 días)
        $fecha_publicacion = get_the_date('U', $producto_id);
        $dias_nuevo = get_theme_mod('saico_dias_nuevo', 30);
        $es_nuevo = (time() - $fecha_publicacion) < ($dias_nuevo * 24 * 60 * 60);

        // Popular (basado en descargas)
        $contadores = self::obtener_contadores_producto($producto_id);
        $umbral_popular = get_theme_mod('saico_umbral_popular', 100);
        $es_popular = $contadores['descargas'] > $umbral_popular;

        // Destacado
        $es_destacado = $producto ? $producto->is_featured() : false;

        // Gratis
        $es_gratis = $producto ? (!$producto->get_price() || $producto->get_price() == 0) : false;

        $resultado = array(
            'es_nuevo' => $es_nuevo,
            'es_popular' => $es_popular,
            'es_destacado' => $es_destacado,
            'es_gratis' => $es_gratis
        );

        self::$cache_producto[$cache_key] = $resultado;
        return $resultado;
    }

    /**
     * ========================================================================
     * FUNCIONES DE AUTOR
     * ========================================================================
     */

    /**
     * Obtener información del autor del producto
     */
    public static function obtener_autor_producto($producto_id) {
        $cache_key = 'autor_' . $producto_id;

        if (isset(self::$cache_producto[$cache_key])) {
            return self::$cache_producto[$cache_key];
        }

        $autor_id = get_post_field('post_author', $producto_id);
        $nombre = get_the_author_meta('display_name', $autor_id);
        $enlace = get_author_posts_url($autor_id);
        $avatar = get_avatar_url($autor_id, array('size' => 50));

        $resultado = array(
            'id' => $autor_id,
            'nombre' => $nombre,
            'enlace' => $enlace,
            'avatar' => $avatar
        );

        self::$cache_producto[$cache_key] = $resultado;
        return $resultado;
    }

    /**
     * ========================================================================
     * FUNCIONES DE TIEMPO
     * ========================================================================
     */

    /**
     * Calcular tiempo relativo desde publicación
     */
    public static function obtener_tiempo_relativo($producto_id) {
        $cache_key = 'tiempo_' . $producto_id;

        if (isset(self::$cache_producto[$cache_key])) {
            return self::$cache_producto[$cache_key];
        }

        $fecha = get_the_date('U', $producto_id);
        $diferencia = current_time('timestamp') - $fecha;

        if ($diferencia < 3600) {
            $cantidad = floor($diferencia / 60);
            $texto = $cantidad . ' ' . ($cantidad == 1 ? 'minuto' : 'minutos');
        } elseif ($diferencia < 86400) {
            $cantidad = floor($diferencia / 3600);
            $texto = $cantidad . ' ' . ($cantidad == 1 ? 'hora' : 'horas');
        } elseif ($diferencia < 2592000) {
            $cantidad = floor($diferencia / 86400);
            $texto = $cantidad . ' ' . ($cantidad == 1 ? 'día' : 'días');
        } elseif ($diferencia < 31536000) {
            $cantidad = floor($diferencia / 2592000);
            $texto = $cantidad . ' ' . ($cantidad == 1 ? 'mes' : 'meses');
        } else {
            $cantidad = floor($diferencia / 31536000);
            $texto = $cantidad . ' ' . ($cantidad == 1 ? 'año' : 'años');
        }

        self::$cache_producto[$cache_key] = $texto;
        return $texto;
    }

    /**
     * ========================================================================
     * FUNCIONES DE LIMPIEZA DE CACHÉ
     * ========================================================================
     */

    /**
     * Limpiar caché de un producto específico
     */
    public static function limpiar_cache_producto($producto_id) {
        $keys = array('audio_', 'midi_', 'imagen_', 'categoria_', 'contadores_', 'badges_', 'autor_', 'tiempo_');

        foreach ($keys as $prefix) {
            foreach (self::$cache_producto as $key => $value) {
                if (strpos($key, $prefix . $producto_id) === 0) {
                    unset(self::$cache_producto[$key]);
                }
            }
        }
    }

    /**
     * Limpiar todo el caché
     */
    public static function limpiar_cache_completo() {
        self::$cache_producto = array();
    }
}

/**
 * ============================================================================
 * FUNCIONES HELPER GLOBALES
 * ============================================================================
 */

/**
 * Helper para obtener audio del producto
 */
function saico_obtener_audio($producto_id) {
    return Saico_Funciones_Globales::obtener_audio_producto($producto_id);
}

/**
 * Helper para obtener MIDI del producto
 */
function saico_obtener_midi($producto_id) {
    return Saico_Funciones_Globales::obtener_midi_producto($producto_id);
}

/**
 * Helper para obtener imagen del producto
 */
function saico_obtener_imagen($producto_id, $tamano = 'woocommerce_thumbnail') {
    return Saico_Funciones_Globales::obtener_imagen_producto($producto_id, $tamano);
}

/**
 * Helper para obtener categoría del producto
 */
function saico_obtener_categoria($producto_id) {
    return Saico_Funciones_Globales::obtener_categoria_producto($producto_id);
}

/**
 * Helper para obtener contadores del producto
 */
function saico_obtener_contadores($producto_id) {
    return Saico_Funciones_Globales::obtener_contadores_producto($producto_id);
}

/**
 * Helper para obtener badges del producto
 */
function saico_obtener_badges($producto_id, $producto = null) {
    return Saico_Funciones_Globales::obtener_badges_producto($producto_id, $producto);
}

/**
 * Helper para obtener autor del producto
 */
function saico_obtener_autor($producto_id) {
    return Saico_Funciones_Globales::obtener_autor_producto($producto_id);
}

/**
 * Helper para obtener tiempo relativo
 */
function saico_tiempo_relativo($producto_id) {
    return Saico_Funciones_Globales::obtener_tiempo_relativo($producto_id);
}

/**
 * Limpiar caché cuando se actualiza un producto
 */
function saico_limpiar_cache_al_actualizar($producto_id) {
    Saico_Funciones_Globales::limpiar_cache_producto($producto_id);
}
add_action('save_post_product', 'saico_limpiar_cache_al_actualizar');
