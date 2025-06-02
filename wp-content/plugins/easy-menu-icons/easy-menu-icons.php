<?php
/**
 * Plugin Name: Easy Menu Icons
 * Description: Design your navigation menus with modern icons and svg images.
 * Plugin URI:  https://themewant.com/downloads/easy-menu-icons-pro/
 * Author:      Themewant
 * Author URI:  http://themewant.com/
 * Version:     1.0.9
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: easy-menu-icons
 * Domain Path: /languages
*/
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    if(!class_exists('EMICONS_PRO')) {

        define( 'EMICONS_VERSION', '1.0.9' );
        define( 'EMICONS_PL_ROOT', __FILE__ );
        define( 'EMICONS_PL_URL', plugins_url( '/', EMICONS_PL_ROOT ) );
        define( 'EMICONS_PL_PATH', plugin_dir_path( EMICONS_PL_ROOT ) );
        define( 'EMICONS_DIR_URL', plugin_dir_url( EMICONS_PL_ROOT ) );
        define( 'EMICONS_PLUGIN_BASE', plugin_basename( EMICONS_PL_ROOT ) );
        define( 'EMICONS_NAME', 'Easy Menu Icons' );

        include 'admin/includes/admin-settings.php';
        include 'admin/includes/menu-metabox.php';
        include 'admin/includes/plugin-scripts.php';
        include 'admin/includes/admin-ajax-request.php';
        include 'public/includes/plugin-scripts.php';
        include 'public/includes/emicons-nav-walker.php';
        include 'public/includes/emicons-dynamic-css.php';
        include 'admin/includes/notice.php';
        include 'class.easy-menu-icons.php';
        
        // Register activation hook
        register_activation_hook(__FILE__, array('EMICONS_NOTICE', 'emicons_after_plugin_activation'));

        EMICONS::instance();
    }    











