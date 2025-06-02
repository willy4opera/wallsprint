<?php
namespace Elementor;
extract($settings);
$attr = array(
    'item_wrap'         => $item_wrap,
    'item_inner'        => $item_inner,
    'button_icon_pos'   => $button_icon_pos,
    'button_icon'       => $button_icon,
    'button_text'       => $button_text,
    'size'              => $size,
    'excerpt'           => $excerpt,
    'column'            => $column,
    'item_style'        => $item_style,
    'view'              => $view,
    'item_thumbnail'    => $item_thumbnail,
    'item_title'        => $item_title,
    'item_excerpt'      => $item_excerpt,
    'item_button'       => $item_button,
    'item_meta'         => $item_meta,
    'item_meta_select'  => $item_meta_select,
    'thumbnail_hover_animation'     => $thumbnail_hover_animation,
    'style'             => 'grid',
    'item_style_list'   => '',
    );
$dem=1;
$wdata->add_render_attribute( 'elbzotech-wrapper', 'class', ' blog-'.$view.'-post-item-'.$slug);
?>
<div class="elbzotech-wrapper-slider <?php if(!empty($slider_navigation)) echo esc_attr('display-swiper-navi-'.$slider_navigation); ?> <?php if(!empty($slider_pagination)) echo esc_attr('display-swiper-pagination-'.$slider_pagination); ?> <?php if(!empty($slider_scrollbar)) echo esc_attr('display-swiper-scrollbar-'.$slider_scrollbar); ?>">
    <?php echo '<div '.$wdata->get_render_attribute_string( 'elbzotech-wrapper' ).'>';?>
    	<?php echo '<div '.$wdata->get_render_attribute_string( 'elbzotech-inner' ).'>';?>
        	<?php 
        	if($post_query->have_posts()) {
                while($post_query->have_posts()) {
                    $post_query->the_post();
                     $attr['dem'] =$dem;
        			bzotech_get_template_post('grid/grid',$item_style,$attr,true);
                    $dem = $dem+1;
        		}
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