<?php
if(function_exists('bzotech_set_post_view')) bzotech_set_post_view();
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
$get_style_woo_single = bzotech_get_value_by_id('sv_style_woo_single');
$sidebar=bzotech_get_sidebar();
$sidebar_pos=$sidebar['position'];
$thumb_class = 'bzotech-col-md-8 bzotech-col-sm-6 bzotech-col-xs-12';
$info_class = 'bzotech-col-md-4 bzotech-col-sm-6 bzotech-col-xs-12';
if($sidebar_pos!=='no'){
    $thumb_class = 'bzotech-col-lg-5 bzotech-col-md-6 bzotech-col-sm-6';
    $info_class = 'bzotech-col-lg-7 bzotech-col-md-6 bzotech-col-sm-6';

}
$size_gallery = array(150,200);
if(empty($data_visible)) $data_visible = 3;
$tab_style = bzotech_get_value_by_id('product_tab_detail');   
?>
<div class="product-detail <?php echo esc_attr($get_style_woo_single);?>">
    <div class="bzotech-row">
        <div class="<?php echo esc_attr($thumb_class)?>">
            <div class="product-detail-gallery-js product-detail-gallery <?php if($tab_style == 'tab-product-accordion2') echo 'tab-accordion-gallery-sticky'; ?> ">
                <div class="wrap-detail-gallery images <?php echo esc_attr($zoom_style)?>">
                    <div class="mid woocommerce-product-gallery__image image-lightbox" data-gallery="<?php echo esc_attr($gallerys)?>">
                        <?php bzotech_product_label()?>
                         <h2 class="product-title-single-vertical2 title150 font-bold font-title color-title"><?php the_title()?></h2>
                        <?php
                        $html = get_the_post_thumbnail(get_the_ID(),'shop_single',array('class'=> 'wp-post-image'));
                        echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, get_post_thumbnail_id( get_the_ID() ) );
                        ?>
                        <div class="video-content-mid"></div>
                    </div>
                    <?php
                    if ( $attachment_ids && has_post_thumbnail() && count($attachment_ids) > 1) {
                        ?>
                        <div class="gallery-control">
                            <div class="gallery-slider carousel" data-visible="<?php echo esc_attr($data_visible); ?>">
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
                 */
                do_action( 'woocommerce_before_single_product_summary' );
                ?>
            </div>
        </div>
        <div class="<?php echo esc_attr($info_class)?>">
            <div class="summary entry-summary product-detail-info <?php if($tab_style == 'tab-product-accordion2') echo 'tab-accordion-info-sticky'; ?> ">

                <h2 class="product-title-single title36 font-semibold font-title color-title"><?php the_title()?></h2>
                <?php
                /**
                 * Hook: woocommerce_single_product_summary.
                 *
                 */
                do_action( 'woocommerce_single_product_summary' );
                ?>
            </div>
        </div>
    </div>
    <?php bzotech_product_sticky_addcart(); ?>
</div>