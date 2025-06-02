<?php
/**
 * Extra Product Options Check class
 *
 * @package Extra Product Options/Classes
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Extra Product Options Check class
 *
 * Checks if the plugin can be run according to
 * its requirements.
 *
 * @package Extra Product Options/Classes
 * @version 6.0
 */
final class THEMECOMPLETE_EPO_CHECK_Base {

	/**
	 * The single instance of the class
	 *
	 * @var THEMECOMPLETE_EPO_CHECK_Base|null
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @return THEMECOMPLETE_EPO_CHECK_Base
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
		add_action( 'admin_init', [ $this, 'check_version' ] );

		if ( ! self::compatible_version() ) {
			return;
		}

		add_action( 'plugins_loaded', [ $this, 'eco_check' ], 0 );
		add_action( 'plugins_loaded', [ $this, 'multiple_file_upload_check' ], 0 );
		add_action( 'plugins_loaded', [ $this, 'weight_plugin_check' ], 0 );
	}

	/**
	 * Stop the plugin if requirements are not met
	 *
	 * @since 1.0
	 * @return boolean
	 * @static
	 */
	public function stop_plugin() {
		/**
		 * Disable plugin for Themify - WooCommerce Product Filter ajax
		 * https://wordpress.org/plugins/themify-wc-product-filter/
		 */
		if ( isset( $_REQUEST['wpf_ajax'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return true;
		}

		/**
		 * Disable plugin for Product X builder
		 * https://www.wpxpo.com/productx/
		 */
		if ( isset( $_REQUEST['post_type'] ) && 'wopb_builder' === $_REQUEST['post_type'] ) { // phpcs:ignore WordPress.Security.NonceVerification
			return true;
		}

		if ( apply_filters( 'wc_epo_stop_plugin', false ) ) {
			return true;
		}

		if ( ! self::themecomplete_woocommerce_check() ) {
			return true;
		}

		if ( ! self::compatible_version() ) {
			return true;
		}

		if ( self::old_version() ) {
			return true;
		}

		if ( ! self::woocommerce_check() ) {
			return true;
		}

		if ( ! self::php_check() ) {
			return true;
		}

		return false;
	}

	/**
	 * Check for compatible WordPress version
	 *
	 * @since 1.0
	 * @return void
	 * @static
	 */
	public static function activation_check() {
		if ( ! self::compatible_version() ) {
			deactivate_plugins( plugin_basename( THEMECOMPLETE_EPO_PLUGIN_FILE ) );
			/* translators: %s WordPress version number */
			wp_die( sprintf( esc_html__( 'Extra Product Options & Add-Ons for WooCommerce requires WordPress %s or later.', 'woocommerce-tm-extra-product-options' ), esc_html( THEMECOMPLETE_EPO_WP_VERSION ) ) );
		}
	}

	/**
	 * Check for compatible Extra Checkout Options version
	 *
	 * @since 4.8.5
	 * @return void
	 * @static
	 */
	public function eco_check() {
		if ( defined( 'THEMECOMPLETE_ECO_VERSION' ) && defined( 'THEMECOMPLETE_ECO_PLUGIN_PATH' ) && function_exists( 'themecomplete_eco_plugin_init_admin' ) && function_exists( 'themecomplete_eco_plugin_init' ) ) {
			if ( version_compare( THEMECOMPLETE_ECO_VERSION, THEMECOMPLETE_SUPPORTED_ECO_VERSION, '<' ) ) {
				include_once ABSPATH . 'wp-admin/includes/plugin.php';
				add_action( 'admin_notices', [ $this, 'themecomplete_addons_check_eco' ] );
				add_action( 'admin_notices', [ $this, 'eco_notice' ] );
				remove_action( 'plugins_loaded', 'themecomplete_eco_plugin_init_admin' );
				remove_action( 'plugins_loaded', 'themecomplete_eco_plugin_init' );
				if ( is_plugin_active( plugin_basename( THEMECOMPLETE_ECO_PLUGIN_PATH . '/tm-woo-extra-checkout-options.php' ) ) ) {
					deactivate_plugins( plugin_basename( THEMECOMPLETE_ECO_PLUGIN_PATH . '/tm-woo-extra-checkout-options.php' ) );
					add_action( 'admin_notices', [ $this, 'eco_notice_deactivate' ] );
				}
			}
		}

		if ( defined( 'TM_ECO_VERSION' ) && defined( 'TM_ECO_PLUGIN_PATH' ) && function_exists( 'tc_eco_plugin_init_admin' ) && function_exists( 'tc_eco_plugin_init' ) ) {

			if ( version_compare( TM_ECO_VERSION, THEMECOMPLETE_SUPPORTED_ECO_VERSION, '<' ) ) {
				add_action( 'admin_notices', [ $this, 'themecomplete_addons_check_eco' ] );
				add_action( 'admin_notices', [ $this, 'eco_notice' ] );
				remove_action( 'plugins_loaded', 'tc_eco_plugin_init_admin' );
				remove_action( 'plugins_loaded', 'tc_eco_plugin_init' );
				if ( is_plugin_active( plugin_basename( TM_ECO_PLUGIN_PATH . '/tm-woo-extra-checkout-options.php' ) ) ) {
					deactivate_plugins( plugin_basename( TM_ECO_PLUGIN_PATH . '/tm-woo-extra-checkout-options.php' ) );
					add_action( 'admin_notices', [ $this, 'eco_notice_deactivate' ] );
				}
			}
		}
	}

	/**
	 * Disable multiple file upload addon
	 * (allready included in 6.4.2 +)
	 *
	 * @since 6.4.2
	 * @return void
	 * @static
	 */
	public function multiple_file_upload_check() {
		if ( class_exists( 'THEMECOMPLETE_Extension_Class_multiple_file_upload' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
			if ( is_plugin_active( 'woocommerce-tm-extra-product-options-multiple-file-upload/woocommerce-tm-extra-product-options-multiple-file-upload.php' ) ) {
				deactivate_plugins( 'woocommerce-tm-extra-product-options-multiple-file-upload/woocommerce-tm-extra-product-options-multiple-file-upload.php' );
				add_action( 'admin_notices', [ $this, 'multiple_file_upload_notice_deactivate' ] );
			}
		}
	}

	/**
	 * Disable weight addon
	 * (allready included in 6.4.4 +)
	 *
	 * @since 6.4.4
	 * @return void
	 * @static
	 */
	public function weight_plugin_check() {
		if ( function_exists( 'tc_add_custom_weight' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
			if ( is_plugin_active( 'woocommerce-tm-weight-addon/tm-woo-extra-weight.php' ) ) {
				deactivate_plugins( 'woocommerce-tm-weight-addon/tm-woo-extra-weight.php' );
				add_action( 'admin_notices', [ $this, 'weight_plugin_notice_deactivate' ] );
			}
		}
	}

	/**
	 * Deprecation notice for Extra Checkout Options
	 *
	 * @return void
	 * @since 4.9.4
	 */
	public function themecomplete_addons_check_eco() {
		$all_plugins = get_plugins();
		echo '<div class="error fade"><p>';
		/* translators: %s WooCommerce Extra Checkout Options version number */
		printf( esc_html__( 'Extra Product Options & Add-Ons for WooCommerce requires WooCommerce Extra Checkout Options %s or later!.', 'woocommerce-tm-extra-product-options' ), esc_html( THEMECOMPLETE_SUPPORTED_ECO_VERSION ) );

		if ( defined( 'THEMECOMPLETE_ECO_VERSION' ) && isset( $all_plugins['woocommerce-tm-extra-checkout-options-addon/tm-woo-extra-checkout-options.php'] ) ) {
			echo ' ' . esc_html__( 'The installed version is', 'woocommerce-tm-extra-product-options' ) . ' ' . esc_html( THEMECOMPLETE_ECO_VERSION );
		} elseif ( defined( 'TM_ECO_VERSION' ) && isset( $all_plugins['woocommerce-tm-extra-checkout-options-addon/tm-woo-extra-checkout-options.php'] ) ) {
			echo ' ' . esc_html__( 'The installed version is', 'woocommerce-tm-extra-product-options' ) . ' ' . esc_html( TM_ECO_VERSION );
		}

		echo '</p></div>' . "\n";
	}

	/**
	 * Prints a notice
	 *
	 * @return void
	 */
	public function eco_notice() {
		echo '<div class="error fade"><h4>' . esc_html__( 'Extra Product Options & Add-Ons for WooCommerce', 'woocommerce-tm-extra-product-options' ) . '</h4><p>';
		printf(
			/* translators: %1 open strong html tag %2 close strong html tag */
			esc_html__( '%1$sImportant:%2$s Your version of WooCommerce Extra Checkout Options is not supported. Please update to the latest version.', 'woocommerce-tm-extra-product-options' ),
			'<strong>',
			'</strong>'
		);
		echo '</p></div>' . "\n";
	}

	/**
	 * Prints a notice
	 *
	 * @return void
	 */
	public function eco_notice_deactivate() {
		echo '<div class="error fade"><h4>' . esc_html__( 'Extra Product Options & Add-Ons for WooCommerce', 'woocommerce-tm-extra-product-options' ) . '</h4><p>';
		printf(
			/* translators: %1 open strong html tag %2 close strong html tag */
			esc_html__( '%1$sImportant:%2$s Extra Product Options & Add-Ons for WooCommerce has been deactivated because it is not compatible with the current version of Extra Checkout Options.', 'woocommerce-tm-extra-product-options' ),
			'<strong>',
			'</strong>'
		);
		echo '</p></div>' . "\n";
	}

	/**
	 * Prints a notice
	 *
	 * @return void
	 */
	public function multiple_file_upload_notice_deactivate() {
		echo '<div class="error fade"><h4>' . esc_html__( 'Extra Product Options & Add-Ons for WooCommerce', 'woocommerce-tm-extra-product-options' ) . '</h4><p>';
		printf(
			/* translators: %1 open strong html tag %2 close strong html tag */
			esc_html__( '%1$sImportant:%2$s WooCommerce TM Extra Product Options Multiple File Upload has been deactivated because its functionality is included in the main plugin.', 'woocommerce-tm-extra-product-options' ),
			'<strong>',
			'</strong>'
		);
		echo '</p></div>' . "\n";
	}

	/**
	 * Prints a notice
	 *
	 * @return void
	 */
	public function weight_plugin_notice_deactivate() {
		echo '<div class="error fade"><h4>' . esc_html__( 'Extra Product Options & Add-Ons for WooCommerce', 'woocommerce-tm-extra-product-options' ) . '</h4><p>';
		printf(
			/* translators: %1 open strong html tag %2 close strong html tag */
			esc_html__( '%1$sImportant:%2$s Extra Product Options & Add-Ons for WooCommerce - Weight addon has been deactivated because its functionality is included in the main plugin.', 'woocommerce-tm-extra-product-options' ),
			'<strong>',
			'</strong>'
		);
		echo '</p></div>' . "\n";
	}

	/**
	 * Check for compatible WordPress and WooCommerce version
	 *
	 * The function also checks if there is a previous old version of the
	 * plugin install that could potentially conflict.
	 *
	 * @return void
	 * @since 1.0
	 */
	public function check_version() {
		if ( ! self::compatible_version() ) {
			if ( is_plugin_active( plugin_basename( THEMECOMPLETE_EPO_PLUGIN_FILE ) ) ) {
				deactivate_plugins( plugin_basename( THEMECOMPLETE_EPO_PLUGIN_FILE ) );
				add_action( 'admin_notices', [ $this, 'disabled_notice' ] );
				if ( isset( $_GET['activate'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
					unset( $_GET['activate'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				}
			}
		}

		if ( self::old_version() ) {
			deactivate_plugins( 'woocommerce-tm-custom-price-fields/tm-woo-custom-prices.php' );
			add_action( 'admin_notices', [ $this, 'deprecated_notice' ] );
		}

		if ( ! self::woocommerce_check() ) {
			add_action( 'admin_notices', [ $this, 'disabled_notice_woocommerce_check' ] );
		}
	}

	/**
	 * Add notice for WooCommerce version
	 *
	 * @return void
	 * @since 1.0
	 */
	public function disabled_notice_woocommerce_check() {
		echo '<div class="woocommerce-message error fade"><p>';
		if ( self::themecomplete_woocommerce_check_only() ) {
			printf(
				/* translators: %1 open strong html tag %2 close strong html tag */
				esc_html__(
					'%1$sImportant:%2$s Please run WooCommerce updater before using Extra Product Options & Add-Ons for WooCommerce.',
					'woocommerce-tm-extra-product-options'
				),
				'<strong>',
				'</strong>'
			);
		} else {
			printf(
				/* translators: %1 open strong html tag %2 close strong html tag %3 open a html tag %4 close a html tag */
				esc_html__( '%1$sImportant:%2$s Extra Product Options & Add-Ons for WooCommerce requires %3$sWooCommerce%4$s %5$s or later.', 'woocommerce-tm-extra-product-options' ),
				'<strong>',
				'</strong>',
				'<a href="http://wordpress.org/extend/plugins/woocommerce/">',
				'</a>',
				esc_html( THEMECOMPLETE_EPO_WC_VERSION )
			);
		}
		echo '</p>';

		if ( self::themecomplete_woocommerce_check_only() && get_option( 'woocommerce_db_version' ) ) {

			$update_url = wp_nonce_url(
				add_query_arg( 'do_update_woocommerce', 'true', admin_url( 'admin.php?page=wc-settings' ) ),
				'wc_db_update',
				'wc_db_update_nonce'
			);

			echo '<p class="submit"><a href="' . esc_url( $update_url ) . '" class="wc-update-now button-primary">' . esc_attr__( 'Run the updater', 'woocommerce' ) . '</a></p>';
		}

		echo '</div>';
	}

	/**
	 * Add deprecations notices
	 *
	 * @return void
	 * @since 1.0
	 */
	public function deprecated_notice() {
		$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );

		if ( in_array( 'woocommerce-tm-custom-price-fields/tm-woo-custom-prices.php', $active_plugins, true ) ) {
			$deactivate_url = 'plugins.php?action=deactivate&plugin=' . rawurlencode( 'woocommerce-tm-custom-price-fields/tm-woo-custom-prices.php' ) . '&plugin_status=all&paged=1&s&_wpnonce=' . rawurlencode( wp_create_nonce( 'deactivate-plugin_woocommerce-tm-custom-price-fields/tm-woo-custom-prices.php' ) );
			echo '<div class="error fade"><p>';
			echo '<strong>Important:</strong> It is highly recommended that you <a href="' . esc_url( admin_url( $deactivate_url ) ) . '"> deactivate the old Custom Price Fields</a> plugin.';
			echo '</p></div>' . "\n";
		} else {
			$delete_url = 'plugins.php?action=delete-selected&checked%5B0%5D=' . rawurlencode( 'woocommerce-tm-custom-price-fields/tm-woo-custom-prices.php' ) . '&plugin_status=all&paged=1&s&_wpnonce=' . rawurlencode( wp_create_nonce( 'bulk-plugins' ) );
			echo '<div class="error fade"><p>';
			echo '<strong>Important:</strong> It is highly recommended that you <a href="' . esc_url( admin_url( $delete_url ) ) . '"> delete the old Custom Price Fields</a> plugin.';
			echo '</p></div>' . "\n";
		}
	}

	/**
	 * Add notice for WordPress version
	 *
	 * @return void
	 * @since 1.0
	 */
	public function disabled_notice() {
		echo '<div class="error fade"><p>';
		printf(
			/* translators: %1 open strong html tag %2 close strong html tag %3 WordPress version */
			esc_html__( '%1$sImportant:%2$s Extra Product Options & Add-Ons for WooCommerce requires WordPress %3$s or later.', 'woocommerce-tm-extra-product-options' ),
			'<strong>',
			'</strong>',
			esc_html( THEMECOMPLETE_EPO_WP_VERSION )
		);
		echo '</p></div>' . "\n";
	}

	/**
	 * Add notice for WordPress version
	 *
	 * @return void
	 * @since 6.0.5
	 */
	public function php_check_notice() {
		echo '<div class="error fade"><p>';
		printf(
			/* translators: %1 open strong html tag %2 close strong html tag %3 WordPress version */
			esc_html__( '%1$sImportant:%2$s Extra Product Options & Add-Ons for WooCommerce requires PHP version %3$s or later.', 'woocommerce-tm-extra-product-options' ),
			'<strong>',
			'</strong>',
			esc_html( THEMECOMPLETE_EPO_PHP_VERSION )
		);
		echo '</p></div>' . "\n";
	}

	/**
	 * Check for comaptible WordPress version
	 *
	 * @return boolean
	 * @since 6.0.5
	 */
	public static function php_check() {
		if ( function_exists( 'phpversion' ) ) {
			if ( version_compare( phpversion(), THEMECOMPLETE_EPO_PHP_VERSION, '<' ) ) {
				add_action( 'admin_notices', [ $this, 'php_check_notice' ] );
				return false;
			}
		}
		return true;
	}
	/**
	 * Check for comaptible WordPress version
	 *
	 * @return boolean
	 * @since 1.0
	 */
	public static function compatible_version() {
		if ( version_compare( $GLOBALS['wp_version'], THEMECOMPLETE_EPO_WP_VERSION, '<' ) ) {
			return false;
		}
		return true;
	}

	/**
	 * Check for an older plugin version
	 *
	 * @return boolean
	 * @since 1.0
	 */
	public static function old_version() {
		if ( class_exists( 'TM_Custom_Prices' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if WooCommerce database needs update
	 *
	 * @return boolean
	 * @since 1.0
	 */
	public static function tc_needs_wc_db_update() {
		$_tm_current_woo_version = get_option( 'woocommerce_db_version' );
		$_tc_needs_wc_db_update  = false;
		if ( get_option( 'woocommerce_db_version' ) !== false ) {
			if ( version_compare( get_option( 'woocommerce_db_version' ), '2.3', '<' ) && version_compare( get_option( 'woocommerce_version' ), '2.4', '<' ) ) {
				$_tm_notice_check       = '_wc_needs_update';
				$_tc_needs_wc_db_update = get_option( $_tm_notice_check );
				// no check after 2.6 update.
			} elseif ( version_compare( get_option( 'woocommerce_db_version' ), '2.5', '>=' ) ) {
				$_tc_needs_wc_db_update = false;
			} else {
				$_tm_notice_check       = 'woocommerce_admin_notices';
				$_tc_needs_wc_db_update = in_array( 'update', get_option( $_tm_notice_check, [] ), true );
			}
		}

		return $_tc_needs_wc_db_update;
	}

	/**
	 * Check if WooCommerce is active
	 *
	 * @return boolean
	 * @since 1.0
	 */
	public static function themecomplete_woocommerce_check() {
		return ! self::tc_needs_wc_db_update() && self::themecomplete_woocommerce_check_only();
	}

	/**
	 * Check if WooCommerce is active
	 *
	 * @return boolean
	 * @since 1.0
	 */
	public static function themecomplete_woocommerce_check_only() {
		$active_plugins = (array) get_option( 'active_plugins', [] );
		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', [] ) );
		}

		return in_array( 'woocommerce/woocommerce.php', $active_plugins, true ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins );
	}

	/**
	 * Check for comaptible WooCommerce version
	 *
	 * @return boolean
	 * @since 1.0
	 */
	public static function woocommerce_check() {
		if ( get_option( 'woocommerce_db_version' ) === false && class_exists( 'WC_Install' ) ) {
			WC_Install::update_db_version();
		}

		if ( self::themecomplete_woocommerce_check() && ! version_compare( get_option( 'woocommerce_db_version' ), THEMECOMPLETE_EPO_WC_VERSION, '<' ) ) {
			return true;
		}

		return false;
	}
}
