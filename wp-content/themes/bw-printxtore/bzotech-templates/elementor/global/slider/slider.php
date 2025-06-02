<?php
namespace Elementor;
extract($settings); 
use Bzotech_Template;
?>

<?php echo '<div '.$wdata->get_render_attribute_string( 'elbzotech-wrapper' ).'>';?>

    <?php echo '<div '.$wdata->get_render_attribute_string( 'elbzotech-wrapper-slider' ).'>';?>
        <?php echo '<div '.$wdata->get_render_attribute_string( 'elbzotech-inner' ).'>';?>
            <?php 
            foreach (  $list_sliders as $key => $item ) {

                $wdata->add_render_attribute( 'elbzotech-item-slider-'.$style.'-'.$key, 'class', 'swiper-slide wslider-item elementor-repeater-item-'.$item['_id'] );
                echo '<div '.$wdata->get_render_attribute_string( 'elbzotech-item-slider-'.$style.'-'.$key ).'><div class="item-slider-global-'.$style.'">';
                    if(!empty($item['image']['url'])) {
                        if($item['link']['is_external']) $wdata->add_render_attribute( 'img-link', 'target', "_blank");
                        if($item['link']['nofollow']) $wdata->add_render_attribute( 'img-link', 'rel', "nofollow");
                        
                        if($item['image_action']){
                            $wdata->add_render_attribute( 'img-link', 'href', $item['image']['url']);
                        } else{
                            if($item['link']['url']){
                                $html_tab_a = 'a';
                                $wdata->add_render_attribute( 'img-link', 'href', $item['link']['url']);
                            }else{
                                $html_tab_a = 'div';    
                            }
                        }
                        

                        echo '<div class="image-wrap swiper-thumb elementor-animation-'.$image_hover_animation.'"><'.$html_tab_a.' '.$wdata->get_render_attribute_string('img-link').' class="adv-thumb-link">';
                        echo Group_Control_Image_Size::get_attachment_image_html( $list_sliders[$key], 'thumbnail', 'image' );
                        echo '</'.$html_tab_a.'></div>';
                    }
                    
                    if(!empty($item['template'])) 
                        echo Bzotech_Template::get_vc_pagecontent($item['template']);
                    else if(!empty($item['content'])){
                        echo  '<div class="content-slider-custom box-content-custom">'.bzotech_parse_text_editor( $item['content']).'</div>';
                    }  
                            
                echo '</div></div>';

                $wdata->remove_render_attribute( 'img-link', 'target', "_blank" );
                $wdata->remove_render_attribute( 'img-link', 'rel', "nofollow");
                $wdata->remove_render_attribute( 'img-link', 'href', $item['link']['url']);
                $wdata->remove_render_attribute( 'img-link', 'href', $item['image']['url']);
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