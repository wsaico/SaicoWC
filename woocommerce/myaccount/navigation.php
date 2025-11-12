<?php
/**
 * My Account navigation - Saico WC Enhanced
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/navigation.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Iconos SVG para cada endpoint
$menu_icons = array(
	'dashboard'       => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>',
	'orders'          => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>',
	'downloads'       => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>',
	'edit-address'    => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>',
	'payment-methods' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>',
	'edit-account'    => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>',
	'customer-logout' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>',
);

// Obtener datos del usuario actual
$current_user = wp_get_current_user();
$customer_orders_count = wc_get_customer_order_count( get_current_user_id() );
$customer_total_spent = wc_get_customer_total_spent( get_current_user_id() );

do_action( 'woocommerce_before_account_navigation' );
?>

<nav class="woocommerce-MyAccount-navigation esaico-myaccount-nav" aria-label="<?php esc_html_e( 'Account pages', 'woocommerce' ); ?>">

	<!-- Header del usuario -->
	<div class="esaico-myaccount-nav-header">
		<div class="esaico-user-avatar">
			<?php echo get_avatar( $current_user->ID, 80 ); ?>
		</div>
		<div class="esaico-user-info">
			<h4><?php echo esc_html( $current_user->display_name ); ?></h4>
			<p><?php echo esc_html( $current_user->user_email ); ?></p>
		</div>
	</div>

	<!-- Navegación principal -->
	<ul>
		<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) :
			$icon_svg = isset( $menu_icons[ $endpoint ] ) ? $menu_icons[ $endpoint ] : '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle></svg>';
		?>
			<li class="<?php echo wc_get_account_menu_item_classes( $endpoint ); ?>">
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" <?php echo wc_is_current_account_menu_item( $endpoint ) ? 'aria-current="page"' : ''; ?>>
					<span class="esaico-nav-icon">
						<?php echo $icon_svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</span>
					<span class="esaico-nav-text"><?php echo esc_html( $label ); ?></span>
					<?php if ( $endpoint === 'orders' && $customer_orders_count > 0 ) : ?>
						<span class="esaico-nav-badge"><?php echo esc_html( $customer_orders_count ); ?></span>
					<?php endif; ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>

	<!-- Footer con estadísticas -->
	<div class="esaico-myaccount-nav-footer">
		<div class="esaico-account-stats">
			<div class="esaico-stat-item">
				<span class="esaico-stat-number"><?php echo esc_html( $customer_orders_count ); ?></span>
				<span class="esaico-stat-label"><?php esc_html_e( 'Pedidos', 'saico-wc' ); ?></span>
			</div>
			<div class="esaico-stat-item">
				<span class="esaico-stat-number"><?php echo wc_price( $customer_total_spent ); ?></span>
				<span class="esaico-stat-label"><?php esc_html_e( 'Total Gastado', 'saico-wc' ); ?></span>
			</div>
		</div>
	</div>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
