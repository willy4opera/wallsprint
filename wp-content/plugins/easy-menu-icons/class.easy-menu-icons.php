<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

define( 'EMICONS_ID', 5671 );

class EMICONS {
	
    private static $_instance = null;

	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @static
	 *
	 * @return Elementor_Test_Extension An instance of the class.
	 */
	public static function instance() {

		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;

	}

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'i18n' ] );
		add_action( 'plugins_loaded', [ $this, 'init' ] );
	}

	/**
	 * Load Textdomain
	 *
	 * Load plugin localization files.
	 *
	 * Fired by `init` action hook.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function i18n() {
		load_plugin_textdomain( 'easy-menu-icons' );
	}

	/**
	 * Initialize the plugin
	 *
	 * Load the plugin only after Elementor (and other plugins) are loaded.
	 * Checks for basic plugin requirements, if one check fail don't continue,
	 * if all check have passed load the files required to run the plugin.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function init() {


		// Add Plugin actions
		add_filter( 'plugin_action_links_' . EMICONS_PLUGIN_BASE, [ $this, 'emicons_plugin_action_links' ], 10, 4 );

	}

	function emicons_plugin_action_links( $plugin_actions, $plugin_file, $plugin_data, $context ) {

		$new_actions = array();
		$new_actions['emicons_plugin_actions_setting'] = '<a href="'.admin_url( 'options-general.php?page=emicons-menu' ).'">Settings</a>';
		$new_actions['emicons_plugin_actions_upgrade'] = sprintf(
			__( '<a href="%s" style="color: #39b54a; font-weight: bold;" target="_blank">Upgrade to Pro</a>', 'easy-menu-icons' ),
			esc_url( 'https://themewant.com/downloads/easy-menu-icons-pro/' )
		);
		return array_merge( $new_actions, $plugin_actions );

	}



    public function init_widgets() {


		

	}


}