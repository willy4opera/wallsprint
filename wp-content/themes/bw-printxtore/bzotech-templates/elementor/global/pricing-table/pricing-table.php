<?php
namespace Elementor;
extract($settings);
$wdata->add_render_attribute( 'wrapper', 'class', 'justify_content-center element-pricing-table-global-'.$settings['style'].' pricing-active-'.$active_style_picing);
?>
<div <?php echo ''.$wdata->get_render_attribute_string('wrapper');?>>
	<?php if(!empty($label)) echo '<span class="label-pricing">'.$label.'</span>'; ?>
	<?php if(!empty($title)) echo '<h3 class=" title-pricing-table">'.$title.'</h3>'; ?>
	<?php if(!empty($desc)) echo '<div class="desc-pricing-table">'.bzotech_parse_text_editor($desc).'</div>'; ?>
	<?php if(!empty($price)) echo '<h3 class="price-pricing-table">'.$price.'</h3>'; ?>
	<?php

	if(!empty($list_pricing_table) and is_array($list_pricing_table)){ ?>
		<div class="list-pricing-table">
			<?php foreach ($list_pricing_table as $key => $value) {
				if ( ! empty( $value['link']['url'] ) ) {
					$wdata->add_link_attributes( 'data_link'.$key, $value['link'] );
				}
				$wdata->add_render_attribute( 'data_link'.$key, 'class', 'item-link active-style__'.$value['active_style'] );

				$image_hover = $html_icon = '';
				if(!empty($value['icon_image_hover']['url'])) $image_hover='yes';
				
				if(!empty($value['icon_image']['url'])){
					$html_icon .= '<span class="icon-image-link image_hover-'.$image_hover.'">'.Group_Control_Image_Size::get_attachment_image_html( $value,'size_icon_image','icon_image');
					if(!empty($value['icon_image_hover']['url'])){
						$html_icon .= '<span class="icon_image_hover">'.Group_Control_Image_Size::get_attachment_image_html( $value,'size_icon_image','icon_image_hover').'</span>';
					}
					
					$html_icon .= '</span>';
				}else if(!empty($value['icon']['value'])){
					if($value['icon']['library'] == 'svg')
						$html_icon .= '<img alt="'.esc_attr__('svg','bw-printxtore').'" src ="'.esc_url($value['icon']['value']['url']).'">';
					else
						$html_icon .= '<i class="'.esc_attr($value['icon']['value']).'"></i>';
				} 
				?>

				<?php echo '<a '.$wdata->get_render_attribute_string( 'data_link'.$key ).'>'.$html_icon.' <span class="list-pricing-table__text">'. $value['title'].'</span></a>'; ?>
				
				<?php
			} ?>
		</div>
		<?php
	} 
	?>
	<?php if(!empty($button_text)) {
		$wdata->add_render_attribute( 'button-inner', 'href', $button_link['url']);
		$wdata->add_render_attribute( 'button-inner', 'class', 'button-pricing');
		echo '<a '.$wdata->get_render_attribute_string('button-inner').'>'.$button_text.'<i class="las la-arrow-right"></i></a>';
	} ?>
</div>

