<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package SaicoWC
 * @version 9.4.0
 */

defined('ABSPATH') || exit;

global $product;

// Check if the product is a valid WooCommerce product and ensure its visibility before proceeding.
if (!is_a($product, WC_Product::class) || !$product->is_visible()) {
	return;
}

// Cargar el template del card minimalista
wc_get_template_part('loop/card-producto', 'min');
