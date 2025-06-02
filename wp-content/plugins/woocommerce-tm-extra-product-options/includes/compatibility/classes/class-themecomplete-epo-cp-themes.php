<?php
/**
 * Compatibility class
 *
 * @package Extra Product Options/Compatibility
 * @version 6.4
 */

defined( 'ABSPATH' ) || exit;

/**
 * Compatibility class
 *
 * This class is responsible for providing compatibility with
 * various themes
 *
 * @package Extra Product Options/Compatibility
 * @version 6.4
 */
final class THEMECOMPLETE_EPO_CP_Themes {

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_CP_Themes|null
	 * @since 5.0.12.4
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return THEMECOMPLETE_EPO_CP_Themes
	 * @since 5.0.12.4
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class Constructor
	 *
	 * @since 5.0.12.4
	 */
	public function __construct() {
		add_action( 'wp', [ $this, 'add_compatibility' ] );
	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @return void
	 * @since 5.0.12.4
	 */
	public function add_compatibility() {
		if ( defined( 'WOODMART_SLUG' ) ) {
			add_action( 'woodmart_after_footer', [ $this, 'woodmart_after_footer' ], 998 );
			add_action( 'woodmart_after_footer', [ $this, 'woodmart_after_footer2' ], 1000 );
			add_action( 'woodmart_before_wp_footer', [ $this, 'woodmart_after_footer' ], 998 );
			add_action( 'woodmart_before_wp_footer', [ $this, 'woodmart_after_footer2' ], 1000 );
			add_action( 'wp_enqueue_scripts', [ $this, 'woodmart_wp_enqueue_scripts' ], 11 );
		}
	}

	/**
	 * Woodmart sticky add to cart
	 *
	 * @return void
	 * @since 5.0.12.4
	 */
	public function woodmart_after_footer() {
		THEMECOMPLETE_EPO_DISPLAY()->block_epo = true;
	}

	/**
	 * Woodmart sticky add to cart
	 *
	 * @return void
	 * @since 5.0.12.4
	 */
	public function woodmart_after_footer2() {
		THEMECOMPLETE_EPO_DISPLAY()->block_epo = false;
	}

	/**
	 * Woodmart sticky add to cart
	 *
	 * @return void
	 * @since 5.0.12.4
	 */
	public function woodmart_wp_enqueue_scripts() {
		if ( THEMECOMPLETE_EPO()->can_load_scripts() ) {
			wp_enqueue_script( 'themecomplete-comp-woodmart', THEMECOMPLETE_EPO_COMPATIBILITY_URL . 'assets/js/cp-woodmart.js', [ 'jquery' ], THEMECOMPLETE_EPO_VERSION, true );
		}
	}
}
