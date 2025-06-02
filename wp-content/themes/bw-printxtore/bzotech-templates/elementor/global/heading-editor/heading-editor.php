<?php
extract($settings);

if($header_style == ''){
	/*Style Text Editor*/
	echo '<div class="elbzotech-text-editor-global text-css-e '.bzotech_implode($css_by_theme_title).'">'.bzotech_parse_text_editor($editor).'</div>';

}else
if($header_style == 'mouse-cursor'){
	/*Style Text Editor*/
	echo '<div class="mouse-cursor text-css-e '.bzotech_implode($css_by_theme_title).'">'.$title.'</div>';

}else{
	$title_icon = $title_line_html=$title_shadow=$class_title_shadow='';
	if(!empty($title_id)) $title=$title_id;
	if(!empty($title_shadow_text)){
		$class_title_shadow = 'class-title-shadow';
	} $title_shadow = '<span class="type-title_shadow">'.$title_shadow_text.'</span>';
	$wdata->add_render_attribute( 'heading', 'class', bzotech_implode($css_by_theme_title).' '.$class_title_shadow.' container-flex-e elbzotech-heading-global font-title text-css-e font-medium  title48 color-title elbzotech-heading-global-'.$settings['header_style'] );
	if($title_line == 'yes') $title_line_html = '<span class="'.bzotech_implode($line_css_by_theme_title).'  elbzotech-heading-global__line line-e bg-color"></span>';
	

	if(!empty( $icon_hedding['value'])){				
		if( $icon_hedding['library'] == 'svg')
			$title_icon = '<img class="elbzotech-heading-global__icon" alt="'.esc_attr__('svg','bw-printxtore').'" src="'.esc_url($icon_hedding['value']['url']).'">';
		else $title_icon = '<i class="elbzotech-heading-global__icon '.$icon_hedding['value'].'"></i>';
	} 

	if ( !empty( $settings['link']['url'] ) ) {
			$wdata->add_link_attributes( 'url', $settings['link'] );
			
			$title = sprintf( '<a %1$s>%2$s</a>', $wdata->get_render_attribute_string( 'url' ), $title );
		}
	if($header_style == 'style2'){
		$image_left_html = $image_right_html ='';
		if(!empty($image_left['url'])) 
			$image_left_html = '<img class="image_left" alt="image_left" src="'.$image_left['url'].'">';
		if(!empty($image_right['url'])) 
			$image_right_html ='<img class="image_right" alt="image_right" src="'.$image_right['url'].'">';

		$title = sprintf( '<span>%1$s<span>%2$s</span>%3$s</span>', $image_left_html, $title ,$image_right_html);
	}
	echo sprintf( '<%1$s %2$s>%3$s%4$s%5$s%6$s</%1$s>', $settings['header_size'], $wdata->get_render_attribute_string( 'heading' ), $title,$title_line_html,$title_icon,$title_shadow);	
}