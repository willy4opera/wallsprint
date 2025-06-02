<?php
namespace Elementor;
extract($settings); 
use Bzotech_Template;
?>

<?php echo '<div '.$wdata->get_render_attribute_string( 'elbzotech-wrapper' ).'>';?>

    <?php echo '<div '.$wdata->get_render_attribute_string( 'elbzotech-wrapper-slider' ).'>';?>
        <?php echo '<div '.$wdata->get_render_attribute_string( 'elbzotech-inner' ).'>';?>
            <?php 
            foreach (  $banner_project as $key => $item ) {
                $btn_video='';
                $wdata->add_render_attribute( 'elbzotech-item-slider-'.$style.'-'.$key, 'class', 'swiper-slide wslider-item elementor-repeater-item-'.$item['_id'] );
                echo '<div '.$wdata->get_render_attribute_string( 'elbzotech-item-slider-'.$style.'-'.$key ).'><div class="item-slider-global-'.$style.'">';
                    if(!empty($item['image']['url'])) {

                        if($item['link']['is_external']) $wdata->add_render_attribute( 'img-link', 'target', "_blank");
                        if($item['link']['nofollow']) $wdata->add_render_attribute( 'img-link', 'rel', "nofollow");
                        if($item['link']['url']) $wdata->add_render_attribute( 'img-link', 'href', $item['link']['url']);

                        if($item['image_action']){
                            $wdata->add_render_attribute( 'img-link', 'class', 'popup-video adv-thumb-link');
                            $btn_video = '<span class="icon-button-video"><span class="icon-button-video__icon"><span class="icon-button-video__icon2"><i class="item-icon-e las la-play"></i></span></span></span>';
                        } else{
                            $wdata->add_render_attribute( 'img-link', 'class', 'adv-thumb-link');
                            
                        }
                       

                        echo '<div class="image-wrap swiper-thumb elementor-animation-'.$image_hover_animation.'"><a '.$wdata->get_render_attribute_string('img-link').'>';
                        echo Group_Control_Image_Size::get_attachment_image_html( $banner_project[$key], 'thumbnail', 'image' );
                        if(!empty($item['title'])){
                            echo  '<h3 class="title-banner title16 font-bold color-white text-uppercase">'.$item['title'].'<i class="las la-arrow-right"></i></h3>';
                        }  
                        echo apply_filters('bzotech_output',$btn_video);
                        echo '</a></div>';
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