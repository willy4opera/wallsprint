<?php
/**
 * The template for displaying the product element image
 *
 * This template can be overridden by copying it to yourtheme/tm-extra-product-options/products/template-image
 *
 * NOTE that we may need to update template files and you
 * (the plugin or theme developer) will need to copy the new files
 * to your theme or plugin to maintain compatibility.
 *
 * @author  ThemeComplete
 * @package Extra Product Options/Templates/Products
 * @version 6.4
 */

defined( 'ABSPATH' ) || exit;

if ( isset( $product_id ) ) {
	$image_data    = false;
	$image_post_id = '';
	$image_title   = '';
	$image_link    = '';
	if ( has_post_thumbnail( $product_id ) ) {
		$image_post_id = get_post_thumbnail_id( $product_id );
		if ( false !== $image_post_id ) {
			if ( isset( $product_title ) ) {
				$image_title = $product_title;
			} else {
				$image_title = get_the_title( $image_post_id );
			}
			$image_data = wp_get_attachment_image_src( $image_post_id, 'full' );

			if ( ! $image_data ) {
				$image_data = false;
			} else {
				$image_link = $image_data[0];
			}
		}
	}
	if ( false !== $image_data ) {
		?>
		<figure class="tc-product-image">
		<?php
		echo get_the_post_thumbnail(
			$product_id,
			'shop_catalog',
			[
				'title'                   => $image_title,
				'data-caption'            => get_post_field( 'post_excerpt', (int) $image_post_id ),
				'data-large_image'        => $image_link,
				'data-large_image_width'  => $image_data[1],
				'data-large_image_height' => $image_data[2],
			]
		);
		?>
		</figure>
		<?php
	} else {
		?>
		<figure class="tc-product-image woocommerce-product-gallery__image--placeholder">
		<img class="wp-post-image" src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" alt="<?php esc_attr_e( 'Awaiting product image', 'woocommerce-tm-extra-product-options' ); ?>"/>
		</figure>
		<?php
	}
}
