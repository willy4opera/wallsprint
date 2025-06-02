<?php
/**
 * Extra Product Options Frontend Display
 *
 * @package Extra Product Options/Classes
 * @version 6.4
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extra Product Options Frontend Display
 *
 * @package Extra Product Options/Classes
 * @version 6.4
 */
class THEMECOMPLETE_EPO_Display {

	/**
	 * If both the options and the totals box have been displayed or not
	 *
	 * Prevents option duplication for bad coded themes.
	 *
	 * @var boolean
	 */
	public $tm_options_have_been_displayed = false;

	/**
	 * If the options has been displayed or not
	 *
	 * @var boolean
	 */
	public $tm_options_single_have_been_displayed = false;

	/**
	 * If the totals box has been displayed or not
	 *
	 * @var boolean
	 */
	public $tm_options_totals_have_been_displayed = false;

	/**
	 * The id of the current set of options
	 *
	 * @var integer
	 */
	private $epo_id = 0;

	/**
	 * The current id of the set of options displayed
	 *
	 * This is different from $epo_id as it supports
	 * the product element.
	 *
	 * @var integer
	 */
	private $epo_internal_counter = 0;

	/**
	 * Array of the $epo_internal_counter
	 *
	 * @var array<mixed>
	 */
	private $epo_internal_counter_check = [];

	/**
	 * The original $epo_internal_counter
	 *
	 * Used when printing options for the product element.
	 *
	 * @var integer
	 */
	private $original_epo_internal_counter = 0;

	/**
	 * The id of the product the options belong to.
	 *
	 * @var integer
	 */
	private $current_product_id_to_be_displayed = 0;

	/**
	 * Array of the $current_product_id_to_be_displayed
	 *
	 * @var array<mixed>
	 */
	private $current_product_id_to_be_displayed_check = [];

	/**
	 * Inline styles printed at totals box
	 *
	 * @var string
	 */
	public $inline_styles;

	/**
	 * Inline styles printed at html head
	 *
	 * @var string
	 */
	public $inline_styles_head;

	/**
	 * Unique form prefix
	 *
	 * @var string
	 */
	public $unique_form_prefix = '';

	/**
	 * Associated product discount
	 *
	 * @var string
	 */
	private $discount = '';

	/**
	 * Associated product discount type
	 *
	 * @var string
	 */
	private $discount_type = '';

	/**
	 * If the associated product discount is applied to the addons
	 *
	 * @var string
	 */
	public $discount_exclude_addons = '';

	/**
	 * Flag to blocking option display
	 *
	 * @var boolean
	 */
	public $block_epo = false;

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_Display|null
	 * @since 1.0
	 */
	protected static $instance = null;


	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return THEMECOMPLETE_EPO_Display
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
		$this->inline_styles      = '';
		$this->inline_styles_head = '';

		// Display in frontend.
		add_action( 'woocommerce_tm_epo', [ $this, 'frontend_display' ], 10, 3 );
		add_action( 'woocommerce_tm_epo_fields', [ $this, 'tm_epo_fields' ], 10, 4 );
		add_action( 'woocommerce_tm_epo_totals', [ $this, 'tm_epo_totals' ], 10, 3 );

		// Display in frontend (Compatibility for older plugin versions).
		add_action( 'woocommerce_tm_custom_price_fields', [ $this, 'frontend_display' ] );
		add_action( 'woocommerce_tm_custom_price_fields_only', [ $this, 'tm_epo_fields' ] );
		add_action( 'woocommerce_tm_custom_price_fields_totals', [ $this, 'tm_epo_totals' ] );

		// Ensures the correct display order of options when multiple products are displayed.
		add_action( 'woocommerce_before_single_product', [ $this, 'woocommerce_before_single_product' ], 1 );
		add_action( 'woocommerce_after_single_product', [ $this, 'woocommerce_after_single_product' ], 9999 );

		// Internal variables.
		add_action( 'woocommerce_before_add_to_cart_button', [ $this, 'woocommerce_before_add_to_cart_button' ] );

		// Add custom inline css.
		add_action( 'template_redirect', [ $this, 'tm_variation_css_check' ], 9999 );

		// Alter the array of data for a variation. Used in the add to cart form.
		add_filter( 'woocommerce_available_variation', [ $this, 'woocommerce_available_variation' ], 10, 3 );

