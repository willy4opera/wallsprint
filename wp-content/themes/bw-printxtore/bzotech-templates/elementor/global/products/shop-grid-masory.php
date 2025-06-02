<?php
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
$default_template = [
                        'style1'        => esc_html__( 'Style 1 (Replace)', 'bw-printxtore' ),
                        'style2'        => esc_html__( 'Style 2 (Replace)', 'bw-printxtore' ),
                        'style3'        => esc_html__( 'Style 3 (Replace)', 'bw-printxtore' ),
                        'style5'        => esc_html__( 'Style 5 (Replace)', 'bw-printxtore' ),
                    ];
?>
<?php echo '<div '.$wdata->get_render_attribute_string( 'elbzotech-wrapper' ).'>';?>
    <?php echo '<div '.$wdata->get_render_attribute_string( 'elbzotech-inner' ).'>'; ?>
        <div class=" grid-masory-packery">
            <?php
            $count_list = count($list_grid_custom)-1;
            $dem=0; 
            if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();
                        
                        if($dem>$count_list)$dem=0;
                        $template_custom=$class_item_temp=''; $class_item='class="width_masory"'; $item_style_ok = $item_style;
                            foreach ($list_grid_custom as $key => $item_grid_custom ) {

                         
                                if($dem == $key){
                                    if($item_grid_custom['template']){
                                        $template_custom =  $item_grid_custom['template'];
                                        $class_item_temp  = 'class="width_masory '.$item_grid_custom['add_class_css'].' elementor-repeater-item-'.$item_grid_custom['_id'].'"';
                                    }

                                    $size_grid_custom = $item_grid_custom['thumbnail_size'];
                                  
                                    if($item_grid_custom['thumbnail_size'] == 'custom' && !empty($item_grid_custom['thumbnail_custom_dimension']['width']) && !empty($item_grid_custom['thumbnail_custom_dimension']['height'])) {
                                        $size_grid_custom = array($item_grid_custom['thumbnail_custom_dimension']['width'],$item_grid_custom['thumbnail_custom_dimension']['height']);
                                    }
                                    if(!empty($size_grid_custom) && $size_grid_custom !== 'custom') 
                                        $attr = array_replace($attr,array('size' => $size_grid_custom));
                                    else{$attr = array_replace($attr,array('size' => 'custom'));}
                                    if(array_key_exists($item_grid_custom['template'], $default_template)){
                                        $item_style_ok= $item_grid_custom['template']; 
                                    }
                                    $class_item = 'class="width_masory '.$item_grid_custom['add_class_css'].' item-grid-product-'.$item_style_ok.' elementor-repeater-item-'.$item_grid_custom['_id'].'"';
                                
                                    
                                    break;
                                }
                            }
                        
                            
                        if(!empty($template_custom)){
                            echo '<div '.$class_item_temp.'>';
                            echo  Bzotech_Template::get_vc_pagecontent($template_custom);
                            echo '</div>';
                        }else{
                            echo '<div '.$class_item.'>';
                            echo bzotech_get_template_woocommerce('loop/grid/grid',$item_style_ok,$attr,false);
                            echo '</div>';
                        }
                        
                        
                        
                        $dem++;
                    }
                }

            ?>
        </div>
    </div>

    <?php
    if($pagination == 'load-more' && $max_page > 1){
        $data_load = array(
            "args"        => $args,
            "attr"        => $attr,
            );
        $data_loadjs = json_encode($data_load);
        echo    '<input type="hidden" name="load-more-product-ajax-nonce" class="load-more-product-ajax-nonce" value="' . wp_create_nonce( 'load-more-product-ajax-nonce' ) . '" /><div class="btn-loadmore">
                    <a href="#" class="product-loadmore loadmore elbzotech-bt-default elbzotech-bt-medium" 
                        data-load="'.esc_attr($data_loadjs).'" data-paged="1" 
                        data-maxpage="'.esc_attr($max_page).'">
                        '.esc_html__("Load more",'bw-printxtore').'
                    </a>
                </div>';
    }
    if($pagination == 'pagination') bzotech_get_template_woocommerce('loop/pagination','',array('wp_query'=>$product_query),true);
    ?>
</div>