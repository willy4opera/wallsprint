<?php
namespace Elementor;
extract($settings);
$icon_btn_html = '';

if(!empty($icon_image['url'])){
    $icon_btn_html .='<span class="icon-image-btn icon-button-el">';
    $icon_btn_html .= Group_Control_Image_Size::get_attachment_image_html( $settings,'image','icon_image');
    if(!empty($icon_image_hover['url'])){
         $icon_btn_html .= '<span class="icon_image_hover">'.Group_Control_Image_Size::get_attachment_image_html( $settings,'image','icon_image_hover').'</span>';
    }
    $icon_btn_html .='</span>';
}else if(!empty($button_icon['value'])){
    if($button_icon['library'] == 'svg')
        $icon_btn_html .=  '<img class="icon-button-el" alt="'.esc_attr__('svg','bw-printxtore').'" src ="'.esc_url($button_icon['value']['url']).'">';
    else
        $icon_btn_html =  '<i class="icon-button-el '.esc_attr($button_icon['value']).'"></i>';
} ?>
<div <?php echo apply_filters('bzotech_output_content', $wdata->get_render_attribute_string('button-wrap')); ?> >
    <a <?php echo apply_filters('bzotech_output_content', $wdata->get_render_attribute_string('button-inner')); ?> >
    	
        <?php if($button_icon_pos == 'before-text' && !empty($icon_btn_html)) echo apply_filters('bzotech_output_content',$icon_btn_html);?>
        
        	<?php if(!empty($button_text)) echo '<span class="text-button">'.$button_text.'</span>'; ?>
    	
        <?php if($button_icon_pos == 'after-text' && !empty($icon_btn_html)) echo apply_filters('bzotech_output_content',$icon_btn_html);?>
                  
    </a>
</div>