		// Add support for WooCommerce shortcodes on custom pages.
		add_filter( 'woocommerce_loop_add_to_cart_args', [ $this, 'woocommerce_loop_add_to_cart_args' ], 10, 1 );
	}

	/**
	 * Add support for WooCommerce shortcodes on custom pages
	 *
	 * @param array<mixed> $args Array of arguements.
	 * @since 6.4
	 * @return array<mixed>
	 */
	public function woocommerce_loop_add_to_cart_args( $args = [] ) {
		if ( $this->epo_id > 0 ) {
			$args['attributes']['data-epo-id'] = $this->epo_id;
		}
		return $args;
	}

	/**
	 * Apply asoociated product discount
	 *
	 * @param string $discount Associated product discount.
	 * @param string $discount_type Associated product discount type.
	 * @param string $discount_exclude_addons If the associated product discount is applied to the addons.
	 * @return void
	 * @since 5.0.8
	 */
	public function set_discount( $discount = '', $discount_type = '', $discount_exclude_addons = '' ) {
		$this->discount                = $discount;
		$this->discount_type           = $discount_type;
		$this->discount_exclude_addons = $discount_exclude_addons;
	}

	/**
	 * Change internal epo counter
	 * Currently used for associated products
	 *
	 * @param integer $counter The value to set for the internal epo counter.
	 * @return void
	 * @since 5.0.8
	 */
	public function set_epo_internal_counter( $counter = 0 ) {
		$this->original_epo_internal_counter = $this->epo_internal_counter;
		$this->epo_internal_counter          = $counter;
	}

	/**
	 * Restore internal epo counter
	 * Currently used for associated products
	 *
	 * @return void
	 * @since 5.0.8
	 */
	public function restore_epo_internal_counter() {
		$this->epo_internal_counter = $this->original_epo_internal_counter;
	}

	/**
	 * Returns the path for overriding the templates
	 *
	 * @return string
	 * @since 6.0
	 */
	public function get_template_path() {
		return apply_filters( 'wc_epo_template_override_path', THEMECOMPLETE_EPO_NAMESPACE . '/' );
	}

	/**
	 * Returns the default path for the templates
	 *
	 * @return string
	 * @since 6.0
	 */
	public function get_default_path() {
		return apply_filters( 'wc_epo_default_template_path', THEMECOMPLETE_EPO_TEMPLATE_PATH );
	}


	/**
	 * Get the tax rate of the given tax classes
	 *
	 * @param string $classes Tax class.
	 *
	 * @return float|integer
	 */
	public function get_tax_rate( $classes ) {
		return themecomplete_get_tax_rate( $classes );
	}

	/**
	 * Add validation rules
	 *
	 * @param array<mixed> $element The element array.
	 * @return array<mixed>
	 */
	public function get_tm_validation_rules( $element ) {
		$rules = [];
		if ( $element['required'] ) {
			$rules['required'] = true;
		}
		if ( isset( $element['min_chars'] ) && '' !== $element['min_chars'] && false !== $element['min_chars'] ) {
			$rules['minlength'] = absint( $element['min_chars'] );
		}
		if ( isset( $element['max_chars'] ) && '' !== $element['max_chars'] && false !== $element['max_chars'] ) {
			$rules['maxlength'] = absint( $element['max_chars'] );
		}
		if ( isset( $element['min'] ) && '' !== $element['min'] && ( 'number' === $element['validation1'] || 'digits' === $element['validation1'] ) ) {
			$rules['min'] = floatval( $element['min'] );
		}
		if ( isset( $element['max'] ) && '' !== $element['max'] && ( 'number' === $element['validation1'] || 'digits' === $element['validation1'] ) ) {
			$rules['max'] = floatval( $element['max'] );
		}
		if ( ! empty( $element['validation1'] ) ) {
			$rules[ $element['validation1'] ] = true;
		}
		if ( ! empty( $element['repeater'] ) && ! empty( $element['repeater_min_rows'] ) ) {
			$rules['repeaterminrows'] = $element['repeater_min_rows'];
		}
		if ( ! empty( $element['repeater'] ) && ! empty( $element['repeater_max_rows'] ) ) {
			$rules['repeatermaxrows'] = $element['repeater_max_rows'];
		}

		return $rules;
	}

	/**
	 * Alter the array of data for a variation.
	 * Used in the add to cart form.
	 *
	 * @param array<mixed>        $args Array of variation arguments.
	 * @param WC_Product_Variable $class_obj The WC_Product_Variable class.
	 * @param WC_Product          $variation Variation product object or ID.
	 * @return array<mixed>
	 * @since 1.0
	 */
	public function woocommerce_available_variation( $args, $class_obj, $variation ) {
		if ( apply_filters( 'wc_epo_woocommerce_available_variation_check', true ) && ! ( THEMECOMPLETE_EPO()->can_load_scripts() || wp_doing_ajax() ) && ! ( isset( $_REQUEST['wc-ajax'] ) && 'get_variation' === $_REQUEST['wc-ajax'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return $args;
		}

		if ( is_array( $args ) ) {

			$tax_rate = $this->get_tax_rate( themecomplete_get_tax_class( $variation ) );

			$taxes_of_one        = 0;
			$base_taxes_of_one   = 0;
			$modded_taxes_of_one = 0;

			$non_base_location_prices = -1;
			$base_tax_rate            = $tax_rate;

			if ( class_exists( 'WC_Tax' ) && version_compare( get_option( 'woocommerce_version' ), '2.4', '>=' ) ) {
				$tax_rates      = WC_Tax::get_rates( themecomplete_get_tax_class( $variation ) );
				$base_tax_rates = WC_Tax::get_base_tax_rates( themecomplete_get_tax_class( $variation ) );
				$base_tax_rate  = 0;
				foreach ( $base_tax_rates as $key => $value ) {
					$base_tax_rate = $base_tax_rate + floatval( $value['rate'] );
				}

				$non_base_location_prices = true === ( $tax_rates !== $base_tax_rates && apply_filters( 'woocommerce_adjust_non_base_location_prices', true ) ) ? '1' : '0';

				$precision    = wc_get_rounding_precision();
				$price_of_one = 1 * ( pow( 10, $precision ) );

				if ( $non_base_location_prices ) {
					$prices_include_tax = true;
				} else {
					$prices_include_tax = wc_prices_include_tax();
				}

				$taxes_of_one        = array_sum( WC_Tax::calc_tax( $price_of_one, $tax_rates, wc_prices_include_tax() ) );
				$base_taxes_of_one   = array_sum( WC_Tax::calc_tax( $price_of_one, $base_tax_rates, true ) );
				$modded_taxes_of_one = array_sum( WC_Tax::calc_tax( $price_of_one - $base_taxes_of_one, $tax_rates, false ) );

				$taxes_of_one        = $taxes_of_one / ( pow( 10, $precision ) );
				$base_taxes_of_one   = $base_taxes_of_one / ( pow( 10, $precision ) );
				$modded_taxes_of_one = $modded_taxes_of_one / ( pow( 10, $precision ) );
			}

			$args['tc_tax_rate']                 = $tax_rate;
			$args['tc_is_taxable']               = $variation->is_taxable();
			$args['tc_base_tax_rate']            = $base_tax_rate;
			$args['tc_base_taxes_of_one']        = $base_taxes_of_one;
			$args['tc_taxes_of_one']             = $taxes_of_one;
			$args['tc_modded_taxes_of_one']      = $modded_taxes_of_one;
			$args['tc_non_base_location_prices'] = $non_base_location_prices;
			$args['tc_is_on_sale']               = $variation->is_on_sale();
			if ( isset( $_REQUEST['discount_type'] ) && isset( $_REQUEST['discount'] ) && ! empty( $_REQUEST['discount'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$current_price = THEMECOMPLETE_EPO_ASSOCIATED_PRODUCTS()->get_discounted_price( $variation->get_price(), sanitize_text_field( wp_unslash( $_REQUEST['discount'] ) ), sanitize_text_field( wp_unslash( $_REQUEST['discount_type'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$variation->set_sale_price( $current_price );
				$variation->set_price( $current_price );

				// See if prices should be shown for each variation after selection.
				$show_variation_price = apply_filters( 'woocommerce_show_variation_price', $variation->get_price() === '' || $class_obj->get_variation_sale_price( 'min' ) !== $class_obj->get_variation_sale_price( 'max' ) || $class_obj->get_variation_regular_price( 'min' ) !== $class_obj->get_variation_regular_price( 'max' ), $class_obj, $variation );

				$args['display_price'] = wc_get_price_to_display( $variation );
				$args['price_html']    = $show_variation_price ? '<span class="price">' . $variation->get_price_html() . '</span>' : '';
			}
		}

		return $args;
	}

	/**
	 * This loads only on quick view modules
	 * and on the ajax request of composite bundles
	 * and it is required in order to show custom styles
	 *
	 * @return void
	 * @since 4.8
	 */
	public function tm_add_inline_style_qv() {
		if ( ! empty( $this->inline_styles ) ) {
			global $wp_scripts;
			$wp_scripts = new WP_Scripts(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
			$this->tm_add_inline_style_reg();
			wp_print_footer_scripts();
		}
	}

	/**
	 * Register inline styles
	 *
	 * @return void
	 * @since 4.8
	 */
	public function tm_add_inline_style_reg() {
		if ( ! empty( $this->inline_styles ) ) {
			wp_register_style( 'themecomplete-styles-footer', false, [], THEMECOMPLETE_EPO_VERSION );
			wp_add_inline_style( 'themecomplete-styles-footer', $this->inline_styles );
			wp_enqueue_style( 'themecomplete-styles-footer' );
			$this->inline_styles = '';
		}
	}

	/**
	 * Handles any extra styling associated with the fields
	 *
	 * @return void
	 * @since 4.8
	 */
	public function tm_add_inline_style() {
		if ( ! empty( $this->inline_styles ) ) {
			if ( THEMECOMPLETE_EPO()->is_quick_view() || ( wp_doing_ajax() && ( THEMECOMPLETE_EPO()->is_bto || THEMECOMPLETE_EPO()->is_inline_epo ) ) ) {
				$this->tm_add_inline_style_qv();
			} else {
				$this->tm_add_inline_style_reg();
			}
		}
	}

	/**
	 * Handles any extra styling associated with the fields
	 *
	 * @return void
	 * @param string $css_string CSS code to add.
	 * @since 4.8.5
	 */
	public function add_inline_style( $css_string = '' ) {
		$this->inline_styles = $this->inline_styles . $css_string;
	}

	/**
	 * Add custom inline css
	 * Used to hide the native variations
	 *
	 * @param integer $output If the result should be displayed or retuned.
	 * @param integer $product_id The product id.
	 * @return void
	 * @since 1.0
	 */
	public function tm_variation_css_check( $output = 0, $product_id = 0 ) {
		if ( ! is_product() ) {
			return;
		}

		$post_id = get_the_ID();

		if ( $product_id && $product_id !== $post_id ) {
			$post_id = $product_id;
		}

		$post_id = floatval( $post_id );

		$original_product_id = absint( THEMECOMPLETE_EPO_WPML()->get_original_id( $post_id, 'product' ) );

		if ( $original_product_id !== $post_id ) {
			$post_id = $original_product_id;
		}

		$has_epo = THEMECOMPLETE_EPO_API()->has_options( $post_id );

		if ( false !== $has_epo && is_array( $has_epo ) ) {
			$row_gap          = THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_row_gap' );
			$column_gap       = THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_column_gap' );
			$inner_row_gap    = THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_row_inner_gap' );
			$inner_column_gap = THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_column_inner_gap' );

			$css_string = ':root {';

			if ( '' !== $row_gap ) {
				$row_gap     = '15px';
				$css_string .= '--tcgaprow: ' . $row_gap . ';';
			}

			if ( '' !== $column_gap ) {
				$column_gap  = '15px';
				$css_string .= '--tcgapcolumn: ' . $column_gap . ';';
			}

			if ( '' !== $inner_row_gap ) {
				$inner_row_gap = 'calc(var(--tcgaprow) / 2)';
				$css_string   .= '--tcinnergaprow: ' . $inner_row_gap . ';';
			}

			if ( '' !== $inner_column_gap ) {
				$inner_column_gap = 'calc(var(--tcgapcolumn) / 2)';
				$css_string      .= '--tcinnergapcolumn: ' . $inner_column_gap . ';';
			}

			$css_string .= '}';

			// $tcgap      = $row_gap . ' ' . $column_gap;
			// $tcinnergap = $inner_row_gap . ' ' . $inner_column_gap;

			// $css_string .= ':root {--tcgap: ' . $tcgap . ';--tcinnergap: ' . $tcinnergap . ';}';

			$this->inline_styles_head = $this->inline_styles_head . $css_string;
			if ( isset( $has_epo['variations'] ) && true === $has_epo['variations'] && empty( $has_epo['variations_disabled'] ) ) {
				if ( $product_id ) {
					$css_string = '#product-' . $product_id . ' form .variations,.post-' . $product_id . ' form .variations {display:none;}';
				} else {
					$css_string = 'form .variations{display:none;}';
				}

				$this->inline_styles_head = $this->inline_styles_head . $css_string;
			}
			if ( $output ) {
				$this->tm_variation_css_check_do();
			} else {
				add_action( 'wp_enqueue_scripts', [ $this, 'tm_variation_css_check_do' ], 6 );
			}
		}
	}

	/**
	 * Print inline css
	 *
	 * @return void
	 * @see tm_variation_css_check
	 * @since 1.0
	 */
	public function tm_variation_css_check_do() {
		if ( ! empty( $this->inline_styles_head ) ) {
			wp_register_style( 'themecomplete-styles-header', false, [], THEMECOMPLETE_EPO_VERSION );
			wp_add_inline_style( 'themecomplete-styles-header', $this->inline_styles_head );
			wp_enqueue_style( 'themecomplete-styles-header' );
		}
	}

	/**
	 * Internal variables
	 *
	 * @param integer|false $product_id The product id.
	 * @return void
	 * @since 1.0
	 */
	public function woocommerce_before_add_to_cart_button( $product_id = false ) {
		if ( $product_id ) {
			$pid = $product_id;
		} else {
			global $product;
			$pid = themecomplete_get_id( $product );
		}

		$print_inputs = false;
		if ( 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_enable_final_total_box_all' ) ) {
			$print_inputs = true;
		} else {
			$has_epo = THEMECOMPLETE_EPO_API()->has_options( $pid );
			if ( false !== $has_epo && is_array( $has_epo ) ) {
				$print_inputs = true;
			}
		}

		if ( $print_inputs ) {
			++$this->epo_id;
			echo '<input type="hidden" class="tm-epo-counter" name="tm-epo-counter" value="' . esc_attr( (string) $this->epo_id ) . '">';
			if ( ! empty( $pid ) ) {
				echo '<input type="hidden" data-epo-id="' . esc_attr( (string) $this->epo_id ) . '" class="tc-add-to-cart" name="tcaddtocart" value="' . esc_attr( (string) $pid ) . '">';
			}
		}
	}

	/**
	 * Ensures the correct display order of options when multiple products are displayed
	 *
	 * @return void
	 * @since 1.0
	 */
	public function woocommerce_before_single_product() {
		global $woocommerce;
		if ( ! property_exists( $woocommerce, 'product_factory' ) || null === $woocommerce->product_factory ) {
			return;// bad function call.
		}
		global $product;
		if ( $product ) {
			if ( ! is_product() ) {
				$this->tm_variation_css_check( 1, themecomplete_get_id( $product ) );
			}
			$this->current_product_id_to_be_displayed = themecomplete_get_id( $product );
			$this->current_product_id_to_be_displayed_check[ 'tc-' . count( $this->current_product_id_to_be_displayed_check ) . '-' . $this->current_product_id_to_be_displayed ] = $this->current_product_id_to_be_displayed;
		}
	}

	/**
	 * Ensures the correct display order of options when multiple products are displayed
	 *
	 * @return void
	 * @since 1.0
	 */
	public function woocommerce_after_single_product() {
		$this->current_product_id_to_be_displayed = 0;
		$this->unique_form_prefix                 = '';
	}

	/**
	 * Handles the display of all the extra options on the product page.
	 *
	 * IMPORTANT:
	 * We do not support plugins that pollute the global $woocommerce.
	 *
	 * @param integer $product_id The product id.
	 * @param string  $form_prefix The form prefix.
	 * @param boolean $dummy_prefix If we should use the form prefix.
	 * @return void
	 */
	public function frontend_display( $product_id = 0, $form_prefix = '', $dummy_prefix = false ) {
		if ( $this->block_epo ) {
			return;
		}

		global $product, $woocommerce;
		if ( ! property_exists( $woocommerce, 'product_factory' )
			|| null === $woocommerce->product_factory
			|| ( $this->tm_options_have_been_displayed && ( ! ( THEMECOMPLETE_EPO()->is_bto || ( THEMECOMPLETE_EPO()->is_enabled_shortcodes() && ! is_product() ) || ( ( is_shop() || is_product_category() || is_product_tag() ) && 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_enable_in_shop' ) ) ) ) )
		) {
			return;// bad function call.
		}

		$this->tm_epo_fields( $product_id, $form_prefix, false, $dummy_prefix );
		$this->tm_add_inline_style();
		$this->tm_epo_totals( $product_id, $form_prefix );
		if ( ! THEMECOMPLETE_EPO()->is_bto ) {
			$this->tm_options_have_been_displayed = true;
		}
	}

	/**
	 * Batch add plugin options
	 *
	 * @param string  $form_prefix The form prefix.
	 * @param boolean $dummy_prefix If we should use the form prefix.
	 * @return void
	 */
	private function tm_epo_fields_batch( $form_prefix = '', $dummy_prefix = false ) {
		foreach ( $this->current_product_id_to_be_displayed_check as $key => $product_id ) {
			if ( ! empty( $product_id ) ) {
				$this->inline_styles      = '';
				$this->inline_styles_head = '';

				$this->tm_variation_css_check( 1, $product_id );

				$this->tm_epo_fields( $product_id, $form_prefix, false, $dummy_prefix );
				$this->tm_add_inline_style();

				if ( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_options_placement' ) === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_totals_box_placement' ) ) {
					$this->tm_epo_totals( $product_id, $form_prefix );
				} elseif ( ! ( THEMECOMPLETE_EPO()->is_bto || THEMECOMPLETE_EPO()->is_inline_epo ) ) {
					unset( $this->epo_internal_counter_check[ 'tc' . $this->epo_internal_counter ] );
				}
			}
		}
		if ( ! ( THEMECOMPLETE_EPO()->is_bto || THEMECOMPLETE_EPO()->is_inline_epo ) ) {
			if ( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_options_placement' ) !== THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_totals_box_placement' ) ) {
				$this->epo_internal_counter       = 0;
				$this->epo_internal_counter_check = [];
			}
		}
	}

	/**
	 * Display the options in the frontend
	 *
	 * @param mixed   $product_id The product id.
	 * @param string  $form_prefix The form prefix.
	 * @param boolean $is_from_shortcode If we are in a shortcode.
	 * @param boolean $dummy_prefix If we should use the form prefix.
	 * @return void
	 */
	public function tm_epo_fields( $product_id = 0, $form_prefix = '', $is_from_shortcode = false, $dummy_prefix = false ) {
		if ( $this->block_epo ) {
			return;
		}

		global $woocommerce;

		if ( ! empty( $GLOBALS['THEMECOMPLETE_IS_FROM_SHORTCODE'] ) ) {
			$is_from_shortcode = true;
		}

		if ( ! property_exists( $woocommerce, 'product_factory' )
			|| null === $woocommerce->product_factory
			|| ( $this->tm_options_have_been_displayed && ( ! ( THEMECOMPLETE_EPO()->is_bto || ( ( THEMECOMPLETE_EPO()->is_enabled_shortcodes() && ! $is_from_shortcode ) && ! is_product() ) || ( ( is_shop() || is_product_category() || is_product_tag() ) && 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_enable_in_shop' ) ) ) ) )
		) {
			return;// bad function call.
		}

		if ( $product_id instanceof WC_Product ) {
			$product    = $product_id;
			$product_id = themecomplete_get_id( $product );
		} elseif ( ! $product_id ) {
			global $product;
			if ( $product ) {
				$product_id = themecomplete_get_id( $product );
			}
		} else {
			$product = wc_get_product( $product_id );
		}

		if ( ! $product_id || empty( $product ) ) {
			if ( ! empty( $this->current_product_id_to_be_displayed ) ) {
				$product_id = $this->current_product_id_to_be_displayed;
				$product    = wc_get_product( $product_id );
			} else {
				$this->tm_epo_fields_batch( $form_prefix, $dummy_prefix );

				return;
			}
		}

		if ( ! $product_id || empty( $product ) ) {
			return;
		}

		$type = themecomplete_get_product_type( $product );

		if ( 'grouped' === $type ) {
			return;
		}

		// Always dispay composite hidden fields if product is composite.
		if ( $form_prefix ) {
			$_bto_id     = $form_prefix;
			$form_prefix = '_' . $form_prefix;
			if ( THEMECOMPLETE_EPO()->is_bto ) {
				echo '<input type="hidden" class="cpf-bto-id" name="cpf_bto_id[]" value="' . esc_attr( $form_prefix ) . '">';
				echo '<input type="hidden" value="" name="cpf_bto_price[' . esc_attr( $_bto_id ) . ']" class="cpf-bto-price">';
				echo '<input type="hidden" value="0" name="cpf_bto_optionsprice[]" class="cpf-bto-optionsprice">';
			}
		}

		if ( ! $form_prefix ) {
			if ( THEMECOMPLETE_EPO()->is_quick_view() ) {
				if ( ! $this->unique_form_prefix ) {
					$this->unique_form_prefix = uniqid( '' );
				}
				$form_prefix = '_tcform' . $this->unique_form_prefix;
			} elseif ( THEMECOMPLETE_EPO()->wc_vars['is_page'] ) {
				// Workaroung to cover options in pages.
				if ( ! ( THEMECOMPLETE_EPO()->is_bto || THEMECOMPLETE_EPO()->is_inline_epo ) ) {
					$temp_this_epo_internal_counter = $this->epo_internal_counter;
					if ( empty( $temp_this_epo_internal_counter ) || ! isset( $this->epo_internal_counter_check[ 'tc' . $temp_this_epo_internal_counter ] ) ) {
						// First time displaying the fields and totals haven't been displayed.
						++$temp_this_epo_internal_counter;
					}
					$temp_epo_internal_counter = $temp_this_epo_internal_counter;
				} else {
					$temp_epo_internal_counter = 0;
				}

				$form_prefix = '_tcform' . $temp_epo_internal_counter;
			}
		}

		$post_id = $product_id;

		$cpf_price_array = THEMECOMPLETE_EPO()->get_product_tm_epos( $post_id, $form_prefix );

		if ( ! $cpf_price_array ) {
			return;
		}
		$global_price_array = $cpf_price_array['global'];
		$local_price_array  = $cpf_price_array['local'];

		if ( empty( $global_price_array ) && empty( $local_price_array ) ) {
			if ( ! ( THEMECOMPLETE_EPO()->is_bto || THEMECOMPLETE_EPO()->is_inline_epo ) ) {
				if ( empty( $this->epo_internal_counter ) || ! isset( $this->epo_internal_counter_check[ 'tc' . $this->epo_internal_counter ] ) ) {
					// First time displaying the fields and totals haven't been displayed.
					++$this->epo_internal_counter;
					$this->epo_internal_counter_check[ 'tc' . $this->epo_internal_counter ] = $this->epo_internal_counter;
				} else {
					// Totals have already been displayed.
					unset( $this->epo_internal_counter_check[ 'tc' . $this->epo_internal_counter ] );

					$this->current_product_id_to_be_displayed = 0;
					$this->unique_form_prefix                 = '';
				}
				$_epo_internal_counter = $this->epo_internal_counter;
			} else {
				$_epo_internal_counter = 0;
			}

			return;
		}

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

		$tabindex          = 0;
		$_currency         = get_woocommerce_currency_symbol();
		$unit_counter      = 0;
		$field_counter     = 0;
		$element_counter   = 0;
		$d_element_counter = 0;
		$connectors        = [];

		if ( ! ( THEMECOMPLETE_EPO()->is_bto || THEMECOMPLETE_EPO()->is_inline_epo ) ) {
			if ( empty( $this->epo_internal_counter ) || ! isset( $this->epo_internal_counter_check[ 'tc' . $this->epo_internal_counter ] ) ) {
				// First time displaying the fields and totals haven't been displayed.
				++$this->epo_internal_counter;
				$this->epo_internal_counter_check[ 'tc' . $this->epo_internal_counter ] = $this->epo_internal_counter;
			} else {
				// Totals have already been displayed.
				unset( $this->epo_internal_counter_check[ 'tc' . $this->epo_internal_counter ] );

				$this->current_product_id_to_be_displayed = 0;
				$this->unique_form_prefix                 = '';
			}
			$_epo_internal_counter = $this->epo_internal_counter;
		} elseif ( THEMECOMPLETE_EPO()->is_inline_epo && $this->epo_internal_counter ) {
			$_epo_internal_counter = $this->epo_internal_counter;
		} else {
			$_epo_internal_counter = 0;
		}

		$forcart   = 'main';
		$classcart = 'tm-cart-main';
		if ( ! empty( $form_prefix ) ) {
			$forcart   = $form_prefix;
			$classcart = 'tm-cart-' . str_replace( '_', '', $form_prefix );
		}
		$isfromshortcode = '';
		if ( ! empty( $is_from_shortcode ) ) {
			$isfromshortcode = ' tc-shortcode';
		}

		global $wp_filter;
		$saved_filter = false;
		if ( isset( $wp_filter['image_downsize'] ) ) {
			$saved_filter = $wp_filter['image_downsize'];
			unset( $wp_filter['image_downsize'] );
		}

		wc_get_template(
			'tm-start.php',
			[
				'isfromshortcode'      => $isfromshortcode,
				'classcart'            => $classcart,
				'forcart'              => $forcart,
				'form_prefix'          => str_replace( '_', '', $form_prefix ),
				'product_id'           => $product_id,
				'epo_internal_counter' => $_epo_internal_counter,
				'is_from_shortcode'    => $is_from_shortcode,
			],
			$this->get_template_path(),
			$this->get_default_path()
		);

		if ( ( ! empty( THEMECOMPLETE_EPO()->cart_edit_key ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'tm-edit' ) ) || ( ! empty( THEMECOMPLETE_EPO()->cart_edit_key ) && isset( $_REQUEST['update-composite'] ) ) ) {
			if ( isset( WC()->cart->cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key ] ) ) {
				if ( ! empty( WC()->cart->cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key ]['tmdata'] ) ) {
					if ( ! empty( WC()->cart->cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key ]['tmdata']['tmcp_post_fields'] ) && is_array( WC()->cart->cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key ]['tmdata']['tmcp_post_fields'] ) ) {
						$tmcp_post_fields  = WC()->cart->cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key ]['tmdata']['tmcp_post_fields'];
						$saved_form_prefix = WC()->cart->cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key ]['tmdata']['form_prefix'];
						foreach ( $tmcp_post_fields as $posted_name => $posted_value ) {
							if ( '' !== $saved_form_prefix ) {
								$posted_name = str_replace( $saved_form_prefix, '', $posted_name );
							}
							$_REQUEST[ $posted_name ] = $posted_value;
						}
					} else {
						$_REQUEST['tc-compatibilty-request-edit'] = 1;
					}
				}
			}
		}

		// Global options before local.
		foreach ( $global_prices['before'] as $prio => $priorities ) {
			foreach ( $priorities as $gid => $field ) {
				$args    = [
					'tabindex'          => $tabindex,
					'unit_counter'      => $unit_counter,
					'field_counter'     => $field_counter,
					'element_counter'   => $element_counter,
					'd_element_counter' => $d_element_counter,
					'_currency'         => $_currency,
					'product_id'        => $product_id,
					'connectors'        => $connectors,
					'gid'               => $gid,
				];
				$_return = $this->get_builder_display( $post_id, $field, 'before', $args, $form_prefix, $dummy_prefix );

				$tabindex          = $_return['tabindex'];
				$unit_counter      = $_return['unit_counter'];
				$field_counter     = $_return['field_counter'];
				$element_counter   = $_return['element_counter'];
				$d_element_counter = $_return['d_element_counter'];
				$_currency         = $_return['_currency'];
				$connectors        = $_return['connectors'];
			}
		}

		$args    = [
			'tabindex'          => $tabindex,
			'unit_counter'      => $unit_counter,
			'field_counter'     => $field_counter,
			'element_counter'   => $element_counter,
			'd_element_counter' => $d_element_counter,
			'_currency'         => $_currency,
			'product_id'        => $product_id,
		];
		$_return = $this->get_normal_display( $local_price_array, $args, $form_prefix, $dummy_prefix );

		$tabindex        = $_return['tabindex'];
		$unit_counter    = $_return['unit_counter'];
		$field_counter   = $_return['field_counter'];
		$element_counter = $_return['element_counter'];
		$_currency       = $_return['_currency'];

		// Global options after local.
		foreach ( $global_prices['after'] as $priorities ) {
			foreach ( $priorities as $gid => $field ) {
				$args    = [
					'tabindex'          => $tabindex,
					'unit_counter'      => $unit_counter,
					'field_counter'     => $field_counter,
					'element_counter'   => $element_counter,
					'd_element_counter' => $d_element_counter,
					'_currency'         => $_currency,
					'product_id'        => $product_id,
					'connectors'        => $connectors,
					'gid'               => $gid,
				];
				$_return = $this->get_builder_display( $post_id, $field, 'after', $args, $form_prefix, $dummy_prefix );

				$tabindex          = $_return['tabindex'];
				$unit_counter      = $_return['unit_counter'];
				$field_counter     = $_return['field_counter'];
				$element_counter   = $_return['element_counter'];
				$d_element_counter = $_return['d_element_counter'];
				$_currency         = $_return['_currency'];
				$connectors        = $_return['connectors'];
			}
		}

		wc_get_template(
			'tm-end.php',
			[],
			$this->get_template_path(),
			$this->get_default_path()
		);

		if ( $saved_filter ) {
			$wp_filter['image_downsize'] = $saved_filter; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		}

		$this->tm_options_single_have_been_displayed = true;
	}

	/**
	 * Displays the option created from the builder mode
	 *
	 * @param integer      $post_id The post id.
	 * @param array<mixed> $field The field options array.
	 * @param string       $where The field placement 'before' or 'after'.
	 * @param array<mixed> $vars The variable arguemnts.
	 * @param string       $form_prefix The form prefix.
	 * @param boolean      $dummy_prefix If we should use the form prefix.
	 * @return array<mixed>
	 * @since 1.0
	 */
	public function get_builder_display( $post_id, $field, $where, $vars, $form_prefix = '', $dummy_prefix = false ) {
		if ( ! $post_id ) {
			$post_id = 0;
		}
		$columns = [];
		for ( $x = 1; $x <= 100; $x++ ) {
			$columns[ 'w' . $x ] = [ 'tcwidth tcwidth-' . $x, $x ];
		}
		$columns['w12-5'] = [ 'tcwidth tcwidth-12-5', 12.5 ];
		$columns['w37-5'] = [ 'tcwidth tcwidth-37-5', 37.5 ];
		$columns['w62-5'] = [ 'tcwidth tcwidth-62-5', 62.5 ];
		$columns['w87-5'] = [ 'tcwidth tcwidth-87-5', 87.5 ];

		$tabindex          = $vars['tabindex'];
		$unit_counter      = $vars['unit_counter'];
		$field_counter     = $vars['field_counter'];
		$element_counter   = $vars['element_counter'];
		$d_element_counter = $vars['d_element_counter'];
		$_currency         = $vars['_currency'];
		$product_id        = $vars['product_id'];
		$connectors        = $vars['connectors'];
		$gid               = isset( $vars['gid'] ) ? $vars['gid'] : '0';

		$element_type_counter = [];

		if ( isset( $field['sections'] ) && is_array( $field['sections'] ) ) {

			$vars = [
				'field_id' => 'tc-epo-form-' . $gid . '-' . $unit_counter,
			];
			wc_get_template(
				'tm-builder-start.php',
				$vars,
				$this->get_template_path(),
				$this->get_default_path()
			);

			$_section_totals = 0;

			foreach ( $field['sections'] as $section ) {
				if ( ! isset( $section['sections_placement'] ) || $section['sections_placement'] !== $where ) {
					continue;
				}
				if ( isset( $section['sections_size'] ) && isset( $columns[ $section['sections_size'] ] ) ) {
					$size = $columns[ $section['sections_size'] ][0];
				} else {
					$size = 'tcwidth tcwidth-100';
				}

				$_section_totals = $_section_totals + $columns[ $section['sections_size'] ][1];
				if ( $_section_totals > 100 ) {
					$_section_totals = $columns[ $section['sections_size'] ][1];
				}

				$divider = isset( $section['divider_type'] ) ? $section['divider_type'] : '';

				$label_size = 'h3';
				if ( ! empty( $section['label_size'] ) ) {
					switch ( $section['label_size'] ) {
						case '1':
							$label_size = 'h1';
							break;
						case '2':
							$label_size = 'h2';
							break;
						case '3':
							$label_size = 'h3';
							break;
						case '4':
							$label_size = 'h4';
							break;
						case '5':
							$label_size = 'h5';
							break;
						case '6':
							$label_size = 'h6';
							break;
						case '7':
							$label_size = 'p';
							break;
						case '8':
							$label_size = 'div';
							break;
						case '9':
							$label_size = 'span';
							break;
					}
				}

				$section_args = [
					'column'                       => $size,
					'style'                        => $section['sections_style'],
					'uniqid'                       => $section['sections_uniqid'],
					'logic'                        => wp_json_encode( (array) json_decode( wp_unslash( $section['sections_logicrules'] ) ) ),
					'haslogic'                     => $section['sections_logic'],
					'sections_class'               => $section['sections_class'],
					'sections_type'                => $section['sections_type'],
					'sections_popupbutton'         => $section['sections_popupbutton'],
					'sections_popupbuttontext'     => $section['sections_popupbuttontext'],
					'sections_background_color'    => $section['sections_background_color'],
					'label_background_color'       => $section['label_background_color'],
					'description_background_color' => $section['description_background_color'],
					'label_size'                   => $label_size,
					'label'                        => ! empty( $section['label'] ) ? $section['label'] : '',
					'label_color'                  => ! empty( $section['label_color'] ) ? $section['label_color'] : '',
					'label_position'               => ! empty( $section['label_position'] ) ? $section['label_position'] : '',
					'description'                  => ! empty( $section['description'] ) ? $section['description'] : '',
					'description_color'            => ! empty( $section['description_color'] ) ? $section['description_color'] : '',
					'description_position'         => ! empty( $section['description_position'] ) ? $section['description_position'] : '',
					'divider'                      => $divider,
				];

				$css                 = '';
				$labelbgclass        = '';
				$descriptionclass    = '';
				$sectionbgcolorclass = '';
				if ( ! empty( $section_args['label_color'] ) ) {
					$css .= '.tc-epo-label.color-' . esc_attr( themecomplete_sanitize_hex_color_no_hash( $section_args['label_color'] ) ) . '{color:' . esc_attr( themecomplete_sanitize_hex_color( $section_args['label_color'] ) ) . ';}';
				}
				if ( '' !== $section_args['style'] && ! empty( $section_args['label_background_color'] ) ) {
					$css          .= '.tc-epo-label.bgcolor-' . esc_attr( themecomplete_sanitize_hex_color_no_hash( $section_args['label_background_color'] ) ) . '{background:' . esc_attr( themecomplete_sanitize_hex_color( $section_args['label_background_color'] ) ) . ';}';
					$labelbgclass .= ' bgcolor-' . themecomplete_sanitize_hex_color_no_hash( $section_args['label_background_color'] );
				}
				if ( ! empty( $section_args['description_color'] ) ) {
					$css              .= '.tm-section-description.color-' . esc_attr( themecomplete_sanitize_hex_color_no_hash( $section_args['description_color'] ) ) . '{color:' . esc_attr( themecomplete_sanitize_hex_color( $section_args['description_color'] ) ) . ';}';
					$descriptionclass .= ' color-' . themecomplete_sanitize_hex_color_no_hash( $section_args['description_color'] );
				}
				if ( '' !== $section_args['style'] && ! empty( $section_args['description_background_color'] ) ) {
					$css              .= '.tm-section-description.bgcolor-' . esc_attr( themecomplete_sanitize_hex_color_no_hash( $section_args['description_background_color'] ) ) . '{background:' . esc_attr( themecomplete_sanitize_hex_color( $section_args['description_background_color'] ) ) . ';}';
					$descriptionclass .= ' bgcolor-' . themecomplete_sanitize_hex_color_no_hash( $section_args['description_background_color'] );
				}
				if ( '' !== $section_args['style'] && ! empty( $section_args['sections_background_color'] ) ) {
					$css                .= '.tm-collapse.bgcolor-' . esc_attr( themecomplete_sanitize_hex_color_no_hash( $section_args['sections_background_color'] ) ) . ',.tm-box.bgcolor-' . esc_attr( themecomplete_sanitize_hex_color_no_hash( $section_args['sections_background_color'] ) ) . '{background:' . esc_attr( themecomplete_sanitize_hex_color( $section_args['sections_background_color'] ) ) . ';}';
					$sectionbgcolorclass = 'bgcolor-' . themecomplete_sanitize_hex_color_no_hash( $section_args['sections_background_color'] );
				}
				$this->add_inline_style( $css );
				$section_args['labelbgclass']        = $labelbgclass;
				$section_args['descriptionclass']    = $descriptionclass;
				$section_args['sectionbgcolorclass'] = $sectionbgcolorclass;

				// Custom variations check.
				if (
					isset( $section['elements'] )
					&& is_array( $section['elements'] )
					&& isset( $section['elements'][0] )
					&& is_array( $section['elements'][0] )
					&& isset( $section['elements'][0]['type'] )
					&& 'variations' === $section['elements'][0]['type']
				) {
					if ( 'variation' === THEMECOMPLETE_EPO()->associated_type ) {
						continue;
					}
					$section_args['sections_class'] = $section_args['sections_class'] . ' tm-epo-variation-section tc-clearfix';

					if ( THEMECOMPLETE_EPO_WPML()->is_active() ) {
						$wpml_is_original_product = THEMECOMPLETE_EPO_WPML()->is_original_product( $post_id, 'product' );
					}
					if ( ( isset( $wpml_is_original_product ) && empty( $wpml_is_original_product ) ) ) {
						if (
							isset( $section['elements'][0]['original_builder'] ) &&
							isset( $section['elements'][0]['original_builder']['variations_disabled'] ) &&
							(int) 1 === (int) $section['elements'][0]['original_builder']['variations_disabled']
						) {
							$section_args['sections_class'] .= ' tm-hidden';
						}
					} elseif (
						isset( $section['elements'][0]['builder'] ) &&
						isset( $section['elements'][0]['builder']['variations_disabled'] ) &&
						(int) 1 === (int) $section['elements'][0]['builder']['variations_disabled']
					) {
						$section_args['sections_class'] .= ' tm-hidden';
					}
				}
				if ( '' !== $section_args['style'] ) {
					$section_args['label_position'] = '';
				}
				wc_get_template(
					'tm-builder-section-start.php',
					$section_args,
					$this->get_template_path(),
					$this->get_default_path()
				);

				if ( isset( $section['elements'] ) && is_array( $section['elements'] ) ) {
					$totals = 0;

					$slide_counter = 0;
					$use_slides    = false;
					$doing_slides  = false;
					if ( '' !== $section['sections_slides'] && ( 'slider' === $section['sections_type'] || 'tabs' === $section['sections_type'] ) ) {
						$sections_slides = explode( ',', $section['sections_slides'] );
						$use_slides      = true;

						if ( 'tabs' === $section['sections_type'] ) {
							$sections_tabs_labels = isset( $section['sections_tabs_labels'] ) ? $section['sections_tabs_labels'] : '';
							$sections_tabs_labels = json_decode( $sections_tabs_labels );

							if ( ! is_array( $sections_tabs_labels ) ) {
								$sections_slides = '';
								$use_slides      = false;
							} else {
								echo '<div class="tc-tabs"><div class="tc-tabs-wrap tcwidth tcwidth-100">';
								echo '<div class="tc-tab-headers tcwidth tcwidth-100">';
								foreach ( $sections_tabs_labels as $tab_index => $tab_label ) {
									echo '<div class="tc-tab-header tma-tab-label"><div tabindex="0" data-id="tc-tab-slide' . esc_attr( $tab_index ) . '" data-tab="tab' . esc_attr( $tab_index ) . '" class="tab-header' . ( 0 === $tab_index ? ' open' : '' ) . '"><span class="tab-header-label">';
									echo apply_filters( 'wc_epo_kses', wp_kses_post( $tab_label ), $tab_label, false ); // phpcs:ignore WordPress.Security.EscapeOutput
									echo '</span></div></div>';
								}
								echo '</div>';
								echo '<div class="tc-tab-content tcwidth tcwidth-100">';
							}
						} elseif ( 'slider' === $section['sections_type'] ) {
							echo '<div class="tc-slider-content">';
						}
					}

					foreach ( $section['elements'] as $element ) {

						$element = apply_filters( 'wc_epo_get_element_for_display', $element );

						$empty_rules = '';
						if ( isset( $element['rules_filtered'] ) ) {
							$empty_rules = wp_json_encode( ( $element['rules_filtered'] ) );
						}
						$empty_original_rules = '';
						if ( isset( $element['original_rules_filtered'] ) ) {
							$empty_original_rules = wp_json_encode( ( $element['original_rules_filtered'] ) );
						}
						if ( empty( $empty_original_rules ) ) {
							$empty_original_rules = '';
						}
						$empty_rules_type = '';
						if ( isset( $element['rules_type'] ) ) {
							$empty_rules_type = wp_json_encode( ( $element['rules_type'] ) );
						}
						if ( isset( $element['size'] ) && isset( $columns[ $element['size'] ] ) ) {
							$size = $columns[ $element['size'] ][0];
						} else {
							$size = 'tcwidth tcwidth-100';
						}
						$test_for_first_slide = false;
						if ( $use_slides && isset( $sections_slides[ $slide_counter ] ) ) {
							$sections_slides[ $slide_counter ] = (int) $sections_slides[ $slide_counter ];

							if ( $sections_slides[ $slide_counter ] > 0 && ! $doing_slides ) {
								echo '<div class="tc-tab-slide tc-tab-slide' . esc_attr( (string) $slide_counter ) . ' tc-row">';
								$doing_slides         = true;
								$test_for_first_slide = true;
							}
						}

						$cart_fee_name = THEMECOMPLETE_EPO()->cart_fee_name;
						$totals        = $totals + $columns[ $element['size'] ][1];
						if ( $totals > 100 && ! $test_for_first_slide ) {
							$totals = $columns[ $element['size'] ][1];
						}

						$repeater              = isset( $element['repeater'] ) ? $element['repeater'] : '';
						$repeater_quantity     = isset( $element['repeater_quantity'] ) ? $element['repeater_quantity'] : '';
						$repeater_min_rows     = isset( $element['repeater_min_rows'] ) ? $element['repeater_min_rows'] : '';
						$repeater_max_rows     = isset( $element['repeater_max_rows'] ) ? $element['repeater_max_rows'] : '';
						$repeater_button_label = isset( $element['repeater_button_label'] ) ? $element['repeater_button_label'] : '';

						$divider       = isset( $element['divider_type'] ) ? $element['divider_type'] : '';
						$divider_class = '';
						if ( isset( $element['divider_type'] ) ) {
							$divider_class = '';
							if ( 'divider' === $element['type'] && ! empty( $element['class'] ) ) {
								$divider_class = ' ' . $element['class'];
							}
						}
						$label_size = 'h3';
						if ( ! empty( $element['label_size'] ) ) {
							switch ( $element['label_size'] ) {
								case '1':
									$label_size = 'h1';
									break;
								case '2':
									$label_size = 'h2';
									break;
								case '3':
									$label_size = 'h3';
									break;
								case '4':
									$label_size = 'h4';
									break;
								case '5':
									$label_size = 'h5';
									break;
								case '6':
									$label_size = 'h6';
									break;
								case '7':
									$label_size = 'p';
									break;
								case '8':
									$label_size = 'div';
									break;
								case '9':
									$label_size = 'span';
									break;
								case '10':
									$label_size = 'label';
									break;
							}
						}

						$variations_builder_element_start_args = [];
						$tm_validation                         = $this->get_tm_validation_rules( $element );
						$args                                  = apply_filters(
							'wc_epo_builder_element_start_args',
							[
								'tm_element_settings'  => $element,
								'column'               => $size,
								'class'                => ! empty( $element['class'] ) ? $element['class'] : '',
								'container_id'         => ! empty( $element['container_id'] ) ? $element['container_id'] : '',
								'label_size'           => $label_size,
								'label'                => ! empty( $element['label'] ) ? $element['label'] : '',
								'label_position'       => ! empty( $element['label_position'] ) ? $element['label_position'] : '',
								'label_mode'           => ! empty( $element['label_mode'] ) ? $element['label_mode'] : '',
								'label_color'          => ! empty( $element['label_color'] ) ? $element['label_color'] : '',
								'description'          => ! empty( $element['description'] ) ? $element['description'] : '',
								'description_color'    => ! empty( $element['description_color'] ) ? $element['description_color'] : '',
								'description_position' => ! empty( $element['description_position'] ) ? $element['description_position'] : '',
								'divider'              => $divider,
								'divider_class'        => $divider_class,
								'required'             => $element['required'],
								'replacement_mode'     => $element['replacement_mode'],
								'swatch_position'      => $element['swatch_position'],
								'use_url'              => $element['use_url'],
								'enabled'              => $element['enabled'],
								'rules'                => $empty_rules,
								'original_rules'       => $empty_original_rules,
								'rules_type'           => $empty_rules_type,
								'element_type'         => $element['type'],
								'class_id'             => 'tm-element-ul-' . $element['type'] . ' element_' . $element_counter . $form_prefix, // this goes on ul.
								'uniqid'               => $element['uniqid'],
								'logic'                => wp_json_encode( (array) json_decode( wp_unslash( $element['logicrules'] ) ) ),
								'haslogic'             => $element['logic'],
								'clear_options'        => empty( $element['clear_options'] ) ? '' : $element['clear_options'],
								'limit'                => empty( $element['limit'] ) ? '' : 'tm-limit',
								'exactlimit'           => empty( $element['exactlimit'] ) ? '' : 'tm-exactlimit',
								'minimumlimit'         => empty( $element['minimumlimit'] ) ? '' : 'tm-minimumlimit',
								'tm_validation'        => wp_json_encode( ( $tm_validation ) ),
								'extra_class'          => '',
								'repeater'             => $repeater,
								'repeater_quantity'    => $repeater_quantity,
								'repeater_min_rows'    => $repeater_min_rows,
								'repeater_max_rows'    => $repeater_max_rows,
								'hide'                 => isset( $element['hide'] ) ? $element['hide'] : '',
								'operation_mode'       => isset( $element['mode'] ) ? $element['mode'] : '',
							],
							$element,
							$element_counter,
							$form_prefix
						);

						$fullwidth_mode = 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_select_fullwidth' );
						if ( 'auto' === $args['label_mode'] ) {
							$fullwidth_mode = false;
						} elseif ( 'fullwidth' === $args['label_mode'] ) {
							$fullwidth_mode = true;
						}
						if ( $fullwidth_mode ) {
							$args['class'] = ( empty( $args['class'] ) ? '' : $args['class'] . ' ' ) . 'fullwidth';
						}

						if ( 'product' === $element['type'] ) {
							$layout_mode = isset( $element['layout_mode'] ) ? $element['layout_mode'] : '';
							if ( 'product' === $element['mode'] ) {
								$layout_mode = 'single';
							}
							$disable_epo         = isset( $element['disable_epo'] ) ? $element['disable_epo'] : '';
							$args['extra_class'] = 'cpf-type-product-' . $layout_mode . ' cpf-type-product-mode-' . $element['mode'];
							if ( ! empty( $disable_epo ) ) {
								$args['extra_class'] .= ' no-epo';
							}
							$args['element_data_attr'] = [
								'data-mode'                => isset( $element['mode'] ) ? $element['mode'] : '',
								'data-product-layout-mode' => $layout_mode,
								'data-quantity-min'        => isset( $element['quantity_min'] ) ? $element['quantity_min'] : '',
								'data-quantity-max'        => isset( $element['quantity_max'] ) ? $element['quantity_max'] : '',
								'data-priced-individually' => isset( $element['priced_individually'] ) ? $element['priced_individually'] : '',
								'data-discount'            => isset( $element['discount'] ) ? $element['discount'] : '',
								'data-discount-type'       => isset( $element['discount_type'] ) ? $element['discount_type'] : '',
								'data-discount-exclude-addons' => isset( $element['discount_exclude_addons'] ) ? $element['discount_exclude_addons'] : '',
								'data-show-image'          => isset( $element['show_image'] ) ? $element['show_image'] : '1',
								'data-show-title'          => isset( $element['show_title'] ) ? $element['show_title'] : '1',
								'data-show-title-link'     => isset( $element['show_title_link'] ) ? $element['show_title_link'] : '1',
								'data-show-price'          => isset( $element['show_price'] ) ? $element['show_price'] : '1',
								'data-show-description'    => isset( $element['show_description'] ) ? $element['show_description'] : '1',
								'data-show-meta'           => isset( $element['show_meta'] ) ? $element['show_meta'] : '1',
								'data-disable-epo'         => $disable_epo,
							];
							if ( 'product' !== $element['mode'] ) {
								if ( 'radio' === $layout_mode || 'thumbnail' === $layout_mode ) {
									$args['clear_options'] = '1';
								}
							}
						}

						if ( 'variations' === $element['type'] ) {
							$variations_builder_element_start_args = $args;
						}

						if ( ( 'variations' !== $element['type'] && $element['enabled'] ) || 'variations' === $element['type'] ) {

							$field_counter = 0;

							$init_class = 'THEMECOMPLETE_EPO_FIELDS_' . $element['type'];
							if ( ! class_exists( $init_class ) && isset( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ] ) && ! empty( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]->is_addon ) ) {
								$init_class = 'THEMECOMPLETE_EPO_FIELDS';
							}

							if ( isset( THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ] )
								&& ( 'post' === THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]->is_post || 'dynamic' === THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]->is_post || 'display' === THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ]->is_post )
								&& class_exists( $init_class ) && method_exists( $init_class, 'display_field' )
							) {
								$element_object = THEMECOMPLETE_EPO()->tm_builder_elements[ $element['type'] ];

								if ( 'post' === $element_object->is_post ) {
									$c_element_counter = $d_element_counter;
									if ( isset( $element['connector'] ) && isset( $connectors[ 'c-' . sanitize_key( $element['connector'] ) ] ) ) {
										$c_element_counter = $connectors[ 'c-' . sanitize_key( $element['connector'] ) ]['element_counter'];
									}

									if ( 'single' === $element_object->type || 'multipleallsingle' === $element_object->type || 'multiplesingle' === $element_object->type || 'singlemultiple' === $element_object->type ) {

										$name_inc    = $element['raw_name_inc'] . ( $dummy_prefix ? '' : $element['raw_name_inc_prefix'] );
										$is_cart_fee = ! empty( $element['is_cart_fee'] );

										if ( ! isset( $element_type_counter[ $element['type'] ] ) ) {
											$element_type_counter[ $element['type'] ] = 0;
										}

										$posted_name = 'tmcp_' . $name_inc;

										if ( isset( $_REQUEST['tc-compatibilty-request-edit'] ) ) {
											$saved_form_prefix = WC()->cart->cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key ]['tmdata']['form_prefix'];

											$saved_epos = WC()->cart->cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key ]['tmcartepo'];
											foreach ( $saved_epos as $key => $val ) {
												if ( isset( $val['key'] ) ) {
													if ( $element['uniqid'] === $val['section'] ) {
														$_REQUEST[ $posted_name ] = $val['key'];
														if ( isset( $val['quantity'] ) ) {
															$_REQUEST[ $posted_name . '_quantity' ] = $val['quantity'];
														}
													}
												} elseif ( $element['uniqid'] === $val['section'] ) {
														$_REQUEST[ $posted_name ] = $val['value'];
													if ( isset( $val['quantity'] ) ) {
														$_REQUEST[ $posted_name . '_quantity' ] = $val['quantity'];
													}
												}
											}

											$saved_fees = WC()->cart->cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key ]['tmcartepo'];
											foreach ( $saved_fees as $key => $val ) {
												if ( isset( $val['key'] ) ) {
													if ( $element['uniqid'] === $val['section'] ) {
														$_REQUEST[ $posted_name ] = $val['key'];
														if ( isset( $val['quantity'] ) ) {
															$_REQUEST[ $posted_name . '_quantity' ] = $val['quantity'];
														}
													}
												} elseif ( $element['uniqid'] === $val['section'] ) {
														$_REQUEST[ $posted_name ] = $val['value'];
													if ( isset( $val['quantity'] ) ) {
														$_REQUEST[ $posted_name . '_quantity' ] = $val['quantity'];
													}
												}
											}
										}

										$get_posted_name = [ '' ];
										if ( isset( $_REQUEST[ $posted_name ] ) ) {
											$get_posted_name = map_deep( wp_unslash( $_REQUEST[ $posted_name ] ), 'sanitize_text_field' );
											if ( ! is_array( $get_posted_name ) ) {
												$get_posted_name = [ $get_posted_name ];
											}
										}

										if ( count( $get_posted_name ) > 1 && 'multipleallsingle' === $element_object->type ) {
											$get_posted_name = [ $get_posted_name[0] ];
										}

										do_action( 'wc_epo_get_builder_display_single', $element, $name_inc, null );

										$fieldtype = 'tmcp-field';
										if ( $is_cart_fee ) {
											$fieldtype = THEMECOMPLETE_EPO()->cart_fee_class;
										}
										if ( ! empty( $element['class'] ) ) {
											$fieldtype .= ' ' . $element['class'];
										}

										if ( THEMECOMPLETE_EPO()->get_element_price_type( '', $element, '0', true, 0 ) === 'math' ) {
											$fieldtype .= ' tc-is-math';
										}

										$uniqid_suffix = uniqid();

										$args['get_posted_key_count'] = count( $get_posted_name );

										foreach ( $get_posted_name as $get_posted_key => $get_posted_value ) {
											$html_name          = $posted_name . ( ! empty( $repeater ) ? '[' . $get_posted_key . ']' : '' );
											$html_quantity_name = $posted_name . '_quantity' . ( ! empty( $repeater ) ? '[' . $get_posted_key . ']' : '' );
											if ( 'singlemultiple' === $element_object->type ) {
												if ( empty( $repeater ) ) {
													$html_name          = $posted_name . '[0][]';
													$html_quantity_name = $posted_name . '_quantity[0]';
												} else {
													$html_name .= '[]';
												}
											}
											$args['get_posted_key'] = $get_posted_key;
											++$tabindex;
											$field_obj = new $init_class();
											$display   = $field_obj->display_field(
												$element,
												[
													'element_id' => 'tmcp_' . $element_object->post_name_prefix . '_' . $tabindex . $form_prefix . $uniqid_suffix,
													'get_posted_key' => $get_posted_key,
													'repeater' => $repeater,
													'name' => $html_name,
													'name_inc' => $name_inc,
													'posted_name' => $posted_name,
													'c_element_counter' => $c_element_counter,
													'element_counter' => $element_counter,
													'tabindex' => $tabindex,
													'form_prefix' => $form_prefix,
													'fieldtype' => $fieldtype,
													'field_counter' => $field_counter,
													'product_id' => isset( $product_id ) ? $product_id : 0,
												]
											);

											if ( is_array( $display ) ) {

												$original_amount = '';
												if ( isset( $element['original_rules_filtered'][0] ) && isset( $element['original_rules_filtered'][0][0] ) ) {
													$original_amount = $element['original_rules_filtered'][0][0];
												} else {
													if ( isset( $element['default_value'] ) && ! is_array( $element['default_value'] ) && '' !== $element['default_value'] ) {
														$selected_index = array_keys( $element['options'] );
														if ( isset( $selected_index[ $element['default_value'] ] ) ) {
															$selected_index = $selected_index[ $element['default_value'] ];
														} else {
															$selected_index = current( $selected_index );
														}
													} else {
														$selected_index = array_keys( $element['options'] );
														if ( ! empty( $selected_index ) ) {
															$selected_index = $selected_index[0];
														} else {
															$selected_index = false;
														}
													}
													if ( false === $selected_index ) {
														$original_amount = '';
													} else {
														$original_amount = $element['original_rules_filtered'][ esc_attr( (string) $selected_index ) ];
														if ( isset( $original_amount[0] ) ) {
															$original_amount = $original_amount[0];
														} else {
															$original_amount = '';
														}
													}
												}
												if ( isset( $display['default_value_counter'] ) && false !== $display['default_value_counter'] ) {
													$original_amount = $element['original_rules_filtered'][ $display['default_value_counter'] ][0];
												}

												$amount = '';
												if ( isset( $element['rules_filtered'][0] ) && isset( $element['rules_filtered'][0][0] ) ) {
													$amount = $element['rules_filtered'][0][0];
												} else {
													if ( isset( $element['default_value'] ) && ! is_array( $element['default_value'] ) && '' !== $element['default_value'] ) {
														$selected_index = array_keys( $element['options'] );
														if ( isset( $selected_index[ $element['default_value'] ] ) ) {
															$selected_index = $selected_index[ $element['default_value'] ];
														} else {
															$selected_index = current( $selected_index );
														}
													} else {
														$selected_index = array_keys( $element['options'] );
														if ( ! empty( $selected_index ) ) {
															$selected_index = $selected_index[0];
														} else {
															$selected_index = false;
														}
													}
													if ( false === $selected_index ) {
														$amount = '';
													} else {
														$amount = $element['rules_filtered'][ esc_attr( (string) $selected_index ) ];
														if ( isset( $amount[0] ) ) {
															$amount = $amount[0];
														} else {
															$amount = '';
														}
													}
												}
												if ( isset( $display['default_value_counter'] ) && false !== $display['default_value_counter'] ) {
													$amount = $element['rules_filtered'][ $display['default_value_counter'] ][0];
												}

												$original_rules = isset( $element['original_rules_filtered'] ) ? wp_json_encode( ( $element['original_rules_filtered'] ) ) : '';
												if ( empty( $original_rules ) ) {
													$original_rules = '';
												}

												$element_args = [
													'element_id' => 'tmcp_' . $element_object->post_name_prefix . '_' . $tabindex . $form_prefix . $uniqid_suffix,
													'name' => $html_name,
													'get_posted_key' => $get_posted_key,
													'posted_name' => $posted_name,
													'quantity_name' => $html_quantity_name,
													'amount' => '',
													'original_amount' => '',
													'required' => $element['required'],
													'tabindex' => $tabindex,
													'fieldtype' => $fieldtype,
													'rules' => isset( $element['rules_filtered'] ) ? wp_json_encode( ( $element['rules_filtered'] ) ) : '',
													'original_rules' => $original_rules,
													'rules_type' => isset( $element['rules_type'] ) ? wp_json_encode( ( $element['rules_type'] ) ) : '',
													'tm_element_settings' => $element,
													'class' => ! empty( $element['class'] ) ? $element['class'] : '',
													'field_counter' => $field_counter,
													'tax_obj' => ! $is_cart_fee ? false : wp_json_encode(
														( [
															'is_fee'    => $is_cart_fee,
															'has_fee'   => isset( $element['include_tax_for_fee_price_type'] ) ? $element['include_tax_for_fee_price_type'] : '',
															'tax_class' => isset( $element['tax_class_for_fee_price_type'] ) ? $element['tax_class_for_fee_price_type'] : '',
															'tax_rate'  => $this->get_tax_rate( isset( $element['tax_class_for_fee_price_type'] ) ? $element['tax_class_for_fee_price_type'] : '' ),
														] )
													),
												];

												if ( THEMECOMPLETE_EPO()->has_math_special_variable( $element_args['rules'], 'cumulative' ) ) {
													$element_args['fieldtype'] .= ' tc-is-math-cumulative';
												} elseif ( THEMECOMPLETE_EPO()->has_math_special_variable( $element_args['rules'], 'special' ) ) {
													$element_args['fieldtype'] .= ' tc-is-math-special';
												}

												$element_args         = apply_filters( 'wc_epo_display_template_args', array_merge( $element_args, $display ), $element, false, false, $element_type_counter[ $element['type'] ] );
												$element_args['args'] = $element_args;

												if ( 'variations' !== $element['type'] ) {
													if ( $element['enabled'] ) {
														wc_get_template(
															'tm-builder-element-start.php',
															$args,
															$this->get_template_path(),
															$this->get_default_path()
														);
													}
												}
												if ( $element_object->is_addon ) {
													do_action(
														'tm_epo_display_addons',
														$element,
														$element_args,
														[
															'name_inc'        => $name_inc,
															'c_element_counter' => $c_element_counter,
															'element_counter' => $element_counter,
															'tabindex'        => $tabindex,
															'form_prefix'     => $form_prefix,
															'field_counter'   => $field_counter,
														],
														$element_object->namespace
													);
												} elseif ( is_readable( apply_filters( 'wc_epo_template_path_element', THEMECOMPLETE_EPO_TEMPLATE_PATH, $element['type'], $element ) . apply_filters( 'wc_epo_template_element', 'tm-' . str_replace( '_', '-', $element['type'] ) . '.php', $element['type'], $element ) ) ) {
													wc_get_template(
														apply_filters( 'wc_epo_template_element', 'tm-' . str_replace( '_', '-', $element['type'] ) . '.php', $element['type'], $element ),
														$element_args,
														$this->get_template_path(),
														apply_filters( 'wc_epo_template_path_element', $this->get_default_path(), $element['type'], $element )
													);
												}
												if ( 'variations' !== $element['type'] ) {
													if ( $element['enabled'] ) {
														wc_get_template(
															'tm-builder-element-end.php',
															[
																'repeater' => $repeater,
																'repeater_quantity' => $repeater_quantity,
																'repeater_min_rows'    => $repeater_min_rows,
																'repeater_max_rows'    => $repeater_max_rows,
																'repeater_button_label' => $repeater_button_label,
																'get_posted_key' => $get_posted_key,
																'get_posted_key_count' => $args['get_posted_key_count'],
																'tm_element_settings' => $element,
																'element_type' => $element['type'],
																'enabled'     => $element['enabled'],
																'description' => ! empty( $element['description'] ) ? $element['description'] : '',
																'description_color' => ! empty( $element['description_color'] ) ? $element['description_color'] : '',
																'description_position' => ! empty( $element['description_position'] ) ? $element['description_position'] : '',
															],
															$this->get_template_path(),
															$this->get_default_path()
														);
													}
												}
											}
											unset( $field_obj );
										}

										++$element_type_counter[ $element['type'] ];

									} elseif ( 'multipleall' === $element_object->type || 'multiple' === $element_object->type ) {

										if ( ! isset( $element_type_counter[ $element['type'] ] ) ) {
											$element_type_counter[ $element['type'] ] = 0;
										}

										$get_posted_name = [];

										if ( 'multipleall' === $element_object->type ) {
											$_field_counter = 0;
											foreach ( $element['options'] as $value => $label ) {
												$name_inc    = $element['raw_name_inc'][ $_field_counter ] . ( $dummy_prefix ? '' : $element['raw_name_inc_prefix'][ $_field_counter ] );
												$is_cart_fee = ! empty( $element['is_cart_fee_multiple'][ $_field_counter ] );
												$posted_name = 'tmcp_' . $name_inc;
												if ( isset( $_REQUEST[ $posted_name ] ) && is_array( $_REQUEST[ $posted_name ] ) ) {
													$get_posted_name = array_replace( $get_posted_name, wp_unslash( $_REQUEST[ $posted_name ] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
													if ( ! is_array( $get_posted_name ) ) {
														$get_posted_name = [ $get_posted_name ];
													}
													end( $get_posted_name );
													$get_posted_max = key( $get_posted_name );
													if ( $get_posted_max >= count( $get_posted_name ) ) {
														$get_posted_name = $get_posted_name + array_diff_key( array_fill( 0, $get_posted_max, false ), $get_posted_name );
														ksort( $get_posted_name );
													}
												}
												++$_field_counter;
											}
										} else {
											$name_inc        = '';
											$posted_name     = '';
											$get_posted_name = [];
											if ( isset( $element['raw_name_inc'] ) ) {
												$name_inc    = $element['raw_name_inc'][0] . ( $dummy_prefix ? '' : $element['raw_name_inc_prefix'][0] );
												$is_cart_fee = ! empty( $element['is_cart_fee_multiple'][0] );
												$posted_name = 'tmcp_' . $name_inc;
												if ( isset( $_REQUEST[ $posted_name ] ) && is_array( $_REQUEST[ $posted_name ] ) ) {
													$get_posted_name = wp_unslash( $_REQUEST[ $posted_name ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
													if ( ! is_array( $get_posted_name ) ) {
														$get_posted_name = [ $get_posted_name ];
													}
													end( $get_posted_name );
													$get_posted_max = key( $get_posted_name );
													if ( $get_posted_max >= count( $get_posted_name ) ) {
														$get_posted_name = $get_posted_name + array_diff_key( array_fill( 0, $get_posted_max, false ), $get_posted_name );
														ksort( $get_posted_name );
													}
												}
											}
										}

										if ( empty( $get_posted_name ) ) {
											$get_posted_name = [ '' ];
										}

										$args['get_posted_key_count'] = count( $get_posted_name );

										foreach ( $get_posted_name as $get_posted_key => $get_posted_value ) {
											$field_counter          = 0;
											$args['get_posted_key'] = $get_posted_key;

											$field_obj = new $init_class();
											if ( method_exists( $field_obj, 'display_field_pre' ) ) {
												$field_obj->display_field_pre(
													$element,
													[
														'c_element_counter' => $c_element_counter,
														'element_counter' => $element_counter,
														'tabindex' => $tabindex,
														'form_prefix' => $form_prefix,
														'field_counter' => $field_counter,
														'product_id' => isset( $product_id ) ? $product_id : 0,
													]
												);
											}

											$args['field_obj'] = $field_obj;

											if ( 'variations' !== $element['type'] ) {
												if ( $element['enabled'] ) {
													wc_get_template(
														'tm-builder-element-start.php',
														$args,
														$this->get_template_path(),
														$this->get_default_path()
													);
												}
											}

											$choice_counter = 0;

											foreach ( $element['options'] as $value => $label ) {
												$connector_value = $value;
												if ( isset( $element['connector'] ) && isset( $connectors[ 'c-' . sanitize_key( $element['connector'] ) ] ) ) {
													$c_element_counter = $connectors[ 'c-' . sanitize_key( $element['connector'] ) ]['element_counter'];

													$underscore_position = strrpos( $value, '_' );
													if ( false !== $underscore_position ) {
														$first_part  = substr( $value, 0, $underscore_position );
														$second_part = absint( substr( $value, $underscore_position + 1 ) );
														$second_part = $second_part + absint( $connectors[ 'c-' . sanitize_key( $element['connector'] ) ]['choice_counter'] );
														$second_part = absint( $connectors[ 'c-' . sanitize_key( $element['connector'] ) ]['choice_counter'] );

														$connector_value = $value . '-' . $second_part;
													}
												}

												++$tabindex;

												$name_inc = $element['raw_name_inc'][ $field_counter ] . ( $dummy_prefix ? '' : $element['raw_name_inc_prefix'][ $field_counter ] );

												$is_cart_fee = ! empty( $element['is_cart_fee_multiple'][ $field_counter ] );

												$posted_name = 'tmcp_' . $name_inc;
												do_action( 'wc_epo_get_builder_display_single', $element, $name_inc, $value );

												$html_name          = $posted_name . ( ! empty( $repeater ) ? '[' . $get_posted_key . ']' : '' );
												$html_quantity_name = $posted_name . '_quantity' . ( ! empty( $repeater ) ? '[' . $get_posted_key . ']' : '' );

												if ( ! empty( THEMECOMPLETE_EPO()->cart_edit_key ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'tm-edit' ) ) {
													if ( isset( WC()->cart->cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key ] ) ) {
														if ( ! empty( WC()->cart->cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key ]['tmcartepo'] ) ) {
															$saved_epos = WC()->cart->cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key ]['tmcartepo'];
															foreach ( $saved_epos as $key => $val ) {
																if ( $element['uniqid'] === $val['section'] && (string) $value === (string) $val['key'] ) {
																	if ( isset( $val['quantity'] ) ) {
																		if ( ! empty( $repeater ) ) {
																			$_REQUEST[ $posted_name . '_quantity' ][ $get_posted_key ] = $val['quantity'];
																		} else {
																			$_REQUEST[ $posted_name . '_quantity' ] = $val['quantity'];
																			if ( isset( $_REQUEST['tc-compatibilty-request-edit'] ) ) {
																				$_REQUEST[ $posted_name ] = $val['key'];
																			}
																		}
																	}
																}
															}
														}
														if ( ! empty( WC()->cart->cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key ]['tmcartfee'] ) ) {
															$saved_fees = WC()->cart->cart_contents[ THEMECOMPLETE_EPO()->cart_edit_key ]['tmcartfee'];
															foreach ( $saved_fees as $key => $val ) {
																if ( $element['uniqid'] === $val['section'] && (string) $value === (string) $val['key'] ) {
																	if ( isset( $val['quantity'] ) ) {
																		if ( isset( $val['quantity'] ) ) {
																			if ( ! empty( $repeater ) ) {
																				$_REQUEST[ $posted_name . '_quantity' ][ $get_posted_key ] = $val['quantity'];
																			} else {
																				$_REQUEST[ $posted_name . '_quantity' ] = $val['quantity'];
																				if ( isset( $_REQUEST['tc-compatibilty-request-edit'] ) ) {
																					$_REQUEST[ $posted_name ] = $val['key'];
																				}
																			}
																		}
																	}
																}
															}
														}
													}
												}

												$fieldtype = 'tmcp-field';
												if ( $is_cart_fee ) {
													$fieldtype = THEMECOMPLETE_EPO()->cart_fee_class;
												}
												if ( ! empty( $element['class'] ) ) {
													$fieldtype .= ' ' . $element['class'];
												}

												if ( THEMECOMPLETE_EPO()->get_element_price_type( '', $element, $value, true, 0 ) === 'math' ) {
													$fieldtype .= ' tc-is-math';
												}

												$uniqid_suffix = uniqid();

												$display = $field_obj->display_field(
													$element,
													[
														'element_id' => 'tmcp_' . $element_object->post_name_prefix . '_' . $c_element_counter . '_' . $field_counter . '_' . $tabindex . $form_prefix . $uniqid_suffix,
														'get_posted_key' => $get_posted_key,
														'repeater' => $repeater,
														'name' => $html_name,
														'name_inc' => $name_inc,
														'posted_name' => $posted_name,
														'value' => $value,
														'connector_value' => $connector_value,
														'label' => $label,
														'c_element_counter' => $c_element_counter,
														'element_counter' => $element_counter,
														'tabindex' => $tabindex,
														'form_prefix' => $form_prefix,
														'fieldtype' => $fieldtype,
														'border_type' => THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_css_selected_border' ),
														'field_counter' => $field_counter,
														'product_id' => isset( $product_id ) ? $product_id : 0,
													]
												);

												if ( is_array( $display ) ) {

													$original_amount = $element['original_rules_filtered'][ $value ][0];

													$amount = $element['rules_filtered'][ $value ][0];

													$original_rules = isset( $element['original_rules_filtered'][ $value ] ) ? wp_json_encode( ( $element['original_rules_filtered'][ $value ] ) ) : '';
													if ( empty( $original_rules ) ) {
														$original_rules = '';
													}

													$element_args = [
														'element_id' => 'tmcp_' . $element_object->post_name_prefix . '_' . $c_element_counter . '_' . $field_counter . '_' . $tabindex . $form_prefix . $uniqid_suffix,
														'name' => $html_name,
														'get_posted_key' => $get_posted_key,
														'posted_name' => $posted_name,
														'quantity_name' => $html_quantity_name,
														'amount' => '',
														'original_amount' => '',
														'required' => $element['required'],
														'tabindex' => $tabindex,
														'fieldtype' => $fieldtype,
														'rules' => isset( $element['rules_filtered'][ $value ] ) ? wp_json_encode( ( $element['rules_filtered'][ $value ] ) ) : '',
														'original_rules' => $original_rules,
														'rules_type' => isset( $element['rules_type'][ $value ] ) ? wp_json_encode( ( $element['rules_type'][ $value ] ) ) : '',
														'border_type' => THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_css_selected_border' ),
														'tm_element_settings' => $element,
														'class' => ! empty( $element['class'] ) ? $element['class'] : '',
														'field_counter' => $field_counter,
														'tax_obj' => ! $is_cart_fee ? false : wp_json_encode(
															( [
																'is_fee'    => $is_cart_fee,
																'has_fee'   => isset( $element['include_tax_for_fee_price_type'] ) ? $element['include_tax_for_fee_price_type'] : '',
																'tax_class' => isset( $element['tax_class_for_fee_price_type'] ) ? $element['tax_class_for_fee_price_type'] : '',
																'tax_rate'  => $this->get_tax_rate( isset( $element['tax_class_for_fee_price_type'] ) ? $element['tax_class_for_fee_price_type'] : '' ),
															] )
														),
													];

													$element_args         = apply_filters( 'wc_epo_display_template_args', array_merge( $element_args, $display ), $element, $value, $choice_counter, $element_type_counter[ $element['type'] ] );
													$element_args['args'] = $element_args;
													if ( THEMECOMPLETE_EPO()->has_math_special_variable( $element_args['rules'], 'cumulative' ) ) {
														$element_args['fieldtype'] .= ' tc-is-math-cumulative';
													} elseif ( THEMECOMPLETE_EPO()->has_math_special_variable( $element_args['rules'], 'special' ) ) {
														$element_args['fieldtype'] .= ' tc-is-math-special';
													}
													if ( $element_object->is_addon ) {
														do_action(
															'tm_epo_display_addons',
															$element,
															$element_args,
															[
																'name_inc'        => $name_inc,
																'element_counter' => $c_element_counter,
																'tabindex'        => $tabindex,
																'form_prefix'     => $form_prefix,
																'field_counter'   => $field_counter,
																'border_type'     => THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_css_selected_border' ),
															],
															$element_object->namespace
														);
													} elseif ( is_readable( apply_filters( 'wc_epo_template_path_element', THEMECOMPLETE_EPO_TEMPLATE_PATH, $element['type'], $element ) . apply_filters( 'wc_epo_template_element', 'tm-' . str_replace( '_', '-', $element['type'] ) . '.php', $element['type'], $element ) ) ) {
														wc_get_template(
															apply_filters( 'wc_epo_template_element', 'tm-' . str_replace( '_', '-', $element['type'] ) . '.php', $element['type'], $element ),
															$element_args,
															$this->get_template_path(),
															apply_filters( 'wc_epo_template_path_element', $this->get_default_path(), $element['type'], $element )
														);
													}
												}

												++$choice_counter;

												++$field_counter;

											}

											if ( 'variations' !== $element['type'] ) {
												if ( $element['enabled'] ) {
													wc_get_template(
														'tm-builder-element-end.php',
														[
															'repeater' => $repeater,
															'repeater_quantity' => $repeater_quantity,
															'repeater_min_rows'    => $repeater_min_rows,
															'repeater_max_rows'    => $repeater_max_rows,
															'repeater_button_label' => $repeater_button_label,
															'get_posted_key' => $get_posted_key,
															'get_posted_key_count' => $args['get_posted_key_count'],
															'tm_element_settings' => $element,
															'element_type' => $element['type'],
															'enabled'     => $element['enabled'],
															'description' => ! empty( $element['description'] ) ? $element['description'] : '',
															'description_color' => ! empty( $element['description_color'] ) ? $element['description_color'] : '',
															'description_position' => ! empty( $element['description_position'] ) ? $element['description_position'] : '',
														],
														$this->get_template_path(),
														$this->get_default_path()
													);
												}
											}

											unset( $args['field_obj'] );
											unset( $field_obj );

										}

										++$element_type_counter[ $element['type'] ];
									}

									if ( isset( $element['connector'] ) && '' !== $element['connector'] ) {
										if ( ! isset( $connectors[ 'c-' . sanitize_key( $element['connector'] ) ] ) ) {
											++$d_element_counter;
										}
										$connectors_choice_counter = 0;
										if ( isset( $connectors[ 'c-' . sanitize_key( $element['connector'] ) ]['choice_counter'] ) ) {
											$connectors_choice_counter = 1 + absint( $connectors[ 'c-' . sanitize_key( $element['connector'] ) ]['choice_counter'] );
										}
										$connectors[ 'c-' . sanitize_key( $element['connector'] ) ] = [
											'element_counter' => $c_element_counter,
											'choice_counter'  => $connectors_choice_counter,
										];
									} else {
										++$d_element_counter;
									}
									++$element_counter;
								} elseif ( 'dynamic' === $element_object->is_post || 'display' === $element_object->is_post ) {
									$field_obj = new $init_class();
									$display   = $field_obj->display_field(
										$element,
										[
											'element_counter' => $element_counter,
											'tabindex'    => $tabindex,
											'form_prefix' => $form_prefix,
											'field_counter' => $field_counter,
											'args'        => $args,
											'product_id'  => isset( $product_id ) ? $product_id : 0,
										]
									);

									if ( is_array( $display ) ) {
										$element_args = [
											'tm_element_settings' => $element,
											'class'       => ! empty( $element['class'] ) ? $element['class'] : '',
											'form_prefix' => $form_prefix,
											'field_counter' => $field_counter,
											'tm_element'  => $element,
											'epo_template_path' => $this->get_template_path(),
											'epo_default_path' => $this->get_default_path(),
											'tm_product_id' => $product_id,
										];

										if ( 'variations' === $element['type'] ) {
											$element_args['variations_builder_element_start_args'] = $variations_builder_element_start_args;
											$element_args['variations_builder_element_end_args']   = [
												'repeater' => '',
												'repeater_quantity' => '',
												'repeater_min_rows' => '',
												'repeater_max_rows' => '',
												'repeater_button_label' => '',
												'get_posted_key' => '',
												'get_posted_key_count' => '',
												'tm_element_settings' => $element,
												'element_type' => $element['type'],
												'description' => ! empty( $element['description'] ) ? $element['description'] : '',
												'description_color' => ! empty( $element['description_color'] ) ? $element['description_color'] : '',
												'description_position' => ! empty( $element['description_position'] ) ? $element['description_position'] : '',
											];
										}

										$element_args = array_merge( $element_args, $display );
										if ( 'variations' !== $element['type'] ) {
											if ( $element['enabled'] ) {
												wc_get_template(
													'tm-builder-element-start.php',
													$args,
													$this->get_template_path(),
													$this->get_default_path()
												);
											}
										}
										if ( $element_object->is_addon ) {
											do_action(
												'tm_epo_display_addons',
												$element,
												$element_args,
												[
													'name_inc' => '',
													'element_counter' => $element_counter,
													'tabindex' => $tabindex,
													'form_prefix' => $form_prefix,
													'field_counter' => $field_counter,
												],
												$element_object->namespace
											);
										} elseif ( is_readable( apply_filters( 'wc_epo_template_path_element', THEMECOMPLETE_EPO_TEMPLATE_PATH, $element['type'], $element ) . apply_filters( 'wc_epo_template_element', 'tm-' . str_replace( '_', '-', $element['type'] ) . '.php', $element['type'], $element ) ) ) {
											wc_get_template(
												apply_filters( 'wc_epo_template_element', 'tm-' . str_replace( '_', '-', $element['type'] ) . '.php', $element['type'], $element ),
												$element_args,
												$this->get_template_path(),
												apply_filters( 'wc_epo_template_path_element', $this->get_default_path(), $element['type'], $element )
											);
										}
										if ( 'variations' !== $element['type'] ) {
											if ( $element['enabled'] ) {
												wc_get_template(
													'tm-builder-element-end.php',
													[
														'repeater' => $repeater,
														'repeater_quantity' => $repeater_quantity,
														'repeater_min_rows'    => $repeater_min_rows,
														'repeater_max_rows'    => $repeater_max_rows,
														'repeater_button_label' => $repeater_button_label,
														'get_posted_key' => isset( $get_posted_key ) ? $get_posted_key : 0,
														'get_posted_key_count' => isset( $args['get_posted_key_count'] ) ? $args['get_posted_key_count'] : 0,
														'tm_element_settings' => $element,
														'element_type' => $element['type'],
														'enabled'     => $element['enabled'],
														'description' => ! empty( $element['description'] ) ? $element['description'] : '',
														'description_color' => ! empty( $element['description_color'] ) ? $element['description_color'] : '',
														'description_position' => ! empty( $element['description_position'] ) ? $element['description_position'] : '',
													],
													$this->get_template_path(),
													$this->get_default_path()
												);
											}
										}
									}

									unset( $field_obj );
								}
							}
						}

						if ( $use_slides && isset( $sections_slides[ $slide_counter ] ) ) {
							$sections_slides[ $slide_counter ] = $sections_slides[ $slide_counter ] - 1;

							if ( $sections_slides[ $slide_counter ] <= 0 ) {
								echo '</div>';
								++$slide_counter;
								$doing_slides = false;
							}
						}
					}

					if ( '' !== $section['sections_slides'] && ( 'slider' === $section['sections_type'] || 'tabs' === $section['sections_type'] ) ) {
						if ( 'tabs' === $section['sections_type'] ) {
							if ( isset( $sections_tabs_labels ) && is_array( $sections_tabs_labels ) ) {
								echo '</div>';
								echo '</div></div>';
							}
						} elseif ( 'slider' === $section['sections_type'] ) {
							echo '</div>';
						}
					}
				}

				wc_get_template(
					'tm-builder-section-end.php',
					$section_args,
					$this->get_template_path(),
					$this->get_default_path()
				);

			}

			wc_get_template(
				'tm-builder-end.php',
				[],
				$this->get_template_path(),
				$this->get_default_path()
			);

			++$unit_counter;

		}

		return [
			'tabindex'          => $tabindex,
			'unit_counter'      => $unit_counter,
			'field_counter'     => $field_counter,
			'element_counter'   => $element_counter,
			'd_element_counter' => $d_element_counter,
			'_currency'         => $_currency,
			'connectors'        => $connectors,
		];
	}

	/**
	 * Displays the option created from the normal (local) mode
	 *
	 * @param array<mixed> $local_price_array The normal options array.
	 * @param array<mixed> $args The variable arguemnts..
	 * @param string       $form_prefix The form prefix.
	 * @param boolean      $dummy_prefix If we should use the form prefix.
	 * @return array<mixed>
	 * @since 4.8
	 */
	public function get_normal_display( $local_price_array = [], $args = [], $form_prefix = null, $dummy_prefix = null ) {
		$tabindex        = $args['tabindex'];
		$unit_counter    = $args['unit_counter'];
		$field_counter   = $args['field_counter'];
		$element_counter = $args['element_counter'];
		$_currency       = $args['_currency'];
		$product_id      = $args['product_id'];

		$form_prefix_onform = '' !== $form_prefix ? '_' . str_replace( '_', '', $form_prefix ) : '';

		// Normal (local) options.
		if ( is_array( $local_price_array ) && count( $local_price_array ) > 0 ) {

			$attributes      = themecomplete_get_attributes( absint( THEMECOMPLETE_EPO_WPML()->get_original_id( $product_id ) ) );
			$wpml_attributes = themecomplete_get_attributes( $product_id );

			$fieldtype = 'tmcp-field';

			if ( is_array( $attributes ) && count( $attributes ) > 0 ) {
				foreach ( $local_price_array as $field ) {
					if ( isset( $field['name'] ) && isset( $attributes[ $field['name'] ] ) && is_array( $attributes[ $field['name'] ] ) && ! $attributes[ $field['name'] ]['is_variation'] ) {

						$attribute      = $attributes[ $field['name'] ];
						$wpml_attribute = isset( $wpml_attributes[ $field['name'] ] ) ? $wpml_attributes[ $field['name'] ] : [];

						$empty_rules = '';
						if ( isset( $field['rules_filtered'][0] ) ) {
							$empty_rules = wp_json_encode( ( $field['rules_filtered'][0] ) );
						}
						if ( empty( $empty_rules ) ) {
							$empty_rules = '';
						}
						$empty_rules_type = '';
						if ( isset( $field['rules_type'][0] ) ) {
							$empty_rules_type = wp_json_encode( ( $field['rules_type'][0] ) );
						}

						$args = [
							'label'          => ( ! $attribute['is_taxonomy'] && isset( $attributes[ $field['name'] ]['name'] ) )
								? wc_attribute_label( $attributes[ $field['name'] ]['name'] )
								: wc_attribute_label( $field['name'] ),
							'required'       => wc_attribute_label( $field['required'] ),
							'field_id'       => 'tc-epo-field-' . $unit_counter,
							'field_type'     => $field['type'],
							'rules'          => $empty_rules,
							'original_rules' => $empty_rules,
							'rules_type'     => $empty_rules_type,
							'li_class'       => 'tc-normal-mode',
						];
						wc_get_template(
							'tm-field-start.php',
							$args,
							$this->get_template_path(),
							$this->get_default_path()
						);

						$name_inc      = '';
						$field_counter = 0;

						if ( $attribute['is_taxonomy'] ) {

							$orderby    = wc_attribute_orderby( $attribute['name'] );
							$order_args = 'orderby=name&hide_empty=0';
							$order_args = [
								'taxonomy'   => $attribute['name'],
								'hide_empty' => false,
							];
							switch ( $orderby ) {
								case 'name':
									$order_args['orderby']    = 'name';
									$order_args['menu_order'] = false;
									break;
								case 'id':
									$order_args['orderby']    = 'id';
									$order_args['order']      = 'ASC';
									$order_args['menu_order'] = false;
									break;
								case 'menu_order':
									$order_args['menu_order'] = 'ASC';
									break;
							}

							// Terms in current lang.
							$_current_terms  = THEMECOMPLETE_EPO_WPML()->get_terms( THEMECOMPLETE_EPO_WPML()->get_lang(), $attribute['name'], $order_args );
							$_current_terms2 = get_terms( $order_args );
							if ( ! is_array( $_current_terms2 ) ) {
								$_current_terms2 = [];
							}
							$_current_terms = THEMECOMPLETE_EPO_WPML()->order_terms( $_current_terms, $_current_terms2 );

							$current_language = apply_filters( 'wpml_current_language', false );
							$default_language = apply_filters( 'wpml_default_language', false );
							do_action( 'wpml_switch_language', $default_language );

							// Terms in default WPML lang.
							$_default_terms  = THEMECOMPLETE_EPO_WPML()->get_terms( THEMECOMPLETE_EPO_WPML()->get_lang(), $attribute['name'], $order_args );
							$_default_terms2 = get_terms( $order_args );
							if ( ! is_array( $_default_terms2 ) ) {
								$_default_terms2 = [];
							}
							$_default_terms = THEMECOMPLETE_EPO_WPML()->order_terms( $_default_terms, $_default_terms2 );

							do_action( 'wpml_switch_language', $current_language );

							$_tems_to_use = THEMECOMPLETE_EPO_WPML()->merge_terms( $_current_terms, $_default_terms );

							$slugs = THEMECOMPLETE_EPO_WPML()->merge_terms_slugs( $_current_terms, $_default_terms );

							switch ( $field['type'] ) {

								case 'select':
									$name_inc = 'select_' . $element_counter;
									++$tabindex;

									$args = [
										'element_id'      => 'tmcp_select_' . $tabindex . $form_prefix,
										'name'            => 'tmcp_' . $name_inc . ( $dummy_prefix ? '' : $form_prefix_onform ),
										'amount'          => '',
										'original_amount' => '',
										'tabindex'        => $tabindex,
										'fieldtype'       => $fieldtype,
										'rules'           => '',
										'original_rules'  => '',
										'rules_type'      => '',
										'textafterprice'  => '',
										'textbeforeprice' => '',
										'class'           => '',
										'class_label'     => '',
										'element_data_attr_html' => '',
										'hide_amount'     => ! empty( $field['hide_price'] ) ? ' hidden' : '',
										'tax_obj'         => false,

										'options'         => [],
										'placeholder'     => '',
									];
									if ( $_tems_to_use && is_array( $_tems_to_use ) ) {
										foreach ( $_tems_to_use as $trid => $term ) {
											if ( ! isset( $slugs[ $term->slug ] ) ) {
												$slugs[ $term->slug ] = $term->slug;
											}
											$has_term = has_term( (int) $term->term_id, $attribute['name'], absint( THEMECOMPLETE_EPO_WPML()->get_original_id( $product_id ) ) ) ? 1 : 0;

											if ( $has_term ) {
												$wpml_term_id = THEMECOMPLETE_EPO_WPML()->is_active() && function_exists( 'icl_object_id' ) ? icl_object_id( $term->term_id, $attribute['name'], false ) : false;
												if ( $wpml_term_id ) {
													$wpml_term = get_term( $wpml_term_id, $attribute['name'] );
												} else {
													$wpml_term = $term;
												}

												$option = [
													'value_to_show' => sanitize_title( $term->slug ),
													'data_price' => '',
													'data_rules' => ( isset( $field['rules_filtered'][ $slugs[ $term->slug ] ] )
														? wp_json_encode( ( $field['rules_filtered'][ $slugs[ $term->slug ] ] ) )
														: ( isset( $field['rules_filtered'][ $term->slug ] )
															? wp_json_encode( $field['rules_filtered'][ $term->slug ] )
															: '' ) ),
													'data_original_rules' => ( isset( $field['rules_filtered'][ $slugs[ $term->slug ] ] )
														? wp_json_encode( ( $field['rules_filtered'][ $slugs[ $term->slug ] ] ) )
														: ( isset( $field['rules_filtered'][ $term->slug ] )
															? wp_json_encode( $field['rules_filtered'][ $term->slug ] )
															: '' ) ),
													'data_rulestype' => ( isset( $field['rules_type'][ $slugs[ $term->slug ] ] )
														? wp_json_encode( ( $field['rules_type'][ $slugs[ $term->slug ] ] ) )
														: ( isset( $field['rules_type'][ $term->slug ] )
															? wp_json_encode( $field['rules_type'][ $term->slug ] )
															: '' ) ),
													'text' => $wpml_term->name,
												];

												if ( isset( $_POST[ 'tmcp_' . $name_inc . $form_prefix_onform ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
													$option['selected'] = wp_unslash( $_POST[ 'tmcp_' . $name_inc . $form_prefix_onform ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification
													$option['current']  = esc_attr( $option['value_to_show'] );
												}

												$args['options'][] = $option;

											}
										}
									}

									wc_get_template(
										'tm-' . $field['type'] . '.php',
										$args,
										$this->get_template_path(),
										$this->get_default_path()
									);
									++$element_counter;
									break;

								case 'radio':
								case 'checkbox':
									if ( $_tems_to_use && is_array( $_tems_to_use ) ) {
										$labelclass       = '';
										$labelclass_start = '';
										$labelclass_end   = '';
										if ( 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_css_styles' ) ) {
											$labelclass       = THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_css_styles_style' );
											$labelclass_start = THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_css_styles_style' );
											$labelclass_end   = true;
										}

										foreach ( $_tems_to_use as $trid => $term ) {
											if ( ! isset( $slugs[ $term->slug ] ) ) {
												$slugs[ $term->slug ] = $term->slug;
											}

											$has_term = has_term( (int) $term->term_id, $attribute['name'], absint( THEMECOMPLETE_EPO_WPML()->get_original_id( $product_id ) ) ) ? 1 : 0;

											if ( $has_term ) {

												$wpml_term_id = THEMECOMPLETE_EPO_WPML()->is_active() && function_exists( 'icl_object_id' ) ? icl_object_id( $term->term_id, $attribute['name'], false ) : false;

												if ( $wpml_term_id ) {
													$wpml_term = get_term( $wpml_term_id, $attribute['name'] );
												} else {
													$wpml_term = $term;
												}

												++$tabindex;

												if ( 'radio' === $field['type'] ) {
													$name_inc = 'radio_' . $element_counter;
												}
												if ( 'checkbox' === $field['type'] ) {
													$name_inc = 'checkbox_' . $element_counter . '_' . $field_counter;
												}

												$original_rules = ( isset( $field['rules_filtered'][ $slugs[ $term->slug ] ] ) ? wp_json_encode( ( $field['rules_filtered'][ $slugs[ $term->slug ] ] ) ) : ( isset( $field['rules_filtered'][ $term->slug ] ) ? wp_json_encode( $field['rules_filtered'][ $term->slug ] ) : '' ) );
												if ( empty( $original_rules ) ) {
													$original_rules = '';
												}

												$checked = false;
												$value   = sanitize_title( $term->slug );
												switch ( $field['type'] ) {
													case 'radio':
														$selected_value = '';
														$name           = 'tmcp_' . $name_inc . ( $dummy_prefix ? '' : $form_prefix_onform );

														if ( 'no' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_reset_options_after_add' ) && isset( $_POST[ $name ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
															$selected_value = wp_unslash( $_POST[ $name ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification
														} elseif ( empty( $_POST ) && isset( $_REQUEST[ $name ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification
															$selected_value = wp_unslash( $_REQUEST[ $name ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
														} elseif ( empty( $_POST ) || ! isset( $_POST[ $name ] ) || 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_reset_options_after_add' ) ) { // phpcs:ignore WordPress.Security.NonceVerification
															$selected_value = -1;
														}

														$checked = -1 !== $selected_value && esc_attr( stripcslashes( $selected_value ) ) === esc_attr( $value );
														break;

													case 'checkbox':
														$selected_value = '';
														$name           = 'tmcp_' . $name_inc . ( $dummy_prefix ? '' : $form_prefix_onform );
														if ( 'no' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_reset_options_after_add' ) && isset( $_POST[ $name ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
															$selected_value = wp_unslash( $_POST[ $name ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification
														} elseif ( empty( $_POST ) && isset( $_REQUEST[ $name ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.NonceVerification
															$selected_value = wp_unslash( $_REQUEST[ $name ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
														} elseif ( ( ( THEMECOMPLETE_EPO()->is_quick_view() || empty( $_POST ) ) && empty( THEMECOMPLETE_EPO()->cart_edit_key ) ) || 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_reset_options_after_add' ) ) { // phpcs:ignore WordPress.Security.NonceVerification
															$selected_value = -1;
														}

														$checked = -1 !== $selected_value && esc_attr( stripcslashes( $selected_value ) ) === esc_attr( $value );
														break;
												}
												$args = [
													'element_id' => 'tmcp_choice_' . $element_counter . '_' . $field_counter . '_' . $tabindex . $form_prefix,
													'name' => isset( $name ) ? $name : '',
													'amount' => '',
													'original_amount' => '',
													'tabindex' => $tabindex,
													'fieldtype' => $fieldtype,
													'rules' => ( isset( $field['rules_filtered'][ $slugs[ $term->slug ] ] ) ? wp_json_encode( ( $field['rules_filtered'][ $slugs[ $term->slug ] ] ) ) : ( isset( $field['rules_filtered'][ $term->slug ] ) ? wp_json_encode( $field['rules_filtered'][ $term->slug ] ) : '' ) ),
													'original_rules' => $original_rules,
													'rules_type' => ( isset( $field['rules_type'][ $slugs[ $term->slug ] ] ) ? wp_json_encode( ( $field['rules_type'][ $slugs[ $term->slug ] ] ) ) : ( isset( $field['rules_type'][ $term->slug ] ) ? wp_json_encode( $field['rules_type'][ $term->slug ] ) : '' ) ),
													'label_mode' => '',
													'label_to_display' => $wpml_term->name,
													'swatch_class' => '',
													'swatch' => [],
													'altsrc' => [],
													'textafterprice' => '',
													'textbeforeprice' => '',
													'class' => '',
													'element_data_attr_html' => '',
													'li_class' => '',
													'exactlimit' => '',
													'minimumlimit' => '',
													'url'  => '',
													'image' => '',
													'imagec' => '',
													'imagep' => '',
													'imagel' => '',
													'image_variations' => '',
													'checked' => $checked,
													'use'  => '',
													'labelclass_start' => $labelclass_start,
													'labelclass' => $labelclass,
													'labelclass_end' => $labelclass_end,
													'hide_amount' => ! empty( $field['hide_price'] ) ? ' hidden' : '',
													'tax_obj' => false,
													'border_type' => '',
													'label' => $wpml_term->name,
													'value' => $value,
													'replacement_mode' => 'none',
													'swatch_position' => 'center',
													'limit' => empty( $field['limit'] ) ? '' : $field['limit'],

												];
												wc_get_template(
													'tm-' . $field['type'] . '.php',
													$args,
													$this->get_template_path(),
													$this->get_default_path()
												);

												++$field_counter;
											}
										}
									}

									++$element_counter;
									break;

							}
						} else {

							$options      = array_map( 'trim', explode( WC_DELIMITER, $attribute['value'] ) );
							$wpml_options = isset( $wpml_attribute['value'] ) ? array_map( 'trim', explode( WC_DELIMITER, $wpml_attribute['value'] ) ) : $options;

							switch ( $field['type'] ) {

								case 'select':
									$name_inc = 'select_' . $element_counter;
									++$tabindex;

									$args = [
										'element_id'      => 'tmcp_select_' . $tabindex . $form_prefix,
										'name'            => 'tmcp_' . $name_inc . ( $dummy_prefix ? '' : $form_prefix_onform ),
										'amount'          => '',
										'original_amount' => '',
										'tabindex'        => $tabindex,
										'fieldtype'       => $fieldtype,
										'rules'           => '',
										'original_rules'  => '',
										'rules_type'      => '',
										'textafterprice'  => '',
										'textbeforeprice' => '',
										'class'           => '',
										'class_label'     => '',
										'element_data_attr_html' => '',
										'hide_amount'     => ! empty( $field['hide_price'] ) ? ' hidden' : '',
										'tax_obj'         => false,
										'border_type'     => '',
										'options'         => [],
										'placeholder'     => '',

									];
									foreach ( $options as $k => $option ) {

										$option = [
											'value_to_show' => sanitize_title( $option ),
											'data_price' => '',
											'data_rules' => ( isset( $field['rules_filtered'][ esc_attr( sanitize_title( $option ) ) ] ) ? wp_json_encode( ( $field['rules_filtered'][ esc_attr( sanitize_title( $option ) ) ] ) ) : '' ),
											'data_original_rules' => ( isset( $field['rules_filtered'][ esc_attr( sanitize_title( $option ) ) ] ) ? wp_json_encode( ( $field['rules_filtered'][ esc_attr( sanitize_title( $option ) ) ] ) ) : '' ),
											'data_rulestype' => ( isset( $field['rules_type'][ esc_attr( sanitize_title( $option ) ) ] ) ? wp_json_encode( ( $field['rules_type'][ esc_attr( sanitize_title( $option ) ) ] ) ) : '' ),
											'text'       => apply_filters( 'woocommerce_tm_epo_option_name', isset( $wpml_options[ $k ] ) ? $wpml_options[ $k ] : $option, null, null ),
										];

										if ( isset( $_POST[ 'tmcp_' . $name_inc . $form_prefix_onform ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
											$option['selected'] = wp_unslash( $_POST[ 'tmcp_' . $name_inc . $form_prefix_onform ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification
											$option['current']  = esc_attr( $option['value_to_show'] );
										}

										$args['options'][] = $option;

									}
									wc_get_template(
										'tm-' . $field['type'] . '.php',
										$args,
										$this->get_template_path(),
										$this->get_default_path()
									);
									++$element_counter;
									break;

								case 'radio':
								case 'checkbox':
									$labelclass       = '';
									$labelclass_start = '';
									$labelclass_end   = '';
									if ( 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_css_styles' ) ) {
										$labelclass       = THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_css_styles_style' );
										$labelclass_start = THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_css_styles_style' );
										$labelclass_end   = true;
									}

									foreach ( $options as $k => $option ) {
										++$tabindex;

										if ( 'radio' === $field['type'] ) {
											$name_inc = 'radio_' . $element_counter;
										}
										if ( 'checkbox' === $field['type'] ) {
											$name_inc = 'checkbox_' . $element_counter . '_' . $field_counter;
										}

										$original_rules = isset( $field['rules_filtered'][ sanitize_title( $option ) ] ) ? wp_json_encode( ( $field['rules_filtered'][ sanitize_title( $option ) ] ) ) : '';
										if ( empty( $original_rules ) ) {
											$original_rules = '';
										}

										$checked = false;
										$value   = sanitize_title( $option );
										switch ( $field['type'] ) {

											case 'radio':
												$selected_value = '';
												$name           = 'tmcp_' . $name_inc . ( $dummy_prefix ? '' : $form_prefix_onform );

												if ( 'no' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_reset_options_after_add' ) && isset( $_POST[ $name ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
													$selected_value = wp_unslash( $_POST[ $name ] );// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification
												} elseif ( empty( $_POST ) && isset( $_REQUEST[ $name ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
													$selected_value = wp_unslash( $_REQUEST[ $name ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
												} elseif ( empty( $_POST ) || ! isset( $_POST[ $name ] ) || 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_reset_options_after_add' ) ) { // phpcs:ignore WordPress.Security.NonceVerification
													$selected_value = -1;
												}

												$checked = -1 !== $selected_value && esc_attr( stripcslashes( $selected_value ) ) === esc_attr( $value );
												break;

											case 'checkbox':
												$selected_value = '';
												$name           = 'tmcp_' . $name_inc . ( $dummy_prefix ? '' : $form_prefix_onform );
												if ( 'no' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_reset_options_after_add' ) && isset( $_POST[ $name ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
													$selected_value = wp_unslash( $_POST[ $name ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification
												} elseif ( empty( $_POST ) && isset( $_REQUEST[ $name ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
													$selected_value = wp_unslash( $_REQUEST[ $name ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended
												} elseif ( ( ( THEMECOMPLETE_EPO()->is_quick_view() || empty( $_POST ) ) && empty( THEMECOMPLETE_EPO()->cart_edit_key ) ) || 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_reset_options_after_add' ) ) { // phpcs:ignore WordPress.Security.NonceVerification
													$selected_value = -1;
												}

												$checked = -1 !== $selected_value && esc_attr( stripcslashes( $selected_value ) ) === esc_attr( $value );
												break;
										}

										$label = apply_filters( 'woocommerce_tm_epo_option_name', isset( $wpml_options[ $k ] ) ? $wpml_options[ $k ] : $option, null, null );

										$args = [
											'element_id'   => 'tmcp_choice_' . $element_counter . '_' . $field_counter . '_' . $tabindex . $form_prefix,
											'name'         => isset( $name ) ? $name : '',
											'amount'       => '',
											'original_amount' => '',
											'tabindex'     => $tabindex,
											'fieldtype'    => $fieldtype,
											'rules'        => isset( $field['rules_filtered'][ sanitize_title( $option ) ] ) ? wp_json_encode( ( $field['rules_filtered'][ sanitize_title( $option ) ] ) ) : '',
											'original_rules' => $original_rules,
											'rules_type'   => isset( $field['rules_type'][ sanitize_title( $option ) ] ) ? wp_json_encode( ( $field['rules_type'][ sanitize_title( $option ) ] ) ) : '',
											'label_mode'   => '',
											'label_to_display' => $label,
											'swatch_class' => '',
											'swatch'       => [],
											'altsrc'       => [],
											'textafterprice' => '',
											'textbeforeprice' => '',
											'class'        => '',
											'element_data_attr_html' => '',
											'li_class'     => '',
											'exactlimit'   => '',
											'minimumlimit' => '',
											'url'          => '',
											'image'        => '',
											'imagec'       => '',
											'imagep'       => '',
											'imagel'       => '',
											'image_variations' => '',
											'checked'      => $checked,
											'use'          => '',
											'labelclass_start' => $labelclass_start,
											'labelclass'   => $labelclass,
											'labelclass_end' => $labelclass_end,
											'hide_amount'  => ! empty( $field['hide_price'] ) ? ' hidden' : '',
											'tax_obj'      => false,
											'border_type'  => '',
											'label'        => $label,
											'value'        => $value,
											'replacement_mode' => 'none',
											'swatch_position' => 'center',
											'limit'        => empty( $field['limit'] ) ? '' : $field['limit'],
										];
										wc_get_template(
											'tm-' . $field['type'] . '.php',
											$args,
											$this->get_template_path(),
											$this->get_default_path()
										);
										++$field_counter;
									}
									++$element_counter;
									break;

							}
						}

						wc_get_template(
							'tm-field-end.php',
							[],
							$this->get_template_path(),
							$this->get_default_path()
						);

						++$unit_counter;
					}
				}
			}
		}

		return [
			'tabindex'        => $tabindex,
			'unit_counter'    => $unit_counter,
			'field_counter'   => $field_counter,
			'element_counter' => $element_counter,
			'_currency'       => $_currency,
		];
	}

	/**
	 * Display totals box
	 *
	 * @param mixed   $product_id The product id.
	 * @param string  $form_prefix The form prefix.
	 * @param boolean $is_from_shortcode If we are in a shortcode.
	 * @return void
	 */
	public function tm_epo_totals( $product_id = 0, $form_prefix = '', $is_from_shortcode = false ) {
		if ( $this->block_epo ) {
			return;
		}

		global $product, $woocommerce;

		if ( ! property_exists( $woocommerce, 'product_factory' )
			|| null === $woocommerce->product_factory
			|| ( $this->tm_options_have_been_displayed && ( ! ( THEMECOMPLETE_EPO()->is_bto || ( ( THEMECOMPLETE_EPO()->is_enabled_shortcodes() && ! $is_from_shortcode ) && ! is_product() ) || ( ( is_shop() || is_product_category() || is_product_tag() ) && 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_enable_in_shop' ) ) ) ) )
		) {
			return;// bad function call.
		}

		$this->print_price_fields( $product_id, $form_prefix, $is_from_shortcode );
		if ( ! ( THEMECOMPLETE_EPO()->is_bto || THEMECOMPLETE_EPO()->is_inline_epo ) && ! $is_from_shortcode ) {
			$this->tm_options_totals_have_been_displayed = true;
		}
	}

	/**
	 * Batch displayh totals box
	 *
	 * @param string $form_prefix The form prefix.
	 * @return void
	 */
	private function tm_epo_totals_batch( $form_prefix = '' ) {
		foreach ( $this->current_product_id_to_be_displayed_check as $key => $product_id ) {
			if ( ! empty( $product_id ) ) {
				$this->print_price_fields( $product_id, $form_prefix );
				if ( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_options_placement' ) !== THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_totals_box_placement' ) ) {
					if ( ! ( THEMECOMPLETE_EPO()->is_bto || THEMECOMPLETE_EPO()->is_inline_epo ) ) {
						unset( $this->epo_internal_counter_check[ 'tc' . $this->epo_internal_counter ] );
					}
				}
			}
		}
		if ( ! ( THEMECOMPLETE_EPO()->is_bto || THEMECOMPLETE_EPO()->is_inline_epo ) ) {
			if ( THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_options_placement' ) !== THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_totals_box_placement' ) ) {
				$this->epo_internal_counter       = 0;
				$this->epo_internal_counter_check = [];
			}
		}
	}

	/**
	 * Display totals box
	 *
	 * @param mixed   $product_id The product id.
	 * @param string  $form_prefix The form prefix.
	 * @param boolean $is_from_shortcode If we are in a shortcode.
	 * @return void
	 */
	private function print_price_fields( $product_id = 0, $form_prefix = '', $is_from_shortcode = false ) {
		if ( $product_id instanceof WC_Product ) {
			$product    = $product_id;
			$product_id = themecomplete_get_id( $product );
		} elseif ( ! is_null( $product_id ) ) {
			$product_id = floatval( trim( $product_id ) );
			if ( ! $product_id ) {
				global $product;
				if ( $product ) {
					$product_id = themecomplete_get_id( $product );
				}
			} else {
				$product = wc_get_product( $product_id );
			}
		}

		if ( ! $product_id || empty( $product ) ) {
			if ( ! empty( $this->current_product_id_to_be_displayed ) ) {
				$product_id = $this->current_product_id_to_be_displayed;
				$product    = wc_get_product( $product_id );
			} else {
				$this->tm_epo_totals_batch( $form_prefix );

				return;
			}
		}
		if ( ! $product_id || ! $product instanceof WC_Product ) {
			return;
		}

		$product_id = absint( $product_id );

		$type = themecomplete_get_product_type( $product );

		if ( 'grouped' === $type ) {
			return;
		}

		$cpf_price_array = THEMECOMPLETE_EPO()->get_product_tm_epos( $product_id, $form_prefix, false, true );
		if ( ! $cpf_price_array ) {
			return;
		}

		if ( THEMECOMPLETE_EPO()->is_associated === false && 'no' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_enable_final_total_box_all' ) ) {
			$global_price_array = $cpf_price_array['global'];
			$local_price_array  = $cpf_price_array['local'];
			if ( empty( $global_price_array ) && empty( $local_price_array ) ) {
				if ( ! ( THEMECOMPLETE_EPO()->is_bto || THEMECOMPLETE_EPO()->is_inline_epo ) ) {
					if ( empty( $this->epo_internal_counter ) || ! isset( $this->epo_internal_counter_check[ 'tc' . $this->epo_internal_counter ] ) ) {
						// First time displaying totals and fields haven't been displayed.
						++$this->epo_internal_counter;
						$this->epo_internal_counter_check[ 'tc' . $this->epo_internal_counter ] = $this->epo_internal_counter;
					} else {
						// Fields have already been displayed.
						unset( $this->epo_internal_counter_check[ 'tc' . $this->epo_internal_counter ] );
						$this->current_product_id_to_be_displayed = 0;
						$this->unique_form_prefix                 = '';
					}
					$_epo_internal_counter = $this->epo_internal_counter;
				} else {
					$_epo_internal_counter = 0;
				}

				return;
			}
		}

		THEMECOMPLETE_EPO()->set_tm_meta( $product_id );

		$force_quantity = 0;
		if ( THEMECOMPLETE_EPO()->cart_edit_key ) {
			$cart_item_key = THEMECOMPLETE_EPO()->cart_edit_key;
			$cart_item     = WC()->cart->get_cart_item( $cart_item_key );

			if ( isset( $cart_item['quantity'] ) ) {
				$force_quantity = $cart_item['quantity'];
			}
		}

		if ( ! $form_prefix && THEMECOMPLETE_EPO()->is_quick_view() ) {
			if ( ! $this->unique_form_prefix ) {
				$this->unique_form_prefix = uniqid( '' );
			}
			$form_prefix = '_tcform' . $this->unique_form_prefix;
		}

		if ( ! ( THEMECOMPLETE_EPO()->is_bto || THEMECOMPLETE_EPO()->is_inline_epo ) ) {
			if ( empty( $this->epo_internal_counter ) || ! isset( $this->epo_internal_counter_check[ 'tc' . $this->epo_internal_counter ] ) ) {
				// First time displaying totals and fields haven't been displayed.
				++$this->epo_internal_counter;
				$this->epo_internal_counter_check[ 'tc' . $this->epo_internal_counter ] = $this->epo_internal_counter;
			} else {
				// Fields have already been displayed.
				unset( $this->epo_internal_counter_check[ 'tc' . $this->epo_internal_counter ] );
				$this->current_product_id_to_be_displayed = 0;
				$this->unique_form_prefix                 = '';
			}
			$_epo_internal_counter = $this->epo_internal_counter;
		} elseif ( THEMECOMPLETE_EPO()->is_inline_epo && $this->epo_internal_counter ) {
			$_epo_internal_counter = $this->epo_internal_counter;
		} else {
			$_epo_internal_counter = 0;
		}

		if ( ! $form_prefix && THEMECOMPLETE_EPO()->wc_vars['is_page'] ) {
			$form_prefix = 'tcform' . $_epo_internal_counter;
		}

		if ( $form_prefix ) {
			$form_prefix = '_' . $form_prefix;
		}

		$minmax = [];

		$minmax['min_price']         = $product->get_price();
		$minmax['min_regular_price'] = $product->get_regular_price();

		if ( function_exists( 'WC_CP' ) && version_compare( WC_CP()->version, '3.8', '<' ) && 'composite' === themecomplete_get_product_type( $product ) && is_callable( [ $product, 'get_base_price' ] ) ) {
			$_price = apply_filters( 'woocommerce_tm_epo_price_compatibility', $product->get_base_price(), $product );
		} else {
			$_price = apply_filters( 'woocommerce_tm_epo_price_compatibility', $minmax['min_price'], $product );
		}

		$price            = [];
		$price['product'] = []; // product price rules.
		$price['price']   = apply_filters( 'wc_epo_product_price', $_price ); // product price.

		$price = apply_filters( 'wc_epo_product_price_rules', $price, $product );

		$regular_price            = [];
		$regular_price['product'] = []; // product price rules.
		$regular_price['price']   = apply_filters( 'wc_epo_product_price', $minmax['min_regular_price'] ); // product price.

		$regular_price = apply_filters( 'wc_epo_product_regular_price_rules', $regular_price, $product );

		// Woothemes Dynamic Pricing (not yet fully compatible).
		if ( class_exists( 'WC_Dynamic_Pricing' ) ) {
			$id = isset( $product->variation_id ) ? $product->variation_id : themecomplete_get_id( $product );
			$dp = WC_Dynamic_Pricing::instance();
			if ( $dp &&
				is_object( $dp ) && property_exists( $dp, 'discounted_products' )
				&& isset( $dp->discounted_products[ $id ] )
			) {
				$_price = $dp->discounted_products[ $id ];
			} else {
				$_price = $product->get_price();
			}
			$price['price'] = apply_filters( 'wc_epo_product_price', $_price ); // product price.
		}

		$variations = [];

		if ( in_array( themecomplete_get_product_type( $product ), apply_filters( 'wc_epo_variable_product_type', [ 'variable' ], $product ), true ) && 'yes' !== THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_no_variation_prices_array' ) ) {
			if ( method_exists( $product, 'get_available_variations' ) ) {
				foreach ( $product->get_available_variations() as $variation ) {

					$child_id          = $variation['variation_id'];
					$product_variation = wc_get_product( $child_id );

					if ( ! $product_variation instanceof WC_Product ) {
						continue;
					}

					if ( $this->discount ) {
						$current_price = THEMECOMPLETE_EPO_ASSOCIATED_PRODUCTS()->get_discounted_price( $product_variation->get_price(), $this->discount, $this->discount_type );
						$product_variation->set_sale_price( $current_price );
						$product_variation->set_price( $current_price );
					}

					// Make sure we always have untaxed price here.
					if ( ! wc_prices_include_tax() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) ) {
						$variation_price = themecomplete_get_price_excluding_tax(
							$product_variation,
							[
								'qty'   => 1,
								'price' => $product_variation->get_price(),
							]
						);
					} else {
						$variation_price = $product_variation->get_price();
					}

					if ( isset( $variation['attributes'] ) && is_array( $variation['attributes'] ) ) {
						$atts = 0;
						foreach ( $variation['attributes'] as $att => $value_att ) {
							if ( isset( $_REQUEST[ $att ] ) && (string) $_REQUEST[ $att ] === (string) $value_att ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
								++$atts;
							}
						}
						if ( count( $variation['attributes'] ) === $atts ) {
							$price['price'] = apply_filters( 'wc_epo_product_price', $variation_price ); // product price.
						}
					}

					do_action( 'wc_epo_print_price_fields_in_variation_loop', $product_variation, $child_id );

					$variations[ $child_id ] = apply_filters( 'woocommerce_tm_epo_price_compatibility', apply_filters( 'wc_epo_product_price', $variation_price, '', false ), $product_variation, $child_id );

				}
			}
		}

		global $woocommerce;
		$cart = $woocommerce->cart;

		$tax_rate = $this->get_tax_rate( themecomplete_get_tax_class( $product ) );

		$taxable          = $product->is_taxable();
		$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
		$tax_string       = '';
		if ( $taxable && 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_tax_string_suffix' ) ) {
			if ( 'excl' === $tax_display_mode ) {

				$tax_string = ' <small>' . apply_filters( 'wc_epo_ex_tax_or_vat_string', WC()->countries->ex_tax_or_vat() ) . '</small>';

			} else {

				$tax_string = ' <small>' . apply_filters( 'wc_epo_inc_tax_or_vat_string', WC()->countries->inc_tax_or_vat() ) . '</small>';

			}
		}
		if ( $taxable && 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_wc_price_suffix' ) ) {
			$tax_string .= ' <small>' . get_option( 'woocommerce_price_display_suffix' ) . '</small>';
		}

		$taxes_of_one        = 0;
		$base_taxes_of_one   = 0;
		$modded_taxes_of_one = 0;

		$is_vat_exempt            = -1;
		$non_base_location_prices = -1;
		$base_tax_rate            = $tax_rate;
		if ( class_exists( 'WC_Tax' ) && version_compare( get_option( 'woocommerce_version' ), '2.4', '>=' ) ) {
			$tax_rates      = WC_Tax::get_rates( themecomplete_get_tax_class( $product ) );
			$base_tax_rates = WC_Tax::get_base_tax_rates( themecomplete_get_tax_class( $product, 'unfiltered' ) );
			$base_tax_rate  = 0;
			foreach ( $base_tax_rates as $key => $value ) {
				$base_tax_rate = $base_tax_rate + floatval( $value['rate'] );
			}
			$tax_rate = 0;
			foreach ( $tax_rates as $key => $value ) {
				$tax_rate = $tax_rate + floatval( $value['rate'] );
			}
			$is_vat_exempt            = true === ( WC()->customer instanceof WC_Customer && WC()->customer->is_vat_exempt() ) ? 1 : 0;
			$non_base_location_prices = ( $tax_rates !== $base_tax_rates && true === apply_filters( 'woocommerce_adjust_non_base_location_prices', true ) ) ? 1 : 0;

			$precision    = wc_get_rounding_precision();
			$price_of_one = 1 * ( pow( 10, $precision ) );

			$taxes_of_one = array_sum( WC_Tax::calc_tax( $price_of_one, $tax_rates, wc_prices_include_tax() ) );
			if ( $non_base_location_prices ) {
				$prices_include_tax = true;
			} else {
				$prices_include_tax = wc_prices_include_tax();
			}
			$base_taxes_of_one   = array_sum( WC_Tax::calc_tax( $price_of_one, $base_tax_rates, $prices_include_tax ) );
			$modded_taxes_of_one = array_sum( WC_Tax::calc_tax( $price_of_one - $base_taxes_of_one, $tax_rates, false ) );

			$taxes_of_one        = $taxes_of_one / ( pow( 10, $precision ) );
			$base_taxes_of_one   = $base_taxes_of_one / ( pow( 10, $precision ) );
			$modded_taxes_of_one = $modded_taxes_of_one / ( pow( 10, $precision ) );

		}

		$forcart        = 'main';
		$classcart      = 'tm-cart-main';
		$classtotalform = 'tm-totals-form-main';
		$form_prefix_id = str_replace( '_', '', $form_prefix );
		if ( ! empty( $form_prefix ) ) {
			$forcart        = $form_prefix_id;
			$classcart      = 'tm-cart-' . $form_prefix_id;
			$classtotalform = 'tm-totals-form-' . $form_prefix_id;
		}

		if ( THEMECOMPLETE_EPO()->is_associated ) {
			$classtotalform .= ' tm-totals-form-inline';
			$classcart      .= ' tm-cart-inline';
		}

		do_action(
			'wc_epo_before_totals_box',
			[
				'product_id'        => $product_id,
				'form_prefix'       => $form_prefix,
				'is_from_shortcode' => $is_from_shortcode,
			]
		);
		if ( $is_from_shortcode ) {
			add_action( 'wc_epo_totals_form', [ $this, 'woocommerce_before_add_to_cart_button' ], 10, 1 );
		}

		$tm_epo_final_total_box = ( empty( THEMECOMPLETE_EPO()->tm_meta_cpf['override_final_total_box'] ) ) ? THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_final_total_box' ) : THEMECOMPLETE_EPO()->tm_meta_cpf['override_final_total_box'];
		if ( THEMECOMPLETE_EPO()->is_associated === true && ! THEMECOMPLETE_EPO_API()->has_options( $product_id ) ) {
			$tm_epo_final_total_box = 'disable';
		}
		$tm_epo_final_total_box = THEMECOMPLETE_EPO_SETTINGS()->get_compatibility_value( 'tm_epo_final_total_box', $tm_epo_final_total_box );

		$tm_epo_show_options_total = ( empty( THEMECOMPLETE_EPO()->tm_meta_cpf['override_show_options_total'] ) ) ? THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_show_options_total' ) : THEMECOMPLETE_EPO()->tm_meta_cpf['override_show_options_total'];
		$tm_epo_show_final_total   = ( empty( THEMECOMPLETE_EPO()->tm_meta_cpf['override_show_final_total'] ) ) ? THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_show_final_total' ) : THEMECOMPLETE_EPO()->tm_meta_cpf['override_show_final_total'];

		$tc_form_prefix_name = 'tc_form_prefix';
		if ( THEMECOMPLETE_EPO()->is_associated ) {
			$tc_form_prefix_name = 'tc_form_prefix_assoc[' . THEMECOMPLETE_EPO()->associated_element_uniqid . ']';
			if ( false !== THEMECOMPLETE_EPO()->associated_product_counter ) {
				$tc_form_prefix_name = $tc_form_prefix_name . '[' . THEMECOMPLETE_EPO()->associated_product_counter . ']';
			}
		}

		wc_get_template(
			'tm-totals.php',
			apply_filters(
				'wc_epo_template_args_tm_totals',
				[

					'classcart'                 => $classcart,
					'forcart'                   => $forcart,
					'classtotalform'            => $classtotalform,
					'is_on_sale'                => $product->is_on_sale(),

					'variations'                => wp_json_encode( (array) $variations ),

					'is_sold_individually'      => $product->is_sold_individually(),
					'hidden'                    => 'disable' === THEMECOMPLETE_EPO()->tm_meta_cpf['override_final_total_box'] ? ' hidden' : '',
					'price_override'            => ( 'no' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_override_product_price' ) )
						? 0
						: ( ( 'yes' === THEMECOMPLETE_EPO_DATA_STORE()->get( 'tm_epo_global_override_product_price' ) )
							? 1
							: ( ! empty( THEMECOMPLETE_EPO()->tm_meta_cpf['price_override'] ) ? 1 : 0 ) ),
					'form_prefix'               => $form_prefix_id,
					'tc_form_prefix_name'       => $tc_form_prefix_name,
					'tc_form_prefix_class'      => ( THEMECOMPLETE_EPO()->is_associated ) ? 'tc_form_prefix_assoc' : 'tc_form_prefix',
					'product_type'              => themecomplete_get_product_type( $product ),
					'price'                     => apply_filters( 'woocommerce_tm_final_price', $price['price'], $product ),
					'regular_price'             => apply_filters( 'woocommerce_tm_final_price', $regular_price['price'], $product ),
					'is_vat_exempt'             => $is_vat_exempt,
					'non_base_location_prices'  => $non_base_location_prices,
					'taxable'                   => $taxable,
					'tax_display_mode'          => $tax_display_mode,
					'prices_include_tax'        => wc_prices_include_tax(),
					'tax_rate'                  => $tax_rate,
					'base_tax_rate'             => $base_tax_rate,
					'base_taxes_of_one'         => $base_taxes_of_one,
					'taxes_of_one'              => $taxes_of_one,
					'modded_taxes_of_one'       => $modded_taxes_of_one,
					'tax_string'                => $tax_string,
					'product_price_rules'       => wp_json_encode( (array) $price['product'] ),
					'fields_price_rules'        => 0,
					'force_quantity'            => $force_quantity,
					'product_id'                => $product_id,
					'epo_internal_counter'      => $_epo_internal_counter,
					'is_from_shortcode'         => $is_from_shortcode,
					'tm_epo_final_total_box'    => $tm_epo_final_total_box,
					'tm_epo_show_final_total'   => $tm_epo_show_final_total,
					'tm_epo_show_options_total' => $tm_epo_show_options_total,

				],
				$product
			),
			$this->get_template_path(),
			$this->get_default_path()
		);

		do_action(
			'wc_epo_after_totals_box',
			[
				'product_id'        => $product_id,
				'form_prefix'       => $form_prefix,
				'is_from_shortcode' => $is_from_shortcode,
			]
		);
	}
}
