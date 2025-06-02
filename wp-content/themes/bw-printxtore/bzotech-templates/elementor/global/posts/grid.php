<?php
namespace Elementor;
use Bzotech_Template;
$type_active_temp = $type_active;
$column_temp = $column;
extract($settings);
$type_active = $type_active_temp;
$column = $column_temp;


$slug = $item_style;
if($view == 'grid' && $type_active == 'list'){
	$view = $type_active;
	$slug = $item_list_style;
}
$attr = array(
    'item_wrap'         => $item_wrap,
    'item_inner'        => $item_inner,
    'type_active'       => $type_active,
    'button_icon_pos'   => $button_icon_pos,
    'button_icon'       => $button_icon,
    'button_text'       => $button_text,
    'column'            => $column,
    'item_style'        => $item_style,
    'item_list_style'   => $item_list_style,
    'view'              => $view,
    'thumbnail_hover_animation'     => $thumbnail_hover_animation,
    /*------------------*/
    'item_thumbnail'    => $item_thumbnail,
    'size'              => $size,
    'item_title'        => $item_title,
    'item_excerpt'      => $item_excerpt,
    'excerpt'           => $excerpt,
    'item_meta'         => $item_meta,
    'item_meta_select'  => $item_meta_select,
    'item_button'       => $item_button,
    /*------------------*/
    'item_thumbnail_list'    => $item_thumbnail_list,
    'size_list'              => $size_list,
    'item_title_list'        => $item_title_list,
    'item_excerpt_list'      => $item_excerpt_list,
    'excerpt_list'           => $excerpt_list,    
    'item_meta_list'         => $item_meta_list,
    'item_meta_select_list'  => $item_meta_select_list,
    'item_button_list'       => $item_button_list,
);
$wdata->add_render_attribute( 'elbzotech-wrapper', 'class', ' blog-'.$view.'-post-item-'.$slug);

if($show_top_filter == 'yes')
echo bzotech_get_template('top-filter','',array('style'=>$type_active,'number'=>$number,'count_query'=>$post_query->found_posts,'show_number'=>$show_number,'show_type'=>$show_type,'column_style_type'=>$column_style_type));
$dem=1;
$dem_grid=0;
?>
<?php echo '<div '.$wdata->get_render_attribute_string( 'elbzotech-wrapper' ).'>';?>
	<?php echo '<div '.$wdata->get_render_attribute_string( 'elbzotech-inner' ).'>';?>
    	<?php 
    	if($post_query->have_posts()) {
            while($post_query->have_posts()) {
                $template = '';
                if($grid_type == 'grid-custom' and $view == 'grid')
                    foreach (  $list_grid_custom as $key_grid => $item_grid_custom ) {
                         if($dem_grid == count($list_grid_custom)){
                            $dem_grid = 0;
                        } 
                        if($key_grid == $dem_grid){
                            $wdata->add_render_attribute( 'elbzotech-item-grid-custom', 'class', 
                                'list-col-item grid-'.esc_attr($item_grid_custom['row_grid'][0]).'-row grid-'.esc_attr($item_grid_custom['row_grid_tablet']).'-row-tablet grid-'.esc_attr($item_grid_custom['row_grid_mobile']).'-row-mobile grid-'.esc_attr($item_grid_custom['col_grid'][0]).'-col grid-'.esc_attr($item_grid_custom['col_grid_tablet']).'-col-tablet grid-'.esc_attr($item_grid_custom['col_grid_mobile']).'-col-mobile item-grid-post-'.$item_style);
                            
                            $item_wrap_custom = $wdata->get_render_attribute_string( 'elbzotech-item-grid-custom' );
                            $size_grid_custom = $item_grid_custom['thumbnail_size'];
                            if($item_grid_custom['thumbnail_size'] == 'custom' && !empty($item_grid_custom['thumbnail_custom_dimension']['width']) && !empty($item_grid_custom['thumbnail_custom_dimension']['height'])) {
                                $size_grid_custom = array($item_grid_custom['thumbnail_custom_dimension']['width'],$item_grid_custom['thumbnail_custom_dimension']['height']);
                            }
                            if(!empty($size_grid_custom)) 
                                 $attr = array_replace($attr,array('size' => $size_grid_custom));
                            $attr = array_replace($attr,array('item_wrap' => $item_wrap_custom));
                            if($item_grid_custom['template']) $template = $item_grid_custom['template']; 
                        }
                        $wdata->remove_render_attribute( 'elbzotech-item-grid-custom', 'class', 'list-col-item grid-'.esc_attr($item_grid_custom['row_grid'][0]).'-row grid-'.esc_attr($item_grid_custom['row_grid_tablet']).'-row-tablet grid-'.esc_attr($item_grid_custom['row_grid_mobile']).'-row-mobile grid-'.esc_attr($item_grid_custom['col_grid'][0]).'-col grid-'.esc_attr($item_grid_custom['col_grid_tablet']).'-col-tablet grid-'.esc_attr($item_grid_custom['col_grid_mobile']).'-col-mobile item-grid-post-'.$item_style);
                       
                    }
                $post_query->the_post();
                $attr['dem'] =$dem;
                if(!empty($template)){
                    echo '<div '.$item_wrap_custom.'><div '.$item_inner.'>';
                    echo Bzotech_Template::get_vc_pagecontent($template);
                    echo '</div></div>';
                }
                else
    			bzotech_get_template_post($view.'/'.$view,$slug,$attr,true);
                $dem = $dem+1;
                $dem_grid = $dem_grid+1;

                
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
        echo    '<input type="hidden" name="load-more-post-ajax-nonce" class="load-more-post-ajax-nonce" value="' . wp_create_nonce( 'load-more-post-ajax-nonce' ) . '" /><div class="btn-loadmore">
                    <a href="#" class="blog-loadmore loadmore elbzotech-bt-default elbzotech-bt-medium" 
                        data-load="'.esc_attr($data_loadjs).'" data-paged="1" 
                        data-maxpage="'.esc_attr($max_page).'">
                        '.esc_html__("Load more",'bw-printxtore').'
                    </a>
                </div>';
    }
    if($pagination == 'pagination') bzotech_paging_nav($post_query,'',true);
	?>
</div>