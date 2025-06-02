<?php
namespace Elementor;
extract($settings); 
use Bzotech_Template;
?>

<?php echo '<div '.$wdata->get_render_attribute_string( 'elbzotech-wrapper' ).'>';?>

    <?php echo '<div '.$wdata->get_render_attribute_string( 'elbzotech-wrapper-slider' ).'>';?>
        <?php echo '<div '.$wdata->get_render_attribute_string( 'elbzotech-inner' ).'>';?>
            <?php 
            foreach (  $list_testimonial4 as $key => $item ) {

                $wdata->add_render_attribute( 'elbzotech-item-slider-'.$style.'-'.$key, 'class', 'swiper-slide wslider-item elementor-repeater-item-'.$item['_id'] );
                echo '<div '.$wdata->get_render_attribute_string( 'elbzotech-item-slider-'.$style.'-'.$key ).'><div class="item-slider-global-'.$style.'">';
                    echo '<div class="content-slider-custom box-content-custom">'.bzotech_parse_text_editor( $item['content']).'</div>';
                    echo '<div class="info-client flex-wrapper align_items-center">';
                        if(!empty($item['image']['url'])) {
                            if($item['link']['is_external']) $wdata->add_render_attribute( 'img-link', 'target', "_blank");
                            if($item['link']['nofollow']) $wdata->add_render_attribute( 'img-link', 'rel', "nofollow");
                            
                            if($item['image_action']){
                                $wdata->add_render_attribute( 'img-link', 'href', $item['image']['url']);
                            } else
                            if($item['link']['url']) $wdata->add_render_attribute( 'img-link', 'href', $item['link']['url']);

                            echo '<div class="image-wrap zoom-image elementor-animation-'.$image_hover_animation.'"><a '.$wdata->get_render_attribute_string('img-link').' class="img-wrap adv-thumb-link">';
                            echo Group_Control_Image_Size::get_attachment_image_html( $list_testimonial4[$key], 'thumbnail', 'image' );
                            echo '</a></div>';
                        }
                        echo '<div class="title-desc">';
                            if(!empty($item['title'])) echo '<h3 class="title title20 font-semibold">'.$item['title'].'</h3>';
                            if(!empty($item['description'])) echo '<p class="desc title16">'.$item['description'].'</p>';
                            if(!empty($item['number_star'])) echo '<div class="product-rate"><div class="product-rating number-star_'.$item['number_star'].'"></div></div>';
                            
                        echo '</div>';
                    echo '</div>';
                   
                       
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