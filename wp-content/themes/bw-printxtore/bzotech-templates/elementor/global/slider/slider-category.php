<?php
namespace Elementor;
extract($settings); 
use Bzotech_Template;
?>

<?php echo '<div '.$wdata->get_render_attribute_string( 'elbzotech-wrapper' ).'>';?>

    <?php echo '<div '.$wdata->get_render_attribute_string( 'elbzotech-wrapper-slider' ).'>';?>
        <?php echo '<div '.$wdata->get_render_attribute_string( 'elbzotech-inner' ).'>';?>
            <?php 
            foreach (  $list_categories2 as $key => $item ) {

                $wdata->add_render_attribute( 'elbzotech-item-slider-'.$style.'-'.$key, 'class', 'swiper-slide wslider-item elementor-repeater-item-'.$item['_id'] );
                echo '<div '.$wdata->get_render_attribute_string( 'elbzotech-item-slider-'.$style.'-'.$key ).'><div class=" zoom-image item-slider-global-'.$style.'">';
                   $link_html = $title_html =$desc_html = $image_html =$count_html ='';
                    if(!empty($item['category'])){
                        $cat = get_term_by('slug', $item['category'], 'product_cat');
                        if(!empty($cat->term_id)){
                            $link_html =  'href="'.get_term_link($item['category'],'product_cat').'"';
                            $title_html = $cat->name;
                            $count_html = '('.$cat->count.')';
                            $thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
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
                   
                    
                    if(!empty($item['template'])) 
                        echo Bzotech_Template::get_vc_pagecontent($item['template']);
                    else {
                       echo '<div class="cate-img overflow-hidden text-center"><a class="adv-thumb-link-cate adv-thumb-link" '.$link_html.'>'.$image_html.'</a></div>
                            <div class="info "><h2 class="info-title text-center font-medium title20 text-capitalize"><a class="color-title" '.$link_html.'>'.$title_html.'</a></h2></div>';
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