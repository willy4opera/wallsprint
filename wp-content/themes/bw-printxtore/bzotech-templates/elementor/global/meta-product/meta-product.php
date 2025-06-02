<?php
namespace Elementor;
extract($settings);
global $product;

$product = wc_get_product();


switch ($content) {
	case 'category':
		if ( empty( $product ) ) echo '<div class="posted_in"><label>Categories:</label><div class="meta-item-list"><a href="#" rel="tag">category 1</a>, <a href="#" rel="tag">category 2</a>, <a href="#" rel="tag">category 3</a></div></div>'; 
		else echo wc_get_product_category_list( $product->get_id(), ', ', '<div class="posted_in"><label>' . _n( 'Category:', 'Categories:', count( $product->get_category_ids() ), 'bw-printxtore' ) . '</label><div class="meta-item-list">', '</div></div>' );
		break;
	case 'tag':
	if ( empty( $product ) ) echo '<div class="tagged_as"><label>Tags:</label><div class="meta-item-list"><a href="#" rel="tag">Tag 1</a>, <a href="#" rel="tag">Tag 2</a>, <a href="#" rel="tag">Tag 3</a></div></div>'; 
	else echo wc_get_product_tag_list( $product->get_id(), ', ', '<div class="tagged_as"><label>' . _n( 'Tag:', 'Tags:', count( $product->get_tag_ids() ), 'bw-printxtore' ) . '</label><div class="meta-item-list">', '</div></div>' );
		break;
	case 'reviews':
		 
		 if ( empty( $product ) ) {
			 echo'<div class="product-rating-total-sold flex-wrapper flex_wrap-wrap align_items-center">
	        	<div class="woocommerce-product-rating flex-wrapper flex_wrap-wrap align_items-center">
	        		<ul class="wrap-rating list-inline-block">
	                        <li>
	                            <div class="product-rate">
	                                <div class="product-rating" style="width:80%"></div>
	                            </div>
	                        </li></ul>        		        			        			<a href="#reviews" class="woocommerce-review-link" rel="nofollow">(<span class="count">1</span> customer review)</a>
	        			        		        	</div>

	        </div>';
	    } else{
	    	echo '<div class="elementor-template_single_rating">';
	    	woocommerce_template_single_rating();
	    	echo '</div>';
	    } 
		break;
	case 'sold':
		if ( empty( $product ) ) echo '<div class="total-sold">Sold: <span class="count">0</span></div>';
		else{
			$total_sold = get_post_meta(get_the_ID(), 'total_sales', true );
	        echo '<div class="total-sold">'.esc_html__('Sold: ','bw-printxtore').'<span class="count">'.$total_sold.'</span></div>';
		}
		
		break;
	
	case 'stock':
		if ( empty( $product ) ) echo '<div class="total-stock">stock: <span class="count">0</span></div>';
		else{
			$total_stock = get_post_meta(get_the_ID(), '_stock', true );
			if($total_stock !== 0 && !empty($total_stock))
	        echo '<div class="total-stock">'.esc_html__('Stock: ','bw-printxtore').'<span class="count">'.$total_stock.'</span></div>';
		}
		
		break;
	default:
	if ( empty( $product ) ) echo '<div class="sku_wrapper"><label>Sku: </label> <div class="meta-item-list"><span class="sku">N/A</span></div></div>'; 
	else if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>
        
	        <div class="sku_wrapper"><label><?php echo esc_html($title); ?></label> <div class="meta-item-list"><span class="sku">
	            <?php if( $sku = $product->get_sku() )  echo esc_html($sku); else esc_html__( 'N/A', 'bw-printxtore' ); ?>
	                
	            </span></div></div>

	    <?php endif; 
		break;
}