<?php

/**
 * Plugin Name: Bzotech Core
 * Plugin URI: #
 * Description: Contains all core functions. Required for all Bzotech themes.
 * Version: 2.0
 * Author: Bzotech
 * Author URI: #
 * Requires at least: 3.8
 * Tested up to: 4.3
 *
 * Text Domain: bzotech-core
 *
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if(!defined('HEROT_TEXTDOMAIN')){
    define('HEROT_TEXTDOMAIN','bzotech-core');
}

defined( 'COREHEROT_PLUGIN_URL' ) or define('COREHEROT_PLUGIN_URL', plugins_url( '/', __FILE__ )) ;

defined( 'COREHEROT_OPTIONS_BY_ELEMENTS' ) or define('COREHEROT_OPTIONS_BY_ELEMENTS', '_bzotech_options_by_elements') ;

defined( 'COREHEROT_CUSTOM_ELEMENTS' ) or define('COREHEROT_CUSTOM_ELEMENTS', '_bzotech_custom_elements') ;

defined( 'COREHEROT_MENU_TAB_POSITION' ) or define('COREHEROT_MENU_TAB_POSITION', '_bzotech_menu_tab_position') ;

defined( 'COREHEROT_PARAM_PREFIX' ) or define('COREHEROT_PARAM_PREFIX', 'bzotech_');
defined( 'COREHEROT_DEVICES' ) or define('COREHEROT_DEVICES', '_bzotech_devices');
defined( 'COREHEROT_SHOWHIDE' ) or define('COREHEROT_SHOWHIDE', '_bzotech_showhide');

if(!class_exists('PluginCore'))
{
    class PluginCore
    {
        static protected $_dir='';
        static protected $_uri='';
        static $plugins_data;

        static function init()
        {

            add_action( 'plugins_loaded', array(__CLASS__,'_load_text_domain') );

            self::$_dir=plugin_dir_path(__FILE__);
            self::$_uri=plugin_dir_url(__FILE__);

            global $this_file;
            $this_file=__FILE__;

            self::load_core_class();

            self::load_required_class();


            require_once self::dir('libs/menu.exporter.php');
            require_once self::dir('libs/importer/importer.php');
            require_once self::dir('libs/reduxframe/reduxframe.php');
            require_once self::dir('libs/reduxframe/metaboxes-config.php');

            add_filter( 'user_contactmethods', array(__CLASS__,'_add_author_profile'), 10, 1);
            add_action( 'admin_init', array(__CLASS__,'bzotech_disable_vc_update'), 9 );
            add_filter( 'style_loader_src',array(__CLASS__,'_remove_enqueue_ver'), 10, 2 );
            add_filter( 'script_loader_src',array(__CLASS__,'_remove_enqueue_ver'), 10, 2 );
        }
        static function  _load_auto_update()
        {
            self::$plugins_data=get_plugin_data(__FILE__);
            self::$plugins_data['plugin_basename']=plugin_basename(__FILE__);

            require_once self::dir('libs/class.autoupdater.php');

        }
        static function _load_text_domain()
        {
            load_plugin_textdomain( 'bzotech-core', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
        }

        static function load_core_class()
        {
            $array=glob(self::dir().'core/*');

            if(!is_array($array)) return false;

            $dirs = array_filter($array, 'is_file');

            if(!empty($dirs))
            {
                foreach($dirs as $key=>$value)
                {
                    require_once $value;

                }
            }
        }


        static function load_required_class()
        {
            // Fix array_filter argument should be an array
            $class=glob(self::dir().'class/*');
            if(!is_array($class)) return false;

            $dirs = array_filter($class, 'is_file');

            if(!empty($dirs))
            {
                foreach($dirs as $key=>$value)
                {
                    require_once $value;

                }
            }
        }



        // Helper functions
        static function dir($file=false)
        {
            return self::$_dir.$file;
        }


        static function uri($file=false)
        {
            return self::$_uri.$file;
        }

        static function _add_author_profile( $contactmethods ){       
            $contactmethods['googleplus']   = esc_html__('Google Profile URL','bzotech-core');
            $contactmethods['twitter']      = esc_html__('Twitter Profile URL','bzotech-core');
            $contactmethods['facebook']     = esc_html__('Facebook Profile URL','bzotech-core');
            $contactmethods['linkedin']     = esc_html__('Linkedin Profile URL','bzotech-core');
            $contactmethods['pinterest']    = esc_html__('Pinterest Profile URL','bzotech-core');
            $contactmethods['github']       = esc_html__('Github Profile URL','bzotech-core');
            $contactmethods['instagram']    = esc_html__('Instagram Profile URL','bzotech-core');
            $contactmethods['vimeo']        = esc_html__('Vimeo Profile URL','bzotech-core');       
            $contactmethods['youtube']      = esc_html__('Youtube Profile URL','bzotech-core');       
            return $contactmethods;
        }        

        static function bzotech_disable_vc_update() {
            if (function_exists('vc_license') && function_exists('vc_updater') && ! vc_license()->isActivated()) {

                remove_filter( 'upgrader_pre_download', array( vc_updater(), 'preUpgradeFilter' ), 10);
                remove_filter( 'pre_set_site_transient_update_plugins', array(
                    vc_updater()->updateManager(),
                    'check_update'
                ) );

            }
        }

        static function _remove_enqueue_ver($src)    {
            if (strpos($src, '?ver='))
                $src = remove_query_arg('ver', $src);
            return $src;
        }

    }
    PluginCore::init();
}
