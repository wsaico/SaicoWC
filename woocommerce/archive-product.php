<?php
/**
 * The Template for displaying product archives, including the main shop page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package SaicoWC
 * @version 8.6.0
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

            <?php
            /**
             * Hook: woocommerce_before_main_content
             *
             * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
             * @hooked woocommerce_breadcrumb - 20
             * @hooked WC_Structured_Data::generate_website_data() - 30
             */
            do_action('woocommerce_before_main_content');
            ?>

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
                 *
                 * @hooked woocommerce_output_all_notices - 10
                 * @hooked woocommerce_result_count - 20
                 * @hooked woocommerce_catalog_ordering - 30
                 */
                do_action('woocommerce_before_shop_loop');

                ?>

                <!-- Grid de productos - SIN woocommerce_product_loop_start() que genera <ul> -->
                <div class="productos-grid">
                    <?php
                    if (wc_get_loop_prop('total')) {
                        while (have_posts()) {
                            the_post();

                            /**
                             * Hook: woocommerce_shop_loop
                             */
                            do_action('woocommerce_shop_loop');

                            // Cargar el template del card de producto
                            wc_get_template_part('content', 'product');
                        }
                    }
                    ?>
                </div>

                <?php

                /**
                 * Hook: woocommerce_after_shop_loop
                 *
                 * @hooked woocommerce_pagination - 10
                 */
                do_action('woocommerce_after_shop_loop');

            } else {
                /**
                 * Hook: woocommerce_no_products_found
                 *
                 * @hooked wc_no_products_found - 10
                 */
                do_action('woocommerce_no_products_found');
            }
            ?>

            <?php
            /**
             * Hook: woocommerce_after_main_content
             *
             * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
             */
            do_action('woocommerce_after_main_content');
            ?>

        </main>

    </div>

</div>

<?php
/**
 * Hook: woocommerce_sidebar
 *
 * @hooked woocommerce_get_sidebar - 10
 */
do_action('woocommerce_sidebar');

get_footer('shop');
