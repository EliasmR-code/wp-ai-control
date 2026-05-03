<?php
/**
 * Loader - Hooks and filters for WP AI Control.
 *
 * @package WP_AI_Control
 * @subpackage WP_AI_Control/includes
 */

class WPAIC_Loader {

	private $actions;
	private $filters;
	private $api;
	private $auth;
	private $admin;

	public function __construct() {
		$this->actions = array();
		$this->filters = array();

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	private function load_dependencies() {
		require_once WPAIC_PLUGIN_DIR . 'includes/class-wpaic-activator.php';
		require_once WPAIC_PLUGIN_DIR . 'includes/class-wpaic-deactivator.php';
		require_once WPAIC_PLUGIN_DIR . 'includes/class-wpaic-auth.php';
		require_once WPAIC_PLUGIN_DIR . 'includes/class-wpaic-audit.php';
		require_once WPAIC_PLUGIN_DIR . 'includes/class-wpaic-context.php';
		require_once WPAIC_PLUGIN_DIR . 'includes/class-wpaic-api.php';

		if ( class_exists( 'WooCommerce' ) ) {
			require_once WPAIC_PLUGIN_DIR . 'includes/class-wpaic-woocommerce.php';
		}

		if ( function_exists( 'acf' ) || class_exists( 'ACF' ) ) {
			require_once WPAIC_PLUGIN_DIR . 'includes/class-wpaic-acf.php';
		}

		require_once WPAIC_PLUGIN_DIR . 'includes/class-wpaic-widgets.php';

		require_once WPAIC_PLUGIN_DIR . 'includes/class-wpaic-webmcp.php';

		if ( is_admin() ) {
			require_once WPAIC_PLUGIN_DIR . 'includes/admin/class-wpaic-admin.php';
		}
	}

	private function define_admin_hooks() {
		if ( ! is_admin() ) {
			return;
		}

		$this->admin = new WPAIC_Admin();

		$this->add_action( 'admin_menu', $this->admin, 'add_admin_menu' );
		$this->add_action( 'admin_enqueue_scripts', $this->admin, 'enqueue_scripts' );
	}

	private function define_public_hooks() {
		$this->api = new WPAIC_API();

		$this->add_action( 'rest_api_init', $this->api, 'register_routes' );

		if ( class_exists( 'WooCommerce' ) ) {
			$this->add_action( 'rest_api_init', 'WPAIC_WooCommerce', 'register_routes' );
		}

		if ( function_exists( 'acf' ) || class_exists( 'ACF' ) ) {
			$this->add_action( 'rest_api_init', 'WPAIC_ACF', 'register_routes' );
		}

		$this->add_action( 'rest_api_init', 'WPAIC_Widgets', 'register_routes' );

		$this->add_action( 'rest_api_init', 'WPAIC_WebMCP', 'register_routes' );

		$this->add_action( 'wpaic_purge_audit_log', 'WPAIC_Audit', 'purge_old_entries' );
	}

	public function run() {
		foreach ( $this->filters as $hook ) {
			add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

		foreach ( $this->actions as $hook ) {
			add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

		if ( ! wp_next_scheduled( 'wpaic_purge_audit_log' ) ) {
			wp_schedule_event( time(), 'daily', 'wpaic_purge_audit_log' );
		}
	}

	private function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);
	}

	private function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters[] = array(
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		);
	}
}
