<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

class EMICONS_Add_Icons {

    private static $_instance = null;


    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        add_action('plugins_loaded', [$this, 'init']);
    }

    public function init() {
        add_filter('walker_nav_menu_start_el', [$this, 'add_icon_to_nav_menu'], 10, 4);
    }

    public function add_icon_to_nav_menu($item_output, $item, $depth, $args) {

        // Retrieve the icon class or HTML from the menu item's post meta
        $icon_class = get_post_meta($item->ID, '_menu_item_icon_class', true); // Example meta key

        $icon_styles = '';

        $emicons_options = get_option( 'emicons_options' );
        $EMICONS_item_icon_position = isset($emicons_options['icon_position']) && !empty($emicons_options['icon_position']) ? $emicons_options['icon_position']: 'left';

        
        $emicons_item_settings = get_post_meta( $item->ID, 'emicons_settings', true );

        if(isset($emicons_item_settings['css'])){
    

            $emicons_item_css = $emicons_item_settings['css'];
    
    
    
            if( isset( $emicons_item_css['icon_position'] ) && !empty( $emicons_item_css['icon_position'] ) && $item->ID  ){
                $EMICONS_item_icon_position = $emicons_item_css['icon_position'];
            }
    
      
            $menu_item_icon_source = $emicons_item_settings['content']['icon_source'];
    
            if( isset( $emicons_item_css['icon_color']) && !empty( $emicons_item_css['icon_color'] ) ){
                $icon_styles .= 'color:'.$emicons_item_css['icon_color'] .';';
            }
    
        
            if($menu_item_icon_source == 'custom'){
                if( isset( $emicons_item_css['icon_font_size']) && !empty( $emicons_item_css['icon_font_size'] ) ){
                    $icon_styles .= 'height:'.$emicons_item_css['icon_font_size'] .';';
                }
            }else{
                if( isset( $emicons_item_css['icon_font_size']) && !empty( $emicons_item_css['icon_font_size'] ) ){
                    $icon_styles .= 'font-size:'.$emicons_item_css['icon_font_size'] . ';';
                }
            }
            
    
            if( isset( $emicons_item_css['icon_margin_left']) && !empty( $emicons_item_css['icon_margin_left'] ) ){
                $icon_styles .= 'margin-left:'.$emicons_item_css['icon_margin_left'] . ';';
            }
            if( isset( $emicons_item_css['icon_margin_right']) && !empty( $emicons_item_css['icon_margin_right'] ) ){
                $icon_styles .= 'margin-right:'.$emicons_item_css['icon_margin_right'] . ';';
            }
            if( isset( $emicons_item_css['icon_margin_top']) && !empty( $emicons_item_css['icon_margin_top'] ) ){
                $icon_styles .= 'margin-top:'.$emicons_item_css['icon_margin_top'] . ';';
            }
            if( isset( $emicons_item_css['icon_margin_bottom']) && !empty( $emicons_item_css['icon_margin_bottom'] ) ){
                $icon_styles .= 'margin-bottom:'.$emicons_item_css['icon_margin_bottom'] . ';';
            }
    
        }
    

        $menu_item_icon = '';
        if( isset($emicons_item_settings['content']['icon_source']) 
        && !empty( $emicons_item_settings['content']['icon_source'] )
        && isset( $emicons_item_settings['content']['menu_icon'] )
        && !empty( $emicons_item_settings['content']['menu_icon'] ) ){

            $icon_class = '';
            

            if($EMICONS_item_icon_position == 'right'){
                $icon_class = ' icon-right ';
            }else{
                $icon_class = ' icon-left ';
            }


            if($menu_item_icon_source == 'dashicon'){
                $menu_item_icon = '<span class="emicons menu-icon '. $icon_class . $emicons_item_settings['content']['menu_icon'].'" style="'.$icon_styles.'"></span>';
            }else if($menu_item_icon_source == 'fontawesome'){
                $menu_item_icon = '<i class="emicons menu-icon '. $icon_class . $emicons_item_settings['content']['menu_icon'].'" style="'.$icon_styles.'"></i>';
            }
        
        }

        
        // Add icon before menu text
        if (!empty($menu_item_icon)) {
            if ($EMICONS_item_icon_position == 'right') {
                $item_output = str_replace($args->link_before . $item->title, $args->link_before . $item->title . $menu_item_icon, $item_output);
            } else {
                $item_output = str_replace($args->link_before . $item->title, $args->link_before . $menu_item_icon . $item->title, $item_output);
            }
            
        }

        return $item_output;
    }
}

// Ensure that you instantiate the class somewhere in your plugin.
$emicons_add_icons = EMICONS_Add_Icons::instance();
