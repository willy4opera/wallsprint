<?php
/**
 * Extra Product Options main class
 *
 * @package Extra Product Options/Classes
 * @version 6.4
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extra Product Options main class
 *
 * This class is responsible for displaying the Extra Product Options on the frontend.
 *
 * @package Extra Product Options/Classes
 * @author  ThemeComplete
 * @version 6.4
 */
final class THEMECOMPLETE_Extra_Product_Options {

	/**
	 * Holds the current post id
	 *
	 * @var integer|boolean
	 */
	private $postid_pre = false;

	/**
	 * Helper for determining various conditionals
	 *
	 * @var array<bool>
	 */
	public $wc_vars = [
		'is_product'             => false,
		'is_shop'                => false,
		'is_product_category'    => false,
		'is_product_taxonomy'    => false,
		'is_product_tag'         => false,
		'is_cart'                => false,
		'is_checkout'            => false,
		'is_account_page'        => false,
		'is_ajax'                => false,
		'is_page'                => false,
		'is_order_received_page' => false,
	];

	/**
	 * Product custom settings
	 *
	 * @var array<mixed>
	 */
	public $tm_meta_cpf = [];

	/**
	 * Flag for loading scripts
	 *
	 * @var array<mixed>
	 */
	public $hasoptions = [];

	/**
	 * Product custom settings options
	 *
	 * @var array<mixed>
	 */
	public $meta_fields = [
		'exclude'                     => '',
		'price_override'              => '',
		'override_display'            => '',
		'override_final_total_box'    => '',
		'override_show_options_total' => '',
		'override_show_final_total'   => '',
		'override_enabled_roles'      => '',
		'override_disabled_roles'     => '',
	];

	/**
	 * Cache for all the extra options
	 *
	 * @var array<mixed>
	 */
	private $cpf = [];

	/**
	 * Options cache
	 *
	 * @var array<mixed>
	 */
	private $cpf_single = [];
	/**
	 * Options cache for prices
	 *
	 * @var array<mixed>
	 */
	private $cpf_single_epos_prices = [];
	/**
	 * Options cache for the variation element id
	 *
	 * @var array<mixed>
	 */
	private $cpf_single_variation_element_id = [];
	/**
	 * Options cache for the variation section id
	 *
	 * @var array<mixed>
	 */
	private $cpf_single_variation_section_id = [];

	/**
	 * Holds the upload directory for the upload element
	 *
	 * @var string
	 */
	public $upload_dir = '/extra_product_options/';

	/**
	 * Holds the upload files objects
	 *
	 * @var array<mixed>
	 */
	private $upload_object = [];

	/**
	 * Replacement name for cart fee fields
	 *
	 * @var string
	 */
	public $cart_fee_name = 'tmcartfee_';
	/**
	 * Replacement class name for cart fee fields
	 *
	 * @var string
	 */
	public $cart_fee_class = 'tmcp-fee-field';

	/**
	 * Array of element types that get posted
	 *
	 * @var array<string>
	 */
	public $element_post_types = [];

	/**
	 * Holds builder element attributes
	 *
	 * @var array<mixed>
	 */
	private $tm_original_builder_elements = [];

	/**
	 * Holds modified builder element attributes
	 *
	 * @var array<mixed>
	 */
	public $tm_builder_elements = [];

	/**
	 * Holds the cart key when editing a product in the cart
	 * This isn't in our cart class becuase it needed to be initialized
	 * before the plugins_loaded hook.
	 *
	 * @var string
	 */
	public $cart_edit_key = '';

	/**
	 * Containes current option features
	 *
	 * @var array<string>
	 */
	public $current_option_features = [];

	/**
	 * Enable/disable flag for outputing plugin specific classes
	 * to the post_class filter
	 *
	 * @var boolean
	 */
	private $tm_related_products_output = true;

	/**
	 * Enable/disable flag for checking if we are in related/upsells
	 *
	 * @var boolean
	 */
	private $in_related_upsells = false;

	/**
	 * Cart edit key
	 *
	 * @var string
	 */
	public $cart_edit_key_var = 'tm_cart_item_key';

	/**
	 * Cart edit key alternative
	 *
	 * @var string
	 */
	public $cart_edit_key_var_alt = 'tc_cart_edit_key';

	/**
	 * Contains min/max product option infomation
	 *
	 * @var array<mixed>
	 */
	public $product_options_minmax = [];

	/**
	 * Current free text replacement
	 *
	 * @var string
	 */
	public $current_free_text = '';

	/**
	 * Current free text replacement for associated products
	 *
	 * @var string
	 */
	public $assoc_current_free_text = '';

	/**
	 * Flag to check if we are in the product shortcode
	 *
	 * @var boolean
	 */
	public $is_in_product_shortcode;

	/**
	 * Flag to check if we are in a product loop
	 *
	 * @var boolean
	 */
	public $is_in_product_loop;

	/**
	 * Flag to fix several issues when the woocommerce_get_price hook
	 * isn't being used correct by themes or other plugins.
	 *
	 * @var integer
	 */
	private $tm_woocommerce_get_price_flag = 0;

	/**
	 * If the current product is a composite product
	 *
	 * @var boolean
	 */
	public $is_bto = false;

	/**
	 * If we are in the associated product options
	 *
	 * @var boolean
	 */
	public $is_inline_epo = false;

	/**
	 * If we are in an associated product
	 *
	 * @var boolean
	 */
	public $is_associated = false;

	/**
	 * If associated product is priced individually
	 *
	 * @var integer|null
	 */
	public $associated_per_product_pricing = null;

	/**
	 * Associated product type
	 *
	 * @var string|false
	 */
	public $associated_type = false;

	/**
	 * The element id of the associated product
	 *
	 * @var string|false
	 */
	public $associated_element_uniqid = false;

	/**
	 * The associated product counter when adding more than one product
	 * in the same product element.
	 *
	 * @var integer|false
	 */
	public $associated_product_counter = false;

	/**
	 * The associated product form prefix when adding more than one product
	 * in the same product element.
	 *
	 * @var string|false
	 */
	public $associated_product_formprefix = false;

	/**
	 * Holds all of the lookup tables.
	 *
	 * @var false|array<mixed>
	 */
	public $lookup_tables = false;

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_Extra_Product_Options|null
	 */
	protected static $instance = null;

	/**
	 * Main THEMECOMPLETE_Extra_Product_Options Instance
	 *
	 * Ensures only one instance of THEMECOMPLETE_Extra_Product_Options is loaded or can be loaded
	 *
	 * @since 1.0
	 * @static
	 * @see   THEMECOMPLETE_EPO()
	 * @return THEMECOMPLETE_Extra_Product_Options - Main instance
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
		$this->wc_vars['is_ajax'] = wp_doing_ajax();

		$this->is_bto = false;

		$this->cart_edit_key_var     = apply_filters( 'wc_epo_cart_edit_key_var', 'tm_cart_item_key' );
		$this->cart_edit_key_var_alt = apply_filters( 'wc_epo_cart_edit_key_var_alt', 'tc_cart_edit_key' );
		$this->cart_edit_key         = '';

		if ( isset( $_REQUEST[ $this->cart_edit_key_var ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$cart_edit_key       = filter_var( wp_unslash( $_REQUEST[ $this->cart_edit_key_var ] ), FILTER_SANITIZE_SPECIAL_CHARS ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$this->cart_edit_key = false !== $cart_edit_key ? $cart_edit_key : '';
		} elseif ( isset( $_REQUEST[ $this->cart_edit_key_var_alt ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$cart_edit_key       = filter_var( wp_unslash( $_REQUEST[ $this->cart_edit_key_var_alt ] ), FILTER_SANITIZE_SPECIAL_CHARS ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$this->cart_edit_key = false !== $cart_edit_key ? $cart_edit_key : '';
		} elseif ( isset( $_REQUEST['update-composite'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$cart_edit_key       = filter_var( wp_unslash( $_REQUEST['update-composite'] ), FILTER_SANITIZE_SPECIAL_CHARS ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$this->cart_edit_key = false !== $cart_edit_key ? $cart_edit_key : '';
		}

		// Add compatibility actions and filters with other plugins and themes.
		THEMECOMPLETE_EPO_COMPATIBILITY();

		add_action( 'init', [ $this, 'init_loaded' ], 3 );
		add_action( 'init', [ $this, 'plugin_loaded' ], 4 );
		add_action( 'init', [ $this, 'tm_epo_add_elements' ], 12 );
	}

	/**
	 * Conditional logic (checks if an element is visible)
	 * Compatibility with older versions or Extra Checkout Options.
	 *
	 * @param array<mixed> $element The element array.
	 * @param array<mixed> $section The section array.
	 * @param array<mixed> $sections The sections array.
	 * @param string       $form_prefix The form prefix.
	 * @return boolean
	 * @since 1.0
	 */
	public function is_visible( $element = [], $section = [], $sections = [], $form_prefix = '' ) {
		return THEMECOMPLETE_EPO_CONDITIONAL_LOGIC()->is_visible( $element, $section, $sections, $form_prefix );
	}

	/**
	 * Handles the display of builder sections
	 *
	 * @param boolean $inline If options are displayed inline (associated product).
	 * @since 5.0
	 * @return void
	 */
	public function set_inline_epo( $inline = false ) {
		$this->is_inline_epo = $inline;
	}

	/**
	 * Adds additional builder elements from 3rd party plugins
	 *
	 * @since 1.0
	 * @return void
	 */
	public function tm_epo_add_elements() {
		do_action( 'tm_epo_register_addons' );

		$this->tm_original_builder_elements = THEMECOMPLETE_EPO_BUILDER()->get_elements();

		if ( is_array( $this->tm_original_builder_elements ) ) {
			foreach ( $this->tm_original_builder_elements as $key => $value ) {
				if ( 'post' === $value->is_post ) {
					$this->element_post_types[] = $value->post_name_prefix;
				}

				if ( 'post' === $value->is_post || 'dynamic' === $value->is_post || 'display' === $value->is_post ) {
					$this->tm_builder_elements[ $value->post_name_prefix ] = $value;
				}
			}
		}
	}

	/**
	 * Setup the plugin
	 *
	 * @since 1.0
	 * @return void
	 */
	public function init_loaded() {
		$this->get_plugin_settings();
		THEMECOMPLETE_EPO_ORDER();
	}

	/**
	 * Setup the plugin
	 *
	 * @since 1.0
	 * @return void
	 */
	public function plugin_loaded() {
		if ( ! wp_doing_ajax() && is_admin() ) {
			return;
		}

		$this->get_override_settings();
		$this->add_plugin_actions();
		THEMECOMPLETE_EPO_SCRIPTS();
		THEMECOMPLETE_EPO_ACTIONS();
		THEMECOMPLETE_EPO_DISPLAY();
		THEMECOMPLETE_EPO_CART();

		THEMECOMPLETE_EPO_ASSOCIATED_PRODUCTS();
	}

	/**
	 * Gets all of the plugin settings
	 *
	 * @since 1.0
	 * @return void
	 */
	public function get_plugin_settings() {
		THEMECOMPLETE_EPO_DATA_STORE()->init();
	}

	/**
	 * Gets custom settings for the current product
	 *
	 * @since 1.0
	 * @return void
	 */
	public function get_override_settings() {
		foreach ( $this->meta_fields as $key => $value ) {
			$this->tm_meta_cpf[ $key ] = $value;
		}
	}

	/**
	 * Add required actions and filters
	 *
	 * @since 1.0
	 * @return void
	 */
	public function add_plugin_actions() {
		// Initialize custom product settings.
		if ( $this->is_quick_view() ) {
			add_action( 'init', [ $this, 'init_settings' ] );
		} elseif ( $this->is_enabled_shortcodes() ) {
			add_action( 'init', [ $this, 'init_settings_pre' ] );
		} else {
			add_action( 'template_redirect', [ $this, 'init_settings' ] );
		}

		add_action( 'template_redirect', [ $this, 'init_vars' ], 1 );

		// Force Select Options.
		add_filter( 'woocommerce_product_add_to_cart_url', [ $this, 'add_to_cart_url' ], 50, 1 );
		add_action( 'woocommerce_product_add_to_cart_text', [ $this, 'add_to_cart_text' ], 10, 1 );
		add_filter( 'woocommerce_cart_redirect_after_error', [ $this, 'woocommerce_cart_redirect_after_error' ], 50, 2 );

		// Enable shortcodes for element labels.
		add_filter( 'woocommerce_tm_epo_option_name', [ $this, 'tm_epo_option_name' ], 10, 5 );

		// Add custom class to product div used to initialize the plugin JavaScript.
		add_filter( 'post_class', [ $this, 'tm_post_class' ] );
		add_filter( 'body_class', [ $this, 'tm_body_class' ] );

		// Helper to flag various page positions.
		add_filter( 'woocommerce_related_products_columns', [ $this, 'tm_woocommerce_related_products_args' ], 10, 1 );
		add_action( 'woocommerce_before_single_product', [ $this, 'tm_enable_post_class' ], 1 );
		add_action( 'woocommerce_after_single_product', [ $this, 'tm_enable_post_class' ], 1 );
		add_action( 'woocommerce_upsells_orderby', [ $this, 'tm_woocommerce_related_products_args' ], 10, 1 );
		add_action( 'woocommerce_after_single_product_summary', [ $this, 'tm_woocommerce_after_single_product_summary' ], 99999 );

		// Image filter.
		add_filter( 'tm_image_url', [ $this, 'tm_image_url' ] );

		// Alter the price filter.
		if ( 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_add_product_price_check' ) ) {
			add_filter( 'woocommerce_product_get_price', [ $this, 'woocommerce_product_get_price' ], 1, 2 );
		}

		// Alter product display price to include possible option pricing.
		if ( ! is_admin() && 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_include_possible_option_pricing' ) ) {
			add_filter( 'woocommerce_product_get_price', [ $this, 'tm_woocommerce_get_price' ], 2, 2 );
		}
		if ( ! is_admin() && 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_use_from_on_price' ) ) {
			add_filter( 'woocommerce_show_variation_price', [ $this, 'tm_woocommerce_show_variation_price' ], 50, 3 );
			if ( 'no' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_include_possible_option_pricing' ) ) {
				add_filter( 'woocommerce_get_variation_price_html', [ $this, 'tm_get_price_html' ], 11, 2 );
				add_filter( 'woocommerce_get_price_html', [ $this, 'tm_get_price_html' ], 11, 2 );
			}
		}

		// Override the minimum characters of text fields globally.
		add_filter( 'wc_epo_global_min_chars', [ $this, 'wc_epo_global_min_chars' ], 10, 2 );
		// Override the maximum characters of text fields globally.
		add_filter( 'wc_epo_global_max_chars', [ $this, 'wc_epo_global_max_chars' ], 10, 2 );

		if ( 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_no_upload_to_png' ) ) {
			add_filter( 'wc_epo_no_upload_to_png', '__return_false' );
		}

		// Alter generated Product structured data.
		add_filter( 'woocommerce_structured_data_product_offer', [ $this, 'woocommerce_structured_data_product_offer' ], 10, 2 );

		// Enable shortcodes in options strings.
		if ( 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_enable_data_shortcodes' ) ) {
			add_filter( 'wc_epo_kses', [ $this, 'wc_epo_kses' ], 10, 3 );
			add_filter( 'wc_epo_label_in_cart', [ $this, 'wc_epo_label_in_cart' ], 10, 1 );
		}

		// Enable shortcodes on prices.
		add_filter( 'wc_epo_apply_discount', [ $this, 'enable_shortcodes' ], 10, 1 );
		// Enable shortcodes on various properties.
		add_filter( 'wc_epo_enable_shortocde', [ $this, 'enable_shortcodes' ], 10, 1 );

		// Set the flag for the product loop.
		add_action( 'woocommerce_before_shop_loop_item', [ $this, 'woocommerce_before_shop_loop_item' ], 0 );
		add_action( 'woocommerce_after_shop_loop_item', [ $this, 'woocommerce_after_shop_loop_item' ], 999999 );

		// Change prices in the product loop.
		add_filter( 'woocommerce_get_price_html', [ $this, 'woocommerce_get_price_html' ], 999999, 2 );

		// Trim zeros in prices.
		if ( 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_trim_zeros' ) ) {
			add_filter( 'woocommerce_price_trim_zeros', '__return_true' );
		}

		// Add the extra weight from addons to the product.
		add_action( 'woocommerce_before_calculate_totals', [ $this, 'add_custom_weight' ], 10000, 1 );

		// Force rate recalculation (for extra weight).
		add_action( 'woocommerce_checkout_update_order_review', [ $this, 'weight_woocommerce_checkout_update_order_review' ], 1 );

		// Save the weight setting to cart object.
		add_filter( 'wc_epo_save_cart_item_data', [ $this, 'wc_epo_save_cart_item_data' ], 10, 3 );
	}

	/**
	 * Save the weight setting to cart object.
	 *
	 * @param array<mixed> $data Field data array.
	 * @param string       $type 'normal' or 'fee'.
	 * @param object       $obj The THEMECOMPLETE_EPO_FIELDS_* class object.
	 * @since 6.4.4
	 * @return array<mixed>
	 */
	public function wc_epo_save_cart_item_data( $data, $type, $obj ) {
		if ( ! property_exists( $obj, 'key' ) || ! property_exists( $obj, 'element' ) ) {
			return $data;
		}
		$key = array_search( $obj->key, $obj->element['option_values'], true );
		if ( false === $key ) {
			$key = false;
		}
		$weight = 0;
		if ( isset( $data['weight'] ) ) {
			$weight = floatval( $data['weight'] );
		}
		if ( isset( $obj->element['weight'] ) && '' !== $obj->element['weight'] ) {
			$weight = $weight + floatval( $obj->element['weight'] );
		}
		switch ( $obj->element['type'] ) {
			case 'checkbox':
				if ( false !== $key && isset( $obj->element['extra_multiple_choices'] ) && isset( $obj->element['extra_multiple_choices']['weight'] ) && isset( $obj->element['extra_multiple_choices']['weight'][ $key ] ) ) {
					$weight = $weight + floatval( $obj->element['extra_multiple_choices']['weight'][ $key ] );
				}
				break;
			case 'radio':
				if ( false !== $key && isset( $obj->element['extra_multiple_choices'] ) && isset( $obj->element['extra_multiple_choices']['weight'] ) && isset( $obj->element['extra_multiple_choices']['weight'][ $key ] ) ) {
					$weight = $weight + floatval( $obj->element['extra_multiple_choices']['weight'][ $key ] );
				}
				break;
			case 'select':
				if ( false !== $key && isset( $obj->element['extra_multiple_choices'] ) && isset( $obj->element['extra_multiple_choices']['weight'] ) && isset( $obj->element['extra_multiple_choices']['weight'][ $key ] ) ) {
					$weight = $weight + floatval( $obj->element['extra_multiple_choices']['weight'][ $key ] );
				}
				break;
			default:
				break;
		}

		$data['weight'] = $weight;

		return $data;
	}

	/**
	 * Force rate recalculation
	 *
	 * @since 6.4.4
	 * @return void
	 */
	public function weight_woocommerce_checkout_update_order_review() {
		$packages = apply_filters(
			'woocommerce_cart_shipping_packages',
			[]
		);
		foreach ( $packages as $package_key => $package ) {
			$wc_session_key = 'shipping_for_package_' . $package_key;
			WC()->session->set(
				$wc_session_key,
				false
			);

		}
	}

	/**
	 * Add the extra weight from addons to the product
	 *
	 * @param mixed $cart_object The cart object.
	 * @since 6.4.4
	 * @return void
	 */
	public function add_custom_weight( $cart_object = false ) {
		if ( defined( 'THEMECOMPLETE_DYNAMIC_EXTRA_WEIGHT_DONE' ) ) {
			return;
		}
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return;
		}
		if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) {
			return;
		}

		if ( ! did_action( 'wp_loaded' ) ) {
			return;
		}

		if ( ! $cart_object && function_exists( 'WC' ) && WC()->cart ) { // @phpstan-ignore-line
			$cart_object = WC()->cart;
		}

		if ( ! $cart_object ) {
			return;
		}

		$cart_contents = false;
		if ( is_object( $cart_object ) ) {
			if ( method_exists( $cart_object, 'get_cart' ) ) {
				$cart_contents = $cart_object->get_cart();
			} elseif ( property_exists( $cart_object, 'cart_contents' ) ) {
				$cart_contents = $cart_object->cart_contents;
			}
		}
		if ( ! is_array( $cart_contents ) ) {
			return;
		}

		foreach ( $cart_contents as $cart_key => $cart_item ) {
			$extra_weight = 0;
			if ( isset( $cart_item['tmcartepo'] ) ) {
				foreach ( $cart_item['tmcartepo'] as $key => $value ) {
					if ( isset( $value['weight'] ) ) {
						$quantity = 1;
						if ( isset( $value['quantity'] ) ) {
							$quantity = floatval( $value['quantity'] );
						}
						$extra_weight = $extra_weight + ( $quantity * floatval( $value['weight'] ) );
					}
				}
			}
			if ( isset( $cart_item['data'] ) && $cart_item['data'] && $extra_weight ) {
				$weight       = $cart_item['data']->get_weight();
				$weight       = floatval( $weight );
				$extra_weight = floatval( $extra_weight );
				$cart_item['data']->set_weight( $extra_weight + $weight );
				if ( function_exists( 'WC' ) && WC()->cart && WC()->cart->cart_contents && isset( WC()->cart->cart_contents[ $cart_key ] ) && isset( WC()->cart->cart_contents[ $cart_key ]['data'] ) ) { // @phpstan-ignore-line
					WC()->cart->cart_contents[ $cart_key ]['data']->set_weight( $extra_weight + $weight );
				}
			}
		}

		define( 'THEMECOMPLETE_DYNAMIC_EXTRA_WEIGHT_DONE', 1 );
	}

	/**
	 * Flag to check if we are in the product loop
	 *
	 * @since 6.2
	 * @return void
	 */
	public function woocommerce_before_shop_loop_item() {
		$this->is_in_product_loop = true;
	}

	/**
	 * Flag to check if we are in the product loop
	 *
	 * @since 6.2
	 * @return void
	 */
	public function woocommerce_after_shop_loop_item() {
		$this->is_in_product_loop = false;
	}

	/**
	 * Alter product display price to include possible option pricing on the product loop
	 *
	 * @param mixed        $price The product price.
	 * @param object|false $product The product object.
	 * @return mixed
	 * @since 6.2
	 */
	public function woocommerce_get_price_html( $price = '', $product = false ) {
		if ( ! $this->is_in_product_loop && ! $product instanceof WC_Product ) {
			return $price;
		}

		$tm_meta_cpf = themecomplete_get_post_meta( $product, 'tm_meta_cpf', true );
		if ( is_array( $tm_meta_cpf ) ) {
			$tm_price_display_mode          = isset( $tm_meta_cpf['price_display_mode'] ) ? $tm_meta_cpf['price_display_mode'] : 'none';
			$tm_price_display_override      = isset( $tm_meta_cpf['price_display_override'] ) ? $tm_meta_cpf['price_display_override'] : '';
			$tm_price_display_override_sale = isset( $tm_meta_cpf['price_display_override_sale'] ) ? $tm_meta_cpf['price_display_override_sale'] : '';
			$tm_price_display_override_to   = isset( $tm_meta_cpf['price_display_override_to'] ) ? $tm_meta_cpf['price_display_override_to'] : '';

			$f_tm_price_display_override      = apply_filters( 'wc_epo_get_price_in_currency', $tm_price_display_override );
			$f_tm_price_display_override_sale = apply_filters( 'wc_epo_get_price_in_currency', $tm_price_display_override_sale );
			$f_tm_price_display_override_to   = apply_filters( 'wc_epo_get_price_in_currency', $tm_price_display_override_to );

			$taxable    = $product->is_taxable();
			$tax_string = '';
			if ( $taxable ) {
				$tax_string .= ' <small>' . get_option( 'woocommerce_price_display_suffix' ) . '</small>';
			}
			switch ( $tm_price_display_mode ) {
				case 'price':
					if ( '' !== $tm_price_display_override_sale ) {
						$price = ( function_exists( 'wc_get_price_to_display' )
							? wc_format_sale_price( wc_format_decimal( $f_tm_price_display_override ), wc_format_decimal( $f_tm_price_display_override_sale ) )
							: '<del>' . themecomplete_price( wc_format_decimal( $f_tm_price_display_override ) ) . '</del> <ins>' . themecomplete_price( wc_format_decimal( $tm_price_display_override_sale ) ) . '</ins>'
						);
					} else {
						$price = themecomplete_price( wc_format_decimal( $f_tm_price_display_override ) );
					}
					$price = apply_filters( 'wc_epo_price_display_mode_price', $price, $tm_price_display_override, $tm_price_display_override_sale, $product );
					$price = $price . $tax_string;
					break;
				case 'from':
					$price = ( function_exists( 'wc_get_price_html_from_text' ) ? wc_get_price_html_from_text() : ( $product->get_price_html_from_text() ) );
					if ( '' !== $tm_price_display_override_sale ) {
						$price .= ( function_exists( 'wc_get_price_to_display' )
							? wc_format_sale_price( wc_format_decimal( $f_tm_price_display_override ), wc_format_decimal( $f_tm_price_display_override_sale ) )
							: '<del>' . themecomplete_price( wc_format_decimal( $f_tm_price_display_override ) ) . '</del> <ins>' . themecomplete_price( wc_format_decimal( $f_tm_price_display_override_sale ) ) . '</ins>'
						);
					} else {
						$price .= themecomplete_price( wc_format_decimal( $f_tm_price_display_override ) );
					}
					$price = apply_filters( 'wc_epo_price_display_mode_from', $price, $tm_price_display_override, $tm_price_display_override_sale, $product );
					$price = $price . $tax_string;
					break;
				case 'range':
					$price = themecomplete_price( wc_format_decimal( $f_tm_price_display_override ) ) . ' - ' . themecomplete_price( wc_format_decimal( $f_tm_price_display_override_to ) );
					$price = apply_filters( 'wc_epo_price_display_mode_range', $price, $tm_price_display_override, $tm_price_display_override_to, $product );
					$price = $price . $tax_string;
					break;
			}
		}

		return apply_filters( 'woocommerce_epo_get_price_html', $price, $product );
	}

	/**
	 * Enable shortcodes on an element property
	 *
	 * @param mixed $property The option property value.
	 * @return mixed
	 * @since 6.0.4
	 */
	public function enable_shortcodes( $property = '' ) {
		if ( '' !== $property ) {
			if ( is_array( $property ) ) {
				foreach ( $property as $key => $value ) {
					$property[ $key ] = themecomplete_do_shortcode( $value );
				}
			} else {
				$property = themecomplete_do_shortcode( $property );
			}
		}
		return $property;
	}

	/**
	 * Enable shortcodes in options strings
	 *
	 * @param string  $text Filtered text.
	 * @param string  $original_text Original text.
	 * @param boolean $shortcode If shortcode should be enabled.
	 * @return string
	 * @since 4.9.2
	 */
	public function wc_epo_kses( $text = '', $original_text = '', $shortcode = true ) {
		$text = $original_text;

		if ( $shortcode ) {
			$text = themecomplete_do_shortcode( $text );
		}

		return $text;
	}

	/**
	 * Enable shortcodes in cart option strings
	 *
	 * @param string $text The element label text.
	 * @return string
	 * @since 4.9.2
	 */
	public function wc_epo_label_in_cart( $text = '' ) {
		return themecomplete_do_shortcode( $text );
	}

	/**
	 * Get product min/max prices
	 *
	 * @param WC_Product $product Product object.
	 * @return array<mixed>
	 * @since 4.8.1
	 */
	public function get_product_min_max_prices( $product ) {
		$id   = themecomplete_get_id( $product );
		$type = themecomplete_get_product_type( $product );

		$has_epo = THEMECOMPLETE_EPO_API()->has_options( $id );
		if ( ! THEMECOMPLETE_EPO_API()->is_valid_options( $has_epo ) || 'variation' === $type ) {
			return [];
		}

		$override_id = absint( THEMECOMPLETE_EPO_WPML()->get_original_id( $id, 'product' ) );
		$tm_meta_cpf = themecomplete_get_post_meta( $override_id, 'tm_meta_cpf', true );

		$price_override = ( 'no' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_override_product_price' ) )
			? 0
			: ( ( 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_override_product_price' ) )
				? 1
				: ( ! empty( $tm_meta_cpf['price_override'] ) ? 1 : 0 ) );

		$minmax = $this->add_product_tc_prices( $id );
		if ( ! is_array( $minmax ) ) {
			$minmax = [
				'tc_min_price'    => 0,
				'tc_max_price'    => 0,
				'tc_min_variable' => 0,
				'tc_max_variable' => 0,
				'tc_min_max'      => false,
			];
		}
		if ( ! isset( $minmax['tc_min_price'] ) ) {
			$minmax['tc_min_price'] = 0;
		}
		if ( ! isset( $minmax['tc_max_price'] ) ) {
			$minmax['tc_max_price'] = 0;
		}

		$min_raw           = 0;
		$max_raw           = 0;
		$min               = 0;
		$max               = 0;
		$min_price         = 0;
		$max_price         = 0;
		$min_regular_price = 0;
		$max_regular_price = 0;

		if ( 'variable' === $type || 'variable-subscription' === $type ) {
			$prices = $product->get_variation_prices( false ); // @phpstan-ignore-line

			// Calculate min price.
			$min_price       = current( $prices['price'] );
			$tc_min_variable = isset( $minmax['tc_min_variable'][ key( $prices['price'] ) ] )
				? $minmax['tc_min_variable'][ key( $prices['price'] ) ]
				: ( isset( $minmax['tc_min_variable'] )
					? $minmax['tc_min_variable']
					: 0
				);

			if ( is_array( $tc_min_variable ) ) {
				$tc_min_variable = min( $tc_min_variable );
			}

			$min_raw = floatval( apply_filters( 'wc_epo_options_min_price', $tc_min_variable, $product, true ) );

			$min_price = $min_price + $min_raw;

			// include taxes.
			$min_price = $this->tc_get_display_price( $product, $min_price );

			if ( $price_override ) {
				if ( ! empty( $min_raw ) ) {
					$min_price = $min_raw;
				}
				$this->product_options_minmax[ $id ]['is_override'] = 1;
			}

			$min = $this->tc_get_display_price( $product, $min_raw );

			// Calculate max price.
			$copy_prices = $prices['price'];
			$added_max   = [];
			foreach ( $copy_prices as $vkey => $vprice ) {
				$added_price_max = is_array( $minmax['tc_max_variable'] )
					? ( isset( $minmax['tc_max_variable'][ $vkey ] )
						? $minmax['tc_max_variable'][ $vkey ]
						: 0 )
					: $minmax['tc_max_variable'];

				$added_price          = floatval( apply_filters( 'wc_epo_options_max_price_raw', $added_price_max, $product, false ) );
				$added_max[]          = $added_price;
				$copy_prices[ $vkey ] = $vprice + $added_price;
			}
			asort( $copy_prices );
			$max_price = end( $copy_prices );

			asort( $added_max );

			$max_raw = floatval( apply_filters( 'wc_epo_options_max_price', end( $added_max ), $product, false ) );

			$max_price = $this->tc_get_display_price( $product, $max_price );

			if ( $price_override && ! ( empty( $minmax['tc_min_variable'] ) && empty( $minmax['tc_max_variable'] ) ) ) {
				$max_price = floatval( $max_price ) - floatval( $this->tc_get_display_price( $product, floatval( $prices['price'][ key( $copy_prices ) ] ) ) );
			}

			$max = $this->tc_get_display_price( $product, $max_raw );

			$min_regular_price = floatval( current( $prices['regular_price'] ) ) + $min_raw;
			$max_regular_price = floatval( end( $prices['regular_price'] ) ) + $max_raw;

			// include taxes.
			$min_regular_price = $this->tc_get_display_price( $product, $min_regular_price );
			$max_regular_price = $this->tc_get_display_price( $product, $max_regular_price );

		} else {

			// Calculate min price.
			$min_raw = floatval( apply_filters( 'wc_epo_options_min_price', $minmax['tc_min_price'], $product, false ) );

			if ( $price_override ) {

				if ( ! empty( $min_raw ) ) {
					$new_min = $min_raw;
				} else {
					$new_min = $product->get_price();
				}

				$min_raw = $new_min;

				$this->product_options_minmax[ $id ]['is_override'] = 1;
			}

			$this->product_options_minmax[ $id ]['tc_min_price'] = $min_raw;

			$display_price         = $this->tc_get_display_price( $product );
			$display_regular_price = $this->tc_get_display_price( $product, $this->tc_get_regular_price( $product ) );

			if ( $price_override && $min_raw <= 0 ) {
				$display_price = $display_regular_price;
			}

			$min       = $this->tc_get_display_price( $product, $min_raw );
			$min_price = $display_price;

			// Calculate max price.
			$max_raw = floatval( apply_filters( 'wc_epo_options_max_price', $minmax['tc_max_price'], $product, false ) );
			$this->product_options_minmax[ $id ]['tc_max_price'] = $max_raw;
			$max = $this->tc_get_display_price( $product, $max_raw );
			if ( $price_override ) {
				$max_price = $max;
			} else {
				$max_price = $this->tc_get_display_price( $product, (float) apply_filters( 'wc_epo_product_price', $product->get_price() ) + $max_raw );
			}
			$min_regular_price = floatval( $display_regular_price );
			if ( $price_override ) {
				$max_regular_price = $max;
			} else {
				$max_regular_price = floatval( $this->tc_get_display_price( $product, $product->get_regular_price() ) ) + floatval( $max );
			}
		}

		return [
			'min_raw'             => $min_raw,
			'max_raw'             => $max_raw,
			'min'                 => $min,
			'max'                 => $max,
			'min_price'           => $min_price,
			'max_price'           => $max_price,

			'min_regular_price'   => $min_regular_price,
			'max_regular_price'   => $max_regular_price,

			'formatted_min'       => wc_format_decimal( $min, wc_get_price_decimals() ),
			'formatted_max'       => wc_format_decimal( $max, wc_get_price_decimals() ),
			'formatted_min_price' => wc_format_decimal( $min_price, wc_get_price_decimals() ),
			'formatted_max_price' => wc_format_decimal( $max_price, wc_get_price_decimals() ),

		];
	}

	/**
	 * Alter generated product structured data
	 *
	 * @param array<mixed> $markup The markup array.
	 * @param object       $product The product object.
	 * @return array<mixed>
	 * @since 4.8.1
	 */
	public function woocommerce_structured_data_product_offer( $markup, $product ) {
		if ( ! $product instanceof WC_Product || 'no' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_alter_structured_data' ) ) {
			return $markup;
		}

		$tm_price_display_mode = 'none';

		$tm_meta_cpf = themecomplete_get_post_meta( $product, 'tm_meta_cpf', true );

		if ( is_array( $tm_meta_cpf ) ) {
			$tm_price_display_mode          = isset( $tm_meta_cpf['price_display_mode'] ) ? $tm_meta_cpf['price_display_mode'] : 'none';
			$tm_price_display_override      = isset( $tm_meta_cpf['price_display_override'] ) ? $tm_meta_cpf['price_display_override'] : '';
			$tm_price_display_override_sale = isset( $tm_meta_cpf['price_display_override_sale'] ) ? $tm_meta_cpf['price_display_override_sale'] : '';
			$tm_price_display_override_to   = isset( $tm_meta_cpf['price_display_override_to'] ) ? $tm_meta_cpf['price_display_override_to'] : '';

			switch ( $tm_price_display_mode ) {
				case 'price':
				case 'from':
					if ( '' !== $tm_price_display_override_sale ) {
						$min_price = $tm_price_display_override_sale;
					} else {
						$min_price = $tm_price_display_override;
					}
					break;
				case 'range':
					$min_price = $tm_price_display_override;
					$max_price = $tm_price_display_override_to;

					$markup['lowPrice']  = $min_price;
					$markup['highPrice'] = $max_price;
					break;

				case 'none':
				default:
					$min_max = $this->get_product_min_max_prices( $product );

					if ( empty( $min_max ) ) {
						return $markup;
					}

					$min_price = $min_max['formatted_min_price'];
					$max_price = $min_max['formatted_max_price'];

					break;
			}
		}

		if ( isset( $min_price ) ) {
			if ( isset( $markup['priceSpecification'] ) && is_array( $markup['priceSpecification'] ) && isset( $markup['priceSpecification']['price'] ) ) {
				$markup['priceSpecification']['price'] = $min_price;
				$markup['price']                       = $min_price;
			}
			if ( isset( $max_price ) && isset( $markup['lowPrice'] ) && isset( $markup['highPrice'] ) ) {
				$markup['lowPrice']  = $min_price;
				$markup['highPrice'] = $max_price;
			}
		}

		return $markup;
	}

	/**
	 * Override the minimum characters of text fields globally
	 *
	 * @param string $min The minimum characters.
	 * @param string $element The element type.
	 * @return string
	 * @since 1.0
	 */
	public function wc_epo_global_min_chars( $min = '', $element = '' ) {
		$element = str_replace( '_min_chars', '', $element );

		if ( ( 'textfield' === $element || 'textarea' === $element ) && '' !== THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_min_chars' ) && '' === $min ) {
			$min = THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_min_chars' );
		}

		return $min;
	}

	/**
	 * Override the maximum characters of text fields globally
	 *
	 * @param string $max The maximum characters.
	 * @param string $element The element type.
	 * @return string
	 * @since 1.0
	 */
	public function wc_epo_global_max_chars( $max = '', $element = '' ) {
		$element = str_replace( '_min_chars', '', $element );
		if ( ( 'textfield' === $element || 'textarea' === $element ) && '' !== THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_max_chars' ) && '' === $max ) {
			$max = THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_max_chars' );
		}

		return $max;
	}

	/**
	 * Initialize custom product settings
	 *
	 * @return void
	 * @since 1.0
	 */
	public function init_settings_pre() {
		$postid = false;
		if ( function_exists( 'ux_builder_is_iframe' ) && ux_builder_is_iframe() ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			if ( isset( $_GET['post_id'] ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$postid = absint( wp_unslash( $_GET['post_id'] ) );
			}
		} elseif ( ! isset( $_SERVER['HTTP_HOST'] ) || ! isset( $_SERVER['REQUEST_URI'] ) ) {
			$postid = 0;
		} else {
			$url    = 'http://' . esc_url_raw( wp_unslash( $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ) );
			$postid = THEMECOMPLETE_EPO_HELPER()->get_url_to_postid( $url );
		}

		$this->postid_pre = $postid;
		$product          = wc_get_product( $postid );

		$check1 = ( 0 === (int) $postid );
		$check2 = ( $product
					&& is_object( $product )
					&& property_exists( $product, 'post' )
					&& property_exists( $product->post, 'post_type' )
					&& ( in_array( $product->post->post_type, [ 'product', 'product_variation' ], true ) ) );
		$check3 = ( $product
					&& is_object( $product )
					&& property_exists( $product, 'post_type' )
					&& ( in_array( $product->post_type, [ 'product', 'product_variation' ], true ) ) ); // @phpstan-ignore-line

		if ( $check1 || $check2 || $check3 ) {
			add_action( 'template_redirect', [ $this, 'init_settings' ] );
		} else {
			$this->init_settings();
		}
	}

	/**
	 * Initialize variables
	 *
	 * @return void
	 * @since 1.0
	 */
	public function init_vars() {
		$this->wc_vars = [
			'is_product'             => is_product(),
			'is_shop'                => is_shop(),
			'is_product_category'    => is_product_category(),
			'is_product_taxonomy'    => is_product_taxonomy(),
			'is_product_tag'         => is_product_tag(),
			'is_cart'                => is_cart(),
			'is_checkout'            => is_checkout(),
			'is_account_page'        => is_account_page(),
			'is_ajax'                => wp_doing_ajax(),
			'is_page'                => is_page(),
			'is_order_received_page' => is_order_received_page(),
		];

		// Disable floating totals box on non product pages.
		if ( ! $this->wc_vars['is_product'] ) {
			THEMECOMPLETE_EPO_DATA_STORE()->set( 'tm_epo_floating_totals_box', '' );
		}
	}

	/**
	 * Initialize custom product settings
	 *
	 * @return void
	 * @since 1.0
	 */
	public function init_settings() {
		if ( is_admin() && ! $this->is_quick_view() ) {
			return;
		}

		// Re populate options for WPML.
		if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
			// todo:Find another place to re init settings for WPML.
			$this->get_plugin_settings();
		}

		do_action( 'wc_epo_init_settings' );

		$post_max = (float) wc_let_to_num( (string) ini_get( 'post_max_size' ) );

		// post_max_size debug.
		// phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( ( empty( $_FILES ) && empty( $_POST ) && isset( $_SERVER['REQUEST_METHOD'] ) && strtolower( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) === 'post'
			&& isset( $_SERVER['CONTENT_LENGTH'] ) && (float) $_SERVER['CONTENT_LENGTH'] > $post_max )
			// phpcs:ignore WordPress.Security.NonceVerification, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			|| ( is_admin() && ( ! isset( $_GET ) || ( isset( $_GET ) && isset( $_GET['post_type'] ) && isset( $_GET['action'] ) && ! str_starts_with( wp_unslash( $_GET['post_type'] ), 'ct_template' ) && ! str_starts_with( wp_unslash( $_GET['action'] ), 'oxy_render_' ) ) ) ) // @phpstan-ignore-line
		) {
			/* translators: %s: post max size */
			wc_add_notice( sprintf( esc_html__( 'Trying to upload files larger than %s is not allowed!', 'woocommerce-tm-extra-product-options' ), $post_max ), 'error' );
		}

		global $post, $product;
		$this->set_tm_meta();
		$this->init_settings_after();
	}

	/**
	 * Initialize custom product settings
	 *
	 * @return void
	 * @since 1.0
	 */
	public function init_settings_after() {
		global $post, $product;
		// Check if the plugin is active for the user.
		if ( $this->check_enable() ) {
			if ( ( $this->is_enabled_shortcodes() || is_product() || $this->is_quick_view() )
				&& ( 'normal' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_display' ) || 'normal' === $this->tm_meta_cpf['override_display'] )
				&& 'action' !== $this->tm_meta_cpf['override_display']
			) {
				// Add options to the page.
				THEMECOMPLETE_EPO_DATA_STORE()->set( 'tm_epo_options_placement_hook_priority', THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_options_placement_hook_priority' ) );
				THEMECOMPLETE_EPO_DATA_STORE()->set( 'tm_epo_totals_box_placement_hook_priority', THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_totals_box_placement_hook_priority' ) );

				add_action( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_options_placement' ), [ THEMECOMPLETE_EPO_DISPLAY(), 'tm_epo_fields' ], THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_options_placement_hook_priority' ) );
				add_action( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_options_placement' ), [ THEMECOMPLETE_EPO_DISPLAY(), 'tm_add_inline_style' ], intval( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_options_placement_hook_priority' ) ) + 99999 );
				add_action( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_totals_box_placement' ), [ THEMECOMPLETE_EPO_DISPLAY(), 'tm_epo_totals' ], THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_totals_box_placement_hook_priority' ) );
			}
		}

		if ( 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_enable_in_shop' ) && ( is_shop() || is_product_category() || is_product_tag() || function_exists( 'dokan' ) ) ) {
			add_action( 'woocommerce_after_shop_loop_item', [ $this, 'tm_woocommerce_after_shop_loop_item' ], 9 );
		}

		add_action( 'woocommerce_shortcode_before_product_loop', [ $this, 'woocommerce_shortcode_before_product_loop' ] );
		add_action( 'woocommerce_shortcode_after_product_loop', [ $this, 'woocommerce_shortcode_after_product_loop' ] );
		if ( $this->is_enabled_shortcodes() ) {
			add_action( 'woocommerce_after_shop_loop_item', [ $this, 'tm_enable_options_on_product_shortcode' ], 1 );
		}

		$this->current_free_text       = esc_attr__( 'Free!', 'woocommerce' );
		$this->assoc_current_free_text = $this->current_free_text;
		if ( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_replacement_free_price_text' ) ) {
			$this->assoc_current_free_text = THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_replacement_free_price_text' );
		}
		if ( 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_remove_free_price_label' ) ) {
			$this->assoc_current_free_text = '';
		}

		if ( 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_remove_free_price_label' ) && 'no' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_include_possible_option_pricing' ) ) {

			if ( $post || $this->postid_pre ) {

				if ( $post ) {
					$thiscpf = $this->get_product_tm_epos( $post->ID, '', false, true );
				}

				if ( is_product() && isset( $thiscpf ) && is_array( $thiscpf ) && ( ! empty( $thiscpf['global'] ) || ! empty( $thiscpf['local'] ) ) ) {
					if ( $product &&
						( is_object( $product ) && ! is_callable( [ $product, 'get_price' ] ) ) ||
						( ! is_object( $product ) )
					) {
						$product = wc_get_product( $post->ID );
					}
					if ( $product &&
						is_object( $product ) && is_callable( [ $product, 'get_price' ] )
					) {
						if ( ! (float) $product->get_price() > 0 ) {
							if ( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_replacement_free_price_text' ) ) {
								$this->current_free_text = THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_replacement_free_price_text' );
								add_filter( 'woocommerce_get_price_html', [ $this, 'get_price_html' ], 10, 2 );
							} elseif ( 'no' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_use_from_on_price' ) ) {
								$this->current_free_text = '';
								remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
							}
						}
						add_filter( 'woocommerce_get_price_html', [ $this, 'related_get_price_html' ], 10, 2 );
					}
				} elseif ( is_shop() || is_product_category() || is_product_tag() ) {
					add_filter( 'woocommerce_get_price_html', [ $this, 'get_price_html_shop' ], 10, 2 );
				} elseif ( ! is_product() && $this->is_enabled_shortcodes() ) {
					if ( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_replacement_free_price_text' ) ) {
						$this->current_free_text = THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_replacement_free_price_text' );
						add_filter( 'woocommerce_get_price_html', [ $this, 'get_price_html' ], 10, 2 );
					} else {
						if ( 'no' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_use_from_on_price' ) ) {
							$this->current_free_text = '';
							remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
						}
						add_filter( 'woocommerce_get_price_html', [ $this, 'get_price_html' ], 10, 2 );
					}
				} elseif ( is_product() ) {
					add_filter( 'woocommerce_get_price_html', [ $this, 'related_get_price_html2' ], 10, 2 );
				}
			} elseif ( $this->is_quick_view() ) {
				if ( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_replacement_free_price_text' ) ) {
					$this->current_free_text = THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_replacement_free_price_text' );
					add_filter( 'woocommerce_get_price_html', [ $this, 'get_price_html' ], 10, 2 );
				} else {
					add_filter( 'woocommerce_get_price_html', [ $this, 'get_price_html' ], 10, 2 );
					if ( 'no' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_use_from_on_price' ) ) {
						$this->current_free_text = '';
						remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
					}
				}
			}
		} elseif ( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_replacement_free_price_text' ) ) {
			$this->current_free_text = THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_replacement_free_price_text' );
			add_filter( 'woocommerce_get_price_html', [ $this, 'get_price_html' ], 10, 2 );
		}

		if ( 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_use_from_on_price' ) && is_product() && $post ) {
			if ( $product &&
				( is_object( $product ) && ! is_callable( [ $product, 'get_price' ] ) ) ||
				( ! is_object( $product ) )
			) {
				$product = wc_get_product( $post->ID );
			}
			if ( $product && is_object( $product ) && is_callable( [ $product, 'get_price' ] ) ) {
				$this->current_free_text = $this->tm_get_price_html( $product->get_price(), $product );
			}
		}
	}

	/**
	 * Get the theme name
	 *
	 * @param string $header Theme header. Name, Description, Author, Version, ThemeURI, AuthorURI, Status, Tags.
	 *
	 * @return string|array<mixed>|false
	 */
	public function get_theme( $header = '' ) {
		$out = '';
		if ( function_exists( 'wp_get_theme' ) ) {
			$theme = wp_get_theme();
			$out   = $theme->get( $header );
		}

		return $out;
	}

	/**
	 * Check if we have a support theme quickview
	 *
	 * @return boolean
	 */
	public function is_supported_quick_view() {
		$theme_name = $this->get_theme( 'Name' );
		if ( ! is_string( $theme_name ) ) {
			return false;
		}
		$theme_name = strtolower( $theme_name );
		$theme      = explode( ' ', $theme_name );
		if ( isset( $theme[0] ) && isset( $theme[1] ) ) {
			$theme = $theme[0];
		} else {
			$theme = explode( '-', $theme_name );
			if ( isset( $theme[0] ) && isset( $theme[1] ) ) {
				$theme = $theme[0];
			}
		}

		if ( is_array( $theme ) ) {
			$theme = $theme_name;
		}

		if (
			'flatsome' === $theme
			|| 'kleo' === $theme
			|| 'venedor' === $theme
			|| 'elise' === $theme
			|| 'minshop' === $theme
			|| 'porto' === $theme
			|| 'grace' === $theme
			|| 'woodmart' === $theme
		) {
			return true;
		}

		return false;
	}

	/**
	 * Generate the hasoptions variable
	 *
	 * @since 6.4
	 * @return void
	 */
	public function hasoptions() {
		$post_id = get_the_ID();
		if ( ! isset( $this->hasoptions[ $post_id ] ) ) {
			if ( $post_id && ( $this->wc_vars['is_product'] || 'product' === get_post_type( $post_id ) ) ) {
				$has_epo = THEMECOMPLETE_EPO_API()->has_options( $post_id );

				// Product has extra options.
				if ( THEMECOMPLETE_EPO_API()->is_valid_options( $has_epo ) ) {
					$this->hasoptions[ $post_id ] = true;

					// Product doesn't have extra options but the final total box is enabled for all products.
				} elseif ( 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_enable_final_total_box_all' ) ) {

					$this->hasoptions[ $post_id ] = true;

					// Search for composite products extra options.
					// Product has styled variations.
				} elseif ( isset( $has_epo['variations'] ) && ! empty( $has_epo['variations'] ) && isset( $has_epo['variations_disabled'] ) && empty( $has_epo['variations_disabled'] ) ) {
					$this->hasoptions[ $post_id ] = true;
				} else {
					$this->hasoptions[ $post_id ] = false;
				}
			}
		}

		$this->hasoptions[ $post_id ] = apply_filters( 'wc_epo_hasoptions', $this->hasoptions[ $post_id ], $post_id );
	}

	/**
	 * Check if plugin scripts can be loaded
	 *
	 * @since 1.0
	 * @return boolean
	 */
	public function can_load_scripts() {
		$loading_areas = THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_loading_areas' );
		if ( ! is_array( $loading_areas ) ) {
			$loading_areas = [ $loading_areas ];
		}

		if ( 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_enable_in_shop' ) ) {
			$loading_areas = array_unique( array_merge( $loading_areas, [ 'shop', 'product_taxonomy' ] ) );
		}

		$can_load_scripts = false;

		foreach ( $loading_areas as $area ) {
			switch ( $area ) {
				case 'everywhere':
					$can_load_scripts = true;
					break;
				case 'shop':
					$can_load_scripts = $can_load_scripts || THEMECOMPLETE_EPO()->wc_vars['is_shop'];
					break;
				case 'product_category':
					$can_load_scripts = $can_load_scripts || THEMECOMPLETE_EPO()->wc_vars['is_product_category'];
					break;
				case 'product_taxonomy':
					$can_load_scripts = $can_load_scripts || THEMECOMPLETE_EPO()->wc_vars['is_product_taxonomy'];
					break;
				case 'product_tag':
					$can_load_scripts = $can_load_scripts || THEMECOMPLETE_EPO()->wc_vars['is_product_tag'];
					break;
				case 'product':
					$can_load_scripts = $can_load_scripts || THEMECOMPLETE_EPO()->wc_vars['is_product'];
					break;
				case 'cart':
					$can_load_scripts = $can_load_scripts || THEMECOMPLETE_EPO()->wc_vars['is_cart'];
					break;
				case 'checkout':
					$can_load_scripts = $can_load_scripts || THEMECOMPLETE_EPO()->wc_vars['is_checkout'];
					break;
				case 'order_received_page':
					$can_load_scripts = $can_load_scripts || THEMECOMPLETE_EPO()->wc_vars['is_order_received_page'];
					break;
				case 'account_page':
					$can_load_scripts = $can_load_scripts || THEMECOMPLETE_EPO()->wc_vars['is_account_page'];
					break;
				case 'woocommerce':
					$can_load_scripts = $can_load_scripts || THEMECOMPLETE_EPO()->wc_vars['is_shop'] || THEMECOMPLETE_EPO()->wc_vars['is_product_taxonomy'] || THEMECOMPLETE_EPO()->wc_vars['is_product'] || THEMECOMPLETE_EPO()->wc_vars['is_cart'] || THEMECOMPLETE_EPO()->wc_vars['is_checkout'] || THEMECOMPLETE_EPO()->wc_vars['is_order_received_page'] || THEMECOMPLETE_EPO()->wc_vars['is_account_page'];
					break;
				case 'quickview':
					$can_load_scripts = $can_load_scripts || ( ( class_exists( 'WC_Quick_View' ) || $this->is_supported_quick_view() ) && ( THEMECOMPLETE_EPO()->wc_vars['is_shop'] || THEMECOMPLETE_EPO()->wc_vars['is_product_category'] || THEMECOMPLETE_EPO()->wc_vars['is_product_tag'] ) );
					break;
			}
		}

		if ( apply_filters( 'wc_epo_disable_scripts_if_no_extra_options', true ) && $can_load_scripts && THEMECOMPLETE_EPO()->wc_vars['is_product'] ) {
			$this->hasoptions();

			if ( ! $this->hasoptions[ get_the_ID() ] ) {
				return false;
			}
		}

		$can_load_scripts = apply_filters( 'wc_epo_can_load_scripts', $can_load_scripts );

		return $can_load_scripts;
	}

	/**
	 * Flag to check if we are in the product shortcode
	 *
	 * @since 1.0
	 * @return void
	 */
	public function woocommerce_shortcode_before_product_loop() {
		$this->is_in_product_shortcode = true;
	}

	/**
	 * Flag to check if we are in the product shortcode
	 *
	 * @since 1.0
	 * @return void
	 */
	public function woocommerce_shortcode_after_product_loop() {
		$this->is_in_product_shortcode = false;
	}

	/**
	 * Displays options in [product] shortcode
	 *
	 * @since 1.0
	 * @return void
	 */
	public function tm_enable_options_on_product_shortcode() {
		if ( $this->is_in_product_shortcode ) {
			$this->tm_woocommerce_after_shop_loop_item();
		}
	}

	/**
	 * Displays options in shop page
	 *
	 * @since 1.0
	 * @return void
	 */
	public function tm_woocommerce_after_shop_loop_item() {
		$post_id = get_the_ID();
		$has_epo = THEMECOMPLETE_EPO_API()->has_options( $post_id );
		if ( false !== $post_id && THEMECOMPLETE_EPO_API()->is_valid_options( $has_epo ) ) {
			echo '<div class="tm-has-options tc-after-shop-loop"><form class="cart">';
			THEMECOMPLETE_EPO_DISPLAY()->frontend_display( $post_id, 'tc_' . $post_id, false );
			THEMECOMPLETE_EPO_DISPLAY()->woocommerce_before_add_to_cart_button( $post_id );
			echo '</form></div>';
		}
	}

	/**
	 * Generate min/max prices for the $product
	 *
	 * @param integer|false $id The product id.
	 * @return mixed
	 * @since 1.0
	 */
	public function add_product_tc_prices( $id = false ) {
		if ( false !== $id ) {
			if ( isset( $this->product_options_minmax[ $id ] ) ) {
				return $this->product_options_minmax[ $id ];
			}

			$this->product_options_minmax[ $id ] = [
				'tc_min_price'    => 0,
				'tc_max_price'    => 0,
				'tc_min_variable' => 0,
				'tc_max_variable' => 0,
				'tc_min_max'      => false,
			];

			$epos = $this->get_product_tm_epos( $id, '', false, true );

			if ( is_array( $epos ) && ( ! empty( $epos['global'] ) || ! empty( $epos['local'] ) ) && ! empty( $epos['price'] ) ) {
				$minmax = THEMECOMPLETE_EPO_HELPER()->sum_array_values( $id, true );

				if ( ! isset( $minmax['min'] ) ) {
					$minmax['min'] = 0;
				}
				if ( ! isset( $minmax['max'] ) ) {
					$minmax['max'] = 0;
				}

				$min                    = $minmax['min'];
				$max                    = $minmax['max'];
				$minmax['tc_min_price'] = $min;
				$minmax['tc_max_price'] = $max;

				$minmax['tc_min_variable'] = $min;
				$minmax['tc_max_variable'] = $max;

				$minmax['tc_min_max'] = true;

				$this->product_options_minmax[ $id ] = [
					'tc_min_price'    => $min,
					'tc_max_price'    => $max,
					'tc_min_variable' => $min,
					'tc_max_variable' => $max,
					'tc_min_max'      => true,
				];

				if ( is_array( $min ) && is_array( $max ) ) {
					$this->product_options_minmax[ $id ] = [
						'tc_min_price'    => min( $min ),
						'tc_max_price'    => max( $max ),
						'tc_min_variable' => $min,
						'tc_max_variable' => $max,
						'tc_min_max'      => true,
					];

					$minmax['tc_min_price']    = min( $min );
					$minmax['tc_max_price']    = max( $max );
					$minmax['tc_min_variable'] = $min;
					$minmax['tc_max_variable'] = $max;
				}

				return $minmax;
			}
		}

		return false;
	}

	/**
	 * Alter the price filter
	 *
	 * @param mixed        $price The product price.
	 * @param object|false $product The product object.
	 * @return mixed
	 * @since 4.8.4
	 */
	public function woocommerce_product_get_price( $price = 0, $product = false ) {
		if ( '' === $price ) {
			$minmax = $this->add_product_tc_prices( themecomplete_get_id( $product ) );
			if ( false !== $minmax ) {
				$price = 0;
			}
		}

		return $price;
	}

	/**
	 * Alter product display price to include possible option pricing
	 *
	 * @param float        $price The product price.
	 * @param object|false $product The product object.
	 * @return float
	 * @since 1.0
	 */
	public function tm_woocommerce_get_price( $price = 0, $product = false ) {
		++$this->tm_woocommerce_get_price_flag;

		if ( 1 === $this->tm_woocommerce_get_price_flag ) {
			if ( ! is_admin() && ! $this->wc_vars['is_product'] && 'no' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_use_from_on_price' ) ) {
				add_filter( 'woocommerce_get_price_html', [ $this, 'tm_get_price_html' ], 11, 2 );
				add_filter( 'woocommerce_get_variation_price_html', [ $this, 'tm_get_price_html' ], 11, 2 );
			} else {
				$minmax = $this->add_product_tc_prices( themecomplete_get_id( $product ) );
				if ( false !== $minmax ) {
					add_filter( 'woocommerce_get_price_html', [ $this, 'tm_get_price_html' ], 11, 2 );
					add_filter( 'woocommerce_get_variation_price_html', [ $this, 'tm_get_price_html' ], 11, 2 );
				}
			}
		}

		return $price;
	}

	/**
	 * Alter product display price to include possible option pricing
	 *
	 * @param boolean      $show See if prices should be shown for each variation after selection.
	 * @param object|false $product The product object.
	 * @param object|false $variation The variable product object.
	 * @return boolean
	 * @since 1.0
	 */
	public function tm_woocommerce_show_variation_price( $show = true, $product = false, $variation = false ) {
		if ( $product && $variation ) {
			$minmax = THEMECOMPLETE_EPO_HELPER()->sum_array_values( themecomplete_get_id( $product ) );
			if ( is_array( $minmax ) && ! empty( $minmax['max'] ) ) {
				$show = true;
			}
		}

		return $show;
	}

	/**
	 * Returns the product's active price
	 *
	 * @param object|false $product The product object.
	 * @return mixed
	 * @since 1.0
	 */
	public function tc_get_price( $product = false ) {
		$tc_min_price = 0;
		$price        = $tc_min_price;
		if ( $product instanceof WC_Product ) {
			$id = themecomplete_get_id( $product );
			if ( false !== $id && isset( $this->product_options_minmax[ $id ] ) ) {
				$tc_min_price = $this->product_options_minmax[ $id ]['tc_min_price'];
			}

			if ( empty( $this->product_options_minmax[ $id ]['is_override'] ) ) {
				$price = (float) apply_filters( 'wc_epo_product_price', $product->get_price() ) + (float) $tc_min_price;
			} else {
				$price = (float) $tc_min_price;
			}
		}
		return apply_filters( 'tc_woocommerce_product_get_price', $price, $product );
	}

	/**
	 * Returns the price including or excluding tax, based on the 'woocommerce_tax_display_shop' setting.
	 *
	 * @param object|false $product The product object.
	 * @param mixed        $price The product price.
	 * @param integer      $qty The product quantity.
	 * @return string|float
	 * @since 1.0
	 */
	public function tc_get_display_price( $product = false, $price = '', $qty = 1 ) {
		if ( '' === $price ) {
			$price = $this->tc_get_price( $product );
		}
		$display_price = $price;
		if ( $product instanceof WC_Product ) {
			$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
			$display_price    = 'incl' === $tax_display_mode ? themecomplete_get_price_including_tax(
				$product,
				[
					'qty'   => $qty,
					'price' => $price,
				]
			) : themecomplete_get_price_excluding_tax(
				$product,
				[
					'qty'   => $qty,
					'price' => $price,
				]
			);
		}

		return $display_price;
	}

	/**
	 * Returns the product's regular price.
	 *
	 * @param object|false $product The product object.
	 * @return float
	 * @since 1.0
	 */
	public function tc_get_regular_price( $product = false ) {
		$tc_min_price = (float) 0;
		$price        = $tc_min_price;
		if ( $product instanceof WC_Product ) {
			$id = themecomplete_get_id( $product );
			if ( false !== $id && isset( $this->product_options_minmax[ $id ] ) ) {
				$tc_min_price = $this->product_options_minmax[ $id ]['tc_min_price'];
			}
			if ( empty( $this->product_options_minmax[ $id ]['is_override'] ) ) {
				$price = (float) apply_filters( 'wc_epo_product_price', $product->get_regular_price() ) + (float) $tc_min_price;
			} else {
				$price = (float) $tc_min_price;
			}
		}
		return apply_filters( 'tc_woocommerce_product_get_regular_price', $price, $product );
	}

	/**
	 * Alter product display price to include possible option pricing
	 *
	 * @param mixed        $price The product price.
	 * @param object|false $product The product object.
	 * @return mixed
	 * @since 1.0
	 */
	public function tm_get_price_html( $price = '', $product = false ) {
		if ( $product instanceof WC_Product ) {
			$original_price = $price;

			$min_max = $this->get_product_min_max_prices( $product );
			$type    = themecomplete_get_product_type( $product );

			if ( empty( $min_max ) || 'variation' === $type ) {
				$check_filter_1 = has_filter( 'woocommerce_get_price_html', [ $this, 'tm_get_price_html' ] );
				$check_filter_2 = has_filter( 'woocommerce_get_variation_price_html', [ $this, 'tm_get_price_html' ] );
				if ( $check_filter_1 ) {
					remove_filter( 'woocommerce_get_price_html', [ $this, 'tm_get_price_html' ], 11 );
				}
				if ( $check_filter_2 ) {
					remove_filter( 'woocommerce_get_variation_price_html', [ $this, 'tm_get_price_html' ], 11 );
				}
				$price = $product->get_price_html();
				if ( $check_filter_1 ) {
					add_filter( 'woocommerce_get_price_html', [ $this, 'tm_get_price_html' ], 11, 2 );
				}
				if ( $check_filter_2 ) {
					add_filter( 'woocommerce_get_variation_price_html', [ $this, 'tm_get_price_html' ], 11, 2 );
				}

				return $price;
			}

			$use_from  = ( 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_use_from_on_price' ) );
			$free_text = ( 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_remove_free_price_label' ) ) ? ( '' !== THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_replacement_free_price_text' ) ? THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_replacement_free_price_text' ) : '' ) : esc_attr__( 'Free!', 'woocommerce' );

			$min               = $min_max['min_raw'];
			$max               = $min_max['max_raw'];
			$min_price         = $min_max['min_price'];
			$max_price         = $min_max['max_price'];
			$min_regular_price = $min_max['min_regular_price'];
			$max_regular_price = $min_max['max_regular_price'];

			if ( 'variable' === $type || 'variable-subscription' === $type ) {
				$is_free = (float) 0 === (float) $min_price && (float) 0 === (float) $max_price;

				if ( $product->is_on_sale() ) {

					$displayed_price = ( function_exists( 'wc_get_price_to_display' )
						? wc_format_sale_price( $min_regular_price, $min_price )
						: '<del>' . ( is_numeric( $min_regular_price ) ? wc_price( (float) $min_regular_price ) : $min_regular_price ) . '</del> <ins>' . ( is_numeric( $min_price ) ? wc_price( (float) $min_price ) : $min_price ) . '</ins>'
					);
					$price           = $min_price !== $max_price
						? ( ! $use_from
							/* translators: %1 %2: from price to price  */
							? sprintf( esc_html_x( '%1$s&ndash;%2$s', 'Price range: from-to', 'woocommerce' ), themecomplete_price( $min_price ), themecomplete_price( $max_price ) )
							: ( function_exists( 'wc_get_price_html_from_text' ) ? wc_get_price_html_from_text() : $product->get_price_html_from_text() ) . $displayed_price )
						: $displayed_price;

					$regular_price = $min_regular_price !== $max_regular_price
						? ( ! $use_from
							/* translators: %1 %2: from price to price  */
							? sprintf( esc_html_x( '%1$s&ndash;%2$s', 'Price range: from-to', 'woocommerce' ), themecomplete_price( $min_regular_price ), themecomplete_price( $max_regular_price ) )
							: ( function_exists( 'wc_get_price_html_from_text' ) ? wc_get_price_html_from_text() : $product->get_price_html_from_text() ) . themecomplete_price( $min_regular_price ) )
						: themecomplete_price( $min_regular_price );
					$regular_price = '<del>' . $regular_price . '</del>';
					if ( $min_price === $max_price && $min_regular_price === $max_regular_price ) {
						$price = themecomplete_price( $max_price );
					}
					$price = ( ! $use_from ? ( $regular_price . ' <ins>' . $price . '</ins>' ) : $price ) . $product->get_price_suffix();

				} elseif ( $is_free ) {
					$price = apply_filters( 'woocommerce_variable_free_price_html', $free_text, $product );
				} else {
					$price = $min_price !== $max_price
						? ( ! $use_from
							/* translators: %1 %2: from price to price  */
							? sprintf( esc_html_x( '%1$s &ndash; %2$s', 'Price range: from-to', 'woocommerce' ), themecomplete_price( $min_price ), themecomplete_price( $max_price ) )
							: ( function_exists( 'wc_get_price_html_from_text' ) ? wc_get_price_html_from_text() : $product->get_price_html_from_text() ) . themecomplete_price( $min_price ) )
						: themecomplete_price( $min_price );
					$price = $price . $product->get_price_suffix();
				}
			} else {

				$display_price         = $min_price;
				$display_regular_price = $min_regular_price;

				$price = '';
				if ( $this->tc_get_price( $product ) > 0 ) {

					if ( $product->is_on_sale() && $this->tc_get_regular_price( $product ) ) {
						if ( $use_from && ( $max > 0 || $max > $min ) ) {

							$displayed_price = ( function_exists( 'wc_get_price_to_display' )
								? wc_format_sale_price( $display_regular_price, $display_price )
								: '<del>' . ( is_numeric( $display_regular_price ) ? wc_price( (float) $display_regular_price ) : $display_regular_price ) . '</del> <ins>' . ( is_numeric( $display_price ) ? wc_price( (float) $display_price ) : $display_price ) . '</ins>'
							);
							$price          .= ( function_exists( 'wc_get_price_html_from_text' )
									? wc_get_price_html_from_text()
									: $product->get_price_html_from_text() )
												. $displayed_price;
							$price          .= $product->get_price_suffix();
						} else {
							$price .= $original_price;
						}
					} else {
						if ( $use_from && ( $max > 0 || $max > $min ) ) {
							$price .= ( function_exists( 'wc_get_price_html_from_text' ) ? wc_get_price_html_from_text() : $product->get_price_html_from_text() );
						}
						$price .= themecomplete_price( $display_price ) . $product->get_price_suffix();

					}
				} elseif ( $this->tc_get_price( $product ) === '' ) {

					$price = apply_filters( 'woocommerce_empty_price_html', '', $product );

				} elseif ( (float) $this->tc_get_price( $product ) === (float) 0 ) {
					if ( $product->is_on_sale() && $this->tc_get_regular_price( $product ) ) {
						if ( $use_from && ( $max > 0 || $max > $min ) ) {
							$price .= ( function_exists( 'wc_get_price_html_from_text' ) ? wc_get_price_html_from_text() : $product->get_price_html_from_text() ) . themecomplete_price( ( $min > 0 ) ? $min : 0 );
						} else {

							$price .= $original_price;

							$price = apply_filters( 'woocommerce_free_sale_price_html', $price, $product );
						}
					} elseif ( $use_from && ( $max > 0 || $max > $min ) ) {
						$price .= ( function_exists( 'wc_get_price_html_from_text' ) ? wc_get_price_html_from_text() : $product->get_price_html_from_text() ) . themecomplete_price( ( $min > 0 ) ? $min : 0 );
					} else {
						$price = '<span class="amount">' . $free_text . '</span>';
						$price = apply_filters( 'woocommerce_free_price_html', $price, $product );
					}
				}
			}
		}

		return apply_filters( 'wc_epo_get_price_html', $price, $product );
	}

	/**
	 * Image filter
	 *
	 * @param mixed $url The image url.
	 * @return string|array<mixed>
	 * @since 1.0
	 */
	public function tm_image_url( $url = '' ) {
		if ( is_array( $url ) ) {
			foreach ( $url as $url_key => $url_value ) {
				if ( ! is_array( $url_value ) ) {
					$url[ $url_key ] = $this->get_cdn_url( $url_value );
				}
			}
		} else {
			$url = $this->get_cdn_url( $url );
		}

		// SSL support.
		$url = THEMECOMPLETE_EPO_HELPER()->to_ssl( $url );

		return $url;
	}

	/**
	 * Get cdn url
	 *
	 * @param mixed $url The cdn url.
	 * @return string
	 * @since 6.0
	 */
	public function get_cdn_url( $url = '' ) {
		if ( is_admin() || is_array( $url ) ) {
			return $url;
		}

		$ext = strtolower( pathinfo( $url, PATHINFO_EXTENSION ) );

		if ( 'php' === $ext ) {
			return $url;
		}

		// WP Rocket cdn.
		if ( 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_cdn_rocket' ) && defined( 'WP_ROCKET_VERSION' ) && function_exists( 'get_rocket_cdn_cnames' ) && function_exists( 'get_rocket_cdn_url' ) ) {
			$zone   = [ 'all', 'images' ];
			$cnames = get_rocket_cdn_cnames( $zone );
			if ( $cnames ) {
				$url = get_rocket_cdn_url( $url, $zone );
			}
		}

		// Jetpack cdn.
		if ( 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_cdn_jetpack' ) && function_exists( 'jetpack_photon_url' ) && is_string( $url ) ) {
			$url = jetpack_photon_url( $url );
		}

		return $url;
	}

	/**
	 * Flag related products start
	 *
	 * @return void
	 * @since 1.0
	 */
	public function tm_enable_post_class() {
		$this->tm_related_products_output = true;
	}

	/**
	 * Flag related products end
	 *
	 * @return void
	 * @since 1.0
	 */
	public function tm_disable_post_class() {
		$this->tm_related_products_output = false;
	}

	/**
	 * Flag related upsells start
	 *
	 * @param array<mixed> $args Array of arguments.
	 * @return array<mixed>
	 * @since 1.0
	 */
	public function tm_woocommerce_related_products_args( $args ) {
		$this->tm_disable_post_class();
		$this->in_related_upsells = true;

		return $args;
	}

	/**
	 * Flag related upsells end
	 *
	 * @return void
	 * @since 1.0
	 */
	public function tm_woocommerce_after_single_product_summary() {
		$this->in_related_upsells = false;
	}

	/**
	 * Add custom class to the body tag
	 *
	 * @param array<string> $classes Array of classes.
	 * @return array<string>
	 * @since 1.0
	 */
	public function tm_body_class( $classes = [] ) {
		$post_id = get_the_ID();

		if (
			// disable in admin interface.
			is_admin() ||

			// disable if not in the product div.
			! $this->tm_related_products_output ||

			// disable if not correct post id.
			false === $post_id ||

			// disable if not in a product page, shop or product archive page.
			! (
				'product' === get_post_type( $post_id ) ||
				$this->wc_vars['is_product'] ||
				$this->wc_vars['is_shop'] ||
				$this->wc_vars['is_cart'] ||
				$this->wc_vars['is_product_category'] ||
				$this->wc_vars['is_product_tag']
			) ||

			// disable if options are not visible in shop/archive pages.
			( (
				$this->wc_vars['is_shop'] ||
				$this->wc_vars['is_product_category'] ||
				$this->wc_vars['is_product_tag']
			)
			&&
			'no' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_enable_in_shop' )
			)

		) {
			return $classes;
		}

		if ( 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_responsive_display' ) ) {
			$classes[] = 'tm-responsive';
		}

		return $classes;
	}

	/**
	 * Add custom class to product div used to initialize the plugin JavaScript
	 *
	 * @param array<string> $classes Array of classes.
	 * @return array<string>
	 * @since 1.0
	 */
	public function tm_post_class( $classes = [] ) {
		$post_id = get_the_ID();

		if (
			// Disable if filter runs for more than one time.
			did_filter( 'post_class' ) > 1 ||

			// disable in admin interface.
			is_admin() ||

			// disable if not correct post id.
			false === $post_id ||

			// disable if not in the product div.
			! $this->tm_related_products_output ||

			// disable if not in a product page, shop or product archive page.
			! (
				'product' === get_post_type( $post_id ) ||
				$this->wc_vars['is_product'] ||
				$this->wc_vars['is_shop'] ||
				$this->wc_vars['is_product_category'] ||
				$this->wc_vars['is_product_tag']
			) ||

			// disable if options are not visible in shop/archive pages.
			( (
				$this->wc_vars['is_shop'] ||
				$this->wc_vars['is_product_category'] ||
				$this->wc_vars['is_product_tag']
			)
			&&
			'no' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_enable_in_shop' )
			)

		) {
			return $classes;
		}

		// enabling "global $post;" here will cause issues on certain Visual composer shortcodes.

		if ( $post_id && ( $this->wc_vars['is_product'] || 'product' === get_post_type( $post_id ) ) ) {

			$has_epo = THEMECOMPLETE_EPO_API()->has_options( $post_id );

			// Product has styled variations.
			if ( ! empty( $has_epo['variations'] ) && empty( $has_epo['variations_disabled'] ) ) {
				$classes[] = 'tm-has-styled-variations';
			}

			// Product has extra options.
			if ( THEMECOMPLETE_EPO_API()->is_valid_options( $has_epo ) ) {
				$classes[] = 'tm-has-options';

				// Product doesn't have extra options but the final total box is enabled for all products.
			} elseif ( 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_enable_final_total_box_all' ) ) {

				$classes[] = 'tm-no-options-pxq';

				// Search for composite products extra options.
			} else {

				$extra_classes = apply_filters( 'wc_epo_tm_post_class_no_options', [], $post_id );

				if ( ! empty( $extra_classes ) ) {
					$classes = array_merge( $classes, $extra_classes );
				} elseif ( isset( $has_epo['variations'] ) && ! empty( $has_epo['variations'] ) && isset( $has_epo['variations_disabled'] ) && empty( $has_epo['variations_disabled'] ) ) {
					$classes[] = 'tm-variations-only';
				} else {
					$classes[] = 'tm-no-options';

					$this->hasoptions[ $post_id ] = false;
				}
			}
		}

		return $classes;
	}

	/**
	 * Check if we are in edit mode
	 *
	 * @return boolean
	 * @since 1.0
	 */
	public function is_edit_mode() {
		return ! empty( $this->cart_edit_key ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( wp_unslash( $_GET['_wpnonce'] ), 'tm-edit' ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	}

	/**
	 * Check if the plugin is active for the user
	 *
	 * @return boolean
	 * @since 1.0
	 */
	public function check_enable() {
		$enable         = false;
		$enabled_roles  = THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_roles_enabled' );
		$disabled_roles = THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_roles_disabled' );

		if ( isset( $this->tm_meta_cpf['override_enabled_roles'] ) && '' !== $this->tm_meta_cpf['override_enabled_roles'] ) {
			$enabled_roles = $this->tm_meta_cpf['override_enabled_roles'];
		}
		if ( isset( $this->tm_meta_cpf['override_disabled_roles'] ) && '' !== $this->tm_meta_cpf['override_disabled_roles'] ) {
			$disabled_roles = $this->tm_meta_cpf['override_disabled_roles'];
		}
		// Get all roles.
		$current_user = wp_get_current_user();

		if ( ! is_array( $enabled_roles ) ) {
			$enabled_roles = [ $enabled_roles ];
		}
		if ( ! is_array( $disabled_roles ) ) {
			$disabled_roles = [ $disabled_roles ];
		}

		// Check if plugin is enabled for everyone.
		foreach ( $enabled_roles as $key => $value ) {
			if ( '@everyone' === $value ) {
				$enable = true;
			}
			if ( '@loggedin' === $value && is_user_logged_in() ) {
				$enable = true;
			}
		}

		if ( $current_user instanceof WP_User ) {
			$roles = $current_user->roles;
			// Check if plugin is enabled for current user.
			if ( is_array( $roles ) ) {

				foreach ( $roles as $key => $value ) {
					if ( in_array( $value, $enabled_roles, true ) ) {
						$enable = true;
						break;
					}
				}

				foreach ( $roles as $key => $value ) {
					if ( in_array( $value, $disabled_roles, true ) ) {
						$enable = false;
						break;
					}
				}
			}
		}

		return $enable;
	}

	/**
	 * Check if we are on a supported quickview mode
	 *
	 * @return boolean
	 * @since 1.0
	 */
	public function is_quick_view() {
		return apply_filters( 'woocommerce_tm_quick_view', false );
	}

	/**
	 * Check if the setting "Enable plugin for WooCommerce shortcodes" is active
	 *
	 * @return boolean
	 * @since 1.0
	 */
	public function is_enabled_shortcodes() {
		return 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_enable_shortcodes' );
	}

	/**
	 * Apply wc_epo_get_current_currency_price filter to prices
	 *
	 * @param mixed  $price The option price.
	 * @param string $type The option type.
	 * @return mixed
	 * @since 1.0
	 */
	public function tm_epo_price_filtered( $price = '', $type = '' ) {
		return apply_filters( 'wc_epo_get_current_currency_price', $price, $type );
	}

	/**
	 * Enable shortcodes for labels
	 *
	 * @param string            $label The element label.
	 * @param array<mixed>|null $args The element array.
	 * @param integer|null      $counter The choice counter.
	 * @param string|null       $value The choice value.
	 * @param string|null       $vlabel The choice label.
	 * @return mixed
	 * @since 1.0
	 */
	public function tm_epo_option_name( $label = '', $args = null, $counter = null, $value = null, $vlabel = null ) {
		if ( ( null === $this->associated_per_product_pricing || 1 === $this->associated_per_product_pricing ) &&
			'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_show_price_inside_option' ) &&
			( empty( $args['hide_amount'] ) || 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_show_price_inside_option_hidden_even' ) ) &&
			null !== $value &&
			null !== $vlabel &&
			isset( $args['rules_type'] ) &&
			isset( $args['rules_type'][ $value ] ) &&
			isset( $args['rules_type'][ $value ][0] ) &&
			empty( $args['rules_type'][ $value ][0] )
		) {
			$display_price = ( isset( $args['rules_filtered'][ $value ][0] ) ) ? $args['rules_filtered'][ $value ][0] : '';
			$qty           = 1;

			if ( 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_multiply_price_inside_option' ) ) {
				if ( ! empty( $args['quantity'] ) && ! empty( $args['quantity_default_value'] ) ) {
					$qty = floatval( $args['quantity_default_value'] );
				}
			}
			$display_price = floatval( $display_price ) * $qty;

			if ( ( 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_auto_hide_price_if_zero' ) && ! empty( $display_price ) ) || ( 'yes' !== THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_auto_hide_price_if_zero' ) && '' !== $display_price ) ) {
				$symbol = '';
				if ( '' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_options_price_sign' ) ) {
					$symbol = apply_filters( 'wc_epo_price_in_dropdown_plus_sign', '+' );
				}

				global $product, $associated_product;
				$current_product = $product;
				if ( ! $product && $associated_product ) {
					$current_product = $associated_product;
				}
				if ( $current_product && wc_tax_enabled() ) {
					$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );

					if ( 'excl' === $tax_display_mode ) {
						$display_price = themecomplete_get_price_excluding_tax( $current_product, [ 'price' => $display_price ] );
					} else {
						$display_price = themecomplete_get_price_including_tax( $current_product, [ 'price' => $display_price ] );
					}
				}

				if ( '0' === (string) $display_price ) {
					$symbol = '';
				} elseif ( floatval( $display_price ) < 0 ) {
					$symbol = apply_filters( 'wc_epo_price_in_dropdown_minus_sign', '-' );
				}
				if ( $display_price ) {
					$display_price = apply_filters( 'wc_epo_price_in_dropdown', ' (' . $symbol . wc_price( (float) $display_price ) . ')', $display_price );
				} else {
					$display_price = '';
				}

				$label .= $display_price;

			}
		}

		return apply_filters( 'wc_epo_label', apply_filters( 'wc_epo_kses', $label, $label ) );
	}

	/**
	 * Alters the Free label html
	 *
	 * @param mixed $price The price html.
	 * @param mixed $product The product instance.
	 * @return mixed
	 * @since 1.0
	 */
	public function get_price_html( $price = '', $product = '' ) {
		if ( $product && is_object( $product ) && is_callable( [ $product, 'get_price' ] ) ) {
			if ( (float) $product->get_price() > 0 ) {
				return $price;
			} else {
				return sprintf( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_replacement_free_price_text' ), $price );
			}
		} else {
			return sprintf( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_replacement_free_price_text' ), $price );
		}
	}

	/**
	 * Fix for related products when replacing free label
	 *
	 * @param mixed $price The price html.
	 * @param mixed $product The product instance.
	 * @return mixed
	 * @since 1.0
	 */
	public function related_get_price_html( $price = '', $product = '' ) {
		if ( $product && is_object( $product ) && is_callable( [ $product, 'get_price' ] ) ) {
			if ( (float) $product->get_price() > 0 ) {
				return $price;
			} elseif ( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_replacement_free_price_text' ) ) {
				return sprintf( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_replacement_free_price_text' ), $price );
			} else {
				$price = '';
			}
		} elseif ( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_replacement_free_price_text' ) ) {
			return sprintf( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_replacement_free_price_text' ), $price );
		} else {
			$price = '';
		}

		return $price;
	}

	/**
	 * Fix for related products when replacing free label
	 *
	 * @param mixed $price The price html.
	 * @param mixed $product The product instance.
	 * @return mixed
	 * @since 1.0
	 */
	public function related_get_price_html2( $price = '', $product = '' ) {
		if ( $product && is_object( $product ) && is_callable( [ $product, 'get_price' ] ) ) {

			if ( (float) $product->get_price() > 0 ) {
				return $price;
			} else {

				$thiscpf = $this->get_product_tm_epos( themecomplete_get_id( $product ), '', false, true );

				if ( is_array( $thiscpf ) && ( ! empty( $thiscpf['global'] ) || ! empty( $thiscpf['local'] ) ) ) {
					if ( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_replacement_free_price_text' ) ) {
						return sprintf( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_replacement_free_price_text' ), $price );
					} else {
						$price = '';
					}
				}
			}
		}

		return $price;
	}

	/**
	 * Free label text replacement
	 *
	 * @param mixed $price The price html.
	 * @param mixed $product The product instance.
	 * @return mixed
	 * @since 1.0
	 */
	public function get_price_html_shop( $price = '', $product = '' ) {
		if ( $product &&
			is_object( $product ) && is_callable( [ $product, 'get_price' ] )
			&& ! (float) $product->get_price() > 0
		) {

			if ( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_replacement_free_price_text' ) ) {
				$price = sprintf( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_replacement_free_price_text' ), $price );
			} else {
				$price = '';
			}
		}

		return $price;
	}

	/**
	 * Replaces add to cart text when the force select setting is enabled
	 *
	 * @param string $text The add to cart text.
	 * @return string
	 * @since 1.0
	 */
	public function add_to_cart_text( $text = '' ) {
		global $product;

		if ( ( is_product() && ! $this->in_related_upsells ) || $this->is_in_product_shortcode ) {
			return $text;
		}
		if ( 'no' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_enable_in_shop' )
			&& 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_force_select_options' )
			&& is_object( $product )
			&& property_exists( $product, 'id' )
		) {
			$has_epo = THEMECOMPLETE_EPO_API()->has_options( themecomplete_get_id( $product ) );
			if ( THEMECOMPLETE_EPO_API()->is_valid_options( $has_epo ) ) {
				$text = ( ! empty( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_force_select_text' ) ) ) ? esc_html( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_force_select_text' ) ) : esc_html__( 'Select options', 'woocommerce-tm-extra-product-options' );
			}
		}
		if ( 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_enable_in_shop' ) && ! $this->in_related_upsells ) {
			$text = esc_html__( 'Add to cart', 'woocommerce' );
		}

		return $text;
	}

	/**
	 * Prevenets ajax add to cart when product has extra options and the force select setting is enabled
	 *
	 * @param string $url The url.
	 * @return string
	 * @since 1.0
	 */
	public function add_to_cart_url( $url = '' ) {
		global $product;

		if ( ! is_product()
			&& 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_force_select_options' )
			&& is_object( $product )
			&& property_exists( $product, 'id' )
		) {
			$has_epo = THEMECOMPLETE_EPO_API()->has_options( themecomplete_get_id( $product ) );
			if ( THEMECOMPLETE_EPO_API()->is_valid_options( $has_epo ) ) {
				$product_url = get_permalink( themecomplete_get_id( $product ) );
				if ( false !== $product_url ) {
					$url = $product_url;
				}
			}
		}

		return $url;
	}

	/**
	 * Redirect to product URL
	 * THis is used when using the forced select setting
	 *
	 * @param string  $url The url to redirect to.
	 * @param integer $product_id The product id.
	 * @return string
	 * @since 1.0
	 */
	public function woocommerce_cart_redirect_after_error( $url = '', $product_id = 0 ) {
		$product = wc_get_product( $product_id );

		if ( 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_force_select_options' )
			&& is_object( $product )
			&& property_exists( $product, 'id' )
		) {
			$has_epo = THEMECOMPLETE_EPO_API()->has_options( themecomplete_get_id( $product ) );
			if ( THEMECOMPLETE_EPO_API()->is_valid_options( $has_epo ) ) {
				$product_url = get_permalink( themecomplete_get_id( $product ) );
				if ( false !== $product_url ) {
					$url = $product_url;
				}
			}
		}

		return $url;
	}

	/**
	 * Sets current product settings
	 *
	 * @param integer $override_id Set meta or not.
	 * @return void
	 * @since 1.0
	 */
	public function set_tm_meta( $override_id = 0 ) {
		if ( empty( $override_id ) ) {
			if ( isset( $_REQUEST['add-to-cart'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$override_id = absint( wp_unslash( $_REQUEST['add-to-cart'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			} else {
				global $post;
				if ( ! is_null( $post ) && property_exists( $post, 'ID' ) && property_exists( $post, 'post_type' ) ) {
					if ( 'product' !== $post->post_type ) {
						return;
					}
					$override_id = $post->ID;
				}
			}
		}
		if ( empty( $override_id ) ) {
			return;
		}

		// Translated products inherit original product meta overrides.
		$override_id = absint( THEMECOMPLETE_EPO_WPML()->get_original_id( $override_id, 'product' ) );

		$this->tm_meta_cpf = themecomplete_get_post_meta( $override_id, 'tm_meta_cpf', true );
		if ( ! is_array( $this->tm_meta_cpf ) ) {
			$this->tm_meta_cpf = [];
		}
		foreach ( $this->meta_fields as $key => $value ) {
			$this->tm_meta_cpf[ $key ] = isset( $this->tm_meta_cpf[ $key ] ) ? $this->tm_meta_cpf[ $key ] : $value;
		}
		$this->tm_meta_cpf['metainit'] = 1;
	}

	/**
	 * Calculates the formula price
	 *
	 * @param string                   $_price The math formula.
	 * @param array<mixed>             $post_data The posted data.
	 * @param array<mixed>             $element The element array.
	 * @param string                   $key The posted element value.
	 * @param string|false             $attribute The posted element name.
	 * @param float|array<mixed>|false $attribute_quantity The option quantity of this element.
	 * @param integer                  $key_id The array key of the posted element values array.
	 * @param integer                  $keyvalue_id The array key for the values of the posted element values array.
	 * @param boolean                  $per_product_pricing If the product has pricing, true or false.
	 * @param mixed                    $cpf_product_price The product price.
	 * @param integer|false            $variation_id The variation id.
	 * @param integer                  $price_default_value The value to return if the formula fails.
	 * @param string|false             $currency The currency to set the result to.
	 * @param string|false             $current_currency The current currency.
	 * @param array<mixed>             $price_per_currencies The price per currencies array.
	 * @param array<mixed>|false       $tmcp Saved element data.
	 * @param array<mixed>|false       $cart_meta_epo_type Saved cart meta epo type data.
	 * @param array<mixed>             $tmdata Saved tmdata array.
	 * @param mixed                    $func_total Current options total if applicable.
	 * @param mixed                    $cumulative_total Current cumulative total if applicable.
	 * @return mixed
	 * @since 1.0
	 */
	public function calculate_math_price( $_price = '', $post_data = [], $element = [], $key = '', $attribute = false, $attribute_quantity = false, $key_id = 0, $keyvalue_id = 0, $per_product_pricing = null, $cpf_product_price = false, $variation_id = 0, $price_default_value = 0, $currency = false, $current_currency = false, $price_per_currencies = null, $tmcp = false, $cart_meta_epo_type = false, $tmdata = [], $func_total = false, $cumulative_total = false ) {
		$formula = $_price;

		// This happens when the user has prevented the totals box from being displayed.
		if ( ! isset( $post_data['tc_form_prefix'] ) ) {
			$post_data['tc_form_prefix'] = '';
		}

		$form_prefix = $post_data['tc_form_prefix'];

		if ( false !== $this->associated_element_uniqid && isset( $post_data['tc_form_prefix_assoc'] ) && isset( $post_data['tc_form_prefix_assoc'][ $this->associated_element_uniqid ] ) ) {
			$form_prefix = $post_data['tc_form_prefix_assoc'][ $this->associated_element_uniqid ];
			if ( is_array( $form_prefix ) ) {
				$form_prefix = THEMECOMPLETE_EPO()->associated_product_formprefix;
			}
		}
		if ( '' !== $form_prefix ) {
			$form_prefix = str_replace( '_', '', $form_prefix );
			$form_prefix = '_' . $form_prefix;
		}

		$current_id         = $element['uniqid'] . $form_prefix;
		$current_attributes = THEMECOMPLETE_EPO_CART()->element_id_array[ $current_id ]['name_inc'];
		if ( $current_attributes ) {
			if ( ! is_array( $current_attributes ) ) {
				$current_attributes = [ $current_attributes ];
			}
		} else {
			$current_attributes = [];
		}

		// constants.
		$constants = json_decode( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_math' ), true );
		if ( is_array( $constants ) ) {
			foreach ( $constants as $constant ) {
				if ( str_starts_with( $constant['name'], '{' ) ) {
					$formula = str_replace( $constant['name'], $constant['value'], $formula );
				} elseif ( '' !== $constant['name'] && '' !== $constant['value'] ) {
					if ( str_starts_with( $constant['value'], '{' ) ) {
						$formula = str_replace( '{' . $constant['name'] . '}', $constant['value'], $formula );
					} else {
						$formula = str_replace( '{' . $constant['name'] . '}', THEMECOMPLETE_EPO_HELPER()->convert_to_number( floatval( themecomplete_convert_local_numbers( $constant['value'] ) ), true ), $formula );
					}
				}
			}
		}

		// Replaces any number between curly braces with the current currency.
		$formula = preg_replace_callback(
			'/\{(\d+)\}/u',
			function ( $matches ) {
				return apply_filters( 'wc_epo_get_currency_price', $matches[1], false, '' );
			},
			$formula
		);

		// the number of options the user has selected.
		$formula = str_replace( '{this.count}', strval( count( array_intersect_key( $post_data, array_flip( $current_attributes ) ) ) ), $formula );

		// the total option quantity of this element.
		$current_attributes_quantity = array_map(
			function ( $y ) {
				return $y . '_quantity';
			},
			$current_attributes
		);
		$quantity_intersect          = array_intersect_key( $post_data, array_flip( $current_attributes_quantity ) );
		$quantity_intersect          = array_map(
			function ( $y ) {
				if ( is_array( $y ) ) {
					$y = array_sum(
						array_map(
							function ( $x ) {
								if ( is_array( $x ) ) {
									$x = array_sum( $x );
								} return $x;
							},
							$y
						)
					);
				} return $y;
			},
			$quantity_intersect
		);

		$formula = str_replace( '{this.count.quantity}', strval( array_sum( (array) $quantity_intersect ) ), $formula );

		// the option quantity of this element.
		$current_quantity = '';
		if ( false === $attribute_quantity ) {
			if ( isset( $post_data[ $attribute . '_quantity' ] ) ) {
				$attribute_quantity = $post_data[ $attribute . '_quantity' ];
			}
		}
		if ( is_array( $attribute_quantity ) && isset( $attribute_quantity[ $key_id ] ) ) {
			$attribute_quantity = $attribute_quantity[ $key_id ];
			if ( is_array( $attribute_quantity ) && isset( $attribute_quantity[ $keyvalue_id ] ) ) {
				$attribute_quantity = $attribute_quantity[ $keyvalue_id ];
			}
		}
		$current_quantity = $attribute_quantity;

		// the option/element quantity.
		$formula = str_replace( '{this.quantity}', THEMECOMPLETE_EPO_HELPER()->convert_to_number( floatval( $current_quantity ), true ), $formula );

		if ( isset( $element['options'] ) && isset( $element['options'][ $key ] ) ) {
			// the option/element value to float.
			$formula = str_replace( '{this.value}', THEMECOMPLETE_EPO_HELPER()->convert_to_number( floatval( $element['options'][ $key ] ), true ), $formula );
			// the option/element value.
			$formula = str_replace( '{this.rawvalue}', $element['options'][ $key ], $formula );
			// the option/element value.
			$formula = str_replace( '{this.text}', $element['options'][ $key ], $formula );
			// the option/element value length.
			$formula = str_replace( '{this.value.length}', THEMECOMPLETE_EPO_HELPER()->convert_to_number( floatval( tc_strlen( $element['options'][ $key ] ) ), true ), $formula );
		} else {
			// the option/element value.
			if ( isset( $post_data[ $attribute ] ) ) {
				$attribute_value = $post_data[ $attribute ];
			} elseif ( isset( $tmdata['tmcp_post_fields'][ $attribute ] ) ) {
				$attribute_value = $tmdata['tmcp_post_fields'][ $attribute ];
			} elseif ( isset( $_REQUEST[ $attribute ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$attribute_value = wp_unslash( $_REQUEST[ $attribute ] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			} elseif ( isset( $_FILES[ $attribute ] ) && isset( $_FILES[ $attribute ]['name'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				$attribute_value = wp_unslash( $_FILES[ $attribute ]['name'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification
			} else {
				$attribute_value = '';
			}

			if ( is_array( $attribute_value ) && isset( $attribute_value[ $key_id ] ) ) {
				$attribute_value = $attribute_value[ $key_id ];
				if ( is_array( $attribute_value ) && isset( $attribute_value[ $keyvalue_id ] ) ) {
					$attribute_value = $attribute_value[ $keyvalue_id ];
				}
			}
			// the option/element value to float.
			$formula = str_replace( '{this.value}', THEMECOMPLETE_EPO_HELPER()->convert_to_number( floatval( $attribute_value ), true ), $formula );
			// the option/element value.
			$formula = str_replace( '{this.rawvalue}', $attribute_value, $formula );
			// the option/element value.
			$formula = str_replace( '{this.text}', $attribute_value, $formula );
			// the option/element value length.
			$formula = str_replace( '{this.value.length}', THEMECOMPLETE_EPO_HELPER()->convert_to_number( floatval( tc_strlen( $attribute_value ) ), true ), $formula );
		}

		// product quantity.
		if ( isset( $post_data['quantity'] ) ) {
			$formula = str_replace( '{quantity}', THEMECOMPLETE_EPO_HELPER()->convert_to_number( floatval( $post_data['quantity'] ), true ), $formula );
		}

		// original product price.
		$product_price = $cpf_product_price;
		if ( ! $product_price ) {
			$product_price = 0;
		}
		$product_price = THEMECOMPLETE_EPO_HELPER()->convert_to_number( floatval( $product_price ), true );

		$formula = str_replace( '{product_price}', $product_price, $formula );
		if ( isset( $post_data['dynamic_product_price'] ) ) {
			$dynamic_product_price = THEMECOMPLETE_EPO_HELPER()->convert_to_number( floatval( $post_data['dynamic_product_price'] ), true );
			$formula               = str_replace( '{dynamic_product_price}', $dynamic_product_price, $formula );
		}

		if ( ! $func_total ) {
			$func_total = 0;
		}
		$func_total = THEMECOMPLETE_EPO_HELPER()->convert_to_number( floatval( $func_total ), true );

		if ( ! $cumulative_total ) {
			$cumulative_total = 0;
		}
		$cumulative_total = THEMECOMPLETE_EPO_HELPER()->convert_to_number( floatval( $cumulative_total ), true );

		$product_price_plus_func_total       = $product_price + $func_total;
		$product_price_plus_cumulative_total = $product_price + $cumulative_total;

		$formula = str_replace( '{options_total}', $func_total, $formula );
		$formula = str_replace( '{product_price_plus_options_total}', $product_price_plus_func_total, $formula );

		$formula = str_replace( '{cumulative_total}', $cumulative_total, $formula );
		$formula = str_replace( '{product_price_plus_cumulative_total}', $product_price_plus_cumulative_total, $formula );

		preg_match_all( '/\{(\s)*?field\.([^}]*)}/', $formula, $matches );

		if ( is_array( $matches ) && isset( $matches[2] ) && is_array( $matches[2] ) ) {

			foreach ( $matches[2] as $matchkey => $match ) {
				$val  = 0;
				$type = '';
				$pos  = strrpos( $match, '.' );

				if ( false !== $pos ) {

					$id     = substr( $match, 0, $pos );
					$pos_id = strrpos( $id, '.' );
					if ( substr_count( $id, '.' ) > 1 && false !== $pos_id ) {
						$id  = substr( $id, 0, $pos_id );
						$pos = $pos_id;
					}
					$id   = $id . $form_prefix;
					$type = substr( $match, $pos + 1 );
					if ( 'text' === $type || 'rawvalue' === $type ) {
						$val = '';
					}

					$thiselement = isset( THEMECOMPLETE_EPO_CART()->element_id_array[ $id ] ) ? THEMECOMPLETE_EPO_CART()->element_id_array[ $id ] : null;
					if ( $thiselement ) {

						$priority              = $thiselement['priority'];
						$pid                   = $thiselement['pid'];
						$section_id            = $thiselement['section_id'];
						$element_key           = $thiselement['element_key'];
						$thiselement           = THEMECOMPLETE_EPO_CART()->global_price_array[ $priority ][ $pid ]['sections'][ $section_id ]['elements'][ $element_key ];
						$_price_per_currencies = isset( $thiselement['price_per_currencies'] ) ? $thiselement['price_per_currencies'] : [];

						$thisattributes = THEMECOMPLETE_EPO_CART()->element_id_array[ $id ]['name_inc'];
						if ( ! is_array( $thisattributes ) ) {
							$thisattributes = [ $thisattributes ];
						}

						$thisattributes = array_unique( $thisattributes );

						if ( is_array( $thisattributes ) ) {
							foreach ( $thisattributes as $thisattribute ) {
								if ( ! isset( $post_data[ $thisattribute ] ) ) {
									continue;
								}
								$thiskey = $post_data[ $thisattribute ];
								if ( in_array( $type, [ 'price', 'value', 'value.length', 'rawvalue', 'text', 'text.length', 'quantity', 'count.quantity', 'count' ], true ) ) {
									switch ( $type ) {
										case 'price':
											// When a dynamic element is linked to another element that is
											// priced with {dynamic_product_price} a circular dependency is created.
											$_price_type = $this->get_element_price_type( '', $thiselement, $thiskey, $per_product_pricing, $variation_id );
											$__price     = $this->get_element_price( '', $_price_type, $thiselement, $thiskey, $per_product_pricing, $variation_id );
											if ( is_array( $tmcp ) && isset( $tmcp['dynamic'] ) ) {
												if ( 'math' === $_price_type && str_contains( $__price, '{dynamic_product_price}' ) ) {
													continue 2;
												}
											}
											// The price types percentcurrenttotal and fixedcurrenttotal
											// create a circular dependency when used with the math formula.
											if ( 'percentcurrenttotal' !== $_price_type && 'fixedcurrenttotal' !== $_price_type ) {
												if ( is_array( $thiskey ) ) {
													foreach ( $thiskey as $thiskey_id => $thiskey_value ) {
														if ( ! is_array( $thiskey_value ) ) {
															$thiskey_value = [ $thiskey_value ];
														}
														foreach ( $thiskey_value as $thiskeyvalue_id => $thiskeyvalue_value ) {
															$cart_meta_epo_type_key = false;
															if ( is_array( $cart_meta_epo_type ) ) {
																foreach ( $cart_meta_epo_type as $ckey => $cvalue ) {
																	if ( $thiselement['uniqid'] === $cvalue['section'] ) {
																		$cart_meta_epo_type_key = $ckey;
																		break;
																	}
																}
															}
															$val += floatval( $this->calculate_price( $post_data, $thiselement, $thiskey, $thisattribute, false, $thiskey_id, $thiskeyvalue_id, $per_product_pricing, $cpf_product_price, $variation_id, $price_default_value, $currency, $current_currency, $_price_per_currencies, false !== $cart_meta_epo_type_key && is_array( $cart_meta_epo_type ) ? $cart_meta_epo_type[ $cart_meta_epo_type_key ] : false, $cart_meta_epo_type, $tmdata ) );
														}
													}
												} else {
													$cart_meta_epo_type_key = false;
													if ( is_array( $cart_meta_epo_type ) ) {
														foreach ( $cart_meta_epo_type as $ckey => $cvalue ) {
															if ( $thiselement['uniqid'] === $cvalue['section'] ) {
																$cart_meta_epo_type_key = $ckey;
																break;
															}
														}
													}
													$val += floatval( $this->calculate_price( $post_data, $thiselement, $thiskey, $thisattribute, false, 0, 0, $per_product_pricing, $cpf_product_price, $variation_id, $price_default_value, $currency, $current_currency, $_price_per_currencies, false !== $cart_meta_epo_type_key && is_array( $cart_meta_epo_type ) ? $cart_meta_epo_type[ $cart_meta_epo_type_key ] : false, $cart_meta_epo_type, $tmdata ) );
												}
											}
											break;
										case 'value':
										case 'text':
										case 'rawvalue':
											if ( is_array( $thiskey ) ) {
												foreach ( $thiskey as $thiskey_id => $thiskey_value ) {
													if ( ! is_array( $thiskey_value ) ) {
														$thiskey_value = [ $thiskey_value ];
													}
													foreach ( $thiskey_value as $thiskeyvalue_id => $thiskeyvalue_value ) {
														$temp_value = $thiskeyvalue_value;
														if ( isset( $thiselement['options'] ) && isset( $thiselement['options'][ $thiskeyvalue_value ] ) ) {
															$temp_value = $thiselement['options'][ $thiskeyvalue_value ];
														}
														if ( 'text' === $type || 'rawvalue' === $type ) {
															if ( '' === $temp_value ) {
																$temp_value = "''";
															} elseif ( ! is_numeric( $temp_value ) ) {
																$temp_value = "'" . $temp_value . "'";
															}
															$val .= $temp_value;
														} else {
															$val += THEMECOMPLETE_EPO_HELPER()->unformat( $temp_value );
														}
													}
												}
											} else {
												$temp_value = $thiskey;
												if ( isset( $thiselement['options'] ) && isset( $thiselement['options'][ $thiskey ] ) ) {
													$temp_value = $thiselement['options'][ $thiskey ];
												} else {
													if ( 'select' === $thiselement['type'] ) {
														$thiskey = THEMECOMPLETE_EPO_HELPER()->reverse_strrchr( $thiskey, '_' );
													}
													$temp_value = $thiskey;
												}
												if ( 'text' === $type || 'rawvalue' === $type ) {
													if ( '' === $temp_value ) {
														$temp_value = "''";
													} elseif ( ! is_numeric( $temp_value ) ) {
														$temp_value = "'" . $temp_value . "'";
													}
													$val .= $temp_value;
												} else {
													$val += THEMECOMPLETE_EPO_HELPER()->unformat( $temp_value );
												}
											}
											break;
										case 'value.length':
										case 'text.length':
											if ( is_array( $thiskey ) ) {
												foreach ( $thiskey as $thiskey_id => $thiskey_value ) {
													if ( ! is_array( $thiskey_value ) ) {
														$thiskey_value = [ $thiskey_value ];
													}
													foreach ( $thiskey_value as $thiskeyvalue_id => $thiskeyvalue_value ) {
														if ( isset( $thiselement['options'] ) && isset( $thiselement['options'][ $thiskeyvalue_value ] ) ) {
															$val += tc_strlen( $thiselement['options'][ $thiskeyvalue_value ] );
														} else {
															$val += tc_strlen( $thiskeyvalue_value );
														}
													}
												}
											} elseif ( isset( $thiselement['options'] ) && isset( $thiselement['options'][ $thiskey ] ) ) {
												$val += tc_strlen( $thiselement['options'][ $thiskey ] );
											} else {
												$val += tc_strlen( $thiskey );
											}
											break;
										case 'quantity':
										case 'count.quantity':
											if ( isset( $post_data[ $thisattribute . '_quantity' ] ) ) {
												$thisquantity = array_map(
													function ( $y ) {
														if ( is_array( $y ) ) {
															$y = array_sum(
																array_map(
																	function ( $x ) {
																		if ( is_array( $x ) ) {
																			$x = array_sum( $x );
																		} return $x;
																	},
																	$y
																)
															);
														} return $y;
													},
													(array) $post_data[ $thisattribute . '_quantity' ]
												);
												$val         += floatval( array_sum( $thisquantity ) );
											}
											break;
										case 'count':
											// Only for when radio buttons uses connector.
											if ( isset( $thiselement['connector'] ) && '' !== $thiselement['connector'] ) {
												$ppattern = '/_(\d+)(?:-(\d+))?$/';
												if ( preg_match( $ppattern, $thiskey, $mmatches ) ) {
													if ( isset( $matches[2] ) ) {
														$posted_value = THEMECOMPLETE_EPO_HELPER()->reverse_strrchr( $thiskey, '-' );
														if ( '' === $posted_value ) {
															$posted_value = $thiskey;
														}
													}
												}
												if ( in_array( $posted_value, array_keys( $thiselement['options'] ), true ) ) {
													++$val;
												}
											}
											break;
									}
								}
							}

							if ( 'count' === $type ) {
								// On all cases except connecting radio buttons.
								if ( ! ( isset( $thiselement['connector'] ) && '' !== $thiselement['connector'] ) ) {
									$val = floatval( count( array_intersect_key( $post_data, array_flip( $thisattributes ) ) ) );
								}
							}
						}
					}
				}
				if ( ! is_numeric( $val ) && ( 'text' === $type || 'rawvalue' === $type ) ) {
					// This can happen if the value of the element is not posted, like a radio button that is not selected.
					if ( '' === $val ) {
						$val = "''";
					}
					$formula = str_replace( $matches[0][ $matchkey ], $val, $formula );
				} else {
					$val     = THEMECOMPLETE_EPO_HELPER()->convert_to_number( $val, true );
					$formula = str_replace( $matches[0][ $matchkey ], $val, $formula );
				}
			}
		}

		$formula = themecomplete_convert_local_numbers( $formula );

		// Do the math.
		if ( version_compare( phpversion(), THEMECOMPLETE_EPO_PHP_VERSION, '<' ) ) {
			return $formula ? THEMECOMPLETE_EPO_MATH_DEPRECATED::evaluate( $formula ) : 0;
		}
		return $formula ? THEMECOMPLETE_EPO_MATH::evaluate( $formula ) : 0;
	}

	/**
	 * Get element's saved price type
	 *
	 * @param array<mixed> $tmcp Saved element data.
	 * @param mixed        $key The posted element value.
	 *
	 * @return string
	 */
	public function get_saved_element_price_type( $tmcp = [], $key = false ) {
		$price_type = '';
		$key        = ( false !== $key ) ? $key : ( isset( $tmcp['key'] ) ? $tmcp['key'] : 0 );
		$key        = esc_attr( $key );

		if ( ! isset( $tmcp['element']['rules_type'][ $key ] ) ) {// field price rule.
			if ( isset( $tmcp['element']['rules_type'][0][0] ) ) {// general rule.
				$price_type = $tmcp['element']['rules_type'][0][0];
			}
		} elseif ( isset( $tmcp['element']['rules_type'][ $key ][0] ) ) {// general field variation rule.
			$price_type = $tmcp['element']['rules_type'][ $key ][0];
		} elseif ( isset( $tmcp['element']['rules_type'][0][0] ) ) {// general rule.
			$price_type = $tmcp['element']['rules_type'][0][0];
		}

		return $price_type;
	}

	/**
	 * Get element's saved price
	 *
	 * @param array<mixed> $tmcp Saved element data.
	 * @param array<mixed> $element The element array.
	 * @param string|false $key The posted element value.
	 *
	 * @return string
	 */
	public function get_saved_element_price( $tmcp = [], $element = [], $key = false ) {
		$price = '';
		$key   = ( false !== $key ) ? $key : ( isset( $tmcp['key'] ) ? $tmcp['key'] : 0 );
		$key   = esc_attr( $key );

		if ( THEMECOMPLETE_EPO_WPML()->is_multi_currency() && isset( $element['price_rules'][ $key ] ) ) {
			if ( isset( $element['price_rules'][ $key ][0] ) ) {// general rule.
				$price = $element['price_rules'][ $key ][0];
			}
		} elseif ( ! isset( $tmcp['element']['rules'][ $key ] ) ) {// field price rule.
			if ( isset( $tmcp['element']['rules'][0][0] ) ) {// general rule.
				$price = $tmcp['element']['rules'][0][0];
			}
		} elseif ( isset( $tmcp['element']['rules'][ $key ][0] ) ) {// general field variation rule.
			$price = $tmcp['element']['rules'][ $key ][0];
		} elseif ( isset( $tmcp['element']['rules'][0][0] ) ) {// general rule.
			$price = $tmcp['element']['rules'][0][0];
		} elseif ( isset( $element['price_rules'][ $key ] ) && isset( $element['price_rules'][ $key ][0] ) && '' !== $element['price_rules'][ $key ][0] ) {
			$price = $element['price_rules'][ $key ][0];
		}

		return $price;
	}

	/**
	 * Get the element price type
	 *
	 * @param string              $price_type_default_value The default price type.
	 * @param array<mixed>        $element The element array.
	 * @param string|array<mixed> $key The posted element value.
	 * @param boolean|null        $per_product_pricing If the product has pricing, true or false.
	 * @param integer|false       $variation_id The variation id.
	 * @return string
	 * @since 5.0.11
	 */
	public function get_element_price_type( $price_type_default_value = '', $element = [], $key = '', $per_product_pricing = null, $variation_id = false ) {
		$_price_type = $price_type_default_value;
		// This currently happens for multiple file uploads.
		if ( is_array( $key ) ) {
			$key = '0';
		}
		$key = esc_attr( THEMECOMPLETE_EPO_HELPER()->clear_key( $key ) );
		if ( $per_product_pricing ) {

			if ( ! isset( $element['price_rules_type'][ $key ] ) ) {// field price rule.
				if ( $variation_id && isset( $element['price_rules_type'][0][ $variation_id ] ) ) {// general variation rule.
					$_price_type = $element['price_rules_type'][0][ $variation_id ];
				} elseif ( isset( $element['price_rules_type'][0][0] ) ) {// general rule.
					$_price_type = $element['price_rules_type'][0][0];
				}
			} elseif ( $variation_id && isset( $element['price_rules_type'][ $key ][ $variation_id ] ) ) {// field price rule.
				$_price_type = $element['price_rules_type'][ $key ][ $variation_id ];
			} elseif ( isset( $element['price_rules_type'][ $key ][0] ) ) {// general field variation rule.
				$_price_type = $element['price_rules_type'][ $key ][0];
			} elseif ( $variation_id && isset( $element['price_rules_type'][0][ $variation_id ] ) ) {// general variation rule.
				$_price_type = $element['price_rules_type'][0][ $variation_id ];
			} elseif ( isset( $element['price_rules_type'][0][0] ) ) {// general rule.
				$_price_type = $element['price_rules_type'][0][0];
			}
		}

		return $_price_type;
	}

	/**
	 * Get the element price
	 *
	 * @param mixed               $price_default_value The default value to return.
	 * @param string              $_price_type The price type.
	 * @param array<mixed>        $element The element array.
	 * @param string|array<mixed> $key The posted element value.
	 * @param boolean|null        $per_product_pricing If the product has pricing, true or false.
	 * @param integer|false       $variation_id The variation id.
	 * @param array<mixed>|null   $price_per_currencies The price per currencies array.
	 * @param string|false        $currency The currency to set the result to.
	 * @return mixed
	 * @since 6.0
	 */
	public function get_element_price( $price_default_value = 0, $_price_type = '', $element = [], $key = '', $per_product_pricing = null, $variation_id = false, $price_per_currencies = null, $currency = false ) {
		$_price = $price_default_value;
		// This currently happens for multiple file uploads.
		if ( is_array( $key ) ) {
			$key = '0';
		}
		$key = esc_attr( THEMECOMPLETE_EPO_HELPER()->clear_key( $key ) );
		if ( $per_product_pricing ) {

			if ( ! isset( $element['price_rules'][ $key ] ) ) {// field price rule.
				if ( $variation_id && isset( $element['price_rules'][0][ $variation_id ] ) ) {// general variation rule.
					$_price = $element['price_rules'][0][ $variation_id ];
				} elseif ( isset( $element['price_rules'][0][0] ) ) {// general rule.
					$_price = $element['price_rules'][0][0];
				}
			} elseif ( $variation_id && isset( $element['price_rules'][ $key ][ $variation_id ] ) ) {// field price rule.
				$_price = $element['price_rules'][ $key ][ $variation_id ];
			} elseif ( isset( $element['price_rules'][ $key ][0] ) ) {// general field variation rule.
				$_price = $element['price_rules'][ $key ][0];
			} elseif ( $variation_id && isset( $element['price_rules'][0][ $variation_id ] ) ) {// general variation rule.
				$_price = $element['price_rules'][0][ $variation_id ];
			} elseif ( isset( $element['price_rules'][0][0] ) ) {// general rule.
				$_price = $element['price_rules'][0][0];
			}

			if ( ( 'percent' === $_price_type || 'percentcurrenttotal' === $_price_type ) && '' === $_price && isset( $element['price_rules_original'] ) ) {
				if ( ! isset( $element['price_rules_original'][ $key ] ) ) {// field price rule.
					if ( $variation_id && isset( $element['price_rules_original'][0][ $variation_id ] ) ) {// general variation rule.
						$_price = $element['price_rules_original'][0][ $variation_id ];
					} elseif ( isset( $element['price_rules_original'][0][0] ) ) {// general rule.
						$_price = $element['price_rules_original'][0][0];
					}
				} elseif ( $variation_id && isset( $element['price_rules_original'][ $key ][ $variation_id ] ) ) {// field price rule.
					$_price = $element['price_rules_original'][ $key ][ $variation_id ];
				} elseif ( isset( $element['price_rules_original'][ $key ][0] ) ) {// general field variation rule.
					$_price = $element['price_rules_original'][ $key ][0];
				} elseif ( $variation_id && isset( $element['price_rules_original'][0][ $variation_id ] ) ) {// general variation rule.
					$_price = $element['price_rules_original'][0][ $variation_id ];
				} elseif ( isset( $element['price_rules_original'][0][0] ) ) {// general rule.
					$_price = $element['price_rules_original'][0][0];
				}
			}

			$currency_price = false;
			if ( $currency && $price_per_currencies && is_array( $price_per_currencies ) && isset( $price_per_currencies[ $currency ] ) ) {
				if ( isset( $price_per_currencies[ $currency ][ $key ] ) ) {
					if ( isset( $price_per_currencies[ $currency ][ $key ][0] ) ) {
						$currency_price = $price_per_currencies[ $currency ][ $key ][0];
						if ( '' !== $currency_price ) {
							$currency = false;
						} else {
							$currency_price = false;
						}
					}
				} elseif ( '' !== $key && isset( $price_per_currencies[ $currency ][0] ) && isset( $price_per_currencies[ $currency ][0][0] ) ) {
					$currency_price = $price_per_currencies[ $currency ][0][0];
					if ( '' !== $currency_price ) {
						$currency = false;
					} else {
						$currency_price = false;
					}
				}
			}

			if ( false !== $currency_price ) {
				$_price = $currency_price;
			}

			if ( 'math' !== $_price_type && '' !== $_price ) {
				$_price = floatval( wc_format_decimal( $_price, false, true ) );
			}
		}

		if ( is_array( $_price ) ) {
			$_price = $price_default_value;
		}

		if ( null !== $price_per_currencies ) {
			$_price = [
				'price'    => $_price,
				'currency' => $currency,
			];
		}

		return $_price;
	}

	/**
	 * Check for the presence of mathematical special variables in a given string.
	 *
	 * @param mixed  $price_string The input string to be checked for mathematical special variables.
	 * @param string $type The type of the special variable to check.
	 *                     Possible values: '' (all types), 'special', 'cumulative'.
	 *
	 * @return bool True if any special variable is found, false otherwise.
	 */
	public function has_math_special_variable( $price_string = '', $type = '' ) {
		if ( ! is_string( $price_string ) ) {
			return false;
		}
		$math_special_variables = [
			'{product_price_plus_options_total}',
			'{options_total}',
		];

		$math_cumulative_special_variables = [
			'{cumulative_total}',
			'{product_price_plus_cumulative_total}',
		];

		if ( empty( $type ) || 'special' === $type ) {
			foreach ( $math_special_variables as $variable ) {
				if ( str_contains( $price_string, $variable ) ) {
					return true;
				}
			}
		}

		if ( empty( $type ) || 'cumulative' === $type ) {
			foreach ( $math_cumulative_special_variables as $variable ) {
				if ( str_contains( $price_string, $variable ) ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Calculates the correct option price
	 *
	 * @param array<mixed>|null        $post_data The posted data.
	 * @param array<mixed>             $element The element array.
	 * @param mixed                    $key The posted element value.
	 * @param string|false             $attribute The posted element name.
	 * @param float|array<mixed>|false $attribute_quantity The option quantity of this element.
	 * @param integer                  $key_id The array key of the posted element values array.
	 * @param integer                  $keyvalue_id The array key for the values of the posted element values array.
	 * @param boolean|null             $per_product_pricing If the product has pricing, true or false.
	 * @param mixed                    $cpf_product_price The product price.
	 * @param integer|false            $variation_id The variation id.
	 * @param mixed                    $price_default_value The value to return if the formula fails.
	 * @param string|false             $currency The currency to set the result to.
	 * @param string|false             $current_currency The current currency.
	 * @param array<mixed>|null        $price_per_currencies The price per currencies array.
	 * @param array<mixed>|false       $tmcp Saved element data.
	 * @param array<mixed>|false       $cart_meta_epo_type Saved cart meta epo type data.
	 * @param array<mixed>             $tmdata Saved tmdata array.
	 * @param mixed                    $func_total Current options total if applicable.
	 * @param mixed                    $cumulative_total Current cumulative total if applicable.
	 * @return mixed
	 * @since 1.0
	 */
	public function calculate_price( $post_data = null, $element = [], $key = null, $attribute = false, $attribute_quantity = false, $key_id = 0, $keyvalue_id = 0, $per_product_pricing = null, $cpf_product_price = false, $variation_id = false, $price_default_value = false, $currency = false, $current_currency = false, $price_per_currencies = null, $tmcp = false, $cart_meta_epo_type = false, $tmdata = [], $func_total = false, $cumulative_total = false ) {
		$element = apply_filters( 'wc_epo_get_element_for_display', $element );
		// @phpstan-ignore-next-line
		if ( is_null( $post_data ) && isset( $_POST ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$post_data = wp_unslash( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification
		}
		if ( empty( $post_data ) && isset( $_REQUEST['tcajax'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$post_data = wp_unslash( $_REQUEST ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}

		/**
		 * Post data
		 *
		 * @var array<mixed> $post_data
		 */
		$post_data = wp_unslash( $post_data );

		if ( false === $attribute_quantity ) {
			if ( isset( $post_data[ $attribute . '_quantity' ] ) ) {
				$attribute_quantity = $post_data[ $attribute . '_quantity' ];
			}
		}
		if ( is_array( $attribute_quantity ) && isset( $attribute_quantity[ $key_id ] ) ) {
			$attribute_quantity = $attribute_quantity[ $key_id ];
			if ( is_array( $attribute_quantity ) && isset( $attribute_quantity[ $keyvalue_id ] ) ) {
				$attribute_quantity = $attribute_quantity[ $keyvalue_id ];
			}
		}

		// This should only trigger manually for internal calculation in math price.
		if ( false === $attribute_quantity ) {
			$attribute_quantity = 1;
		}

		$posted_attribute = '';
		if ( isset( $post_data[ $attribute ] ) ) {
			$posted_attribute = $post_data[ $attribute ];
		} elseif ( isset( $_FILES[ $attribute ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			$posted_attribute = wp_unslash( $_FILES[ $attribute ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification
		}

		if ( is_array( $posted_attribute ) && isset( $posted_attribute[ $key_id ] ) ) {
			$posted_attribute = $posted_attribute[ $key_id ];
			if ( is_array( $posted_attribute ) && isset( $posted_attribute[ $keyvalue_id ] ) ) {
				$posted_attribute = $posted_attribute[ $keyvalue_id ];
			}
		}

		// This currently happens for multiple file uploads and repeaters.
		if ( is_array( $key ) ) {
			if ( 'multiple_file_upload' === $element['type'] ) {
				$key = [ 0 ];
			}
		}

		if ( ! is_array( $key ) ) {
			$key = [ $key ];
		}

		$price = 0;

		foreach ( $key as $thiskey ) {

			$_price = $this->calculate_key_price( $posted_attribute, $post_data, $element, $thiskey, $attribute, $attribute_quantity, $key_id, $keyvalue_id, $per_product_pricing, $cpf_product_price, $variation_id, $price_default_value, $currency, $current_currency, $price_per_currencies, $tmcp, $cart_meta_epo_type, $tmdata, $func_total, $cumulative_total );
			if ( '' === $price_default_value && '' === $_price ) {
				$price = $_price;
			} else {
				if ( false === $price_default_value || '' === $price_default_value ) {
					$price_default_value = 0;
				}
				$price = floatval( $_price ) + floatval( $price );
			}
		}

		return $price;
	}

	/**
	 * Calculates the correct option price for the $key
	 *
	 * @param string                   $posted_attribute The posted attribute.
	 * @param array<mixed>|null        $post_data The posted data.
	 * @param array<mixed>             $element The element array.
	 * @param string                   $key The posted element value.
	 * @param string|false             $attribute The posted element name.
	 * @param float|array<mixed>|false $attribute_quantity The option quantity of this element.
	 * @param integer                  $key_id The array key of the posted element values array.
	 * @param integer                  $keyvalue_id The array key for the values of the posted element values array.
	 * @param boolean|null             $per_product_pricing If the product has pricing, true or false.
	 * @param mixed                    $cpf_product_price The product price.
	 * @param integer|false            $variation_id The variation id.
	 * @param mixed                    $price_default_value The value to return if the formula fails.
	 * @param string|false             $currency The currency to set the result to.
	 * @param string|false             $current_currency The current currency.
	 * @param array<mixed>|null        $price_per_currencies The price per currencies array.
	 * @param array<mixed>|false       $tmcp Saved element data.
	 * @param array<mixed>|false       $cart_meta_epo_type Saved cart meta epo type data.
	 * @param array<mixed>             $tmdata Saved tmdata array.
	 * @param mixed                    $func_total Current options total if applicable.
	 * @param mixed                    $cumulative_total Current cumulative total if applicable.
	 * @return mixed
	 * @since 6.0
	 */
	public function calculate_key_price( $posted_attribute = '', $post_data = null, $element = [], $key = '', $attribute = false, $attribute_quantity = false, $key_id = 0, $keyvalue_id = 0, $per_product_pricing = null, $cpf_product_price = false, $variation_id = false, $price_default_value = 0, $currency = false, $current_currency = false, $price_per_currencies = null, $tmcp = false, $cart_meta_epo_type = false, $tmdata = [], $func_total = false, $cumulative_total = false ) {
		if ( false === $price_default_value ) {
			$price_default_value = 0;
		}

		$key = esc_attr( $key );

		$original_currency = $currency;

		$_price_type = $this->get_element_price_type( '', $element, $key, $per_product_pricing, $variation_id );
		$_price      = $this->get_element_price( $price_default_value, $_price_type, $element, $key, $per_product_pricing, $variation_id, $price_per_currencies, $currency );
		if ( is_array( $_price ) ) {
			if ( false === $_price['currency'] ) {
				$currency = false;
			}
			$_price = $_price['price'];
		}

		if ( $per_product_pricing && '' !== $key ) {

			if ( false !== $cpf_product_price ) {
				$cpf_product_price = apply_filters( 'wc_epo_original_price_type_mode', $cpf_product_price, $post_data );
			}
			switch ( $_price_type ) {
				case 'percent_cart_total':
					$_price = ( floatval( $_price ) / 100 ) * floatval( WC()->cart->get_cart_contents_total() );
					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;

				case 'percent':
					if ( false !== $cpf_product_price ) {
						if ( $currency ) {
							$cpf_product_price = apply_filters( 'wc_epo_convert_to_currency', $cpf_product_price, $current_currency, $currency );
						}
						$_price = ( floatval( $_price ) / 100 ) * floatval( $cpf_product_price );
					}
					break;
				case 'percentcurrenttotal':
					$_original_price = $_price;
					if ( false !== $cpf_product_price ) {
						if ( '' !== $_price ) {
							if ( isset( $post_data[ $attribute . '_hidden' ] ) ) {
								$_price = floatval( $post_data[ $attribute . '_hidden' ] );
							}
							if ( isset( $post_data['tm_epo_options_static_prices'] ) ) {
								$_price = ( floatval( $post_data['tm_epo_options_static_prices'] ) + floatval( $cpf_product_price ) ) * ( floatval( $_original_price ) / 100 );
								if ( $attribute_quantity > 0 ) {
									$_price = $_price * floatval( $attribute_quantity );
								}
							}

							if ( $attribute_quantity > 0 ) {
								$_price = $_price / floatval( $attribute_quantity );
							}
						}
					}
					break;
				case 'fixedcurrenttotal':
					$_original_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, '', $current_currency, $price_per_currencies, $key, $attribute );

					if ( '' !== $_price && isset( $post_data[ $attribute . '_hiddenfixed' ] ) ) {
						$_price = floatval( $post_data[ $attribute . '_hiddenfixed' ] );

						if ( isset( $post_data['tm_epo_options_static_prices'] ) ) {
							$_price = ( floatval( $post_data['tm_epo_options_static_prices'] ) + floatval( $_original_price ) );
							if ( $attribute_quantity > 0 ) {
								$_price = $_price * floatval( $attribute_quantity );
							}
						}

						if ( $attribute_quantity > 0 ) {
							$_price = $_price / floatval( $attribute_quantity );
						}
					}
					break;
				case 'word':
					$_price = floatval( floatval( $_price ) * floatval( THEMECOMPLETE_EPO_HELPER()->count_words( $posted_attribute ) ) );
					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;
				case 'wordpercent':
					if ( false !== $cpf_product_price ) {
						if ( $currency ) {
							$cpf_product_price = apply_filters( 'wc_epo_get_currency_price', $cpf_product_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
						}
						$_price = floatval( THEMECOMPLETE_EPO_HELPER()->count_words( $posted_attribute ) ) * ( floatval( $_price / 100 ) * floatval( $cpf_product_price ) );
					}
					break;
				case 'wordnon':
					$freechars   = absint( $element['freechars'] );
					$_textlength = floatval( THEMECOMPLETE_EPO_HELPER()->count_words( $posted_attribute ) ) - $freechars;
					if ( $_textlength < 0 ) {
						$_textlength = 0;
					}
					$_price = floatval( floatval( $_price ) * $_textlength );
					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;
				case 'wordpercentnon':
					if ( false !== $cpf_product_price ) {
						$freechars = absint( $element['freechars'] );
						if ( $currency ) {
							$cpf_product_price = apply_filters( 'wc_epo_get_currency_price', $cpf_product_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
						}
						$_textlength = floatval( THEMECOMPLETE_EPO_HELPER()->count_words( $posted_attribute ) ) - $freechars;
						if ( $_textlength < 0 ) {
							$_textlength = 0;
						}
						$_price = floatval( $_textlength ) * ( ( floatval( $_price ) / 100 ) * floatval( $cpf_product_price ) );
					}
					break;

				case 'char':
					$_price = floatval( floatval( $_price ) * floatval( tc_strlen( $posted_attribute, true ) ) );
					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;
				case 'charpercent':
					if ( false !== $cpf_product_price ) {
						if ( $currency ) {
							$cpf_product_price = apply_filters( 'wc_epo_get_currency_price', $cpf_product_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
						}
						$_price = floatval( tc_strlen( $posted_attribute, true ) ) * ( ( floatval( $_price ) / 100 ) * floatval( $cpf_product_price ) );
					}
					break;
				case 'charnofirst':
					$_textlength = floatval( tc_strlen( $posted_attribute, true ) ) - 1;
					if ( $_textlength < 0 ) {
						$_textlength = 0;
					}
					$_price = floatval( floatval( $_price ) * $_textlength );
					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;

				case 'charnon':
					$freechars   = absint( $element['freechars'] );
					$_textlength = floatval( tc_strlen( $posted_attribute, true ) ) - $freechars;
					if ( $_textlength < 0 ) {
						$_textlength = 0;
					}
					$_price = floatval( floatval( $_price ) * $_textlength );
					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;
				case 'charpercentnon':
					if ( false !== $cpf_product_price ) {
						$freechars = absint( $element['freechars'] );
						if ( $currency ) {
							$cpf_product_price = apply_filters( 'wc_epo_get_currency_price', $cpf_product_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
						}
						$_textlength = floatval( tc_strlen( $posted_attribute, true ) ) - $freechars;
						if ( $_textlength < 0 ) {
							$_textlength = 0;
						}
						$_price = floatval( $_textlength ) * ( ( floatval( $_price ) / 100 ) * floatval( $cpf_product_price ) );
					}
					break;
				case 'charnonnospaces':
					$freechars   = absint( $element['freechars'] );
					$_textlength = floatval( strlen( preg_replace( '/\s+/u', '', stripcslashes( THEMECOMPLETE_EPO_HELPER()->utf8_decode( $posted_attribute ) ) ) ) ) - $freechars;
					if ( $_textlength < 0 ) {
						$_textlength = 0;
					}
					$_price = floatval( floatval( $_price ) * $_textlength );
					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;
				case 'charpercentnonnospaces':
					if ( false !== $cpf_product_price ) {
						$freechars = absint( $element['freechars'] );
						if ( $currency ) {
							$cpf_product_price = apply_filters( 'wc_epo_get_currency_price', $cpf_product_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
						}
						$_textlength = floatval( strlen( preg_replace( '/\s+/u', '', stripcslashes( THEMECOMPLETE_EPO_HELPER()->utf8_decode( $posted_attribute ) ) ) ) ) - $freechars;
						if ( $_textlength < 0 ) {
							$_textlength = 0;
						}
						$_price = floatval( $_textlength ) * ( ( floatval( $_price ) / 100 ) * floatval( $cpf_product_price ) );
					}
					break;

				case 'charnospaces':
					$_price = floatval( floatval( $_price ) * strlen( preg_replace( '/\s+/u', '', stripcslashes( THEMECOMPLETE_EPO_HELPER()->utf8_decode( $posted_attribute ) ) ) ) );
					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;
				case 'charpercentnofirst':
					if ( false !== $cpf_product_price ) {
						if ( $currency ) {
							$cpf_product_price = apply_filters( 'wc_epo_get_currency_price', $cpf_product_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
						}
						$_textlength = floatval( tc_strlen( $posted_attribute, true ) ) - 1;
						if ( $_textlength < 0 ) {
							$_textlength = 0;
						}
						$_price = floatval( $_textlength ) * ( ( floatval( $_price ) / 100 ) * floatval( $cpf_product_price ) );
					}
					break;
				case 'step':
					$_price = floatval( floatval( $_price ) * floatval( stripcslashes( $posted_attribute ) ) );
					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;
				case 'currentstep':
					$_price = floatval( stripcslashes( $posted_attribute ) );
					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;
				case 'intervalstep':
					if ( isset( $element['min'] ) ) {
						$_min   = floatval( $element['min'] );
						$_price = floatval( floatval( $_price ) * ( floatval( stripcslashes( $posted_attribute ) ) - $_min ) );
						if ( $currency ) {
							$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
						}
					}
					break;
				case 'row':
					$_price = floatval( floatval( $_price ) * ( substr_count( stripcslashes( THEMECOMPLETE_EPO_HELPER()->utf8_decode( $posted_attribute ) ), "\r\n" ) + 1 ) );
					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;
				case 'math':
					$_price = $this->calculate_math_price( $_price, $post_data, $element, $key, $attribute, $attribute_quantity, $key_id, $keyvalue_id, $per_product_pricing, $cpf_product_price, $variation_id, $price_default_value, $original_currency, $current_currency, $price_per_currencies, $tmcp, $cart_meta_epo_type, $tmdata, $func_total, $cumulative_total );
					break;
				default:
					// fixed price.
					if ( $currency ) {
						$_price = apply_filters( 'wc_epo_get_currency_price', $_price, $currency, $_price_type, $current_currency, $price_per_currencies, $key, $attribute );
					}
					break;
			}

			$_price = floatval( $_price ) * floatval( $attribute_quantity );

			if ( '' === $price_default_value && (float) 0 === $_price ) {
				$_price = '';
			}
		} else {
			$_price = $price_default_value;
		}

		return $_price;
	}

	/**
	 * Get all lookup tables
	 *
	 * @return array<mixed>
	 * @since 6.1
	 */
	public function fetch_all_lookuptables() {
		$meta_array = THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'OR', 'lookuptable_meta', '', '!=', 'NOT EXISTS' );

		$meta_query_args = [
			'relation' => 'AND',
			$meta_array,
			[
				[
					'key'     => 'lookuptable_meta',
					'compare' => 'EXISTS',
				],
			],
		];

		$args = [
			'post_type'        => THEMECOMPLETE_EPO_LOOKUPTABLE_POST_TYPE,
			'post_status'      => [ 'publish' ],
			'numberposts'      => -1,
			'orderby'          => 'ID',
			'order'            => 'asc',
			'meta_query'       => $meta_query_args, // phpcs:ignore WordPress.DB.SlowDBQuery
			'suppress_filters' => false,
		];

		$lookuptables = THEMECOMPLETE_EPO_HELPER()->get_cached_posts( $args );

		return $lookuptables;
	}

	/**
	 * Fetches all lookup tables
	 *
	 * @return false|array<mixed>
	 * @since 6.4
	 */
	public function fetch_lookuptables() {
		if ( false === $this->lookup_tables ) {
			$this->lookup_tables = [];
			$this->generate_lookuptables();
		}
		return $this->lookup_tables;
	}

	/**
	 * Generate all lookup tables
	 * and save them in $this->lookup_tables
	 *
	 * @return void
	 * @since 6.1
	 */
	public function generate_lookuptables() {
		$lookuptables = $this->fetch_all_lookuptables();
		if ( $lookuptables && is_array( $this->lookup_tables ) ) {
			foreach ( $lookuptables as $table ) {
				$meta = themecomplete_get_post_meta( $table->ID, 'lookuptable_meta', true );

				if ( ! is_array( $meta ) ) {
					$meta = [];
				}

				foreach ( $meta as $table_name => $table_data ) {
					$index = 0;
					if ( isset( $this->lookup_tables[ $table_name ] ) ) {
						$index = count( $this->lookup_tables[ $table_name ] );
					}
					$this->lookup_tables[ $table_name ][ $index ]['data'] = $table_data;
				}
			}
		}
	}

	/**
	 * Upload file
	 *
	 * @param array<mixed> $file The file array.
	 * @param integer      $key_id The array key of the posted element values array.
	 * @param integer      $keyvalue_id The array key for the values of the posted element values array.
	 *
	 * @return array<mixed>|mixed
	 */
	public function upload_file( $file, $key_id = 0, $keyvalue_id = 0 ) {
		$tmp_name = $file['tmp_name'];
		if ( is_array( $tmp_name ) && isset( $tmp_name[ $key_id ] ) ) {
			$tmp_name = $tmp_name[ $key_id ];
			if ( is_array( $tmp_name ) && isset( $tmp_name[ $keyvalue_id ] ) ) {
				$tmp_name = $tmp_name[ $keyvalue_id ];
			}
		}

		if ( is_array( $file ) ) {
			foreach ( $file as $key => $value ) {
				if ( is_array( $value ) && isset( $value[ $key_id ] ) ) {
					$value = $value[ $key_id ];
					if ( is_array( $value ) && isset( $value[ $keyvalue_id ] ) ) {
						$value = $value[ $keyvalue_id ];
					}
				}
				$file[ $key ] = $value;
			}
		}
		$tmp_name = $file['tmp_name'];

		if ( '' === $tmp_name ) {
			return false;
		}

		if ( is_array( $file ) && ! empty( $tmp_name ) && isset( $this->upload_object[ $tmp_name ] ) ) {
			$this->upload_object[ $tmp_name ]['tc'] = true;

			return $this->upload_object[ $tmp_name ];
		}
		if ( ! defined( 'ALLOW_UNFILTERED_UPLOADS' ) ) {
			define( 'ALLOW_UNFILTERED_UPLOADS', true );
		}
		include_once ABSPATH . 'wp-admin/includes/file.php';
		include_once ABSPATH . 'wp-admin/includes/media.php';
		add_filter( 'upload_dir', [ $this, 'upload_dir_trick' ] );
		add_filter( 'upload_mimes', [ $this, 'upload_mimes_trick' ] );
		$upload = wp_handle_upload(
			$file,
			[
				'test_form' => false,
				'test_type' => false,
			]
		);
		remove_filter( 'upload_dir', [ $this, 'upload_dir_trick' ] );
		remove_filter( 'upload_mimes', [ $this, 'upload_mimes_trick' ] );

		if ( is_array( $file ) && ! empty( $tmp_name ) ) {
			$this->upload_object[ $tmp_name ] = $upload;
		}

		return $upload;
	}

	/**
	 * Alter allowed file mime and type
	 *
	 * @return mixed|void
	 */
	public function get_allowed_mimes() {
		$mimes = [];

		$tm_epo_custom_file_types  = THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_custom_file_types' );
		$tm_epo_allowed_file_types = THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_allowed_file_types' );

		$tm_epo_custom_file_types = explode( ',', $tm_epo_custom_file_types );
		if ( ! is_array( $tm_epo_custom_file_types ) ) {
			$tm_epo_custom_file_types = [];
		}
		if ( ! is_array( $tm_epo_allowed_file_types ) ) {
			$tm_epo_allowed_file_types = [ '@' ];
		}
		$tm_epo_allowed_file_types = array_merge( $tm_epo_allowed_file_types, $tm_epo_custom_file_types );
		$tm_epo_allowed_file_types = array_unique( $tm_epo_allowed_file_types );

		$wp_get_ext_types  = wp_get_ext_types();
		$wp_get_mime_types = wp_get_mime_types();

		foreach ( $tm_epo_allowed_file_types as $key => $value ) {
			if ( ! $value ) {
				continue;
			}
			if ( '@' === $value ) {
				$mimes = $wp_get_mime_types;
			} else {
				$value = ltrim( $value, '@' );
				switch ( $value ) {
					case 'image':
					case 'audio':
					case 'video':
					case 'document':
					case 'spreadsheet':
					case 'interactive':
					case 'text':
					case 'archive':
					case 'code':
						if ( isset( $wp_get_ext_types[ $value ] ) && is_array( $wp_get_ext_types[ $value ] ) ) { // @phpstan-ignore-line
							foreach ( $wp_get_ext_types[ $value ] as $k => $extension ) {
								$type = false;
								foreach ( $wp_get_mime_types as $exts => $_mime ) {
									if ( preg_match( '!^(' . $exts . ')$!i', $extension ) ) {
										$type = $_mime;
										break;
									}
								}
								if ( $type ) {
									$mimes[ $extension ] = $type;
								}
							}
						}
						break;

					default:
						$type = false;
						foreach ( $wp_get_mime_types as $exts => $_mime ) {
							if ( preg_match( '!^(' . $exts . ')$!i', $value ) ) {
								$type = $_mime;
								break;
							}
						}
						if ( $type ) {
							$mimes[ $value ] = $type;
						} else {
							$mimes[ $value ] = 'application/octet-stream';
						}
						break;
				}
			}
		}

		$allowed_mimes = [];
		foreach ( $mimes as $key => $value ) {
			$value = explode( '|', $key );

			foreach ( $value as $k => $v ) {
				$v               = str_replace( '.', '', trim( $v ) );
				$v               = '.' . $v;
				$allowed_mimes[] = $v;
			}
		}

		return apply_filters( 'wc_epo_get_allowed_mimes', $allowed_mimes );
	}

	/**
	 * Alter allowed file mime and type
	 *
	 * @param array<mixed> $existing_mimes The existing mimes array.
	 *
	 * @return mixed|void
	 */
	public function upload_mimes_trick( $existing_mimes = [] ) {
		$mimes = [];

		$tm_epo_custom_file_types  = THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_custom_file_types' );
		$tm_epo_allowed_file_types = THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_allowed_file_types' );

		$tm_epo_custom_file_types = explode( ',', $tm_epo_custom_file_types );
		if ( ! is_array( $tm_epo_custom_file_types ) ) {
			$tm_epo_custom_file_types = [];
		}
		if ( ! is_array( $tm_epo_allowed_file_types ) ) {
			$tm_epo_allowed_file_types = [ '@' ];
		}
		$tm_epo_allowed_file_types = array_merge( $tm_epo_allowed_file_types, $tm_epo_custom_file_types );
		$tm_epo_allowed_file_types = array_unique( $tm_epo_allowed_file_types );

		$wp_get_ext_types  = wp_get_ext_types();
		$wp_get_mime_types = wp_get_mime_types();

		foreach ( $tm_epo_allowed_file_types as $key => $value ) {
			if ( ! $value ) {
				continue;
			}
			if ( '@' === $value ) {
				$mimes = $existing_mimes;
			} else {
				$value = ltrim( $value, '@' );
				switch ( $value ) {
					case 'image':
					case 'audio':
					case 'video':
					case 'document':
					case 'spreadsheet':
					case 'interactive':
					case 'text':
					case 'archive':
					case 'code':
						if ( isset( $wp_get_ext_types[ $value ] ) && is_array( $wp_get_ext_types[ $value ] ) ) { // @phpstan-ignore-line
							foreach ( $wp_get_ext_types[ $value ] as $k => $extension ) {
								$type = false;
								foreach ( $wp_get_mime_types as $exts => $_mime ) {
									if ( preg_match( '!^(' . $exts . ')$!i', $extension ) ) {
										$type = $_mime;
										break;
									}
								}
								if ( $type ) {
									$mimes[ $extension ] = $type;
								}
							}
						}
						break;

					default:
						$type = false;
						foreach ( $wp_get_mime_types as $exts => $_mime ) {
							if ( preg_match( '!^(' . $exts . ')$!i', $value ) ) {
								$type = $_mime;
								break;
							}
						}
						if ( $type ) {
							$mimes[ $value ] = $type;
						} else {
							$mimes[ $value ] = 'application/octet-stream';
						}
						break;
				}
			}
		}

		return apply_filters( 'wc_epo_upload_mimes', $mimes );
	}

	/**
	 * Alter upload directory
	 *
	 * @param array<mixed> $param Array of information about the uplaod directory.
	 *
	 * @return mixed
	 */
	public function upload_dir_trick( $param ) {
		global $woocommerce;
		$unique_dir = apply_filters( 'wc_epo_upload_unique_dir', md5( $woocommerce->session->get_customer_id() ) );
		$subdir     = $this->upload_dir . $unique_dir;
		if ( empty( $param['subdir'] ) ) {
			$param['path']   = $param['path'] . $subdir;
			$param['url']    = $param['url'] . $subdir;
			$param['subdir'] = $subdir;
		} else {
			$param['path']   = str_replace( $param['subdir'], $subdir, $param['path'] );
			$param['url']    = str_replace( $param['subdir'], $subdir, $param['url'] );
			$param['subdir'] = str_replace( $param['subdir'], $subdir, $param['subdir'] );
		}

		return $param;
	}

	/**
	 * Apply custom filter
	 *
	 * @param mixed  $value The value to apply the filter.
	 * @param string $filter The filter to apply.
	 * @param string $element The Element name.
	 * @param string $element_uniqueid The Element unique ID.
	 *
	 * @return mixed|string|void
	 */
	private function tm_apply_filter( $value = '', $filter = '', $element = '', $element_uniqueid = '' ) {
		// Normalize posted strings.
		$value = THEMECOMPLETE_EPO_HELPER()->normalize_data( $value );

		if ( ! empty( $filter ) ) {
			$value = apply_filters( $filter, $value, $element, $element_uniqueid );
		}

		return apply_filters( 'wc_epo_setting', apply_filters( 'tm_translate', $value ), $element, $element_uniqueid );
	}

	/**
	 * Get builder element
	 *
	 * @param string        $element The Element name.
	 * @param array<mixed>  $builder The builder array.
	 * @param array<mixed>  $current_builder The current builder array.
	 * @param integer|false $index The element index in the builder array.
	 * @param mixed         $alt Alternative value.
	 * @param string        $identifier Identifier 'sections' or the current element.
	 * @param string        $apply_filters Filter name to apply to the returned value.
	 * @param string        $element_uniqueid The Element unique ID.
	 *
	 * @return mixed|string|void
	 */
	public function get_builder_element( $element, $builder, $current_builder, $index = false, $alt = '', $identifier = 'sections', $apply_filters = '', $element_uniqueid = '' ) {
		$original_index = $index;

		list( $use_original_builder, $index ) = apply_filters( 'wc_epo_use_original_builder', [ true, $index ], $element, $builder, $current_builder, $identifier );

		if ( isset( $builder[ $element ] ) ) {
			if ( ! $use_original_builder ) {
				if ( false !== $index ) {
					if ( isset( $current_builder[ $element ][ $index ] ) ) {
						if ( is_object( $current_builder[ $element ][ $index ] ) ) {
							$current_builder[ $element ][ $index ] = wp_json_encode( $current_builder[ $element ][ $index ] );
						}
						if ( is_object( $builder[ $element ][ $original_index ] ) ) {
							$builder[ $element ][ $original_index ] = wp_json_encode( $builder[ $element ][ $original_index ] );
						}
						return $this->tm_apply_filter( THEMECOMPLETE_EPO_HELPER()->build_array( $current_builder[ $element ][ $index ], $builder[ $element ][ $original_index ] ), $apply_filters, $element, $element_uniqueid );
					} else {
						return $this->tm_apply_filter( $alt, $apply_filters, $element, $element_uniqueid );
					}
				} else {
					if ( is_object( $current_builder[ $element ] ) ) {
						$current_builder[ $element ] = wp_json_encode( $current_builder[ $element ] );
					}
					if ( is_object( $builder[ $element ][ $original_index ] ) ) {
						$builder[ $element ] = wp_json_encode( $builder[ $element ] );
					}
					return $this->tm_apply_filter( THEMECOMPLETE_EPO_HELPER()->build_array( $current_builder[ $element ], $builder[ $element ] ), $apply_filters, $element, $element_uniqueid );
				}
			}
			if ( false !== $index ) {
				if ( isset( $builder[ $element ][ $index ] ) ) {
					if ( is_object( $builder[ $element ][ $index ] ) ) {
						$builder[ $element ][ $index ] = wp_json_encode( $builder[ $element ][ $index ] );
					}
					return $this->tm_apply_filter( $builder[ $element ][ $index ], $apply_filters, $element, $element_uniqueid );
				} else {
					return $this->tm_apply_filter( $alt, $apply_filters, $element, $element_uniqueid );
				}
			} else {
				if ( is_object( $builder[ $element ][ $original_index ] ) ) {
					$builder[ $element ] = wp_json_encode( $builder[ $element ] );
				}
				return $this->tm_apply_filter( $builder[ $element ], $apply_filters, $element, $element_uniqueid );
			}
		} else {
			return $this->tm_apply_filter( $alt, $apply_filters, $element, $element_uniqueid );
		}
	}

	/**
	 * Gets a list of all the Extra Product Options (normal and global)
	 * for the specific $post_id.
	 *
	 * @param integer $post_id The product id.
	 * @param string  $form_prefix The form prefix.
	 * @param boolean $no_cache If we should use cached results.
	 * @param boolean $no_disabled If disabled elements should be skipped.
	 * @return array<mixed>
	 * @since 1.0
	 */
	public function get_product_tm_epos( $post_id = 0, $form_prefix = '', $no_cache = false, $no_disabled = false ) {
		if ( empty( $post_id ) || apply_filters( 'wc_epo_disable', false, $post_id ) || ! $this->check_enable() ) {
			return [];
		}

		$post_type = get_post_type( $post_id );

		// Support for variable products in product element.
		if ( ! in_array( $post_type, [ 'product', 'product_variation' ], true ) ) {
			return [];
		}

		$product = wc_get_product( $post_id );
		if ( ! $product instanceof WC_Product ) {
			return [];
		}

		$product_type = themecomplete_get_product_type( $product );

		// Yith gift cards are not supported.
		if ( 'gift-card' === $product_type ) {
			return [];
		}

		// disable cache for associated products
		// as they may have discounts which will not
		// show up on the product page if the product
		// is already in the cart.
		if ( ! $this->is_inline_epo && isset( $this->cpf[ $post_id ][ "{$no_disabled}" ][ "f{$form_prefix}" ] ) ) {
			return $this->cpf[ $post_id ][ "{$no_disabled}" ][ "f{$form_prefix}" ];
		}

		if ( 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_enable_validation' ) ) {
			$this->current_option_features[] = 'validation';
		}

		if ( 'no' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_no_lazy_load' ) ) {
			$this->current_option_features[] = 'lazyload';
		}

		$this->set_tm_meta( $post_id );

		$in_cat = [];
		$in_tax = [];

		$tmglobalprices                   = [];
		$variations_for_conditional_logic = [];

		$terms = get_the_terms( $post_id, 'product_cat' );
		if ( is_array( $terms ) ) {
			foreach ( $terms as $term ) {
				$in_cat[] = $term->term_id;
			}
		}

		$custom_product_taxonomies = get_object_taxonomies( 'product' );
		if ( is_array( $custom_product_taxonomies ) && count( $custom_product_taxonomies ) > 0 ) {
			foreach ( $custom_product_taxonomies as $tax ) {
				if ( 'product_cat' === $tax || 'translation_priority' === $tax ) {
					continue;
				}
				$terms = get_the_terms( $post_id, $tax );
				if ( is_array( $terms ) ) {
					$in_tax[ $tax ] = [];
					foreach ( $terms as $term ) {
						$in_tax[ $tax ][] = $term->slug;
					}
				}
			}
		}

		// Get Normal (Local) options.
		$args = [
			'post_type'        => THEMECOMPLETE_EPO_LOCAL_POST_TYPE,
			'post_status'      => [ 'publish' ], // get only enabled extra options.
			'numberposts'      => -1,
			'orderby'          => 'menu_order',
			'order'            => 'asc',
			'suppress_filters' => true,
			'post_parent'      => absint( THEMECOMPLETE_EPO_WPML()->get_original_id( $post_id ) ),
		];
		THEMECOMPLETE_EPO_WPML()->remove_sql_filter();
		$tmlocalprices = THEMECOMPLETE_EPO_HELPER()->get_cached_posts( $args );
		THEMECOMPLETE_EPO_WPML()->restore_sql_filter();

		$tm_meta_cpf_global_forms = ( isset( $this->tm_meta_cpf['global_forms'] ) && is_array( $this->tm_meta_cpf['global_forms'] ) ) ? $this->tm_meta_cpf['global_forms'] : [];
		foreach ( $tm_meta_cpf_global_forms as $key => $value ) {
			$tm_meta_cpf_global_forms[ $key ] = absint( $value );
		}
		$tm_meta_cpf_global_forms_added = [];

		$post_original_id = absint( THEMECOMPLETE_EPO_WPML()->get_original_id( $post_id ) );

		if ( ! $this->tm_meta_cpf['exclude'] ) {

			/**
			 * Procedure to get global forms
			 * that apply to all products or
			 * specific product categories.
			 */
			$meta_array_mode_custom = THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'AND', 'tm_meta_apply_mode', 'customize-selection', '==', 'EXISTS' );
			$meta_array_mode_all    = THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'AND', 'tm_meta_apply_mode', 'apply-to-all-products', '==', 'EXISTS' );

			$meta_array  = THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'OR', 'tm_meta_disable_categories', '1', '!=', 'NOT EXISTS' );
			$meta_array2 = THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'OR', 'tm_meta_product_exclude_ids', $post_id, 'NOT LIKE', 'NOT EXISTS', true );

			$meta_query_args = [
				'relation' => 'AND',
				$meta_array,
				[
					'key'     => 'tm_meta_apply_mode',
					'compare' => 'NOT EXISTS',
				],
				$meta_array2,
			];

			$args = [
				'post_type'   => THEMECOMPLETE_EPO_GLOBAL_POST_TYPE,
				'post_status' => [ 'publish' ], // get only enabled global extra options.
				'numberposts' => -1,
				'orderby'     => 'date',
				'order'       => 'asc',
				'meta_query'  => $meta_query_args, // phpcs:ignore WordPress.DB.SlowDBQuery
			];

			// phpcs:ignore WordPress.DB.SlowDBQuery
			$args['tax_query'] = [
				'relation' => 'OR',
				// Get Global options that belong to the product categories.
				[
					'taxonomy'         => 'product_cat',
					'field'            => 'term_id',
					'terms'            => $in_cat,
					'operator'         => 'IN',
					'include_children' => false,
				],
				// Get Global options that have no catergory set (they apply to all products).
				[
					'taxonomy' => 'product_cat',
					'operator' => 'NOT EXISTS',
				],
			];

			THEMECOMPLETE_EPO_WPML()->remove_sql_filter();
			THEMECOMPLETE_EPO_WPML()->remove_term_filters();

			// Get global forms that use the old format.
			$tmp_tmglobalprices = THEMECOMPLETE_EPO_HELPER()->get_cached_posts( $args );

			// Get global forms (new format) that are applied to all products.
			$meta_query_args    = [
				'relation' => 'AND',
				$meta_array_mode_all,
				$meta_array2,
			];
			$args['meta_query'] = $meta_query_args; // phpcs:ignore WordPress.DB.SlowDBQuery

			$tmp_tmglobalprices_all = THEMECOMPLETE_EPO_HELPER()->get_cached_posts( $args );
			$tmp_tmglobalprices     = array_merge( $tmp_tmglobalprices, $tmp_tmglobalprices_all );
			$tmp_tmglobalprices     = array_unique( $tmp_tmglobalprices, SORT_REGULAR );

			// Get global forms (new format) that are custom applied.
			$meta_query_args    = [
				'relation' => 'AND',
				$meta_array_mode_custom,
				$meta_array2,
			];
			$args['meta_query'] = $meta_query_args; // phpcs:ignore WordPress.DB.SlowDBQuery

			// phpcs:ignore WordPress.DB.SlowDBQuery
			$args['tax_query'] = [
				'relation' => 'OR',
				// Get Global options that belong to the product categories.
				[
					'taxonomy'         => 'product_cat',
					'field'            => 'term_id',
					'terms'            => $in_cat,
					'operator'         => 'IN',
					'include_children' => false,
				],
			];

			$tmp_tmglobalprices_custom = THEMECOMPLETE_EPO_HELPER()->get_cached_posts( $args );
			$tmp_tmglobalprices        = array_merge( $tmp_tmglobalprices, $tmp_tmglobalprices_custom );
			$tmp_tmglobalprices        = array_unique( $tmp_tmglobalprices, SORT_REGULAR );

			foreach ( $in_tax as $tax => $tax_temrs ) {
				$args_tax = $args;
				// phpcs:ignore WordPress.DB.SlowDBQuery
				$args_tax['tax_query'] = [
					// Get Global options that belong to the product tag.
					[
						'taxonomy'         => $tax,
						'field'            => 'slug',
						'terms'            => $tax_temrs,
						'operator'         => 'IN',
						'include_children' => false,
					],
				];

				$tmp_tmglobalprices_tax = THEMECOMPLETE_EPO_HELPER()->get_cached_posts( $args_tax );
				$tmp_tmglobalprices     = array_merge( $tmp_tmglobalprices, $tmp_tmglobalprices_tax );
				$tmp_tmglobalprices     = array_unique( $tmp_tmglobalprices, SORT_REGULAR );
			}

			THEMECOMPLETE_EPO_WPML()->restore_term_filters();
			THEMECOMPLETE_EPO_WPML()->restore_sql_filter();

			if ( $tmp_tmglobalprices ) {
				$wpml_tmp_tmglobalprices       = [];
				$wpml_tmp_tmglobalprices_added = [];
				foreach ( $tmp_tmglobalprices as $price ) {

					if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
						$price_meta_lang                 = themecomplete_get_post_meta( $price->ID, THEMECOMPLETE_EPO_WPML_LANG_META, true );
						$original_product_id             = absint( THEMECOMPLETE_EPO_WPML()->get_original_id( $price->ID, $price->post_type ) );
						$double_check_disable_categories = themecomplete_get_post_meta( $original_product_id, 'tm_meta_disable_categories', true );
						$double_check_apply_mode         = themecomplete_get_post_meta( $original_product_id, 'tm_meta_apply_mode', true );
						if ( $double_check_apply_mode ) {
							$double_check_disable_categories = 'disable-form' === $double_check_apply_mode;
						}
						THEMECOMPLETE_EPO_WPML()->remove_sql_filter();
						THEMECOMPLETE_EPO_WPML()->remove_term_filters();

						$double_check_terms = false;
						foreach ( $in_tax as $tax => $tax_temrs ) {
							$double_check_terms = get_terms(
								[
									'taxonomy'   => $tax,
									'object_ids' => $price->ID,
								]
							);

							if ( $double_check_terms ) {
								break;
							}
						}

						THEMECOMPLETE_EPO_WPML()->restore_term_filters();
						THEMECOMPLETE_EPO_WPML()->restore_sql_filter();
						if ( ! $double_check_disable_categories || $double_check_terms ) {

							if ( THEMECOMPLETE_EPO_WPML()->get_lang() === $price_meta_lang
								|| ( '' === $price_meta_lang && THEMECOMPLETE_EPO_WPML()->get_lang() === THEMECOMPLETE_EPO_WPML()->get_default_lang() )
							) {
								$tmglobalprices[]                 = $price;
								$tm_meta_cpf_global_forms_added[] = $price->ID;
								if ( THEMECOMPLETE_EPO_WPML()->get_default_lang() !== $price_meta_lang && '' !== $price_meta_lang ) {
									$wpml_tmp_tmglobalprices_added[ $original_product_id ] = $price;
								}
							} elseif ( THEMECOMPLETE_EPO_WPML()->get_default_lang() === $price_meta_lang || '' === $price_meta_lang ) {
								$wpml_tmp_tmglobalprices[ $original_product_id ] = $price;
							}
						}
					} else {
						$tmglobalprices[]                 = $price;
						$tm_meta_cpf_global_forms_added[] = $price->ID;
					}
				}
				// Replace missing translation with original.
				if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
					$wpml_gp_keys = array_keys( $wpml_tmp_tmglobalprices );
					foreach ( $wpml_gp_keys as $key => $value ) {
						if ( ! isset( $wpml_tmp_tmglobalprices_added[ $value ] ) ) {
							$price                            = $wpml_tmp_tmglobalprices[ $value ];
							$tmglobalprices[]                 = $price;
							$tm_meta_cpf_global_forms_added[] = $price->ID;
						}
					}
				}
			}

			$original_post_id = absint( THEMECOMPLETE_EPO_WPML()->get_original_id( $post_id, 'product' ) );

			/**
			 * Get Global options that apply to the product
			 */
			$args = [
				'post_type'   => THEMECOMPLETE_EPO_GLOBAL_POST_TYPE,
				'post_status' => [ 'publish' ], // get only enabled global extra options.
				'numberposts' => -1,
				'orderby'     => 'date',
				'order'       => 'asc',
				// phpcs:ignore WordPress.DB.SlowDBQuery
				'meta_query'  => [
					'relation' => 'OR',
					[
						'key'     => 'tm_meta_product_ids',
						'value'   => ':"' . $original_post_id . '";',
						'compare' => 'LIKE',

					],
					[
						'key'     => 'tm_meta_product_ids',
						'value'   => ':' . $original_post_id . ';',
						'compare' => 'LIKE',

					],
				],
			];

			$available_variations = apply_filters( 'wc_epo_global_forms_available_variations', $product->get_children(), $product );
			$glue                 = [];

			if ( is_array( $available_variations ) ) {
				foreach ( $available_variations as $variation_id ) {
					$variations_for_conditional_logic[] = $variation_id;
					$glue[]                             = [
						'key'     => 'tm_meta_product_ids',
						'value'   => ':"' . $variation_id . '";',
						'compare' => 'LIKE',
					];
					$glue[]                             = [
						'key'     => 'tm_meta_product_ids',
						'value'   => ':' . $variation_id . ';',
						'compare' => 'LIKE',
					];
				}
				if ( $glue ) {
					$args['meta_query'] = array_merge( $args['meta_query'], $glue ); // phpcs:ignore WordPress.DB.SlowDBQuery
				}
			}

			$tmglobalprices_products = THEMECOMPLETE_EPO_HELPER()->get_cached_posts( $args );

			if ( $tmglobalprices_products ) {

				$global_id_array = [];
				foreach ( $tmglobalprices as $price ) {
					$global_id_array[] = $price->ID;
				}

				$wpml_tmglobalprices_products       = [];
				$wpml_tmglobalprices_products_added = [];
				foreach ( $tmglobalprices_products as $price ) {
					if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
						$price_meta_lang     = themecomplete_get_post_meta( $price->ID, THEMECOMPLETE_EPO_WPML_LANG_META, true );
						$original_product_id = absint( THEMECOMPLETE_EPO_WPML()->get_original_id( $price->ID, $price->post_type ) );

						if ( THEMECOMPLETE_EPO_WPML()->get_lang() === $price_meta_lang
							|| ( '' === $price_meta_lang && THEMECOMPLETE_EPO_WPML()->get_lang() === THEMECOMPLETE_EPO_WPML()->get_default_lang() )
						) {
							if ( ! in_array( $price->ID, $global_id_array, true ) ) {
								$global_id_array[]                = $price->ID;
								$tmglobalprices[]                 = $price;
								$tm_meta_cpf_global_forms_added[] = $price->ID;
								if ( THEMECOMPLETE_EPO_WPML()->get_default_lang() !== $price_meta_lang && '' !== $price_meta_lang ) {
									$wpml_tmglobalprices_products_added[ $original_product_id ] = $price;
								}
							}
						} elseif ( THEMECOMPLETE_EPO_WPML()->get_default_lang() === $price_meta_lang || '' === $price_meta_lang ) {
							$wpml_tmglobalprices_products[ $original_product_id ] = $price;
						}
					} elseif ( ! in_array( $price->ID, $global_id_array, true ) ) {
						$global_id_array[]                = $price->ID;
						$tmglobalprices[]                 = $price;
						$tm_meta_cpf_global_forms_added[] = $price->ID;
					}
				}
				// Replace missing translation with original.
				if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
					$wpml_gp_keys = array_keys( $wpml_tmglobalprices_products );
					foreach ( $wpml_gp_keys as $key => $value ) {
						if ( ! isset( $wpml_tmglobalprices_products_added[ $value ] ) ) {
							$price = $wpml_tmglobalprices_products[ $value ];
							if ( ! in_array( $price->ID, $global_id_array, true ) ) {
								$global_id_array[]                = $price->ID;
								$tmglobalprices[]                 = $price;
								$tm_meta_cpf_global_forms_added[] = $price->ID;
							}
						}
					}
				}
			}

			/**
			 * Get Global options that apply to the product
			 * only for translated products
			 */
			if ( floatval( $post_id ) !== $post_original_id ) {
				// Get Global options that apply to the product.
				$args = [
					'post_type'   => THEMECOMPLETE_EPO_GLOBAL_POST_TYPE,
					'post_status' => [ 'publish' ], // get only enabled global extra options.
					'numberposts' => -1,
					'orderby'     => 'date',
					'order'       => 'asc',
					// phpcs:ignore WordPress.DB.SlowDBQuery
					'meta_query'  => [
						'relation' => 'OR',
						[
							'key'     => 'tm_meta_product_ids',
							'value'   => ':"' . $post_original_id . '";',
							'compare' => 'LIKE',

						],
						[
							'key'     => 'tm_meta_product_ids',
							'value'   => ':' . $post_original_id . ';',
							'compare' => 'LIKE',

						],
					],

				];

				THEMECOMPLETE_EPO_WPML()->remove_sql_filter();
				THEMECOMPLETE_EPO_WPML()->remove_term_filters();
				$tmglobalprices_products = THEMECOMPLETE_EPO_HELPER()->get_cached_posts( $args );
				THEMECOMPLETE_EPO_WPML()->restore_term_filters();
				THEMECOMPLETE_EPO_WPML()->restore_sql_filter();

				if ( $tmglobalprices_products ) {

					$global_id_array = [];
					foreach ( $tmglobalprices as $price ) {
						$global_id_array[] = $price->ID;
					}

					$wpml_tmglobalprices_products       = [];
					$wpml_tmglobalprices_products_added = [];
					foreach ( $tmglobalprices_products as $price ) {

						if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
							$price_meta_lang     = themecomplete_get_post_meta( $price->ID, THEMECOMPLETE_EPO_WPML_LANG_META, true );
							$original_product_id = absint( THEMECOMPLETE_EPO_WPML()->get_original_id( $price->ID, $price->post_type ) );

							if ( THEMECOMPLETE_EPO_WPML()->get_lang() === $price_meta_lang
								|| ( '' === $price_meta_lang && THEMECOMPLETE_EPO_WPML()->get_lang() === THEMECOMPLETE_EPO_WPML()->get_default_lang() )
							) {
								if ( ! in_array( $price->ID, $global_id_array, true ) ) {
									$global_id_array[]                = $price->ID;
									$tmglobalprices[]                 = $price;
									$tm_meta_cpf_global_forms_added[] = $price->ID;
									if ( THEMECOMPLETE_EPO_WPML()->get_default_lang() !== $price_meta_lang && '' !== $price_meta_lang ) {
										$wpml_tmglobalprices_products_added[ $original_product_id ] = $price;
									}
								}
							} elseif ( THEMECOMPLETE_EPO_WPML()->get_default_lang() === $price_meta_lang || '' === $price_meta_lang ) {
								$wpml_tmglobalprices_products[ $original_product_id ] = $price;
							}
						} elseif ( ! in_array( $price->ID, $global_id_array, true ) ) {
							$global_id_array[]                = $price->ID;
							$tmglobalprices[]                 = $price;
							$tm_meta_cpf_global_forms_added[] = $price->ID;
						}
					}
					// Replace missing translation with original.
					if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
						$wpml_gp_keys = array_keys( $wpml_tmglobalprices_products );
						foreach ( $wpml_gp_keys as $key => $value ) {
							if ( ! isset( $wpml_tmglobalprices_products_added[ $value ] ) ) {
								$price = $wpml_tmglobalprices_products[ $value ];
								if ( ! in_array( $price->ID, $global_id_array, true ) ) {
									$query = new WP_Query(
										[
											'post_type'   => THEMECOMPLETE_EPO_GLOBAL_POST_TYPE,
											'post_status' => [ 'publish' ],
											'numberposts' => -1,
											'posts_per_page' => -1,
											'orderby'     => 'date',
											'order'       => 'asc',
											'no_found_rows' => true,
											// phpcs:ignore WordPress.DB.SlowDBQuery
											'meta_query'  => [
												'relation' => 'AND',
												[
													'key' => THEMECOMPLETE_EPO_WPML_LANG_META,
													'value' => THEMECOMPLETE_EPO_WPML()->get_default_lang(),
													'compare' => '!=',
												],
												[
													'key' => THEMECOMPLETE_EPO_WPML_PARENT_POSTID,
													'value' => $price->ID,
													'compare' => '=',
												],
											],
										]
									);
									if ( ! empty( $query->posts ) ) {
										foreach ( $query->posts as $qpost ) {
											if ( ! $qpost instanceof WP_Post ) {
												continue;
											}
											$qmetalang = themecomplete_get_post_meta( $qpost->ID, THEMECOMPLETE_EPO_WPML_LANG_META, true );
											if ( THEMECOMPLETE_EPO_WPML()->get_lang() !== $qmetalang ) {
												continue;
											}
											if ( ! in_array( $qpost->ID, $global_id_array, true ) ) {
												$global_id_array[]                = $qpost->ID;
												$tmglobalprices[]                 = $qpost;
												$tm_meta_cpf_global_forms_added[] = $qpost->ID;
											}
											break;
										}
									} else {
										$global_id_array[]                = $price->ID;
										$tmglobalprices[]                 = $price;
										$tm_meta_cpf_global_forms_added[] = $price->ID;
									}
								}
							}
						}
					}
				}
			}

			/**
			 * Support for conditional logic based on variations
			 * on translated products
			 */
			if ( floatval( $post_id ) !== $post_original_id ) {
				// Get Global options that apply to the product.
				$args = [
					'post_type'   => THEMECOMPLETE_EPO_GLOBAL_POST_TYPE,
					'post_status' => [ 'publish' ], // get only enabled global extra options.
					'numberposts' => -1,
					'orderby'     => 'date',
					'order'       => 'asc',
					// phpcs:ignore WordPress.DB.SlowDBQuery
					'meta_query'  => [
						'relation' => 'OR',
						[
							'key'     => 'tm_meta_product_ids',
							'value'   => ':"' . $post_original_id . '";',
							'compare' => 'LIKE',
						],
						[
							'key'     => 'tm_meta_product_ids',
							'value'   => ':' . $post_original_id . ';',
							'compare' => 'LIKE',
						],
					],
				];

				$original_product     = wc_get_product( $post_original_id );
				$available_variations = [];
				if ( $original_product instanceof WC_Product ) {
					$available_variations = apply_filters( 'wc_epo_global_forms_available_variations', $original_product->get_children() );
				}
				$glue = [];

				if ( is_array( $available_variations ) ) {
					foreach ( $available_variations as $variation_id ) {
						$variations_for_conditional_logic[] = $variation_id;
						$glue[]                             = [
							'key'     => 'tm_meta_product_ids',
							'value'   => ':"' . absint( THEMECOMPLETE_EPO_WPML()->get_original_id( $variation_id ) ) . '";',
							'compare' => 'LIKE',
						];
						$glue[]                             = [
							'key'     => 'tm_meta_product_ids',
							'value'   => ':' . absint( THEMECOMPLETE_EPO_WPML()->get_original_id( $variation_id ) ) . ';',
							'compare' => 'LIKE',
						];
					}

					if ( $glue ) {
						$args['meta_query']['relation'] = 'OR'; // phpcs:ignore WordPress.DB.SlowDBQuery
						$args['meta_query']             = array_merge( $args['meta_query'], $glue ); // phpcs:ignore WordPress.DB.SlowDBQuery

						$tmglobalprices_products = THEMECOMPLETE_EPO_HELPER()->get_cached_posts( $args );

						// Merge Global options.
						if ( $tmglobalprices_products ) {
							$global_id_array = [];
							foreach ( $tmglobalprices as $price ) {
								$global_id_array[] = $price->ID;
							}
							foreach ( $tmglobalprices_products as $price ) {
								if ( ! in_array( $price->ID, $global_id_array, true ) ) {
									$original_product_id = absint( THEMECOMPLETE_EPO_WPML()->get_original_id( $price->ID, $price->post_type ) );
									if ( absint( $price->ID ) !== absint( $original_product_id ) ) {
										if ( ! in_array( $original_product_id, $global_id_array, true ) ) {
											$tmglobalprices[]                 = $price;
											$tm_meta_cpf_global_forms_added[] = $price->ID;
										}
									} else {
										$tmglobalprices[]                 = $price;
										$tm_meta_cpf_global_forms_added[] = $price->ID;
									}
								}
							}
						}
					}
				}
			}
		}

		$tm_meta_cpf_global_forms_added = array_unique( $tm_meta_cpf_global_forms_added );

		$tm_meta_cpf_global_forms = apply_filters( 'wc_epo_additional_global_forms', $tm_meta_cpf_global_forms, $post_id, $form_prefix, $this );
		$tm_meta_cpf_global_forms = array_unique( $tm_meta_cpf_global_forms );

		foreach ( $tm_meta_cpf_global_forms as $key => $value ) {
			if ( ! in_array( $value, $tm_meta_cpf_global_forms_added, true ) ) {
				if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {

					$tm_meta_lang = themecomplete_get_post_meta( $value, THEMECOMPLETE_EPO_WPML_LANG_META, true );
					if ( empty( $tm_meta_lang ) ) {
						$tm_meta_lang = THEMECOMPLETE_EPO_WPML()->get_default_lang();
					}
					$meta_query   = THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'AND', THEMECOMPLETE_EPO_WPML_LANG_META, THEMECOMPLETE_EPO_WPML()->get_lang(), '=', 'EXISTS' );
					$meta_query[] = [
						'key'     => THEMECOMPLETE_EPO_WPML_PARENT_POSTID,
						'value'   => $value,
						'compare' => '=',
					];

					$query = new WP_Query(
						[
							'post_type'      => THEMECOMPLETE_EPO_GLOBAL_POST_TYPE,
							'post_status'    => [ 'publish' ],
							'numberposts'    => -1,
							'posts_per_page' => -1,
							'orderby'        => 'date',
							'order'          => 'asc',
							'no_found_rows'  => true,
							'meta_query'     => $meta_query, // phpcs:ignore WordPress.DB.SlowDBQuery
						]
					);

					if ( ! empty( $query->posts ) ) {
						if ( $query->post_count > 1 ) {

							foreach ( $query->posts as $current_post ) {
								if ( $current_post instanceof WP_Post ) {
									$metalang = themecomplete_get_post_meta( $current_post->ID, THEMECOMPLETE_EPO_WPML_LANG_META, true );

									if ( THEMECOMPLETE_EPO_WPML()->get_lang() === $metalang ) {
										$tmglobalprices[] = get_post( $current_post->ID );
										break;
									}
								}
							}
						} else {
							$tmglobalprices[] = get_post( $query->post->ID );
						}
					} elseif ( empty( $query->posts ) ) {
						$tmglobalprices[] = get_post( $value );
					}
				} else {
					$ispostactive = get_post( $value );
					if ( $ispostactive && 'publish' === $ispostactive->post_status ) {
						$tmglobalprices[] = get_post( $value );
					}
				}
			}
		}

		// Add current product to Global options array (has to be last to not conflict).
		$tmglobalprices[] = THEMECOMPLETE_EPO_HELPER()->get_cached_post( $post_id );

		$tmglobalprices = apply_filters( 'wc_epo_global_forms', $tmglobalprices, $post_id, $this, $variations_for_conditional_logic, $no_cache, $no_disabled );

		// End of DB init.

		$epos                        = $this->generate_global_epos( $tmglobalprices, $post_id, $this->tm_original_builder_elements, $variations_for_conditional_logic, $no_cache, $no_disabled );
		$global_epos                 = $epos['global'];
		$raw_epos                    = $epos['raw_epos'];
		$epos_prices                 = $epos['price'];
		$variation_element_id        = $epos['variation_element_id'];
		$variation_section_id        = $epos['variation_section_id'];
		$variations_disabled         = $epos['variations_disabled'];
		$global_product_epos_uniqids = $epos['product_epos_uniqids'];
		$product_epos_choices        = $epos['product_epos_choices'];

		if ( is_array( $global_epos ) ) {
			ksort( $global_epos );
		}

		$product_epos = $this->generate_local_epos( $tmlocalprices, $post_id );

		$global_epos = $this->tm_fill_element_names( $post_id, $global_epos, $product_epos, $form_prefix, 'epo' );

		$epos = [
			'global'               => $global_epos,
			'raw_epos'             => $raw_epos,
			'global_ids'           => $tmglobalprices,
			'local'                => $product_epos['product_epos'],
			'price'                => $epos_prices,
			'variation_element_id' => $variation_element_id,
			'variation_section_id' => $variation_section_id,
			'variations_disabled'  => $variations_disabled,
			'epos_uniqids'         => array_merge( $product_epos['product_epos_uniqids'], $global_product_epos_uniqids ),
			'product_epos_choices' => $product_epos_choices,
		];

		$this->cpf[ $post_id ][ "{$no_disabled}" ][ "f{$form_prefix}" ] = $epos;

		return $epos;
	}

	/**
	 * Generate normal (local) option array
	 *
	 * @param array<mixed> $tmlocalprices Array of posts for normal options.
	 * @param integer      $post_id The product id.
	 *
	 * @return array<mixed>
	 */
	public function generate_local_epos( $tmlocalprices, $post_id ) {
		$product_epos         = [];
		$product_epos_uniqids = [];
		if ( $tmlocalprices ) {
			THEMECOMPLETE_EPO_WPML()->remove_sql_filter();
			$attributes      = themecomplete_get_attributes( absint( THEMECOMPLETE_EPO_WPML()->get_original_id( $post_id ) ) );
			$wpml_attributes = themecomplete_get_attributes( $post_id );

			foreach ( $tmlocalprices as $price ) {

				$tmcp_id = absint( $price->ID );

				$n = themecomplete_get_post_meta( $tmcp_id, 'tmcp_attribute', true );
				if ( ! isset( $attributes[ $n ] ) ) {
					continue;
				}
				$att = $attributes[ $n ];
				if ( $att['is_variation'] || sanitize_title( $att['name'] ) !== $n ) {
					continue;
				}

				$tmcp_required                           = themecomplete_get_post_meta( $tmcp_id, 'tmcp_required', true );
				$tmcp_hide_price                         = themecomplete_get_post_meta( $tmcp_id, 'tmcp_hide_price', true );
				$tmcp_limit                              = themecomplete_get_post_meta( $tmcp_id, 'tmcp_limit', true );
				$product_epos[ $tmcp_id ]['is_form']     = 0;
				$product_epos[ $tmcp_id ]['required']    = empty( $tmcp_required ) ? 0 : 1;
				$product_epos[ $tmcp_id ]['hide_price']  = empty( $tmcp_hide_price ) ? 0 : 1;
				$product_epos[ $tmcp_id ]['limit']       = empty( $tmcp_limit ) ? '' : $tmcp_limit;
				$product_epos[ $tmcp_id ]['name']        = themecomplete_get_post_meta( $tmcp_id, 'tmcp_attribute', true );
				$product_epos[ $tmcp_id ]['is_taxonomy'] = themecomplete_get_post_meta( $tmcp_id, 'tmcp_attribute_is_taxonomy', true );
				$product_epos[ $tmcp_id ]['label']       = wc_attribute_label( $product_epos[ $tmcp_id ]['name'] );
				$product_epos[ $tmcp_id ]['type']        = themecomplete_get_post_meta( $tmcp_id, 'tmcp_type', true );
				$product_epos_uniqids[]                  = $product_epos[ $tmcp_id ]['name'];

				// Retrieve attributes.
				$product_epos[ $tmcp_id ]['attributes']      = [];
				$product_epos[ $tmcp_id ]['attributes_wpml'] = [];
				if ( $product_epos[ $tmcp_id ]['is_taxonomy'] ) {
					if ( ! ( $attributes[ $product_epos[ $tmcp_id ]['name'] ]['is_variation'] ) ) {
						$orderby = wc_attribute_orderby( $attributes[ $product_epos[ $tmcp_id ]['name'] ]['name'] );
						$args    = [
							'orderby'    => 'name',
							'hide_empty' => false,
						];
						switch ( $orderby ) {
							case 'name':
								$args = [
									'orderby'    => 'name',
									'hide_empty' => false,
									'menu_order' => false,
								];
								break;
							case 'id':
								$args = [
									'orderby'    => 'id',
									'order'      => 'ASC',
									'menu_order' => false,
									'hide_empty' => false,
								];
								break;
							case 'menu_order':
								$args = [
									'menu_order' => 'ASC',
									'hide_empty' => false,
								];
								break;
						}

						$all_terms = THEMECOMPLETE_EPO_WPML()->get_terms( null, $attributes[ $product_epos[ $tmcp_id ]['name'] ]['name'], $args );

						if ( $all_terms ) {
							foreach ( $all_terms as $term ) {
								if ( $term instanceof WP_Term ) {
									$has_term     = has_term( (int) $term->term_id, $attributes[ $product_epos[ $tmcp_id ]['name'] ]['name'], absint( THEMECOMPLETE_EPO_WPML()->get_original_id( $post_id ) ) ) ? 1 : 0;
									$wpml_term_id = THEMECOMPLETE_EPO_WPML()->is_active() && function_exists( 'icl_object_id' ) ? icl_object_id( $term->term_id, $attributes[ $product_epos[ $tmcp_id ]['name'] ]['name'], false ) : false;
									if ( $has_term ) {
										$product_epos[ $tmcp_id ]['attributes'][ esc_attr( $term->slug ) ] = apply_filters( 'woocommerce_tm_epo_option_name', esc_html( $term->name ), null, null );
										if ( $wpml_term_id ) {
											$wpml_term = get_term( $wpml_term_id, $attributes[ $product_epos[ $tmcp_id ]['name'] ]['name'] );
											if ( $wpml_term instanceof WP_Term ) {
												$product_epos[ $tmcp_id ]['attributes_wpml'][ esc_attr( $term->slug ) ] = apply_filters( 'woocommerce_tm_epo_option_name', esc_html( $wpml_term->name ), null, null );
											}
										} else {
											$product_epos[ $tmcp_id ]['attributes_wpml'][ esc_attr( $term->slug ) ] = $product_epos[ $tmcp_id ]['attributes'][ esc_attr( $term->slug ) ];
										}
									}
								}
							}
						}
					}
				} elseif ( isset( $attributes[ $product_epos[ $tmcp_id ]['name'] ] ) ) {
					$options      = array_map( 'trim', explode( WC_DELIMITER, $attributes[ $product_epos[ $tmcp_id ]['name'] ]['value'] ) );
					$wpml_options = isset( $wpml_attributes[ $product_epos[ $tmcp_id ]['name'] ]['value'] ) ? array_map( 'trim', explode( WC_DELIMITER, $wpml_attributes[ $product_epos[ $tmcp_id ]['name'] ]['value'] ) ) : $options;
					foreach ( $options as $k => $option ) {
						$product_epos[ $tmcp_id ]['attributes'][ esc_attr( sanitize_title( $option ) ) ]      = esc_html( apply_filters( 'woocommerce_tm_epo_option_name', $option, null, null ) );
						$product_epos[ $tmcp_id ]['attributes_wpml'][ esc_attr( sanitize_title( $option ) ) ] = esc_html( apply_filters( 'woocommerce_tm_epo_option_name', isset( $wpml_options[ $k ] ) ? $wpml_options[ $k ] : $option, null, null ) );
					}
				}

				// Retrieve price rules.
				$_regular_price                    = themecomplete_get_post_meta( $tmcp_id, '_regular_price', true );
				$_regular_price_type               = themecomplete_get_post_meta( $tmcp_id, '_regular_price_type', true );
				$product_epos[ $tmcp_id ]['rules'] = $_regular_price;

				$_regular_price_filtered                    = THEMECOMPLETE_EPO_HELPER()->array_map_deep( $_regular_price, $_regular_price_type, [ $this, 'tm_epo_price_filtered' ] );
				$product_epos[ $tmcp_id ]['rules_filtered'] = $_regular_price_filtered;

				$product_epos[ $tmcp_id ]['rules_type'] = $_regular_price_type;
				if ( ! is_array( $_regular_price ) ) {
					$_regular_price = [];
				}
				if ( ! is_array( $_regular_price_type ) ) {
					$_regular_price_type = [];
				}
				foreach ( $_regular_price as $key => $value ) {
					foreach ( $value as $k => $v ) {
						$_regular_price[ $key ][ $k ] = wc_format_localized_price( $v );
					}
				}
				foreach ( $_regular_price_type as $key => $value ) {
					foreach ( $value as $k => $v ) {
						$_regular_price_type[ $key ][ $k ] = $v;
					}
				}
				$product_epos[ $tmcp_id ]['price_rules']          = $_regular_price;
				$product_epos[ $tmcp_id ]['price_rules_filtered'] = $_regular_price_filtered;
				$product_epos[ $tmcp_id ]['price_rules_type']     = $_regular_price_type;
			}
			THEMECOMPLETE_EPO_WPML()->restore_sql_filter();
		}

		return [
			'product_epos'         => $product_epos,
			'product_epos_uniqids' => $product_epos_uniqids,
		];
	}

	/**
	 * Generate global (builder) option array
	 *
	 * @param array<mixed> $tmglobalprices Array of posts (global or directly on the product) that have saved options.
	 * @param integer      $post_id The product id.
	 * @param array<mixed> $tm_original_builder_elements Builder element attributes.
	 * @param array<mixed> $variations_for_conditional_logic The variations used in conditiona logic.
	 * @param boolean      $no_cache If we should use cached results.
	 * @param boolean      $no_disabled If disabled elements should be skipped.
	 * @param boolean      $add_templates_to_builder If the template element should be added to the internal builder array.
	 * @return array<mixed>
	 */
	public function generate_global_epos( $tmglobalprices, $post_id, $tm_original_builder_elements, $variations_for_conditional_logic = [], $no_cache = false, $no_disabled = false, $add_templates_to_builder = true ) {
		$global_epos              = [];
		$product_epos_uniqids     = [];
		$product_epos_choices     = [];
		$epos_prices              = [];
		$extra_section_logic      = [];
		$extra_section_hide_logic = [];
		$raw_epos                 = [];
		$builder_element_order    = [];

		$not_isset_global_post     = false;
		$not_isset_request_post_id = false;
		if ( ! isset( $GLOBALS['post'] ) ) {
			$not_isset_global_post = true;
			$GLOBALS['post']       = get_post( $post_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
			if ( wp_doing_ajax() && ! isset( $_REQUEST['post_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$not_isset_request_post_id = true;
				$_REQUEST['post_id']       = $post_id;
			}
		}

		$post_original_id = absint( THEMECOMPLETE_EPO_WPML()->get_original_id( $post_id ) );

		$variation_element_id = false;
		$variation_section_id = false;

		$enable_sales = 'sale' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_options_price_mode' );

		if ( $tmglobalprices ) {

			foreach ( $tmglobalprices as $price ) {
				$templates = [];
				if ( ! $price instanceof WP_Post ) {
					continue;
				}

				$original_product_id = $price->ID;
				$object              = $price;
				if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
					$wpml_is_original_product = THEMECOMPLETE_EPO_WPML()->is_original_product( $price->ID, $price->post_type );
					if ( ! $wpml_is_original_product ) {
						$original_product_id = absint( THEMECOMPLETE_EPO_WPML()->get_original_id( $price->ID, $price->post_type ) );
						if ( 'product' === $price->post_type ) {
							$object = wc_get_product( $original_product_id );
						} else {
							$object = get_post( $original_product_id );
						}
					}
				}

				$tmcp_id                     = absint( $original_product_id );
				$tmcp_meta                   = themecomplete_get_post_meta( $object, 'tm_meta', true );
				$enabled_roles               = themecomplete_get_post_meta( $object, 'tm_meta_enabled_roles', true );
				$disabled_roles              = themecomplete_get_post_meta( $object, 'tm_meta_disabled_roles', true );
				$tm_meta_product_ids         = themecomplete_get_post_meta( $object, 'tm_meta_product_ids', true );
				$tm_meta_product_exclude_ids = themecomplete_get_post_meta( $object, 'tm_meta_product_exclude_ids', true );

				if ( ! empty( $enabled_roles ) || ! empty( $disabled_roles ) ) {
					$enable = false;
					if ( ! is_array( $enabled_roles ) ) {
						$enabled_roles = [ $enabled_roles ];
					}
					if ( ! is_array( $disabled_roles ) ) {
						$disabled_roles = [ $disabled_roles ];
					}
					if ( isset( $enabled_roles[0] ) && '' === $enabled_roles[0] ) {
						$enabled_roles = [];
					}

					if ( isset( $disabled_roles[0] ) && '' === $disabled_roles[0] ) {
						$disabled_roles = [];
					}

					if ( empty( $enabled_roles ) && ! empty( $disabled_roles ) ) {
						$enable = true;
					}

					// Get all roles.
					$current_user = wp_get_current_user();

					foreach ( $enabled_roles as $key => $value ) {
						if ( '@everyone' === $value ) {
							$enable = true;
						}
						if ( '@loggedin' === $value && is_user_logged_in() ) {
							$enable = true;
						}
					}

					foreach ( $disabled_roles as $key => $value ) {
						if ( '@everyone' === $value ) {
							$enable = false;
						}
						if ( '@loggedin' === $value && is_user_logged_in() ) {
							$enable = false;
						}
					}

					if ( $current_user instanceof WP_User ) {
						$roles = $current_user->roles;

						if ( is_array( $roles ) ) {

							foreach ( $roles as $key => $value ) {
								if ( in_array( $value, $enabled_roles, true ) ) {
									$enable = true;
									break;
								}
							}

							foreach ( $roles as $key => $value ) {
								if ( in_array( $value, $disabled_roles, true ) ) {
									$enable = false;
									break;
								}
							}
						}
					}

					if ( ! $enable ) {
						continue;
					}
				}

				$current_builder = THEMECOMPLETE_EPO_WPML()->is_active() ? themecomplete_get_post_meta( $price, 'tm_meta_wpml', true ) : [];

				if ( ! $current_builder ) {
					$current_builder = [];
				} else {
					if ( ! isset( $current_builder['tmfbuilder'] ) ) {
						$current_builder['tmfbuilder'] = [];
					}
					$current_builder = $current_builder['tmfbuilder'];
				}

				$priority = isset( $tmcp_meta['priority'] ) ? absint( $tmcp_meta['priority'] ) : 1000;

				// This check is required in case the translated product loads before the original in $tmglobalprices.
				if ( isset( $global_epos[ $priority ][ $tmcp_id ] ) ) {
					if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
						if ( empty( $global_epos[ $priority ][ $tmcp_id ]['wpml_is_original_product'] ) ) {
							continue;
						}
					}
				}
				if ( THEMECOMPLETE_EPO_WPML()->is_active() && isset( $wpml_is_original_product ) ) {
					$global_epos[ $priority ][ $tmcp_id ]['original_product_id']      = $original_product_id;
					$global_epos[ $priority ][ $tmcp_id ]['wpml_is_original_product'] = $wpml_is_original_product;
				}

				if ( isset( $tmcp_meta['tmfbuilder'] ) ) {

					$global_epos[ $priority ][ $tmcp_id ]['is_form']     = 1;
					$global_epos[ $priority ][ $tmcp_id ]['is_taxonomy'] = 0;
					$global_epos[ $priority ][ $tmcp_id ]['name']        = $price->post_title;
					$global_epos[ $priority ][ $tmcp_id ]['description'] = $price->post_excerpt;
					$global_epos[ $priority ][ $tmcp_id ]['sections']    = [];

					$builder = $tmcp_meta['tmfbuilder'];
					if ( is_array( $builder ) && count( $builder ) > 0 && isset( $builder['element_type'] ) && is_array( $builder['element_type'] ) && count( $builder['element_type'] ) > 0 ) {
						// All the elements.
						$_elements = $builder['element_type'];
						// All element sizes.
						$_div_size = $builder['div_size'];

						// All sections (holds element count for each section).
						$_sections = $builder['sections'];
						// All section sizes.
						$_sections_size = $builder['sections_size'];
						// All section styles.
						$_sections_style = $builder['sections_style'];
						// All section placements.
						$_sections_placement = $builder['sections_placement'];

						$_sections_slides = isset( $builder['sections_slides'] ) ? $builder['sections_slides'] : '';

						if ( ! is_array( $_sections ) ) {
							$_sections = [ count( $_elements ) ];
						}
						if ( ! is_array( $_sections_size ) ) {
							$_sections_size = array_fill( 0, count( $_sections ), 'w100' );
						}
						if ( ! is_array( $_sections_style ) ) {
							$_sections_style = array_fill( 0, count( $_sections ), '' );
						}
						if ( ! is_array( $_sections_placement ) ) {
							$_sections_placement = array_fill( 0, count( $_sections ), 'before' );
						}

						if ( ! is_array( $_sections_slides ) ) {
							$_sections_slides = array_fill( 0, count( $_sections ), '' );
						}

						$_helper_counter = 0;
						$_counter        = [];
						$_sectionscount  = count( $_sections );

						for ( $_s = 0; $_s < $_sectionscount; $_s++ ) {
							$_sections_uniqid = $this->get_builder_element( 'sections_uniqid', $builder, $current_builder, $_s, THEMECOMPLETE_EPO_HELPER()->tm_temp_uniqid( count( $_sections ) ) );
							$_sections[ $_s ] = (int) $_sections[ $_s ];

							$sections_clogic     = $this->get_builder_element( 'sections_clogic', $builder, $current_builder, $_s, false, 'sections', '', $_sections_uniqid );
							$sections_logicrules = $this->get_builder_element( 'sections_logicrules', $builder, $current_builder, $_s, false, 'sections', '', $_sections_uniqid );
							if ( false === $sections_logicrules || 'false' === $sections_logicrules ) {
								$sections_logicrules = $sections_clogic;
							}

							$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ] = [
								'total_elements'           => $_sections[ $_s ],
								'sections_size'            => $_sections_size[ $_s ],
								'sections_slides'          => isset( $_sections_slides[ $_s ] ) ? $_sections_slides[ $_s ] : '',
								'sections_tabs_labels'     => $this->get_builder_element( 'sections_tabs_labels', $builder, $current_builder, $_s, '', 'sections', '', $_sections_uniqid ),
								'sections_style'           => $_sections_style[ $_s ],
								'sections_placement'       => $_sections_placement[ $_s ],
								'sections_uniqid'          => $_sections_uniqid,
								'sections_logicrules'      => $sections_logicrules,
								'sections_logic'           => $this->get_builder_element( 'sections_logic', $builder, $current_builder, $_s, '', 'sections', '', $_sections_uniqid ),
								'sections_class'           => $this->get_builder_element( 'sections_class', $builder, $current_builder, $_s, '', 'sections', '', $_sections_uniqid ),
								'sections_type'            => $this->get_builder_element( 'sections_type', $builder, $current_builder, $_s, '', 'sections', '', $_sections_uniqid ),
								'sections_popupbutton'     => $this->get_builder_element( 'sections_popupbutton', $builder, $current_builder, $_s, '', 'sections', '', $_sections_uniqid ),
								'sections_popupbuttontext' => $this->get_builder_element( 'sections_popupbuttontext', $builder, $current_builder, $_s, '', 'sections', '', $_sections_uniqid ),
								'sections_background_color' => $this->get_builder_element( 'sections_background_color', $builder, $current_builder, $_s, '', 'sections', '', $_sections_uniqid ),
								'label_background_color'   => $this->get_builder_element( 'sections_label_background_color', $builder, $current_builder, $_s, '', 'sections', '', $_sections_uniqid ),
								'description_background_color' => $this->get_builder_element( 'sections_subtitle_background_color', $builder, $current_builder, $_s, '', 'sections', '', $_sections_uniqid ),
								'label_size'               => $this->get_builder_element( 'section_header_size', $builder, $current_builder, $_s, '', 'sections', '', $_sections_uniqid ),
								'label'                    => $this->get_builder_element( 'section_header_title', $builder, $current_builder, $_s, '', 'sections', 'wc_epo_label', $_sections_uniqid ),
								'label_color'              => $this->get_builder_element( 'section_header_title_color', $builder, $current_builder, $_s, '', 'sections', '', $_sections_uniqid ),
								'label_position'           => $this->get_builder_element( 'section_header_title_position', $builder, $current_builder, $_s, '', 'sections', '', $_sections_uniqid ),
								'description'              => $this->get_builder_element( 'section_header_subtitle', $builder, $current_builder, $_s, '', 'sections', '', $_sections_uniqid ),
								'description_position'     => $this->get_builder_element( 'section_header_subtitle_position', $builder, $current_builder, $_s, '', 'sections', '', $_sections_uniqid ),
								'description_color'        => $this->get_builder_element( 'section_header_subtitle_color', $builder, $current_builder, $_s, '', 'sections', '', $_sections_uniqid ),
								'divider_type'             => $this->get_builder_element( 'section_divider_type', $builder, $current_builder, $_s, '', 'sections', '', $_sections_uniqid ),
							];

							$this->current_option_features[] = 'section' . $this->get_builder_element( 'sections_type', $builder, $current_builder, $_s, '', 'sections', '', $_sections_uniqid );

							$element_no_in_section      = -1;
							$element_real_no_in_section = -1;
							$section_slides             = $global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_slides'];
							if ( '' !== $section_slides && ( 'slider' === $global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_type'] || 'tabs' === $global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_type'] ) ) {
								$section_slides = explode( ',', $section_slides );

							}
							$section_slides_copy = $section_slides;
							for ( $k0 = $_helper_counter; $k0 < $_helper_counter + $_sections[ $_s ]; $k0++ ) {
								if ( ! isset( $_elements[ $k0 ] ) ) {
									continue;
								}

								++$element_no_in_section;
								$current_element = $_elements[ $k0 ];

								$is_override_element      = false;
								$original_current_element = $current_element;
								if ( ( $this->is_bto || $this->is_associated ) && 'product' === $current_element ) {
									$current_element     = 'header';
									$is_override_element = true;
								}
								$element_object = isset( $tm_original_builder_elements[ $current_element ] ) ? $tm_original_builder_elements[ $current_element ] : false;

								$raw_epos[] = $current_element;

								// Delete logic for variations section - not applicable.
								if ( 'variations' === $current_element ) {
									$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_logic']      = '';
									$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_logicrules'] = '';
								}

								if ( $element_object ) {
									if ( ! isset( $_counter[ $original_current_element ] ) ) {
										$_counter[ $original_current_element ] = 0;
									} else {
										++$_counter[ $original_current_element ];
									}
									$current_counter = $_counter[ $original_current_element ];

									if ( $is_override_element ) {
										if ( current_user_can( 'manage_options' ) ) {
											if ( $this->is_bto ) {
												$builder['header_title'][ $current_counter ]         = esc_html__( 'Product element is not supported for components!', 'woocommerce-tm-extra-product-options' );
												$current_builder['header_title'][ $current_counter ] = esc_html__( 'Product element is not supported for components!', 'woocommerce-tm-extra-product-options' );
											}
											if ( $this->is_associated ) {
												$builder['header_title'][ $current_counter ] = esc_html__( 'Product element is not supported within another product element!', 'woocommerce-tm-extra-product-options' );
											}
										}
									}

									$_options                         = [];
									$_options_all                     = []; // even disabled ones - currently used for WPML translation at get_wpml_translation_by_id.
									$_regular_price                   = [];
									$_regular_price_filtered          = [];
									$_original_regular_price_filtered = [];
									$_regular_price_type              = [];
									$_new_type                        = $current_element;
									$_prefix                          = '';
									$_min_price0                      = '';
									$_min_price10                     = '';
									$_min_price                       = '';
									$_max_price                       = '';
									$_regular_currencies              = [];
									$price_per_currencies_original    = [];
									$price_per_currencies             = [];
									$_description                     = false;
									$_extra_multiple_choices          = [];
									$_extra_addon_properties          = [];
									$_use_lightbox                    = '';
									$_current_deleted_choices         = [];
									$_is_price_fee                    = '';

									if (
										'dynamic' === $element_object->is_post ||
										( true === $element_object->is_addon && 'display' === $element_object->is_post ) ||
										( 'single' === $element_object->type || 'multipleallsingle' === $element_object->type ) ||
										( 'multiple' === $element_object->type || 'multipleall' === $element_object->type || 'multiplesingle' === $element_object->type || 'singlemultiple' === $element_object->type ) ||
										'template' === $element_object->type
										) {
										$_prefix = $original_current_element . '_';
									}

									$is_override_element_prefix = $is_override_element ? $original_current_element . '_' : $_prefix;

									$element_uniqueid = $this->get_builder_element( $is_override_element_prefix . 'uniqid', $builder, $current_builder, $current_counter, THEMECOMPLETE_EPO_HELPER()->tm_uniqid(), $original_current_element );

									$logic_prefix = ( 'header' === $original_current_element || 'divider' === $original_current_element ) ? $original_current_element . '_' : $_prefix;
									$clogic       = $this->get_builder_element( $logic_prefix . 'clogic', $builder, $current_builder, $current_counter, false, $current_element, '', $element_uniqueid );
									$logicrules   = $this->get_builder_element( $logic_prefix . 'logicrules', $builder, $current_builder, $current_counter, false, $current_element, '', $element_uniqueid );
									if ( false === $logicrules || 'false' === $logicrules ) {
										$logicrules = $clogic;
									}

									$is_enabled  = $this->get_builder_element( $is_override_element_prefix . 'enabled', $builder, $current_builder, $current_counter, '2', $original_current_element, '', $element_uniqueid );
									$is_required = $this->get_builder_element( $_prefix . 'required', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
									// Currently $no_disabled is disabled by default
									// to allow the conditional logic
									// to work correctly when there is a disabled element.
									if ( $no_disabled ) {
										if ( '' === $is_enabled || '0' === $is_enabled ) {

											if ( is_array( $section_slides ) ) {
												$elements_done = 0;
												foreach ( $section_slides as $section_slides_key => $section_slides_value ) {
													$section_slides_value = (int) $section_slides_value;
													$elements_done        = $elements_done + $section_slides_value;
													$previous_done        = $elements_done - $section_slides_value;

													if ( $element_no_in_section >= $previous_done && $element_no_in_section < $elements_done ) {
														$section_slides_copy[ $section_slides_key ]                                 = (string) ( (int) ( $section_slides_copy[ $section_slides_key ] ) - 1 );
														$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_slides'] = implode( ',', $section_slides_copy );
														break;
													}
												}
											}

											continue;
										}
									}

									++$element_real_no_in_section;

									$tm_epo_options_cache = ( ! $no_cache && 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_options_cache' ) ) ? true : false;

									if ( isset( $wpml_is_original_product ) && ! empty( $wpml_is_original_product ) && apply_filters( 'wc_epo_use_elements_cache', $tm_epo_options_cache ) && isset( $this->cpf_single[ $element_uniqueid ] ) ) {
										$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['elements'][] = $this->cpf_single[ $element_uniqueid ];
										if ( isset( $this->cpf_single_epos_prices[ $element_uniqueid ] ) ) {
											$epos_prices[] = $this->cpf_single_epos_prices[ $element_uniqueid ];
										}
										if ( isset( $this->cpf_single_variation_element_id[ $element_uniqueid ] ) ) {
											$variation_element_id = $this->cpf_single_variation_element_id[ $element_uniqueid ];
										}
										if ( isset( $this->cpf_single_variation_section_id[ $element_uniqueid ] ) ) {
											$variation_section_id = $this->cpf_single_variation_section_id[ $element_uniqueid ];
										}

										continue;
									}

									if ( $is_enabled && 'template' === $current_element ) {
										$templateids = $this->get_builder_element( $_prefix . 'templateids', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
										if ( empty( $templateids ) ) {
											continue;
										}
										$templateglobalprices = get_post( $templateids );
										if ( $templateglobalprices ) {
											if ( 'publish' !== $templateglobalprices->post_status ) {
												continue;
											}
										} else {
											continue;
										}
										$templateglobalprices = [ $templateglobalprices ];
										$template_elements    = $this->generate_global_epos( $templateglobalprices, $post_id, $tm_original_builder_elements, $variations_for_conditional_logic, $no_cache, $no_disabled, false );

										// Add template elements
										// Each foreach loop should produce only 1 result!
										if ( isset( $template_elements['global'] ) ) {
											$added_element_uniqid = false;
											foreach ( $template_elements['global'] as $pid_element ) {
												foreach ( $pid_element as $id_element ) {
													foreach ( $id_element['sections'] as $id_sections ) {
														if ( isset( $id_sections['elements'] ) ) {
															foreach ( $id_sections['elements'] as $added_element ) {
																$added_element_uniqid        = $added_element['uniqid'];
																$added_element['size']       = $_div_size[ $k0 ];
																$added_element['enabled']    = $is_enabled; // Always true.
																$added_element['uniqid']     = $element_uniqueid;
																$added_element['logicrules'] = $logicrules;
																$added_element['logic']      = $this->get_builder_element( $_prefix . 'logic', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
																$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['elements'][] = $added_element;
																$this->cpf_single[ $element_uniqueid ]                                 = $added_element;
																if ( ! isset( $builder_element_order[ $added_element['type'] ] ) ) {
																	$builder_element_order[ $added_element['type'] ] = 0;
																} else {
																	$builder_element_order[ $added_element['type'] ] = $builder_element_order[ $added_element['type'] ] + 1;
																}
																if ( $add_templates_to_builder && isset( $added_element['builder'] ) ) {
																	$templates[ $builder_element_order[ $added_element['type'] ] ] = $added_element;
																	$template_builder = $added_element['builder'];
																	$placement        = $builder_element_order[ $added_element['type'] ];
																	foreach ( $template_builder as $key => $value ) {
																		if ( str_ends_with( $key, '_subscriptionfee' ) && isset( $builder[ $key ] ) ) {
																			array_splice( $builder[ $key ], $placement, 0, $value );
																		}
																	}
																}
															}
														}
													}
												}
											}
											$product_epos_uniqids[]                    = $element_uniqueid;
											$product_epos_choices[ $element_uniqueid ] = $template_elements['product_epos_choices'];
											// This should always be true!
											if ( $added_element_uniqid ) {
												if ( isset( $product_epos_choices[ $element_uniqueid ][ $added_element_uniqid ] ) ) {
													$product_epos_choices[ $element_uniqueid ] = $product_epos_choices[ $element_uniqueid ][ $added_element_uniqid ];
												}
											}
										}
										continue;
									}

									if ( isset( $builder[ $current_element . '_fee' ] ) && isset( $builder[ $current_element . '_fee' ][ $current_counter ] ) ) {
										$_is_price_fee = $builder[ $current_element . '_fee' ][ $current_counter ];
									}

									// Backwards compatibility.
									$swatchmode   = $this->get_builder_element( $_prefix . 'swatchmode', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
									$show_tooltip = $this->get_builder_element( $_prefix . 'show_tooltip', $builder, $current_builder, $current_counter, '0', $current_element, '', $element_uniqueid );
									if ( '0' === $show_tooltip ) {
										$show_tooltip = $swatchmode;
									}

									if ( 'single' === $element_object->type || 'multipleallsingle' === $element_object->type ) {
										$_prefix = $current_element . '_';

										$_is_field_required     = $this->get_builder_element( $_prefix . 'required', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
										$_changes_product_image = $this->get_builder_element( $_prefix . 'changes_product_image', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
										$_replacement_mode      = $this->get_builder_element( $_prefix . 'replacement_mode', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
										$_swatch_position       = $this->get_builder_element( $_prefix . 'swatch_position', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
										$_use_images            = $this->get_builder_element( $_prefix . 'use_images', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
										$_use_colors            = $this->get_builder_element( $_prefix . 'use_colors', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
										$_price                 = isset( $builder[ $current_element . '_price' ] ) ? $builder[ $current_element . '_price' ][ $current_counter ] : '';
										$_price                 = $this->get_builder_element( $_prefix . 'price', $builder, $current_builder, $current_counter, $_price, $current_element, 'wc_epo_option_regular_price', $element_uniqueid );

										foreach ( THEMECOMPLETE_EPO_BUILDER()->extra_addon_properties['settings'] as $__key => $__name ) {
											$_extra_name                             = $__name['id'];
											$_extra_addon_properties[ $_extra_name ] = $this->get_builder_element( $current_element . '_' . $_extra_name, $builder, $current_builder, $current_counter, [], $current_element, '', $element_uniqueid );
										}

										$_original_regular_price_filtered = $_price;
										if ( $enable_sales && isset( $builder[ $current_element . '_sale_price' ][ $current_counter ] ) && '' !== $builder[ $current_element . '_sale_price' ][ $current_counter ] ) {
											$_price = $builder[ $current_element . '_sale_price' ][ $current_counter ];
											$_price = $this->get_builder_element( $_prefix . 'sale_price', $builder, $current_builder, $current_counter, $_price, $current_element, 'wc_epo_option_sale_price', $element_uniqueid );
										}

										$_price                           = apply_filters( 'wc_epo_apply_discount', $_price, $_price, $post_id );
										$_original_regular_price_filtered = apply_filters( 'wc_epo_enable_shortocde', $_original_regular_price_filtered, $_original_regular_price_filtered, $post_id );

										$this_price_type   = '';
										$this_price_type_o = '';

										$_regular_price_type    = [ [ '' ] ];
										$_for_filter_price_type = '';

										// backwards compatiiblity.
										if ( isset( $builder[ $current_element . '_price_type' ][ $current_counter ] ) ) {
											$_regular_price_type = $builder[ $current_element . '_price_type' ][ $current_counter ];
											$this_price_type     = $_regular_price_type;
											$this_price_type_o   = $_regular_price_type;

											switch ( $_regular_price_type ) {
												case 'fee':
													$_regular_price_type = '';
													$_is_price_fee       = '1';
													break;
												case 'stepfee':
													$_regular_price_type = 'step';
													$_is_price_fee       = '1';
													break;
												case 'currentstepfee':
													$_regular_price_type = 'currentstep';
													$_is_price_fee       = '1';
													break;
											}
											$_for_filter_price_type = $_regular_price_type;
											$_regular_price_type    = [ [ $_regular_price_type ] ];
										}

										if ( 'math' === $this_price_type || 'lookuptable' === $this_price_type ) {
											$_regular_price = [ [ $_price ] ];
										} else {
											$_regular_price = [ [ wc_format_decimal( $_price, false, true ) ] ];
										}

										$lookuptable   = $this->get_builder_element( $_prefix . 'lookuptable', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
										$lookuptable_x = $this->get_builder_element( $_prefix . 'lookuptable_x', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
										$lookuptable_y = $this->get_builder_element( $_prefix . 'lookuptable_y', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
										if ( 'lookuptable' === $this_price_type ) {
											$table = THEMECOMPLETE_EPO_HELPER()->reverse_strrchr( $lookuptable, '|' );
											if ( is_string( $table ) ) {
												$table = trim( $table );
											} else {
												$table = '';
											}
											$table_name = strrchr( $lookuptable, '|' );
											if ( false !== $table_name ) {
												$table_num = trim( substr( $lookuptable, ( 0 - ( tc_strlen( $table_name ) - 1 ) ) ) );
											} else {
												$table_num = '0';
											}

											$xy            = '0';
											$lookuptable_x = trim( $lookuptable_x );
											$lookuptable_y = trim( $lookuptable_y );
											if ( ! empty( $lookuptable_x ) ) {
												if ( ! str_starts_with( $lookuptable_x, '{' ) ) {
													$lookuptable_x = '{field.' . $lookuptable_x . '.text}';
												}
											}
											if ( ! empty( $lookuptable_y ) ) {
												if ( ! str_starts_with( $lookuptable_y, '{' ) ) {
													$lookuptable_y = '{field.' . $lookuptable_y . '.text}';
												}
											}
											if ( ! empty( $lookuptable_x ) && ! empty( $lookuptable_y ) ) {
												$xy = '[' . $lookuptable_x . ', ' . $lookuptable_y . ']';
											} elseif ( ! empty( $lookuptable_x ) ) {
												$xy = $lookuptable_x;
											} elseif ( ! empty( $lookuptable_y ) ) {
												$xy = $lookuptable_y;
											}
											$this_price_type                  = 'math';
											$_regular_price_type              = [ [ $this_price_type ] ];
											$_price                           = 'lookuptable(' . $xy . ', ["' . $table . '", ' . $table_num . '])';
											$_original_regular_price_filtered = $_price;
											$_regular_price                   = [ [ $_price ] ];
										}

										if ( THEMECOMPLETE_EPO_WPML()->is_active() && THEMECOMPLETE_EPO_WPML()->is_multi_currency() ) {
											global $woocommerce_wpml;
											global $sitepress;
											if ( $woocommerce_wpml && isset( $wpml_is_original_product ) ) {

												$basetype     = $price->post_type;
												$translations = THEMECOMPLETE_EPO_WPML()->sitepress_instance()->get_element_translations( THEMECOMPLETE_EPO_WPML()->sitepress_instance()->get_element_trid( $original_product_id, 'post_product' ), 'product' );

												$woocommerce_wpml_currencies = $woocommerce_wpml->settings['currency_options'];

												foreach ( $woocommerce_wpml_currencies as $currency => $currency_data ) {

													$thisbuilder = [];

													if ( ! isset( $currency_data['languages'] ) ) {
														continue;
													}

													foreach ( $currency_data['languages'] as $lang => $is_lang_enabled ) {
														if ( $is_lang_enabled && THEMECOMPLETE_EPO_WPML()->get_lang() === $lang ) {
															if ( 'product' === $basetype ) {
																if ( isset( $translations[ $lang ] ) ) {
																	$this_wpml_is_original_product = THEMECOMPLETE_EPO_WPML()->is_original_product( $translations[ $lang ]->element_id, 'product' );
																	if ( $this_wpml_is_original_product && THEMECOMPLETE_EPO_WPML()->get_lang() === $lang ) {
																		$thisbuilder = $builder;
																	} else {
																		$thisbuilder = themecomplete_get_post_meta( $translations[ $lang ]->element_id, ( $this_wpml_is_original_product ? 'tm_meta' : 'tm_meta_wpml' ), true );
																		if ( isset( $thisbuilder['tmfbuilder'] ) ) {
																			$thisbuilder = $thisbuilder['tmfbuilder'];
																		} else {
																			$thisbuilder = themecomplete_get_post_meta( $original_product_id, 'tm_meta', true );
																			if ( isset( $thisbuilder['tmfbuilder'] ) ) {
																				$thisbuilder = $thisbuilder['tmfbuilder'];
																			} else {
																				$thisbuilder = [];
																			}
																		}
																	}
																}
															} elseif ( $wpml_is_original_product && THEMECOMPLETE_EPO_WPML()->get_lang() === $lang ) {
																$thisbuilder = $builder;
															} else {
																$args                 = [
																	'post_type'   => $basetype,
																	'post_status' => [ 'publish', 'draft' ], // get only enabled global extra options.
																	'numberposts' => -1,
																	'orderby'     => 'date',
																	'order'       => 'asc',
																];
																$args['meta_query']   = THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'AND', THEMECOMPLETE_EPO_WPML_LANG_META, $lang, '=', 'EXISTS' ); // phpcs:ignore WordPress.DB.SlowDBQuery
																$args['meta_query'][] = [
																	'key'     => THEMECOMPLETE_EPO_WPML_PARENT_POSTID,
																	'value'   => $original_product_id,
																	'compare' => '=',
																];
																$other_translations   = THEMECOMPLETE_EPO_HELPER()->get_cached_posts( $args );

																if ( ! empty( $other_translations ) && isset( $other_translations[0] ) && is_object( $other_translations[0] ) && property_exists( $other_translations[0], 'ID' ) ) {
																	$this_wpml_is_original_product = THEMECOMPLETE_EPO_WPML()->is_original_product( $other_translations[0]->ID, $basetype );
																	$thisbuilder                   = themecomplete_get_post_meta( $other_translations[0]->ID, ( $this_wpml_is_original_product ? 'tm_meta' : 'tm_meta_wpml' ), true );
																	if ( isset( $thisbuilder['tmfbuilder'] ) ) {
																		$thisbuilder = $thisbuilder['tmfbuilder'];
																	} else {
																		$thisbuilder = themecomplete_get_post_meta( $original_product_id, 'tm_meta', true );
																		if ( isset( $thisbuilder['tmfbuilder'] ) ) {
																			$thisbuilder = $thisbuilder['tmfbuilder'];
																		} else {
																			$thisbuilder = [];
																		}
																	}
																}
															}

															break;
														}
													}
													if ( empty( $thisbuilder ) ) {
														$thisbuilder = $builder;
													}
													if ( $currency !== $woocommerce_wpml->multi_currency->get_default_currency() ) {
														$_current_currency_price = ( isset( $thisbuilder[ $current_element . '_price_' . $currency ] ) && isset( $thisbuilder[ $current_element . '_price_' . $currency ][ $current_counter ] ) && '' !== $thisbuilder[ $current_element . '_price_' . $currency ][ $current_counter ] ) ? $thisbuilder[ $current_element . '_price_' . $currency ][ $current_counter ] : '';
														if ( '' === $_current_currency_price ) {
															if ( isset( $builder[ $current_element . '_price_' . $currency ] ) && isset( $builder[ $current_element . '_price_' . $currency ][ $current_counter ] ) && '' !== $builder[ $current_element . '_price_' . $currency ][ $current_counter ] ) {
																$_current_currency_price = $builder[ $current_element . '_price_' . $currency ][ $current_counter ];
															}
														}
														$_current_currency_sale_price = ( isset( $thisbuilder[ $current_element . '_sale_price_' . $currency ] ) && isset( $thisbuilder[ $current_element . '_sale_price_' . $currency ][ $current_counter ] ) && '' !== $thisbuilder[ $current_element . '_sale_price_' . $currency ][ $current_counter ] ) ? $thisbuilder[ $current_element . '_sale_price_' . $currency ][ $current_counter ] : '';
														if ( '' === $_current_currency_sale_price ) {
															if ( isset( $builder[ $current_element . '_sale_price_' . $currency ] ) && isset( $builder[ $current_element . '_sale_price_' . $currency ][ $current_counter ] ) && '' !== $builder[ $current_element . '_sale_price_' . $currency ][ $current_counter ] ) {
																$_current_currency_sale_price = $builder[ $current_element . '_sale_price_' . $currency ][ $current_counter ];
															}
														}
													} else {
														$_current_currency_price = isset( $thisbuilder[ $current_element . '_price' ][ $current_counter ] ) ? $thisbuilder[ $current_element . '_price' ][ $current_counter ] : '';
														if ( '' === $_current_currency_price ) {
															if ( isset( $builder[ $current_element . '_price' ][ $current_counter ] ) && '' !== $builder[ $current_element . '_price' ][ $current_counter ] ) {
																$_current_currency_price = $builder[ $current_element . '_price' ][ $current_counter ];
															}
														}
														$_current_currency_sale_price = isset( $thisbuilder[ $current_element . '_sale_price' ][ $current_counter ] ) ? $thisbuilder[ $current_element . '_sale_price' ][ $current_counter ] : '';
														if ( '' === $_current_currency_sale_price ) {
															if ( isset( $builder[ $current_element . '_sale_price' ][ $current_counter ] ) && '' !== $builder[ $current_element . '_sale_price' ][ $current_counter ] ) {
																$_current_currency_sale_price = $builder[ $current_element . '_sale_price' ][ $current_counter ];
															}
														}
													}
													if ( ! ( isset( $thisbuilder[ 'multiple_' . $current_element . '_options_price_' . $currency ] ) && isset( $thisbuilder[ 'multiple_' . $current_element . '_options_price_' . $currency ][ $current_counter ] ) ) && ! ( isset( $builder[ 'multiple_' . $current_element . '_options_price_' . $currency ] ) && isset( $builder[ 'multiple_' . $current_element . '_options_price_' . $currency ][ $current_counter ] ) ) ) {
														if ( is_array( $_current_currency_price ) ) {
															foreach ( $_current_currency_price as $_k => $_v ) {
																if ( isset( $_prices_type[ $_k ] ) && 'math' === $_prices_type[ $_k ] ) {
																	$_current_currency_price[ $_k ] = $_v;
																} elseif ( '' !== $_v ) {
																	$_current_currency_price[ $_k ] = THEMECOMPLETE_EPO_WPML()->get_price_in_currency( $_v, $currency );
																}
															}
														}
													}
													if ( ! ( isset( $thisbuilder[ 'multiple_' . $current_element . '_options_sale_price_' . $currency ] ) && isset( $thisbuilder[ 'multiple_' . $current_element . '_options_sale_price_' . $currency ][ $current_counter ] ) ) && ! ( isset( $builder[ 'multiple_' . $current_element . '_options_sale_price_' . $currency ] ) && isset( $builder[ 'multiple_' . $current_element . '_options_sale_price_' . $currency ][ $current_counter ] ) ) ) {
														if ( is_array( $_current_currency_sale_price ) ) {
															foreach ( $_current_currency_sale_price as $_k => $_v ) {
																if ( isset( $_prices_type[ $_k ] ) && 'math' === $_prices_type[ $_k ] ) {
																	$_current_currency_sale_price[ $_k ] = $_v;
																} elseif ( '' !== $_v ) {
																	$_current_currency_sale_price[ $_k ] = THEMECOMPLETE_EPO_WPML()->get_price_in_currency( $_v, $currency );
																}
															}
														}
													}

													if ( 'math' === $this_price_type || 'lookuptable' === $this_price_type_o ) {
														$price_per_currencies_original[ $currency ] = [ [ $_current_currency_price ] ];
													} else {
														$price_per_currencies_original[ $currency ] = [ [ wc_format_decimal( $_current_currency_price, false, true ) ] ];
													}

													$_current_currency_price          = apply_filters( 'wc_epo_option_regular_price' . $currency, $_current_currency_price, $_prefix . 'price' . $currency, $element_uniqueid );
													$_current_currency_sale_price     = apply_filters( 'wc_epo_option_sale_price' . $currency, $_current_currency_price, $_prefix . 'sale_price' . $currency, $element_uniqueid );
													$_original_current_currency_price = $_current_currency_price;

													if ( $enable_sales && $_current_currency_sale_price && '' !== $_current_currency_sale_price ) {
														$_current_currency_price = $_current_currency_sale_price;
													}
													$_current_currency_price = apply_filters( 'wc_epo_apply_discount', $_current_currency_price, $_original_current_currency_price, $post_id );

													if ( 'math' === $this_price_type || 'lookuptable' === $this_price_type_o ) {
														$price_per_currencies[ $currency ] = [ [ $_current_currency_price ] ];
													} else {
														$price_per_currencies[ $currency ] = [ [ wc_format_decimal( $_current_currency_price, false, true ) ] ];
													}
												}
											}
										} else {
											foreach ( THEMECOMPLETE_EPO_HELPER()->get_currencies() as $currency ) {
												$mt_prefix = THEMECOMPLETE_EPO_HELPER()->get_currency_price_prefix( $currency );

												if ( '' === $mt_prefix ) {
													$_current_currency_price          = $_price;
													$_original_current_currency_price = $_current_currency_price;
												} else {
													$_current_currency_price          = isset( $builder[ $current_element . '_price' . $mt_prefix ][ $current_counter ] ) ? $builder[ $current_element . '_price' . $mt_prefix ][ $current_counter ] : '';
													$_current_currency_sale_price     = isset( $builder[ $current_element . '_sale_price' . $mt_prefix ][ $current_counter ] ) ? $builder[ $current_element . '_sale_price' . $mt_prefix ][ $current_counter ] : '';
													$_current_currency_price          = $this->get_builder_element( $_prefix . 'price' . $mt_prefix, $builder, $current_builder, $current_counter, $_current_currency_price, $current_element, 'wc_epo_option_regular_price' . $mt_prefix, $element_uniqueid );
													$_current_currency_sale_price     = $this->get_builder_element( $_prefix . 'sale_price' . $mt_prefix, $builder, $current_builder, $current_counter, $_current_currency_sale_price, $current_element, 'wc_epo_option_sale_price' . $mt_prefix, $element_uniqueid );
													$_original_current_currency_price = $_current_currency_price;
													if ( $enable_sales && $_current_currency_sale_price && '' !== $_current_currency_sale_price ) {
														$_current_currency_price = $_current_currency_sale_price;
													}
													$_current_currency_price = apply_filters( 'wc_epo_apply_discount', $_current_currency_price, $_original_current_currency_price, $post_id );
												}

												if ( '' !== $_original_current_currency_price ) {
													if ( 'math' === $this_price_type || 'lookuptable' === $this_price_type_o ) {
														$price_per_currencies_original[ $currency ] = [ [ $_original_current_currency_price ] ];
													} else {
														$price_per_currencies_original[ $currency ] = [ [ wc_format_decimal( $_original_current_currency_price, false, true ) ] ];
													}
												}

												if ( '' !== $_current_currency_price ) {
													if ( 'math' === $this_price_type || 'lookuptable' === $this_price_type_o ) {
														$price_per_currencies[ $currency ] = [ [ $_current_currency_price ] ];
													} else {
														$price_per_currencies[ $currency ] = [ [ wc_format_decimal( $_current_currency_price, false, true ) ] ];
													}
												}
											}
										}

										$new_currency = false;
										$mt_prefix    = THEMECOMPLETE_EPO_HELPER()->get_currency_price_prefix( null, '' );

										$_current_currency_original_price = isset( $price_per_currencies_original[ $mt_prefix ] ) ? $price_per_currencies_original[ $mt_prefix ][0][0] : '';
										$_current_currency_price          = isset( $price_per_currencies[ $mt_prefix ] ) ? $price_per_currencies[ $mt_prefix ][0][0] : '';

										if ( THEMECOMPLETE_EPO_WPML()->is_active() && THEMECOMPLETE_EPO_WPML()->is_multi_currency() ) {
											global $woocommerce_wpml;
											if ( '' === $_current_currency_original_price && $mt_prefix !== $woocommerce_wpml->multi_currency->get_default_currency() ) {
												$_current_currency_original_price = isset( $price_per_currencies_original[ $woocommerce_wpml->multi_currency->get_client_currency() ] ) ? $price_per_currencies_original[ $woocommerce_wpml->multi_currency->get_client_currency() ][0][0] : '';
												if ( '' !== $_current_currency_original_price && 'math' !== $this_price_type && 'lookuptable' !== $this_price_type ) {
													$_current_currency_original_price = apply_filters( 'wc_epo_get_current_currency_price', $_current_currency_original_price, $this_price_type, null, $woocommerce_wpml->multi_currency->get_client_currency() );
												}
											}
											if ( '' === $_current_currency_price && $mt_prefix !== $woocommerce_wpml->multi_currency->get_default_currency() ) {
												$_current_currency_price = isset( $price_per_currencies[ $woocommerce_wpml->multi_currency->get_client_currency() ] ) ? $price_per_currencies[ $woocommerce_wpml->multi_currency->get_client_currency() ][0][0] : '';
												if ( '' !== $_current_currency_price && 'math' !== $this_price_type && 'lookuptable' !== $this_price_type ) {
													$_current_currency_price = apply_filters( 'wc_epo_get_current_currency_price', $_current_currency_price, $this_price_type, null, $woocommerce_wpml->multi_currency->get_client_currency() );
												}
											}
										}

										if ( '' !== $mt_prefix && '' !== $_current_currency_price ) {
											$_price                           = $_current_currency_price;
											$_original_regular_price_filtered = $_current_currency_original_price;

											$_regular_currencies = [ themecomplete_get_woocommerce_currency() ];
											$new_currency        = true;
										}

										if ( ! $new_currency ) {
											$_price                           = apply_filters( 'wc_epo_get_current_currency_price', $_price, $_for_filter_price_type, null, $mt_prefix );
											$_original_regular_price_filtered = apply_filters( 'wc_epo_get_current_currency_price', $_original_regular_price_filtered, $_for_filter_price_type, null, $mt_prefix );
										}

										$_price                           = apply_filters( 'wc_epo_price', $_price, $_for_filter_price_type, $post_id );
										$_original_regular_price_filtered = apply_filters( 'wc_epo_price', $_original_regular_price_filtered, $_for_filter_price_type, $post_id );

										if ( '' === $_is_price_fee && '' !== $_price && isset( $builder[ $current_element . '_price_type' ][ $current_counter ] ) && '' === $builder[ $current_element . '_price_type' ][ $current_counter ] ) {
											$_min_price = wc_format_decimal( $_price, false, true );
											$_max_price = $_min_price;
											if ( $_is_field_required ) {
												$_min_price0 = $_min_price;
											} else {
												$_min_price0  = 0;
												$_min_price10 = $_min_price;
											}
										} else {
											$_min_price  = false;
											$_max_price  = $_min_price;
											$_min_price0 = 0;
										}

										if ( 'math' === $this_price_type ) {
											$_regular_price_filtered          = [ [ $_price ] ];
											$_original_regular_price_filtered = [ [ $_original_regular_price_filtered ] ];
										} else {
											$_regular_price_filtered          = [ [ wc_format_decimal( $_price, false, true ) ] ];
											$_original_regular_price_filtered = [ [ wc_format_decimal( $_original_regular_price_filtered, false, true ) ] ];
										}
									} elseif ( 'multiple' === $element_object->type || 'multipleall' === $element_object->type || 'multiplesingle' === $element_object->type || 'singlemultiple' === $element_object->type ) {
										$_prefix = $current_element . '_';

										$_is_field_required = $this->get_builder_element( $_prefix . 'required', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );

										$_changes_product_image = $this->get_builder_element( $_prefix . 'changes_product_image', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
										$_replacement_mode      = $this->get_builder_element( $_prefix . 'replacement_mode', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
										$_swatch_position       = $this->get_builder_element( $_prefix . 'swatch_position', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
										$_use_images            = $this->get_builder_element( $_prefix . 'use_images', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
										$_use_colors            = $this->get_builder_element( $_prefix . 'use_colors', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
										$_use_lightbox          = $this->get_builder_element( $_prefix . 'use_lightbox', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );

										foreach ( THEMECOMPLETE_EPO_BUILDER()->extra_addon_properties['settings'] as $__key => $__name ) {
											$_extra_name                             = $__name['id'];
											$_extra_addon_properties[ $_extra_name ] = $this->get_builder_element( $current_element . '_' . $_extra_name, $builder, $current_builder, $current_counter, [], $current_element, '', $element_uniqueid );
										}

										if ( isset( $builder[ 'multiple_' . $current_element . '_options_price' ][ $current_counter ] ) ) {

											$_prices = $builder[ 'multiple_' . $current_element . '_options_price' ][ $current_counter ];
											$_prices = $this->get_builder_element( 'multiple_' . $current_element . '_options_price', $builder, $current_builder, $current_counter, $_prices, $current_element, 'wc_epo_multiple_prices', $element_uniqueid );

											$_original_prices = $_prices;
											$_sale_prices     = $_prices;
											if ( $enable_sales && isset( $builder[ 'multiple_' . $current_element . '_options_sale_price' ][ $current_counter ] ) ) {
												$_sale_prices = $builder[ 'multiple_' . $current_element . '_options_sale_price' ][ $current_counter ];
												$_sale_prices = $this->get_builder_element( 'multiple_' . $current_element . '_sale_prices', $builder, $current_builder, $current_counter, $_sale_prices, $current_element, 'wc_epo_multiple_sale_prices', $element_uniqueid );
											}
											$_prices = THEMECOMPLETE_EPO_HELPER()->merge_price_array( $_prices, $_sale_prices );
											$_prices = apply_filters( 'wc_epo_apply_discount', $_prices, $_original_prices, $post_id );

											$_original_prices = apply_filters( 'wc_epo_enable_shortocde', $_original_prices, $_original_prices, $post_id );

											$_values      = $this->get_builder_element( 'multiple_' . $current_element . '_options_value', $builder, $current_builder, $current_counter, [], $current_element, 'wc_epo_multiple_values', $element_uniqueid );
											$_titles      = $this->get_builder_element( 'multiple_' . $current_element . '_options_title', $builder, $current_builder, $current_counter, [], $current_element, 'wc_epo_multiple_titles', $element_uniqueid );
											$_images      = $this->get_builder_element( 'multiple_' . $current_element . '_options_image', $builder, $current_builder, $current_counter, [], $current_element, 'tm_image_url', $element_uniqueid );
											$_imagesc     = $this->get_builder_element( 'multiple_' . $current_element . '_options_imagec', $builder, $current_builder, $current_counter, [], $current_element, 'tm_image_url', $element_uniqueid );
											$_imagesp     = $this->get_builder_element( 'multiple_' . $current_element . '_options_imagep', $builder, $current_builder, $current_counter, [], $current_element, 'tm_image_url', $element_uniqueid );
											$_imagesl     = $this->get_builder_element( 'multiple_' . $current_element . '_options_imagel', $builder, $current_builder, $current_counter, [], $current_element, 'tm_image_url', $element_uniqueid );
											$_color       = $this->get_builder_element( 'multiple_' . $current_element . '_options_color', $builder, $current_builder, $current_counter, [], $current_element, '', $element_uniqueid );
											$_prices_type = $this->get_builder_element( 'multiple_' . $current_element . '_options_price_type', $builder, $current_builder, $current_counter, [], $current_element, '', $element_uniqueid );

											if ( ! is_array( $_values ) ) {
												$_values = [ $_values ];
											}
											if ( ! is_array( $_titles ) ) {
												$_titles = [ $_titles ];
											}
											// Check values in case they are empty and set them to title.
											foreach ( $_titles as $i => $_title ) {
												if ( '' === $_values[ $i ] ) {
													$_values[ $i ] = $_titles[ $i ];
												}
											}
											if ( ! is_array( $_images ) ) {
												$_images = [ $_images ];
											}
											if ( ! is_array( $_imagesc ) ) {
												$_imagesc = [ $_imagesc ];
											}
											if ( ! is_array( $_imagesp ) ) {
												$_imagesp = [ $_imagesp ];
											}
											if ( ! is_array( $_imagesl ) ) {
												$_imagesl = [ $_imagesl ];
											}
											if ( ! is_array( $_color ) ) {
												$_color = [ $_color ];
											}
											if ( ! is_array( $_prices_type ) ) {
												$_prices_type = [ $_prices_type ];
											}

											if ( THEMECOMPLETE_EPO_WPML()->is_active() && THEMECOMPLETE_EPO_WPML()->is_multi_currency() ) {
												global $woocommerce_wpml;
												global $sitepress;
												if ( $woocommerce_wpml && isset( $wpml_is_original_product ) ) {

													$basetype     = $price->post_type;
													$translations = THEMECOMPLETE_EPO_WPML()->sitepress_instance()->get_element_translations( THEMECOMPLETE_EPO_WPML()->sitepress_instance()->get_element_trid( $original_product_id, 'post_product' ), 'product' );

													$woocommerce_wpml_currencies = $woocommerce_wpml->settings['currency_options'];

													foreach ( $woocommerce_wpml_currencies as $currency => $currency_data ) {

														$thisbuilder = [];

														if ( ! isset( $currency_data['languages'] ) ) {
															continue;
														}

														foreach ( $currency_data['languages'] as $lang => $is_lang_enabled ) {

															if ( $is_lang_enabled && THEMECOMPLETE_EPO_WPML()->get_lang() === $lang ) {

																if ( 'product' === $basetype ) {

																	if ( isset( $translations[ $lang ] ) ) {

																		$this_wpml_is_original_product = THEMECOMPLETE_EPO_WPML()->is_original_product( $translations[ $lang ]->element_id, 'product' );

																		if ( $this_wpml_is_original_product && THEMECOMPLETE_EPO_WPML()->get_lang() === $lang ) {
																			$thisbuilder = $builder;
																		} else {
																			$thisbuilder = themecomplete_get_post_meta( $translations[ $lang ]->element_id, ( $this_wpml_is_original_product ? 'tm_meta' : 'tm_meta_wpml' ), true );
																			if ( isset( $thisbuilder['tmfbuilder'] ) ) {
																				$thisbuilder = $thisbuilder['tmfbuilder'];
																			} else {
																				$thisbuilder = themecomplete_get_post_meta( $original_product_id, 'tm_meta', true );
																				if ( isset( $thisbuilder['tmfbuilder'] ) ) {
																					$thisbuilder = $thisbuilder['tmfbuilder'];
																				} else {
																					$thisbuilder = [];
																				}
																			}
																		}
																	}
																} elseif ( $wpml_is_original_product && THEMECOMPLETE_EPO_WPML()->get_lang() === $lang ) {
																	$thisbuilder = $builder;
																} else {
																	$args                 = [
																		'post_type'   => $basetype,
																		'post_status' => [ 'publish', 'draft' ], // get only enabled global extra options.
																		'numberposts' => -1,
																		'orderby'     => 'date',
																		'order'       => 'asc',
																	];
																	$args['meta_query']   = THEMECOMPLETE_EPO_HELPER()->build_meta_query( 'AND', THEMECOMPLETE_EPO_WPML_LANG_META, $lang, '=', 'EXISTS' ); // phpcs:ignore WordPress.DB.SlowDBQuery
																	$args['meta_query'][] = [
																		'key'     => THEMECOMPLETE_EPO_WPML_PARENT_POSTID,
																		'value'   => $original_product_id,
																		'compare' => '=',
																	];
																	$other_translations   = THEMECOMPLETE_EPO_HELPER()->get_cached_posts( $args );

																	if ( ! empty( $other_translations ) && isset( $other_translations[0] ) && is_object( $other_translations[0] ) && property_exists( $other_translations[0], 'ID' ) ) {
																		$this_wpml_is_original_product = THEMECOMPLETE_EPO_WPML()->is_original_product( $other_translations[0]->ID, $basetype );
																		$thisbuilder                   = themecomplete_get_post_meta( $other_translations[0]->ID, ( $this_wpml_is_original_product ? 'tm_meta' : 'tm_meta_wpml' ), true );
																		if ( isset( $thisbuilder['tmfbuilder'] ) ) {
																			$thisbuilder = $thisbuilder['tmfbuilder'];
																		} else {
																			$thisbuilder = themecomplete_get_post_meta( $original_product_id, 'tm_meta', true );
																			if ( isset( $thisbuilder['tmfbuilder'] ) ) {
																				$thisbuilder = $thisbuilder['tmfbuilder'];
																			} else {
																				$thisbuilder = [];
																			}
																		}
																	}
																}

																break;
															}
														}

														if ( empty( $thisbuilder ) ) {
															$thisbuilder = $builder;
														}

														if ( $currency !== $woocommerce_wpml->multi_currency->get_default_currency() ) {
															$_current_currency_price = ( isset( $thisbuilder[ 'multiple_' . $current_element . '_options_price_' . $currency ] ) && isset( $thisbuilder[ 'multiple_' . $current_element . '_options_price_' . $currency ][ $current_counter ] ) && '' !== $thisbuilder[ 'multiple_' . $current_element . '_options_price_' . $currency ][ $current_counter ] ) ? $thisbuilder[ 'multiple_' . $current_element . '_options_price_' . $currency ][ $current_counter ] : '';
															if ( '' === $_current_currency_price ) {
																if ( isset( $builder[ 'multiple_' . $current_element . '_options_price_' . $currency ] ) && isset( $builder[ 'multiple_' . $current_element . '_options_price_' . $currency ][ $current_counter ] ) && '' !== $builder[ 'multiple_' . $current_element . '_options_price_' . $currency ][ $current_counter ] ) {
																	$_current_currency_price = $builder[ 'multiple_' . $current_element . '_options_price_' . $currency ][ $current_counter ];
																}
															}
															$_current_currency_sale_price = ( isset( $thisbuilder[ 'multiple_' . $current_element . '_options_sale_price_' . $currency ] ) && isset( $thisbuilder[ 'multiple_' . $current_element . '_options_sale_price_' . $currency ][ $current_counter ] ) && '' !== $thisbuilder[ 'multiple_' . $current_element . '_options_sale_price_' . $currency ][ $current_counter ] ) ? $thisbuilder[ 'multiple_' . $current_element . '_options_sale_price_' . $currency ][ $current_counter ] : '';
															if ( '' === $_current_currency_price ) {
																if ( isset( $builder[ 'multiple_' . $current_element . '_options_sale_price_' . $currency ] ) && isset( $builder[ 'multiple_' . $current_element . '_options_sale_price_' . $currency ][ $current_counter ] ) && '' !== $builder[ 'multiple_' . $current_element . '_options_sale_price_' . $currency ][ $current_counter ] ) {
																	$_current_currency_sale_price = $builder[ 'multiple_' . $current_element . '_options_sale_price_' . $currency ][ $current_counter ];
																}
															}
														} else {
															$_current_currency_price = isset( $thisbuilder[ 'multiple_' . $current_element . '_options_price' ][ $current_counter ] ) ? $thisbuilder[ 'multiple_' . $current_element . '_options_price' ][ $current_counter ] : '';
															if ( '' === $_current_currency_price ) {
																if ( isset( $builder[ 'multiple_' . $current_element . '_options_price' ][ $current_counter ] ) && '' !== $builder[ 'multiple_' . $current_element . '_options_price' ][ $current_counter ] ) {
																	$_current_currency_price = $builder[ 'multiple_' . $current_element . '_options_price' ][ $current_counter ];
																}
															}
															$_current_currency_sale_price = isset( $thisbuilder[ 'multiple_' . $current_element . '_options_sale_price' ][ $current_counter ] ) ? $thisbuilder[ 'multiple_' . $current_element . '_options_sale_price' ][ $current_counter ] : '';
															if ( '' === $_current_currency_price ) {
																if ( isset( $builder[ 'multiple_' . $current_element . '_options_sale_price' ][ $current_counter ] ) && '' !== $builder[ 'multiple_' . $current_element . '_options_sale_price' ][ $current_counter ] ) {
																	$_current_currency_sale_price = $builder[ 'multiple_' . $current_element . '_options_sale_price' ][ $current_counter ];
																}
															}
														}

														if ( ! ( isset( $thisbuilder[ 'multiple_' . $current_element . '_options_price_' . $currency ] ) && isset( $thisbuilder[ 'multiple_' . $current_element . '_options_price_' . $currency ][ $current_counter ] ) ) && ! ( isset( $builder[ 'multiple_' . $current_element . '_options_price_' . $currency ] ) && isset( $builder[ 'multiple_' . $current_element . '_options_price_' . $currency ][ $current_counter ] ) ) ) {
															if ( is_array( $_current_currency_price ) ) {
																foreach ( $_current_currency_price as $_k => $_v ) {
																	if ( isset( $_prices_type[ $_k ] ) && 'math' === $_prices_type[ $_k ] ) {
																		$_current_currency_price[ $_k ] = $_v;
																	} elseif ( '' !== $_v ) {
																		$_current_currency_price[ $_k ] = THEMECOMPLETE_EPO_WPML()->get_price_in_currency( $_v, $currency );
																	}
																}
															}
														}
														if ( ! ( isset( $thisbuilder[ 'multiple_' . $current_element . '_options_sale_price_' . $currency ] ) && isset( $thisbuilder[ 'multiple_' . $current_element . '_options_sale_price_' . $currency ][ $current_counter ] ) ) && ! ( isset( $builder[ 'multiple_' . $current_element . '_options_sale_price_' . $currency ] ) && isset( $builder[ 'multiple_' . $current_element . '_options_sale_price_' . $currency ][ $current_counter ] ) ) ) {
															if ( is_array( $_current_currency_sale_price ) ) {
																foreach ( $_current_currency_sale_price as $_k => $_v ) {
																	if ( isset( $_prices_type[ $_k ] ) && 'math' === $_prices_type[ $_k ] ) {
																		$_current_currency_sale_price[ $_k ] = $_v;
																	} elseif ( '' !== $_v ) {
																		$_current_currency_sale_price[ $_k ] = THEMECOMPLETE_EPO_WPML()->get_price_in_currency( $_v, $currency );
																	}
																}
															}
														}

														$_current_currency_price          = apply_filters( 'wc_epo_multiple_prices' . $currency, $_current_currency_price, 'multiple_' . $current_element . '_options_price' . $currency, $element_uniqueid );
														$_current_currency_sale_price     = apply_filters( 'wc_epo_multiple_sale_prices' . $currency, $_current_currency_sale_price, 'multiple_' . $current_element . '_options_sale_price' . $currency, $element_uniqueid );
														$_original_current_currency_price = $_current_currency_price;

														if ( $enable_sales ) {
															$_current_currency_price = THEMECOMPLETE_EPO_HELPER()->merge_price_array( $_current_currency_price, $_current_currency_sale_price );
														}
														$_current_currency_price = apply_filters( 'wc_epo_apply_discount', $_current_currency_price, $_original_current_currency_price, $post_id );

														$price_per_currencies_original[ $currency ] = $_original_current_currency_price;
														if ( ! is_array( $price_per_currencies_original[ $currency ] ) ) {
															$price_per_currencies_original[ $currency ] = [];
														}

														$price_per_currencies[ $currency ] = $_current_currency_price;
														if ( ! is_array( $price_per_currencies[ $currency ] ) ) {
															$price_per_currencies[ $currency ] = [];
														}

														foreach ( $_prices as $_n => $_price ) {
															if ( ! isset( $_prices_type[ $_n ] ) ) {
																continue;
															}

															$to_price = '';
															if ( is_array( $_original_current_currency_price ) && isset( $_original_current_currency_price[ $_n ] ) ) {
																$to_price = $_original_current_currency_price[ $_n ];
															}
															if ( 'math' === $_prices_type[ $_n ] ) {
																$price_per_currencies_original[ $currency ][ esc_attr( ( $_values[ $_n ] ) ) . '_' . $_n ] = [ $to_price ];
															} else {
																$price_per_currencies_original[ $currency ][ esc_attr( ( $_values[ $_n ] ) ) . '_' . $_n ] = [ wc_format_decimal( $to_price, false, true ) ];
															}

															$to_price = '';
															if ( is_array( $_current_currency_price ) && isset( $_current_currency_price[ $_n ] ) ) {
																$to_price = $_current_currency_price[ $_n ];
															}
															if ( 'math' === $_prices_type[ $_n ] ) {
																$price_per_currencies[ $currency ][ esc_attr( ( $_values[ $_n ] ) ) . '_' . $_n ] = [ $to_price ];
															} else {
																$price_per_currencies[ $currency ][ esc_attr( ( $_values[ $_n ] ) ) . '_' . $_n ] = [ wc_format_decimal( $to_price, false, true ) ];
															}
														}
													}
												}
											} else {
												foreach ( THEMECOMPLETE_EPO_HELPER()->get_currencies() as $currency ) {
													$mt_prefix = THEMECOMPLETE_EPO_HELPER()->get_currency_price_prefix( $currency );

													if ( '' === $mt_prefix ) {
														$_current_currency_price          = $_original_prices;
														$_original_current_currency_price = $_original_prices;
														if ( $enable_sales ) {
															$_current_currency_price = THEMECOMPLETE_EPO_HELPER()->merge_price_array( $_current_currency_price, $_prices );
														}
													} else {
														$_current_currency_price          = $this->get_builder_element( 'multiple_' . $current_element . '_options_price' . $mt_prefix, $builder, $current_builder, $current_counter, [], $current_element, 'wc_epo_multiple_prices' . $mt_prefix, $element_uniqueid );
														$_current_currency_sale_price     = $this->get_builder_element( 'multiple_' . $current_element . '_options_sale_price' . $mt_prefix, $builder, $current_builder, $current_counter, [], $current_element, 'wc_epo_multiple_sale_prices' . $mt_prefix, $element_uniqueid );
														$_original_current_currency_price = $_current_currency_price;
														if ( $enable_sales ) {
															$_current_currency_price = THEMECOMPLETE_EPO_HELPER()->merge_price_array( $_current_currency_price, $_current_currency_sale_price );
														}
														$_current_currency_price = apply_filters( 'wc_epo_apply_discount', $_current_currency_price, $_original_current_currency_price, $post_id );
													}

													$price_per_currencies_original[ $currency ] = $_original_current_currency_price;
													if ( ! is_array( $price_per_currencies_original[ $currency ] ) ) {
														$price_per_currencies_original[ $currency ] = [];
													}
													$price_per_currencies[ $currency ] = $_current_currency_price;
													if ( ! is_array( $price_per_currencies[ $currency ] ) ) {
														$price_per_currencies[ $currency ] = [];
													}
													foreach ( $_prices as $_n => $_price ) {
														if ( ! isset( $_prices_type[ $_n ] ) ) {
															continue;
														}
														$to_price = '';
														if ( is_array( $_original_current_currency_price ) && isset( $_original_current_currency_price[ $_n ] ) ) {
															$to_price = $_original_current_currency_price[ $_n ];
														}
														if ( 'math' === $_prices_type[ $_n ] ) {
															$price_per_currencies_original[ $currency ][ esc_attr( ( $_values[ $_n ] ) ) . '_' . $_n ] = [ $to_price ];
														} else {
															$price_per_currencies_original[ $currency ][ esc_attr( ( $_values[ $_n ] ) ) . '_' . $_n ] = [ wc_format_decimal( $to_price, false, true ) ];
														}
														$to_price = '';
														if ( is_array( $_current_currency_price ) && isset( $_current_currency_price[ $_n ] ) ) {
															$to_price = $_current_currency_price[ $_n ];
														}
														if ( 'math' === $_prices_type[ $_n ] ) {
															$price_per_currencies[ $currency ][ esc_attr( ( $_values[ $_n ] ) ) . '_' . $_n ] = [ $to_price ];
														} else {
															$price_per_currencies[ $currency ][ esc_attr( ( $_values[ $_n ] ) ) . '_' . $_n ] = [ wc_format_decimal( $to_price, false, true ) ];
														}
													}
												}
											}

											$mt_prefix                         = THEMECOMPLETE_EPO_HELPER()->get_currency_price_prefix( true, '' );
											$_original_current_currency_prices = isset( $price_per_currencies_original[ $mt_prefix ] ) ? $price_per_currencies_original[ $mt_prefix ] : '';
											$_current_currency_prices          = isset( $price_per_currencies[ $mt_prefix ] ) ? $price_per_currencies[ $mt_prefix ] : '';

											// Backwards compatibility.
											if ( '' === $_replacement_mode ) {
												if ( '' !== $_use_images ) {
													$_replacement_mode = 'image';
												} elseif ( '' !== $_use_colors ) {
													$_replacement_mode = 'color';
												} else {
													$_replacement_mode = 'none';
												}
											}
											if ( '' === $_swatch_position ) {
												switch ( $_replacement_mode ) {
													case 'none':
														$_swatch_position = 'center';
														break;
													case 'image':
														if ( ! empty( $_use_images ) ) {
															switch ( $_use_images ) {
																case 'images':
																	$_swatch_position = 'center';
																	break;
																default:
																	$_swatch_position = $_use_images;
																	break;
															}
														}
														break;
													case 'color':
														if ( ! empty( $_use_colors ) ) {
															switch ( $_use_colors ) {
																case 'color':
																	$_swatch_position = 'center';
																	break;
																default:
																	$_swatch_position = $_use_colors;
																	break;
															}
														}
														break;
												}
												if ( '' === $_swatch_position ) {
													$_swatch_position = 'center';
												}
											}

											if ( 'images' === $_changes_product_image && 'image' !== $_replacement_mode ) {
												$_imagesp               = $_images;
												$_images                = [];
												$_imagesc               = [];
												$_changes_product_image = 'custom';
											}
											if ( 'image' !== $_replacement_mode ) {
												$_use_lightbox = '';
											}

											$_url         = $this->get_builder_element( 'multiple_' . $current_element . '_options_url', $builder, $current_builder, $current_counter, [], $current_element, '', $element_uniqueid );
											$_description = $this->get_builder_element( 'multiple_' . $current_element . '_options_description', $builder, $current_builder, $current_counter, [], $current_element, '', $element_uniqueid );
											$_enabled     = $this->get_builder_element( 'multiple_' . $current_element . '_options_enabled', $builder, $current_builder, $current_counter, [], $current_element, '1', $element_uniqueid );
											$_fee         = $this->get_builder_element( 'multiple_' . $current_element . '_options_fee', $builder, $current_builder, $current_counter, [], $current_element, '', $element_uniqueid );

											foreach ( THEMECOMPLETE_EPO_BUILDER()->extra_multiple_options as $__key => $__name ) {
												$_extra_name                             = $__name['name'];
												$_extra_multiple_choices[ $_extra_name ] = $this->get_builder_element( 'multiple_' . $current_element . '_options_' . $_extra_name, $builder, $current_builder, $current_counter, [], $current_element, '', $element_uniqueid );
											}

											$_values_c  = $_values;
											$_values_ce = $_values;
											$mt_prefix  = THEMECOMPLETE_EPO_HELPER()->get_currency_price_prefix( null, '' );
											$_nn        = 0;
											foreach ( $_prices as $_n => $_price ) {

												if ( isset( $_enabled[ $_n ] ) && ( '0' === $_enabled[ $_n ] || '' === $_enabled[ $_n ] ) ) {
													$_options_all[ esc_attr( ( $_values[ $_n ] ) ) . '_' . $_n ] = $_titles[ $_n ];
													unset( $_images[ $_n ] );
													unset( $_imagesc[ $_n ] );
													unset( $_imagesp[ $_n ] );
													unset( $_imagesl[ $_n ] );
													unset( $_color[ $_n ] );
													unset( $_url[ $_n ] );
													unset( $_description[ $_n ] );
													unset( $_titles[ $_n ] );
													unset( $_values[ $_n ] );
													unset( $_original_prices[ $_n ] );
													unset( $_prices_type[ $_n ] );
													unset( $_values_ce[ $_n ] );
													if ( is_array( $_current_currency_prices ) ) {
														unset( $_current_currency_prices[ $_n ] );
													}
													if ( is_array( $_original_current_currency_prices ) ) {
														unset( $_original_current_currency_prices[ $_n ] );
													}
													if ( isset( $_fee ) && is_array( $_fee ) ) {
														unset( $_fee[ $_n ] );
													}
													unset( $_sale_prices[ $_n ] );
													foreach ( THEMECOMPLETE_EPO_BUILDER()->extra_multiple_options as $__key => $__name ) {
														$_extra_name = $__name['name'];
														if ( is_array( $_extra_multiple_choices[ $_extra_name ] ) ) {
															unset( $_extra_multiple_choices[ $_extra_name ][ $_n ] );
														}
													}

													do_action( 'wc_epo_admin_option_is_disable', $_n );
													$_current_deleted_choices[] = $_n;
													continue;
												}

												if ( ! isset( $_prices_type[ $_n ] ) ) {
													continue;
												}

												// backwards compatibility.
												if ( isset( $_prices_type[ $_n ] ) ) {
													if ( 'fee' === $_prices_type[ $_n ] ) {
														if ( 'checkboxes' === $current_element ) {
															$_fee[ $_n ] = '1';
														} else {
															$_is_price_fee = '1';
														}
														$_prices_type[ $_n ] = '';
													}
												}

												$new_currency = false;
												if ( '' !== $mt_prefix
													&& '' !== $_current_currency_prices
													&& is_array( $_current_currency_prices )
													&& isset( $_current_currency_prices[ $_n ] )
													&& '' !== $_current_currency_prices[ $_n ]
												) {
													$new_currency            = true;
													$_price                  = $_current_currency_prices[ $_n ];
													$_original_prices[ $_n ] = $_original_current_currency_prices[ $_n ];
													$_regular_currencies[ esc_attr( ( $_values[ $_n ] ) ) . '_' . $_n ] = [ themecomplete_get_woocommerce_currency() ];
												}

												if ( 'math' === $_prices_type[ $_n ] ) {
													$_f_price = $_price;
												} else {
													$_f_price = wc_format_decimal( $_price, false, true );
												}

												$_regular_price[ esc_attr( ( $_values[ $_n ] ) ) . '_' . $_n ] = [ $_f_price ];
												$_for_filter_price_type                                        = isset( $_prices_type[ $_n ] ) ? $_prices_type[ $_n ] : '';

												if ( ! $new_currency ) {
													$_price                  = apply_filters( 'wc_epo_get_current_currency_price', $_price, $_for_filter_price_type, null, $mt_prefix );
													$_original_prices[ $_n ] = apply_filters( 'wc_epo_get_current_currency_price', $_original_prices[ $_n ], $_for_filter_price_type, null, $mt_prefix );
												}
												$_price                  = apply_filters( 'wc_epo_price', $_price, $_for_filter_price_type, $post_id );
												$_original_prices[ $_n ] = apply_filters( 'wc_epo_price', $_original_prices[ $_n ], $_for_filter_price_type, $post_id );

												if ( 'math' === $_prices_type[ $_n ] ) {
													$_f_price = $_price;
													$_regular_price_filtered[ esc_attr( ( $_values[ $_n ] ) ) . '_' . $_n ]           = [ $_price ];
													$_original_regular_price_filtered [ esc_attr( ( $_values[ $_n ] ) ) . '_' . $_n ] = [ $_original_prices[ $_n ] ];
												} else {
													$_f_price = wc_format_decimal( $_price, false, true );
													$_regular_price_filtered[ esc_attr( ( $_values[ $_n ] ) ) . '_' . $_n ]           = [ $_f_price ];
													$_original_regular_price_filtered [ esc_attr( ( $_values[ $_n ] ) ) . '_' . $_n ] = [ wc_format_decimal( $_original_prices[ $_n ], false, true ) ];
												}

												$_regular_price_type[ esc_attr( ( $_values[ $_n ] ) ) . '_' . $_n ] = isset( $_prices_type[ $_n ] ) ? [ ( $_prices_type[ $_n ] ) ] : [ '' ];
												$_options_all[ esc_attr( ( $_values[ $_n ] ) ) . '_' . $_n ]        = $_titles[ $_n ];
												$_options[ esc_attr( ( $_values[ $_n ] ) ) . '_' . $_n ]            = $_titles[ $_n ];
												$_values_c[ $_n ]  = $_values[ $_n ] . '_' . $_n;
												$_values_ce[ $_n ] = $_values_c[ $_n ];
												if ( ( ( isset( $_fee[ $_n ] ) && '1' !== $_fee[ $_n ] ) || ! isset( $_fee[ $_n ] ) ) && isset( $_prices_type[ $_n ] ) && '' === $_prices_type[ $_n ] && ( ( isset( $builder[ $current_element . '_price_type' ][ $current_counter ] ) && '' === $builder[ $current_element . '_price_type' ][ $current_counter ] ) || ! isset( $builder[ $current_element . '_price_type' ][ $current_counter ] ) ) ) {
													if ( false !== $_min_price && '' !== $_price ) {
														if ( '' === $_min_price ) {
															$_min_price = $_f_price;
														} elseif ( $_min_price > $_f_price ) {
															$_min_price = $_f_price;
														}
														if ( '' === $_min_price0 ) {
															if ( $_is_field_required ) {
																$_min_price0 = floatval( $_min_price );
															} else {
																$_min_price0 = 0;
															}
														} elseif ( $_is_field_required && $_min_price0 > floatval( $_min_price ) ) {
															$_min_price0 = floatval( $_min_price );
														}
														if ( '' === $_min_price10 ) {
															$_min_price10 = floatval( $_min_price );
														} elseif ( $_min_price10 > floatval( $_min_price ) ) {
															$_min_price10 = floatval( $_min_price );
														}
														if ( '' === $_max_price ) {
															$_max_price = $_f_price;
														} elseif ( 'checkboxes' === $current_element ) {
															// needs work for Limit selection/Exact selection/Minimum selection.
															$_max_price = floatval( $_max_price ) + floatval( $_f_price );
														} elseif ( $_max_price < $_f_price ) {
															$_max_price = $_f_price;
														}
													} elseif ( '' === $_price ) {
														$_min_price0  = 0;
														$_min_price10 = 0;
													}
												} else {
													$_min_price = false;
													$_max_price = $_min_price;
													if ( '' === $_min_price0 ) {
														$_min_price0 = 0;
													} elseif ( $_min_price0 > floatval( $_min_price ) ) {
														$_min_price0 = floatval( $_min_price );
													}
													if ( '' === $_min_price10 ) {
														$_min_price10 = 0;
													} elseif ( $_min_price10 > floatval( $_min_price ) ) {
														$_min_price10 = floatval( $_min_price );
													}
												}
												++$_nn;
											}

											$_images          = array_values( $_images );
											$_imagesc         = array_values( $_imagesc );
											$_imagesp         = array_values( $_imagesp );
											$_imagesl         = array_values( $_imagesl );
											$_color           = array_values( $_color );
											$_url             = array_values( $_url );
											$_description     = array_values( $_description );
											$_titles          = array_values( $_titles );
											$_values          = array_values( $_values );
											$_original_prices = array_values( $_original_prices );
											$_prices_type     = array_values( $_prices_type );
											if ( is_array( $_current_currency_prices ) ) {
												$_current_currency_prices = array_values( $_current_currency_prices );
											}
											if ( is_array( $_original_current_currency_prices ) ) {
												$_original_current_currency_prices = array_values( $_original_current_currency_prices );
											}
											if ( isset( $_fee ) && is_array( $_fee ) ) {
												$_fee = array_values( $_fee );
											}
											$_sale_prices = array_values( $_sale_prices );
											$_values_c    = array_values( $_values_c );
											$_values_ce   = array_values( $_values_ce );
											$_prices      = array_values( $_prices );

											foreach ( THEMECOMPLETE_EPO_BUILDER()->extra_multiple_options as $__key => $__name ) {
												$_extra_name                             = $__name['name'];
												$_extra_multiple_choices[ $_extra_name ] = array_values( $_extra_multiple_choices[ $_extra_name ] );
											}

											do_action( 'wc_epo_admin_option_reindex' );
										}
									}

									$default_value = '';
									if ( isset( $builder[ 'multiple_' . $current_element . '_options_default_value' ][ $current_counter ] ) ) {
										$default_value = $builder[ 'multiple_' . $current_element . '_options_default_value' ][ $current_counter ];

										$disabled_count = count(
											array_filter(
												$_current_deleted_choices,
												function ( $n ) use ( $default_value ) {
													return $n <= $default_value;
												}
											)
										);

										if ( is_array( $default_value ) ) {
											foreach ( $default_value as $key => $value ) {
												if ( '' !== $value ) {
													$this_disabled_count   = count(
														array_filter(
															$_current_deleted_choices,
															function ( $n ) use ( $default_value, $value ) {
																return (int) $n < (int) $value && $n <= $default_value;
															}
														)
													);
													$default_value[ $key ] = (string) ( (int) $value - (int) $this_disabled_count );
												}
											}
											if ( 'selectbox' === $current_element && isset( $default_value[ $current_counter ] ) ) {
												$default_value = (string) $default_value[ $current_counter ];
											}
										} elseif ( '' !== $default_value ) {
											$default_value = (string) ( (int) $default_value - (int) $disabled_count );
										}
									} elseif ( isset( $builder[ $_prefix . 'default_value' ] ) && isset( $builder[ $_prefix . 'default_value' ][ $current_counter ] ) ) {
										$default_value = (string) $builder[ $_prefix . 'default_value' ][ $current_counter ];
									}
									$default_value = apply_filters( 'wc_epo_enable_shortocde', $default_value, $default_value, $post_id );

									switch ( $current_element ) {
										case 'selectbox':
											$_new_type = 'select';
											if ( isset( $builder[ $current_element . '_price_type' ][ $current_counter ] ) ) {
												// backwards compatibility.
												$selectbox_fee = $builder[ $current_element . '_price_type' ][ $current_counter ];
												$_is_price_fee = ( 'fee' === $selectbox_fee ) ? '1' : '';

											}
											break;

										case 'selectboxmultiple':
											$_new_type = 'selectmultiple';
											break;

										case 'radiobuttons':
											$_new_type = 'radio';
											break;

										case 'checkboxes':
											$_new_type = 'checkbox';
											break;

									}

									$_rules_type = $_regular_price_type;
									foreach ( $_regular_price_type as $key => $value ) {
										foreach ( $value as $k => $v ) {
											$_regular_price_type[ $key ][ $k ] = $v;
										}
									}

									$has_feature_lookuptable = false;

									$_rules          = $_regular_price;
									$_rules_filtered = $_regular_price_filtered;
									foreach ( $_regular_price as $key => $value ) {
										foreach ( $value as $k => $v ) {
											if ( 'math' !== $_regular_price_type[ $key ][ $k ] ) {
												$_regular_price[ $key ][ $k ]          = wc_format_localized_price( $v );
												$_regular_price_filtered[ $key ][ $k ] = wc_format_localized_price( $v );
											} elseif ( ! $has_feature_lookuptable ) {
												if ( str_contains( $v, 'lookuptable(' ) ) {
													$this->current_option_features[] = 'lookuptable';
													$has_feature_lookuptable         = true;
												}
											}
										}
									}

									if ( 'variations' !== $current_element ) {
										$this->cpf_single_epos_prices[ $element_uniqueid ] = [
											'type'       => $_new_type,
											'options'    => $_options,
											'prices'     => $_rules_filtered,
											'price_type' => $_rules_type,
											'uniqueid'   => $element_uniqueid,
											'required'   => $is_required,
											'element'    => $element_real_no_in_section,
											'section_uniqueid' => $_sections_uniqid,
											'minall'     => floatval( $_min_price10 ),
											'min'        => floatval( $_min_price0 ),
											'max'        => floatval( $_max_price ),
											'logicrules' => $logicrules,
											'section_logicrules' => $global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_logicrules'],
											'logic'      => $this->get_builder_element( $_prefix . 'logic', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
											'section_logic' => $global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_logic'],
										];

										$epos_prices[] = $this->cpf_single_epos_prices[ $element_uniqueid ];
									}
									if ( false !== $_min_price ) {
										$_min_price = wc_format_localized_price( $_min_price );
									}
									if ( false !== $_max_price ) {
										$_max_price = wc_format_localized_price( $_max_price );
									}

									// Fix for getting right results for dates even if the user enters wrong format.
									$format         = $this->get_builder_element( $_prefix . 'format', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
									$data           = $this->get_date_format( $format );
									$date_format    = $data['date_format'];
									$sep            = $data['sep'];
									$disabled_dates = $this->get_builder_element( $_prefix . 'disabled_dates', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
									if ( $disabled_dates ) {
										$disabled_dates = explode( ',', $disabled_dates );
										foreach ( $disabled_dates as $key => $value ) {
											if ( ! $value ) {
												continue;
											}
											$value = str_replace( '.', '-', $value );
											$value = str_replace( '/', '-', $value );
											$value = explode( '-', $value );
											if ( count( $value ) !== 3 ) {
												continue;
											}
											switch ( $format ) {
												case '0':
												case '2':
												case '4':
													$value = $value[2] . '-' . $value[1] . '-' . $value[0];
													break;
												case '1':
												case '3':
												case '5':
													$value = $value[2] . '-' . $value[0] . '-' . $value[1];
													break;
											}
											if ( is_array( $value ) ) {
												continue;
											}
											$value_to_date = date_create( $value );
											if ( ! $value_to_date ) {
												continue;
											}
											$value                  = date_format( $value_to_date, $date_format );
											$disabled_dates[ $key ] = $value;
										}
										$disabled_dates = implode( ',', $disabled_dates );

									}
									$enabled_only_dates = $this->get_builder_element( $_prefix . 'enabled_only_dates', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
									if ( $enabled_only_dates ) {
										$enabled_only_dates = explode( ',', $enabled_only_dates );
										foreach ( $enabled_only_dates as $key => $value ) {
											if ( ! $value ) {
												continue;
											}
											$value = str_replace( '.', '-', $value );
											$value = str_replace( '/', '-', $value );
											$value = explode( '-', $value );
											if ( count( $value ) !== 3 ) {
												continue;
											}
											switch ( $format ) {
												case '0':
												case '2':
												case '4':
													$value = $value[2] . '-' . $value[1] . '-' . $value[0];
													break;
												case '1':
												case '3':
												case '5':
													$value = $value[2] . '-' . $value[0] . '-' . $value[1];
													break;
											}
											if ( is_array( $value ) ) {
												continue;
											}
											$value_to_date = date_create( $value );
											if ( ! $value_to_date ) {
												continue;
											}
											$value                      = date_format( $value_to_date, $date_format );
											$enabled_only_dates[ $key ] = $value;
										}
										$enabled_only_dates = implode( ',', $enabled_only_dates );
									}

									if ( $is_enabled ) {
										$this->current_option_features[] = $current_element;
									}

									$repeater  = $this->get_builder_element( $_prefix . 'repeater', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
									$connector = $this->get_builder_element( $_prefix . 'connector', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
									if ( '' !== $connector ) {
										$repeater = '';
									}

									if ( ! isset( $builder_element_order[ $_new_type ] ) ) {
										$builder_element_order[ $_new_type ] = 0;
									} else {
										$builder_element_order[ $_new_type ] = $builder_element_order[ $_new_type ] + 1;
									}

									if ( 'header' !== $current_element && 'divider' !== $current_element ) {
										if ( 'variations' === $current_element ) {
											$this->cpf_single_variation_element_id[ $element_uniqueid ] = $this->get_builder_element( $_prefix . 'uniqid', $builder, $current_builder, $current_counter, THEMECOMPLETE_EPO_HELPER()->tm_uniqid(), $current_element, '', $element_uniqueid );
											$variation_element_id                                       = $this->cpf_single_variation_element_id[ $element_uniqueid ];
											$this->cpf_single_variation_section_id[ $element_uniqueid ] = $global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_uniqid'];
											$variation_section_id                                       = $this->cpf_single_variation_section_id[ $element_uniqueid ];
										}
										$product_epos_uniqids[] = $element_uniqueid;
										if ( in_array( $_new_type, [ 'select', 'radio', 'checkbox' ], true ) ) {
											$product_epos_choices[ $element_uniqueid ] = array_keys( $_rules_type );
										}

										$shipping_methods_enable             = $this->get_builder_element( $_prefix . 'shipping_methods_enable', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
										$shipping_methods_enable_logicrules  = $this->get_builder_element( $_prefix . 'shipping_methods_enable_logicrules', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
										$shipping_methods_disable            = $this->get_builder_element( $_prefix . 'shipping_methods_disable', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
										$shipping_methods_disable_logicrules = $this->get_builder_element( $_prefix . 'shipping_methods_disable_logicrules', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid );
										if ( empty( $shipping_methods_enable ) ) {
											$shipping_methods_enable = '';
										}
										if ( empty( $shipping_methods_disable ) ) {
											$shipping_methods_disable = '';
										}
										if ( empty( $shipping_methods_enable_logicrules ) || is_array( $shipping_methods_enable_logicrules ) ) {
											$shipping_methods_enable_logicrules = '';
										} else {
											$shipping_methods_enable_logicrules = THEMECOMPLETE_EPO_CONDITIONAL_LOGIC()->convert_rules( json_decode( wp_unslash( $shipping_methods_enable_logicrules ) ) );
										}
										if ( empty( $shipping_methods_disable_logicrules ) || is_array( $shipping_methods_disable_logicrules ) ) {
											$shipping_methods_disable_logicrules = '';
										} else {
											$shipping_methods_disable_logicrules = THEMECOMPLETE_EPO_CONDITIONAL_LOGIC()->convert_rules( json_decode( wp_unslash( $shipping_methods_disable_logicrules ) ) );
										}

										$this->cpf_single[ $element_uniqueid ] =
											array_merge(
												THEMECOMPLETE_EPO_BUILDER()->get_custom_properties( $builder, $_prefix, $_counter, $_elements, $k0, $current_builder, $current_counter, $current_element ),
												$_extra_multiple_choices,
												$_extra_addon_properties,
												[
													'internal_name' => $this->get_builder_element( $_prefix . 'internal_name', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'builder' => ( isset( $wpml_is_original_product ) && empty( $wpml_is_original_product ) ) ? $current_builder : $builder,
													'original_builder' => $builder,
													'section' => $_sections_uniqid,
													'type' => $_new_type,
													'size' => $_div_size[ $k0 ],

													'include_tax_for_fee_price_type' => $this->get_builder_element( $_prefix . 'include_tax_for_fee_price_type', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'tax_class_for_fee_price_type' => $this->get_builder_element( $_prefix . 'tax_class_for_fee_price_type', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),

													'hide_element_label_in_cart' => $this->get_builder_element( $_prefix . 'hide_element_label_in_cart', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'hide_element_value_in_cart' => $this->get_builder_element( $_prefix . 'hide_element_value_in_cart', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'hide_element_label_in_order' => $this->get_builder_element( $_prefix . 'hide_element_label_in_order', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'hide_element_value_in_order' => $this->get_builder_element( $_prefix . 'hide_element_value_in_order', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'hide_element_label_in_floatbox' => $this->get_builder_element( $_prefix . 'hide_element_label_in_floatbox', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'hide_element_value_in_floatbox' => $this->get_builder_element( $_prefix . 'hide_element_value_in_floatbox', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),

													'enabled' => $is_enabled,
													'required' => $is_required,
													'replacement_mode' => isset( $_replacement_mode ) ? $_replacement_mode : 'none',
													'swatch_position' => isset( $_swatch_position ) ? $_swatch_position : 'center',
													'use_images' => isset( $_use_images ) ? $_use_images : '',
													'use_colors' => isset( $_use_colors ) ? $_use_colors : '',
													'use_lightbox' => $_use_lightbox,
													'use_url' => $this->get_builder_element( $_prefix . 'use_url', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'items_per_row' => $this->get_builder_element( $_prefix . 'items_per_row', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),

													'items_per_row_r' => [
														'desktop'        => $this->get_builder_element( $_prefix . 'items_per_row', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
														'tablets_galaxy' => $this->get_builder_element( $_prefix . 'items_per_row_tablets_galaxy', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
														'tablets'        => $this->get_builder_element( $_prefix . 'items_per_row_tablets', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
														'tablets_small'  => $this->get_builder_element( $_prefix . 'items_per_row_tablets_small', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
														'iphone6_plus'   => $this->get_builder_element( $_prefix . 'items_per_row_iphone6_plus', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
														'iphone6'        => $this->get_builder_element( $_prefix . 'items_per_row_iphone6', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
														'galaxy'         => $this->get_builder_element( $_prefix . 'items_per_row_samsung_galaxy', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
														'iphone5'        => $this->get_builder_element( $_prefix . 'items_per_row_iphone5', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
														'smartphones'    => $this->get_builder_element( $_prefix . 'items_per_row_smartphones', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													],

													'label_size' => $this->get_builder_element( $_prefix . 'header_size', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'label' => $this->get_builder_element( $_prefix . 'header_title', $builder, $current_builder, $current_counter, '', $current_element, 'wc_epo_label', $element_uniqueid ),
													'label_position' => $this->get_builder_element( $_prefix . 'header_title_position', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'label_mode' => $this->get_builder_element( $_prefix . 'header_title_mode', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'label_color' => $this->get_builder_element( $_prefix . 'header_title_color', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'description' => $this->get_builder_element( $_prefix . 'header_subtitle', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'description_position' => $this->get_builder_element( $_prefix . 'header_subtitle_position', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'description_color' => $this->get_builder_element( $_prefix . 'header_subtitle_color', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'divider_type' => $this->get_builder_element( $_prefix . 'divider_type', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'placeholder' => $this->get_builder_element( $_prefix . 'placeholder', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'min_chars' => $this->get_builder_element( $_prefix . 'min_chars', $builder, $current_builder, $current_counter, false, $current_element, 'wc_epo_global_min_chars', $element_uniqueid ),
													'max_chars' => $this->get_builder_element( $_prefix . 'max_chars', $builder, $current_builder, $current_counter, false, $current_element, 'wc_epo_global_max_chars', $element_uniqueid ),
													'hide_amount' => $this->get_builder_element( $_prefix . 'hide_amount', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'text_before_price' => $this->get_builder_element( $_prefix . 'text_before_price', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'text_after_price' => $this->get_builder_element( $_prefix . 'text_after_price', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'options' => $_options,
													'options_all' => $_options_all,
													'min_price' => $_min_price,
													'max_price' => $_max_price,
													'rules' => $_rules,
													'price_rules' => $_regular_price,
													'rules_filtered' => $_rules_filtered,
													'price_rules_filtered' => $_regular_price_filtered,
													'original_rules_filtered' => $_original_regular_price_filtered,
													'price_rules_type' => $_regular_price_type,
													'rules_type' => $_rules_type,
													'currencies' => $_regular_currencies,
													'price_per_currencies' => $price_per_currencies,
													'lookuptable' => $this->get_builder_element( $_prefix . 'lookuptable', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'lookuptable_x' => $this->get_builder_element( $_prefix . 'lookuptable_x', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'lookuptable_y' => $this->get_builder_element( $_prefix . 'lookuptable_y', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'images' => isset( $_images ) ? $_images : '',
													'imagesc' => isset( $_imagesc ) ? $_imagesc : '',
													'imagesp' => isset( $_imagesp ) ? $_imagesp : '',
													'imagesl' => isset( $_imagesl ) ? $_imagesl : '',
													'color' => isset( $_color ) ? $_color : '',
													'url'  => isset( $_url ) ? $_url : '',

													'cdescription' => ( false !== $_description ) ? $_description : '',
													'extra_multiple_choices' => $_extra_multiple_choices,
													'extra_addon_properties' => $_extra_addon_properties,
													'weight' => $this->get_builder_element( $_prefix . 'weight', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'limit' => $this->get_builder_element( $_prefix . 'limit_choices', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'exactlimit' => $this->get_builder_element( $_prefix . 'exactlimit_choices', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'minimumlimit' => $this->get_builder_element( $_prefix . 'minimumlimit_choices', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'clear_options' => $this->get_builder_element( $_prefix . 'clear_options', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'option_values_all' => isset( $_values_c ) ? $_values_c : [],
													'option_values' => isset( $_values_ce ) ? $_values_ce : [],
													'button_type' => $this->get_builder_element( $_prefix . 'button_type', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'uniqid' => $element_uniqueid,
													'logicrules' => $logicrules,
													'logic' => $this->get_builder_element( $_prefix . 'logic', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'format' => $format,
													'start_year' => $this->get_builder_element( $_prefix . 'start_year', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'end_year' => $this->get_builder_element( $_prefix . 'end_year', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'min_date' => $this->get_builder_element( $_prefix . 'min_date', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'max_date' => $this->get_builder_element( $_prefix . 'max_date', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'disabled_dates' => $disabled_dates,
													'enabled_only_dates' => $enabled_only_dates,
													'exlude_disabled' => $this->get_builder_element( $_prefix . 'exlude_disabled', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'disabled_weekdays' => $this->get_builder_element( $_prefix . 'disabled_weekdays', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'disabled_months' => $this->get_builder_element( $_prefix . 'disabled_months', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),

													'time_format' => $this->get_builder_element( $_prefix . 'time_format', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'custom_time_format' => $this->get_builder_element( $_prefix . 'custom_time_format', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'min_time' => $this->get_builder_element( $_prefix . 'min_time', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'max_time' => $this->get_builder_element( $_prefix . 'max_time', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'showhour' => $this->get_builder_element( $_prefix . 'showhour', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'showminute' => $this->get_builder_element( $_prefix . 'showminute', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'showsecond' => $this->get_builder_element( $_prefix . 'showsecond', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'translation_hour' => $this->get_builder_element( $_prefix . 'tranlation_hour', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'translation_minute' => $this->get_builder_element( $_prefix . 'tranlation_minute', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'translation_second' => $this->get_builder_element( $_prefix . 'tranlation_second', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),

													'theme' => $this->get_builder_element( $_prefix . 'theme', $builder, $current_builder, $current_counter, 'epo', $current_element, '', $element_uniqueid ),
													'theme_size' => $this->get_builder_element( $_prefix . 'theme_size', $builder, $current_builder, $current_counter, 'medium', $current_element, '', $element_uniqueid ),
													'theme_position' => $this->get_builder_element( $_prefix . 'theme_position', $builder, $current_builder, $current_counter, 'normal', $current_element, '', $element_uniqueid ),

													'translation_day' => $this->get_builder_element( $_prefix . 'tranlation_day', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'translation_month' => $this->get_builder_element( $_prefix . 'tranlation_month', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'translation_year' => $this->get_builder_element( $_prefix . 'tranlation_year', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'default_value' => $default_value,

													'is_cart_fee' => '1' === $_is_price_fee,
													'is_cart_fee_multiple' => isset( $_fee ) ? $_fee : [],
													'class' => $this->get_builder_element( $_prefix . 'class', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'container_id' => $this->get_builder_element( $_prefix . 'container_id', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'show_tooltip' => $show_tooltip,
													'changes_product_image' => isset( $_changes_product_image ) ? $_changes_product_image : '',
													'min'  => $this->get_builder_element( $_prefix . 'min', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'max'  => $this->get_builder_element( $_prefix . 'max', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'freechars' => $this->get_builder_element( $_prefix . 'freechars', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'step' => $this->get_builder_element( $_prefix . 'step', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'pips' => $this->get_builder_element( $_prefix . 'pips', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'noofpips' => $this->get_builder_element( $_prefix . 'noofpips', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'show_picker_value' => $this->get_builder_element( $_prefix . 'show_picker_value', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),

													'quantity' => $this->get_builder_element( $_prefix . 'quantity', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'quantity_min' => $this->get_builder_element( $_prefix . 'quantity_min', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'quantity_max' => $this->get_builder_element( $_prefix . 'quantity_max', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'quantity_step' => $this->get_builder_element( $_prefix . 'quantity_step', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'quantity_default_value' => $this->get_builder_element( $_prefix . 'quantity_default_value', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),

													'mode' => $this->get_builder_element( $_prefix . 'mode', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'layout_mode' => $this->get_builder_element( $_prefix . 'layout_mode', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'categoryids' => $this->get_builder_element( $_prefix . 'categoryids', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'productids' => $this->get_builder_element( $_prefix . 'productids', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'priced_individually' => $this->get_builder_element( $_prefix . 'priced_individually', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'order' => $this->get_builder_element( $_prefix . 'order', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'orderby' => $this->get_builder_element( $_prefix . 'orderby', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'disable_epo' => $this->get_builder_element( $_prefix . 'disable_epo', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'shipped_individually' => $this->get_builder_element( $_prefix . 'shipped_individually', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'maintain_weight' => $this->get_builder_element( $_prefix . 'maintain_weight', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'discount' => $this->get_builder_element( $_prefix . 'discount', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'discount_type' => $this->get_builder_element( $_prefix . 'discount_type', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'discount_exclude_addons' => $this->get_builder_element( $_prefix . 'discount_exclude_addons', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'hiddenin' => $this->get_builder_element( $_prefix . 'hiddenin', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),

													'show_title' => $this->get_builder_element( $_prefix . 'show_title', $builder, $current_builder, $current_counter, '1', $current_element, '', $element_uniqueid ),
													'show_title_link' => $this->get_builder_element( $_prefix . 'show_title_link', $builder, $current_builder, $current_counter, '1', $current_element, '', $element_uniqueid ),
													'show_price' => $this->get_builder_element( $_prefix . 'show_price', $builder, $current_builder, $current_counter, '1', $current_element, '', $element_uniqueid ),
													'show_description' => $this->get_builder_element( $_prefix . 'show_description', $builder, $current_builder, $current_counter, '1', $current_element, '', $element_uniqueid ),
													'show_meta' => $this->get_builder_element( $_prefix . 'show_meta', $builder, $current_builder, $current_counter, '1', $current_element, '', $element_uniqueid ),
													'show_image' => $this->get_builder_element( $_prefix . 'show_image', $builder, $current_builder, $current_counter, '1', $current_element, '', $element_uniqueid ),

													'repeater' => $repeater,
													'repeater_quantity' => $this->get_builder_element( $_prefix . 'repeater_quantity', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'repeater_min_rows' => $this->get_builder_element( $_prefix . 'repeater_min_rows', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'repeater_max_rows' => $this->get_builder_element( $_prefix . 'repeater_max_rows', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'repeater_button_label' => $this->get_builder_element( $_prefix . 'repeater_button_label', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'connector' => $connector,

													'shipping_methods_enable' => $shipping_methods_enable,
													'shipping_methods_enable_logicrules' => $shipping_methods_enable_logicrules,
													'shipping_methods_disable' => $shipping_methods_disable,
													'shipping_methods_disable_logicrules' => $shipping_methods_disable_logicrules,

													'hide' => $this->get_builder_element( $_prefix . 'hide', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'result_as_price' => $this->get_builder_element( $_prefix . 'result_as_price', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
													'result_label' => $this->get_builder_element( $_prefix . 'result_label', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),

													'validation1' => $this->get_builder_element( $_prefix . 'validation1', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
												]
											);
										$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['elements'][] = $this->cpf_single[ $element_uniqueid ];

									} elseif ( 'header' === $current_element ) {
										$product_epos_uniqids[] = $element_uniqueid;

										$this->cpf_single[ $element_uniqueid ] = [
											'internal_name' => $this->get_builder_element( $_prefix . 'internal_name', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
											'section'      => $_sections_uniqid,
											'type'         => $_new_type,
											'size'         => $_div_size[ $k0 ],
											'required'     => '',
											'enabled'      => $is_enabled,
											'replacement_mode' => 'none',
											'swatch_position' => 'center',
											'use_images'   => '',
											'use_colors'   => '',
											'use_url'      => '',
											'items_per_row' => '',
											'label_size'   => $this->get_builder_element( $_prefix . 'header_size', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
											'label'        => $this->get_builder_element( $_prefix . 'header_title', $builder, $current_builder, $current_counter, '', $current_element, 'wc_epo_label', $element_uniqueid ),
											'label_position' => $this->get_builder_element( $_prefix . 'header_title_position', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
											'label_color'  => $this->get_builder_element( $_prefix . 'header_title_color', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
											'description'  => $this->get_builder_element( $_prefix . 'header_subtitle', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
											'description_color' => $this->get_builder_element( $_prefix . 'header_subtitle_color', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
											'description_position' => $this->get_builder_element( $_prefix . 'header_subtitle_position', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
											'divider_type' => '',
											'placeholder'  => '',
											'max_chars'    => '',
											'hide_amount'  => '',
											'options'      => $_options,
											'options_all'  => $_options_all,
											'min_price'    => $_min_price,
											'max_price'    => $_max_price,
											'rules'        => $_rules,
											'price_rules'  => $_regular_price,
											'rules_filtered' => $_rules_filtered,
											'price_rules_filtered' => $_regular_price_filtered,
											'price_rules_type' => $_regular_price_type,
											'rules_type'   => $_rules_type,
											'images'       => '',
											'limit'        => '',
											'exactlimit'   => '',
											'minimumlimit' => '',
											'option_values' => [],
											'button_type'  => '',
											'class'        => $this->get_builder_element( 'header_class', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
											'uniqid'       => $this->get_builder_element( 'header_uniqid', $builder, $current_builder, $current_counter, THEMECOMPLETE_EPO_HELPER()->tm_uniqid(), $current_element, '', $element_uniqueid ),
											'logicrules'   => $logicrules,
											'logic'        => $this->get_builder_element( 'header_logic', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
											'format'       => '',
											'start_year'   => '',
											'end_year'     => '',
											'translation_day' => '',
											'translation_month' => '',
											'translation_year' => '',
											'show_tooltip' => '',
											'changes_product_image' => '',
											'min'          => '',
											'max'          => '',
											'step'         => '',
											'pips'         => '',

										];

										$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['elements'][] = $this->cpf_single[ $element_uniqueid ];

									} elseif ( 'divider' === $current_element ) {
										$product_epos_uniqids[] = $element_uniqueid;

										$this->cpf_single[ $element_uniqueid ] = [
											'internal_name' => $this->get_builder_element( $_prefix . 'internal_name', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
											'section'      => $_sections_uniqid,
											'type'         => $_new_type,
											'size'         => $_div_size[ $k0 ],
											'required'     => '',
											'enabled'      => $is_enabled,
											'replacement_mode' => 'none',
											'swatch_position' => 'center',
											'use_images'   => '',
											'use_colors'   => '',
											'use_url'      => '',
											'items_per_row' => '',
											'label_size'   => '',
											'label'        => '',
											'label_color'  => '',
											'label_position' => '',
											'description'  => '',
											'description_color' => '',
											'divider_type' => $this->get_builder_element( $_prefix . 'divider_type', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
											'placeholder'  => '',
											'max_chars'    => '',
											'hide_amount'  => '',
											'options'      => $_options,
											'options_all'  => $_options_all,
											'min_price'    => $_min_price,
											'max_price'    => $_max_price,
											'rules'        => $_rules,
											'price_rules'  => $_regular_price,
											'rules_filtered' => $_rules_filtered,
											'price_rules_filtered' => $_regular_price_filtered,
											'price_rules_type' => $_regular_price_type,
											'rules_type'   => $_rules_type,
											'images'       => '',
											'limit'        => '',
											'exactlimit'   => '',
											'minimumlimit' => '',
											'option_values' => [],
											'button_type'  => '',
											'class'        => $this->get_builder_element( 'divider_class', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
											'uniqid'       => $this->get_builder_element( 'divider_uniqid', $builder, $current_builder, $current_counter, THEMECOMPLETE_EPO_HELPER()->tm_uniqid(), $current_element, '', $element_uniqueid ),
											'logicrules'   => $logicrules,
											'logic'        => $this->get_builder_element( 'divider_logic', $builder, $current_builder, $current_counter, '', $current_element, '', $element_uniqueid ),
											'format'       => '',
											'start_year'   => '',
											'end_year'     => '',
											'translation_day' => '',
											'translation_month' => '',
											'trasnlation_year' => '',
											'show_tooltip' => '',
											'changes_product_image' => '',
											'min'          => '',
											'max'          => '',
											'step'         => '',
											'pips'         => '',
										];

										$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['elements'][] = $this->cpf_single[ $element_uniqueid ];
									}
								}
							}

							$_helper_counter = $_helper_counter + $_sections[ $_s ];

							if ( $post_id !== $original_product_id ) {

								if ( is_array( $tm_meta_product_ids ) ) {
									foreach ( $variations_for_conditional_logic as $variation_id ) {
										if ( in_array( $variation_id, $tm_meta_product_ids ) ) { // phpcs:ignore WordPress.PHP.StrictInArray
											$rule             = [];
											$rule['section']  = $variation_section_id; // this will be addeed correctly later.
											$rule['element']  = 0;
											$rule['operator'] = 'is';
											$rule['value']    = floatval( THEMECOMPLETE_EPO_WPML()->get_current_id( $variation_id, 'product', null, 'product_variation' ) );
											if ( ! isset( $extra_section_logic[ $priority ] ) ) {
												$extra_section_logic[ $priority ] = [];
											}
											if ( ! isset( $extra_section_logic[ $priority ][ $tmcp_id ] ) ) {
												$extra_section_logic[ $priority ][ $tmcp_id ] = [];
											}
											if ( ! isset( $extra_section_logic[ $priority ][ $tmcp_id ][ $_s ] ) ) {
												$extra_section_logic[ $priority ][ $tmcp_id ][ $_s ] = [
													'section'         => $_sections_uniqid,
													'toggle'          => 'show',
													'sections_logic'  => $global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_logic'],
													'sections_logicrules' => $global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_logicrules'],
													'rules_to_add'    => [],
												];
											}
											$extra_section_logic[ $priority ][ $tmcp_id ][ $_s ]['rules_to_add'][] = $rule;
										}
									}
								}

								if ( is_array( $tm_meta_product_exclude_ids ) ) {
									foreach ( $variations_for_conditional_logic as $variation_id ) {
										if ( in_array( $variation_id, $tm_meta_product_exclude_ids ) ) { // phpcs:ignore WordPress.PHP.StrictInArray
											$rule             = [];
											$rule['section']  = $variation_section_id; // this will be addeed correctly later.
											$rule['element']  = 0;
											$rule['operator'] = 'is';
											$rule['value']    = floatval( THEMECOMPLETE_EPO_WPML()->get_current_id( $variation_id, 'product', null, 'product_variation' ) );
											if ( ! isset( $extra_section_hide_logic[ $priority ] ) ) {
												$extra_section_hide_logic[ $priority ] = [];
											}
											if ( ! isset( $extra_section_hide_logic[ $priority ][ $tmcp_id ] ) ) {
												$extra_section_hide_logic[ $priority ][ $tmcp_id ] = [];
											}
											if ( ! isset( $extra_section_hide_logic[ $priority ][ $tmcp_id ][ $_s ] ) ) {
												$extra_section_hide_logic[ $priority ][ $tmcp_id ][ $_s ] = [
													'section'         => $_sections_uniqid,
													'toggle'          => 'hide',
													'sections_logic'  => $global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_logic'],
													'sections_logicrules' => $global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_logicrules'],
													'rules_to_add'    => [],
												];
											}
											$extra_section_hide_logic[ $priority ][ $tmcp_id ][ $_s ]['rules_to_add'][] = $rule;

										}
									}
								}
							}
						}
					}

					if ( ! empty( $templates ) ) {
						foreach ( $global_epos[ $priority ][ $tmcp_id ]['sections'] as $_s => $sections_data ) {
							if ( isset( $sections_data['elements'] ) ) {
								foreach ( $sections_data['elements'] as $element_key_index => $element_data ) {
									if ( ! isset( $element_data['builder'] ) || ! isset( $element_data['uniqid'] ) ) {
										continue;
									}
									$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['elements'][ $element_key_index ]['builder'] = $builder;
									$this->cpf_single[ $element_data['uniqid'] ] = $global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['elements'][ $element_key_index ];
								}
							}
						}
					}
				}
			}
		}

		if ( $variation_section_id ) {
			foreach ( $extra_section_logic as $priority => $priority_data ) {
				foreach ( $priority_data as $tmcp_id => $_s_data ) {
					foreach ( $_s_data as $_s => $logic_data ) {
						$has_logic    = $logic_data['sections_logic'];
						$rules_to_add = array_map(
							function ( $element ) use ( $variation_section_id ) {
								$element['section'] = $variation_section_id;
								return $element;
							},
							$logic_data['rules_to_add']
						);

						if ( $has_logic ) {
							$current_section_logic = THEMECOMPLETE_EPO_CONDITIONAL_LOGIC()->convert_rules( json_decode( wp_unslash( $logic_data['sections_logicrules'] ) ) );
							$current_section_logic = THEMECOMPLETE_EPO_CONDITIONAL_LOGIC()->convert_rules( $current_section_logic );
							if ( is_array( $current_section_logic ) ) {
								$current_toggle = $current_section_logic['toggle'];
								if ( 'show' === $current_toggle ) {
									// For each of the rules to add
									// create a new set of the original rules
									// and add the current rule to the start.
									$combinations = [];
									foreach ( $rules_to_add as $rule ) {
										foreach ( $current_section_logic['rules'] as $original_rule ) {
											$combinations[] = array_merge( [ $rule ], $original_rule );
										}
									}
									$current_section_logic['rules'] = array_reduce(
										$combinations,
										function ( $carry, $item ) {
											return array_merge( $carry, [ $item ] );
										},
										[]
									);
								} elseif ( 'hide' === $current_toggle ) {
									$rules_to_add = array_map(
										function ( $element ) {
											$element['operator'] = 'isnot';
											return $element;
										},
										$rules_to_add
									);

									$current_section_logic['rules'] = array_merge( $current_section_logic['rules'], [ $rules_to_add ] );
								}
							}

							$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_logicrules'] = wp_json_encode( $current_section_logic );

						} else {
							$section   = $logic_data['section'];
							$toggle    = $logic_data['toggle'];
							$new_logic = [
								'section' => $section,
								'toggle'  => $toggle,
								'rules'   => array_map(
									function ( $element ) {
										return [ $element ];
									},
									$rules_to_add
								),
							];
							$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_logicrules'] = wp_json_encode( $new_logic );
						}

						$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_logic'] = '1';
					}
				}
			}
			foreach ( $extra_section_hide_logic as $priority => $priority_data ) {
				foreach ( $priority_data as $tmcp_id => $_s_data ) {
					foreach ( $_s_data as $_s => $logic_data ) {
						$has_logic    = $logic_data['sections_logic'];
						$rules_to_add = array_map(
							function ( $element ) use ( $variation_section_id ) {
								$element['section'] = $variation_section_id;
								return $element;
							},
							$logic_data['rules_to_add']
						);

						if ( $has_logic ) {
							$current_section_logic = THEMECOMPLETE_EPO_CONDITIONAL_LOGIC()->convert_rules( json_decode( wp_unslash( $logic_data['sections_logicrules'] ) ) );
							$current_section_logic = THEMECOMPLETE_EPO_CONDITIONAL_LOGIC()->convert_rules( $current_section_logic );
							if ( is_array( $current_section_logic ) ) {
								$current_toggle = $current_section_logic['toggle'];
								if ( 'show' === $current_toggle ) {
									$rules_to_add = array_map(
										function ( $element ) {
											$element['operator'] = 'isnot';
											return $element;
										},
										$rules_to_add
									);
									// For each of the rules to add
									// create a new set of the original rules
									// and add the current rule to the start.
									$combinations = [];
									foreach ( $rules_to_add as $rule ) {
										foreach ( $current_section_logic['rules'] as $original_rule ) {
											$combinations[] = array_merge( [ $rule ], $original_rule );
										}
									}
									$current_section_logic['rules'] = array_reduce(
										$combinations,
										function ( $carry, $item ) {
											return array_merge( $carry, [ $item ] );
										},
										[]
									);
								} elseif ( 'hide' === $current_toggle ) {
									$current_section_logic['rules'] = array_merge( $current_section_logic['rules'], [ $rules_to_add ] );
								}
							}

							$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_logicrules'] = wp_json_encode( $current_section_logic );

						} else {
							$section   = $logic_data['section'];
							$toggle    = $logic_data['toggle'];
							$new_logic = [
								'section' => $section,
								'toggle'  => $toggle,
								'rules'   => array_map(
									function ( $element ) {
										return [ $element ];
									},
									$rules_to_add
								),
							];
							$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_logicrules'] = wp_json_encode( $new_logic );
						}

						$global_epos[ $priority ][ $tmcp_id ]['sections'][ $_s ]['sections_logic'] = '1';
					}
				}
			}

			if ( empty( $extra_section_logic )
				&& 1 === count( $global_epos )
				&& isset( $global_epos[1000] )
				&& isset( $global_epos[1000][ $post_id ] )
				&& isset( $global_epos[1000][ $post_id ]['sections'] )
				&& 1 === count( $global_epos[1000][ $post_id ]['sections'] )
				&& isset( $global_epos[1000][ $post_id ]['sections'][0] )
				&& (int) 1 === (int) $global_epos[1000][ $post_id ]['sections'][0]['total_elements']
				&& isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'] )
				&& 1 === count( $global_epos[1000][ $post_id ]['sections'][0]['elements'] )
				&& isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'][0] )
				&& isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'][0]['type'] )
				&& 'variations' === $global_epos[1000][ $post_id ]['sections'][0]['elements'][0]['type']
				&& isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'][0]['builder'] )
				&& isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'][0]['builder']['variations_disabled'] )
				&& (int) 1 === (int) $global_epos[1000][ $post_id ]['sections'][0]['elements'][0]['builder']['variations_disabled']
			) {
				$global_epos = [];
			}

			if ( ( isset( $wpml_is_original_product ) && empty( $wpml_is_original_product ) ) ) {
				if ( empty( $extra_section_logic )
					&& 1 === count( $global_epos )
					&& isset( $global_epos[1000] )
					&& isset( $global_epos[1000][ $post_original_id ] )
					&& isset( $global_epos[1000][ $post_original_id ]['sections'] )
					&& 1 === count( $global_epos[1000][ $post_original_id ]['sections'] )
					&& isset( $global_epos[1000][ $post_original_id ]['sections'][0] )
					&& isset( $global_epos[1000][ $post_original_id ]['sections'][0]['elements'] )
					&& (int) 1 === (int) $global_epos[1000][ $post_original_id ]['sections'][0]['total_elements']
					&& 1 === count( $global_epos[1000][ $post_original_id ]['sections'][0]['elements'] )
					&& isset( $global_epos[1000][ $post_original_id ]['sections'][0]['elements'][0] )
					&& isset( $global_epos[1000][ $post_original_id ]['sections'][0]['elements'][0]['type'] )
					&& 'variations' === $global_epos[1000][ $post_original_id ]['sections'][0]['elements'][0]['type']
					&& isset( $global_epos[1000][ $post_original_id ]['sections'][0]['elements'][0]['original_builder'] )
					&& isset( $global_epos[1000][ $post_original_id ]['sections'][0]['elements'][0]['original_builder']['variations_disabled'] )
					&& (int) 1 === (int) $global_epos[1000][ $post_original_id ]['sections'][0]['elements'][0]['original_builder']['variations_disabled']
				) {
					$global_epos = [];
				}
			}
		}

		$variations_disabled       = false;
		$isset_variations_disabled = ( isset( $global_epos[1000] )
									&& isset( $global_epos[1000][ $post_id ] )
									&& isset( $global_epos[1000][ $post_id ]['sections'][0] )
									&& isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'] )
									&& isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'][0] )
									&& isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'][0]['builder'] )
									&& isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'][0]['builder']['variations_disabled'] ) );

		if ( ( isset( $wpml_is_original_product ) && empty( $wpml_is_original_product ) ) ) {
			$isset_variations_disabled = ( isset( $global_epos[1000] )
				&& isset( $global_epos[1000][ $post_original_id ] )
				&& isset( $global_epos[1000][ $post_original_id ]['sections'][0] )
				&& isset( $global_epos[1000][ $post_original_id ]['sections'][0]['elements'] )
				&& isset( $global_epos[1000][ $post_original_id ]['sections'][0]['elements'][0] )
				&& isset( $global_epos[1000][ $post_original_id ]['sections'][0]['elements'][0]['original_builder'] )
				&& isset( $global_epos[1000][ $post_original_id ]['sections'][0]['elements'][0]['original_builder']['variations_disabled'] ) );
		}
		if ( $isset_variations_disabled ) {
			$variations_disabled = ( isset( $global_epos[1000][ $post_id ] )
									&& isset( $global_epos[1000][ $post_id ]['sections'][0] )
									&& isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'] )
									&& isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'][0] )
									&& isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'][0]['builder'] )
									&& isset( $global_epos[1000][ $post_id ]['sections'][0]['elements'][0]['builder']['variations_disabled'] )
									&& (int) 1 === (int) $global_epos[1000][ $post_id ]['sections'][0]['elements'][0]['builder']['variations_disabled'] );

			if ( ( isset( $wpml_is_original_product ) && empty( $wpml_is_original_product ) ) ) {
				$variations_disabled = ( isset( $global_epos[1000] )
					&& isset( $global_epos[1000][ $post_original_id ] )
					&& isset( $global_epos[1000][ $post_original_id ]['sections'][0] )
					&& isset( $global_epos[1000][ $post_original_id ]['sections'][0]['elements'] )
					&& isset( $global_epos[1000][ $post_original_id ]['sections'][0]['elements'][0] )
					&& isset( $global_epos[1000][ $post_original_id ]['sections'][0]['elements'][0]['original_builder'] )
					&& isset( $global_epos[1000][ $post_original_id ]['sections'][0]['elements'][0]['original_builder']['variations_disabled'] )
					&& (int) 1 === (int) $global_epos[1000][ $post_original_id ]['sections'][0]['elements'][0]['original_builder']['variations_disabled'] );
			}
		}

		if ( $not_isset_global_post ) {
			unset( $GLOBALS['post'] );
			if ( wp_doing_ajax() && $not_isset_request_post_id ) {
				unset( $_REQUEST['post_id'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			}
		}
		return [
			'global'               => $global_epos,
			'price'                => $epos_prices,
			'variation_element_id' => $variation_element_id,
			'variation_section_id' => $variation_section_id,
			'variations_disabled'  => $variations_disabled,
			'raw_epos'             => $raw_epos,
			'product_epos_uniqids' => $product_epos_uniqids,
			'product_epos_choices' => $product_epos_choices,
		];
	}

	/**
	 * Return date format data for the date element
	 *
	 * @param string $format The format code.
	 *
	 * @since 6.1
	 * @return array<string>
	 */
	public function get_date_format( $format = '0' ) {
		$date_format         = 'd/m/Y';
		$sep                 = '/';
		$element_date_format = 'dd/mm/yy';
		$date_placeholder    = 'dd/mm/yyyy';
		$date_mask           = '00/00/0000';

		switch ( $format ) {
			case '0':
				$date_format         = 'd/m/Y';
				$sep                 = '/';
				$element_date_format = 'dd/mm/yy';
				$date_placeholder    = 'dd/mm/yyyy';
				$date_mask           = '00/00/0000';
				break;
			case '1':
				$date_format         = 'm/d/Y';
				$sep                 = '/';
				$element_date_format = 'mm/dd/yy';
				$date_placeholder    = 'mm/dd/yyyy';
				$date_mask           = '00/00/0000';
				break;
			case '2':
				$date_format         = 'd.m.Y';
				$sep                 = '.';
				$element_date_format = 'dd.mm.yy';
				$date_placeholder    = 'dd.mm.yyyy';
				$date_mask           = '00.00.0000';
				break;
			case '3':
				$date_format         = 'm.d.Y';
				$sep                 = '.';
				$element_date_format = 'mm.dd.yy';
				$date_placeholder    = 'mm.dd.yyyy';
				$date_mask           = '00.00.0000';
				break;
			case '4':
				$date_format         = 'd-m-Y';
				$sep                 = '-';
				$element_date_format = 'dd-mm-yy';
				$date_placeholder    = 'dd-mm-yyyy';
				$date_mask           = '00-00-0000';
				break;
			case '5':
				$date_format         = 'm-d-Y';
				$sep                 = '-';
				$element_date_format = 'mm-dd-yy';
				$date_placeholder    = 'mm-dd-yyyy';
				$date_mask           = '00-00-0000';
				break;

			case '6':
				$date_format         = 'Y/m/d';
				$sep                 = '/';
				$element_date_format = 'yy/mm/dd';
				$date_placeholder    = 'yyyy/mm/dd';
				$date_mask           = '0000/00/00';
				break;
			case '7':
				$date_format         = 'Y/d/m';
				$sep                 = '/';
				$element_date_format = 'yy/dd/mm';
				$date_placeholder    = 'yyyy/dd/mm';
				$date_mask           = '0000/00/00';
				break;
			case '8':
				$date_format         = 'Y.m.d';
				$sep                 = '.';
				$element_date_format = 'yy.mm.dd';
				$date_placeholder    = 'yyyy.mm.dd';
				$date_mask           = '0000.00.00';
				break;
			case '9':
				$date_format         = 'Y.d.m';
				$sep                 = '.';
				$element_date_format = 'yy.dd.mm';
				$date_placeholder    = 'yyyy.dd.mm';
				$date_mask           = '0000.00.00';
				break;
			case '10':
				$date_format         = 'Y-m-d';
				$sep                 = '-';
				$element_date_format = 'yy-mm-dd';
				$date_placeholder    = 'yyyyy-mm-dd';
				$date_mask           = '0000-00-00';
				break;
			case '11':
				$date_format         = 'Y-d-m';
				$sep                 = '-';
				$element_date_format = 'yy-dd-mm';
				$date_placeholder    = 'yyyy-dd-mm';
				$date_mask           = '0000-00-00';
				break;
		}

		$data = [
			'date_format'         => $date_format,
			'sep'                 => $sep,
			'element_date_format' => $element_date_format,
			'date_placeholder'    => $date_placeholder,
			'date_mask'           => $date_mask,
		];

		return $data;
	}

	/**
	 * Translate $attributes to post names
	 *
	 * @param array<mixed> $attributes Element option choices.
	 * @param string       $type element type.
	 * @param mixed        $field_loop Field loop.
	 * @param string       $form_prefix should be passed with _ if not empty.
	 * @param string       $name_prefix Name prefix.
	 * @param array<mixed> $element The element array.
	 *
	 * @return array<mixed>
	 */
	public function get_post_names( $attributes, $type, $field_loop = '', $form_prefix = '', $name_prefix = '', $element = [] ) {
		$fields = [];
		$loop   = 0;

		$element_object = $this->tm_builder_elements[ $type ];
		if ( ! empty( $attributes ) ) {
			foreach ( $attributes as $key => $attribute ) {
				$name_inc = '';
				if ( ! empty( $element_object->post_name_prefix ) && ! empty( $element_object->type ) ) {
					if ( 'multiple' === $element_object->type || 'multiplesingle' === $element_object->type || 'singlemultiple' === $element_object->type ) {
						$name_inc = 'tmcp_' . $name_prefix . $element_object->post_name_prefix . '_' . $field_loop . $form_prefix;
					} elseif ( 'multipleall' === $element_object->type ) {
						$name_inc = 'tmcp_' . $name_prefix . $element_object->post_name_prefix . '_' . $field_loop . '_' . $loop . $form_prefix;
					}
				}
				$fields[] = $name_inc;
				++$loop;
			}
		} else {
			if ( ! empty( $element_object->type ) && ! empty( $element_object->post_name_prefix ) ) {
				$name_inc = 'tmcp_' . $name_prefix . $element_object->post_name_prefix . '_' . $field_loop . $form_prefix;
				if ( is_array( $element ) && isset( $element['mode'] ) && 'product' !== $element['mode'] && isset( $element['type'] ) && 'product' === $element['type'] && isset( $element['layout_mode'] ) && ( 'checkbox' === $element['layout_mode'] || 'thumbnailmultiple' === $element['layout_mode'] ) ) {
					$name_inc = $name_inc . '_*';
				}
			}

			if ( ! empty( $name_inc ) ) {
				$fields[] = $name_inc;
			}
		}

		return $fields;
	}

	/**
	 * Append name_inc functions (required for condition logic to check if an element is visible)
	 *
	 * @param integer      $post_id The product id.
	 * @param array<mixed> $global_epos The global options array.
	 * @param array<mixed> $product_epos The normal options array.
	 * @param string       $form_prefix The form prefix.
	 * @param string       $add_identifier The identifier (currently not used).
	 *
	 * @return array<mixed>
	 */
	public function tm_fill_element_names( $post_id = 0, $global_epos = [], $product_epos = [], $form_prefix = '', $add_identifier = '' ) {
		$global_price_array = $global_epos;
		$local_price_array  = $product_epos;

		$global_prices = [
			'before' => [],
			'after'  => [],
		];
		foreach ( $global_price_array as $priority => $priorities ) {
			foreach ( $priorities as $pid => $field ) {
				if ( isset( $field['sections'] ) && is_array( $field['sections'] ) ) {
					foreach ( $field['sections'] as $section_id => $section ) {
						if ( isset( $section['sections_placement'] ) ) {
							$global_prices[ $section['sections_placement'] ][ $priority ][ $pid ]['sections'][ $section_id ] = $section;
						}
					}
				}
			}
		}
		$unit_counter         = 0;
		$field_counter        = 0;
		$element_counter      = 0;
		$connectors           = [];
		$element_type_counter = [];

		// global options before local.
		foreach ( $global_prices['before'] as $priority => $priorities ) {
			foreach ( $priorities as $pid => $field ) {
				$args    = [
					'priority'             => $priority,
					'pid'                  => $pid,
					'unit_counter'         => $unit_counter,
					'field_counter'        => $field_counter,
					'element_counter'      => $element_counter,
					'connectors'           => $connectors,
					'element_type_counter' => $element_type_counter,
				];
				$_return = $this->fill_builder_display( $global_epos, $field, 'before', $args, $form_prefix, $add_identifier );

				$global_epos          = $_return['global_epos'];
				$unit_counter         = $_return['unit_counter'];
				$field_counter        = $_return['field_counter'];
				$element_counter      = $_return['element_counter'];
				$connectors           = $_return['connectors'];
				$element_type_counter = $_return['element_type_counter'];

			}
		}

		// normal (local) options.
		if ( is_array( $local_price_array ) && count( $local_price_array ) > 0 ) {
			$attributes = themecomplete_get_attributes( $post_id );
			if ( is_array( $attributes ) && count( $attributes ) > 0 ) {
				foreach ( $local_price_array['product_epos'] as $field ) {
					if ( isset( $field['name'] ) && isset( $attributes[ $field['name'] ] ) && ! $attributes[ $field['name'] ]['is_variation'] ) {
						$attribute     = $attributes[ $field['name'] ];
						$field_counter = 0;
						if ( $attribute['is_taxonomy'] ) {
							switch ( $field['type'] ) {
								case 'select':
									++$element_counter;
									break;
								case 'radio':
								case 'checkbox':
									++$element_counter;
									break;
							}
						} else {
							switch ( $field['type'] ) {
								case 'select':
									++$element_counter;
									break;
								case 'radio':
								case 'checkbox':
									++$element_counter;
									break;
							}
						}
						++$unit_counter;
					}
				}
			}
		}

		// global options after normal (local).
		foreach ( $global_prices['after'] as $priority => $priorities ) {
			foreach ( $priorities as $pid => $field ) {
				$args    = [
					'priority'             => $priority,
					'pid'                  => $pid,
					'unit_counter'         => $unit_counter,
					'field_counter'        => $field_counter,
					'element_counter'      => $element_counter,
					'connectors'           => $connectors,
					'element_type_counter' => $element_type_counter,
				];
				$_return = $this->fill_builder_display( $global_epos, $field, 'after', $args, $form_prefix, $add_identifier );

				$global_epos          = $_return['global_epos'];
				$unit_counter         = $_return['unit_counter'];
				$field_counter        = $_return['field_counter'];
				$element_counter      = $_return['element_counter'];
				$connectors           = $_return['connectors'];
				$element_type_counter = $_return['element_type_counter'];

			}
		}

		return $global_epos;
	}

	/**
	 * Generates correct html names for the builder fields
	 *
	 * @param array<mixed> $global_epos The global options array.
	 * @param array<mixed> $field The element field array.
	 * @param string       $where Placement of the section 'before' or 'after'.
	 * @param array<mixed> $args Array of arguments.
	 * @param string       $form_prefix The form prefix (shoud be passed with _ if not empty).
	 * @param string       $add_identifier The identifier (currently not used).
	 *
	 * @return array<mixed>
	 */
	public function fill_builder_display( $global_epos, $field, $where, $args, $form_prefix = '', $add_identifier = '' ) {
		$priority             = $args['priority'];
		$pid                  = $args['pid'];
		$unit_counter         = $args['unit_counter'];
		$field_counter        = $args['field_counter'];
		$element_counter      = $args['element_counter'];
		$connectors           = $args['connectors'];
		$element_type_counter = $args['element_type_counter'];

		$cart_fee_name = $this->cart_fee_name;

		if ( isset( $field['sections'] ) && is_array( $field['sections'] ) ) {
			foreach ( $field['sections'] as $_s => $section ) {
				if ( ! isset( $section['sections_placement'] ) || $section['sections_placement'] !== $where ) {
					continue;
				}
				if ( isset( $section['elements'] ) && is_array( $section['elements'] ) ) {
					foreach ( $section['elements'] as $arr_element_counter => $element ) {

						$is_enabled = isset( $element['enabled'] ) ? $element['enabled'] : 2;
						// Currently $no_disabled is disabled by default
						// to allow the conditional logic
						// to work correctly when there is a disabled element.
						if ( '' === $is_enabled || '0' === $is_enabled ) {

							// It is required to increment the counter because
							// it is use on the builder variable which includes disabled elements.
							if ( ! isset( $element_type_counter[ $element['type'] ] ) ) {
								$element_type_counter[ $element['type'] ] = 0;
							}
							++$element_type_counter[ $element['type'] ];

							continue;
						}
						$field_counter = 0;

						if ( ! empty( $add_identifier ) ) {
							$global_epos[ $priority ][ $pid ]['sections'][ $_s ]['elements'][ $arr_element_counter ]['add_identifier'] = $add_identifier;
						}
						if ( isset( $this->tm_builder_elements[ $element['type'] ] ) && 'post' === $this->tm_builder_elements[ $element['type'] ]->is_post ) {

							$element_object = $this->tm_builder_elements[ $element['type'] ];

							$c_element_counter = $element_counter;
							if ( isset( $element['connector'] ) && isset( $connectors[ 'c-' . sanitize_key( $element['connector'] ) ] ) ) {
								$c_element_counter = $connectors[ 'c-' . sanitize_key( $element['connector'] ) ];
							}

							if ( 'single' === $element_object->type || 'multipleallsingle' === $element_object->type || 'multiplesingle' === $element_object->type || 'singlemultiple' === $element_object->type ) {

								if ( ! isset( $element_type_counter[ $element['type'] ] ) ) {
									$element_type_counter[ $element['type'] ] = 0;
								}

								$name_inc      = $element_object->post_name_prefix . '_' . $c_element_counter;
								$base_name_inc = $name_inc;

								$is_cart_fee = ! empty( $element['is_cart_fee'] );
								if ( $is_cart_fee ) {
									$name_inc = $cart_fee_name . $name_inc;
								}

								$name_inc = apply_filters( 'wc_epo_name_inc', $name_inc, $base_name_inc, $element, false, false, $element_type_counter[ $element['type'] ] );

								$global_epos[ $priority ][ $pid ]['sections'][ $_s ]['elements'][ $arr_element_counter ]['raw_name_inc']        = $name_inc;
								$global_epos[ $priority ][ $pid ]['sections'][ $_s ]['elements'][ $arr_element_counter ]['raw_name_inc_prefix'] = ( '' !== $form_prefix ) ? '_' . str_replace( '_', '', $form_prefix ) : '';

								$name_inc = 'tmcp_' . $name_inc . ( ( '' !== $form_prefix ) ? '_' . str_replace( '_', '', $form_prefix ) : '' );
								$global_epos[ $priority ][ $pid ]['sections'][ $_s ]['elements'][ $arr_element_counter ]['name_inc']    = $name_inc;
								$global_epos[ $priority ][ $pid ]['sections'][ $_s ]['elements'][ $arr_element_counter ]['is_cart_fee'] = $is_cart_fee;

								$global_epos = apply_filters( 'global_epos_fill_builder_display', $global_epos, $priority, $pid, $_s, $arr_element_counter, $element, false, false, false );

								++$element_type_counter[ $element['type'] ];
							} elseif ( 'multipleall' === $element_object->type || 'multiple' === $element_object->type ) {

								$choice_counter = 0;

								if ( ! isset( $element_type_counter[ $element['type'] ] ) ) {
									$element_type_counter[ $element['type'] ] = 0;
								}

								foreach ( $element['options'] as $value => $label ) {

									if ( 'multipleall' === $element_object->type ) {
										$name_inc = $element_object->post_name_prefix . '_' . $c_element_counter . '_' . $field_counter;
									} else {
										$name_inc = $element_object->post_name_prefix . '_' . $c_element_counter;
									}

									$base_name_inc = $name_inc;

									if ( 'checkbox' === $element['type'] ) {
										$is_cart_fee = ! empty( $element['is_cart_fee_multiple'][ $field_counter ] );
									} else {
										$is_cart_fee = ! empty( $element['is_cart_fee'] );
									}
									if ( $is_cart_fee ) {
										$name_inc = $cart_fee_name . $name_inc;
									}

									$name_inc = apply_filters( 'wc_epo_name_inc', $name_inc, $base_name_inc, $element, $value, $choice_counter, $element_type_counter[ $element['type'] ] );

									$global_epos[ $priority ][ $pid ]['sections'][ $_s ]['elements'][ $arr_element_counter ]['raw_name_inc'][ $field_counter ]        = $name_inc;
									$global_epos[ $priority ][ $pid ]['sections'][ $_s ]['elements'][ $arr_element_counter ]['raw_name_inc_prefix'][ $field_counter ] = ( '' !== $form_prefix ) ? '_' . str_replace( '_', '', $form_prefix ) : '';

									$name_inc = 'tmcp_' . $name_inc . ( ( '' !== $form_prefix ) ? '_' . str_replace( '_', '', $form_prefix ) : '' );
									$global_epos[ $priority ][ $pid ]['sections'][ $_s ]['elements'][ $arr_element_counter ]['name_inc'][] = $name_inc;

									$global_epos[ $priority ][ $pid ]['sections'][ $_s ]['elements'][ $arr_element_counter ]['is_cart_fee_multiple'][ $field_counter ] = $is_cart_fee;

									$global_epos = apply_filters( 'global_epos_fill_builder_display', $global_epos, $priority, $pid, $_s, $arr_element_counter, $element, $value, $choice_counter, $element_type_counter[ $element['type'] ] );

									++$choice_counter;

									++$field_counter;

								}

								++$element_type_counter[ $element['type'] ];

							}
							if ( isset( $element['connector'] ) && '' !== $element['connector'] ) {
								if ( ! isset( $connectors[ 'c-' . sanitize_key( $element['connector'] ) ] ) ) {
									++$element_counter;
								}
								$connectors[ 'c-' . sanitize_key( $element['connector'] ) ] = $c_element_counter;
							} else {
								++$element_counter;
							}
						}
					}
				}
			}
			++$unit_counter;
		}

		return [
			'global_epos'          => $global_epos,
			'unit_counter'         => $unit_counter,
			'field_counter'        => $field_counter,
			'element_counter'      => $element_counter,
			'connectors'           => $connectors,
			'element_type_counter' => $element_type_counter,
		];
	}
}

define( 'THEMECOMPLETE_EPO_INCLUDED', 1 );
