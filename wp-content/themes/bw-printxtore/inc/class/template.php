<?php
if(!defined('ABSPATH')) return;

if(!class_exists('Bzotech_Template'))
{
    class Bzotech_Template{

        static $_template_dir;
        static $_template_dir_assets;

        static function _init()
        {
            self::$_template_dir=apply_filters('bzotech_template_dir','/bzotech-templates');
            self::$_template_dir_assets=apply_filters('bzotech_template_dir','/assets');

        }


        static function load_view($view_name,$slug=false,$data=array(),$echo=FALSE)
        {
            $template_path = get_template_directory();
            $stylesheet_path = get_stylesheet_directory();
            if($slug){
                $path = $stylesheet_path .self::$_template_dir.'/'.$view_name.'-'.$slug.'.php';
                if( $template_path != $stylesheet_path && is_file($path) ) $template = $path;
                else $template =  get_template_directory().self::$_template_dir.'/'.$view_name.'-'.$slug.'.php';
                if(!is_file($template)){
                    $path = $stylesheet_path .self::$_template_dir.'/'.$view_name.'.php';
                    if( $template_path != $stylesheet_path && is_file($path) ) $template = $path;
                    else $template = get_template_directory().self::$_template_dir.'/'.$view_name.'.php';
                }
            }else{
                $path = $stylesheet_path .self::$_template_dir.'/'.$view_name.'.php';
                if( $template_path != $stylesheet_path && is_file($path) ) $template = $path;
                else $template = get_template_directory().self::$_template_dir.'/'.$view_name.'.php';
            }
            $template=apply_filters('bzotech_load_view',$template,$view_name,$slug);
            if($data) extract($data);
            if(file_exists($template)){

                if(!$echo){

                    ob_start();
                    include $template;
                    return @ob_get_clean();

                }else

                include $template;
            }
        }     
        static function load_view_assets($view_name,$slug=false,$data=array(),$echo=FALSE)
        {
            $template_path = get_template_directory();
            $stylesheet_path = get_stylesheet_directory();
            if($slug){
                $path = $stylesheet_path .self::$_template_dir_assets.'/'.$view_name.'-'.$slug.'.css';
                if( $template_path != $stylesheet_path && is_file($path) ) $template = $path;
                else $template =  get_template_directory().self::$_template_dir_assets.'/'.$view_name.'-'.$slug.'.css';
                if(!is_file($template)){
                    $path = $stylesheet_path .self::$_template_dir_assets.'/'.$view_name.'.css';
                    if( $template_path != $stylesheet_path && is_file($path) ) $template = $path;
                    else $template = get_template_directory().self::$_template_dir_assets.'/'.$view_name.'.css';
                }
            }else{
                $path = $stylesheet_path .self::$_template_dir_assets.'/'.$view_name.'.css';
                if( $template_path != $stylesheet_path && is_file($path) ) $template = $path;
                else $template = get_template_directory().self::$_template_dir_assets.'/'.$view_name.'.css';
            }
            $template=apply_filters('bzotech_load_view',$template,$view_name,$slug);
            if($data) extract($data);
            if(file_exists($template)){

                if(!$echo){

                    ob_start();
                    include $template;
                    return @ob_get_clean();

                }else

                include $template;
            }
        }       

        public static function get_vc_pagecontent($page_id=false,$remove_wrap = false)
        {
            if($page_id)
            {

                if(class_exists('Elementor\Plugin')){

                   $elementor = \Elementor\Plugin::instance();
                    return $elementor->frontend->get_builder_content_for_display( $page_id );
                    
                }
                else{
                    $page = get_post($page_id);
                    $content = apply_filters('the_content', $page->post_content );
                    return $content;
                }
            }
        }

        static function remove_wpautop( $content, $autop = false ) {

            if ( $autop ) {
                $content = wpautop( preg_replace( '/<\/?p\>/', "\n", $content ) . "\n" );
            }
            return do_shortcode( shortcode_unautop( $content) );
        }
    }

    Bzotech_Template::_init();
}