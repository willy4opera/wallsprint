<?php
/**
 * Created by PhpStorm.
 * User: mai100it
 * Date: 26/02/2018
 * Time: 9:16 SA
 */
$i=1;
$animation = bzotech_get_option('shop_thumb_animation');
if($query->have_posts()) {

    if ( ! empty( $instance['title'] ) ) {
        echo wp_kses_post($args['before_title']) . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
    }
    ?>
    <div class="wg-product-slider heree">
        <div class="elbzotech-wrapper-slider display-swiper-navi-style1 ">
            <div class="elbzotech-swiper-slider swiper-container slider-wrap" data-items-custom="1:1" data-items="1" data-items-tablet="1" data-items-mobile="1"data-items-laptop="1" data-items-tablet-extra="1" data-items-mobile-extra ="1"  data-space="15" data-space-tablet="" data-space-mobile="" data-column="<?php echo esc_attr($number_row)?>" data-auto="" data-center="" data-loop="" data-speed="" data-navigation="" data-pagination=""> 
                <div class="swiper-wrapper"> 
                    <?php  while ($query->have_posts()) {
                        $query->the_post(); ?>
                        <div class="swiper-slide">
                            <div class="item-product item-product-wg flex-wrapper">
                                <div class="product-thumb">
                                    <?php bzotech_woocommerce_thumbnail_loop($image_size,$animation);?>
                                </div>
                                <div class="product-info">
                                    <?php bzotech_get_rating_html(true,false); ?>
                                    <?php the_title(' <h3 class="product-title"><a class="color-title" href="'.esc_url(get_the_permalink()).'">','</a></h3>'); ?>
                                    <?php bzotech_get_price_html(); ?>
                                    <?php do_action( 'bzotech_vendor_shop_loop_sold_by' );?>                                     
                                    
                                </div>
                            </div>
                        </div>
                        <?php
                        $i= $i+1;
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

<?php } wp_reset_postdata();