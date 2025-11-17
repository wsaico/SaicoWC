<?php
/**
 * Script para limpiar códigos de AdSense escapados en la base de datos
 * EJECUTAR UNA SOLA VEZ y luego BORRAR este archivo
 */

// Cargar WordPress
require_once('../../../wp-load.php');

if (!current_user_can('manage_options')) {
    die('No tienes permisos para ejecutar este script');
}

echo "<h1>Limpiando códigos de AdSense escapados...</h1>";

$adsense_settings = array(
    'adsense_before_download_button',
    'adsense_modal_waiting',
    'adsense_modal_before_links',
    'adsense_modal_after_links',
    'adsense_page_waiting',
    'adsense_page_before_links',
    'adsense_page_after_links'
);

foreach ($adsense_settings as $setting) {
    $code = get_theme_mod($setting, '');
    
    if (!empty($code)) {
        echo "<h3>$setting:</h3>";
        echo "<p><strong>ANTES:</strong></p>";
        echo "<pre>" . htmlspecialchars(substr($code, 0, 200)) . "...</pre>";
        
        // Decodificar HTML entities
        $decoded = html_entity_decode($code, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        // Guardar de vuelta
        set_theme_mod($setting, $decoded);
        
        echo "<p><strong>DESPUÉS:</strong></p>";
        echo "<pre>" . htmlspecialchars(substr($decoded, 0, 200)) . "...</pre>";
        echo "<p style='color: green;'>✓ Limpiado</p>";
        echo "<hr>";
    }
}

echo "<h2 style='color: green;'>✓ COMPLETADO - Ahora BORRA este archivo (fix-adsense-db.php)</h2>";
