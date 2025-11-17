<?php
/**
 * Sistema de AdSense - Sanitizado y Optimizado
 * Cumple con las políticas de Google AdSense
 *
 * @package SaicoWC
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Sanitizar código de AdSense
 * Solo valida que sea código de AdSense legítimo
 * NO escapa el JavaScript para que pueda ejecutarse
 *
 * @param string $code Código de AdSense sin sanitizar
 * @return string Código validado (sin escapar)
 */
function saico_sanitize_adsense_code($code) {
    if (empty($code)) {
        return '';
    }

    // CRÍTICO: Si el código viene ya escapado de HTML, desescaparlo primero
    // Esto sucede cuando el usuario pega el código en el Customizer
    if (strpos($code, '&lt;') !== false || strpos($code, '&gt;') !== false || strpos($code, '&amp;') !== false) {
        $code = html_entity_decode($code, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        // Decodificar múltiples veces si es necesario (a veces WordPress escapa dos veces)
        if (strpos($code, '&lt;') !== false || strpos($code, '&gt;') !== false) {
            $code = html_entity_decode($code, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
    }

    // Validar que contiene elementos esenciales de AdSense
    if (strpos($code, 'pagead2.googlesyndication.com') === false &&
        strpos($code, 'adsbygoogle') === false) {
        return ''; // No es código de AdSense válido
    }

    // Validar que no contiene código malicioso
    $malicious_patterns = array(
        'eval(',
        'base64_decode',
        'gzinflate',
        'str_rot13',
    );

    foreach ($malicious_patterns as $pattern) {
        if (stripos($code, $pattern) !== false) {
            return ''; // Código potencialmente malicioso
        }
    }

    // Retornar el código SIN escapar para que el JavaScript se ejecute
    // La validación ya se hizo, no usar wp_kses() aquí
    return trim($code);
}

/**
 * Renderizar código de AdSense de forma segura
 *
 * @param string $ad_code Código de AdSense ya sanitizado
 * @param string $position Posición del anuncio (para tracking)
 * @return void
 */
function saico_render_adsense($ad_code, $position = '') {
    if (empty($ad_code)) {
        return;
    }

    // Contenedor con identificador de posición
    $container_class = 'saico-adsense-container';
    if (!empty($position)) {
        $container_class .= ' saico-adsense-' . sanitize_html_class($position);
    }

    // CRÍTICO: Verificar si hay código JavaScript suelto (método más eficiente)
    // Solo buscar el patrón común de AdSense y envolverlo si es necesario
    if (strpos($ad_code, '(adsbygoogle') !== false) {
        $pattern = '(adsbygoogle = window.adsbygoogle || []).push({});';
        // Si existe el patrón Y NO está dentro de <script>
        if (strpos($ad_code, $pattern) !== false &&
            strpos($ad_code, '<script>' . $pattern) === false) {
            // Envolver rápidamente
            $ad_code = str_replace($pattern, '<script>' . $pattern . '</script>', $ad_code);
        }
    }

    echo '<div class="' . esc_attr($container_class) . '" data-position="' . esc_attr($position) . '">';

    // El código ya viene sanitizado por saico_sanitize_adsense_code()
    // NO usar wp_kses_post() porque escapa el JavaScript
    echo $ad_code;

    echo '</div>';
}

/**
 * Obtener y renderizar anuncio de AdSense desde el customizer
 *
 * @param string $setting_name Nombre del setting en el customizer
 * @param string $position Posición del anuncio
 * @param bool $echo Si debe imprimir o retornar
 * @return string|void
 */
function saico_get_adsense($setting_name, $position = '', $echo = true) {
    // Obtener código del customizer
    $ad_code = get_theme_mod($setting_name, '');

    if (empty($ad_code)) {
        if ($echo) {
            return;
        }
        return '';
    }

    // CRÍTICO: Descodificar si viene escapado (por si acaso)
    // Esto asegura que incluso si el código está escapado en BD, se descodifica al leerlo
    if (strpos($ad_code, '&lt;') !== false || strpos($ad_code, '&gt;') !== false || strpos($ad_code, '&amp;') !== false) {
        $ad_code = html_entity_decode($ad_code, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        // Decodificar dos veces si es necesario
        if (strpos($ad_code, '&lt;') !== false || strpos($ad_code, '&gt;') !== false) {
            $ad_code = html_entity_decode($ad_code, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
    }

    // Renderizar
    if ($echo) {
        saico_render_adsense($ad_code, $position);
    } else {
        ob_start();
        saico_render_adsense($ad_code, $position);
        return ob_get_clean();
    }
}

/**
 * Hook para insertar AdSense antes del botón de descarga
 */
function saico_adsense_before_download_button() {
    saico_get_adsense('adsense_before_download_button', 'before-button');
}
add_action('saico_before_download_button', 'saico_adsense_before_download_button');

/**
 * Agregar CSP para AdSense (Content Security Policy)
 * Permite que AdSense funcione correctamente
 */
function saico_adsense_csp_headers() {
    if (!is_admin()) {
        // Permitir scripts de Google AdSense
        header("Content-Security-Policy: script-src 'self' 'unsafe-inline' 'unsafe-eval' https://pagead2.googlesyndication.com https://adservice.google.com https://www.googletagservices.com; frame-src 'self' https://googleads.g.doubleclick.net https://tpc.googlesyndication.com;", false);
    }
}
// Solo agregar si no hay conflictos con otros plugins de seguridad
// add_action('send_headers', 'saico_adsense_csp_headers');

/**
 * Lazy load de AdSense para mejor performance
 * Carga los anuncios solo cuando están en viewport
 */
function saico_adsense_lazy_load_script() {
    ?>
    <script>
    (function() {
        'use strict';

        // Lazy load AdSense cuando esté en viewport (más eficiente)
        function loadAdsense() {
            const containers = document.querySelectorAll('.saico-adsense-container');
            if (!containers.length) return;

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const container = entry.target;

                        // Limpiar texto suelto SOLO en este contenedor (más eficiente)
                        const textNodes = [];
                        container.childNodes.forEach(function(node) {
                            if (node.nodeType === 3 && node.textContent.includes('adsbygoogle')) {
                                textNodes.push(node);
                            }
                        });
                        textNodes.forEach(function(node) { container.removeChild(node); });

                        // Cargar anuncio si es necesario
                        const ins = container.querySelector('ins.adsbygoogle');
                        if (ins && !ins.hasAttribute('data-adsbygoogle-status')) {
                            try {
                                (adsbygoogle = window.adsbygoogle || []).push({});
                            } catch (e) {
                                console.error('AdSense:', e);
                            }
                        }

                        observer.unobserve(container);
                    }
                });
            }, { rootMargin: '200px' });

            containers.forEach(function(c) { observer.observe(c); });
        }

        // Ejecutar solo una vez cuando el DOM esté listo
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', loadAdsense);
        } else {
            loadAdsense();
        }
    })();
    </script>
    <?php
}
add_action('wp_footer', 'saico_adsense_lazy_load_script', 99);

