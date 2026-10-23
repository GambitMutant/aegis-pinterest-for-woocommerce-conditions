<?php
/**
 * Plugin Name:       AEGIS Pinterest for WooCommerce Conditions
 * Plugin URI:        https://github.com/GambitMutant/aegis-pinterest-for-woocommerce-conditions
 * Description:       Sets the Pinterest condition (New, Used, or Refurbished) for a product, or for every variation at once. Add-on for Pinterest for WooCommerce. Donations support The AEGIS Alliance's project funding, with a secondary purpose of business operations funding. Crypto accepted at Coinbase: USDC 0x39C7e115C4184f2E646D44E3681A87a22a93341B, BTC 3PCJwFfVytUCW2mfgUgKbjWmqsYvTzgCH1, ETH 0xcD8B620962e9ff4a098c4134f1C82fDf96236F87, DOGE DAo22guf8pmNcF1V7FvuqrXTgy2khuPysB, USDT 0x4aF2eE05A313c8a8dd53EdA87DDA69F3E375c3B5.
 * Version:           1.3.2
 * Author:            Gambit Mutant
 * Author URI:        https://github.com/GambitMutant/aegis-pinterest-for-woocommerce-conditions
 * License:           Source-available. See TERMS.md.
 * License URI:       https://github.com/GambitMutant/aegis-pinterest-for-woocommerce-conditions/blob/main/TERMS.md
 * Text Domain:       aegis-pinterest-wc-conditions
 * Domain Path:       /languages
 * Requires at least: 6.2
 * Requires PHP:      7.4
 * Requires Plugins:  woocommerce, pinterest-for-woocommerce
 * Tested up to:      7.1.3
 * Update URI:        https://github.com/GambitMutant/aegis-pinterest-for-woocommerce-conditions
 *
 * WC requires at least: 7.1
 * WC tested up to:      11.2
 *
 * @package Aegis_Pinterest_WC_Conditions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'AEGIS_PINTEREST_WC_CONDITIONS_VERSION', '1.3.2' );
define( 'AEGIS_PINTEREST_WC_CONDITIONS_FILE', __FILE__ );

/**
 * Tell WooCommerce this add-on does not break HPOS, Cart and Checkout Blocks,
 * or the block product editor. The condition control itself is printed from
 * the classic General product-data tab.
 */
add_action(
	'before_woocommerce_init',
	static function () {
		if ( ! class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			return;
		}

		$features = array(
			'custom_order_tables',
			'cart_checkout_blocks',
			'product_block_editor',
		);

		foreach ( $features as $feature ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( $feature, AEGIS_PINTEREST_WC_CONDITIONS_FILE, true );
		}
	}
);

add_action( 'plugins_loaded', 'aegis_pinterest_wc_conditions_boot', 20 );
add_action( 'init', 'aegis_pinterest_wc_conditions_load_textdomain' );

/**
 * Load translations.
 *
 * WordPress loads a plugin text domain on its own from WordPress 4.6 onward.
 * The explicit call stays as a fallback for older sites.
 */
function aegis_pinterest_wc_conditions_load_textdomain() {
	load_plugin_textdomain(
		'aegis-pinterest-wc-conditions',
		false,
		dirname( plugin_basename( AEGIS_PINTEREST_WC_CONDITIONS_FILE ) ) . '/languages'
	);
}

/**
 * Whether WooCommerce and Pinterest for WooCommerce are both available.
 *
 * Class and constant checks come first so a security plugin, a must-use loader,
 * or Multisite network activation cannot hide a dependency that is actually loaded.
 *
 * @return bool
 */
function aegis_pinterest_wc_conditions_dependencies_met() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return false;
	}

	if ( defined( 'PINTEREST_FOR_WOOCOMMERCE_VERSION' ) || class_exists( 'Pinterest_For_Woocommerce' ) ) {
		return true;
	}

	$plugin_file = 'pinterest-for-woocommerce/pinterest-for-woocommerce.php';

	if ( ! function_exists( 'is_plugin_active' ) ) {
		$plugin_api = ABSPATH . 'wp-admin/includes/plugin.php';
		if ( is_readable( $plugin_api ) ) {
			require_once $plugin_api;
		}
	}

	if ( function_exists( 'is_plugin_active' ) && is_plugin_active( $plugin_file ) ) {
		return true;
	}

	if ( function_exists( 'is_plugin_active_for_network' ) && is_plugin_active_for_network( $plugin_file ) ) {
		return true;
	}

	return false;
}

