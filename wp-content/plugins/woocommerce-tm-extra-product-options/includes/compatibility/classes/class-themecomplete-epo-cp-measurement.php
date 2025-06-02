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
 * Measurement Price Calculator
 * https://woocommerce.com/products/measurement-price-calculator/
 *
 * @package Extra Product Options/Compatibility
 * @version 6.4
 */
final class THEMECOMPLETE_EPO_CP_Measurement {

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_CP_Measurement|null
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return THEMECOMPLETE_EPO_CP_Measurement
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
		add_action( 'plugins_loaded', [ $this, 'add_compatibility2' ], 2 );
		add_action( 'plugins_loaded', [ $this, 'add_compatibility' ] );
		add_action( 'init', [ $this, 'template_redirect' ], 11 );
		add_action( 'template_redirect', [ $this, 'template_redirect' ], 11 );
	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @return void
	 * @since 1.0
	 */
	public function add_compatibility() {
		if ( ! class_exists( 'WC_Measurement_Price_Calculator' ) ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', [ $this, 'wp_enqueue_scripts' ], 11 );

		add_filter( 'wc_epo_add_cart_item_original_price', [ $this, 'wc_epo_add_cart_item_original_price' ], 10, 2 );
		add_filter( 'wc_epo_option_price_correction', [ $this, 'wc_epo_option_price_correction' ], 10, 2 );
		add_filter( 'wc_epo_price_on_cart', [ $this, 'wc_epo_price_on_cart' ], 10, 2 );
	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @return void
	 * @since 4.9.12
	 */
	public function add_compatibility2() {
		if ( class_exists( 'WC_Measurement_Price_Calculator' ) || class_exists( 'WC_Measurement_Price_Calculator_Loader' ) ) {
			add_filter( 'wc_epo_get_settings', [ $this, 'wc_epo_get_settings' ], 10, 1 );
			add_filter( 'tm_epo_settings_headers', [ $this, 'tm_epo_settings_headers' ], 10, 1 );
			add_filter( 'tm_epo_settings_settings', [ $this, 'tm_epo_settings_settings' ], 10, 1 );
		}
	}

	/**
	 * Enqueue scripts
	 *
	 * @return void
	 * @since 1.0
	 */
	public function wp_enqueue_scripts() {
		if ( THEMECOMPLETE_EPO()->can_load_scripts() ) {
			wp_enqueue_script( 'themecomplete-comp-measurement', THEMECOMPLETE_EPO_COMPATIBILITY_URL . 'assets/js/cp-measurement.js', [ 'jquery' ], THEMECOMPLETE_EPO_VERSION, true );
			$args = [
				'wc_measurement_qty_multiplier' => 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_measurement_calculate_mode' ) ? 1 : 0,
				'wc_measurement_divide'         => 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_measurement_divide' ) ? 1 : 0,
			];
			wp_localize_script( 'themecomplete-comp-measurement', 'TMEPOMEASUREMENTJS', $args );
		}
	}

	/**
	 * Disable EPO price filters
	 *
	 * @return void
	 * @since 1.0
	 */
	public function template_redirect() {
		remove_filter( 'woocommerce_get_price_html', [ THEMECOMPLETE_EPO(), 'get_price_html' ], 10 );
		remove_filter( 'woocommerce_product_get_price', [ THEMECOMPLETE_EPO(), 'tm_woocommerce_get_price' ], 1 );
	}

	/**
	 * Add plugin setting (header)
	 *
	 * @param array<mixed> $headers Array of settings.
	 * @return array<mixed>
	 * @since 1.0
	 */
	public function tm_epo_settings_headers( $headers = [] ) {
		$headers['measurement'] = [ 'tcfa tcfa-ruler-combined', esc_html__( 'WooCommerce Measurement Calculator', 'woocommerce-tm-extra-product-options' ) ];

		return $headers;
	}

	/**
	 * Add plugin setting (setting)
	 *
	 * @param array<mixed> $settings Array of settings.
	 * @return array<mixed>
	 * @since 1.0
	 */
	public function tm_epo_settings_settings( $settings = [] ) {
		$label                   = esc_html__( 'WooCommerce Measurement Calculator', 'woocommerce-tm-extra-product-options' );
		$settings['measurement'] = [
			[
				'type'  => 'tm_title',
				'id'    => 'epo_page_options',
				'title' => $label,
			],
			[
				'title'   => esc_html__( 'Multiply options cost by area', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'Enabling this will multiply the options price by the calculated area.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_measurement_calculate_mode',
				'default' => 'no',
				'class'   => 'tcdisplay',
				'type'    => 'checkbox',
			],
			[
				'title'   => esc_html__( 'Divide price with measurement', 'woocommerce-tm-extra-product-options' ),
				'desc'    => esc_html__( 'This will divide the original price with the needed measurement.', 'woocommerce-tm-extra-product-options' ),
				'id'      => 'tm_epo_measurement_divide',
				'default' => 'no',
				'class'   => 'tcdisplay',
				'type'    => 'checkbox',
			],

			[
				'type' => 'tm_sectionend',
				'id'   => 'epo_page_options',
			],
		];

		return $settings;
	}

	/**
	 * Add setting in main THEMECOMPLETE_EPO class
	 *
	 * @param array<mixed> $settings Array of settings.
	 * @return array<mixed>
	 * @since 1.0
	 */
	public function wc_epo_get_settings( $settings = [] ) {
		if ( class_exists( 'WC_Measurement_Price_Calculator' ) ) {
			$settings['tm_epo_measurement_calculate_mode'] = 'no';
			$settings['tm_epo_measurement_divide']         = 'no';
		}

		return $settings;
	}

	/**
	 * Alter price on cart
	 *
	 * @param float|string $price The price to alter.
	 * @param array<mixed> $cart_item The cart item.
	 * @return float|string
	 * @since 1.0
	 */
	public function wc_epo_price_on_cart( $price = '', $cart_item = [] ) {
		if ( 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_measurement_calculate_mode' ) ) {
			if ( is_array( $cart_item ) && isset( $cart_item['pricing_item_meta_data'] ) && ! empty( $cart_item['pricing_item_meta_data']['_quantity'] ) ) {
				$new_quantity   = (float) $cart_item['quantity'] / $cart_item['pricing_item_meta_data']['_quantity'];
				$original_price = (float) $price;
				$original_price = $original_price * $new_quantity;

				$price = $original_price;
			}
		}

		return $price;
	}

	/**
	 * Alter option prices
	 *
	 * @param float|string $price The price to alter.
	 * @param array<mixed> $cart_item The cart item.
	 * @return float|string
	 * @since 1.0
	 */
	public function wc_epo_option_price_correction( $price = '', $cart_item = [] ) {

		if ( isset( $cart_item['pricing_item_meta_data'] ) && ! empty( $cart_item['pricing_item_meta_data']['_measurement_needed'] ) && 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_measurement_divide' ) ) {
			$price = floatval( $price ) / floatval( $cart_item['pricing_item_meta_data']['_measurement_needed'] );
		}

		if ( 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_measurement_calculate_mode' ) ) {

			if ( is_array( $cart_item ) && isset( $cart_item['pricing_item_meta_data'] ) && ! empty( $cart_item['pricing_item_meta_data']['_measurement_needed'] ) ) {
				$price = floatval( $price ) * floatval( $cart_item['pricing_item_meta_data']['_measurement_needed'] );
			}
		}

		return $price;
	}

	/**
	 * Set original price
	 *
	 * @param float|string $price The price to alter.
	 * @param array<mixed> $cart_item The cart item.
	 * @return float|string
	 * @since 1.0
	 */
	public function wc_epo_add_cart_item_original_price( $price = '', $cart_item = [] ) {

		if ( isset( $cart_item['pricing_item_meta_data'] ) && isset( $cart_item['pricing_item_meta_data']['_price'] ) ) {
			$price = floatval( $cart_item['pricing_item_meta_data']['_price'] );
			if ( ! empty( $cart_item['pricing_item_meta_data']['_measurement_needed'] ) && 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_measurement_divide' ) ) {
				$price = floatval( $price ) / floatval( $cart_item['pricing_item_meta_data']['_measurement_needed'] );
			}
		}

		return $price;
	}
}