/**
 * Estilos CSS para contenedores de AdSense
 */
function saico_adsense_styles() {
    ?>
    <style>
    /* ============================================================================
       ESTILOS GLOBALES DE ADSENSE - OPTIMIZADOS Y CENTRADOS
       ============================================================================ */
    .saico-adsense-container {
        margin: 16px auto;
        padding: 8px;
        background: transparent;
        border-radius: 0;
        min-height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: visible;
        position: relative;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }

    /* Ocultar texto suelto de JavaScript que no esté en tags script */
    .saico-adsense-container > :not(ins):not(script):not(div) {
        display: none !important;
    }

    /* Asegurar que el ins de AdSense se muestre centrado */
    .saico-adsense-container ins.adsbygoogle {
        display: block !important;
        margin: 0 auto !important;
        text-align: center !important;
    }

    /* ============================================================================
       POSICIONES ESPECÍFICAS - SIN BORDES EXCESIVOS
       ============================================================================ */

    /* Sidebar - Antes del botón */
    .saico-adsense-before-button {
        margin: 0 0 16px 0;
        padding: 0;
        background: transparent;
    }

    /* Modal - Durante espera */
    .saico-adsense-modal-waiting {
        margin: 20px auto;
        padding: 8px;
        background: transparent;
        max-width: 100%;
    }

    /* Modal - Antes de links (posición premium) */
    .saico-adsense-modal-before {
        margin: 0 auto 20px;
        padding: 8px;
        background: transparent;
        max-width: 100%;
    }

    /* Modal - Después de links */
    .saico-adsense-modal-after {
        margin: 20px auto 0;
        padding: 8px;
        background: transparent;
        max-width: 100%;
    }

    /* Página - Durante espera */
    .saico-adsense-page-waiting {
        margin: 20px auto;
        padding: 8px;
        background: transparent;
        max-width: 100%;
    }

    /* Página - Antes de links */
    .saico-adsense-page-before {
        margin: 0 auto 20px;
        padding: 8px;
        background: transparent;
        max-width: 100%;
    }

    /* Página - Después de links */
    .saico-adsense-page-after {
        margin: 20px auto 0;
        padding: 8px;
        background: transparent;
        max-width: 100%;
    }

    /* ============================================================================
       RESPONSIVE
       ============================================================================ */
    @media (max-width: 768px) {
        .saico-adsense-container {
            margin: 12px auto;
            padding: 4px;
        }

        .saico-adsense-modal-waiting,
        .saico-adsense-modal-before,
        .saico-adsense-modal-after,
        .saico-adsense-page-waiting,
        .saico-adsense-page-before,
        .saico-adsense-page-after {
            margin-left: auto;
            margin-right: auto;
            padding: 4px;
        }
    }

    /* ============================================================================
       ASEGURAR QUE ADSENSE RESPONSIVE FUNCIONE
       ============================================================================ */
    .saico-adsense-container ins.adsbygoogle {
        display: block !important;
        width: 100% !important;
        max-width: 100% !important;
        margin-left: auto !important;
        margin-right: auto !important;
    }
    </style>
    <?php
}
add_action('wp_head', 'saico_adsense_styles', 100);

