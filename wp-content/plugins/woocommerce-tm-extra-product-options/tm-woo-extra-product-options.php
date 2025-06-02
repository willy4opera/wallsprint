<?php
/**
 * Plugin Name: Extra Product Options & Add-Ons for WooCommerce
 * Plugin URI: https://epo.themecomplete.com/
 * Description: <code><strong>Extra Product Options</strong></code> elevates your WooCommerce store by adding customizable add-ons.
 * Author: ThemeComplete
 * Author URI: https://themecomplete.com/
 *
 * Version: 6.5.1
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * Tested up to: 7.0
 * Requires Plugins: woocommerce
 * WC requires at least: 4.1
 * WC tested up to: 10.0
 * Copyright: © 2024 THEMECOMPLETE LTD
 *
 * @package  Extra Product Options & Add-Ons for WooCommerce
 * @category Core
 * @author   ThemeComplete
 */

defined( 'ABSPATH' ) || exit;

// Define THEMECOMPLETE_EPO_PLUGIN_FILE.
if ( ! defined( 'THEMECOMPLETE_EPO_PLUGIN_FILE' ) ) {
	define( 'THEMECOMPLETE_EPO_PLUGIN_FILE', __FILE__ );
}

// Check if another plugin is overwriting our classes.
if ( class_exists( 'Themecomplete_Extra_Product_Options_Setup' ) ) {
	return;
}

// Include the main Extra Product Options Setup class.
require_once __DIR__ . '/includes/class-themecomplete-extra-product-options-setup.php';

/**
 * Main instance of Themecomplete_Extra_Product_Options_Setup.
 *
 * @since 4.8
 * @return Themecomplete_Extra_Product_Options_Setup
 */
function themecomplete_extra_product_options_setup() {
	return Themecomplete_Extra_Product_Options_Setup::instance();
}

// Setup the plugin.
themecomplete_extra_product_options_setup();
