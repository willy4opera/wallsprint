<?php do_action( 'woocommerce_before_mini_cart' ); ?>

<?php if ( ! WC()->cart->is_empty() ) : ?>
    <div class="product-mini-cart list-mini-cart-item bzotech-scrollbar">
        <?php
            do_action( 'woocommerce_before_mini_cart_contents' );
            $count_item = 0;
            echo '<input type="hidden" name="product-remove-ajax-nonce" class="product-remove-ajax-nonce" value="' . wp_create_nonce( 'product-remove-ajax-nonce' ) . '" />';
            foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                $count_item++;
                $_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                $product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
                $product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
                
                if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
                    $product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
                    $product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
                    ?>
                    <div class="item-info-cart product-mini-cart table-custom <?php echo esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key ) ); ?>" data-key="<?php echo esc_attr($cart_item_key)?>">
                        <div class="product-thumb">
                            <a href="<?php echo esc_url($product_permalink)?>" class="product-thumb-link">
                                <?php echo apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(array(70,70)), $cart_item, $cart_item_key )?>
                            </a>
                        </div>
                        <div class="product-info">
                            <h3 class="title14 product-title"><a href="<?php echo esc_url($product_permalink)?>"><?php echo esc_html($product_name)?></a></h3>
                            <div class="mini-cart-qty">
                                <span><?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="qty-num">' . sprintf( '%s', $cart_item['quantity'] ) . '</span>', $cart_item, $cart_item_key ); ?> x <span class=""><?php echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );?></span></span>
                            </div>
                        </div>
                        <div class="product-delete text-right">
                            <a href="#" class="remove-product title18"><i class="la la-trash"></i></a>
                        </div>
                    </div>
                    <?php
                }
            }

            do_action( 'woocommerce_mini_cart_contents' );
        ?>
    </div>

    <input class="get-cart-number" type="hidden" value="<?php echo esc_attr($count_item)?>">

    <div class="mini-cart-total text-uppercase title18 clearfix">
        <span class="pull-left"><?php esc_html_e( 'Total', 'bw-printxtore' ); ?></span>
        <strong class="pull-right mini-cart-total-price get-cart-price"><?php echo WC()->cart->get_cart_subtotal(); ?></strong>
    </div>

    <?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>

    <div class="mini-cart-button">
        <?php do_action( 'woocommerce_widget_shopping_cart_buttons' ); ?>
    </div>

<?php else : ?>

    <div class="mini-cart-empty"><?php esc_html_e( 'No products in the cart.', 'bw-printxtore' ); ?></div>

<?php endif; ?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>