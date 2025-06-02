<?php
$zoom_style = bzotech_get_value_by_id('product_image_zoom');
global $product;
$thumb_id = array(get_post_thumbnail_id());
$attachment_ids = $product->get_gallery_image_ids();
$attachment_ids = array_merge($thumb_id,$attachment_ids);
$gallerys = ''; $i = 1;
foreach ( $attachment_ids as $attachment_id ) {
    $image_link = wp_get_attachment_url( $attachment_id );
    if($i == 1) $gallerys .= $image_link;
    else $gallerys .= ','.$image_link;
    $i++;
}

$sidebar=bzotech_get_sidebar();
$sidebar_pos=$sidebar['position'];
$thumb_class = 'bzotech-col-md-6 bzotech-col-sm-12 bzotech-col-xs-12';
$info_class = 'bzotech-col-md-6 bzotech-col-sm-12 bzotech-col-xs-12';

$size_gallery = array(150,150);
$tab_style = bzotech_get_value_by_id('product_tab_detail');
?>
<div class="product-detail gallery-horizontal-js style-gallery-horizontal" >
    <div class="bzotech-row">
        <div class="<?php echo esc_attr($thumb_class)?>">
            <div class="product-detail-gallery-js product-detail-gallery">
                <div class="wrap-detail-gallery images <?php echo esc_attr($zoom_style)?>">
                    <div class="mid woocommerce-product-gallery__image image-lightbox" data-gallery="<?php echo esc_attr($gallerys)?>">
                        <?php bzotech_product_label()?>
                        <?php
                        $html = get_the_post_thumbnail(get_the_ID(),'shop_single',array('class'=> 'wp-post-image'));
                        echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, get_post_thumbnail_id( get_the_ID() ) );
                        ?>
                        <div class="video-content-mid"></div>
                    </div>
                    <?php
                    if (!is_singular('product') && $attachment_ids && has_post_thumbnail() && count($attachment_ids) > 1) {
                        ?>
                        <div class="gallery-control">
                            <div class="gallery-slider carousel" data-visible="4">
                                <?php
                                $i = 1;
                                foreach ( $attachment_ids as $attachment_id ) {
                                    if($i == 1) $active = 'active';
                                    else $active = '';
                                    $attributes      = array(
                                        'data-src'      => wp_get_attachment_image_url( $attachment_id, 'woocommerce_single' ),
                                        'data-srcset'   => wp_get_attachment_image_srcset( $attachment_id, 'woocommerce_single' ),
                                        'data-srcfull'  => wp_get_attachment_image_url( $attachment_id, 'full' ),
                                    );
                                    $html = wp_get_attachment_image($attachment_id,$size_gallery,false,$attributes );
                                    echo   '<a  data-number="'.esc_attr($i).'" title="'.esc_attr( get_the_title( $attachment_id ) ).'" class="'.esc_attr($active).'" href="javascript:;">
                                                '.apply_filters( 'woocommerce_single_product_image_thumbnail_html',$html,$attachment_id).'
                                            </a>';
                                    $i++;
                                }
                                ?>
                            </div>
                        </div>

                        <?php
                        do_action( 'woocommerce_product_thumbnails' );
                    }
                    ?>
                </div>
                <?php
                /**
                 * Hook: woocommerce_before_single_product_summary.
                 *
                 * @hooked woocommerce_show_product_sale_flash - 10
                 * @hooked woocommerce_show_product_images - 20
                 */
                do_action( 'woocommerce_before_single_product_summary' );
                ?>
            </div>
        </div>
        <div class="<?php echo esc_attr($info_class)?>">
            <div class="summary entry-summary product-detail-info <?php if($tab_style == 'tab-product-accordion2') echo 'tab-accordion-info-sticky'; ?> ">

                <h2 class="product-title-single font-title title30 font-medium color-title"><?php the_title()?></h2>
                <?php
                /**
                 * Hook: woocommerce_single_product_summary.
                 *
                 * @hooked woocommerce_template_single_title - 5
                 * @hooked woocommerce_template_single_rating - 10
                 * @hooked woocommerce_template_single_price - 10
                 * @hooked woocommerce_template_single_excerpt - 20
                 * @hooked woocommerce_template_single_add_to_cart - 30
                 * @hooked woocommerce_template_single_meta - 40
                 * @hooked woocommerce_template_single_sharing - 50
                 * @hooked WC_Structured_Data::generate_product_data() - 60
                 */
                do_action( 'woocommerce_single_product_summary' );
                echo '<a class="detail-quickview" href="'.esc_url(get_the_permalink()).'"><span>'.esc_html__('View Details','bw-printxtore').'</span><i class="las la-arrow-right"></i></a>'
                ?>
            </div>
        </div>
    </div>
</div>