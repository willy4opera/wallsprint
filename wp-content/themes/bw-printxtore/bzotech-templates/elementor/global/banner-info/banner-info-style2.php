<?php
namespace Elementor;
extract($settings);
if(!empty($image_hover_animation)) $image_effect_banner ='';
$wdata->add_render_attribute( 'banner-wrap', 'class', ' elbzotech-banner-info-global-wrap elbzotech-banner-info-global-'.$banner_style.' '.$image_effect_banner.' '.$box_overflow);
$class_background_overlay ='';
if(!empty($background_overlay)) $class_background_overlay = 'background-overlay';
$wdata->add_render_attribute( 'banner-image-link', 'class', 'popup-video adv-thumb-link '.$class_background_overlay.' elementor-animation-'.$image_hover_animation);
if($link_video['is_external']) $wdata->add_render_attribute( 'banner-image-link', 'target', "_blank");
if($link_video['nofollow']) $wdata->add_render_attribute( 'banner-image-link', 'rel', "nofollow");
if($link_video['url']) $wdata->add_render_attribute( 'banner-image-link', 'href', $link_video['url']);
?>
<div <?php echo apply_filters('bzotech_output_content', $wdata->get_render_attribute_string('banner-wrap')); ?> >
	<?php
	if(!empty($image['url'])) { ?>
		<div class="elbzotech-banner-info-global-thumb <?php echo esc_attr($image_effect_banner.' '.$box_overflow); ?>" >
			<a <?php echo apply_filters('bzotech_output_content', $wdata->get_render_attribute_string('banner-image-link')); ?> >
				<?php if($image_effect_banner == 'hover-icon' && !empty($effect_hover_icon)) {

				 echo '<div class="effect-hover-icon title24 color-white">';
				 Icons_Manager::render_icon( $settings['effect_hover_icon'], [ 'aria-hidden' => 'true' ] );
				 echo '</div>'; 
				} ?>
				<?php if(!empty($background_overlay)) echo '<span class="background-overlay-color"></span>'?>
				<?php echo Group_Control_Image_Size::get_attachment_image_html( $settings); ?>
				<?php 
				if($image_effect_banner == 'zoom-out' || $image_effect_banner == 'zoom-out overlay-image'){
					if(!empty($image2['url'])) {
						echo Group_Control_Image_Size::get_attachment_image_html( $settings,'image','image2');
					}else echo Group_Control_Image_Size::get_attachment_image_html( $settings);
				} 
				if(!empty($icon_button_video['value'])){
					if($icon_button_video['library']=='svg'){
						echo '<span class="icon-button-video"><img class="item-icon-e" alt="'.esc_attr__('svg','bw-printxtore').'" src="'.esc_url($icon_button_video['value']['url']).'"></span>';
					}else{
						echo '<span class="icon-button-video"><span class="icon-button-video__icon"><span class="icon-button-video__icon2"><i class="item-icon-e '.$icon_button_video['value'].'"></i></span></span></span>';
					}
				} 
				?>
				
			</a>
		</div>
	<?php } ?>
</div>