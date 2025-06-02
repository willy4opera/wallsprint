<?php
/**
 * Woocommerce Compare page
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Compare
 * @version 1.1.4
 */

defined( 'YITH_WOOCOMPARE' ) || exit; // Exit if accessed directly.

do_action( 'yith_woocompare_before_compare' );

// Remove the style of woocommerce.
if ( defined( 'WOOCOMMERCE_USE_CSS' ) && WOOCOMMERCE_USE_CSS ) {
	wp_dequeue_style( 'woocommerce_frontend_styles' );
}

// Removes scripts for massive-dynamic theme.
remove_action( 'wp_enqueue_scripts', 'pixflow_theme_scripts' );

?><!DOCTYPE html>
<!--[if IE 6]>
<html id="ie6" class="ie"<?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 7]>
<html id="ie7" class="ie"<?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html id="ie8" class="ie"<?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 9]>
<html id="ie9" class="ie"<?php language_attributes(); ?>>
<![endif]-->
<!--[if gt IE 9]>
<html class="ie"<?php language_attributes(); ?>>
<![endif]-->
<!--[if !IE]>
<html <?php language_attributes(); ?>>
<![endif]-->

<!-- START HEAD -->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width" />
	<title><?php esc_html_e( 'Product Comparison', 'yith-woocommerce-compare' ); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11" />

	<?php wp_head(); ?>

	<?php
	/**
	 * DO_ACTION: yith_woocompare_popup_head
	 *
	 * Allows to render some content in the head of the comparison popup.
	 */
	do_action( 'yith_woocompare_popup_head' );
	?>

	<style type="text/css">
		body.loading {
			background: url("<?php echo YITH_WOOCOMPARE_URL; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>assets/images/colorbox/loading.gif") no-repeat scroll center center transparent;
		}
	</style>
</head>
<!-- END HEAD -->

<?php global $product; ?>

<!-- START BODY -->
<body <?php body_class( 'woocommerce yith-woocompare-popup' ); ?>>

<?php wc_get_template( 'yith-compare-table.php', $args, '', YITH_WOOCOMPARE_TEMPLATE_PATH ); ?>

<?php
/**
 * DO_ACTION: yith_woocompare_popup_footer
 *
 * Allows to render some content in the footer of the comparison popup.
 */
do_action( 'yith_woocompare_popup_footer' );
?>

<?php do_action( 'wp_print_footer_scripts' ); ?>

<script type="text/javascript">

	jQuery(document).ready(function($){

		$('a').attr('target', '_parent');

		var body = $('body'),
			redirect_to_cart = false;

		// close colorbox if redirect to cart is active after add to cart
		body.on( 'adding_to_cart', function ( $thisbutton, data ) {
			if( wc_add_to_cart_params.cart_redirect_after_add == 'yes' ) {
				wc_add_to_cart_params.cart_redirect_after_add = 'no';
				redirect_to_cart = true;
			}
		});

		// remove add to cart button after added
		body.on('added_to_cart', function( ev, fragments, cart_hash, button ){

			if( redirect_to_cart == true ) {
				// redirect
				parent.window.location = wc_add_to_cart_params.cart_url;
				return;
			}

			$('a').attr('target', '_parent');

			// Replace fragments
			if ( fragments ) {
				$.each(fragments, function(key, value) {
					$(key, window.parent.document).replaceWith(value);
				});
			}
		});
	});

</script>

</body>
</html>

<?php

do_action( 'yith_woocompare_after_compare' );
