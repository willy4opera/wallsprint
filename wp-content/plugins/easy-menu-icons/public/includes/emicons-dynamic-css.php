<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

add_action( 'wp_enqueue_scripts', 'emicons_dynamic_css' );
function emicons_dynamic_css() {

    $emicons_options = get_option( 'emicons_options' ); 
    $main_menu_color = !empty($emicons_options['icon_color']) ? $emicons_options['icon_color'] : '';
    $icon_font_size = !empty($emicons_options['icon_font_size']) ? $emicons_options['icon_font_size'] : '';
    $icon_margin = !empty($emicons_options['icon_margin']) ? $emicons_options['icon_margin'] : '';

    $icon_margin_left = !empty($icon_margin['margin_left']) ? $icon_margin['margin_left'] : '';
    $icon_margin_right = !empty($icon_margin['margin_right']) ? $icon_margin['margin_right'] : '';
    $icon_margin_top = !empty($icon_margin['margin_top']) ? $icon_margin['margin_top'] : '';
    $icon_margin_bottom = !empty($icon_margin['margin_bottom']) ? $icon_margin['margin_bottom'] : '';
    

    $custom_css = "";

    if(!empty($main_menu_color)){
        $custom_css .= "
        .menu-item > a .emicons.menu-icon {
            color: {$main_menu_color};
        }";
        ?>
        
        <?php
    }
    if(!empty($icon_font_size)){
         $custom_css .= "
        .menu-item > a .emicons.menu-icon {
            font-size: {$icon_font_size}
        }";
    }

    if(!empty($icon_margin)){
        $custom_css .= "
       .menu-item > a .emicons.menu-icon {
           margin-left: {$icon_margin_left};
           margin-right: {$icon_margin_right};
           margin-top: {$icon_margin_top};
           margin-bottom: {$icon_margin_bottom};
       }";
   }

    wp_add_inline_style( 'emicons-style', $custom_css );
}


