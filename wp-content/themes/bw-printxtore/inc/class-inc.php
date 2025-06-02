<?php
if(!defined('ABSPATH')) return;

if(!class_exists('Bzotech_Inc')){
    class Bzotech_Inc{
        
        /**
         * 
         * Require theme file
         *
         * @return void
         * 
         */

		static function _init(){
			get_template_part('inc/functions' );
			get_template_part('inc/config' );
		    
		    if ( is_admin() ) {
		        get_template_part( 'inc/class/class-tgm-plugin-activation' );
				get_template_part( 'inc/class/require-plugin' );
		    }
		   
			get_template_part('inc/class/asset' );
			get_template_part('inc/class/importer' );
			get_template_part('inc/class/mega_menu' );
			get_template_part('inc/class/order-comment-field' );
			get_template_part('inc/class/template' );

			get_template_part('inc/controler/base-control' );
			get_template_part('inc/controler/customize-control' );
			get_template_part('inc/controler/walker-megamenu' );
			get_template_part('inc/controler/multi-language-control' );
		    get_template_part('inc/controler/header-control' );
			get_template_part('inc/controler/footer-control' );
			get_template_part('inc/controler/megaItem-control' );
			if(class_exists('woocommerce')){
				get_template_part('inc/controler/woocommerce-control' );
				get_template_part('inc/controler/woocommerce-variable' );
		    }
			if(class_exists('Elementor\Core\Admin\Admin')) 
				get_template_part('inc/controler/elementor-control' );
			if(class_exists('Redux'))
				get_template_part('inc/controler/redux-config' );
			
			get_template_part('inc/controler/metabox-control' );
		    
		    //Load widgets auto
		    if(function_exists('bzotech_load_lib')){
				bzotech_load_lib('widget');
			}
		}
    }
    Bzotech_Inc::_init();
}