/**
 * Validar que el código de AdSense es válido y seguro
 *
 * @param string $code Código para validar
 * @return bool True si es válido
 */
function saico_validate_adsense_code($code) {
    if (empty($code)) {
        return false;
    }

    // Debe contener elementos esenciales de AdSense
    $required_elements = array(
        'pagead2.googlesyndication.com', // URL del script
        'adsbygoogle', // Clase del ins o variable de push
    );

    foreach ($required_elements as $element) {
        if (strpos($code, $element) === false) {
            return false;
        }
    }

    // Validar que tiene data-ad-client (Publisher ID)
    if (strpos($code, 'data-ad-client') === false && strpos($code, 'client=') === false) {
        return false;
    }

    // No debe contener scripts maliciosos
    $malicious_patterns = array(
        'eval(',
        'base64_decode',
        'gzinflate',
        'str_rot13',
        'document.write',
        'innerHTML',
        'createElement',
    );

    foreach ($malicious_patterns as $pattern) {
        if (stripos($code, $pattern) !== false) {
            return false;
        }
    }

    return true;
}

/**
 * Registrar configuraciones de AdSense en el Customizer
 * Se llama desde inc/customizer.php
 */
function saico_register_adsense_customizer($wp_customize) {
    // Sección de AdSense
    $wp_customize->add_section('saico_adsense', array(
        'title' => __('AdSense', 'saico-wc'),
        'priority' => 36,
        'description' => __('Configura los códigos de Google AdSense para Modal y Vista de Página. Solo pega el código completo proporcionado por AdSense.', 'saico-wc'),
    ));

    // Anuncio antes del botón de descarga
    $wp_customize->add_setting('adsense_before_download_button', array(
        'default' => '',
        'sanitize_callback' => 'saico_sanitize_adsense_code',
    ));

    $wp_customize->add_control('adsense_before_download_button', array(
        'label' => __('Antes del Botón de Descarga', 'saico-wc'),
        'description' => __('Anuncio en el sidebar del producto, ANTES del botón de descarga.', 'saico-wc'),
        'section' => 'saico_adsense',
        'type' => 'textarea',
        'input_attrs' => array('rows' => 10),
    ));

    // Modal - Durante espera
    $wp_customize->add_setting('adsense_modal_waiting', array(
        'default' => '',
        'sanitize_callback' => 'saico_sanitize_adsense_code',
    ));

    $wp_customize->add_control('adsense_modal_waiting', array(
        'label' => __('Modal - Durante Espera', 'saico-wc'),
        'description' => __('Anuncio que se muestra en el modal mientras el usuario espera el countdown.', 'saico-wc'),
        'section' => 'saico_adsense',
        'type' => 'textarea',
        'input_attrs' => array('rows' => 10),
    ));

    // Modal - Antes de links
    $wp_customize->add_setting('adsense_modal_before_links', array(
        'default' => '',
        'sanitize_callback' => 'saico_sanitize_adsense_code',
    ));

    $wp_customize->add_control('adsense_modal_before_links', array(
        'label' => __('Modal - Antes de Links de Descarga', 'saico-wc'),
        'description' => __('Anuncio que se muestra ANTES de los links de descarga (posición premium).', 'saico-wc'),
        'section' => 'saico_adsense',
        'type' => 'textarea',
        'input_attrs' => array('rows' => 10),
    ));

    // Modal - Después de links
    $wp_customize->add_setting('adsense_modal_after_links', array(
        'default' => '',
        'sanitize_callback' => 'saico_sanitize_adsense_code',
    ));

    $wp_customize->add_control('adsense_modal_after_links', array(
        'label' => __('Modal - Después de Links de Descarga', 'saico-wc'),
        'description' => __('Anuncio que se muestra DESPUÉS de los links de descarga.', 'saico-wc'),
        'section' => 'saico_adsense',
        'type' => 'textarea',
        'input_attrs' => array('rows' => 10),
    ));

    // Página - Durante espera
    $wp_customize->add_setting('adsense_page_waiting', array(
        'default' => '',
        'sanitize_callback' => 'saico_sanitize_adsense_code',
    ));

    $wp_customize->add_control('adsense_page_waiting', array(
        'label' => __('Página - Durante Espera', 'saico-wc'),
        'description' => __('Anuncio en vista de página completa durante el countdown.', 'saico-wc'),
        'section' => 'saico_adsense',
        'type' => 'textarea',
        'input_attrs' => array('rows' => 10),
    ));

    // Página - Antes de links
    $wp_customize->add_setting('adsense_page_before_links', array(
        'default' => '',
        'sanitize_callback' => 'saico_sanitize_adsense_code',
    ));

    $wp_customize->add_control('adsense_page_before_links', array(
        'label' => __('Página - Antes de Links', 'saico-wc'),
        'description' => __('Anuncio ANTES de links en vista de página completa.', 'saico-wc'),
        'section' => 'saico_adsense',
        'type' => 'textarea',
        'input_attrs' => array('rows' => 10),
    ));

    // Página - Después de links
    $wp_customize->add_setting('adsense_page_after_links', array(
        'default' => '',
        'sanitize_callback' => 'saico_sanitize_adsense_code',
    ));

    $wp_customize->add_control('adsense_page_after_links', array(
        'label' => __('Página - Después de Links', 'saico-wc'),
        'description' => __('Anuncio DESPUÉS de links en vista de página completa.', 'saico-wc'),
        'section' => 'saico_adsense',
        'type' => 'textarea',
        'input_attrs' => array('rows' => 10),
    ));
}
