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
 * Fancy Product Designer
 * https://codecanyon.net/item/fancy-product-designer-woocommercewordpress/6318393
 *
 * @package Extra Product Options/Compatibility
 * @version 6.4
 */
final class THEMECOMPLETE_EPO_CP_FPD {

	/**
	 * URL cache
	 *
	 * @var string
	 * @since 6.4.7
	 */
	private $url_cache = '';

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_CP_FPD|null
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return THEMECOMPLETE_EPO_CP_FPD
	 * @since 1.0
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
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'add_compatibility' ] );
	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @return void
	 * @since 1.0
	 */
	public function add_compatibility() {
		if ( ! class_exists( 'Fancy_Product_Designer' ) ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ], 4 );
		add_filter( 'woocommerce_add_cart_item_data', [ $this, 'woocommerce_add_cart_item_data' ], 10, 1 );
		add_filter( 'wc_epo_get_default_value_post_data', [ $this, 'wc_epo_get_default_value_post_data' ], 10, 1 );
	}

	/**
	 * Reset posted data
	 *
	 * @param array<mixed> $post_data The posted data.
	 * @return array<mixed>
	 * @since 6.4.7
	 */
	public function wc_epo_get_default_value_post_data( $post_data = [] ) {
		if ( isset( $_GET['cart_item_key'] ) ) {
			$cart      = WC()->cart->get_cart();
			$cart_item = WC()->cart->get_cart_item( sanitize_key( $_GET['cart_item_key'] ) );
			if ( ! empty( $cart_item ) ) {
				if ( isset( $cart_item['fpd_data'] ) ) {
					if ( isset( $cart_item['quantity'] ) ) {
						if ( isset( $post_data['quantity'] ) ) {
							unset( $post_data['quantity'] );
						}
					}
				}
			}
		}
		return $post_data;
	}

	/**
	 * Edit cart functionality
	 *
	 * @param array<mixed> $cart_item_meta The cart item meta.
	 * @return array<mixed>
	 * @since 5.0
	 */
	public function woocommerce_add_cart_item_data( $cart_item_meta ) {
		unset( $cart_item_meta['fpd_data']['fpd_remove_cart_item'] );
		return $cart_item_meta;
	}

	/**
	 * Enqueue scripts
	 *
	 * @return void
	 * @since 1.0
	 */
	public function wp_enqueue_scripts() {
		if ( THEMECOMPLETE_EPO()->can_load_scripts() ) {
			wp_enqueue_script( 'themecomplete-comp-fpd', THEMECOMPLETE_EPO_COMPATIBILITY_URL . 'assets/js/cp-fpd.js', [ 'jquery' ], THEMECOMPLETE_EPO_VERSION, true );
		}
	}
}
