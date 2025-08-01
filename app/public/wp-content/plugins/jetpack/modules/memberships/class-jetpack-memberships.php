<?php
/**
 * Jetpack_Memberships: wrapper for memberships functions.
 *
 * @package    Jetpack
 * @since      7.3.0
 */

use Automattic\Jetpack\Blocks;
use Automattic\Jetpack\Extensions\Premium_Content\Subscription_Service\Abstract_Token_Subscription_Service;
use Automattic\Jetpack\Status;
use Automattic\Jetpack\Status\Host;
use Automattic\Jetpack\Status\Request;
use const Automattic\Jetpack\Extensions\Subscriptions\META_NAME_FOR_POST_LEVEL_ACCESS_SETTINGS;
use const Automattic\Jetpack\Extensions\Subscriptions\META_NAME_FOR_POST_TIER_ID_SETTINGS;

require_once __DIR__ . '/../../extensions/blocks/subscriptions/constants.php';

/**
 * Class Jetpack_Memberships
 * This class represents the Memberships functionality.
 */
class Jetpack_Memberships {
	/**
	 * CSS class prefix to use in the styling.
	 *
	 * @var string
	 */
	public static $css_classname_prefix = 'jetpack-memberships';
	/**
	 * Our CPT type for the product (plan).
	 *
	 * @var string
	 */
	public static $post_type_plan = 'jp_mem_plan';

	/**
	 * Our CPT type for the product (plan).
	 *
	 * @var string
	 */
	public static $post_type_coupon = 'memberships_coupon';

	/**
	 * Our CPT type for the product (plan).
	 *
	 * @var string
	 */
	public static $post_type_gift = 'memberships_gift';

	/**
	 * Tier type for plans
	 *
	 * @var string
	 */
	public static $type_tier = 'tier';

	/**
	 * Option stores status for memberships (Stripe, etc.).
	 *
	 * @var string
	 */
	public static $has_connected_account_option_name = 'jetpack-memberships-has-connected-account';

	/**
	 * Post meta that will store the level of access for newsletters
	 *
	 * @var string
	 */
	public static $post_access_level_meta_name = META_NAME_FOR_POST_LEVEL_ACCESS_SETTINGS;

	/**
	 * Post meta that will store the tier ID of access for newsletters
	 *
	 * @var string
	 */
	public static $post_access_tier_meta_name = META_NAME_FOR_POST_TIER_ID_SETTINGS;

	/**
	 * Button block type to use.
	 *
	 * @var string
	 */
	private static $button_block_name = 'recurring-payments';

	/**
	 * These are defaults for wp_kses ran on the membership button.
	 *
	 * @var array
	 */
	private static $tags_allowed_in_the_button = array( 'br' => array() );

	/**
	 * The minimum required plan for this Gutenberg block.
	 *
	 * @var string Plan slug
	 */
	private static $required_plan;

	/**
	 * Track recurring payments block registration.
	 *
	 * @var boolean True if block registration has been executed.
	 */
	private static $has_registered_block = false;

	/**
	 * Classic singleton pattern
	 *
	 * @var Jetpack_Memberships
	 */
	private static $instance;

	/**
	 * Cached results of user_can_view_post() method.
	 *
	 * @var array
	 */
	private static $user_can_view_post_cache = array();

	/**
	 * Cached results of user_is_paid_subscriber() method.
	 *
	 * @var array
	 */
	private static $user_is_paid_subscriber_cache = array();

	/**
	 * Cached results of get_post_access_level method.
	 *
	 * @var array
	 */
	private static $post_access_level_cache = array();

	/**
	 * Clear cached results of get_post_access_level method.
	 */
	public static function clear_post_access_level_cache() {
		self::$post_access_level_cache = array();
	}

	/**
	 * Currencies we support and Stripe's minimum amount for a transaction in that currency.
	 *
	 * @link https://stripe.com/docs/currencies#minimum-and-maximum-charge-amounts
	 *
	 * List has to be in with `SUPPORTED_CURRENCIES` in extensions/shared/currencies.js.
	 */
	const SUPPORTED_CURRENCIES = array(
		'USD' => 0.5,
		'AUD' => 0.5,
		'BRL' => 0.5,
		'CAD' => 0.5,
		'CHF' => 0.5,
		'DKK' => 2.5,
		'EUR' => 0.5,
		'GBP' => 0.3,
		'HKD' => 4.0,
		'INR' => 0.5,
		'JPY' => 50,
		'MXN' => 10,
		'NOK' => 3.0,
		'NZD' => 0.5,
		'PLN' => 2.0,
		'SEK' => 3.0,
		'SGD' => 0.5,
		'CZK' => 15.0,
		'HUF' => 175.0,
		'TWD' => 10.0,
		'IDR' => 0,
		'ILS' => 0,
		'PHP' => 0,
		'RUB' => 0,
		'TRY' => 0,
	);

