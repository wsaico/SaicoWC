<?php
/**
 * Template de Tienda/Archivo de Productos - Saico WC
 *
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

get_header('shop');

?>

<div class="saico-contenedor saico-tienda-contenedor">

    <!-- Breadcrumb -->
    <?php saico_breadcrumbs(); ?>

    <div class="saico-tienda-layout">

        <!-- Sidebar (si está activo) -->
        <?php if (is_active_sidebar('sidebar-tienda')): ?>
        <aside class="saico-tienda-sidebar">
            <?php dynamic_sidebar('sidebar-tienda'); ?>
        </aside>
        <?php endif; ?>

        <!-- Contenido principal -->
        <main class="saico-tienda-main">

            <!-- Header de la tienda -->
            <header class="saico-tienda-header">
                <?php if (apply_filters('woocommerce_show_page_title', true)): ?>
                    <h1 class="saico-tienda-titulo">
                        <?php woocommerce_page_title(); ?>
                    </h1>
                <?php endif; ?>

                <?php
                /**
                 * Hook: woocommerce_archive_description
                 * Descripción de la categoría/tienda
                 */
                do_action('woocommerce_archive_description');
                ?>
            </header>

            <?php
            if (woocommerce_product_loop()) {

                /**
                 * Hook: woocommerce_before_shop_loop
                 * Resultados y ordenamiento
                 */
                do_action('woocommerce_before_shop_loop');

                ?>

                <!-- Grid de productos -->
                <div class="productos-grid">
                    <?php
                    if (wc_get_loop_prop('total')) {
                        while (have_posts()) {
                            the_post();

                            /**
                             * Hook: woocommerce_shop_loop
                             * Cargar el template del card de producto
                             */
                            wc_get_template_part('loop/card-producto', 'min');
                        }
                    }
                    ?>
                </div>

                <?php

                /**
                 * Hook: woocommerce_after_shop_loop
                 * Paginación
                 */
                do_action('woocommerce_after_shop_loop');

            } else {
                /**
                 * Hook: woocommerce_no_products_found
                 * Mensaje cuando no hay productos
                 */
                do_action('woocommerce_no_products_found');
            }
            ?>

        </main>

    </div>

</div>

<?php
get_footer('shop');
