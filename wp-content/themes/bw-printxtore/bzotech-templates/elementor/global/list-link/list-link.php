<?php
namespace Elementor;
extract($settings);
$wdata->add_render_attribute( 'wrapper', 'class', 'elbzotech-list-link-global-'.$settings['style'].' elbzotech-list-link-global ');
$html_separator = '';
if(!empty($settings['separator_inline']) and $settings['style'] == 'inline'){
	$html_separator = '<span class="space-separator separator-'.$settings['separator_inline'].'"></span>';
}
$size_icon_image = 'full';
if(!empty($heading)) echo'<div class="text-css-e">'.$heading.'</div>';
if(!empty($settings['list_link']) and is_array($settings['list_link'])){ ?>
	<div <?php echo ''.$wdata->get_render_attribute_string('wrapper');?>>
		<?php
		
		foreach ($settings['list_link'] as $key => $value_icon) {
			if(!empty($value_icon['link_id'])){
				$link_by_id = get_permalink($value_icon['link_id']);
				if( !empty( $value_icon['link']['url']))
					$link_by_id = $link_by_id.$value_icon['link']['url'];
				$wdata->add_render_attribute( 'data_link'.$key, 'href', $link_by_id );
			}else{
				if ( !empty( $value_icon['link']['url'] ) ) {
					$wdata->add_link_attributes( 'data_link'.$key, $value_icon['link'] );
				}
			}
			$icon_check='icon-check-no';
			if(!empty($value_icon['icon_image']['url'])||!empty($value_icon['icon']['value'])) $icon_check='icon-check-yes';
			$wdata->add_render_attribute( 'data_link'.$key, 'class', 'item-link icon-position-'.$icon_position.' '.$icon_check.' elementor-repeater-item-'.$value_icon['_id'].' '.bzotech_implode($value_icon['css_by_theme_text']).' '.bzotech_implode($css_by_theme_text).' '.bzotech_implode($css_by_theme_text_hover) );
			

			?>
			<a <?php echo apply_filters('bzotech_output_content', $wdata->get_render_attribute_string( 'data_link'.$key )); ?>>

				<?php 
				if(!empty($value_icon['title'])&&$icon_position=='right') echo '<span class="title">'.$value_icon['title'].'</span>';
				$image_hover = '';
				if(!empty($value_icon['icon_image_hover']['url'])) $image_hover='yes';
				$value_icon['size_icon_image'] = $size_icon_image;
				if(!empty($value_icon['icon_image']['url'])){
					echo '<span class="icon-image-link image_hover-'.$image_hover.'">'.Group_Control_Image_Size::get_attachment_image_html( $value_icon,'size_icon_image','icon_image');
					if(!empty($value_icon['icon_image_hover']['url'])){
						echo '<span class="icon_image_hover">'.Group_Control_Image_Size::get_attachment_image_html( $value_icon,'size_icon_image','icon_image_hover').'</span>';
					}
					
					echo '</span>';
				}else if(!empty($value_icon['icon']['value'])){
					if($value_icon['icon']['library'] == 'svg')
						echo '<span class="icon-image-link"><img alt="'.esc_attr__('svg','bw-printxtore').'" src ="'.esc_url($value_icon['icon']['value']['url']).'"></span>';
					else
						echo '<i class="'.esc_attr($value_icon['icon']['value']).'"></i>';
				} 

				if(!empty($value_icon['title'])&&$icon_position=='left') echo '<span class="title">'.$value_icon['title'].'</span>';?>
			</a>
			<?php echo apply_filters('bzotech_output_content',$html_separator); ?>
			<?php
			$wdata->remove_render_attribute( 'data_link'.$key, 'class', 'item-link icon-position-'.$icon_position.' '.$icon_check.' elementor-repeater-item-'.$value_icon['_id']);
			if(!empty($value_icon['link_id'])){
			 	$wdata->remove_render_attribute( 'data_link'.$key, 'href', $link_by_id);
			}
			 
		} ?>
	</div>
	<?php
}