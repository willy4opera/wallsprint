<?php
namespace Elementor;
extract($settings);
$wdata->add_render_attribute( 'banner-wrap', 'class', 'elbzotech-banner-info-global-wrap elbzotech-banner-info-global-'.$banner_style);
if($link['is_external']) $wdata->add_render_attribute( 'banner-image-link', 'target', "_blank");
if($link['nofollow']) $wdata->add_render_attribute( 'banner-image-link', 'rel', "nofollow");
if($link['url']) $wdata->add_render_attribute( 'banner-image-link', 'href', $link['url']);
?>
<div <?php echo apply_filters('bzotech_output_content', $wdata->get_render_attribute_string('banner-wrap')); ?> >
	<?php
	if(!empty($image['url'])) { ?>
		<div class="elbzotech-banner-info-global-thumb" >
			<a <?php echo apply_filters('bzotech_output_content', $wdata->get_render_attribute_string('banner-image-link')); ?> >
				<?php echo Group_Control_Image_Size::get_attachment_image_html( $settings); ?>
			</a>
		</div>
		<div class="info-banner">
			<?php
			if(!empty($list_point) and is_array($list_point)) { 
		        echo '<div class = "list-point">';?>
		        <?php
		        foreach ($list_point as $key => $item ) {
		        	
					$wdata->add_render_attribute( 'link_item'.$key, 'class', 'point');
		        	$point_info = $item['info'];
		        	if(!empty($item['id_product'])){
		        		$wdata->add_render_attribute( 'link_item'.$key, 'href', get_permalink($item['id_product']) );
		        		$product_point = wc_get_product( $item['id_product'] );
		        		if(!empty($product_point))
		        		$point_info = '<h3 class="title_product title20 font-semibold title-color"><a href="'.get_permalink($item['id_product']).'">'.get_the_title($item['id_product']).'</a></h3>'.$product_point->get_price_html();
		        	}else{
		        		if ( ! empty( $item['link']['url'] ) ) {
							$wdata->add_link_attributes( 'link_item'.$key, $item['link'] );
						}
		        	}
		        	echo '<div class="elementor-repeater-item-'.$item['_id'].'"><div class="list-point__item active-'.$item['active'].'">
			        	<div class="list-point__itempoint">
			        		<a '.$wdata->get_render_attribute_string( 'link_item'.$key ).'><span><i class="las la-plus"></i></span></a>
				        	<div class="list-point__info text-right">
				        		'.$point_info.'
				        	</div>
			        	</div>
		        	</div></div>';
		        }
		        echo '</div>';
		    }
			?>
		</div>
	<?php } ?>
	
</div>