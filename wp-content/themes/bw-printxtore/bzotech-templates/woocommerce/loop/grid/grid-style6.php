<?php
global $product;
if(!isset($animation)) $animation = bzotech_get_option('shop_thumb_animation');

if(empty($size)|| $size=='custom') $size = array(460,460);
if(is_array($size)) $size = bzotech_size_random($size);


if(empty($view)){
    $view = 'grid';
}
if(empty($item_thumbnail)){
    $item_thumbnail = 'yes';
}
if(empty($item_title)){
    $item_title = 'yes';
}
if(empty($item_price)){
    $item_price = 'yes';
}
if(empty($item_rate)){
    $item_rate = 'yes';
}
if(empty($item_label)){
    $item_label = 'yes';
}
if(empty($item_quickview)){
    $item_quickview = 'yes';
}
if(empty($item_button)){
    $item_button = 'yes';
}
if(empty($item_countdown)){
    $item_countdown = 'no';
}
if(empty($item_brand)){
    $item_brand = 'no';
}
if(empty($item_flash_sale)){
    $item_flash_sale = 'no';
}
if(empty($item_gallery_hover)){
    $item_gallery_hover = 'no';
}
$class_attribute = 'attribute-close';
$data_tabs = get_post_meta(get_the_ID(),'bzotech_product_attribute_data',true);
if(!empty($data_tabs) and is_array($data_tabs) and !empty($data_tabs[0]['color_att']['color'])){
	$class_attribute = 'attribute-open';
}
?>
<?php if($view !== 'slider-masory') echo '<div '.$item_wrap.'>';?>
	<?php if($view !== 'slider-masory') echo '<div '.$item_inner.'>';?>
		<?php do_action( 'woocommerce_before_shop_loop_item' );?>
		<?php if($item_thumbnail == 'yes'):?>
			<div class="product-thumb <?php echo esc_attr($class_attribute); ?> product">
				<!-- bzotech_woocommerce_thumbnail_loop have $size and $animation -->
				<?php if($item_label == 'yes') bzotech_product_label()?>
				<?php bzotech_woocommerce_thumbnail_loop($size,$animation);?>
				<?php $attachment_ids = $product->get_gallery_image_ids(); 
				
				if($item_gallery_hover == 'yes' && !empty($attachment_ids) && is_array($attachment_ids) ){ 
					if(($animation == 'rotate-thumb' || $animation == 'translate-thumb') && count($attachment_ids) > 1){ 
						$thumb_hover_current = wp_get_attachment_image_src($attachment_ids[0], $size) ?>
						<div class="flex-wrapper gallery-hover justify_content-center" data-thumb-hover-current = "<?php echo esc_attr($thumb_hover_current[0])?>">
							<?php foreach ($attachment_ids as $key => $value) {
								
								if($key !== 0){
									$src_gallery_size = wp_get_attachment_image_src($value, $size);
									echo wp_get_attachment_image($value,array(50,50),false,array( 'class' => 'image-hover_gallery','data-urlsize'=>$src_gallery_size[0] ));
								}
								if($key == 4) break;
								
							} ?>
							
						</div>
					<?php } 
					else if(($animation !== 'rotate-thumb' && $animation !== 'translate-thumb')){ ?>
						<div class="flex-wrapper gallery-hover justify_content-center" data-thumb-hover-current = "<?php echo esc_attr(get_the_post_thumbnail_url(get_the_ID(),$size))?>">
							<?php foreach ($attachment_ids as $key => $value) {
								$src_gallery_size = wp_get_attachment_image_src($value, $size);
								echo wp_get_attachment_image($value,array(50,50),false,array( 'class' => 'image-hover_gallery','data-urlsize'=>$src_gallery_size[0] ));
									if($key == 3) break;								
							} ?>
							
						</div>
						<?php
					}
				}
				?>
				
				<div class="product-extra-link">
					
					
					<?php if($item_quickview == 'yes') bzotech_product_quickview('')?>	
					<?php if($item_button == 'yes'):?>
						<?php 
						$icon_after = $icon = '';
						if(!empty($button_icon['value'])){
							$icon = '<i class="'.$button_icon['value'].'"></i>';
							if($button_icon_pos == 'after-text'){
								$icon_after = $icon;
								$icon = '';
							}
						}else{
							$icon = '';
							$icon_after = '<i class="las la-cart-plus"></i>';
						}
						bzotech_addtocart_link([
							'icon'		=>$icon,
							'icon_after'=>$icon_after,
							'el_class'=>'addcart-link-style1',
							'style'=>'cart-icon'
						]);
						?>
					<?php endif?>
					<?php echo bzotech_compare_url();?>
				</div>
				<?php if($item_countdown == 'yes') bzotech_timer_countdown_product('countdown-style-item-'); ?>							
			</div>
		<?php endif?>
		<div class="product-info text-center">
			<?php do_action( 'woocommerce_before_shop_loop_item_title' );?>
			<?php if($item_rate == 'yes') bzotech_get_rating_html(true,false)?>
			
			<?php if($item_title == 'yes'):?>
				<h3 class="title16 product-title font-semibold text-capitalize">
					<a class="title-color" title="<?php echo esc_attr(the_title_attribute(array('echo'=>false)))?>" href="<?php the_permalink()?>"><?php the_title()?></a>
				</h3>
			<?php endif?>
			<?php do_action( 'woocommerce_shop_loop_item_title' );?>
			<?php do_action( 'woocommerce_after_shop_loop_item_title' );?>
			
			
			<?php if($item_price == 'yes') bzotech_get_price_html()?>
			<?php if(!empty($product_type) && $product_type == 'flash_sale' && $item_flash_sale == 'yes') bzotech_flashsale_countdown_and_stock_prod(); ?>
		</div>		
		<?php do_action( 'woocommerce_after_shop_loop_item' );?>
<?php if($view !== 'slider-masory') echo '</div></div>';?>
	