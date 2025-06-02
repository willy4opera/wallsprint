<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.9.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' ); ?>

<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
    <?php do_action( 'woocommerce_before_cart_table' ); ?>

    <table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
        <thead>
        <tr>
            <th class="product-remove"><i class="la la-trash color-title title18"></i></th>
            <th class="product-thumbnail  color-title title16 font-semibold"><?php esc_html_e( 'Photo', 'bw-printxtore' ); ?></th>
            <th class="product-name color-title title16 font-semibold"><?php esc_html_e( 'Product', 'bw-printxtore' ); ?></th>
            <th class="product-price  color-title title16 font-semibold"><?php esc_html_e( 'Price', 'bw-printxtore' ); ?></th>
            <th class="product-quantity  color-title title16 font-semibold"><?php esc_html_e( 'Quantity', 'bw-printxtore' ); ?></th>
            <th class="product-subtotal  color-title title16 font-semibold text-right"><?php esc_html_e( 'Total', 'bw-printxtore' ); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php do_action( 'woocommerce_before_cart_contents' ); ?>

        <?php
        echo '<input type="hidden" name="update-mini-cart-ajax-nonce" class="update-mini-cart-ajax-nonce" value="' . wp_create_nonce( 'update-mini-cart-ajax-nonce' ) . '" />';
        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
            $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
            $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

            if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                ?>
                <tr class="woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

                    <td class="product-remove">
                        <?php
                        echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
                            '<a href="%s" class="remove" aria-label="%s" data-product_id="%s" data-product_sku="%s"><i class="la la-close"></i></a>',
                            esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                            esc_html__( 'Remove this item', 'bw-printxtore' ),
                            esc_attr( $product_id ),
                            esc_attr( $_product->get_sku() )
                        ), $cart_item_key );
                        ?>
                    </td>

                    <td class="product-thumbnail">
                        <?php
                        $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

                        if ( ! $product_permalink ) {
                            echo apply_filters('bzotech_output_content',$thumbnail); 
                        } else {
                            printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail );
                        }
                        ?>
                    </td>

                    <td class="product-name" data-title="<?php esc_attr_e( 'Product', 'bw-printxtore' ); ?>">
                        <?php
                        if ( ! $product_permalink ) {
                            echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
                        } else {
                            echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a class="product-title color-title title16 font-semibold" href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
                        }

                        do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

                        echo wc_get_formatted_cart_item_data( $cart_item );

                        if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
                            echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'bw-printxtore' ) . '</p>', $product_id ) );
                        }
                        ?>
                    </td>

                    <td class="product-price" data-title="<?php esc_attr_e( 'Price', 'bw-printxtore' ); ?>">
                        <?php
                        echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ); 
                        ?>
                    </td>

                    <td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'bw-printxtore' ); ?>">
                        <?php
                        if ( $_product->is_sold_individually() ) {
                            $product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
                        } else {
                            $product_quantity = woocommerce_quantity_input( array(
                                'input_name'   => "cart[{$cart_item_key}][qty]",
                                'input_value'  => $cart_item['quantity'],
                                'max_value'    => $_product->get_max_purchase_quantity(),
                                'min_value'    => '0',
                                'product_name' => $_product->get_name(),
                            ), $_product, false );
                        }

                        echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item );
                        ?>
                    </td>

                    <td class="product-subtotal text-right" data-title="<?php esc_attr_e( 'Total', 'bw-printxtore' ); ?>">
                        <?php
                        echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
                        ?>
                    </td>
                </tr>
                <?php
            }
        }
        ?>

        <?php do_action( 'woocommerce_cart_contents' ); ?>

        <tr>
            <td colspan="6" class="actions">

                <?php if ( wc_coupons_enabled() ) { ?>
                    <div class="coupon">
                        <label for="coupon_code"><?php esc_html_e( 'Coupon:', 'bw-printxtore' ); ?></label> <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'bw-printxtore' ); ?>" /> <button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'bw-printxtore' ); ?>"><?php esc_html_e( 'Apply coupon', 'bw-printxtore' ); ?></button>
                        <?php do_action( 'woocommerce_cart_coupon' ); ?>
                    </div>
                <?php }
                echo '<input type="hidden" name="update-mini-cart-ajax-nonce" class="update-mini-cart-ajax-nonce" value="' . wp_create_nonce( 'update-mini-cart-ajax-nonce' ) . '" />';
                ?>

                <button type="submit" class="button" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'bw-printxtore' ); ?>"><?php esc_html_e( 'Update cart', 'bw-printxtore' ); ?></button>

                <?php do_action( 'woocommerce_cart_actions' ); ?>

                <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
            </td>
        </tr>

        <?php do_action( 'woocommerce_after_cart_contents' ); ?>
        </tbody>
    </table>
    <?php do_action( 'woocommerce_after_cart_table' ); ?>
</form>
<?php do_action( 'bzotech_after_cart_form' ); ?>
<div class="cart-collaterals">
    <?php
    /**
     * Cart collaterals hook.
     *
     * @hooked woocommerce_cross_sell_display
     * @hooked woocommerce_cart_totals - 10
     */
    do_action( 'woocommerce_cart_collaterals' );
    ?>
</div>

<?php do_action( 'woocommerce_after_cart' ); ?>
