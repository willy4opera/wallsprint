<?php
global $product;
if(!isset($animation)) $animation = bzotech_get_option('shop_thumb_animation');

if (empty($size) || $size == 'custom') {
	$size = array(300, 300);
}
if (is_array($size)) {
	$size = bzotech_size_random($size);
}

if (empty($view)) {
	$view = 'grid';
}
if (empty($item_thumbnail)) {
	$item_thumbnail = 'yes';
}
if (empty($item_title)) {
	$item_title = 'yes';
}
if (empty($item_price)) {
	$item_price = 'yes';
}
if (empty($item_rate)) {
	$item_rate = 'no';
}
if (empty($item_label)) {
	$item_label = 'yes';
}
if (empty($item_quickview)) {
	$item_quickview = 'yes';
}
if (empty($item_button)) {
	$item_button = 'yes';
}
if (empty($item_countdown)) {
	$item_countdown = 'no';
}
if (empty($item_brand)) {
	$item_brand = 'no';
}
if (empty($item_flash_sale)) {
	$item_flash_sale = 'no';
}
if (empty($item_attribute)) {
	$item_attribute = 'no';
}
if (empty($item_gallery_hover)) {
	$item_gallery_hover = 'no';
}
if (empty($excerpt)) {
	$excerpt = 50;
}
if (empty($item_excerpt)) {
	$item_excerpt = 'yes';
}
$class_attribute = 'attribute-close';
$data_tabs = get_post_meta(get_the_ID(), 'bzotech_product_attribute_data', true);
if (!empty($data_tabs) and is_array($data_tabs) and !empty($data_tabs[0]['color_att']['color'])) {
	$class_attribute = 'attribute-open';
}
?> 
<?php if($view !== 'slider-masory') echo '<div '.$item_wrap.'>';?>
	<?php if($view !== 'slider-masory') echo '<div '.$item_inner.'>';?>
		<?php do_action( 'woocommerce_before_shop_loop_item' );?>
		<?php if($item_thumbnail == 'yes'):?>
			<div class="product-thumb <?php echo esc_attr($class_attribute); ?> product">
				<div class="product-thumb-inner">
					<!-- bzotech_woocommerce_thumbnail_loop have $size and $animation -->
					<?php if($item_label == 'yes') bzotech_product_label()?>
					<?php bzotech_woocommerce_thumbnail_loop($size,$animation);?>
					

				</div>
				<?php if($item_countdown == 'yes') bzotech_timer_countdown_product('countdown-style-item-'); ?>		

				<?php $attachment_ids = $product->get_gallery_image_ids(); 
				if($item_gallery_hover == 'yes' && !empty($attachment_ids) && is_array($attachment_ids) ){ 
					if(($animation == 'rotate-thumb' || $animation == 'translate-thumb') && count($attachment_ids) > 1){ 
						$thumb_hover_current = wp_get_attachment_image_src($attachment_ids[0], $size) ?>
						<div class="flex-wrapper gallery-hover" data-thumb-hover-current = "<?php echo esc_attr($thumb_hover_current[0])?>">
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
						<div class="flex-wrapper gallery-hover" data-thumb-hover-current = "<?php echo esc_attr(get_the_post_thumbnail_url(get_the_ID(),$size))?>">
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

				<?php

				if($item_attribute == 'yes'){
					$variables_product = new WC_Product_Variable(get_the_ID());
					$available_variations = $variables_product->get_available_variations();

					if (!empty($available_variations) && true !== $available_variations) {
						
						$attributes = $variables_product->get_variation_attributes();

						$attribute_keys = array_keys($attributes);
						$variations_json = wp_json_encode($available_variations);
						$variations_attr = function_exists('wc_esc_json') ? wc_esc_json($variations_json) : _wp_specialchars($variations_json, ENT_QUOTES, 'UTF-8', true);
						?>
						<div class="wap-item-attribute variations flex-wrapper justify_content-center">
							<?php foreach ($attributes as $attribute_name => $options): ?>
								<div class="attribute-item js-count-plus-attr">
									<div class="attribute-item__value value">
										<?php
										wc_dropdown_variation_attribute_options(
											array(
												'options' => $options,
												'attribute' => $attribute_name,
												'product' => $product,
											)
										); ?>
									</div>
								</div>
							<?php endforeach;?>
						</div>
					<?php } 
				}?>			
			</div>
		<?php endif?>
		

		<div class="product-info">
			<?php 
			echo wc_get_product_category_list(get_the_ID(),'','<div class="product-category-list">','</div>');
			?>
			<?php do_action( 'woocommerce_before_shop_loop_item_title' );?>		
			<?php if($item_title == 'yes'):?>
				<h3 class="title16 product-title font-semibold">
					<a class="color-title" title="<?php echo esc_attr(the_title_attribute(array('echo'=>false)))?>" href="<?php the_permalink()?>"><?php the_title()?></a>
				</h3>
			<?php endif?>
			<?php do_action( 'woocommerce_shop_loop_item_title' );?>
			<?php do_action( 'woocommerce_after_shop_loop_item_title' );?>
			<div class="flex-wrapper justify_content-space-between">
				<div class="title-rating">
					<?php if($item_rate == 'yes') bzotech_get_rating_html(true,false)?>
					<?php if($item_price == 'yes') bzotech_get_price_html()?>
				</div>
				<?php 
				$brand = bzotech_get_loop_term_meta('logo_brand_product','brand_woo');
				if(!empty($brand) && $item_brand == 'yes'){
					echo '<div class="flex-wrapper flex_wrap-wrap flex-align-center align_items-center item-brand-product">';
					foreach ($brand as $key => $value) { 
						if(!empty($value['logo_brand_product']))
						echo '<a class="image-brand" href="'.$value['link'].'">'.wp_get_attachment_image($value['logo_brand_product'],'full').'</a>';
						else echo '<a class="item-brand-product-name title14 main-color text-uppercase font-medium" href="'.$value['link'].'">'.$value['name'].'</a>';
					}
					echo '</div>';
				}
				?>	
			</div>
			<?php if(!empty($product_type) && $product_type == 'flash_sale' && $item_flash_sale == 'yes') bzotech_flashsale_countdown_and_stock_prod(); ?>
		</div>
		<div class="product-extra-link flex-wrapper justify_content-space-between product">
			<?php if($item_button == 'yes'):?>
				<div class="action-buttons">
					<?php 
					$icon_after = $icon = '';
					if(!empty($button_icon['value'])){
						$icon = '<i class="'.$button_icon['value'].'"></i>';
						if($button_icon_pos == 'after-text'){
							$icon_after = $icon;
							$icon = '';
						}
					}else{
						$icon = '<i class="las la-plus-circle"></i>';
						$icon_after = '';
					}
					bzotech_addtocart_link([
						'icon'		=>$icon,
						'icon_after'=>$icon_after,
						'el_class'=>'',
						'style'=>''
					]);
					?>
				</div>
			<?php endif?>
			<div class="extra-links-btn">
				<?php echo bzotech_wishlist_url();?>
				<?php echo bzotech_compare_url(); ?>
				<?php if($item_quickview == 'yes') bzotech_product_quickview('')?>
				
			</div>
		</div>	
				
		<?php do_action( 'woocommerce_after_shop_loop_item' );?>
<?php if($view !== 'slider-masory') echo '</div></div>';?>
	