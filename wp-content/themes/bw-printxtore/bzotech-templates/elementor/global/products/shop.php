<?php
$get_view='';
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
    $column_style_type = $column;
    if(isset($_GET['type'])) $get_view = sanitize_text_field($_GET['type']);
    if(!empty($get_view)){
        if($get_view !== 'list') $view = 'grid'; else $view = 'list';
    }
    
    if(isset($_GET['number'])) $number = sanitize_text_field($_GET['number']);
    
    $slug = $item_style;
    if($view == 'list') $slug = $item_list_style;
    $wdata->add_render_attribute( 'elbzotech-wrapper', 'class', ' shop-'.$view.'-product-item-'.$slug);
?>
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
    if($show_top_filter == 'yes') bzotech_get_template('top-filter','',array('style'=>$get_type,'number'=>$number,'show_number'=>$show_number,'show_type'=>$show_type,'show_order'=>$show_order,'column_style_type'=>$column_style_type),true);
    ?>
    <?php echo '<div '.$wdata->get_render_attribute_string( 'elbzotech-inner' ).'>';?>
    	<?php
        
    	if($product_query->have_posts()) {
            while($product_query->have_posts()) {
                $product_query->the_post();
                $attr['count'] = $count;
    			bzotech_get_template_woocommerce('loop/'.$view.'/'.$view,$slug,$attr,true);
                $count++;
    		}
    	}
    	?>
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