	/**
	 * Jetpack_Memberships constructor.
	 */
	private function __construct() {}

	/**
	 * The actual constructor initializing the object.
	 *
	 * @return Jetpack_Memberships
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
			self::$instance->register_init_hook();
			// Yes, `pro-plan` with a dash, `jetpack_personal` with an underscore. Check the v1.5 endpoint to verify.
			$wpcom_plan_slug     = defined( 'ENABLE_PRO_PLAN' ) ? 'pro-plan' : 'personal-bundle';
			self::$required_plan = ( new Host() )->is_wpcom_simple() ? $wpcom_plan_slug : 'jetpack_personal';
		}

		return self::$instance;
	}
	/**
	 * Get the map that defines the shape of CPT post. keys are names of fields and
	 * 'meta' is the name of actual WP post meta field that corresponds.
	 *
	 * @return array
	 */
	private static function get_plan_property_mapping() {
		$meta_prefix = 'jetpack_memberships_';
		$properties  = array(
			'price'           => array(
				'meta' => $meta_prefix . 'price',
			),
			'currency'        => array(
				'meta' => $meta_prefix . 'currency',
			),
			'site_subscriber' => array(
				'meta' => $meta_prefix . 'site_subscriber',
			),
			'product_id'      => array(
				'meta' => $meta_prefix . 'product_id',
			),
			'tier'            => array(
				'meta' => $meta_prefix . 'tier',
			),
			'is_deleted'      => array(
				'meta' => $meta_prefix . 'is_deleted',
			),
			'is_sandboxed'    => array(
				'meta' => $meta_prefix . 'is_sandboxed',
			),
		);
		return $properties;
	}

	/**
	 * Inits further hooks on init hook.
	 */
	private function register_init_hook() {
		add_action( 'init', array( $this, 'init_hook_action' ) );
		add_action( 'jetpack_register_gutenberg_extensions', array( $this, 'register_gutenberg_block' ) );
		// phpcs:ignore WPCUT.SwitchBlog.SwitchBlog -- wpcom flags **every** use of switch_blog, apparently expecting valid instances to ignore or suppress the sniff.
		add_action( 'switch_blog', array( $this, 'clear_post_access_level_cache' ) );
	}

	/**
	 * Actual hooks initializing on init.
	 */
	public function init_hook_action() {
		add_filter( 'rest_api_allowed_post_types', array( $this, 'allow_rest_api_types' ) );
		add_filter( 'jetpack_sync_post_meta_whitelist', array( $this, 'allow_sync_post_meta' ) );
		$this->setup_cpts();

		if ( Jetpack::is_module_active( 'subscriptions' ) && Request::is_frontend() ) {
			add_action( 'wp_logout', array( $this, 'subscriber_logout' ) );
		}
	}

	/**
	 * Logs the subscriber out by clearing out the premium content cookie.
	 */
	public function subscriber_logout() {
		if ( ! class_exists( 'Automattic\Jetpack\Extensions\Premium_Content\Subscription_Service\Abstract_Token_Subscription_Service' ) ) {
			return;
		}

		Abstract_Token_Subscription_Service::clear_token_cookie();
	}

