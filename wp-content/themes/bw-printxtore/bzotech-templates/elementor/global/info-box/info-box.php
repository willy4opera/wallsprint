<?php
namespace Elementor;
extract($settings);

$wdata->add_render_attribute( 'wrapper', 'class', 'flex-wrapper container-flex-e bzoteche-info-box-global-'.$settings['style'].' item-info-box-global');

?>
<div <?php echo apply_filters('bzotech_output_content', $wdata->get_render_attribute_string('wrapper'));?>>
	<?php
	$tag_icon = 'div';
	if ( !empty( $link_info['url']) ) { 
		$wdata->add_link_attributes( 'link_icon', $link_info);
		$tag_icon = 'a';
	}
	if(!empty($icon_image['url'])){
		$class_icon_image_hover ='';
		if(!empty($icon_image_hover['url'])) $class_icon_image_hover = 'icon-image-hover__active';
		
		
		echo '<div class="info-box-icon item-image-icon-e '.$class_icon_image_hover.' elementor-animation-'.$image_icon_animation_hover_css.'">';
			echo  '<'.$tag_icon.' '.$wdata->get_render_attribute_string( 'link_icon' ).'>';
				echo Group_Control_Image_Size::get_attachment_image_html( $settings,'image_icon','icon_image');
				if(!empty($icon_image_hover['url']))
				echo '<span class="image-hover">'.Group_Control_Image_Size::get_attachment_image_html( $settings,'image_icon','icon_image_hover').'</span>';
			echo '</'.$tag_icon.'>';

			/*List icon style2*/
			if(!empty($list_icon) && is_array($list_icon) && ($style == 'style2' || $style == 'style3' )) { 
				echo '<div class="list-icon">';
				 foreach ($list_icon as $key => $item ) {
					if ( ! empty( $item['link']['url'] ) ) {
						$wdata->add_link_attributes( 'list_link_icon'.$key, $item['link'] );
					}
					$wdata->add_render_attribute( 'list_link_icon'.$key, 'class', 'list-icon__item');
					if(!empty($item['icon']['value']))
					echo '<a '.$wdata->get_render_attribute_string( 'list_link_icon'.$key ).'><i class="'.$item['icon']['value'].'"></i></a>';
					
		        }
		        echo '</div>';
			}

		echo '</div>';
	}else if(!empty( $icon['value'])){				
		if( $icon['library'] == 'svg')
			echo '<div class="info-box-icon item-image-icon-e elementor-animation-'.$image_icon_animation_hover_css.'"><'.$tag_icon.' '.$wdata->get_render_attribute_string( 'link_icon' ).'><img class="item-icon-e" alt="'.esc_attr__('svg','bw-printxtore').'" src="'.esc_url($icon['value']['url']).'"></'.$tag_icon.'></div>';
		else
		echo '<div class="info-box-icon elementor-animation-'.$image_icon_animation_hover_css.'"><'.$tag_icon.' '.$wdata->get_render_attribute_string( 'link_icon' ).'><i class="item-icon-e title50  '.$icon['value'].' '.bzotech_implode($css_by_theme_text).'"></i></'.$tag_icon.'></div>';
	} 
	if(!empty($list_text_info) and is_array($list_text_info)) { 
        echo '<div class = "list-text-info flex-wrapper info-container-flex-e">';?>
        <?php
        foreach ($list_text_info as $key => $item ) {
        	if(!empty($item['image']['id'])) {
				echo wp_get_attachment_image($item['image']['id'],'full',false,['class'=>$item['add_class_css']]);
			}
			$href_a_tag = '';
			if(!empty($item['link_text_tag']) && $item['text_tag'] =='a') $href_a_tag = 'href = "'.$item['link_text_tag'].'"';
			echo '<'.$item['text_tag'].' '.$href_a_tag .' class="list-text-info__item '.$item['add_class_css'].' elementor-repeater-item-'.$item['_id'].'">'.$item['text'].'</'.$item['text_tag'].'>';
        }
        echo '</div>';
    }
	?>
</div>