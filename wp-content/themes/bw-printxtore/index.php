<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @package BzoTech-Framework
 */

get_header();
$view           = bzotech_get_option('blog_default_style','list');
$grid_type      = bzotech_get_option('post_grid_type');
$item_style     = bzotech_get_option('post_grid_item_style');
$item_list_style= bzotech_get_option('post_list_item_style');
$excerpt        = bzotech_get_option('post_grid_excerpt',80); if($excerpt == 999) $excerpt = '';
$excerpt_list        = bzotech_get_option('post_list_excerpt','999'); if($excerpt_list == 999) $excerpt_list = '';
$blog_style     = bzotech_get_option('blog_style');
$column= $column_style_type         = bzotech_get_option('post_grid_column',3);
$size           = bzotech_get_option('post_grid_size');
$size_list      = bzotech_get_option('post_list_size');
$number         = get_option('posts_per_page');
$show_number    = bzotech_get_option('blog_number_filter',false);
$show_type     = bzotech_get_option('blog_type_filter',false);
$item_meta_select  = bzotech_get_option('item_meta_select');
if(isset($_GET['number'])) $number = sanitize_text_field($_GET['number']);
$get_type =$type_active = $view;
if(isset($_GET['type']) && $_GET['type']) $get_type = sanitize_text_field($_GET['type']);
if($get_type !== 'list'){
    $type_active = 'grid';
} else  $type_active = 'list';
if($get_type == 'grid-2col'){
    $column = 2;
}else if($get_type == 'grid-3col'){
    $column = 3;
}
$view = $type_active;
$item_wrap = 'class="list-col-item list-'.esc_attr($column).'-item list-2-item-tablet list-2-item-mobile item-grid-post-'.$item_style.'"';
$item_inner = 'class="item-post"';
$button_icon_pos = $button_icon = '';
$button_text = esc_html__("Read more", 'bw-printxtore');
$slug = $item_style;
if($type_active == 'list') $slug = $item_list_style;
if($view == 'slider') $view = 'grid';
$thumbnail_hover_animation = '';
$size = bzotech_get_size_crop($size);
$size_list = bzotech_get_size_crop($size_list);
$attr = array(
    'item_wrap'         => $item_wrap,
    'item_inner'        => $item_inner,
    'type_active'       => $type_active,
    'button_icon_pos'   => $button_icon_pos,
    'button_icon'       => $button_icon,
    'button_text'       => $button_text,
    'size'              => $size,
    'size_list'         => $size_list,
    'excerpt'           => $excerpt,
    'excerpt_list'           => $excerpt_list,
    'column'            => $column,
    'item_style'        => $item_style,
    'item_list_style'   => $item_list_style,
    'view'              => $view,
    'thumbnail_hover_animation'     => $thumbnail_hover_animation,
    'get_type'     => $get_type,
    'item_meta_select'     => $item_meta_select,
    );


$max_page = $GLOBALS['wp_query']->max_num_pages;
$paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
$args = array(
    'post_type'         => 'post',
    'posts_per_page'    => $number,
    'order'             => 'DESC',
    'paged'             => $paged,
);

$curent_query = $GLOBALS['wp_query']->query;
if(is_array($curent_query)) $args = array_merge($args,$curent_query);

              
?>
<?php do_action('bzotech_before_main_content')?>

<div id="main-content" class="main-page-default main-page-blog">

    <div class="bzotech-container">
        <?php bzotech_get_template('top-filter','',array('style'=>$type_active,'number'=>$number,'show_number'=>$show_number,'show_type'=>$show_type,'column_style_type'=>$column_style_type),true); ?>
        <div class="bzotech-row">
            <?php bzotech_output_sidebar('left')?>
            <div class="<?php echo esc_attr(bzotech_get_main_class()); ?>">
            	
                <?php
                if($type_active == 'list' && $view == 'grid') $el_class = 'blog-list-view '.$grid_type;
                else $el_class = 'blog-'.$view.'-view '.$grid_type;
                $el_class .= ' blog-'.$view.'-post-item-'.$slug;
                ?>
                <div class="js-content-wrap elbzotech-posts-wrap <?php echo esc_attr($el_class)?>" data-column="<?php echo esc_attr($column)?>">
                    <?php if(have_posts()):
                        $dem=1; ?>
                        <div class="js-content-main list-post-wrap bzotech-row">
                        
        				    <?php while (have_posts()) :the_post();?>

                                <?php 
                                $attr['dem'] =$dem;
                                bzotech_get_template_post($view.'/'.$view,$slug,$attr,true);
                                $dem = $dem+1;
                                ?>

        				    <?php endwhile;?>

                        </div>
                        
                        <?php 
                        if($blog_style == 'load-more' && $max_page > 1){
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
                        else bzotech_paging_nav(); 
                        ?>

    				<?php else : ?>

    				    <?php echo bzotech_get_template_post( 'content' , 'none' ); ?>

    				<?php endif;?>

                </div>
            </div>
        <?php bzotech_output_sidebar('right')?>
        </div>
    </div>
</div>
<?php do_action('bzotech_after_main_content')?>
<?php get_footer(); ?>
