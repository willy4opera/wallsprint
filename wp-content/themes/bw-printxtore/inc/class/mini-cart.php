<?php
if(!defined('ABSPATH')) return;

if(!class_exists('Bzotech_Mini_Cart'))
{
    class Bzotech_Mini_Cart{

        private static $_instance = null;
   

        public static function _init() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        public function menu_cart_icon_bew( $fragments ) {
                
            
        }
    }

    Bzotech_Template::_init();
}