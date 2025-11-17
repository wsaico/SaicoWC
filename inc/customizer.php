<?php
/**
 * Customizer del tema - Configuraciones globales
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registrar opciones del customizer
 */
function saico_customizer_register($wp_customize) {

    /**
     * ========================================================================
     * SECCIÓN: TIPOGRAFÍA
     * ========================================================================
     */
    $wp_customize->add_section('saico_typography', array(
        'title' => __('Tipografía', 'saico-wc'),
        'priority' => 25,
        'description' => __('Personaliza la fuente del tema', 'saico-wc'),
    ));

    // Familia de fuente
    $wp_customize->add_setting('saico_font_family', array(
        'default' => 'Noto Sans JP',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('saico_font_family', array(
        'label' => __('Familia de Fuente', 'saico-wc'),
        'description' => __('Selecciona la fuente principal del tema', 'saico-wc'),
        'section' => 'saico_typography',
        'type' => 'select',
        'choices' => array(
            'Noto Sans JP' => 'Noto Sans JP (Predeterminada)',
            'Roboto' => 'Roboto',
            'Open Sans' => 'Open Sans',
            'Lato' => 'Lato',
            'Poppins' => 'Poppins',
            'Montserrat' => 'Montserrat',
            'Raleway' => 'Raleway',
            'Ubuntu' => 'Ubuntu',
            'Nunito' => 'Nunito',
            'Inter' => 'Inter',
            'Work Sans' => 'Work Sans',
            'Mukta' => 'Mukta',
            'Rubik' => 'Rubik',
            'Manrope' => 'Manrope',
            'DM Sans' => 'DM Sans',
            'Plus Jakarta Sans' => 'Plus Jakarta Sans',
            'Outfit' => 'Outfit',
            'Space Grotesk' => 'Space Grotesk',
            'Sora' => 'Sora',
            'IBM Plex Sans' => 'IBM Plex Sans',
        ),
    ));

    /**
     * ========================================================================
     * PANEL: PÁGINA DE INICIO
     * ========================================================================
     */
    $wp_customize->add_panel('saico_portada', array(
        'title' => __('Página de Inicio', 'saico-wc'),
        'priority' => 30,
        'description' => __('Personaliza la página de inicio', 'saico-wc'),
    ));

    // SECCIÓN: Hero
    $wp_customize->add_section('saico_hero', array(
        'title' => __('Hero Section', 'saico-wc'),
        'panel' => 'saico_portada',
        'priority' => 10,
    ));

    // Hero: Badge
    $wp_customize->add_setting('saico_hero_badge', array(
        'default' => 'Nuevos productos disponibles',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('saico_hero_badge', array(
        'label' => __('Badge', 'saico-wc'),
        'section' => 'saico_hero',
        'type' => 'text',
    ));

    // Hero: Título principal
    $wp_customize->add_setting('saico_hero_titulo', array(
        'default' => 'Descarga Digital',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('saico_hero_titulo', array(
        'label' => __('Título Principal', 'saico-wc'),
        'section' => 'saico_hero',
        'type' => 'text',
    ));

    // Hero: Título acento
    $wp_customize->add_setting('saico_hero_acento', array(
        'default' => 'Extraordinaria',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('saico_hero_acento', array(
        'label' => __('Título Acento (color verde)', 'saico-wc'),
        'section' => 'saico_hero',
        'type' => 'text',
    ));

    // Hero: Descripción
    $wp_customize->add_setting('saico_hero_descripcion', array(
        'default' => 'Descubre nuestra colección premium de productos digitales de alta calidad.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('saico_hero_descripcion', array(
        'label' => __('Descripción', 'saico-wc'),
        'section' => 'saico_hero',
        'type' => 'textarea',
    ));

    // Hero: Botón primario
    $wp_customize->add_setting('saico_hero_boton1_texto', array(
        'default' => 'Descubrir',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('saico_hero_boton1_texto', array(
        'label' => __('Botón Primario - Texto', 'saico-wc'),
        'section' => 'saico_hero',
        'type' => 'text',
    ));

    $wp_customize->add_setting('saico_hero_boton1_url', array(
        'default' => function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : '#',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('saico_hero_boton1_url', array(
        'label' => __('Botón Primario - URL', 'saico-wc'),
        'section' => 'saico_hero',
        'type' => 'url',
    ));

    // Hero: Botón secundario
    $wp_customize->add_setting('saico_hero_boton2_texto', array(
        'default' => 'Ver Demo',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('saico_hero_boton2_texto', array(
        'label' => __('Botón Secundario - Texto', 'saico-wc'),
        'section' => 'saico_hero',
        'type' => 'text',
    ));

    $wp_customize->add_setting('saico_hero_boton2_url', array(
        'default' => '#',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('saico_hero_boton2_url', array(
        'label' => __('Botón Secundario - URL', 'saico-wc'),
        'section' => 'saico_hero',
        'type' => 'url',
    ));

    // Hero: Producto destacado
    $wp_customize->add_setting('saico_hero_featured_product', array(
        'default' => '',
        'sanitize_callback' => 'absint',
    ));

    $wp_customize->add_control('saico_hero_featured_product', array(
        'label' => __('Producto Destacado', 'saico-wc'),
        'description' => __('Selecciona el producto que aparecerá en el Hero (columna derecha)', 'saico-wc'),
        'section' => 'saico_hero',
        'type' => 'select',
        'choices' => saico_get_products_for_customizer(),
    ));

    // Hero: Estadísticas (3 stats - reducido de 4 a 3 para mejor diseño)
    for ($i = 1; $i <= 3; $i++) {
        $defaults = array(
            1 => array('numero' => '500+', 'etiqueta' => 'Productos', 'icono' => 'box'),
            2 => array('numero' => '15K+', 'etiqueta' => 'Descargas', 'icono' => 'download'),
            3 => array('numero' => '4.9', 'etiqueta' => 'Rating', 'icono' => 'star'),
        );

        $wp_customize->add_setting("saico_hero_stat{$i}_numero", array(
            'default' => $defaults[$i]['numero'],
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("saico_hero_stat{$i}_numero", array(
            'label' => sprintf(__('Estadística %d - Número', 'saico-wc'), $i),
            'section' => 'saico_hero',
            'type' => 'text',
        ));

        $wp_customize->add_setting("saico_hero_stat{$i}_etiqueta", array(
            'default' => $defaults[$i]['etiqueta'],
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("saico_hero_stat{$i}_etiqueta", array(
            'label' => sprintf(__('Estadística %d - Etiqueta', 'saico-wc'), $i),
            'section' => 'saico_hero',
            'type' => 'text',
        ));

        $wp_customize->add_setting("saico_hero_stat{$i}_icono", array(
            'default' => $defaults[$i]['icono'],
            'sanitize_callback' => 'sanitize_text_field',
        ));
        $wp_customize->add_control("saico_hero_stat{$i}_icono", array(
            'label' => sprintf(__('Estadística %d - Ícono', 'saico-wc'), $i),
            'description' => __('Opciones: box, download, star, users, heart, check', 'saico-wc'),
            'section' => 'saico_hero',
            'type' => 'text',
        ));
    }

    // SECCIÓN: Categorías
    $wp_customize->add_section('saico_categorias', array(
        'title' => __('Sección Categorías', 'saico-wc'),
        'panel' => 'saico_portada',
        'priority' => 20,
    ));

    $wp_customize->add_setting('saico_categorias_titulo', array(
        'default' => 'Explora por Categorías',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('saico_categorias_titulo', array(
        'label' => __('Título', 'saico-wc'),
        'section' => 'saico_categorias',
        'type' => 'text',
    ));

    $wp_customize->add_setting('saico_categorias_subtitulo', array(
        'default' => 'Descubre contenido digital organizado por temática',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('saico_categorias_subtitulo', array(
        'label' => __('Subtítulo', 'saico-wc'),
        'section' => 'saico_categorias',
        'type' => 'text',
    ));

    // SECCIÓN: Productos Destacados
    $wp_customize->add_section('saico_productos_seccion', array(
        'title' => __('Sección Productos', 'saico-wc'),
        'panel' => 'saico_portada',
        'priority' => 30,
    ));

    $wp_customize->add_setting('saico_productos_titulo', array(
        'default' => 'Productos Destacados',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('saico_productos_titulo', array(
        'label' => __('Título', 'saico-wc'),
        'section' => 'saico_productos_seccion',
        'type' => 'text',
    ));

    // SECCIÓN: CTA (Call to Action)
    $wp_customize->add_section('saico_cta', array(
        'title' => __('CTA (Llamada a la Acción)', 'saico-wc'),
        'panel' => 'saico_portada',
        'priority' => 40,
    ));

    $wp_customize->add_setting('saico_cta_activar', array(
        'default' => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ));
    $wp_customize->add_control('saico_cta_activar', array(
        'label' => __('Activar CTA', 'saico-wc'),
        'section' => 'saico_cta',
        'type' => 'checkbox',
    ));

    $wp_customize->add_setting('saico_cta_titulo', array(
        'default' => 'Comienza Tu Viaje Digital Hoy',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('saico_cta_titulo', array(
        'label' => __('Título', 'saico-wc'),
        'section' => 'saico_cta',
        'type' => 'text',
    ));

    $wp_customize->add_setting('saico_cta_descripcion', array(
        'default' => 'Únete a miles de creadores que ya están transformando sus proyectos con nuestros productos digitales premium',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('saico_cta_descripcion', array(
        'label' => __('Descripción', 'saico-wc'),
        'section' => 'saico_cta',
        'type' => 'textarea',
    ));

    $wp_customize->add_setting('saico_cta_boton_texto', array(
        'default' => 'Explorar Ahora',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('saico_cta_boton_texto', array(
        'label' => __('Texto del Botón', 'saico-wc'),
        'section' => 'saico_cta',
        'type' => 'text',
    ));

    $wp_customize->add_setting('saico_cta_boton_url', array(
        'default' => function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : '#',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('saico_cta_boton_url', array(
        'label' => __('URL del Botón', 'saico-wc'),
        'section' => 'saico_cta',
        'type' => 'url',
    ));

    /**
     * ========================================================================
     * PANEL: COLORES GLOBALES
     * ========================================================================
     */
    $wp_customize->add_section('saico_colores', array(
        'title' => __('Colores Globales', 'saico-wc'),
        'priority' => 40,
    ));

    // Color primario
    $wp_customize->add_setting('saico_color_primario', array(
        'default' => '#0B996E',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'saico_color_primario', array(
        'label' => __('Color Primario', 'saico-wc'),
        'section' => 'saico_colores',
    )));

    // Color secundario
    $wp_customize->add_setting('saico_color_secundario', array(
        'default' => '#FF6B6B',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'saico_color_secundario', array(
        'label' => __('Color Secundario', 'saico-wc'),
        'section' => 'saico_colores',
    )));

    // Color acento
    $wp_customize->add_setting('saico_color_acento', array(
        'default' => '#4ECDC4',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'saico_color_acento', array(
        'label' => __('Color Acento', 'saico-wc'),
        'section' => 'saico_colores',
    )));

    /**
     * ========================================================================
     * PANEL: TIPOGRAFÍA
     * ========================================================================
     */
    $wp_customize->add_section('saico_tipografia', array(
        'title' => __('Tipografía', 'saico-wc'),
        'priority' => 50,
    ));

    // Fuente global
    $wp_customize->add_setting('saico_fuente_global', array(
        'default' => 'Inter',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('saico_fuente_global', array(
        'label' => __('Fuente Global', 'saico-wc'),
        'section' => 'saico_tipografia',
        'type' => 'select',
        'choices' => array(
            'sistema' => 'Sistema (por defecto)',
            'Inter' => 'Inter',
            'Poppins' => 'Poppins',
            'Roboto' => 'Roboto',
            'Open+Sans' => 'Open Sans',
            'Montserrat' => 'Montserrat',
        ),
    ));

    /**
     * ========================================================================
     * PANEL: FOOTER
     * ========================================================================
     */
    $wp_customize->add_section('saico_footer', array(
        'title' => __('Footer', 'saico-wc'),
        'priority' => 60,
    ));

    // Footer: Copyright
    $wp_customize->add_setting('saico_footer_copyright', array(
        'default' => '© ' . date('Y') . ' ' . get_bloginfo('name') . '. Todos los derechos reservados.',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('saico_footer_copyright', array(
        'label' => __('Texto Copyright', 'saico-wc'),
        'section' => 'saico_footer',
        'type' => 'textarea',
    ));

    // Footer: Redes sociales
    $redes = array('facebook', 'twitter', 'instagram', 'youtube', 'linkedin');
    foreach ($redes as $red) {
        $wp_customize->add_setting("saico_footer_{$red}", array(
            'default' => '',
            'sanitize_callback' => 'esc_url_raw',
        ));
        $wp_customize->add_control("saico_footer_{$red}", array(
            'label' => sprintf(__('URL %s', 'saico-wc'), ucfirst($red)),
            'section' => 'saico_footer',
            'type' => 'url',
        ));
    }

    /**
     * ========================================================================
     * PANEL: OPCIONES GENERALES
     * ========================================================================
     */
    $wp_customize->add_section('saico_general', array(
        'title' => __('Opciones Generales', 'saico-wc'),
        'priority' => 70,
    ));

    // Productos por página
    $wp_customize->add_setting('saico_productos_por_pagina', array(
        'default' => 12,
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('saico_productos_por_pagina', array(
        'label' => __('Productos por página', 'saico-wc'),
        'section' => 'saico_general',
        'type' => 'number',
    ));

    // Columnas de productos
    $wp_customize->add_setting('saico_columnas_productos', array(
        'default' => 4,
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('saico_columnas_productos', array(
        'label' => __('Columnas de productos (escritorio)', 'saico-wc'),
        'section' => 'saico_general',
        'type' => 'select',
        'choices' => array(
            '2' => '2 columnas',
            '3' => '3 columnas',
            '4' => '4 columnas',
            '5' => '5 columnas',
        ),
    ));

    // Días para considerar producto "nuevo"
    $wp_customize->add_setting('saico_dias_nuevo', array(
        'default' => 30,
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('saico_dias_nuevo', array(
        'label' => __('Días para badge "Nuevo"', 'saico-wc'),
        'section' => 'saico_general',
        'type' => 'number',
    ));

    // Umbral de descargas para producto "popular"
    $wp_customize->add_setting('saico_umbral_popular', array(
        'default' => 100,
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('saico_umbral_popular', array(
        'label' => __('Descargas para badge "Popular"', 'saico-wc'),
        'section' => 'saico_general',
        'type' => 'number',
    ));

    /**
     * ========================================================================
     * SECCIÓN: OPCIONES DE DESCARGA
     * ========================================================================
     */
    $wp_customize->add_section('saico_descargas', array(
        'title' => __('Opciones de Descarga', 'saico-wc'),
        'priority' => 35,
        'description' => __('Configura cómo funcionan las descargas de productos gratuitos', 'saico-wc'),
    ));

    // Texto del botón de descarga
    $wp_customize->add_setting('download_button_text', array(
        'default' => 'DESCARGAR GRATIS',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('download_button_text', array(
        'label' => __('Texto del Botón', 'saico-wc'),
        'section' => 'saico_descargas',
        'type' => 'text',
    ));

    // Habilitar modal de descarga
    $wp_customize->add_setting('enable_download_modal', array(
        'default' => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ));
    $wp_customize->add_control('enable_download_modal', array(
        'label' => __('Habilitar Modal de Descarga', 'saico-wc'),
        'description' => __('Muestra un modal con cuenta regresiva y opciones de descarga', 'saico-wc'),
        'section' => 'saico_descargas',
        'type' => 'checkbox',
    ));

    // Habilitar vista por página
    $wp_customize->add_setting('enable_download_page_view', array(
        'default' => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
    ));
    $wp_customize->add_control('enable_download_page_view', array(
        'label' => __('Habilitar Vista por Página', 'saico-wc'),
        'description' => __('Redirige a una vista dedicada con temporizador circular (prioridad sobre modal)', 'saico-wc'),
        'section' => 'saico_descargas',
        'type' => 'checkbox',
    ));

    // Tiempo del botón animado
    $wp_customize->add_setting('animated_button_time', array(
        'default' => 10,
        'sanitize_callback' => 'absint',
    ));
    $wp_customize->add_control('animated_button_time', array(
        'label' => __('Tiempo Botón Animado (segundos)', 'saico-wc'),
        'description' => __('Tiempo de espera cuando no se usa modal ni vista por página', 'saico-wc'),
        'section' => 'saico_descargas',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 1,
            'max' => 60,
            'step' => 1,
        ),
    ));

    /**
     * ========================================================================
     * SECCIÓN: ADSENSE
     * ========================================================================
     * NOTA: La configuración de AdSense se gestiona desde inc/adsense.php
     */
    // Registrar controles de AdSense desde el módulo centralizado
    if (function_exists('saico_register_adsense_customizer')) {
        saico_register_adsense_customizer($wp_customize);
    }

    /**
     * ========================================================================
     * SECCIÓN: DONACIONES
     * ========================================================================
     */
    $wp_customize->add_section('saico_donaciones', array(
        'title' => __('Donaciones', 'saico-wc'),
        'priority' => 37,
        'description' => __('Configure el botón de donación en la página del producto', 'saico-wc'),
    ));

    // URL de donación
    $wp_customize->add_setting('donate_url', array(
        'default' => 'https://paypal.me/tuusuario',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('donate_url', array(
        'label' => __('URL de Donación', 'saico-wc'),
        'description' => __('URL de PayPal, Ko-fi, Buy Me a Coffee, etc.', 'saico-wc'),
        'section' => 'saico_donaciones',
        'type' => 'url',
    ));

    /**
     * ========================================================================
     * SECCIÓN: HEADER CTA
     * ========================================================================
     */
    $wp_customize->add_section('saico_header_cta', array(
        'title' => __('Header CTA', 'saico-wc'),
        'priority' => 38,
        'description' => __('Configure el botón CTA del header', 'saico-wc'),
    ));

    // Habilitar CTA
    $wp_customize->add_setting('header_cta_enabled', array(
        'default' => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));
    $wp_customize->add_control('header_cta_enabled', array(
        'label' => __('Mostrar botón CTA', 'saico-wc'),
        'section' => 'saico_header_cta',
        'type' => 'checkbox',
    ));

    // Texto del CTA
    $wp_customize->add_setting('header_cta_text', array(
        'default' => 'Únete Gratis',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('header_cta_text', array(
        'label' => __('Texto del botón', 'saico-wc'),
        'section' => 'saico_header_cta',
        'type' => 'text',
    ));

    // URL del CTA
    $wp_customize->add_setting('header_cta_url', array(
        'default' => '/mi-cuenta/',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('header_cta_url', array(
        'label' => __('URL del botón', 'saico-wc'),
        'description' => __('URL a donde redirige el botón CTA', 'saico-wc'),
        'section' => 'saico_header_cta',
        'type' => 'url',
    ));
}
add_action('customize_register', 'saico_customizer_register');

/**
 * ============================================================================
 * FUNCIONES HELPER PARA OBTENER VALORES DEL CUSTOMIZER
 * ============================================================================
 */

// Hero
function saico_hero_badge() { return get_theme_mod('saico_hero_badge', 'Nuevos productos disponibles'); }
function saico_hero_titulo() { return get_theme_mod('saico_hero_titulo', 'Descarga Digital'); }
function saico_hero_acento() { return get_theme_mod('saico_hero_acento', 'Extraordinaria'); }
function saico_hero_titulo_acento() { return get_theme_mod('saico_hero_acento', 'Extraordinaria'); } // Alias
function saico_hero_descripcion() { return get_theme_mod('saico_hero_descripcion', 'Descubre nuestra colección premium de productos digitales de alta calidad.'); }
function saico_hero_boton1_texto() { return get_theme_mod('saico_hero_boton1_texto', 'Descubrir'); }
function saico_hero_boton1_url() { return get_theme_mod('saico_hero_boton1_url', function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : '#'); }
function saico_hero_boton2_texto() { return get_theme_mod('saico_hero_boton2_texto', 'Ver Demo'); }
function saico_hero_boton2_url() { return get_theme_mod('saico_hero_boton2_url', '#'); }

// Stats - Funciones individuales
function saico_hero_stat1_numero() { return get_theme_mod('saico_hero_stat1_numero', '500+'); }
function saico_hero_stat1_texto() { return get_theme_mod('saico_hero_stat1_etiqueta', 'Productos'); }
function saico_hero_stat2_numero() { return get_theme_mod('saico_hero_stat2_numero', '15K+'); }
function saico_hero_stat2_texto() { return get_theme_mod('saico_hero_stat2_etiqueta', 'Descargas'); }
function saico_hero_stat3_numero() { return get_theme_mod('saico_hero_stat3_numero', '4.9'); }
function saico_hero_stat3_texto() { return get_theme_mod('saico_hero_stat3_etiqueta', 'Rating'); }
function saico_hero_stat4_numero() { return get_theme_mod('saico_hero_stat4_numero', '24/7'); }
function saico_hero_stat4_texto() { return get_theme_mod('saico_hero_stat4_etiqueta', 'Soporte'); }

// Stats - Función genérica
function saico_hero_stat($num, $tipo) {
    $defaults = array(
        1 => array('numero' => '500+', 'etiqueta' => 'Productos'),
        2 => array('numero' => '15K+', 'etiqueta' => 'Descargas'),
        3 => array('numero' => '4.9', 'etiqueta' => 'Rating'),
        4 => array('numero' => '24/7', 'etiqueta' => 'Soporte'),
    );
    return get_theme_mod("saico_hero_stat{$num}_{$tipo}", $defaults[$num][$tipo]);
}

// Colores
function saico_color_primario() { return get_theme_mod('saico_color_primario', '#0B996E'); }
function saico_color_secundario() { return get_theme_mod('saico_color_secundario', '#FF6B6B'); }
function saico_color_acento() { return get_theme_mod('saico_color_acento', '#4ECDC4'); }

// Footer
function saico_footer_copyright() { return get_theme_mod('saico_footer_copyright', '© ' . date('Y') . ' ' . get_bloginfo('name') . '. Todos los derechos reservados.'); }
function saico_footer_red_social($red) { return get_theme_mod("saico_footer_{$red}", ''); }

/**
 * Obtener productos para el selector del Customizer
 */
function saico_get_products_for_customizer() {
    $products_array = array('' => __('-- Seleccionar producto --', 'saico-wc'));

    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 50,
        'post_status' => 'publish',
        'orderby' => 'title',
        'order' => 'ASC',
    );

    $products = get_posts($args);

    foreach ($products as $product) {
        $products_array[$product->ID] = $product->post_title;
    }

    return $products_array;
}

/**
 * Funciones helper para obtener valores del customizer
 */
// Categorías
function saico_categorias_titulo() {
    return get_theme_mod('saico_categorias_titulo', 'Explora por Categorías');
}

function saico_categorias_subtitulo() {
    return get_theme_mod('saico_categorias_subtitulo', 'Descubre contenido digital organizado por temática');
}

// Productos
function saico_productos_titulo() {
    return get_theme_mod('saico_productos_titulo', 'Productos Destacados');
}

// CTA
function saico_cta_activar() {
    return get_theme_mod('saico_cta_activar', true);
}

function saico_cta_titulo() {
    return get_theme_mod('saico_cta_titulo', 'Comienza Tu Viaje Digital Hoy');
}

function saico_cta_descripcion() {
    return get_theme_mod('saico_cta_descripcion', 'Únete a miles de creadores que ya están transformando sus proyectos con nuestros productos digitales premium');
}

function saico_cta_boton_texto() {
    return get_theme_mod('saico_cta_boton_texto', 'Explorar Ahora');
}

function saico_cta_boton_url() {
    return get_theme_mod('saico_cta_boton_url', function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : '#');
}

/**
 * Inyectar CSS personalizado para la fuente
 */
function saico_customizer_css() {
    $font_family = get_theme_mod('saico_font_family', 'Noto Sans JP');

    if ($font_family) {
        ?>
        <style type="text/css">
            :root {
                --saico-font-principal: '<?php echo esc_attr($font_family); ?>', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            }
            body,
            html,
            * {
                font-family: var(--saico-font-principal) !important;
            }
        </style>
        <?php
    }
}
add_action('wp_head', 'saico_customizer_css');

/**
 * ============================================================================
 * SECCIÓN: LOGIN PERSONALIZADO
 * ============================================================================
 */
function saico_login_customizer($wp_customize) {

    // Sección de Login
    $wp_customize->add_section('saico_login_section', array(
        'title' => __('Configuración de Login', 'saico-wc'),
        'priority' => 100,
        'description' => __('Personaliza la página de login y opciones de seguridad', 'saico-wc'),
    ));

    // ========================================================================
    // TEXTOS DEL LOGIN
    // ========================================================================

    // Título de bienvenida
    $wp_customize->add_setting('login_welcome_title', array(
        'default' => '¡Bienvenido de vuelta!',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('login_welcome_title', array(
        'label' => __('Título de Bienvenida', 'saico-wc'),
        'description' => __('Texto principal en el panel izquierdo', 'saico-wc'),
        'section' => 'saico_login_section',
        'type' => 'text',
    ));

    // Texto de bienvenida
    $wp_customize->add_setting('login_welcome_text', array(
        'default' => 'Inicia sesión para acceder a tu cuenta y disfrutar de todos nuestros servicios.',
        'sanitize_callback' => 'sanitize_textarea_field',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('login_welcome_text', array(
        'label' => __('Texto de Bienvenida', 'saico-wc'),
        'description' => __('Descripción debajo del título', 'saico-wc'),
        'section' => 'saico_login_section',
        'type' => 'textarea',
    ));

    // ========================================================================
    // SEGURIDAD Y reCAPTCHA
    // ========================================================================

    // reCAPTCHA Site Key
    $wp_customize->add_setting('recaptcha_site_key', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('recaptcha_site_key', array(
        'label' => __('reCAPTCHA Site Key (v3)', 'saico-wc'),
        'description' => __('Clave del sitio de Google reCAPTCHA v3. <a href="https://www.google.com/recaptcha/admin" target="_blank">Obtener claves</a>', 'saico-wc'),
        'section' => 'saico_login_section',
        'type' => 'text',
    ));

    // reCAPTCHA Secret Key
    $wp_customize->add_setting('recaptcha_secret_key', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('recaptcha_secret_key', array(
        'label' => __('reCAPTCHA Secret Key (v3)', 'saico-wc'),
        'description' => __('Clave secreta de Google reCAPTCHA v3', 'saico-wc'),
        'section' => 'saico_login_section',
        'type' => 'text',
    ));

    // Intentos máximos de login
    $wp_customize->add_setting('max_login_attempts', array(
        'default' => 5,
        'sanitize_callback' => 'absint',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('max_login_attempts', array(
        'label' => __('Intentos Máximos de Login', 'saico-wc'),
        'description' => __('Número de intentos fallidos antes de bloquear (1-10)', 'saico-wc'),
        'section' => 'saico_login_section',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 1,
            'max' => 10,
            'step' => 1,
        ),
    ));

    // Tiempo de bloqueo
    $wp_customize->add_setting('lockout_duration', array(
        'default' => 15,
        'sanitize_callback' => 'absint',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('lockout_duration', array(
        'label' => __('Tiempo de Bloqueo (minutos)', 'saico-wc'),
        'description' => __('Duración del bloqueo después de exceder intentos (5-60 minutos)', 'saico-wc'),
        'section' => 'saico_login_section',
        'type' => 'number',
        'input_attrs' => array(
            'min' => 5,
            'max' => 60,
            'step' => 5,
        ),
    ));

    // ========================================================================
    // LOGIN SOCIAL
    // ========================================================================

    // Habilitar Google Login
    $wp_customize->add_setting('enable_google_login', array(
        'default' => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('enable_google_login', array(
        'label' => __('Habilitar Login con Google', 'saico-wc'),
        'description' => __('Permite login con cuenta de Google', 'saico-wc'),
        'section' => 'saico_login_section',
        'type' => 'checkbox',
    ));

    // Google Client ID
    $wp_customize->add_setting('google_client_id', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('google_client_id', array(
        'label' => __('Google Client ID', 'saico-wc'),
        'description' => __('ID de cliente de OAuth 2.0. <a href="https://console.cloud.google.com/apis/credentials" target="_blank">Obtener en Google Cloud Console</a>', 'saico-wc'),
        'section' => 'saico_login_section',
        'type' => 'text',
    ));

    // Google Client Secret
    $wp_customize->add_setting('google_client_secret', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('google_client_secret', array(
        'label' => __('Google Client Secret', 'saico-wc'),
        'description' => __('Secreto de cliente de OAuth 2.0', 'saico-wc'),
        'section' => 'saico_login_section',
        'type' => 'text',
    ));

    // Habilitar Facebook Login
    $wp_customize->add_setting('enable_facebook_login', array(
        'default' => false,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('enable_facebook_login', array(
        'label' => __('Habilitar Login con Facebook', 'saico-wc'),
        'description' => __('Permite login con cuenta de Facebook', 'saico-wc'),
        'section' => 'saico_login_section',
        'type' => 'checkbox',
    ));

    // Facebook App ID
    $wp_customize->add_setting('facebook_app_id', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('facebook_app_id', array(
        'label' => __('Facebook App ID', 'saico-wc'),
        'description' => __('ID de aplicación de Facebook. <a href="https://developers.facebook.com/apps/" target="_blank">Obtener en Facebook Developers</a>', 'saico-wc'),
        'section' => 'saico_login_section',
        'type' => 'text',
    ));

    // Facebook App Secret
    $wp_customize->add_setting('facebook_app_secret', array(
        'default' => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('facebook_app_secret', array(
        'label' => __('Facebook App Secret', 'saico-wc'),
        'description' => __('Secreto de aplicación de Facebook', 'saico-wc'),
        'section' => 'saico_login_section',
        'type' => 'text',
    ));

    // ========================================================================
    // COLORES DEL FORMULARIO
    // ========================================================================

    // Color primario del login
    $wp_customize->add_setting('saico_primary_color', array(
        'default' => '#667eea',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'saico_primary_color', array(
        'label' => __('Color Primario del Login', 'saico-wc'),
        'description' => __('Color de botones y elementos activos', 'saico-wc'),
        'section' => 'saico_login_section',
    )));

    // Color de fondo del gradiente 1
    $wp_customize->add_setting('login_bg_gradient_1', array(
        'default' => '#f5f7fa',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'login_bg_gradient_1', array(
        'label' => __('Color de Fondo 1', 'saico-wc'),
        'description' => __('Primer color del gradiente de fondo', 'saico-wc'),
        'section' => 'saico_login_section',
    )));

    // Color de fondo del gradiente 2
    $wp_customize->add_setting('login_bg_gradient_2', array(
        'default' => '#c3cfe2',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'login_bg_gradient_2', array(
        'label' => __('Color de Fondo 2', 'saico-wc'),
        'description' => __('Segundo color del gradiente de fondo', 'saico-wc'),
        'section' => 'saico_login_section',
    )));

    // Color del panel izquierdo
    $wp_customize->add_setting('login_panel_color', array(
        'default' => '#667eea',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'login_panel_color', array(
        'label' => __('Color del Panel Izquierdo', 'saico-wc'),
        'description' => __('Color de fondo del panel de bienvenida', 'saico-wc'),
        'section' => 'saico_login_section',
    )));

    // Color de enlaces y textos destacados
    $wp_customize->add_setting('login_link_color', array(
        'default' => '#4f46e5',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'login_link_color', array(
        'label' => __('Color de Enlaces', 'saico-wc'),
        'description' => __('Color de enlaces y textos destacados', 'saico-wc'),
        'section' => 'saico_login_section',
    )));

    // Color de texto del panel izquierdo
    $wp_customize->add_setting('login_panel_text_color', array(
        'default' => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'login_panel_text_color', array(
        'label' => __('Color de Texto del Panel', 'saico-wc'),
        'description' => __('Color del texto en el panel izquierdo', 'saico-wc'),
        'section' => 'saico_login_section',
    )));

    // Color de texto de formularios
    $wp_customize->add_setting('login_form_text_color', array(
        'default' => '#1f2937',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'login_form_text_color', array(
        'label' => __('Color de Texto de Formularios', 'saico-wc'),
        'description' => __('Color del texto en campos de formulario', 'saico-wc'),
        'section' => 'saico_login_section',
    )));
}
add_action('customize_register', 'saico_login_customizer');

/**
 * ============================================================================
 * CUSTOMIZER - SEO FALLBACK DESCRIPTION
 * ============================================================================
 */
function saico_seo_fallback_customizer($wp_customize) {
    // Sección de SEO Fallback Description
    $wp_customize->add_section('saico_seo_fallback', array(
        'title' => __('SEO - Descripción Automática', 'saico-wc'),
        'priority' => 37,
        'description' => __('Genera descripciones automáticas para productos sin descripción. Usa ganchos como {titulo}, {tipo}, {categoria}, etc.', 'saico-wc'),
    ));

    // ========================================================================
    // HABILITAR FALLBACK
    // ========================================================================

    $wp_customize->add_setting('enable_seo_fallback_description', array(
        'default' => true,
        'sanitize_callback' => 'rest_sanitize_boolean',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('enable_seo_fallback_description', array(
        'label' => __('Habilitar Descripción Automática', 'saico-wc'),
        'description' => __('Genera automáticamente descripciones SEO cuando un producto no tiene descripción', 'saico-wc'),
        'section' => 'saico_seo_fallback',
        'type' => 'checkbox',
    ));

    // ========================================================================
    // TEMPLATE DE DESCRIPCIÓN
    // ========================================================================

    $wp_customize->add_setting('seo_fallback_template', array(
        'default' => saico_get_default_fallback_template(),
        'sanitize_callback' => 'wp_kses_post',
        'transport' => 'refresh',
    ));

    $wp_customize->add_control('seo_fallback_template', array(
        'label' => __('Plantilla de Descripción', 'saico-wc'),
        'description' => __('Usa ganchos para crear descripciones dinámicas. <a href="#" onclick="alert(\'Ganchos disponibles:\\n\\n{titulo} - Título del producto\\n{tipo} - Gratis/Premium\\n{categoria} - Categoría principal\\n{categorias} - Todas las categorías\\n{precio} - Precio formateado\\n{tags} - Etiquetas\\n{descripcion_corta} - Resumen\\n{atributos} - Atributos del producto\\n{fecha} - Fecha de publicación\\n{autor} - Nombre del autor\\n{rating} - Calificación\\n{reviews} - Número de opiniones\\n{sitio} - Nombre del sitio\\n\\nY muchos más...\'); return false;">Ver todos los ganchos</a>', 'saico-wc'),
        'section' => 'saico_seo_fallback',
        'type' => 'textarea',
        'input_attrs' => array(
            'rows' => 12,
            'placeholder' => 'Descarga {titulo} - {tipo} y de alta calidad...',
        ),
    ));

    // ========================================================================
    // AYUDA - GANCHOS DISPONIBLES
    // ========================================================================

    $wp_customize->add_setting('seo_fallback_help', array(
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $placeholders_list = saico_get_available_placeholders_list();
    $help_html = '<div style="background: #f0f0f0; padding: 15px; border-radius: 8px; margin-top: 10px;">';
    $help_html .= '<h4 style="margin-top: 0;">📌 Ganchos Disponibles:</h4>';

    foreach ($placeholders_list as $category => $items) {
        $help_html .= '<p style="margin: 10px 0; font-weight: 600; color: #10b981;">' . $category . ':</p>';
        $help_html .= '<ul style="margin: 5px 0; padding-left: 20px; font-size: 12px;">';
        foreach ($items as $placeholder => $description) {
            $help_html .= '<li><code>' . esc_html($placeholder) . '</code> - ' . esc_html($description) . '</li>';
        }
        $help_html .= '</ul>';
    }

    $help_html .= '<p style="margin-top: 15px; font-size: 12px; color: #666;"><strong>Ejemplo:</strong><br>';
    $help_html .= '<code>Descarga {titulo} - {tipo} de {categoria}. {descripcion_corta}</code></p>';
    $help_html .= '</div>';

    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'seo_fallback_help', array(
        'label' => __('Guía de Ganchos', 'saico-wc'),
        'section' => 'saico_seo_fallback',
        'type' => 'hidden',
        'description' => $help_html,
    )));

    // ========================================================================
    // TEMPLATE EJEMPLO 1
    // ========================================================================

    $wp_customize->add_setting('seo_fallback_example1', array(
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $example1 = "Descarga {titulo} - {tipo} y de alta calidad. Disponible en {categorias}.\n\n{descripcion_corta}\n\nCaracterísticas:\n• Tipo: {tipo}\n• Categoría: {categoria}\n• Formato: Digital\n\n¡Obtén {titulo} ahora de forma {tipo_minuscula}!";

    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'seo_fallback_example1', array(
        'label' => __('📝 Plantilla de Ejemplo 1', 'saico-wc'),
        'section' => 'saico_seo_fallback',
        'type' => 'hidden',
        'description' => '<div style="background: #fff; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 12px;"><pre style="white-space: pre-wrap; margin: 0;">' . esc_html($example1) . '</pre><button type="button" onclick="document.querySelector(\'#_customize-input-seo_fallback_template\').value = ' . esc_js(json_encode($example1)) . '; document.querySelector(\'#_customize-input-seo_fallback_template\').dispatchEvent(new Event(\'change\'));" style="margin-top: 10px; padding: 5px 10px; background: #10b981; color: white; border: none; border-radius: 4px; cursor: pointer;">Usar esta plantilla</button></div>',
    )));

    // ========================================================================
    // TEMPLATE EJEMPLO 2
    // ========================================================================

    $wp_customize->add_setting('seo_fallback_example2', array(
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $example2 = "{titulo} - {tipo} para descargar en {sitio}\n\nDescubre {titulo}, un recurso {tipo_minuscula} de {categoria} disponible para descarga inmediata.\n\n✓ Calidad premium\n✓ Descarga instantánea\n✓ Formato digital\n\n{descripcion_corta}";

    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'seo_fallback_example2', array(
        'label' => __('📝 Plantilla de Ejemplo 2 (Más Simple)', 'saico-wc'),
        'section' => 'saico_seo_fallback',
        'type' => 'hidden',
        'description' => '<div style="background: #fff; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 12px;"><pre style="white-space: pre-wrap; margin: 0;">' . esc_html($example2) . '</pre><button type="button" onclick="document.querySelector(\'#_customize-input-seo_fallback_template\').value = ' . esc_js(json_encode($example2)) . '; document.querySelector(\'#_customize-input-seo_fallback_template\').dispatchEvent(new Event(\'change\'));" style="margin-top: 10px; padding: 5px 10px; background: #10b981; color: white; border: none; border-radius: 4px; cursor: pointer;">Usar esta plantilla</button></div>',
    )));
}
add_action('customize_register', 'saico_seo_fallback_customizer');
