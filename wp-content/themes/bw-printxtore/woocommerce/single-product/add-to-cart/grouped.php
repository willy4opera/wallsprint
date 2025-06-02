<?php
/**
 * Grouped product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/grouped.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;

global $product, $post;

do_action( 'woocommerce_before_add_to_cart_form' ); ?>
<div class="list-product-group">
	<form class="cart" method="post" enctype='multipart/form-data'>
		<table cellspacing="0" class="group_table">
			<tbody>
				<?php
					$quantites_required = false;

					foreach ( $grouped_products as $grouped_product ) {
						$post_object        = get_post( $grouped_product->get_id() );
						$quantites_required = $quantites_required || ( $grouped_product->is_purchasable() && ! $grouped_product->has_options() );

						setup_postdata( $GLOBALS['post'] =& $post_object );
						?>
						<tr id="product-<?php the_ID(); ?>" <?php post_class(); ?>>
							<td class="group-thumb">
								<div class="product-thumb">
									<a href="<?php echo esc_url(get_the_permalink())?>" class="product-thumb-link"><?php echo get_the_post_thumbnail($grouped_product->get_id(),array(100,100))?></a>
								</div>
							</td>
							<td class="group-info">
								<div class="product-info">
									<h3 class="product-title title14"><a href="<?php echo esc_url(get_the_permalink())?>"><?php the_title()?></a></h3>
									<?php do_action( 'woocommerce_grouped_product_list_before_price', $grouped_product ); ?>
									<?php
										echo apply_filters('bzotech_output_content',$grouped_product->get_price_html());
										echo wc_get_stock_html( $grouped_product );
									?>
								</div>
							</td>
							<td class="group-qty">
								<?php if ( ! $grouped_product->is_purchasable() || $grouped_product->has_options() ) : ?>
									<?php woocommerce_template_loop_add_to_cart(); ?>

								<?php elseif ( $grouped_product->is_sold_individually() ) : ?>
									<input type="checkbox" name="<?php echo esc_attr( 'quantity[' . $grouped_product->get_id() . ']' ); ?>" value="1" class="wc-grouped-product-add-to-cart-checkbox" />

								<?php else : ?>
									<?php
										/**
										 * @since 3.0.0.
										 */
										do_action( 'woocommerce_before_add_to_cart_quantity' );

										woocommerce_quantity_input( array(
											'input_name'  => 'quantity[' . $grouped_product->get_id() . ']',
											'input_value' => isset( $_POST['quantity'][ $grouped_product->get_id() ] ) ? wc_stock_amount( $_POST['quantity'][ $grouped_product->get_id() ] ) : 0,
											'min_value'   => apply_filters( 'woocommerce_quantity_input_min', 0, $grouped_product ),
											'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $grouped_product->get_max_purchase_quantity(), $grouped_product ),
										) );

										/**
										 * @since 3.0.0.
										 */
										do_action( 'woocommerce_after_add_to_cart_quantity' );
									?>
								<?php endif; ?>
							</td>							
						</tr>
						<?php
					}
					wp_reset_postdata();
				?>
			</tbody>
		</table>

		<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" />

		<?php if ( $quantites_required ) : ?>

			<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>
			<div class="flex-wrapper add_to_cart-after-cart_button">
				<button type="submit" class="single_add_to_cart_button button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>

				<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
			</div>
		<?php endif; ?>
	</form>
</div>
<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