/**
 * Start the condition tool, or explain which required plugin is missing.
 */
function aegis_pinterest_wc_conditions_boot() {
	if ( ! empty( $GLOBALS['aegis_pinterest_wc_conditions_booted'] ) ) {
		return;
	}
	$GLOBALS['aegis_pinterest_wc_conditions_booted'] = true;

	if ( ! aegis_pinterest_wc_conditions_dependencies_met() ) {
		add_action( 'admin_notices', 'aegis_pinterest_wc_conditions_dependency_notice' );
		return;
	}

	if ( class_exists( 'AEGIS_Pinterest_WC_Conditions', false ) ) {
		new AEGIS_Pinterest_WC_Conditions();
	}
}

/**
 * Admin notice when WooCommerce or Pinterest for WooCommerce is not active.
 */
function aegis_pinterest_wc_conditions_dependency_notice() {
	if ( ! current_user_can( 'manage_woocommerce' ) && ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	$missing = array();

	if ( ! class_exists( 'WooCommerce' ) ) {
		$missing[] = 'WooCommerce';
	}

	if ( ! defined( 'PINTEREST_FOR_WOOCOMMERCE_VERSION' ) && ! class_exists( 'Pinterest_For_Woocommerce' ) ) {
		$missing[] = 'Pinterest for WooCommerce';
	}

	if ( empty( $missing ) ) {
		return;
	}

	$message = sprintf(
		/* translators: %s: comma-separated list of missing plugin names. */
		__( 'AEGIS Pinterest for WooCommerce Conditions is active, but it is waiting for %s. Install and activate the missing plugin and the condition tool will show on the product editor.', 'aegis-pinterest-wc-conditions' ),
		implode( ', ', $missing )
	);

	aegis_pinterest_wc_conditions_print_notice( $message, 'warning' );
}

/**
 * Print an admin notice, using wp_admin_notice() when this WordPress has it.
 *
 * @param string $message Plain-text message.
 * @param string $type    Notice type: success, warning, error, or info.
 */
function aegis_pinterest_wc_conditions_print_notice( $message, $type ) {
	if ( ! is_string( $message ) || '' === $message ) {
		return;
	}

	$allowed = array( 'success', 'warning', 'error', 'info' );
	if ( ! in_array( $type, $allowed, true ) ) {
		$type = 'info';
	}

	if ( function_exists( 'wp_admin_notice' ) ) {
		wp_admin_notice(
			$message,
			array(
				'type'        => $type,
				'dismissible' => true,
			)
		);
		return;
	}

	echo '<div class="notice notice-' . esc_attr( $type ) . ' is-dismissible"><p>' . esc_html( $message ) . '</p></div>';
}

if ( ! class_exists( 'AEGIS_Pinterest_WC_Conditions', false ) ) {

	/**
	 * Adds the condition control and writes Pinterest condition meta on save.
	 */
	class AEGIS_Pinterest_WC_Conditions {

		/**
		 * Meta key Pinterest for WooCommerce 1.5.4 reads and writes.
		 *
		 * AttributeManager prefixes the attribute id "condition" with "_wc_pinterest_".
		 *
		 * @var string
		 */
		private $meta_key = '_wc_pinterest_condition';

		/**
		 * Condition values accepted by Pinterest for WooCommerce.
		 *
		 * @var string[]
		 */
		private $allowed = array( 'new', 'used', 'refurbished' );

		/**
		 * Constructor.
		 */
		public function __construct() {
			add_action( 'woocommerce_product_options_general_product_data', array( $this, 'add_condition_update_tool' ) );
			add_action( 'woocommerce_update_product', array( $this, 'handle_condition_update' ) );
			add_action( 'admin_notices', array( $this, 'show_admin_notices' ) );
		}

		/**
		 * Add the condition control to the General product data tab.
		 */
		public function add_condition_update_tool() {
			if ( ! function_exists( 'woocommerce_wp_select' ) ) {
				return;
			}

			$product_object = $this->get_screen_product();
			if ( ! $product_object instanceof WC_Product ) {
				return;
			}

			$is_variable       = $this->is_variable_product( $product_object );
			$current_condition = $this->get_current_condition( $product_object, $is_variable );

			$title       = $is_variable ? __( 'Bulk Update Variation Pinterest Condition', 'aegis-pinterest-wc-conditions' ) : __( 'Update Pinterest Condition', 'aegis-pinterest-wc-conditions' );
			$description = $is_variable ? __( 'Apply a Pinterest condition to ALL variations of THIS product. This will run after the product is updated.', 'aegis-pinterest-wc-conditions' ) : __( 'Set the Pinterest condition for this product.', 'aegis-pinterest-wc-conditions' );
			$label       = $is_variable ? __( 'Condition for All Variations', 'aegis-pinterest-wc-conditions' ) : __( 'Condition for this Product', 'aegis-pinterest-wc-conditions' );
			$field_desc  = __( 'Select a condition and click "Update Product".', 'aegis-pinterest-wc-conditions' );

			$current_text = $this->format_condition_for_display( $current_condition );
			if ( '' !== $current_text ) {
				$current_label = $is_variable
					? __( 'Current condition (all variations): ', 'aegis-pinterest-wc-conditions' )
					: __( 'Current condition: ', 'aegis-pinterest-wc-conditions' );
				$description  .= ' ' . $current_label . '<strong>' . esc_html( $current_text ) . '</strong>';
			}

			echo '<div class="options_group">';
			echo '<h3>' . esc_html( $title ) . '</h3>';
			echo '<p class="form-field">' . wp_kses_post( $description ) . '</p>';

			wp_nonce_field( 'pinterest_condition_update', 'pinterest_condition_nonce' );

			woocommerce_wp_select(
				array(
					'id'          => 'pinterest_condition_updater_field',
					'name'        => 'pinterest_condition_updater_field',
					'label'       => $label,
					'value'       => '',
					'options'     => array(
						''            => __( 'Do not change', 'aegis-pinterest-wc-conditions' ),
						'new'         => __( 'New', 'aegis-pinterest-wc-conditions' ),
						'used'        => __( 'Used', 'aegis-pinterest-wc-conditions' ),
						'refurbished' => __( 'Refurbished', 'aegis-pinterest-wc-conditions' ),
					),
					'description' => $field_desc,
					'desc_tip'    => true,
				)
			);

			echo '</div>';
		}

		/**
		 * Save the submitted condition for this product, or for every variation.
		 *
		 * @param int|WC_Product $product_id Product ID, or a product object on older callers.
		 */
		public function handle_condition_update( $product_id ) {
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			if ( $product_id instanceof WC_Product ) {
				$product_id = $product_id->get_id();
			}

			$product_id = absint( $product_id );
			if ( $product_id <= 0 ) {
				return;
			}

			if ( ! isset( $_POST['pinterest_condition_updater_field'], $_POST['pinterest_condition_nonce'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- nonce is checked immediately below.
				return;
			}

			$nonce = wp_unslash( $_POST['pinterest_condition_nonce'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized as a string before wp_verify_nonce().
			if ( ! is_string( $nonce ) || ! wp_verify_nonce( sanitize_text_field( $nonce ), 'pinterest_condition_update' ) ) {
				return;
			}

			if ( ! current_user_can( 'edit_product', $product_id ) ) {
				return;
			}

			$user_id = get_current_user_id();
			if ( $user_id <= 0 ) {
				return;
			}

			$raw = wp_unslash( $_POST['pinterest_condition_updater_field'] );
			if ( ! is_string( $raw ) ) {
				return;
			}

			$condition = strtolower( sanitize_text_field( $raw ) );
			if ( ! in_array( $condition, $this->allowed, true ) ) {
				return;
			}

			$product = function_exists( 'wc_get_product' ) ? wc_get_product( $product_id ) : null;
			if ( ! $product instanceof WC_Product ) {
				return;
			}

			$updated_count   = 0;
			$success_message = '';
			$condition_label = $this->condition_label( $condition );

			if ( $this->is_variable_product( $product ) ) {
				$variation_ids = is_callable( array( $product, 'get_children' ) ) ? $product->get_children() : array();
				if ( is_array( $variation_ids ) ) {
					foreach ( $variation_ids as $variation_id ) {
						$variation_id = absint( $variation_id );
						if ( $variation_id <= 0 ) {
							continue;
						}
						update_post_meta( $variation_id, $this->meta_key, $condition );
						$updated_count++;
					}
				}

				if ( $updated_count > 0 ) {
					$success_message = sprintf(
						/* translators: 1: number of variations updated, 2: condition label. */
						__( 'Successfully updated %1$d variations to the "%2$s" Pinterest condition.', 'aegis-pinterest-wc-conditions' ),
						$updated_count,
						$condition_label
					);
				}
			} else {
				update_post_meta( $product_id, $this->meta_key, $condition );
				$updated_count   = 1;
				$success_message = sprintf(
					/* translators: %s: condition label. */
					__( 'Successfully updated this product to the "%s" Pinterest condition.', 'aegis-pinterest-wc-conditions' ),
					$condition_label
				);
			}

			if ( $updated_count > 0 && '' !== $success_message ) {
				set_transient( 'aegis_pin_condition_notice_' . $user_id, $success_message, 30 );
			}
		}

		/**
		 * Show the success notice for the user who just saved a condition.
		 */
		public function show_admin_notices() {
			$user_id = get_current_user_id();
			if ( $user_id <= 0 ) {
				return;
			}

			$key    = 'aegis_pin_condition_notice_' . $user_id;
			$notice = get_transient( $key );
			if ( ! is_string( $notice ) || '' === $notice ) {
				return;
			}

			delete_transient( $key );
			aegis_pinterest_wc_conditions_print_notice( $notice, 'success' );
		}

		/**
		 * Product being edited. Falls back if another plugin unsets the global.
		 *
		 * @return WC_Product|null
		 */
		private function get_screen_product() {
			global $product_object, $post;

			if ( $product_object instanceof WC_Product ) {
				return $product_object;
			}

			$product_id = 0;
			if ( isset( $post ) && $post instanceof WP_Post ) {
				$product_id = (int) $post->ID;
			}

			if ( $product_id <= 0 && isset( $_GET['post'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- identifies the product already open on this screen. No data is changed.
				$raw_post = wp_unslash( $_GET['post'] );
				if ( is_scalar( $raw_post ) ) {
					$product_id = absint( $raw_post );
				}
			}

			if ( $product_id > 0 && function_exists( 'wc_get_product' ) ) {
				$product = wc_get_product( $product_id );
				if ( $product instanceof WC_Product ) {
					return $product;
				}
			}

			return null;
		}

		/**
		 * Whether this is a variable product whose variations should all be updated.
		 *
		 * @param WC_Product $product Product.
		 * @return bool
		 */
		private function is_variable_product( $product ) {
			return $product instanceof WC_Product && is_callable( array( $product, 'is_type' ) ) && $product->is_type( 'variable' );
		}

		/**
		 * Current stored condition, or an empty string when variations disagree.
		 *
		 * @param WC_Product $product     Product on screen.
		 * @param bool       $is_variable Whether to read child variations instead of the parent.
		 * @return string
		 */
		private function get_current_condition( $product, $is_variable ) {
			if ( $is_variable ) {
				$variation_ids = is_callable( array( $product, 'get_children' ) ) ? $product->get_children() : array();
				if ( ! is_array( $variation_ids ) || empty( $variation_ids ) ) {
					return '';
				}

				$conditions = array();
				foreach ( $variation_ids as $variation_id ) {
					$value = get_post_meta( absint( $variation_id ), $this->meta_key, true );
					if ( is_string( $value ) ) {
						$conditions[] = $value;
					}
				}

				$conditions = array_unique( $conditions );
				if ( 1 === count( $conditions ) ) {
					$only = reset( $conditions );
					return is_string( $only ) ? $only : '';
				}

				return '';
			}

			$value = get_post_meta( $product->get_id(), $this->meta_key, true );
			return is_string( $value ) ? $value : '';
		}

		/**
		 * Translated label for a known condition value.
		 *
		 * @param string $condition Stored value.
		 * @return string
		 */
		private function condition_label( $condition ) {
			$labels = array(
				'new'         => __( 'New', 'aegis-pinterest-wc-conditions' ),
				'used'        => __( 'Used', 'aegis-pinterest-wc-conditions' ),
				'refurbished' => __( 'Refurbished', 'aegis-pinterest-wc-conditions' ),
			);

			return isset( $labels[ $condition ] ) ? $labels[ $condition ] : '';
		}

		/**
		 * Safe display text for a stored condition. Unknown or non-string values are not printed raw.
		 *
		 * @param mixed $condition Stored meta.
		 * @return string
		 */
		private function format_condition_for_display( $condition ) {
			if ( ! is_string( $condition ) ) {
				return '';
			}

			$condition = strtolower( trim( $condition ) );
			if ( '' === $condition ) {
				return '';
			}

			$label = $this->condition_label( $condition );
			if ( '' !== $label ) {
				return $label;
			}

			return ucfirst( sanitize_text_field( $condition ) );
		}
	}
}
