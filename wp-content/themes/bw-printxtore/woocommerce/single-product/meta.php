<?php
/**
 * Single Product Meta
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/meta.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $product;

?>
<div class="product_meta item-product-meta-info flex-wrapper flex_wrap-wrap align_items-center">

    <?php do_action( 'woocommerce_product_meta_start' ); ?>

    
    <?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>
        
        <div class="sku_wrapper"><label><?php esc_html_e( 'Product Code:', 'bw-printxtore' ); ?></label> <div class="meta-item-list"><span class="sku">
            <?php if( $sku = $product->get_sku() )  echo esc_html($sku); else esc_html__( 'N/A', 'bw-printxtore' ); ?>
                
            </span></div></div>

    <?php endif; ?>
    <?php echo wc_get_product_category_list( $product->get_id(), ', ', '<div class="posted_in"><label>' . _n( 'Category:', 'Categories:', count( $product->get_category_ids() ), 'bw-printxtore' ) . '</label><div class="meta-item-list">', '</div></div>' ); ?>

    <?php echo wc_get_product_tag_list( $product->get_id(), ', ', '<div class="tagged_as"><label>' . _n( 'Tag:', 'Tags:', count( $product->get_tag_ids() ), 'bw-printxtore' ) . '</label><div class="meta-item-list">', '</div></div>' ); ?>
    <?php do_action( 'woocommerce_product_meta_end' ); ?>

</div>
   