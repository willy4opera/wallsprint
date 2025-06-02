<?php
/**
 * Single Product Rating
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/rating.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}

global $product;

if ( ! wc_review_ratings_enabled() ) {
	return;
}
$total_sold = get_post_meta(get_the_ID(), 'total_sales', true );
$rating_count = $product->get_rating_count();
$review_count = $product->get_review_count();
$average      = $product->get_average_rating();
if ( $rating_count > 0 || $total_sold !== '0') :
    echo '<div class="product-rating-total-sold flex-wrapper flex_wrap-wrap align_items-center">';
        if ( $rating_count > 0 ) : ?>

        	<div class="woocommerce-product-rating flex-wrapper flex_wrap-wrap align_items-center">
        		<?php echo wc_get_rating_html( $average, $rating_count );  ?>
        		<?php if ( comments_open() ) : ?>
        			
        			<a href="#reviews" class="woocommerce-review-link" rel="nofollow">(<?php printf( _n( '%s customer review', '%s customer reviews', $review_count, 'bw-printxtore' ), '<span class="count">' . esc_html( $review_count ) . '</span>' ); ?>)</a>
        		<?php endif ?>
        	</div>

        <?php endif;
        
        if($total_sold !== '0'){
            echo '<div class="total-sold">'.esc_html__('Sold: ','bw-printxtore').$total_sold.'</div>';
        }
    echo '</div>';
endif;
