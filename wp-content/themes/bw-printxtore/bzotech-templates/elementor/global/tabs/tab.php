<?php
namespace Elementor;
use Bzotech_Template;
extract($settings);
$wdata->add_render_attribute( 'wrap', 'class', 'flex-wrapper flex-box-tab-e tab-wrap elbzotech-tabs-global-'.$style);
$id_int = substr( $wdata->get_id_int(), 0, 3 );
$header_tab_item_html = $tab_content_html = '';
foreach ( $tabs as $key => $tab ) : 
	extract($tab); 
	if($key == 0) $active = 'active'; else $active = '';
	$header_tab_item_html .= '<a class="flex-wrapper flex-box-header-tab-item-e style-header-tab-item-e tab-item-wrap '.esc_attr($active).'" href="#'.esc_attr($_id).''.$id_int.'" data-target="#'.esc_attr($_id).''.$id_int.'" data-toggle="tab" aria-expanded="false">';

	if(!empty($icon_image['url'])){
		$class_icon_image_hover ='';
		if(!empty($icon_image_hover['url'])) $class_icon_image_hover = 'icon-image-hover__active';
		$header_tab_item_html .= '<span class="'.$class_icon_image_hover.'">';
		$header_tab_item_html .= Group_Control_Image_Size::get_attachment_image_html( $tabs[$key],'style_header_tab_icon_image_item','icon_image');
		
		if(!empty($icon_image_hover['url'])){
			$header_tab_item_html .= '<span class="img-hover">'.Group_Control_Image_Size::get_attachment_image_html( $tabs[$key],'style_header_tab_icon_image_item','icon_image_hover').'</span>';
		}
		$header_tab_item_html .= '</span>';
	}else if(!empty( $icon['value'])){
		$header_tab_item_html .= '<span class="">';		
		if( $icon['library'] == 'svg')
			$header_tab_item_html .= '<img alt="'.esc_attr__('svg','bw-printxtore').'" src="'.esc_url($icon['value']['url']).'">';
		else
		$header_tab_item_html .= '<i class="style-header-tab-icon-item-e  '.$icon['value'].'"></i>';
		$header_tab_item_html .= '</span>';
	} 

	if(!empty($tab_title))
		$header_tab_item_html .= '<span class="">'.$tab_title.'</span>';
	
	$header_tab_item_html .= '</a>';

	$tab_content_html .= '<div id="'.$_id.''.$id_int.'" class=" tab-pane '.$active.'">';
		if(!empty($template)) 
			$tab_content_html .= Bzotech_Template::get_vc_pagecontent($template);
		else   
			$tab_content_html .= bzotech_parse_text_editor( $tab_content); 
    $tab_content_html .= '</div>';

endforeach; ?>
<div <?php echo apply_filters('bzotech_output_content',$wdata->get_render_attribute_string( 'wrap')) ?>>
	<div class="flex-wrapper flex-box-header-tab-e">
		<?php if(!empty($title_header)){?>
			<div class="flex-wrapper flex-box-header-title-e">
				<?php echo '<h3 class="style-header-title-e">'.$title_header.'</h3>'; ?>
			</div>
		<?php }?>
		
		<div class="flex-wrapper flex-box-header-tab-list-item-e nav nav-tabs" role="tablist">
			<?php echo apply_filters('bzotech_output_content',$header_tab_item_html); ?>
			
		</div>
	</div>
	<?php echo '<div class="tab-content flex-wrapper flex-box-content-tab-e">'.apply_filters('bzotech_output_content',$tab_content_html).'</div>'; ?>
</div>
