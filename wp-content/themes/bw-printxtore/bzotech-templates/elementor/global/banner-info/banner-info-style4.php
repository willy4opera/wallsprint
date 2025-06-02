<?php
namespace Elementor;
extract($settings);
if(!empty($image_hover_animation)) $image_effect_banner ='';
$wdata->add_render_attribute( 'banner-wrap', 'class', ' elbzotech-banner-info-global-wrap elbzotech-banner-info-global-'.$banner_style.' '.$image_effect_banner.' '.$box_overflow);
$class_background_overlay ='';
if(!empty($background_overlay)) $class_background_overlay = 'background-overlay';
$wdata->add_render_attribute( 'banner-image-link', 'class', 'popup-video adv-thumb-link '.$class_background_overlay.' elementor-animation-'.$image_hover_animation);
?>
<div <?php echo apply_filters('bzotech_output_content', $wdata->get_render_attribute_string('banner-wrap')); ?> >
	<?php
	if(!empty($image['url'])) { ?>
		<div class="elbzotech-banner-info-global-thumb <?php echo esc_attr($image_effect_banner.' '.$box_overflow); ?>" >
			<?php
			if(!empty($category)){
                $cat = get_term_by('slug', $category, 'product_cat');
                if($cat->term_id){
                    $link_html =  'href="'.get_term_link($category,'product_cat').'"';
                    $title_html = $cat->name;
                    $count_html = '('.$cat->count.')';
                    $thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
                    $image_html    = wp_get_attachment_image( $thumbnail_id ,'full');
                    $desc_html    = category_description($cat->term_id);
                }
                
            }
            if ( ! empty( $link['url'] ) ) {
                $wdata->add_link_attributes( 'data_link', $link);
                $link_html = $wdata->get_render_attribute_string( 'data_link'.$key);
            }
            if(!empty($title_category)){
                $title_html = $title_category;
            }
            if(!empty($desc_category)){
                $desc_html = $desc_category;
            }
            if(!empty($image['url'])){
                $image_html = wp_get_attachment_image( $image['id'] ,'full');
            }

             echo '<a class="adv-thumb-link-cate adv-thumb-link" '.$link_html.'>'.$image_html.'</a>
             		<h2 class="info-title title_banner font-semibold title34">'.$title_html.'</h2>
                   <div class="info text-center"><h2 class="info-title  font-semibold title34"><a class="color-white" '.$link_html.'>'.$title_html.'</a></h2><div class="desc title20 color-white">'.$desc_html.'</div><a '.$link_html.' class = "elbzotech-bt-global-style2">'.esc_html__('VIEW NOW','bw-printxtore').'</a></div>';
			?>
		</div>
	<?php } ?>
</div>