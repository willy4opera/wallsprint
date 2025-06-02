<?php
/**
 * Created by Sublime Text 3.
 * User: MBach90
 * Date: 12/08/15
 * Time: 10:20 AM
 */

if(!class_exists('Bzotech_Assets'))
{
    class Bzotech_Assets
    {
        static $asset_url;

        static $inline_css;
        static $current_css_id;
        static $prefix_class="bzotech_";

        static function _init()
        {
            self::$current_css_id=time();
            
        }

        static function build_css($string=false,$effect = false){
            self::$current_css_id++;
            self::$inline_css.="
                .".self::$prefix_class.self::$current_css_id.$effect."{
                    {$string}
                }
        ";
            return self::$prefix_class.self::$current_css_id;
        }

        static function add_css($string=false){
            self::$inline_css.=$string;

        }

        static function _action_footer_css(){
            $string = trim(preg_replace('/\s\s+/', ' ', self::$inline_css));
            if(function_exists('bzotech_add_inline_style')){
                print bzotech_add_inline_style($string);
            }
        }
    }

    Bzotech_Assets::_init();
}