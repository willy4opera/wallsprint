<?php
namespace Elementor;
extract($settings);
if(!empty($image_hover_animation)) $image_effect_banner ='';
$wdata->add_render_attribute( 'banner-wrap', 'class', 'elbzotech-banner-info-global-wrap elbzotech-banner-info-global-'.$banner_style.' '.$image_effect_banner.' '.$box_overflow.' wrap-flex-e-'.$style_info);
$class_background_overlay ='';
if(!empty($background_overlay)) $class_background_overlay = 'background-overlay';
$wdata->add_render_attribute( 'banner-image-link', 'class', 'adv-thumb-link '.$class_background_overlay.' elementor-animation-'.$image_hover_animation);
if($link['is_external']) $wdata->add_render_attribute( 'banner-image-link', 'target', "_blank");
if($link['nofollow']) $wdata->add_render_attribute( 'banner-image-link', 'rel', "nofollow");
if($link['url']) $wdata->add_render_attribute( 'banner-image-link', 'href', $link['url']);
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
				} ?>
			</a>
		</div>
	<?php }
	if($list_text || $list_button){
		$wdata->add_render_attribute( 'info_attr', 'class', 'elbzotech-banner-info '.$style_info.' '.$align.' '.bzotech_implode($info_wrap_css_by_theme) );
		
		
		echo '<div '.$wdata->get_render_attribute_string( 'info_attr' ).'>';
		
			echo '<div class="info-banner2 info-container-flex-e '.bzotech_implode($info_css_by_theme).'">';
			if($link['url']) echo '<a class = "link-bg-banner" href ="'.$link['url'].'" ></a>';
		
				
				if ( $list_text ) {
					foreach (  $list_text as $key => $item ) {
						if(!empty($item['image']['id'])) {
							echo wp_get_attachment_image($item['image']['id'],'full',false,['class'=>$item['add_class_css']]);
						}
						$href_a_tag = '';
						if(!empty($item['link_text_tag']) && $item['text_tag'] =='a') $href_a_tag = 'href = "'.$item['link_text_tag'].'"';
						echo '<'.$item['text_tag'].' '.$href_a_tag .' class="elbzotech-text-item '.bzotech_implode($item['css_by_theme_text']).' '.$item['add_class_css'].' elementor-repeater-item-'.$item['_id'].'">'.$item['text'].'</'.$item['text_tag'].'>';
					}
				}
				if ( $list_button ) {
					echo '<div class="elbzotech-btwrap btwrapinfo-container-flex-e">';
					foreach (  $list_button as $item ) {

						$target = $item['link']['is_external'] ? ' target="_blank"' : '';
						$nofollow = $item['link']['nofollow'] ? ' rel="nofollow"' : '';
						$class_item_custom = '';
						if($item['style'] == 'custom'){
							$class_item_custom='item-custom-botton-e';
						}
						$wdata->add_render_attribute( 'button-inner', 'class', 'button-inner elbzotech-bt-global-'.$item['style'].' '.$item['add_class_css'].' '.$class_item_custom.' '.bzotech_implode($item['css_by_theme_button']).' elementor-repeater-item-'.$item['_id'] );
						if($item['link']['is_external']) $wdata->add_render_attribute( 'button-inner', 'target', "_blank");
						if($item['link']['nofollow']) $wdata->add_render_attribute( 'button-inner', 'rel', "nofollow");
						if($item['link']['url']) $wdata->add_render_attribute( 'button-inner', 'href', $item['link']['url']);

						$icon_btn = '';
						if(!empty($item['button_icon']['value'])){
						    if($item['button_icon']['library'] == 'svg')
						        $icon_btn .=  '<img class="icon-button-el" alt="'.esc_attr__('svg','bw-printxtore').'" src ="'.esc_url($item['button_icon']['value']['url']).'">';
						    else
						        $icon_btn =  '<i class="icon-button-el '.esc_attr($item['button_icon']['value']).'"></i>';
						} 

						echo  '<a '.$wdata->get_render_attribute_string('button-inner').'>';
							
								if($item['button_icon_pos'] == 'before-text' && !empty($item['button_icon']['value'])) echo apply_filters('bzotech_output_html',$icon_btn);
								echo '<span class="text-button">'.apply_filters('bzotech_output_content',$item['text']).'</span>';
								if($item['button_icon_pos'] == 'after-text' && !empty($item['button_icon']['value'])) echo apply_filters('bzotech_output_html',$icon_btn);
						echo  '</a>';
						$wdata->remove_render_attribute( 'button-inner', 'target', '_blank');
						$wdata->remove_render_attribute( 'button-inner', 'rel', 'nofollow');
						$wdata->remove_render_attribute( 'button-inner', 'href', $item['link']['url']);
						$wdata->remove_render_attribute( 'button-inner', 'class', 'button-inner elbzotech-bt-'.$item['style'].' '.$item['add_class_css'].' '.$class_item_custom.' elementor-repeater-item-'.$item['_id'] );
					}
					echo '</div>';
				}
			echo '</div>';
		echo '</div>';
	}?>
</div>