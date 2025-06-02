<?php
namespace Elementor;
use Bzotech_Template;
extract($settings);
$attr = array(
    'item_wrap'         => $item_wrap,
    'item_inner'        => $item_inner,
    'button_icon_pos'   => $button_icon_pos,
    'button_icon'       => $button_icon,
    'button_text'       => $button_text,
    'size'              => $size,
    'view'              => $view,
    'column'            => $column,
    'item_style'        => $item_style,
    'item_thumbnail'    => $item_thumbnail,
    'item_quickview'    => $item_quickview,
    'item_label'        => $item_label,
    'item_title'        => $item_title,
    'item_rate'         => $item_rate,
    'item_price'        => $item_price,
    'item_button'       => $item_button,
    'item_countdown'       => $item_countdown,
    'item_brand'       => $item_brand,
    'product_type'       => $product_type,
    'animation'         => $thumbnail_hover_animation,
    'item_flash_sale'         => $item_flash_sale,
    'item_attribute'         => $item_attribute,
    'item_excerpt'         => $item_excerpt,
    'excerpt'         => $excerpt,
    );
?>
<div class="elbzotech-wrapper-slider elbzotech-wrapper-slider-product <?php if(!empty($slider_navigation)) echo esc_attr('display-swiper-navi-'.$slider_navigation); ?> <?php if(!empty($slider_pagination)) echo esc_attr('display-swiper-pagination-'.$slider_pagination); ?> <?php if(!empty($slider_scrollbar)) echo esc_attr('display-swiper-scrollbar-'.$slider_scrollbar); ?>">
    <?php echo '<div '.$wdata->get_render_attribute_string( 'elbzotech-wrapper' ).'>';?>
        <?php
        if($filter_show == 'yes'){
            $data_filter = array(
                'args'          => $args,
                'attr'          => $attr,
                'filter_style'  => $filter_style,
                'filter_column' => $filter_column,
                'filter_cats'   => $filter_cats,
                'filter_price'  => $filter_price,
                'filter_attr'   => $filter_attr,
                'filter_pos'    => '',
            );
            bzotech_get_template_woocommerce('loop/filter-product','',$data_filter,true);
        }
        ?>
        <?php 
       
        echo '<div '.$wdata->get_render_attribute_string( 'elbzotech-inner' ).'>'; ?>
            <?php 

            $dem_grid=0;$dem_grid2=0; $dem_insert_template=0; $template_no_replace = true;
            $default_template = [
                                    'style1'        => esc_html__( 'Style 1 (Replace)', 'bw-printxtore' ),
                                    'style2'        => esc_html__( 'Style 2 (Replace)', 'bw-printxtore' ),
                                    'style3'        => esc_html__( 'Style 3 (Replace)', 'bw-printxtore' ),
                                    'style5'        => esc_html__( 'Style 5 (Replace)', 'bw-printxtore' ),
                                ];
            $tong_templaste = 0;
            foreach ($list_grid_custom as $value ) {
                if($value['template']){ 
                    if(!array_key_exists($value['template'], $default_template)){
                        $tong_templaste = $tong_templaste + 1; 
                    }
                   
                }
            }
            $product_array = array();
            if($product_query->have_posts()) {
                while($product_query->have_posts()) {
                    $product_query->the_post();
                    $template = $item_style_replace='';
                    
                    if($dem_grid == count($list_grid_custom)){
                        $dem_grid = 0;
                        $tong_templaste = 0;
                        $template_no_replace = false;
                    } 
                    if($dem_grid2+$tong_templaste>= count($list_grid_custom)){
                        $dem_grid2= 0;
                    }

                    foreach ($list_grid_custom as $key_grid => $item_grid_custom ) {
                        if($key_grid == $dem_grid && $item_grid_custom['template'] ){
                            
                            if(array_key_exists($item_grid_custom['template'], $default_template)){
                                $item_style_replace= $item_grid_custom['template']; 
                            }
                            else if($template_no_replace){

                                $template = $item_grid_custom['template']; 
                                $dem_grid2 = $dem_grid2+1;
                            }
                            
                        }
                        
                        if($key_grid == $dem_grid2){
                            $size_grid_custom = $item_grid_custom['thumbnail_size'];
                            if($item_grid_custom['thumbnail_size'] == 'custom' && !empty($item_grid_custom['thumbnail_custom_dimension']['width']) && !empty($item_grid_custom['thumbnail_custom_dimension']['height'])) {
                                $size_grid_custom = array($item_grid_custom['thumbnail_custom_dimension']['width'],$item_grid_custom['thumbnail_custom_dimension']['height']);
                            }
                            if(!empty($size_grid_custom)) 
                                $attr = array_replace($attr,array('size' => $size_grid_custom));
                            
                        }
                        if($key_grid == $dem_grid && $item_grid_custom['template'] && $template_no_replace){
                            
                            if(!array_key_exists($item_grid_custom['template'], $default_template)){
                                
                                $dem_grid2 = $dem_grid2-1;
                            }
                            
                        }

                        
                    }
                    if(!empty($template) && $template_no_replace){
                        $product_array[] =  Bzotech_Template::get_vc_pagecontent($template);
                    }

                    $item_style_ok = $item_style;

                    if(!empty($item_style_replace)) $item_style_ok=$item_style_replace;
                    $attr['count'] = $count;
                    $product_array[] = bzotech_get_template_woocommerce('loop/grid/grid',$item_style_ok,$attr,false);
                    $count++;
                    $dem_grid++;
                    $dem_grid2++;
                    
                }
            }
            $dem = 1; $dem_class=0; $check_count_query=1;
            foreach ($product_array  as $key=>$value) {
                $item_wrap_ok = '';
                if($dem_class == count($list_grid_custom)){
                        $dem_class = 0;
                }

                foreach ($list_grid_custom as $key_class => $item_grid_class ) {
                       
                        if($key_class == $dem_class){
                            
                            if(array_key_exists($item_grid_class['template'], $default_template)){
                                $item_style_class= $item_grid_class['template']; 
                                
                            }else $item_style_class = $item_style;

                            $item_wrap_ok = 'class="width_masory '.$item_grid_class['add_class_css'].' item-grid-product-'.$item_style_class.' elementor-repeater-item-'.$item_grid_class['_id'].'"';
                        }
                }
                if($slider_items_group > 1 and ($dem ==1 || $dem % $slider_items_group == 1)) echo '<div class="swiper-slide"><div class=" grid-masory-packery">';

                echo '<div '.$item_wrap_ok.'>';
                    echo '<div '.$item_inner.'>';
                        echo apply_filters('bzotech_output_content',$value);
                echo '</div></div>';
                if($slider_items_group > 1 and ($dem % $slider_items_group == 0 || $dem == $slider_items_group|| $check_count_query == $count_query)) echo '</div></div>';
                if($dem == count($list_grid_custom)){
                    $dem = 0;
                } 
                $dem++;
                $dem_class++;
                $check_count_query++;
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
        <div class="swiper-pagination "></div>
    <?php endif?>
    <?php if ( $slider_scrollbar !== '' ):?>
        <div class="swiper-scrollbar"></div>
    <?php endif?>
</div>