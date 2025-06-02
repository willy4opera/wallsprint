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
 * FOX - WooCommerce Currency Switcher from realmag777
 * https://codecanyon.net/item/woocommerce-currency-switcher/8085217
 *
 * @package Extra Product Options/Compatibility
 * @version 6.4
 */
final class THEMECOMPLETE_EPO_CP_WOOCS {

	/**
	 * Flag to check in WOOCS is enabled
	 *
	 * @var integer|null|boolean
	 */
	public $is_woocs = false;

	/**
	 * Holds the global $WOOCS variable
	 *
	 * @var mixed
	 */
	public $woocs = false;

	/**
	 * Cache for the default currency
	 *
	 * @var string|false
	 */
	public $default_to_currency = false;

	/**
	 * Cache for the default from currency
	 *
	 * @var string|false
	 */
	public $default_from_currency = false;

	/**
	 * Custom help data
	 * This is used in places where we need to add a custom property
	 *
	 * @var array<mixed>
	 */
	public $data_store = [];

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_CP_WOOCS|null
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return THEMECOMPLETE_EPO_CP_WOOCS
	 * @since 6.0
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
	 * @since 6.0
	 */
	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'plugins_loaded' ], 1 );
	}

	/**
	 * Add compatibility hooks and filters
	 *
	 * @return void
	 * @since 1.0
	 */
	public function add_compatibility() {

		if ( ! $this->is_woocs ) {
			return;
		}

		add_filter( 'wc_epo_enabled_currencies', [ $this, 'wc_epo_enabled_currencies' ], 10, 1 );
		add_filter( 'wc_epo_convert_to_currency', [ $this, 'wc_epo_convert_to_currency' ], 10, 5 );
		add_filter( 'wc_epo_product_price', [ $this, 'wc_epo_product_price' ], 10, 2 );
		add_filter( 'wc_epo_option_price_correction', [ $this, 'wc_epo_option_price_correction' ], 10, 1 );
		add_filter( 'woocs_fixed_raw_woocommerce_price', [ $this, 'woocs_fixed_raw_woocommerce_price' ], 10, 3 );
		add_filter( 'wc_epo_get_current_currency_price', [ $this, 'wc_epo_get_current_currency_price' ], 10, 6 );
		add_filter( 'wc_epo_maybe_get_current_currency_price', [ $this, 'wc_epo_maybe_get_current_currency_price' ], 10, 5 );
		add_filter( 'wc_epo_remove_current_currency_price', [ $this, 'wc_epo_remove_current_currency_price' ], 10, 5 );
		add_filter( 'wc_epo_get_currency_price', [ $this, 'tm_wc_epo_get_currency_price' ], 10, 6 );
		add_filter( 'wc_epo_get_price_html', [ $this, 'wc_epo_get_price_html' ], 10, 2 );
		add_action( 'wc_epo_currency_actions', [ $this, 'wc_epo_currency_actions' ], 10, 3 );
		add_filter( 'wc_epo_script_args', [ $this, 'wc_epo_script_args' ], 10, 1 );
		add_filter( 'tc_get_default_currency', [ $this, 'tc_get_default_currency' ], 10, 1 );
		add_action( 'wc_epo_template_tm_totals', [ $this, 'wc_epo_template_tm_totals' ], 10 );
		add_action( 'wc_epo_get_price_in_currency', [ $this, 'get_price_in_currency' ], 10, 6 );
		add_action( 'wc_epo_price_display_mode_price', [ $this, 'wc_epo_price_display_mode_price' ], 10, 4 );
		add_action( 'wc_epo_price_display_mode_from', [ $this, 'wc_epo_price_display_mode_from' ], 10, 4 );
		add_action( 'wc_epo_price_display_mode_range', [ $this, 'wc_epo_price_display_mode_range' ], 10, 4 );
	}

	/**
	 * Setup initial variables
	 *
	 * @return void
	 * @since 6.0
	 */
	public function plugins_loaded() {
		$this->is_woocs = class_exists( 'WOOCS' );

		if ( $this->is_woocs ) {
			global $WOOCS;// phpcs:ignore WordPress.NamingConventions.ValidVariableName
			$this->woocs = $WOOCS;// phpcs:ignore WordPress.NamingConventions.ValidVariableName
		}

		add_action( 'init', [ $this, 'init' ], 100 );
		add_action( 'wc_epo_init_settings', [ $this, 'woocommerce_order_amount_total' ], 1 );

		$this->add_compatibility();
	}

	/**
	 * Add extra html data attributes
	 *
	 * @return void
	 * @since 5.1
	 */
	public function wc_epo_template_tm_totals() {
		echo 'data-tm-epo-is-woocs="' . esc_attr( (string) $this->is_woocs ) . '" ';
	}

	/**
	 * Get default currency
	 *
	 * @param string $currency The currency.
	 * @return string
	 * @since 5.0.12.11
	 */
	public function tc_get_default_currency( $currency = '' ) {
		$currency = $this->woocs->default_currency;
		return $currency;
	}

	/**
	 * Remove filter
	 *
	 * @return void
	 * @since 6.0
	 */
	public function woocommerce_order_amount_total() {
		remove_filter( 'woocommerce_order_amount_total', [ $this->woocs, 'woocommerce_order_amount_total' ], 999 );
	}

	/**
	 * Setup initial variables
	 *
	 * @return void
	 * @since 1.0
	 */
	public function init() {
		if ( $this->is_woocs && function_exists( 'woocs_convert_fix_price' ) ) {
			remove_filter( 'wcml_raw_price_amount', 'woocs_convert_fix_price' );
		}
	}

	/**
	 * Alter enabled currencies
	 *
	 * @param array<mixed> $currencies Array of currencies.
	 * @return array<mixed>
	 *
	 * @since 6.0
	 */
	public function wc_epo_enabled_currencies( $currencies = [] ) {
		$currencies = is_object( $this->woocs ) && is_callable( [ $this->woocs, 'get_currencies' ] ) ? $this->woocs->get_currencies() : [];

		$enabled_currencies = [];
		if ( $currencies && is_array( $currencies ) ) {
			foreach ( $currencies as $key => $value ) {
				$enabled_currencies[] = $value['name'];
			}
		}

		return $enabled_currencies;
	}

	/**
	 * Add to main JS script arguments
	 *
	 * @param array<mixed> $args Array of arguments.
	 * @return array<mixed>
	 * @since 1.0
	 */
	public function wc_epo_script_args( $args ) {
		if ( isset( $args['product_id'] ) ) {
			$customer_price_format = get_option( 'woocs_customer_price_format', '' );

			if ( ! empty( $customer_price_format ) ) {
				$args['customer_price_format']            = $customer_price_format;
				$args['current_currency']                 = $this->woocs->current_currency;
				$args['customer_price_format_wrap_start'] = '<span class="woocs_price_code" data-product-id="' . $args['product_id'] . '">';
				$args['customer_price_format_wrap_end']   = '</span>';
			}
		}

		return $args;
	}

	/**
	 * Alter product variables in cart
	 *
	 * @param float        $price1 Total price.
	 * @param float        $price2 Option price.
	 * @param array<mixed> $cart_item The cart item.
	 * @return void
	 * @since 1.0
	 */
	public function wc_epo_currency_actions( $price1, $price2, $cart_item ) {
		$price1     = apply_filters( 'wc_epo_convert_to_currency', $price1, $this->woocs->default_currency, $this->woocs->current_currency );
		$product_id = themecomplete_get_id( $cart_item['data'] );

		$this->data_store[ $product_id ]['tc_price1']                     = floatval( $price1 );// option prices.
		$this->data_store[ $product_id ]['tc_price2']                     = floatval( $price2 );
		$this->data_store[ $product_id ]['tm_epo_product_original_price'] = floatval( $cart_item['tm_epo_product_original_price'] );
	}

	/**
	 * Fixed currency support for WOOCS
	 *
	 * @param float $fixed_price The fixed product price.
	 * @param mixed $product The product object.
	 * @param float $main_price The main price.
	 * @return mixed
	 * @since 1.0
	 */
	public function woocs_fixed_raw_woocommerce_price( $fixed_price = 0, $product = false, $main_price = null ) {
		if ( null === $main_price ) {
			if ( ! defined( 'THEMECOMPLETE_CS_ERROR' ) ) {
				define( 'THEMECOMPLETE_CS_ERROR', 1 );
				wc_add_notice( 'You are using an unsupported version of Currency switcher. Prices will not be correct!', 'error' );
			}

			return $fixed_price;
		}
		$product_id = $product->get_id();

		$flag = false;
		if ( 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_override_product_price' ) ) {
			$flag = true;
		} elseif ( '' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_override_product_price' ) ) {
			$tm_meta_cpf = themecomplete_get_post_meta( $product_id, 'tm_meta_cpf', true );
			if ( ! is_array( $tm_meta_cpf ) ) {
				$tm_meta_cpf = [];
			}

			if ( ! empty( $tm_meta_cpf['price_override'] ) ) {
				$flag = true;
			}
		}
		if ( $flag ) {
			return $this->woocs->woocs_exchange_value( $main_price );
		}

		$type = $this->woocs->fixed->get_price_type( $product, $main_price );

		$main_price = floatval( $main_price );

		$get_value = floatval( themecomplete_get_post_meta( $product_id, '_' . $type . '_price', true ) );

		$get_sale_value = floatval( themecomplete_get_post_meta( $product_id, '_sale_price', true ) );

		if ( $main_price === $get_value ) {
			return $fixed_price;
		}

		$option_prices                      = isset( $this->data_store[ $product_id ] ) ? $this->data_store[ $product_id ]['tc_price1'] : 0;
		$original_price_in_current_currency = isset( $this->data_store[ $product_id ] ) ? $this->data_store[ $product_id ]['tm_epo_product_original_price'] : 0;
		$original_price_in_current_currency = floatval( $this->wc_epo_get_current_currency_price( $original_price_in_current_currency ) );

		$new_price = $original_price_in_current_currency + $option_prices;

		if ( $new_price < 0 ) {
			return $fixed_price;
		}

		$fixed_price = $fixed_price + $option_prices;

		return $fixed_price;
	}

	/**
	 * General price manipulations.
	 *
	 * @param string $price_html The price html code.
	 * @param object $product The product object.
	 * @return string
	 * @since 6.4.2
	 */
	public function woocs_price_html_general( $price_html, $product ) {
		if ( is_object( $this->woocs ) && property_exists( $this->woocs, 'current_currency' ) && property_exists( $this->woocs, 'no_cents' ) ) {
			$currencies            = is_callable( [ $this->woocs, 'get_currencies' ] ) ? $this->woocs->get_currencies() : [];
			$customer_price_format = get_option( 'woocs_customer_price_format', '' );

			if ( ! empty( $customer_price_format ) ) {
				$txt        = '<span class="woocs_price_code" data-product-id="' . themecomplete_get_id( $product ) . '">' . $customer_price_format . '</span>';
				$txt        = str_replace( '__PRICE__', $price_html, $txt );
				$price_html = str_replace( '__CODE__', $this->woocs->current_currency, $txt );
			}

			// Hide cents on front as html element.
			if ( ! in_array( $this->woocs->current_currency, $this->woocs->no_cents ) ) { // phpcs:ignore WordPress.PHP.StrictInArray
				if ( 1 === intval( $currencies[ $this->woocs->current_currency ]['hide_cents'] ) ) {
					$price_html = preg_replace( '/\.[0-9][0-9]/u', '', $price_html );
				}
			}
		}

		if ( ! is_string( $price_html ) ) {
			$price_html = '';
		}

		return $price_html;
	}

	/**
	 * Add additional info in price html
	 *
	 * @param string $price_html The price html code.
	 * @param mixed  $from_price The from price in default currency.
	 * @param mixed  $to_price The to price in default currency.
	 * @param object $product The product object.
	 * @return string
	 * @since 6.4.2
	 */
	public function wc_epo_price_display_mode_range( $price_html, $from_price, $to_price, $product ) {
		if ( is_object( $this->woocs ) && method_exists( $this->woocs, 'wc_price' ) && property_exists( $this->woocs, 'current_currency' ) && property_exists( $this->woocs, 'decimal_sep' ) ) {
			$currencies = is_callable( [ $this->woocs, 'get_currencies' ] ) ? $this->woocs->get_currencies() : [];
			$price_html = $this->woocs_price_html_general( $price_html, $product );

			if ( ( get_option( 'woocs_price_info', 0 ) && ! is_admin() ) || isset( $_REQUEST['get_product_price_by_ajax'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$info             = '<ul>';
				$current_currency = $this->woocs->current_currency;
				foreach ( $currencies as $curr ) {
					if ( ! isset( $curr['name'] ) || $curr['name'] === $current_currency ) {
						continue;
					}
					$this->woocs->current_currency = $curr['name'];

					$min_value  = (float) $from_price;
					$max_value  = (float) $to_price;
					$min_value  = number_format( $min_value, 2, $this->woocs->decimal_sep, '' );
					$max_value  = number_format( $max_value, 2, $this->woocs->decimal_sep, '' );
					$var_price  = $this->woocs->wc_price( $min_value, [ 'currency' => $curr['name'] ] );
					$var_price .= ' - ';
					$var_price .= $this->woocs->wc_price( $max_value, [ 'currency' => $curr['name'] ] );
					$info      .= '<li><b>' . $curr['name'] . '</b>: ' . $var_price . '</li>';
				}
				$this->woocs->current_currency = $current_currency;

				$info       .= '</ul>';
				$info        = '<div class="woocs_price_info"><span class="woocs_price_info_icon"></span>' . $info . '</div>';
				$price_html .= $info;
			}
		}

		return $price_html;
	}

	/**
	 * Add additional info in price html
	 *
	 * @param string $price_html The price html code.
	 * @param mixed  $price The price in default currency.
	 * @param mixed  $sale_price The sale price in default currency.
	 * @param object $product The product object.
	 * @return string
	 * @since 6.4.2
	 */
	public function wc_epo_price_display_mode_from( $price_html, $price, $sale_price, $product ) {
		if ( is_object( $this->woocs ) && method_exists( $this->woocs, 'wc_price' ) && property_exists( $this->woocs, 'current_currency' ) && property_exists( $this->woocs, 'decimal_sep' ) ) {
			$currencies = is_callable( [ $this->woocs, 'get_currencies' ] ) ? $this->woocs->get_currencies() : [];
			$price_html = $this->woocs_price_html_general( $price_html, $product );

			if ( ( get_option( 'woocs_price_info', 0 ) && ! is_admin() ) || isset( $_REQUEST['get_product_price_by_ajax'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$info             = '<ul>';
				$current_currency = $this->woocs->current_currency;
				foreach ( $currencies as $curr ) {
					if ( ! isset( $curr['name'] ) || $curr['name'] === $current_currency ) {
						continue;
					}
					$this->woocs->current_currency = $curr['name'];

					$value = $price;
					if ( '' !== $sale_price ) {
						$value = $sale_price;
					}
					$value = (float) $value;
					$value = number_format( $value, 2, $this->woocs->decimal_sep, '' );
					$info .= '<li><b>' . $curr['name'] . '</b>: ' . $this->woocs->wc_price( $value, [ 'currency' => $curr['name'] ] ) . '</li>';
				}
				$this->woocs->current_currency = $current_currency;

				$info       .= '</ul>';
				$info        = '<div class="woocs_price_info"><span class="woocs_price_info_icon"></span>' . $info . '</div>';
				$price_html .= $info;
			}
		}

		return $price_html;
	}

	/**
	 * Add additional info in price html
	 *
	 * @param string $price_html The price html code.
	 * @param mixed  $price The price in default currency.
	 * @param mixed  $sale_price The sale price in default currency.
	 * @param object $product The product object.
	 * @return string
	 * @since 6.4.2
	 */
	public function wc_epo_price_display_mode_price( $price_html, $price, $sale_price, $product ) {
		return $this->wc_epo_price_display_mode_from( $price_html, $price, $sale_price, $product );
	}

	/**
	 * Add additional info in price html
	 *
	 * @param string     $price_html The price html code.
	 * @param WC_Product $product The product object.
	 * @return string
	 * @since 1.0
	 */
	public function wc_epo_get_price_html( $price_html, $product ) {
		$price_html = $this->woocs_price_html_general( $price_html, $product );

		if ( ( get_option( 'woocs_price_info', 0 ) && ! is_admin() ) || isset( $_REQUEST['get_product_price_by_ajax'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$currencies       = is_object( $this->woocs ) && is_callable( [ $this->woocs, 'get_currencies' ] ) ? $this->woocs->get_currencies() : [];
			$info             = '<ul>';
			$current_currency = $this->woocs->current_currency;
			foreach ( $currencies as $curr ) {
				if ( ! isset( $curr['name'] ) || $curr['name'] === $current_currency ) {
					continue;
				}
				$this->woocs->current_currency = $curr['name'];
				$value                         = $product->get_price() * $currencies[ $curr['name'] ]['rate'];
				$value                         = number_format( $value, 2, $this->woocs->decimal_sep, '' );
				if ( 'variable' !== themecomplete_get_product_type( $product ) ) {
					$info .= '<li><b>' . $curr['name'] . '</b>: ' . $this->woocs->wc_price( $value, false, [ 'currency' => $curr['name'] ] ) . '</li>';
				} elseif ( $product instanceof WC_Product_Variable ) {
					$min_value  = $product->get_variation_price( 'min', true ) * $currencies[ $curr['name'] ]['rate'];
					$max_value  = $product->get_variation_price( 'max', true ) * $currencies[ $curr['name'] ]['rate'];
					$min_value  = number_format( $min_value, 2, $this->woocs->decimal_sep, '' );
					$max_value  = number_format( $max_value, 2, $this->woocs->decimal_sep, '' );
					$var_price  = $this->woocs->wc_price( $min_value, [ 'currency' => $curr['name'] ] );
					$var_price .= ' - ';
					$var_price .= $this->woocs->wc_price( $max_value, [ 'currency' => $curr['name'] ] );
					$info      .= '<li><b>' . $curr['name'] . '</b>: ' . $var_price . '</li>';
				}
			}
			$this->woocs->current_currency = $current_currency;

			$info       .= '</ul>';
			$info        = '<div class="woocs_price_info"><span class="woocs_price_info_icon"></span>' . $info . '</div>';
			$price_html .= $info;
		}

		return $price_html;
	}

	/**
	 * Get product price
	 * This filter is currency only used for product prices.
	 *
	 * @param float|string   $price The product price.
	 * @param string|boolean $currency The current to convert the price to.
	 * @since 1.0
	 * @return float|string
	 */
	public function wc_epo_product_price( $price = '', $currency = false ) {

		if ( ! $this->woocs->is_multiple_allowed ) {
			$price = $this->convert_to_currency( $price, $currency );
		}

		return $price;
	}

	/**
	 * Get product price
	 * This filter is currently only used for product prices.
	 *
	 * @param float|string   $price The product price.
	 * @param string|boolean $currency The currency to convert the price to.
	 * @return float|string
	 * @since 6.0
	 */
	private function convert_to_currency( $price = '', $currency = false ) {

		if ( defined( 'WOOCS_VERSION' ) ) {
			if ( is_object( $this->woocs ) && property_exists( $this->woocs, 'current_currency' ) ) {
				$currencies = is_callable( [ $this->woocs, 'get_currencies' ] ) ? $this->woocs->get_currencies() : [];
				if ( ! $currency ) {
					$current_currency = $this->woocs->current_currency;
				} else {
					$current_currency = $currency;
				}
				if ( isset( $currencies[ $current_currency ] ) && isset( $currencies[ $current_currency ]['rate'] ) ) {
					$price = (float) $price * (float) $currencies[ $current_currency ]['rate'];
				}
			}
		}

		return $price;
	}

	/**
	 * Alter option prices in cart
	 *
	 * @param float $price The price to convert.
	 * @return mixed
	 * @since 1.0
	 */
	public function wc_epo_option_price_correction( $price ) {
		return apply_filters( 'wc_epo_remove_current_currency_price', $price, '', null, null, null );
	}

	/**
	 * Get current currency price if multiple price is on
	 *
	 * @param mixed             $price The price to convert.
	 * @param string            $type The option price type.
	 * @param array<mixed>|null $currencies Array of currencies.
	 * @param string|false      $currency The currency to get the price.
	 * @param mixed             $product_price The product price (for percent price type).
	 * @param string|false      $tc_added_in_currency The current currency the product was added in.
	 * @return mixed
	 *
	 * @since 6.0
	 */
	public function wc_epo_maybe_get_current_currency_price( $price = '', $type = '', $currencies = null, $currency = false, $product_price = false, $tc_added_in_currency = false ) {
		if ( ! $this->woocs->is_multiple_allowed ) {
			$price = $this->wc_epo_remove_current_currency_price( $price, $type, $tc_added_in_currency, $tc_added_in_currency, $currencies );
		} else {
			$price = apply_filters( 'wc_epo_get_current_currency_price', $price, $type, $currencies, $currency, $product_price, $tc_added_in_currency );
		}
		return $price;
	}

	/**
	 * Get current currency price
	 *
	 * @param mixed             $price The price to convert.
	 * @param mixed             $type The option price type.
	 * @param array<mixed>|null $currencies Array of currencies.
	 * @param string|false      $currency The currency to get the price.
	 * @param mixed             $product_price The product price (for percent price type).
	 * @param string|false      $tc_added_in_currency The current currency the product was added in.
	 * @return mixed
	 *
	 * @since 6.0
	 */
	public function wc_epo_get_current_currency_price( $price = '', $type = '', $currencies = null, $currency = false, $product_price = false, $tc_added_in_currency = false ) {
		if ( $currency === $tc_added_in_currency ) {
			return $price;
		}
		if ( is_array( $type ) ) {
			$type = '';
		}
		// Check if the price should be processed only once.
		if ( 'math' === $type ) {
			// Replaces any number between curly braces with the current currency.
			$price = preg_replace_callback(
				'/\{(\d+)\}/u',
				function ( $matches ) use( $currency ) {
					return apply_filters( 'wc_epo_get_currency_price', $matches[1], $currency, '' );
				},
				$price
			);
		} elseif ( in_array( (string) $type, [ '', 'fixedcurrenttotal', 'word', 'wordnon', 'char', 'step', 'intervalstep', 'charnofirst', 'charnospaces', 'charnon', 'charnonnospaces', 'fee', 'stepfee', 'subscriptionfee' ], true ) ) {
			if ( is_array( $currencies ) && isset( $currencies[ $this->woocs->current_currency ] ) ) {
				$price = $currencies[ $this->woocs->current_currency ];
			} else {
				$price = $this->convert_to_currency( $price );
			}
		} elseif ( false !== $product_price && false !== $tc_added_in_currency && (string) 'percent' === $type ) {
			if ( is_array( $currencies ) && isset( $currencies[ $this->woocs->current_currency ] ) ) {
				$product_price = $currencies[ $this->woocs->current_currency ];
			} else {
				$product_price = $this->convert_to_currency( $product_price, $tc_added_in_currency );
			}
			$price = floatval( $product_price ) * ( floatval( $price ) / 100 );
		}

		return $price;
	}

	/**
	 * Get current currency price
	 *
	 * @param string            $price The price to convert.
	 * @param string|false      $currency The currency to get the price.
	 * @param string            $price_type The option price type.
	 * @param string|false      $current_currency The current currency.
	 * @param array<mixed>|null $price_per_currencies Array of price per currency.
	 * @param string|null       $key The option key.
	 * @return mixed
	 *
	 * @since 6.0
	 */
	public function tm_wc_epo_get_currency_price( $price = '', $currency = false, $price_type = '', $current_currency = false, $price_per_currencies = null, $key = null ) {
		if ( ! $currency ) {
			return $this->wc_epo_get_current_currency_price( $price, $price_type, null, $currency );
		}
		$tc_get_default_currency = apply_filters( 'tc_get_default_currency', get_option( 'woocommerce_currency' ) );
		if ( $current_currency && $current_currency === $currency && $current_currency === $tc_get_default_currency ) {
			return $price;
		}

		$price = $this->get_price_in_currency( $price, $currency, null, $price_per_currencies, $price_type, $key );

		return $price;
	}

	/**
	 * Remove current currency price
	 *
	 * @param mixed             $price The price to convert.
	 * @param string            $type The option price type.
	 * @param string|false      $to_currency The currency to convert to.
	 * @param string|false      $from_currency The currency to convert from.
	 * @param array<mixed>|null $currencies Array of currencies.
	 * @return mixed
	 * @since 6.0
	 */
	public function wc_epo_remove_current_currency_price( $price = '', $type = '', $to_currency = false, $from_currency = false, $currencies = null ) {
		$currencies       = is_object( $this->woocs ) && is_callable( [ $this->woocs, 'get_currencies' ] ) ? $this->woocs->get_currencies() : [];
		$current_currency = $this->woocs->current_currency;
		if ( $this->woocs->default_currency !== $current_currency && ! empty( $currencies[ $current_currency ]['rate'] ) ) {
			$price = (float) $price / $currencies[ $current_currency ]['rate'];
		}

		return $price;
	}

	/**
	 * Convert to currency
	 *
	 * @param mixed        $price The price to convert.
	 * @param string|false $from_currency The currency to convert from.
	 * @param string|false $to_currency The currency to convert to.
	 * @param boolean      $force If we want to force the conversion.
	 * @return mixed
	 *
	 * @since 6.0
	 */
	public function wc_epo_convert_to_currency( $price = '', $from_currency = false, $to_currency = false, $force = false ) {
		if ( ! $from_currency || ! $to_currency || $from_currency === $to_currency ) {
			return $price;
		}
		if ( is_object( $this->woocs ) && property_exists( $this->woocs, 'is_multiple_allowed' ) ) {
			if ( $this->woocs->is_multiple_allowed && ! $force ) {
				return $price;
			}
			$currencies       = is_callable( [ $this->woocs, 'get_currencies' ] ) ? $this->woocs->get_currencies() : [];
			$current_currency = $from_currency;
			if ( ! empty( $currencies[ $to_currency ]['rate'] ) && ! empty( $currencies[ $from_currency ]['rate'] ) ) {
				$price = (float) $price * ( $currencies[ $to_currency ]['rate'] / $currencies[ $from_currency ]['rate'] );
			}
		}

		return $price;
	}

	/**
	 * Helper function
	 *
	 * @return string
	 * @see get_price_in_currency
	 */
	public function get_default_from_currency() {
		if ( false === $this->default_from_currency ) {
			$this->default_from_currency = get_option( 'woocommerce_currency' );
		}

		return $this->default_from_currency;
	}

	/**
	 * Helper function
	 *
	 * @return string
	 * @see get_price_in_currency
	 */
	public function get_default_to_currency() {
		if ( false === $this->default_to_currency ) {
			$this->default_to_currency = themecomplete_get_woocommerce_currency();
		}

		return $this->default_to_currency;
	}

	/**
	 * Return prices converted to the active currency
	 *
	 * @param mixed        $price The source price.
	 * @param string       $to_currency The target currency. If empty, the active currency
	 *                     will be taken.
	 * @param string       $from_currency The source currency. If empty, WooCommerce base
	 *                     currency will be taken.
	 * @param array<mixed> $currencies Array of currencies.
	 * @param string       $type The price type.
	 * @param string       $key The option key.
	 *
	 * @return mixed The price converted from source to destination currency.
	 */
	public function get_price_in_currency( $price, $to_currency = null, $from_currency = null, $currencies = null, $type = null, $key = null ) {

		if ( empty( $from_currency ) ) {
			$from_currency = $this->get_default_from_currency();
		}
		if ( empty( $to_currency ) ) {
			$to_currency = $this->get_default_to_currency();
		}
		if ( $from_currency === $to_currency ) {
			return $price;
		}
		if ( null !== $type && in_array( $type, [ '', 'word', 'wordnon', 'char', 'step', 'intervalstep', 'charnofirst', 'charnospaces', 'charnon', 'charnonnospaces', 'fee', 'stepfee', 'subscriptionfee' ], true ) && is_array( $currencies ) && isset( $currencies[ $to_currency ] ) ) {
			$v = $currencies[ $to_currency ];
			if ( null !== $key && isset( $v[ $key ] ) ) {
				$v = $v[ $key ];
			}
			if ( is_array( $v ) ) {
				$v = array_values( $v );
				$v = $v[0];
				if ( is_array( $v ) ) {
					$v = array_values( $v );
					$v = $v[0];
				}
			}

			if ( '' !== $v ) {
				return $v;
			}
		}

		return apply_filters( 'wc_epo_cs_convert', apply_filters( 'woocs_exchange_value', $price ), $from_currency, $to_currency );
	}
}
