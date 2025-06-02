<?php
global $product;

/*'Get value to theme option : $show_latest, $show_upsell, $show_related, $number, $size, $item_res, $item_style,*/
extract(bzotech_show_single_product_data());
$related = wc_get_related_products($product->get_id(),$number);
if($show_related == '1' && $related): ?>  
    <div class="single-related-product bzo-visible-on-hover">
         <div class="title-related-product">
            <h2 class="font-title color-title title34 font-medium">
                <?php esc_html_e("Related products",'bw-printxtore')?>
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
            $slider_navigation = 'style1'; /*yes or empty*/
            $slider_pagination = ''; /*yes or empty*/
            $slider_scrollbar = ''; /*yes or empty*/
            $size = bzotech_get_size_crop($size);
            $item_wrap = 'class="swiper-slide item-grid-product-'.$item_style.'"';
            $item_inner = 'class="item-product"';
            
            $thumbnail_hover_animation=$button_icon_pos = $button_icon = $button_text = $column = $item_thumbnail = $item_quickview = $item_title = $item_rate = $item_price = $item_button = $item_label=$item_flash_sale= $item_brand=$item_countdown='';
            $item_thumbnail = bzotech_get_option('item_thumbnail');
            $item_quickview = bzotech_get_option('item_quickview');
            $item_title = bzotech_get_option('item_title');
            $item_rate = bzotech_get_option('item_rate');
            $item_price = bzotech_get_option('item_price');
            $item_button = bzotech_get_option('item_button');
            $item_label = bzotech_get_option('item_label');
            $item_countdown = bzotech_get_option('item_countdown');
            $item_brand = bzotech_get_option('item_brand');
            $item_flash_sale = bzotech_get_option('item_flash_sale');
            $thumbnail_hover_animation = bzotech_get_option('shop_thumb_animation');
           
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
            'item_rate'    => $item_rate,
            'item_brand'    => $item_brand,
            'item_flash_sale'    => $item_flash_sale,
            'item_countdown'    => $item_countdown,
            'item_label'        => $item_label,
            'item_title'        => $item_title,
            'item_price'        => $item_price,
            'item_button'       => $item_button,
            'item_quickview'    => $item_quickview,
            'animation'         => $thumbnail_hover_animation,
            );
        ?>

        <div class=" elbzotech-wrapper-slider-product elbzotech-wrapper-slider <?php if(!empty($slider_navigation)) echo esc_attr('display-swiper-navi-'.$slider_navigation); ?> <?php if(!empty($slider_pagination)) echo esc_attr('display-swiper-pagination-'.$slider_pagination); ?> <?php if(!empty($slider_scrollbar)) echo esc_attr('display-swiper-scrollbar-'.$slider_scrollbar); ?>">
            <div class="elbzotech-swiper-slider swiper-container" 
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
                    <?php
                        $args = array(
                            'post_type'           => 'product',
                            'ignore_sticky_posts'  => 1,
                            'no_found_rows'        => 1,
                            'posts_per_page'       => $number,                                    
                            'orderby'              => 'ID',
                            'post__in'             => $related,
                            'post__not_in'         => array( $product->get_id() )
                        );
                        $products = new WP_Query( $args );
                        if ( $products->have_posts() ) :
                            while ( $products->have_posts() ) : 
                                $products->the_post();                                  
                                bzotech_get_template_woocommerce('loop/grid/grid',$item_style,$attr,true);
                            endwhile;
                        endif;
                        wp_reset_postdata();
                    ?>
                </div>
            </div>
            <?php if ( $slider_navigation !== '' ):?>
                <div class="bzotech-swiper-navi">
                    <div class="swiper-button-nav swiper-button-next"><i class="las la-angle-right"></i></div>
                    <div class="swiper-button-nav swiper-button-prev"><i class="las la-angle-left"></i></div>
                </div>
            <?php endif?>
            <?php if ( $slider_pagination !== '' ):?>
                <div class="swiper-pagination "></div>
            <?php endif?>
            <?php if ( $slider_scrollbar !== '' ):?>
                <div class="swiper-scrollbar"></div>
            <?php endif?>
        </div>
    </div>
    <?php  
endif; 