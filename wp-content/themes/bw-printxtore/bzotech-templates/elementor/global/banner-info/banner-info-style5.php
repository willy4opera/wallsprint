<?php
namespace Elementor;
extract($settings);
$wdata->add_render_attribute( 'banner-wrap', 'class', 'elbzotech-banner-info-global-wrap elbzotech-banner-info-global-'.$banner_style);
?>
<div <?php echo apply_filters('bzotech_output_content', $wdata->get_render_attribute_string('banner-wrap')); ?> >
	<?php
	if(!empty($image['url'])) { ?>
		<div class="elbzotech-banner-info-global-thumb" >
			
				<?php echo Group_Control_Image_Size::get_attachment_image_html( $settings); ?>
		</div>
		<div class="info-banner">
			<?php
			if(!empty($list_count_up) and is_array($list_count_up)) { 
		        echo '<div class = "list-point">';?>
		        <?php
		        foreach ($list_count_up as $key => $item ) {
		        	
		        	echo '<div class="text-center count-up-item elementor-repeater-item-'.$item['_id'].'">
		        	<div class="count-up title60 font-semibold"><span class="js-counter">'.$item['number'].'</span><span>'.$item['unit'].'</span></div><div class="title title20 font-regular">'.$item['title'].'</div></div>';
		        }
		        echo '</div>';
		    }
			?>
		</div>
	<?php } ?>
	
</div>