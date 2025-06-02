<?php
if(!isset($animation)) $animation = bzotech_get_option('shop_thumb_animation');
if(empty($size_list)) $size_list = array(370,370);
if(empty($item_quickview)) $item_quickview = 'yes';
$col_class = 'bzotech-col-md-12 bzotech-col-sm-12 bzotech-col-xs-12';
global $post;
global $product;
?>
<div class="bzotech-col-md-12">
    <div class="item-product item-list-default flex-wrapper">
        	<?php do_action( 'woocommerce_before_shop_loop_item' );?>
            <?php if(has_post_thumbnail()):?>
                <div class="list-thumb-wrap">
					<div class="product-thumb">
						<!-- bzotech_woocommerce_thumbnail_loop have $size and $animation -->
						<?php bzotech_woocommerce_thumbnail_loop($size,$animation);?>
						<?php if($item_label == 'yes') bzotech_product_label()?>
						<?php do_action( 'woocommerce_before_shop_loop_item_title' );?>
					</div>
                </div>
            <?php endif;?>
            <div class="list-info-wrap product">
                <div class="product-info">
					<h3 class="title20 product-title font-medium font-title">
						<a class="color-title" title="<?php echo esc_attr(the_title_attribute(array('echo'=>false)))?>" href="<?php the_permalink()?>"><?php the_title()?></a>
					</h3>
				<?php do_action( 'woocommerce_shop_loop_item_title' );?>
				<?php do_action( 'woocommerce_after_shop_loop_item_title' );?>
				<?php  bzotech_get_rating_html(true,false)?>
				<?php  bzotech_get_price_html(); ?>
				
                <?php echo '<div class="desc">'.bzotech_substr(apply_filters( 'woocommerce_short_description', $post->post_excerpt ),0,300).'</div>'; ?>

                </div>
                <div class="product-extra-link-defaut2 addcart-link-wrap flex-wrapper flex_wrap-wrap align_items-center">
					<?php
					if($product->get_type() == 'simple')
						woocommerce_quantity_input();
					$icon_after = $icon = '';
					if(isset($button_icon['value'])){
						$icon = '<i class="'.$button_icon['value'].'"></i>';
						if($button_icon_pos == 'after-text'){
							$icon_after = $icon;
							$icon = '';
						}
					}
					bzotech_addtocart_link([
						'icon'		=>$icon,
						'text'		=>$button_text,
						'icon_after'=>$icon_after,
						'el_class'=>'elbzotech-bt-default',
						'style'=>''
					]);
					?>
					<div class="list-compare-wishlist">
						<?php if(class_exists('YITH_WCWL')) echo bzotech_wishlist_url('<i class="la-heart-o la title22" aria-hidden="true"></i>','');?>
						<?php if(class_exists('YITH_Woocompare')) echo bzotech_compare_url('<i class="las la-exchange-alt title22"></i>',false,'');?>
						<?php if($item_quickview == 'yes') bzotech_product_quickview('','<i class="las la-eye title22"></i>')?>
					
					</div>
				</div>
            </div>
            
            <?php do_action( 'woocommerce_after_shop_loop_item' );?>
       
    </div>
</div>