	/**
	 * Sets up the custom post types for the module.
	 */
	private function setup_cpts() {
		/*
		 * PLAN data structure.
		 */
		$capabilities = array(
			'edit_post'          => 'edit_posts',
			'read_post'          => 'read_private_posts',
			'delete_post'        => 'delete_posts',
			'edit_posts'         => 'edit_posts',
			'edit_others_posts'  => 'edit_others_posts',
			'publish_posts'      => 'publish_posts',
			'read_private_posts' => 'read_private_posts',
		);
		$order_args   = array(
			'label'               => esc_html__( 'Plan', 'jetpack' ),
			'description'         => esc_html__( 'Recurring Payments plans', 'jetpack' ),
			'supports'            => array( 'title', 'custom-fields', 'content' ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => false,
			'show_in_menu'        => false,
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'rewrite'             => false,
			'capabilities'        => $capabilities,
			'show_in_rest'        => false,
		);
		register_post_type( self::$post_type_plan, $order_args );
		$coupon_args = array(
			'label'               => esc_html__( 'Coupon', 'jetpack' ),
			'description'         => esc_html__( 'Memberships coupons', 'jetpack' ),
			'supports'            => array( 'title', 'custom-fields', 'content' ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => false,
			'show_in_menu'        => false,
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'rewrite'             => false,
			'capabilities'        => $capabilities,
			'show_in_rest'        => false,
		);
		register_post_type( self::$post_type_coupon, $coupon_args );
		$gift_args = array(
			'label'               => esc_html__( 'Gift', 'jetpack' ),
			'description'         => esc_html__( 'Memberships gifts', 'jetpack' ),
			'supports'            => array( 'title', 'custom-fields', 'content' ),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => false,
			'show_in_menu'        => false,
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'rewrite'             => false,
			'capabilities'        => $capabilities,
			'show_in_rest'        => false,
		);
		register_post_type( self::$post_type_gift, $gift_args );
	}

	/**
	 * Allows custom post types to be used by REST API.
	 *
	 * @param array $post_types - other post types.
	 *
	 * @see hook 'rest_api_allowed_post_types'
	 * @return array
	 */
	public function allow_rest_api_types( $post_types ) {
		$post_types[] = self::$post_type_plan;
		$post_types[] = self::$post_type_coupon;
		$post_types[] = self::$post_type_gift;

		return $post_types;
	}

	/**
	 * Allows custom meta fields to sync.
	 *
	 * @param array $post_meta - previously changet post meta.
	 *
	 * @return array
	 */
	public function allow_sync_post_meta( $post_meta ) {
		$meta_keys_plans = array_map(
			array( $this, 'return_meta' ),
			self::get_plan_property_mapping()
		);

		$meta_coupons_prefix = self::$post_type_coupon . '_';
		$meta_keys_coupons   = array(
			$meta_coupons_prefix . 'coupon_code',
			$meta_coupons_prefix . 'can_be_combined',
			$meta_coupons_prefix . 'first_time_purchase_only',
			$meta_coupons_prefix . 'limit_per_user',
			$meta_coupons_prefix . 'discount_type',
			$meta_coupons_prefix . 'discount_value',
			$meta_coupons_prefix . 'discount_percentage',
			$meta_coupons_prefix . 'discount_currency',
			$meta_coupons_prefix . 'start_date',
			$meta_coupons_prefix . 'end_date',
			$meta_coupons_prefix . 'plan_ids_allow_list',
			$meta_coupons_prefix . 'duration',
			$meta_coupons_prefix . 'email_allow_list',
			$meta_coupons_prefix . 'is_deleted',
			$meta_coupons_prefix . 'is_sandboxed',
		);

		$meta_gifts_prefix = self::$post_type_gift . '_';
		$meta_keys_gifts   = array(
			$meta_gifts_prefix . 'user_id',
			$meta_gifts_prefix . 'plan_id',
			$meta_gifts_prefix . 'is_deleted',
		);
		return array_merge(
			$post_meta,
			array_values( $meta_keys_plans ),
			$meta_keys_coupons,
			$meta_keys_gifts
		);
	}

	/**
	 * This returns meta attribute of passet array.
	 * Used for array functions.
	 *
	 * @param array $map - stuff.
	 *
	 * @return mixed
	 */
	public function return_meta( $map ) {
		return $map['meta'];
	}

	/**
	 * Show an error to the user (or embed a clue in the HTML) when the button does not get rendered properly.
	 *
	 * @param WP_Error $error The error message with error code.
	 * @return string The error message rendered as HTML.
	 */
	public function render_button_error( $error ) {
		if ( static::user_can_edit() ) {
			return '<div><strong>Jetpack Memberships Error: ' . $error->get_error_code() . '</strong><br />' . $error->get_error_message() . '</div>';
		}
		return '<div>Sorry! This product is not available for purchase at this time.</div><!-- Jetpack Memberships Error: ' . $error->get_error_code() . ' -->';
	}

	/**
	 * Renders a preview of the Recurring Payment button, which is not hooked
	 * up to the subscription url. Used to preview the block on the frontend
	 * for site editors when Stripe has not been connected.
	 *
	 * @param array  $attrs - attributes in the shortcode.
	 * @param string $content - Recurring Payment block content.
	 *
	 * @return string|void
	 */
	public function render_button_preview( $attrs, $content = null ) {
		if ( ! empty( $content ) ) {
			$block_id = esc_attr( wp_unique_id( 'recurring-payments-block-' ) );
			$content  = str_replace( 'recurring-payments-id', $block_id, $content );
			$content  = str_replace( 'wp-block-jetpack-recurring-payments', 'wp-block-jetpack-recurring-payments wp-block-button', $content );
			return $content;
		}
		return $this->deprecated_render_button_v1( $attrs, null );
	}

	/**
	 * Determines whether the button preview should be rendered. Returns true
	 * if the user has editing permissions, the button is not configured correctly
	 * (because it requires a plan upgrade or Stripe connection), and the
	 * button is a child of a Premium Content block.
	 *
	 * @param WP_Block $block Recurring Payments block instance.
	 *
	 * @return boolean
	 */
	public function should_render_button_preview( $block ) {
		$user_can_edit              = static::user_can_edit();
		$requires_stripe_connection = ! static::has_connected_account();

		$jetpack_ready = ! self::is_enabled_jetpack_recurring_payments();

		$is_premium_content_child = false;
		if ( isset( $block ) && isset( $block->context['isPremiumContentChild'] ) ) {
			$is_premium_content_child = (int) $block->context['isPremiumContentChild'];
		}

		return $is_premium_content_child &&
			$user_can_edit &&
			$requires_stripe_connection &&
			$jetpack_ready;
	}

	/**
	 * Callback that parses the membership purchase shortcode.
	 *
	 * @param array    $attributes - attributes in the shortcode. `id` here is the CPT id of the plan.
	 * @param string   $content - Recurring Payment block content.
	 * @param WP_Block $block - Recurring Payment block instance.
	 *
	 * @return string|void - HTML for the button, void removes the button.
	 */
	public function render_button( $attributes, $content = null, $block = null ) {
		Jetpack_Gutenberg::load_assets_as_required( self::$button_block_name );

		if ( $this->should_render_button_preview( $block ) ) {
			return $this->render_button_preview( $attributes, $content );
		}

		if ( empty( $attributes['planId'] ) && empty( $attributes['planIds'] ) ) {
			return $this->render_button_error( new WP_Error( 'jetpack-memberships-rb-npi', __( 'No plan was configured for this button.', 'jetpack' ) . ' ' . __( 'Edit this post and confirm that an existing payment plan is selected for this block.', 'jetpack' ) ) );
		}

		// This is string of '+` separated plan ids. Loop through them and
		// filter out the ones that are not valid.
		$plan_ids = array();
		if ( ! empty( $attributes['planIds'] ) ) {
			$plan_ids = $attributes['planIds'];
		} elseif ( ! empty( $attributes['planId'] ) ) {
			$plan_ids = explode( '+', $attributes['planId'] );
		}
		$valid_plans = array();
		foreach ( $plan_ids as $plan_id ) {
			if ( ! is_numeric( $plan_id ) ) {
				continue;
			}
			$product = get_post( $plan_id );
			if ( ! $product ) {
				return $this->render_button_error( new WP_Error( 'jetpack-memberships-rb-npf', __( 'Could not find a plan for this button.', 'jetpack' ) . ' ' . __( 'Edit this post and confirm that the selected payment plan still exists and is available for purchase.', 'jetpack' ) ) );
			}
			if ( is_wp_error( $product ) ) {
				'@phan-var WP_Error $product'; // `get_post` isn't supposed to return a WP_Error, so Phan is confused here. See also https://github.com/phan/phan/issues/3127
				return $this->render_button_error( new WP_Error( 'jetpack-memberships-rb-npf-we', __( 'Encountered an error when getting the plan associated with this button:', 'jetpack' ) . ' ' . $product->get_error_message() . '. ' . __( ' Edit this post and confirm that the selected payment plan still exists and is available for purchase.', 'jetpack' ) ) );
			}
			if ( $product->post_type !== self::$post_type_plan ) {
				return $this->render_button_error( new WP_Error( 'jetpack-memberships-rb-pnplan', __( 'The payment plan selected is not actually a payment plan.', 'jetpack' ) . ' ' . __( 'Edit this post and confirm that the selected payment plan still exists and is available for purchase.', 'jetpack' ) ) );
			}
			if ( 'publish' !== $product->post_status ) {
				return $this->render_button_error( new WP_Error( 'jetpack-memberships-rb-psnpub', __( 'The selected payment plan is not active.', 'jetpack' ) . ' ' . __( 'Edit this post and confirm that the selected payment plan still exists and is available for purchase.', 'jetpack' ) ) );
			}
			$valid_plans[] = $plan_id;
		}

		// If none are valid, return.
		// (Returning like this makes the button disappear.)
		if ( empty( $valid_plans ) ) {
			return;
		}
		$plan_id = implode( '+', $valid_plans );

		if ( ! empty( $content ) ) {
			$block_id      = esc_attr( wp_unique_id( 'recurring-payments-block-' ) );
			$content       = str_replace( 'recurring-payments-id', $block_id, $content );
			$content       = str_replace( 'wp-block-jetpack-recurring-payments', 'wp-block-jetpack-recurring-payments wp-block-button', $content );
			$subscribe_url = $this->get_subscription_url( $plan_id );
			return preg_replace( '/(href=".*")/U', 'href="' . $subscribe_url . '"', $content );
		}

		return $this->deprecated_render_button_v1( $attributes, $plan_id );
	}

	/**
	 * Builds subscription URL for this membership using the current blog and
	 * supplied plan IDs.
	 *
	 * @param integer $plan_id - Unique ID for the plan being subscribed to.
	 * @return string
	 */
	public function get_subscription_url( $plan_id ) {
		global $wp;

		return add_query_arg(
			array(
				'blog'     => esc_attr( self::get_blog_id() ),
				'plan'     => esc_attr( $plan_id ),
				'lang'     => esc_attr( get_locale() ),
				'pid'      => esc_attr( get_the_ID() ), // Needed for analytics purposes.
				'redirect' => esc_attr( rawurlencode( home_url( $wp->request ) ) ), // Needed for redirect back in case of redirect-based flow.
			),
			'https://subscribe.wordpress.com/memberships/'
		);
	}

	/**
	 * Renders a deprecated legacy version of the button HTML.
	 *
	 * @param array   $attrs - Array containing the Recurring Payment block attributes.
	 * @param integer $plan_id - Unique plan ID the membership is for.
	 *
	 * @return string
	 */
	public function deprecated_render_button_v1( $attrs, $plan_id ) {
		$button_label = isset( $attrs['submitButtonText'] )
			? $attrs['submitButtonText']
			: __( 'Your contribution', 'jetpack' );

		$button_styles = array();
		if ( ! empty( $attrs['customBackgroundButtonColor'] ) ) {
			array_push(
				$button_styles,
				sprintf(
					'background-color: %s',
					sanitize_hex_color( $attrs['customBackgroundButtonColor'] )
				)
			);
		}
		if ( ! empty( $attrs['customTextButtonColor'] ) ) {
			array_push(
				$button_styles,
				sprintf(
					'color: %s',
					sanitize_hex_color( $attrs['customTextButtonColor'] )
				)
			);
		}
		$button_styles = implode( ';', $button_styles );

		return sprintf(
			'<div class="%1$s"><a role="button" href="%2$s" class="%3$s" style="%4$s">%5$s</a></div>',
			esc_attr(
				Blocks::classes(
					self::$button_block_name,
					$attrs,
					array( 'wp-block-button' )
				)
			),
			esc_url( $this->get_subscription_url( $plan_id ) ),
			isset( $attrs['submitButtonClasses'] ) ? esc_attr( $attrs['submitButtonClasses'] ) : 'wp-block-button__link',
			esc_attr( $button_styles ),
			wp_kses( $button_label, self::$tags_allowed_in_the_button )
		);
	}

	/**
	 * Get current blog id.
	 *
	 * @return int
	 */
	public static function get_blog_id() {
		if ( defined( 'IS_WPCOM' ) && IS_WPCOM ) {
			return get_current_blog_id();
		}

		return Jetpack_Options::get_option( 'id' );
	}

	/**
	 * Get the id of the connected payment acount (Stripe etc).
	 *
	 * @return bool
	 */
	public static function has_connected_account() {

		// This is the primary solution.
		$has_option = get_option( self::$has_connected_account_option_name, false ) ? true : false;
		if ( $has_option ) {
			return true;
		}

		return false;
	}

	/**
	 * Get the post access level
	 *
	 * If no ID is provided, the method tries to get it from the global post object.
	 *
	 * @param int|null $post_id The ID of the post. Default is null.
	 *
	 * @return string the actual post access level (see projects/plugins/jetpack/extensions/blocks/subscriptions/constants.js for the values).
	 */
	public static function get_post_access_level( $post_id = null ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}
		if ( ! $post_id ) {
			return Abstract_Token_Subscription_Service::POST_ACCESS_LEVEL_EVERYBODY;
		}

		$blog_id   = get_current_blog_id();
		$cache_key = $blog_id . '_' . $post_id;

		if ( isset( self::$post_access_level_cache[ $cache_key ] ) ) {
			return self::$post_access_level_cache[ $cache_key ];
		}

		$post_access_level = get_post_meta( $post_id, self::$post_access_level_meta_name, true );
		if ( empty( $post_access_level ) ) {
			$post_access_level = Abstract_Token_Subscription_Service::POST_ACCESS_LEVEL_EVERYBODY;
		}

		self::$post_access_level_cache[ $cache_key ] = $post_access_level;

		return $post_access_level;
	}

	/**
	 * Get the post tier plan
	 *
	 * If no ID is provided, the method tries to get it from the global post object.
	 *
	 * @param int|null $post_id The ID of the post. Default is null.
	 *
	 * @return WP_Post|null the actual post tier.
	 */
	public static function get_post_tier( $post_id = null ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		if ( ! $post_id ) {
			return null;
		}

		$post_tier_id = get_post_meta( $post_id, self::$post_access_tier_meta_name, true );
		if ( empty( $post_tier_id ) ) {
			return null;
		}

		return get_post( $post_tier_id );
	}

	/**
	 * Determines whether the current user can edit.
	 *
	 * @return bool Whether the user can edit.
	 */
	public static function user_can_edit() {
		$user = wp_get_current_user();
		// phpcs:ignore ImportDetection.Imports.RequireImports.Symbol
		return 0 !== $user->ID && current_user_can( 'edit_post', get_the_ID() );
	}

	/**
	 * Clears the static cache for all users or for a given user.
	 *
	 * @param int|null $user_id The user_id to unset in the cache, otherwise the entire static cache is cleared.
	 * @return void
	 */
	public static function clear_cache( ?int $user_id = null ) {
		if ( empty( $user_id ) ) {
			self::$user_is_paid_subscriber_cache = array();
			self::$user_can_view_post_cache      = array();
			return;
		}
		unset( self::$user_is_paid_subscriber_cache[ $user_id ] );
		unset( self::$user_can_view_post_cache[ $user_id ] );
	}

	/**
	 * Determines whether the current user is a paid subscriber and caches the result.
	 *
	 * @param array    $valid_plan_ids An array of valid plan ids that the user could be subscribed to which would make the user able to view this content. Defaults to an empty array which will be filled with all newsletter plan IDs.
	 * @param int|null $user_id An optional user_id that can be used to determine service availability (defaults to checking if user is logged in if omitted).
	 * @return bool Whether the post can be viewed
	 */
	public static function user_is_paid_subscriber( $valid_plan_ids = array(), $user_id = null ) {
		if ( empty( $user_id ) ) {
			$user_id = get_current_user_id();
			if ( empty( $user_id ) ) {
				return false;
			}
		}
		// sort and stringify sorted valid plan ids to use as a cache key
		sort( $valid_plan_ids );
		$cache_key = $user_id . '_' . implode( ',', $valid_plan_ids );
		if ( ! isset( self::$user_is_paid_subscriber_cache[ $cache_key ] ) ) {
			require_once JETPACK__PLUGIN_DIR . 'extensions/blocks/premium-content/_inc/subscription-service/include.php';
			if ( empty( $valid_plan_ids ) ) {
				$valid_plan_ids = self::get_all_newsletter_plan_ids();
			}
			$paywall            = \Automattic\Jetpack\Extensions\Premium_Content\subscription_service( $user_id );
			$is_paid_subscriber = $paywall->visitor_can_view_content( $valid_plan_ids, Abstract_Token_Subscription_Service::POST_ACCESS_LEVEL_PAID_SUBSCRIBERS );
			self::$user_is_paid_subscriber_cache[ $cache_key ] = $is_paid_subscriber;
		}
		return self::$user_is_paid_subscriber_cache[ $cache_key ];
	}

	/**
	 * Determines whether the current user has a pending subscription.
	 *
	 * @return bool Whether the user has a pending subscription
	 */
	public static function user_is_pending_subscriber() {
		require_once JETPACK__PLUGIN_DIR . 'extensions/blocks/premium-content/_inc/subscription-service/include.php';
		$subscription_service = \Automattic\Jetpack\Extensions\Premium_Content\subscription_service();
		return $subscription_service->is_current_user_pending_subscriber();
	}

	/**
	 * Determines whether the current user can view the post based on the newsletter access level
	 * and caches the result.
	 *
	 * @param int|null $post_id Explicit post id to check against.
	 *
	 * @return bool Whether the post can be viewed
	 */
	public static function user_can_view_post( $post_id = null ) {
		$user_id = get_current_user_id();
		if ( null === $post_id ) {
			$post_id = get_the_ID();
		}

		if ( false === $post_id ) {
			$post_id = 0;
		}

		$cache_key = sprintf( '%d_%d', $user_id, $post_id );
		if ( isset( self::$user_can_view_post_cache[ $cache_key ] ) ) {
			return self::$user_can_view_post_cache[ $cache_key ];
		}

		$post_access_level = self::get_post_access_level( $post_id );
		if ( Abstract_Token_Subscription_Service::POST_ACCESS_LEVEL_EVERYBODY === $post_access_level ) {
			self::$user_can_view_post_cache[ $cache_key ] = true;
			return true;
		}

		// we are sending the post to subscribers so the user is a subscriber
		if ( defined( 'WPCOM_SENDING_POST_TO_SUBSCRIBERS' ) && WPCOM_SENDING_POST_TO_SUBSCRIBERS && Abstract_Token_Subscription_Service::POST_ACCESS_LEVEL_SUBSCRIBERS === $post_access_level ) {
			self::$user_can_view_post_cache[ $cache_key ] = true;
			return true;
		}

		require_once JETPACK__PLUGIN_DIR . 'extensions/blocks/premium-content/_inc/subscription-service/include.php';
		$paywall = \Automattic\Jetpack\Extensions\Premium_Content\subscription_service();

		$all_newsletters_plan_ids = self::get_all_newsletter_plan_ids();

		if ( 0 === count( $all_newsletters_plan_ids ) &&
			Abstract_Token_Subscription_Service::POST_ACCESS_LEVEL_PAID_SUBSCRIBERS === $post_access_level ||
			Abstract_Token_Subscription_Service::POST_ACCESS_LEVEL_PAID_SUBSCRIBERS_ALL_TIERS === $post_access_level
		) {
			// The post is paywalled but there is no newsletter plans on the site.
			// We downgrade the post level to subscribers-only
			$post_access_level = Abstract_Token_Subscription_Service::POST_ACCESS_LEVEL_SUBSCRIBERS;
		}

		$can_view_post = $paywall->visitor_can_view_content( $all_newsletters_plan_ids, $post_access_level );

		self::$user_can_view_post_cache[ $cache_key ] = $can_view_post;
		return $can_view_post;
	}

	/**
	 * Whether Recurring Payments are enabled. True if the block
	 * is supported by the site's plan, or if it is a Jetpack site
	 * and the feature to enable upgrade nudges is active.
	 *
	 * @return bool
	 */
	public static function is_enabled_jetpack_recurring_payments() {
		$api_available = ( new Host() )->is_wpcom_simple() || Jetpack::is_connection_ready();
		return $api_available;
	}

	/**
	 * Whether to enable the blocks in the editor.
	 * All Monetize blocks (except Simple Payments) need a user with at least `edit_posts` capability
	 *
	 * @return bool
	 */
	public static function should_enable_monetize_blocks_in_editor() {
		if ( ! is_admin() ) {
			// We enable the block for the front-end in all cases
			return true;

		}

		$is_offline_mode                  = ( new Status() )->is_offline_mode();
		$enable_monetize_blocks_in_editor = ( new Host() )->is_wpcom_simple() || ( ! $is_offline_mode );
		return $enable_monetize_blocks_in_editor;
	}

	/**
	 * Whether site has any paid plan.
	 *
	 * @param string $type - Type of a plan for which site is configured. For now supports empty and newsletter.
	 *
	 * @return bool
	 */
	public static function has_configured_plans_jetpack_recurring_payments( $type = '' ) {
		if ( ! self::is_enabled_jetpack_recurring_payments() ) {
			return false;
		}
		$query = array(
			'post_type'      => self::$post_type_plan,
			'posts_per_page' => 1,
		);

		// We want to see if user has any plan marked as a newsletter set up.
		if ( 'newsletter' === $type ) {
			$query['meta_key']   = 'jetpack_memberships_site_subscriber';
			$query['meta_value'] = true;
		}

		$plans = get_posts( $query );
		return ( is_countable( $plans ) && count( $plans ) > 0 );
	}

	/**
	 * Return the list of plan posts
	 *
	 * @return WP_Post[]|WP_Error
	 */
	public static function get_all_plans() {
		if ( ! self::is_enabled_jetpack_recurring_payments() ) {
			return array();
		}

		// We can retrieve the data directly except on a Jetpack/Atomic cached site or
		$is_cached_site = ( new Host() )->is_wpcom_simple() && is_jetpack_site();
		if ( ! $is_cached_site ) {
			return get_posts(
				array(
					'posts_per_page' => -1,
					'post_type'      => self::$post_type_plan,
				)
			);
		} else {
			// On cached site on WPCOM
			require_lib( 'memberships' );
			return Memberships_Product::get_plans_posts_list( get_current_blog_id() );
		}
	}

	/**
	 * Return all membership plans ids (deleted or not)
	 * This function is used both on WPCOM or on Jetpack self-hosted.
	 * Depending on the environment we need to mitigate where the data is retrieved from.
	 *
	 * @param bool $allow_deleted Whether to allow deleted plans to be returned. Defaults to true.
	 *
	 * @return array
	 */
	public static function get_all_newsletter_plan_ids( $allow_deleted = true ) {

		if ( ! self::is_enabled_jetpack_recurring_payments() ) {
			return array();
		}

		// We can retrieve the data directly except on a Jetpack/Atomic cached site or
		$is_cached_site = ( new Host() )->is_wpcom_simple() && is_jetpack_site();
		if ( ! $is_cached_site ) {
			$meta_query = array(
				array(
					'key'   => 'jetpack_memberships_type',
					'value' => self::$type_tier,
				),
			);

			if ( $allow_deleted === false ) {
				$meta_query[] = array(
					'key'     => 'jetpack_memberships_is_deleted',
					'compare' => 'NOT EXISTS',
				);
			}

			return get_posts(
				array(
					'posts_per_page' => -1,
					'fields'         => 'ids',
					'post_type'      => self::$post_type_plan,
					'meta_query'     => $meta_query,
				)
			);

		} else {
			// On cached site on WPCOM
			require_lib( 'memberships' );
			$list = Memberships_Product::get_product_list( get_current_blog_id(), self::$type_tier, null, $allow_deleted );

			if ( is_wp_error( $list ) ) {
				return array();
			}

			return array_map(
				function ( $product ) {
					return $product['id'];
				}, // Returning only post ids
				$list
			);
		}
	}

	/**
	 * Register the Recurring Payments Gutenberg block
	 */
	public function register_gutenberg_block() {
		// This gate was introduced to prevent duplicate registration. A race condition exists where
		// the registration that happens via extensions/blocks/recurring-payments/recurring-payments.php
		// was adding the registration action after the action had been run in some contexts.
		if ( self::$has_registered_block ) {
			return;
		}

		if ( self::is_enabled_jetpack_recurring_payments() ) {
			Blocks::jetpack_register_block(
				'jetpack/recurring-payments',
				array(
					'render_callback'  => array( $this, 'render_button' ),
					'uses_context'     => array( 'isPremiumContentChild' ),
					'provides_context' => array(
						'jetpack/parentBlockWidth' => 'width',
					),
				)
			);
		} else {
			Jetpack_Gutenberg::set_extension_unavailable(
				'recurring-payments',
				'missing_plan',
				array(
					'required_feature' => 'memberships',
					'required_plan'    => self::$required_plan,
				)
			);
		}

		self::$has_registered_block = true;
	}

	/**
	 * Transforms a number into it's short human-readable version.
	 *
	 * @param int $subscribers_total The extrapolated excerpt string.
	 *
	 * @return string Human-readable version of the number. ie. 1.9 M.
	 */
	public static function get_join_others_text( $subscribers_total ) {
		if ( $subscribers_total >= 1000000 ) {
			/* translators: %s: number of folks following the blog, millions(M) with one decimal. i.e. 1.1 */
			return sprintf( __( 'Join %sM other subscribers', 'jetpack' ), floatval( number_format_i18n( $subscribers_total / 1000000, 1 ) ) );
		}
		if ( $subscribers_total >= 10000 ) {
			/* translators: %s: number of folks following the blog, thousands(K) with one decimal. i.e. 1.1 */
			return sprintf( __( 'Join %sK other subscribers', 'jetpack' ), floatval( number_format_i18n( $subscribers_total / 1000, 1 ) ) );
		}

		/* translators: %s: number of folks following the blog */
		return sprintf( _n( 'Join %s other subscriber', 'Join %s other subscribers', $subscribers_total, 'jetpack' ), number_format_i18n( $subscribers_total ) );
	}

	/**
	 * Returns the email of the current user.
	 *
	 * @return string
	 */
	public static function get_current_user_email() {
		require_once JETPACK__PLUGIN_DIR . 'extensions/blocks/premium-content/_inc/subscription-service/include.php';
		$subscription_service = \Automattic\Jetpack\Extensions\Premium_Content\subscription_service();
		return $subscription_service->get_subscriber_email();
	}

	/**
	 * Returns if the current user is subscribed or not.
	 *
	 * @return boolean
	 */
	public static function is_current_user_subscribed() {
		require_once JETPACK__PLUGIN_DIR . 'extensions/blocks/premium-content/_inc/subscription-service/include.php';
		$subscription_service = \Automattic\Jetpack\Extensions\Premium_Content\subscription_service();
		return $subscription_service->is_current_user_subscribed();
	}
}
Jetpack_Memberships::get_instance();
