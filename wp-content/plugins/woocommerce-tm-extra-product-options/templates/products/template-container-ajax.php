<?php
/**
 * The template for displaying the product html via ajax
 *
 * @author  ThemeComplete
 * @package Extra Product Options/Templates/Products
 * @version 6.4
 */

defined( 'ABSPATH' ) || exit;
if ( isset( $product_id, $product_list, $template ) ) :
	$product_id   = absint( $product_id );
	$product_list = (array) $product_list;
	$template     = (string) $template;


	$attributes = $product_list[ $product_id ];
	add_filter( 'woocommerce_product_variation_title_include_attributes', [ THEMECOMPLETE_EPO_ASSOCIATED_PRODUCTS(), 'woocommerce_product_variation_title_include_attributes' ] );
	$current_product = wc_get_product( $product_id );
	remove_filter( 'woocommerce_product_variation_title_include_attributes', [ THEMECOMPLETE_EPO_ASSOCIATED_PRODUCTS(), 'woocommerce_product_variation_title_include_attributes' ] );

	if ( $current_product instanceof WC_Product && isset( $_REQUEST['discount'] ) && isset( $_REQUEST['discount_type'] ) && ! empty( $_REQUEST['discount'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		/**
		 * This is for simple products only
		 * variable products discounts are in the files
		 * /includes/fields/class-tm-epo-fields-product.php
		 * /includes/classes/class-tm-epo-associated-products.php
		 * depending on the situation.
		 */
		$current_price = THEMECOMPLETE_EPO_ASSOCIATED_PRODUCTS()->get_discounted_price( $current_product->get_price(), wp_unslash( $_REQUEST['discount'] ), wp_unslash( $_REQUEST['discount_type'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
		$current_price = apply_filters( 'wc_epo_remove_current_currency_price', $current_price );
		$current_product->set_sale_price( $current_price );
		$current_product->set_price( $current_price );
		$discount_applied = true;
	}

	require apply_filters( 'wc_epo_template_path_product_element', THEMECOMPLETE_EPO_TEMPLATE_PATH ) . apply_filters( 'wc_epo_template_element', 'products/' . $template . '.php', 'product', [] );
endif;
