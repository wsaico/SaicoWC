<?php
/**
 * The Template for displaying all single products
 *
 * @package SaicoWC
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

get_header('shop');

/**
 * Single Product Template - Versión Optimizada
 *
 * Layout responsive mobile-first optimizado:
 *
 * MÓVIL (< 1025px):
 * 1. Descripción (tabs)
 * 2. Acciones sociales
 * 3. Sidebar (información del producto)
 * 4. Productos relacionados
 *
 * DESKTOP (≥ 1025px):
 * Grid de 3 columnas:
 * - Columna 1: Sidebar izquierda sticky (acciones sociales)
 * - Columna 2: Descripción + Productos relacionados
 * - Columna 3: Sidebar derecha sticky (información del producto)
 */
?>

<div class="saico-single-product-container">
    <div class="saico-contenedor">
        <?php
        while (have_posts()) :
            the_post();
            global $product;

            if (!$product || !$product instanceof WC_Product) {
                echo '<div class="saico-error">Este producto no existe o no está disponible.</div>';
                break;
            }

            // Incrementar contador de vistas
            add_action('wp_footer', function() use ($product) {
                $view_count = (int) get_post_meta($product->get_id(), '_view_count', true);
                update_post_meta($product->get_id(), '_view_count', $view_count + 1);
            }, 99);

            $product_id = $product->get_id();
            ?>

            <!-- Breadcrumb -->
            <?php get_template_part('partes/producto/breadcrumb'); ?>

            <!-- Título del Producto -->
            <div class="saico-product-title-header">
                <h1 class="saico-product-title"><?php echo esc_html($product->get_name()); ?></h1>
            </div>

            <!-- Contenedor principal de vistas -->
            <div class="saico-product-views" data-product-id="<?php echo esc_attr($product_id); ?>">
                <!-- Vista principal del producto -->
                <div class="saico-main-view">
                    <!-- Grid de contenido principal -->
                    <div class="saico-product-content">

                        <!-- COLUMNA PRINCIPAL: Reproductor + Tabs + Estadísticas -->
                        <div class="saico-main-column">
                            <!-- Reproductor de Audio/MIDI (si existe) -->
                            <?php get_template_part('partes/producto/reproductor'); ?>

                            <!-- Tabs de Descripción y Reviews -->
                            <?php get_template_part('partes/producto/tabs'); ?>

                            <!-- Card de Estadísticas y Acciones Sociales -->
                            <?php get_template_part('partes/producto/estadisticas'); ?>
                        </div>

                        <!-- SIDEBAR DERECHA: Info del producto -->
                        <div class="saico-sidebar-column">
                            <?php
                            // Sidebar con CTA, acciones rápidas, info y tags
                            $is_free_product = ($product->get_price() == 0 || $product->get_price() == '');
                            ?>

                            <div class="saico-sidebar-modern">
                                <!-- 1. CTA PRINCIPAL -->
                                <div class="saico-cta-container">
                                    <?php if ($is_free_product) : ?>
                                        <!-- Espacio para AdSense -->
                                        <div class="saico-adsense-slot">
                                            <?php
                                            // Hook para insertar código de AdSense
                                            do_action('saico_before_download_button');

                                            // O directamente insertar el código de AdSense aquí
                                            // Ejemplo: echo get_theme_mod('adsense_code_product', '');
                                            ?>
                                        </div>

                                        <?php
                                        // Determinar comportamiento del botón según configuración
                                        $modal_enabled = get_theme_mod('enable_download_modal', true);
                                        $page_view_enabled = get_theme_mod('enable_download_page_view', false);

                                        // Prioridad: vista por página > modal > animado
                                        if ($page_view_enabled) {
                                            $button_id = 'saico-download-button';
                                            $button_class = 'saico-cta-btn-primary';
                                            $button_href = 'javascript:void(0);';
                                        } elseif ($modal_enabled) {
                                            $button_id = 'saico-download-button';
                                            $button_class = 'saico-cta-btn-primary';
                                            $button_href = 'javascript:void(0);';
                                        } else {
                                            $button_id = 'saico-animated-download-button';
                                            $button_class = 'saico-cta-btn-primary saico-animated-button';
                                            $button_href = 'javascript:void(0);';
                                        }
                                        ?>
                                        <a href="<?php echo esc_attr($button_href); ?>" id="<?php echo esc_attr($button_id); ?>" class="<?php echo esc_attr($button_class); ?>">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                <polyline points="7 10 12 15 17 10"></polyline>
                                                <line x1="12" y1="15" x2="12" y2="3"></line>
                                            </svg>
                                            <span class="cta-text">
                                                <strong><?php echo esc_html(get_theme_mod('download_button_text', 'DESCARGAR GRATIS')); ?></strong>
                                                <small><?php echo esc_html($product->get_title()); ?></small>
                                            </span>
                                            <?php if (!$modal_enabled && !$page_view_enabled) : ?>
                                                <span class="countdown-number"></span>
                                                <span class="button-progress"></span>
                                            <?php endif; ?>
                                        </a>

                                        <?php if (!$modal_enabled && !$page_view_enabled) : ?>
                                            <!-- Contenedor para botón animado, se muestra con JS -->
                                            <div class="saico-real-download-container" style="display: none;">
                                                <?php
                                                if (shortcode_exists('download_now_page')) {
                                                    echo do_shortcode('[download_now_page]');
                                                }
                                                ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php else : ?>
                                        <a href="<?php echo esc_url(wc_get_checkout_url() . '?add-to-cart=' . $product_id); ?>" class="saico-cta-btn-primary saico-cta-pro">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <circle cx="9" cy="21" r="1"></circle>
                                                <circle cx="20" cy="21" r="1"></circle>
                                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                            </svg>
                                            <span class="cta-text">
                                                <strong><?php echo wc_price($product->get_price()); ?></strong>
                                                <small>Comprar ahora</small>
                                            </span>
                                        </a>
                                    <?php endif; ?>
                                </div>

                                <!-- 3. ACCIONES RÁPIDAS -->
                                <div class="saico-quick-actions saico-quick-actions-3">
                                    <button class="action-btn" onclick="saicoAbrirModalCompartir();" title="Compartir">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <circle cx="18" cy="5" r="3"/>
                                            <circle cx="6" cy="12" r="3"/>
                                            <circle cx="18" cy="19" r="3"/>
                                            <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
                                            <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
                                        </svg>
                                        <span>Compartir</span>
                                    </button>

                                    <button class="action-btn" onclick="scrollToRelated();" title="Ver similares">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <rect x="3" y="3" width="7" height="7"/>
                                            <rect x="14" y="3" width="7" height="7"/>
                                            <rect x="14" y="14" width="7" height="7"/>
                                            <rect x="3" y="14" width="7" height="7"/>
                                        </svg>
                                        <span>Similar</span>
                                    </button>

                                    <button class="action-btn action-btn-donate" onclick="window.open('<?php echo esc_url(get_theme_mod('donate_url', 'https://paypal.me/tuusuario')); ?>', '_blank');" title="Apoyar con una donación">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                        </svg>
                                        <span>Donar</span>
                                    </button>
                                </div>

                                <!-- 4. INFORMACIÓN DEL PRODUCTO -->
                                <div class="saico-product-info">
                                    <h3 class="info-title">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="12" y1="16" x2="12" y2="12"></line>
                                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                        </svg>
                                        Información
                                    </h3>
                                    <div class="info-list">
                                        <?php
                                        $attributes = $product->get_attributes();
                                        $has_valid_attributes = false;
                                        
                                        if (!empty($attributes)) {
                                            foreach ($attributes as $attribute => $attribute_obj) {
                                                // Filtrar atributos no deseados
                                                $excluded_attributes = array(
                                                    'woocommerce-product-attributes',
                                                    'shop_attributes',
                                                    'pa_woocommerce-product-attributes',
                                                    'pa_shop_attributes'
                                                );
                                                
                                                // Saltar si es un atributo excluido
                                                if (in_array($attribute, $excluded_attributes)) {
                                                    continue;
                                                }
                                                
                                                if ($attribute_obj->get_visible()) {
                                                    $values = array();
                                                    $attribute_label = wc_attribute_label($attribute);

                                                    if ($attribute_obj->get_taxonomy_object()) {
                                                        $attribute_values = wc_get_product_terms($product_id, $attribute, array('fields' => 'all'));
                                                        foreach ($attribute_values as $attribute_value) {
                                                            $values[] = $attribute_value->name;
                                                        }
                                                    } else {
                                                        $values = $attribute_obj->get_options();
                                                    }

                                                    if (!empty($values)) {
                                                        echo '<div class="info-item">';
                                                        echo '<span class="info-label">' . esc_html($attribute_label) . '</span>';
                                                        echo '<span class="info-value">' . esc_html(implode(', ', $values)) . '</span>';
                                                        echo '</div>';
                                                        $has_valid_attributes = true;
                                                    }
                                                }
                                            }
                                        }
                                        
                                        // Si no hay atributos válidos, mostrar información por defecto
                                        if (!$has_valid_attributes) {
                                            echo '<div class="info-item">';
                                            echo '<span class="info-label">Estado</span>';
                                            echo '<span class="info-value">Producto Digital</span>';
                                            echo '</div>';
                                            echo '<div class="info-item">';
                                            echo '<span class="info-label">Tipo</span>';
                                            echo '<span class="info-value">' . ($is_free_product ? 'Gratuito' : 'Premium') . '</span>';
                                            echo '</div>';
                                        }
                                        ?>
                                    </div>
                                </div>

                                <!-- 5. ETIQUETAS -->
                                <?php
                                $product_tags = get_the_terms($product_id, 'product_tag');
                                ?>
                                <div class="saico-tags">
                                    <h3 class="tags-title">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                                            <line x1="7" y1="7" x2="7.01" y2="7"></line>
                                        </svg>
                                        Etiquetas
                                    </h3>
                                    <div class="tags-list">
                                        <?php
                                        if ($product_tags && !is_wp_error($product_tags)) {
                                            foreach ($product_tags as $tag) : ?>
                                                <a href="<?php echo esc_url(get_term_link($tag)); ?>" class="tag-pill">
                                                    <?php echo esc_html($tag->name); ?>
                                                </a>
                                            <?php endforeach;
                                        } else {
                                            ?>
                                            <span class="tag-pill" style="background: var(--saico-exito); color: white;">
                                                <?php echo $is_free_product ? 'Gratuito' : 'Premium'; ?>
                                            </span>
                                            <span class="tag-pill" style="background: var(--saico-info); color: white;">
                                                Digital
                                            </span>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PRODUCTOS RELACIONADOS -->
                        <div class="saico-related-products-wrapper">
                            <?php get_template_part('partes/producto/relacionados'); ?>
                        </div>
                    </div>
                </div>
            </div>

        <?php endwhile; ?>
    </div>
</div>

<script>
function scrollToRelated() {
    const related = document.querySelector('.saico-productos-relacionados');
    if (related) {
        related.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}
</script>

<?php
// Incluir opciones de descarga según configuración
if ($is_free_product) {
    $modal_enabled = get_theme_mod('enable_download_modal', true);
    $page_view_enabled = get_theme_mod('enable_download_page_view', false);

    if ($page_view_enabled) {
        // Vista por página - Link interno
        get_template_part('partes/producto/vista-descarga');
    } elseif ($modal_enabled) {
        // Modal de descarga
        get_template_part('partes/producto/modal-descarga');
    }
    // Si ambos están desactivados, usa botón animado (ya incluido arriba)
}

// Modal de compartir - Siempre incluido
get_template_part('partes/producto/modal-compartir');

get_footer('shop');
