<?php
/**
 * Loader - Hooks and filters for WP AI Control.
 */

class WPAIC_Loader {

	private $actions = array();
	private $filters = array();

	public function __construct() {
		$this->load_dependencies();
		$this->define_hooks();
	}

	private function load_dependencies() {
		require_once WPAIC_PLUGIN_DIR . 'includes/class-wpaic-api.php';
		
		if ( is_admin() ) {
			require_once WPAIC_PLUGIN_DIR . 'includes/admin/class-wpaic-admin.php';
		}
	}

	private function define_hooks() {
		$this->api = new WPAIC_API();
		add_action( 'rest_api_init', array( $this->api, 'register_routes' ) );
		
		if ( is_admin() ) {
			$this->admin = new WPAIC_Admin();
			add_action( 'admin_menu', array( $this->admin, 'add_admin_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $this->admin, 'enqueue_scripts' ) );
		}
	}

	public function run() {
		foreach ( $this->filters as $hook ) {
			add_filter( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}

		foreach ( $this->actions as $hook ) {
			add_action( $hook['hook'], array( $hook['component'], $hook['callback'] ), $hook['priority'], $hook['accepted_args'] );
		}
	}

	private function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions[] = compact( 'hook', 'component', 'callback', 'priority', 'accepted_args' );
	}

	private function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters[] = compact( 'hook', 'component', 'callback', 'priority', 'accepted_args' );
	}
}