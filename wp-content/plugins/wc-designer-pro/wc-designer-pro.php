<?php
/*
 * Plugin Name:     WooCommerce Designer Pro
 * Plugin URI:      http://jmaplugins.com
 * Description:     Product Designer for Wordpress | WooCommerce. Create your online printing.
 * Author:          JMAPlugins
 * Author URI:      http://jmaplugins.com
 * Text Domain:     wcdp
 * Domain Path:     /languages
 * Version:         1.9.24
 */
 
if(!defined('ABSPATH')) exit;

$wp_upload_dir = wp_upload_dir();
define('WCDP_VERSION', '1.9.24');
define('WCDP_URL', plugins_url('', __FILE__));
define('WCDP_PATH', plugin_dir_path( __FILE__ ));
define('WCDP_PATH_UPLOADS', $wp_upload_dir['basedir'] .'/wcdp-uploads');
define('WCDP_URL_UPLOADS', $wp_upload_dir['baseurl'] .'/wcdp-uploads');
define('WCDP_MAIN_FILE', 'wc-designer-pro/wc-designer-pro.php');

require_once(WCDP_PATH. 'settings.php');
require_once(WCDP_PATH. 'includes/wcdp-functions.php');
require_once(WCDP_PATH. 'includes/wcdp-admin-menus.php');
require_once(WCDP_PATH. 'includes/wcdp-upload-images.php');
require_once(WCDP_PATH. 'includes/wcdp-upload-fonts.php');
require_once(WCDP_PATH. 'includes/wcdp-manage-shapes.php');
require_once(WCDP_PATH. 'includes/wcdp-manage-filters.php');
require_once(WCDP_PATH. 'includes/wcdp-metabox-cliparts.php');
require_once(WCDP_PATH. 'includes/wcdp-metabox-calendars.php');
require_once(WCDP_PATH. 'includes/wcdp-metabox-params.php');
require_once(WCDP_PATH. 'includes/wcdp-metabox-categories.php');
require_once(WCDP_PATH. 'includes/wcdp-metabox-designs.php');
require_once(WCDP_PATH. 'includes/wcdp-save-design.php');
require_once(WCDP_PATH. 'includes/wcdp-duplicate-design.php');
require_once(WCDP_PATH. 'includes/wcdp-order-design.php');
require_once(WCDP_PATH. 'includes/wcdp-my-designs.php');
require_once(WCDP_PATH. 'includes/wcdp-skin-style.php');
require_once(WCDP_PATH. 'includes/wcdp-editor-shortcode.php');
require_once(WCDP_PATH. 'includes/wcdp-content-editor.php');
require_once(WCDP_PATH. 'includes/wcdp-convert-colors.php');
require_once(WCDP_PATH. 'includes/wcdp-translations.php');
require_once(WCDP_PATH. 'includes/wcdp-docs.php');
	
register_activation_hook(__FILE__, 'wcdp_plugin_activate');
register_deactivation_hook(__FILE__, 'wcdp_plugin_deactivate');
	
add_action( 'init', 'wcdp_plugin_assets_enqueue');
add_action( 'admin_init', 'wcdp_plugin_assets_enqueue_admin' );