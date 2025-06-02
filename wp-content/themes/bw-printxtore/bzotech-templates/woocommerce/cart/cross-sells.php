<?php
/*'Get value to theme option : $show_latest, $show_upsell, $show_related, $number, $size, $item_res, $item_style,*/
extract(bzotech_show_single_product_data());
$orderby = 'rand';
$order = 'desc';
$limit = 6;
$cross_sells = array_filter( array_map( 'wc_get_product', WC()->cart->get_cross_sells() ), 'wc_products_array_filter_visible' );
// Handle orderby and limit results.
$orderby     = apply_filters( 'woocommerce_cross_sells_orderby', $orderby );
$order       = apply_filters( 'woocommerce_cross_sells_order', $order );
$cross_sells = wc_products_array_orderby( $cross_sells, $orderby, $order );
$limit       = apply_filters( 'woocommerce_cross_sells_total', $limit );
$cross_sells = $limit > 0 ? array_slice( $cross_sells, 0, $limit ) : $cross_sells;
if($cross_sells):
    ?>  
    <div class="single-related-product cross-sell-slider">
        <div class="title-related-product">
            <h2 class="font-title color-title title34 text-uppercase font-medium">
                <?php esc_html_e("Cross sells products",'bw-printxtore')?>
            </h2>
        </div>
        <?php 
            $items = '4'; /*number*/
            $items_tablet = '3'; /*number*/
            $items_mobile = '1'; /*number*/
            $space = '30'; /*number px*/
            $space_tablet = '20'; /*number px*/
            $space_mobile = '10'; /*number px*/
            $column = ''; /*number*/
            $auto = ''; /*yes or empty*/
            $center = ''; /*yes or empty*/
            $loop = ''; /*yes or empty*/
            $speed = ''; /*number ms*/
            $slider_navigation = ''; /*yes or empty*/
            $slider_pagination = ''; /*yes or empty*/
            $slider_scrollbar = ''; /*yes or empty*/
            $size = bzotech_get_size_crop($size);

            $item_wrap = 'class="swiper-slide item-grid-product-'.$item_style.'"';
            $item_inner = 'class="item-product"';
            $button_icon_pos = $button_icon = $button_text = $column = '';
            $item_thumbnail = $item_quickview = $item_label = $item_title = $item_rate = $item_price = $item_button = 'yes';
            $thumbnail_hover_animation = 'zoom-thumb';
            $view = 'slider';
            $attr = array(
            'item_wrap'         => $item_wrap,
            'item_inner'        => $item_inner,
            'button_icon_pos'   => $button_icon_pos,
            'button_icon'       => $button_icon,
            'button_text'       => $button_text,
            'size'              => $size,
            'view'              => $view,
            'column'            => $column,
            'item_style'        => $item_style,
            'item_thumbnail'    => $item_thumbnail,
            'item_label'        => $item_label,
            'item_title'        => $item_title,
            'item_price'        => $item_price,
            'item_button'       => $item_button,
            'animation'         => $thumbnail_hover_animation
            );
        ?>
        <?php woocommerce_product_loop_start(); ?>
        <div class="elbzotech-wrapper-slider <?php if(!empty($slider_navigation)) echo esc_attr('display-swiper-navi-'.$slider_navigation); ?> <?php if(!empty($slider_pagination)) echo esc_attr('display-swiper-pagination-'.$slider_pagination); ?> <?php if(!empty($slider_scrollbar)) echo esc_attr('display-swiper-scrollbar-'.$slider_scrollbar); ?>">
            <div class="elbzotech-swiper-slider swiper-container slider-nav-group-top" 
            data-items-custom="<?php echo esc_attr($items_custom)?>" 
                data-items="<?php echo esc_attr($items)?>" 
                data-items-tablet="<?php echo esc_attr($items_tablet)?>" 
                data-items-mobile="<?php echo esc_attr($items_mobile)?>" 
                data-space="<?php echo esc_attr($space)?>" 
                data-space-tablet="<?php echo esc_attr($space_tablet)?>" 
                data-space-mobile="<?php echo esc_attr($space_mobile)?>" 
                data-column="<?php echo esc_attr($column)?>" 
                data-auto="<?php echo esc_attr($auto)?>" 
                data-center="<?php echo esc_attr($center)?>" 
                data-loop="<?php echo esc_attr($loop)?>" 
                data-speed="<?php echo esc_attr($speed)?>" 
                data-navigation="<?php echo esc_attr($slider_navigation)?>" 
                data-pagination="<?php echo esc_attr($slider_pagination)?>" 
            >
                <div class="swiper-wrapper">
                    <?php foreach ( $cross_sells as $cross_sell ) : ?>

                        <?php
                        $post_object = get_post( $cross_sell->get_id() );

                        setup_postdata( $GLOBALS['post'] =& $post_object );

                        bzotech_get_template_woocommerce('loop/grid/grid',$item_style,$attr,true);?>

                    <?php endforeach; ?>
                    
                </div>
            </div>
            <?php if ( $slider_navigation !== '' ):?>
                <div class="bzotech-swiper-navi">
                    <div class="swiper-button-nav swiper-button-next"><i class="las la-long-arrow-alt-right"></i></div>
                    <div class="swiper-button-nav swiper-button-prev"><i class="las la-long-arrow-alt-left"></i></div>
                </div>
            <?php endif?>
            <?php if ( $slider_pagination !== '' ):?>
                <div class="swiper-pagination "></div>
            <?php endif?>
            <?php if ( $slider_scrollbar !== '' ):?>
                <div class="swiper-scrollbar"></div>
            <?php endif?>
        </div>
        <?php woocommerce_product_loop_end(); ?>
    </div>
<?php endif;
wp_reset_postdata();