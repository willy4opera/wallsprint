<?php
namespace Elementor;
extract($settings); 
use Bzotech_Template;
//https://www.jqueryscript.net/slider/feature-rich-accordion.html
$setting_slider = 'data-width="'.$accordion_slider_width.'" data-height="'.$accordion_slider_height.'" data-responsivemode="'.$accordion_slider_responsivemode.'" data-visiblepanels="'.$accordion_slider_visiblepanels['size'].'" data-autoplay="'.$accordion_slider_autoplay.'" data-startpanel="'.$accordion_slider_startpanel.'"';
?>
<?php echo '<div class="wrap-accordion-slider"><div class="accordion-slider" '.$setting_slider.' >';?>
        <?php echo '<div class="as-panels">';?>
            <?php 
            foreach (  $slider_accordion as $key => $item ) {
                $wdata->add_render_attribute( 'elbzotech-item-slider-'.$style.'-'.$key, 'class', 'as-panel elementor-repeater-item-'.$item['_id'] );
                echo '<div '.$wdata->get_render_attribute_string( 'elbzotech-item-slider-'.$style.'-'.$key ).'>';
                    if(!empty($item['image']['url'])) {
                        if($item['link']['is_external']) $wdata->add_render_attribute( 'img-link', 'target', "_blank");
                        if($item['link']['nofollow']) $wdata->add_render_attribute( 'img-link', 'rel', "nofollow");
                      
                        if($item['link']['url']) $wdata->add_render_attribute( 'img-link', 'href', $item['link']['url']);

                        echo '<a '.$wdata->get_render_attribute_string('img-link').' >';                       
                        
                        echo '<img class="as-background" data-src="'.$item['image']['url'].'" data-retina="'.$item['image']['url'].'"/>';
                        echo '</a>';
                    }
                    if(!empty($item['title']))
                    echo ' <h3 class="panel-title as-layer title20 font-regular as-white as-vertical panel-counter as-closed" 
                         data-position="bottomLeft" data-horizontal="20" data-vertical="20">'.$item['title'].'</h3>
                         <h3 class="panel-title as-layer title20 font-regular as-white as-horizontal panel-counter as-opened" 
                         data-position="bottomLeft" data-horizontal="20" data-vertical="20">'.$item['title'].'</h3>';
                    if(!empty($item['description']))
                    echo '<div class="panel-content as-layer as-opened as-black as-padding title40 font-medium" 
                         data-position="bottomRight" data-horizontal="20" data-vertical="20" data-show-transition="left" data-hide-transition="left">'.$item['description'].'</div>';
                echo '</div>';

                $wdata->remove_render_attribute( 'img-link', 'target', "_blank" );
                $wdata->remove_render_attribute( 'img-link', 'rel', "nofollow");
                $wdata->remove_render_attribute( 'img-link', 'href', $item['link']['url']);
                $wdata->remove_render_attribute( 'img-link', 'href', $item['image']['url']);
                $wdata->remove_render_attribute( 'elbzotech-item', 'class', 'elementor-repeater-item-'.$item['_id'] );
            }
                ?>
        </div>
    </div>
</div>
    