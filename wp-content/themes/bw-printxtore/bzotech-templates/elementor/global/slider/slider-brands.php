<?php
namespace Elementor;
extract($settings); 
use Bzotech_Template;
?>

<?php echo '<div '.$wdata->get_render_attribute_string( 'elbzotech-wrapper' ).'>';?>

    <?php echo '<div '.$wdata->get_render_attribute_string( 'elbzotech-wrapper-slider' ).'>';?>
        <?php echo '<div '.$wdata->get_render_attribute_string( 'elbzotech-inner' ).'>';?>
            <?php 
            foreach (  $brand_slider as $key => $item ) {

                $wdata->add_render_attribute( 'elbzotech-item-slider-'.$style.'-'.$key, 'class', 'swiper-slide wslider-item elementor-repeater-item-'.$item['_id'] );
                echo '<div '.$wdata->get_render_attribute_string( 'elbzotech-item-slider-'.$style.'-'.$key ).'><div class="  item-slider-global-'.$style.' elementor-animation-'.$image_hover_animation.'">';
                   $link_html = $title_html =$desc_html = $image_html =$count_html ='';
                    if(!empty($item['category'])){
                        $cat = get_term_by('slug', $item['category'], 'brand_woo');
                        if($cat->term_id){
                            $link_html =  'href="'.get_term_link($item['category'],'brand_woo').'"';
                            $title_html = $cat->name;
                            $count_html = '('.$cat->count.')';
                            $thumbnail_id = get_term_meta( $cat->term_id, 'logo_brand_product', true );
                            $image_html    = wp_get_attachment_image( $thumbnail_id ,'full');
                            $desc_html    = category_description($cat->term_id);
                        }
                        
                    }
                    if ( ! empty( $item['link']['url'] ) ) {
                        $wdata->add_link_attributes( 'data_link'.$key, $item['link']);
                        if($item['link']['is_external']) $wdata->add_render_attribute( 'data_link'.$key, 'target', "_blank");
                        if($item['link']['nofollow']) $wdata->add_render_attribute( 'data_link'.$key, 'rel', "nofollow");
                        $link_html = $wdata->get_render_attribute_string( 'data_link'.$key);
                    }
                    if(!empty($item['title'])){
                        $title_html = $item['title'];
                    }
                    if(!empty($item['content'])){
                        $desc_html = $item['content'];
                    }
                    if(!empty($item['image']['url'])){
                        $image_html = wp_get_attachment_image( $item['image']['id'] ,'full');
                    }
                    if(!empty($item['image_hover']['url'])){
                        $image_html .= '<span class="image-hover ">'.wp_get_attachment_image( $item['image_hover']['id'] ,'full').'</span>';
                    }
                   
                    
                    if(!empty($item['template'])) 
                        echo Bzotech_Template::get_vc_pagecontent($item['template']);
                    else {
                       echo '<a class="cate-item flex-wrapper flex-align-center align_items-center" '.$link_html.'>'.$image_html.'</a>';
                    }  
                            
                echo '</div></div>';

                $wdata->remove_render_attribute( 'data_link', 'target', "_blank" );
                $wdata->remove_render_attribute( 'data_link', 'rel', "nofollow");
                $wdata->remove_render_attribute( 'data_linkk', 'href', $item['link']['url']);
                $wdata->remove_render_attribute( 'elbzotech-item', 'class', 'elementor-repeater-item-'.$item['_id'] );
            }
            ?>
        </div>
    </div>
    <?php if ( $slider_navigation !== '' ):?>
        <div class="bzotech-swiper-navi">
            <div class="swiper-button-nav swiper-button-next"><?php Icons_Manager::render_icon( $slider_icon_next, [ 'aria-hidden' => 'true' ] );?></div>
            <div class="swiper-button-nav swiper-button-prev"><?php Icons_Manager::render_icon( $slider_icon_prev, [ 'aria-hidden' => 'true' ] );?></div>
        </div>
    <?php endif?>
    <?php if ( $slider_pagination !== '' ):?>
        <div class="swiper-pagination"></div>
    <?php endif?>
    <?php if ( $slider_scrollbar !== '' ):?>
        <div class="swiper-scrollbar"></div>
    <?php endif?>
</div>