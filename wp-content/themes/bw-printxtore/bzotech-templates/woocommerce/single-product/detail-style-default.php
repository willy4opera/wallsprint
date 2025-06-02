<?php
$show_hide_image_gallery_woo = bzotech_get_value_by_id('show_hide_image_gallery_woo');
if($show_hide_image_gallery_woo == 'show'){  
    add_action( 'woocommerce_before_single_product_summary','woocommerce_show_product_images',20 );
    add_action( 'woocommerce_product_thumbnails','woocommerce_show_product_thumbnails',20 );
}
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
if($sidebar_pos!=='no'){
    $thumb_class = 'bzotech-col-lg-6 bzotech-col-md-6 bzotech-col-sm-12';
    $info_class = 'bzotech-col-lg-6 bzotech-col-md-6 bzotech-col-sm-12';
}
$size_gallery = array(150,150);
$tab_style = bzotech_get_value_by_id('product_tab_detail'); 
?>
<div class="product-detail gallery-horizontal-js <?php echo esc_attr($style_woo_single);?>" >
    <div class="bzotech-row">
        <div class="<?php echo esc_attr($thumb_class)?>">
            <div class="product-detail-gallery">
                
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
                ?>
            </div>
        </div>
    </div>
    <?php bzotech_product_sticky_addcart(); ?>
</div>