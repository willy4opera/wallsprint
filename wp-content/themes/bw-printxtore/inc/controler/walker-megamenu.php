<?php
class Bzotech_Walker_Nav_Menu extends Walker_Nav_Menu {  
	function start_lvl( &$output, $depth = 0, $args = array() ) {

	    $indent = ( $depth > 0  ? str_repeat( "\t", $depth ) : '' ); 
	    $display_depth = ( $depth + 1);
	    $classes = array(
	        'sub-menu',
	        ( $display_depth % 2  ? 'menu-odd' : 'menu-even' ),
	        ( $display_depth >=2 ? 'sub-sub-menu' : '' ),
	        'menu-depth-' . $display_depth
	        );

	    $class_names = implode( ' ', $classes );

	    $output .= "\n" . $indent . '<ul class="' . $class_names . '">' . "\n";

	}  

 	function start_el(  &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
	    $indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // code indent

	  	$icon = $enable_megamenu = $content = $background_url = $col_size = '';
	  	$enable_megamenu 	= get_post_meta($item->ID,'enable_megamenu',true);
	  	$enable_megamenu123 = get_post_meta($item->ID,'enable_megamenu123',true);
	  	$image 				= get_post_meta($item->ID,'image',true);
	  	$icon 				= get_post_meta($item->ID,'icon_menu1',true);
	  	$width 				= get_post_meta($item->ID,'icon_menu2',true);
	  	$content_item 		= get_post_meta($item->ID,'content2',true);
	  	$position_menu 		= get_post_meta($item->ID,'position_menu',true);
	  	$content 			= get_post_meta($item->ID,'content1',true);
	  	if(!empty($content_item)) $content = Bzotech_Template::get_vc_pagecontent($content_item);	  	
	  	$icon_html = $icon ? '<i class="fa '.$icon.'"></i>':'';
	  	$mega_menu = false;
	  	if(empty($width)) $width = '1920px';
	  	if(!empty($content)) $mega_menu = true;

	    $depth_classes = array(
	        ( $depth == 0 ? 'main-menu-item' : 'sub-menu-item' ),
	        ( $depth >=2 ? 'sub-sub-menu-item' : '' ),
	        ( $depth % 2 ? 'menu-item-odd' : 'menu-item-even' ),
	        'menu-item-depth-' . $depth
	    );

	    $depth_class_names = esc_attr( implode( ' ', $depth_classes ) );
	    if(!empty($image)) $depth_class_names .= ' has-image-preview';
	  	if(($enable_megamenu || $enable_megamenu123 || $mega_menu) && $depth == 0) $depth_class_names .= ' has-mega-menu';


	    $classes = empty( $item->classes ) ? array() : (array) $item->classes;
	    $class_names = esc_attr( implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) );
	  	
	    $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
	    $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
	    $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
	    $attributes .= ! empty( $item->url )        ? ' href="'   . esc_url( $item->url        ) .'"' : '';
	    $attributes .= ' class="menu-link ' . ( $depth > 0 ? 'sub-menu-link' : 'main-menu-link' ) . '"';	  
	    $item_output = '';
	    if(is_object($args)){
	    	if(in_array('menu-item-has-children', $item->classes) || $enable_megamenu) $indicator_html = '<i class="indicator-icon"></i>';
	    	else $indicator_html = '';
		    $item_output = sprintf( '%1$s<a%2$s><span>'.$icon_html .'%3$s%4$s'.$indicator_html.'%5$s</span></a>%6$s',
		        $args->before,
		        $attributes,
		        $args->link_before,
		        apply_filters( 'the_title', $item->title, $item->ID ),
		        $args->link_after,
		        $args->after
		    );
		    if(!empty($image))  $item_output .= '<div class="preview-image">
													<a '.$attributes.'>'.wp_get_attachment_image($image,'full').'</a>
												</div>';
	  		

	  		if($mega_menu){
		    	$content = str_replace('../wp-content', esc_url(home_url('/')).'/wp-content', $content);
		    	$output .= $indent . '<li id="nav-menu-item-'. $item->ID . '" class="' . $depth_class_names . ' ' . $class_names . '">';
	    		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	    		if($depth == 0) $output .= '<div class="mega-menu" data-positionmenu = "'.$position_menu.'" '.bzotech_add_html_attr('width:'.esc_attr($width)).'>'.do_shortcode($content).'</div>';
	    	}	

		    else {
		    	$output .= $indent . '<li id="nav-menu-item-'. $item->ID . '" class="' . $depth_class_names . ' ' . $class_names . '">';
		    	$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		    }
		}
	}

	function end_el( &$output, $item, $depth = 0, $args = array() ) {
		$icon 				= get_post_meta($item->ID,'icon_menu'.$depth,true);
	  	$content 			= get_post_meta($item->ID,'content'.$depth,true);
	  	$mega_menu = false;
	  	if(!empty($icon) || !empty($content)) $mega_menu = true;
	  	if($mega_menu){
	  		if($depth == 1 && empty($content)) $output .= "</li>\n";
	  		else $output .= "</li>\n";
	  	}
        else $output .= "</li>\n";
    }

}