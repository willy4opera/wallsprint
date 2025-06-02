<?php
/**
 * Plugin Name: Bzotech Elementor
 * Description: An Elementor add-on to showcase your Count down, Info Box, Team, Testimonial, Heading and more. Required with themes of Bzotech.
 * Plugin URI:  #
 * Version:     2.1
 * Author:      Bzotech
 * Author URI:  #
 * Text Domain: bzotech-elementor
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'ELEMENTOR_HELLO_WORLD__FILE__', __FILE__ );
if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}
/**
 * Load Hello World
 *
 * Load the plugin after Elementor (and other plugins) are loaded.
 *
 * @since 1.0.0
 */
function hello_world_load() {
	// Load localization file
	load_plugin_textdomain( 'bzotech-elementor' );

	// Notice if the Elementor is not active
	/*if ( ! did_action( 'elementor/loaded' ) ) {
		add_action( 'admin_notices', 'hello_world_fail_load' );
		return;
	}*/

	// Check required version
	$elementor_version_required = '1.8.0';
	if ( class_exists('Elementor\Core\Admin\Admin') && ! version_compare( ELEMENTOR_VERSION, $elementor_version_required, '>=' ) ) {
		add_action( 'admin_notices', 'hello_world_fail_load_out_of_date' );
		return;
	}

	// Require the main plugin file
	require( __DIR__ . '/plugin.php' );
}
add_action( 'plugins_loaded', 'hello_world_load' );


function hello_world_fail_load_out_of_date() {
	if ( ! current_user_can( 'update_plugins' ) ) {
		return;
	}

	$file_path = 'elementor/elementor.php';

	$upgrade_link = wp_nonce_url( self_admin_url( 'update.php?action=upgrade-plugin&plugin=' ) . $file_path, 'upgrade-plugin_' . $file_path );
	$message = '<p>' . __( 'Elementor Hello World is not working because you are using an old version of Elementor.', 'bzotech-elementor' ) . '</p>';
	$message .= '<p>' . sprintf( '<a href="%s" class="button-primary">%s</a>', $upgrade_link, __( 'Update Elementor Now', 'bzotech-elementor' ) ) . '</p>';

	echo '<div class="error">' . $message . '</div>';
}
