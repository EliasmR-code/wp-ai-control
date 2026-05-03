<?php
/**
 * REST API for WP AI Control.
 *
 * @package WP_AI_Control
 * @subpackage WP_AI_Control/includes
 */

class WPAIC_API {

	public function __construct() {}

	public function register_routes() {
		// API key management
		register_rest_route( WPAIC_REST_NAMESPACE, '/auth/generate-key', array(
			'methods' => 'POST',
			'callback' => array( $this, 'generate_api_key' ),
			'permission_callback' => array( $this, 'admin_permission_check' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/auth/validate-key', array(
			'methods' => 'POST',
			'callback' => array( $this, 'validate_api_key' ),
			'permission_callback' => '__return_true',
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/auth/revoke-key/(?P<key_id>\d+)', array(
			'methods' => 'DELETE',
			'callback' => array( $this, 'revoke_api_key' ),
			'permission_callback' => array( $this, 'admin_permission_check' ),
			'args' => array( 'key_id' => array( 'required' => true, 'validate_callback' => 'is_numeric' ) ),
		));

		// Usage & Plan
		register_rest_route( WPAIC_REST_NAMESPACE, '/usage', array(
			'methods' => 'GET',
			'callback' => array( $this, 'get_usage' ),
			'permission_callback' => array( $this, 'api_key_permission_check_read' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/plan-info', array(
			'methods' => 'GET',
			'callback' => array( $this, 'get_plan_info' ),
			'permission_callback' => array( $this, 'api_key_permission_check_read' ),
		));

		// Context
		register_rest_route( WPAIC_REST_NAMESPACE, '/site-info', array(
			'methods' => 'GET',
			'callback' => array( $this, 'get_site_info' ),
			'permission_callback' => array( $this, 'api_key_permission_check_read' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/builder-info', array(
			'methods' => 'GET',
			'callback' => array( $this, 'get_builder_info' ),
			'permission_callback' => array( $this, 'api_key_permission_check_read' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/theme-docs', array(
			'methods' => 'GET',
			'callback' => array( $this, 'get_theme_docs' ),
			'permission_callback' => array( $this, 'api_key_permission_check_read' ),
		));

		// Pages
		register_rest_route( WPAIC_REST_NAMESPACE, '/pages', array(
			'methods' => 'GET',
			'callback' => array( $this, 'get_pages' ),
			'permission_callback' => array( $this, 'api_key_permission_check_read' ),
			'args' => $this->get_collection_params(),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/pages/(?P<id>\d+)', array(
			'methods' => 'GET',
			'callback' => array( $this, 'get_page' ),
			'permission_callback' => array( $this, 'api_key_permission_check_read' ),
			'args' => array( 'id' => array( 'required' => true, 'validate_callback' => 'is_numeric' ) ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/pages/(?P<id>\d+)', array(
			'methods' => 'PUT',
			'callback' => array( $this, 'update_page' ),
			'permission_callback' => array( $this, 'api_key_permission_check_write' ),
			'args' => array(
				'id' => array( 'required' => true, 'validate_callback' => 'is_numeric' ),
				'title' => array( 'required' => false, 'sanitize_callback' => 'sanitize_text_field' ),
				'content' => array( 'required' => false, 'sanitize_callback' => 'wp_kses_post' ),
				'status' => array( 'required' => false, 'sanitize_callback' => 'sanitize_text_field' ),
			),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/pages/(?P<id>\d+)', array(
			'methods' => 'DELETE',
			'callback' => array( $this, 'delete_page' ),
			'permission_callback' => array( $this, 'api_key_permission_check_write' ),
			'args' => array( 'id' => array( 'required' => true, 'validate_callback' => 'is_numeric' ) ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/pages/(?P<id>\d+)/duplicate', array(
			'methods' => 'POST',
			'callback' => array( $this, 'duplicate_page' ),
			'permission_callback' => array( $this, 'api_key_permission_check_write' ),
			'args' => array( 'id' => array( 'required' => true, 'validate_callback' => 'is_numeric' ) ),
		));

		// Posts
		register_rest_route( WPAIC_REST_NAMESPACE, '/posts', array(
			'methods' => 'GET',
			'callback' => array( $this, 'get_posts' ),
			'permission_callback' => array( $this, 'api_key_permission_check_read' ),
			'args' => $this->get_collection_params(),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/posts/(?P<id>\d+)', array(
			'methods' => 'GET',
			'callback' => array( $this, 'get_post' ),
			'permission_callback' => array( $this, 'api_key_permission_check_read' ),
			'args' => array( 'id' => array( 'required' => true, 'validate_callback' => 'is_numeric' ) ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/posts/(?P<id>\d+)', array(
			'methods' => 'PUT',
			'callback' => array( $this, 'update_post' ),
			'permission_callback' => array( $this, 'api_key_permission_check_write' ),
			'args' => array(
				'id' => array( 'required' => true, 'validate_callback' => 'is_numeric' ),
				'title' => array( 'required' => false, 'sanitize_callback' => 'sanitize_text_field' ),
				'content' => array( 'required' => false, 'sanitize_callback' => 'wp_kses_post' ),
				'status' => array( 'required' => false, 'sanitize_callback' => 'sanitize_text_field' ),
			),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/posts/(?P<id>\d+)', array(
			'methods' => 'DELETE',
			'callback' => array( $this, 'delete_post' ),
			'permission_callback' => array( $this, 'api_key_permission_check_write' ),
			'args' => array( 'id' => array( 'required' => true, 'validate_callback' => 'is_numeric' ) ),
		));

		// Builder
		register_rest_route( WPAIC_REST_NAMESPACE, '/builder/(?P<builder>[a-zA-Z0-9-_]+)/extract/(?P<page_id>\d+)', array(
			'methods' => 'GET',
			'callback' => array( $this, 'extract_builder_content' ),
			'permission_callback' => array( $this, 'api_key_permission_check_read' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/builder/(?P<builder>[a-zA-Z0-9-_]+)/inject/(?P<page_id>\d+)', array(
			'methods' => 'POST',
			'callback' => array( $this, 'inject_builder_content' ),
			'permission_callback' => array( $this, 'api_key_permission_check_write' ),
		));

		// Media
		register_rest_route( WPAIC_REST_NAMESPACE, '/media/upload', array(
			'methods' => 'POST',
			'callback' => array( $this, 'upload_media' ),
			'permission_callback' => array( $this, 'api_key_permission_check_write' ),
		));

		// Analysis
		register_rest_route( WPAIC_REST_NAMESPACE, '/analyze/seo/(?P<page_id>\d+)', array(
			'methods' => 'GET',
			'callback' => array( $this, 'analyze_seo' ),
			'permission_callback' => array( $this, 'api_key_permission_check_read' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/analyze/performance/(?P<page_id>\d+)', array(
			'methods' => 'GET',
			'callback' => array( $this, 'analyze_performance' ),
			'permission_callback' => array( $this, 'api_key_permission_check_read' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/analyze/aeo/(?P<page_id>\d+)', array(
			'methods' => 'GET',
			'callback' => array( $this, 'analyze_aeo' ),
			'permission_callback' => array( $this, 'api_key_permission_check_read' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/analyze/accessibility/(?P<page_id>\d+)', array(
			'methods' => 'GET',
			'callback' => array( $this, 'analyze_accessibility' ),
			'permission_callback' => array( $this, 'api_key_permission_check_read' ),
		));

		// Plugins
		register_rest_route( WPAIC_REST_NAMESPACE, '/plugins', array(
			'methods' => 'GET',
			'callback' => array( $this, 'list_plugins' ),
			'permission_callback' => array( $this, 'api_key_permission_check_read' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/plugins/install', array(
			'methods' => 'POST',
			'callback' => array( $this, 'install_plugin' ),
			'permission_callback' => array( $this, 'api_key_permission_check_write' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/plugins/(?P<slug>[a-zA-Z0-9-_]+)/activate', array(
			'methods' => 'POST',
			'callback' => array( $this, 'activate_plugin' ),
			'permission_callback' => array( $this, 'api_key_permission_check_write' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/plugins/(?P<slug>[a-zA-Z0-9-_]+)/deactivate', array(
			'methods' => 'POST',
			'callback' => array( $this, 'deactivate_plugin' ),
			'permission_callback' => array( $this, 'api_key_permission_check_write' ),
		));

		// Menus
		register_rest_route( WPAIC_REST_NAMESPACE, '/menus', array(
			'methods' => 'GET',
			'callback' => array( $this, 'list_menus' ),
			'permission_callback' => array( $this, 'api_key_permission_check_read' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/menus/(?P<id>\d+)', array(
			'methods' => 'GET',
			'callback' => array( $this, 'get_menu' ),
			'permission_callback' => array( $this, 'api_key_permission_check_read' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/menus/locations', array(
			'methods' => 'GET',
			'callback' => array( $this, 'list_menu_locations' ),
			'permission_callback' => array( $this, 'api_key_permission_check_read' ),
		));

		// Taxonomies
		register_rest_route( WPAIC_REST_NAMESPACE, '/taxonomies', array(
			'methods' => 'GET',
			'callback' => array( $this, 'list_taxonomies' ),
			'permission_callback' => array( $this, 'api_key_permission_check_read' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/taxonomies/(?P<taxonomy>[a-zA-Z0-9_-]+)/terms', array(
			'methods' => 'GET',
			'callback' => array( $this, 'list_terms' ),
			'permission_callback' => array( $this, 'api_key_permission_check_read' ),
		));

		// Snapshots
		register_rest_route( WPAIC_REST_NAMESPACE, '/snapshots', array(
			'methods' => 'GET',
			'callback' => array( $this, 'list_snapshots' ),
			'permission_callback' => array( $this, 'api_key_permission_check_read' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/snapshots/rollback', array(
			'methods' => 'POST',
			'callback' => array( $this, 'restore_snapshot' ),
			'permission_callback' => array( $this, 'api_key_permission_check_write' ),
		));

		// Users
		register_rest_route( WPAIC_REST_NAMESPACE, '/users', array(
			'methods' => 'GET',
			'callback' => array( $this, 'list_users' ),
			'permission_callback' => array( $this, 'api_key_permission_check_read' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/users/(?P<id>\d+)', array(
			'methods' => 'GET',
			'callback' => array( $this, 'get_user' ),
			'permission_callback' => array( $this, 'api_key_permission_check_read' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/users', array(
			'methods' => 'POST',
			'callback' => array( $this, 'create_user' ),
			'permission_callback' => array( $this, 'api_key_permission_check_write' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/users/(?P<id>\d+)', array(
			'methods' => 'PUT',
			'callback' => array( $this, 'update_user' ),
			'permission_callback' => array( $this, 'api_key_permission_check_write' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/users/(?P<id>\d+)', array(
			'methods' => 'DELETE',
			'callback' => array( $this, 'delete_user' ),
			'permission_callback' => array( $this, 'api_key_permission_check_write' ),
		));

		// Comments
		register_rest_route( WPAIC_REST_NAMESPACE, '/comments', array(
			'methods' => 'GET',
			'callback' => array( $this, 'list_comments' ),
			'permission_callback' => array( $this, 'api_key_permission_check_read' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/comments/(?P<id>\d+)', array(
			'methods' => 'GET',
			'callback' => array( $this, 'get_comment' ),
			'permission_callback' => array( $this, 'api_key_permission_check_read' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/comments/(?P<id>\d+)/approve', array(
			'methods' => 'POST',
			'callback' => array( $this, 'approve_comment' ),
			'permission_callback' => array( $this, 'api_key_permission_check_write' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/comments/(?P<id>\d+)/spam', array(
			'methods' => 'POST',
			'callback' => array( $this, 'spam_comment' ),
			'permission_callback' => array( $this, 'api_key_permission_check_write' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/comments/(?P<id>\d+)', array(
			'methods' => 'DELETE',
			'callback' => array( $this, 'delete_comment' ),
			'permission_callback' => array( $this, 'api_key_permission_check_write' ),
		));

		// Media additional
		register_rest_route( WPAIC_REST_NAMESPACE, '/media', array(
			'methods' => 'GET',
			'callback' => array( $this, 'list_media' ),
			'permission_callback' => array( $this, 'api_key_permission_check_read' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/media/(?P<id>\d+)', array(
			'methods' => 'DELETE',
			'callback' => array( $this, 'delete_media' ),
			'permission_callback' => array( $this, 'api_key_permission_check_write' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/media/(?P<id>\d+)/meta', array(
			'methods' => 'PUT',
			'callback' => array( $this, 'update_media_meta' ),
			'permission_callback' => array( $this, 'api_key_permission_check_write' ),
		));

		// Terms CRUD
		register_rest_route( WPAIC_REST_NAMESPACE, '/terms', array(
			'methods' => 'POST',
			'callback' => array( $this, 'create_term' ),
			'permission_callback' => array( $this, 'api_key_permission_check_write' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/terms/(?P<id>\d+)', array(
			'methods' => 'GET',
			'callback' => array( $this, 'get_term' ),
			'permission_callback' => array( $this, 'api_key_permission_check_read' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/terms/(?P<id>\d+)', array(
			'methods' => 'PUT',
			'callback' => array( $this, 'update_term' ),
			'permission_callback' => array( $this, 'api_key_permission_check_write' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/terms/(?P<id>\d+)', array(
			'methods' => 'DELETE',
			'callback' => array( $this, 'delete_term' ),
			'permission_callback' => array( $this, 'api_key_permission_check_write' ),
		));

		// Site Settings
		register_rest_route( WPAIC_REST_NAMESPACE, '/settings', array(
			'methods' => 'GET',
			'callback' => array( $this, 'get_site_settings' ),
			'permission_callback' => array( $this, 'api_key_permission_check_read' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/settings', array(
			'methods' => 'PUT',
			'callback' => array( $this, 'update_site_settings' ),
			'permission_callback' => array( $this, 'api_key_permission_check_write' ),
		));

		// Themes
		register_rest_route( WPAIC_REST_NAMESPACE, '/themes', array(
			'methods' => 'GET',
			'callback' => array( $this, 'list_themes' ),
			'permission_callback' => array( $this, 'api_key_permission_check_read' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/themes/(?P<slug>[a-zA-Z0-9-_]+)/activate', array(
			'methods' => 'POST',
			'callback' => array( $this, 'activate_theme' ),
			'permission_callback' => array( $this, 'api_key_permission_check_write' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/themes/(?P<slug>[a-zA-Z0-9-_]+)/update', array(
			'methods' => 'POST',
			'callback' => array( $this, 'update_theme' ),
			'permission_callback' => array( $this, 'api_key_permission_check_write' ),
		));

		// Post Meta
		register_rest_route( WPAIC_REST_NAMESPACE, '/posts/(?P<id>\d+)/meta', array(
			'methods' => 'GET',
			'callback' => array( $this, 'list_post_meta' ),
			'permission_callback' => array( $this, 'api_key_permission_check_read' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/posts/(?P<id>\d+)/meta', array(
			'methods' => 'POST',
			'callback' => array( $this, 'update_post_meta' ),
			'permission_callback' => array( $this, 'api_key_permission_check_write' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/posts/(?P<id>\d+)/meta/(?P<meta_key>.+)', array(
			'methods' => 'DELETE',
			'callback' => array( $this, 'delete_post_meta' ),
			'permission_callback' => array( $this, 'api_key_permission_check_write' ),
		));

		// Search
		register_rest_route( WPAIC_REST_NAMESPACE, '/search', array(
			'methods' => 'GET',
			'callback' => array( $this, 'search_content' ),
			'permission_callback' => array( $this, 'api_key_permission_check_read' ),
		));

		// Widgets
		register_rest_route( WPAIC_REST_NAMESPACE, '/widgets', array(
			'methods' => 'GET',
			'callback' => array( $this, 'list_widgets' ),
			'permission_callback' => array( $this, 'api_key_permission_check_read' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/widgets/(?P<id>\d+)', array(
			'methods' => 'PUT',
			'callback' => array( $this, 'update_widget' ),
			'permission_callback' => array( $this, 'api_key_permission_check_write' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/sidebars', array(
			'methods' => 'GET',
			'callback' => array( $this, 'list_sidebars' ),
			'permission_callback' => array( $this, 'api_key_permission_check_read' ),
		));

		// Bulk Operations
		register_rest_route( WPAIC_REST_NAMESPACE, '/bulk-update-posts', array(
			'methods' => 'POST',
			'callback' => array( $this, 'bulk_update_posts' ),
			'permission_callback' => array( $this, 'api_key_permission_check_write' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/bulk-delete-posts', array(
			'methods' => 'POST',
			'callback' => array( $this, 'bulk_delete_posts' ),
			'permission_callback' => array( $this, 'api_key_permission_check_write' ),
		));
	}

	// Permission checks
	public function admin_permission_check() {
		return current_user_can( 'manage_options' );
	}

	public function api_key_permission_check( $request, $required_perm = 'read' ) {
		$api_key = $request->get_header( 'x-wpaic-api-key' );

		if ( empty( $api_key ) ) {
			return new WP_Error( 'wpaic_missing_auth', 'API key required in X-WPAIC-API-Key header.', array( 'status' => 401 ) );
		}

		$key_record = WPAIC_Auth::validate_api_key( $api_key );

		if ( ! $key_record ) {
			return new WP_Error( 'wpaic_invalid_key', 'Invalid API key.', array( 'status' => 401 ) );
		}

		// Rate limiting
		if ( ! WPAIC_Auth::check_rate_limit( $key_record->api_key ) ) {
			return new WP_Error( 'wpaic_rate_limit', 'Rate limit exceeded. Max 60 requests per minute.', array( 'status' => 429 ) );
		}

		// Check permissions
		if ( ! WPAIC_Auth::check_permission( $key_record, $required_perm ) ) {
			return new WP_Error( 'wpaic_insufficient_permissions', 'Insufficient permissions.', array( 'status' => 403 ) );
		}

		return true;
	}

	public function api_key_permission_check_read( $request ) {
		return $this->api_key_permission_check( $request, 'read' );
	}

	public function api_key_permission_check_write( $request ) {
		return $this->api_key_permission_check( $request, 'write' );
	}

	// API Key endpoints
	public function generate_api_key( $request ) {
		$user_id = get_current_user_id();
		$name = $request->get_param( 'name' );
		$permissions = $request->get_param( 'permissions' );

		$api_key = WPAIC_Auth::generate_api_key( $user_id, $name, $permissions );

		if ( is_wp_error( $api_key ) ) {
			return $api_key;
		}

		return new WP_REST_Response( array(
			'success' => true,
			'message' => 'API key generated. Save this key - it will not be shown again.',
			'api_key' => $api_key,
			'header' => 'X-WPAIC-API-Key: ' . $api_key,
		), 201 );
	}

	public function validate_api_key( $request ) {
		$api_key = $request->get_param( 'api_key' );

		if ( empty( $api_key ) ) {
			return new WP_Error( 'wpaic_missing_key', 'API key is required.', array( 'status' => 400 ) );
		}

		$valid = WPAIC_Auth::validate_api_key( $api_key );

		if ( ! $valid ) {
			return new WP_Error( 'wpaic_invalid_key', 'Invalid API key.', array( 'status' => 401 ) );
		}

		return new WP_REST_Response( array(
			'success' => true,
			'key' => array(
				'id' => $valid->id,
				'name' => $valid->name,
				'permissions' => json_decode( $valid->permissions ),
				'created_at' => $valid->created_at,
				'last_used' => $valid->last_used,
			),
		), 200 );
	}

	public function revoke_api_key( $request ) {
		$key_id = $request->get_param( 'key_id' );
		$user_id = get_current_user_id();

		$result = WPAIC_Auth::revoke_api_key( $key_id, $user_id );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return new WP_REST_Response( array( 'success' => true, 'message' => 'API key revoked.' ), 200 );
	}

	// Usage & Plan
	public function get_usage() {
		$usage = json_decode( get_option( 'wpaic_usage_data', '{"count":0,"reset_date":"' . date( 'Y-m-01' ) . '"}' ), true );

		return new WP_REST_Response( array(
			'success' => true,
			'data' => array(
				'count' => (int) $usage['count'],
				'reset_date' => $usage['reset_date'],
				'limit' => 'unlimited',
				'plan' => 'local',
			),
		), 200 );
	}

	public function get_plan_info() {
		return new WP_REST_Response( array(
			'success' => true,
			'data' => array(
				'plan' => 'WP AI Control (Local)',
				'version' => WPAIC_VERSION,
				'tier' => 'local',
				'monthly_limit' => 'unlimited',
				'features' => array(
					'pages_crud' => true,
					'posts_crud' => true,
					'builder_support' => true,
					'media_upload' => true,
					'analysis' => true,
					'plugin_management' => true,
					'menu_management' => true,
					'snapshots' => true,
				),
			),
		), 200 );
	}

	// Context endpoints
	public function get_site_info() {
		return new WP_REST_Response( array(
			'success' => true,
			'data' => WPAIC_Context::get_site_info(),
		), 200 );
	}

	public function get_theme_docs() {
		return new WP_REST_Response( array(
			'success' => true,
			'data' => WPAIC_Context::get_theme_docs(),
		), 200 );
	}

	public function get_builder_info() {
		return new WP_REST_Response( array(
			'success' => true,
			'data' => WPAIC_Context::get_builder_info(),
		), 200 );
	}

	// Pages
	public function get_pages( $request ) {
		$per_page = $request->get_param( 'per_page' ) ?: 10;
		$page = $request->get_param( 'page' ) ?: 1;
		$search = $request->get_param( 'search' );
		$status = $request->get_param( 'status' ) ?: 'any';

		$args = array(
			'post_type' => 'page',
			'posts_per_page' => $per_page,
			'paged' => $page,
			'post_status' => $status,
		);

		if ( ! empty( $search ) ) {
			$args['s'] = sanitize_text_field( $search );
		}

		$query = new WP_Query( $args );
		$pages = array();

		foreach ( $query->posts as $post ) {
			$pages[] = $this->prepare_post_response( $post );
		}

		return new WP_REST_Response( array(
			'success' => true,
			'data' => $pages,
			'pagination' => array(
				'total' => $query->found_posts,
				'total_pages' => $query->max_num_pages,
				'current' => $page,
				'per_page' => $per_page,
			),
		), 200 );
	}

	public function get_page( $request ) {
		$page_id = $request->get_param( 'id' );
		$post = get_post( $page_id );

		if ( ! $post || 'page' !== $post->post_type ) {
			return new WP_Error( 'wpaic_not_found', 'Page not found.', array( 'status' => 404 ) );
		}

		return new WP_REST_Response( array(
			'success' => true,
			'data' => $this->prepare_post_response( $post ),
		), 200 );
	}

	public function update_page( $request ) {
		$page_id = (int) $request->get_param( 'id' );
		$post = get_post( $page_id );

		if ( ! $post || 'page' !== $post->post_type ) {
			return new WP_Error( 'wpaic_not_found', 'Page not found.', array( 'status' => 404 ) );
		}

		$update_data = array( 'ID' => $page_id );

		if ( $request->has_param( 'title' ) ) {
			$update_data['post_title'] = $request->get_param( 'title' );
		}
		if ( $request->has_param( 'content' ) ) {
			$update_data['post_content'] = $request->get_param( 'content' );
		}
		if ( $request->has_param( 'status' ) ) {
			$update_data['post_status'] = $request->get_param( 'status' );
		}

		$result = wp_update_post( $update_data, true );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		WPAIC_Audit::log( 'page_updated', $page_id, 'page', get_current_user_id(), array( 'title' => $post->post_title ) );

		return new WP_REST_Response( array(
			'success' => true,
			'message' => 'Page updated.',
			'data' => $this->prepare_post_response( get_post( $page_id ) ),
		), 200 );
	}

	public function delete_page( $request ) {
		$page_id = $request->get_param( 'id' );
		$post = get_post( $page_id );

		if ( ! $post || 'page' !== $post->post_type ) {
			return new WP_Error( 'wpaic_not_found', 'Page not found.', array( 'status' => 404 ) );
		}

		$result = wp_delete_post( $page_id, true );

		if ( ! $result ) {
			return new WP_Error( 'wpaic_delete_failed', 'Failed to delete page.', array( 'status' => 500 ) );
		}

		WPAIC_Audit::log( 'page_deleted', $page_id, 'page', get_current_user_id(), array( 'title' => $post->post_title ) );

		return new WP_REST_Response( array(
			'success' => true,
			'message' => 'Page deleted.',
		), 200 );
	}

	public function duplicate_page( $request ) {
		$page_id = (int) $request->get_param( 'id' );
		$post = get_post( $page_id );

		if ( ! $post ) {
			return new WP_Error( 'wpaic_not_found', 'Page not found.', array( 'status' => 404 ) );
		}

		$new_post_data = array(
			'post_title' => $post->post_title . ' (Copy)',
			'post_content' => $post->post_content,
			'post_status' => 'draft',
			'post_type' => $post->post_type,
			'post_author' => $post->post_author,
		);

		$new_id = wp_insert_post( $new_post_data, true );

		if ( is_wp_error( $new_id ) ) {
			return $new_id;
		}

		// Copy meta
		$meta = get_post_meta( $post->ID );
		foreach ( $meta as $key => $values ) {
			foreach ( $values as $value ) {
				add_post_meta( $new_id, $key, maybe_unserialize( $value ) );
			}
		}

		WPAIC_Audit::log( 'page_duplicated', $page_id, 'page', get_current_user_id(), array( 'duplicate_id' => $new_id ) );

		return new WP_REST_Response( array(
			'success' => true,
			'data' => $this->prepare_post_response( get_post( $new_id ) ),
		), 201 );
	}

	// Posts
	public function get_posts( $request ) {
		$per_page = $request->get_param( 'per_page' ) ?: 10;
		$page = $request->get_param( 'page' ) ?: 1;
		$search = $request->get_param( 'search' );
		$status = $request->get_param( 'status' ) ?: 'any';

		$args = array(
			'post_type' => 'post',
			'posts_per_page' => $per_page,
			'paged' => $page,
			'post_status' => $status,
		);

		if ( ! empty( $search ) ) {
			$args['s'] = sanitize_text_field( $search );
		}

		$query = new WP_Query( $args );
		$posts = array();

		foreach ( $query->posts as $post ) {
			$posts[] = $this->prepare_post_response( $post );
		}

		return new WP_REST_Response( array(
			'success' => true,
			'data' => $posts,
			'pagination' => array(
				'total' => $query->found_posts,
				'total_pages' => $query->max_num_pages,
				'current' => $page,
				'per_page' => $per_page,
			),
		), 200 );
	}

	public function get_post( $request ) {
		$post_id = $request->get_param( 'id' );
		$post = get_post( $post_id );

		if ( ! $post || 'post' !== $post->post_type ) {
			return new WP_Error( 'wpaic_not_found', 'Post not found.', array( 'status' => 404 ) );
		}

		return new WP_REST_Response( array(
			'success' => true,
			'data' => $this->prepare_post_response( $post ),
		), 200 );
	}

	public function update_post( $request ) {
		$post_id = (int) $request->get_param( 'id' );
		$post = get_post( $post_id );

		if ( ! $post || 'post' !== $post->post_type ) {
			return new WP_Error( 'wpaic_not_found', 'Post not found.', array( 'status' => 404 ) );
		}

		$update_data = array( 'ID' => $post_id );

		if ( $request->has_param( 'title' ) ) {
			$update_data['post_title'] = $request->get_param( 'title' );
		}
		if ( $request->has_param( 'content' ) ) {
			$update_data['post_content'] = $request->get_param( 'content' );
		}
		if ( $request->has_param( 'status' ) ) {
			$update_data['post_status'] = $request->get_param( 'status' );
		}

		$result = wp_update_post( $update_data, true );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		WPAIC_Audit::log( 'post_updated', $post_id, 'post', get_current_user_id(), array( 'title' => $post->post_title ) );

		return new WP_REST_Response( array(
			'success' => true,
			'message' => 'Post updated.',
			'data' => $this->prepare_post_response( get_post( $post_id ) ),
		), 200 );
	}

	public function delete_post( $request ) {
		$post_id = $request->get_param( 'id' );
		$post = get_post( $post_id );

		if ( ! $post || 'post' !== $post->post_type ) {
			return new WP_Error( 'wpaic_not_found', 'Post not found.', array( 'status' => 404 ) );
		}

		$result = wp_delete_post( $post_id, true );

		if ( ! $result ) {
			return new WP_Error( 'wpaic_delete_failed', 'Failed to delete post.', array( 'status' => 500 ) );
		}

		WPAIC_Audit::log( 'post_deleted', $post_id, 'post', get_current_user_id(), array( 'title' => $post->post_title ) );

		return new WP_REST_Response( array(
			'success' => true,
			'message' => 'Post deleted.',
		), 200 );
	}

	// Builder
	public function extract_builder_content( $request ) {
		$builder = strtolower( $request->get_param( 'builder' ) );
		$page_id = (int) $request->get_param( 'page_id' );

		$post = get_post( $page_id );
		if ( ! $post ) {
			return new WP_Error( 'wpaic_not_found', 'Page not found.', array( 'status' => 404 ) );
		}

		$content = array(
			'post_id' => $page_id,
			'builder' => $builder,
			'content' => $post->post_content,
			'meta' => get_post_meta( $page_id ),
		);

		switch ( $builder ) {
			// Gutenberg
			case 'gutenberg':
				$content['blocks'] = parse_blocks( $post->post_content );
				break;

			// Elementor
			case 'elementor':
				$elementor_data = get_post_meta( $page_id, '_elementor_data', true );
				$content['elementor_data'] = $elementor_data ? json_decode( $elementor_data, true ) : null;
				$content['elementor_version'] = get_post_meta( $page_id, '_elementor_version', true );
				break;

			// Divi 4 & 5
			case 'divi':
				$content['divi_layout'] = get_post_meta( $page_id, '_et_pb_post_layout', true );
				$content['divi_content'] = get_post_meta( $page_id, '_et_pb_layout', true );
				$content['divi_version'] = function_exists( 'et_get_option' ) ? et_get_option( 'divi_style' ) : '4';
				break;

			// WPBakery (Visual Composer legacy)
			case 'wpbakery':
			case 'visual_composer':
				$content['wpbakery_content'] = get_post_meta( $page_id, '_wpb_shortcodes', true );
				$content['wpbakery_version'] = get_post_meta( $page_id, '_wpb_vc_version', true );
				break;

			// Bricks Builder
			case 'bricks':
				$content['bricks_data'] = get_post_meta( $page_id, '_bricks_page_data', true );
				$content['bricks_template'] = get_post_meta( $page_id, '_bricks_template', true );
				$content['bricks_editor'] = get_post_meta( $page_id, '_bricks_use_editor', true );
				break;

			// Oxygen Builder
			case 'oxygen':
				$content['oxygen_data'] = get_post_meta( $page_id, 'ct_builder_json', true );
				$content['oxygen_template'] = get_post_meta( $page_id, 'ct_template', true );
				break;

			// Beaver Builder
			case 'beaver':
			case 'beaver-builder':
				$content['beaver_data'] = get_post_meta( $page_id, '_fl_builder_data', true );
				$content['beaver_layout'] = get_post_meta( $page_id, '_fl_builder_enabled', true );
				break;

			// Brizy
			case 'brizy':
				$content['brizy_data'] = get_post_meta( $page_id, 'brizy_data', true );
				$content['brizy_editor'] = get_post_meta( $page_id, 'brizy_editor_version', true );
				break;

			// Thrive Architect
			case 'thrive':
			case 'thrive-architect':
				$content['thrive_data'] = get_post_meta( $page_id, 'tve_updated_post', true );
				$content['thrive_architect'] = get_post_meta( $page_id, 'tve_custom_css', true );
				break;

			// Breakdance
			case 'breakdance':
				$content['breakdance_data'] = get_post_meta( $page_id, 'breakdance_data', true );
				$content['breakdance_version'] = get_post_meta( $page_id, 'breakdance_version', true );
				break;

			// Flatsome UX Builder
			case 'flatsome':
				$content['flatsome_data'] = get_post_meta( $page_id, 'flatsome_data', true );
				$content['flatsome_template'] = get_post_meta( $page_id, '_wp_page_template', true );
				break;

			// Kadence Theme
			case 'kadence':
				$content['kadence_data'] = get_post_meta( $page_id, 'kadence_data', true );
				$content['kadence_hero'] = get_post_meta( $page_id, 'kadence_hero_design', true );
				break;

			// Kadence Blocks
			case 'kadence_blocks':
				$content['kadence_blocks_data'] = get_post_meta( $page_id, '_kadence_blocks_data', true );
				$content['kadence_blocks_version'] = defined( 'KADENCE_BLOCKS_VERSION' ) ? KADENCE_BLOCKS_VERSION : null;
				break;

			// Default fallback
			default:
				$content['builder_unsupported'] = true;
				break;
		}

		return new WP_REST_Response( array(
			'success' => true,
			'data' => $content,
		), 200 );
	}

	public function inject_builder_content( $request ) {
		$builder = strtolower( $request->get_param( 'builder' ) );
		$page_id = (int) $request->get_param( 'page_id' );
		$content = $request->get_param( 'content' );

		$post = get_post( $page_id );
		if ( ! $post ) {
			return new WP_Error( 'wpaic_not_found', 'Page not found.', array( 'status' => 404 ) );
		}

		$update_data = array( 'ID' => $page_id );

		switch ( $builder ) {
			// Gutenberg
			case 'gutenberg':
				if ( isset( $content['blocks'] ) ) {
					$update_data['post_content'] = serialize_blocks( $content['blocks'] );
				} elseif ( isset( $content['content'] ) ) {
					$update_data['post_content'] = $content['content'];
				}
				break;

			// Elementor
			case 'elementor':
				if ( isset( $content['elementor_data'] ) ) {
					update_post_meta( $page_id, '_elementor_data', wp_json_encode( $content['elementor_data'] ) );
					update_post_meta( $page_id, '_elementor_edit_mode', 'builder' );
				}
				break;

			// Divi 4 & 5
			case 'divi':
				if ( isset( $content['divi_content'] ) ) {
					update_post_meta( $page_id, '_et_pb_layout', $content['divi_content'] );
				}
				if ( isset( $content['divi_layout'] ) ) {
					update_post_meta( $page_id, '_et_pb_post_layout', $content['divi_layout'] );
				}
				break;

			// WPBakery
			case 'wpbakery':
			case 'visual_composer':
				if ( isset( $content['wpbakery_content'] ) ) {
					update_post_meta( $page_id, '_wpb_shortcodes', $content['wpbakery_content'] );
				}
				if ( isset( $content['content'] ) ) {
					$update_data['post_content'] = $content['content'];
				}
				break;

			// Bricks Builder
			case 'bricks':
				if ( isset( $content['bricks_data'] ) ) {
					update_post_meta( $page_id, '_bricks_page_data', $content['bricks_data'] );
				}
				if ( isset( $content['bricks_template'] ) ) {
					update_post_meta( $page_id, '_bricks_template', $content['bricks_template'] );
				}
				break;

			// Oxygen Builder
			case 'oxygen':
				if ( isset( $content['oxygen_data'] ) ) {
					update_post_meta( $page_id, 'ct_builder_json', $content['oxygen_data'] );
				}
				if ( isset( $content['oxygen_template'] ) ) {
					update_post_meta( $page_id, 'ct_template', $content['oxygen_template'] );
				}
				break;

			// Beaver Builder
			case 'beaver':
			case 'beaver-builder':
				if ( isset( $content['beaver_data'] ) ) {
					update_post_meta( $page_id, '_fl_builder_data', $content['beaver_data'] );
				}
				if ( isset( $content['beaver_layout'] ) ) {
					update_post_meta( $page_id, '_fl_builder_enabled', $content['beaver_layout'] );
				}
				break;

			// Brizy
			case 'brizy':
				if ( isset( $content['brizy_data'] ) ) {
					update_post_meta( $page_id, 'brizy_data', $content['brizy_data'] );
				}
				break;

			// Thrive Architect
			case 'thrive':
			case 'thrive-architect':
				if ( isset( $content['thrive_data'] ) ) {
					update_post_meta( $page_id, 'tve_updated_post', $content['thrive_data'] );
				}
				if ( isset( $content['thrive_architect'] ) ) {
					update_post_meta( $page_id, 'tve_custom_css', $content['thrive_architect'] );
				}
				break;

			// Breakdance
			case 'breakdance':
				if ( isset( $content['breakdance_data'] ) ) {
					update_post_meta( $page_id, 'breakdance_data', $content['breakdance_data'] );
				}
				if ( isset( $content['breakdance_version'] ) ) {
					update_post_meta( $page_id, 'breakdance_version', $content['breakdance_version'] );
				}
				break;

			// Flatsome
			case 'flatsome':
				if ( isset( $content['flatsome_data'] ) ) {
					update_post_meta( $page_id, 'flatsome_data', $content['flatsome_data'] );
				}
				if ( isset( $content['flatsome_template'] ) ) {
					update_post_meta( $page_id, '_wp_page_template', $content['flatsome_template'] );
				}
				break;

			// Kadence Theme
			case 'kadence':
				if ( isset( $content['kadence_data'] ) ) {
					update_post_meta( $page_id, 'kadence_data', $content['kadence_data'] );
				}
				if ( isset( $content['kadence_hero'] ) ) {
					update_post_meta( $page_id, 'kadence_hero_design', $content['kadence_hero'] );
				}
				break;

			// Kadence Blocks
			case 'kadence_blocks':
				if ( isset( $content['kadence_blocks_data'] ) ) {
					update_post_meta( $page_id, '_kadence_blocks_data', $content['kadence_blocks_data'] );
				}
				break;

			// Default fallback
			default:
				if ( isset( $content['content'] ) ) {
					$update_data['post_content'] = $content['content'];
				}
				break;
		}

		if ( isset( $update_data['post_content'] ) ) {
			wp_update_post( $update_data );
		}

		WPAIC_Audit::log( 'builder_content_injected', $page_id, $post->post_type, get_current_user_id(), array( 'builder' => $builder ) );

		return new WP_REST_Response( array(
			'success' => true,
			'message' => 'Builder content updated.',
			'data' => $this->prepare_post_response( get_post( $page_id ) ),
		), 200 );
	}

		$content = array(
			'post_id' => $page_id,
			'builder' => $builder,
			'content' => $post->post_content,
			'meta' => get_post_meta( $page_id ),
		);

		// Get builder-specific content
		switch ( $builder ) {
			case 'gutenberg':
				$content['blocks'] = parse_blocks( $post->post_content );
				break;
			case 'elementor':
				$elementor_data = get_post_meta( $page_id, '_elementor_data', true );
				$content['elementor_data'] = $elementor_data ? json_decode( $elementor_data, true ) : null;
				break;
			case 'divi':
				$divi_layout = get_post_meta( $page_id, '_et_pb_post_layout', true );
				$content['divi_layout'] = $divi_layout;
				$content['divi_content'] = get_post_meta( $page_id, '_et_pb_layout', true );
				break;
		}

		return new WP_REST_Response( array(
			'success' => true,
			'data' => $content,
		), 200 );
	}

	public function inject_builder_content( $request ) {
		$builder = strtolower( $request->get_param( 'builder' ) );
		$page_id = (int) $request->get_param( 'page_id' );
		$content = $request->get_param( 'content' );

		$post = get_post( $page_id );
		if ( ! $post ) {
			return new WP_Error( 'wpaic_not_found', 'Page not found.', array( 'status' => 404 ) );
		}

		$update_data = array( 'ID' => $page_id );

		switch ( $builder ) {
			case 'gutenberg':
				if ( isset( $content['blocks'] ) ) {
					$update_data['post_content'] = serialize_blocks( $content['blocks'] );
				} elseif ( isset( $content['content'] ) ) {
					$update_data['post_content'] = $content['content'];
				}
				break;
			case 'elementor':
				if ( isset( $content['elementor_data'] ) ) {
					update_post_meta( $page_id, '_elementor_data', wp_json_encode( $content['elementor_data'] ) );
					update_post_meta( $page_id, '_elementor_edit_mode', 'builder' );
				}
				break;
			case 'divi':
				if ( isset( $content['divi_content'] ) ) {
					update_post_meta( $page_id, '_et_pb_layout', $content['divi_content'] );
				}
				if ( isset( $content['divi_layout'] ) ) {
					update_post_meta( $page_id, '_et_pb_post_layout', $content['divi_layout'] );
				}
				break;
			default:
				if ( isset( $content['content'] ) ) {
					$update_data['post_content'] = $content['content'];
				}
		}

		if ( isset( $update_data['post_content'] ) ) {
			wp_update_post( $update_data );
		}

		WPAIC_Audit::log( 'builder_content_injected', $page_id, $post->post_type, get_current_user_id(), array( 'builder' => $builder ) );

		return new WP_REST_Response( array(
			'success' => true,
			'message' => 'Builder content updated.',
			'data' => $this->prepare_post_response( get_post( $page_id ) ),
		), 200 );
	}

	// Media
	public function upload_media( $request ) {
		$files = $request->get_file_params();

		if ( empty( $files['file'] ) ) {
			return new WP_Error( 'wpaic_no_file', 'No file provided.', array( 'status' => 400 ) );
		}

		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$upload = wp_handle_upload( $files['file'], array( 'test_form' => false ) );

		if ( isset( $upload['error'] ) ) {
			return new WP_Error( 'wpaic_upload_failed', $upload['error'], array( 'status' => 500 ) );
		}

		$attachment = array(
			'post_mime_type' => $upload['type'],
			'post_title' => sanitize_file_name( basename( $files['file']['name'] ) ),
			'post_content' => '',
			'post_status' => 'inherit',
		);

		$attach_id = wp_insert_attachment( $attachment, $upload['file'] );
		$attach_data = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
		wp_update_attachment_metadata( $attach_id, $attach_data );

		WPAIC_Audit::log( 'media_uploaded', $attach_id, 'attachment', get_current_user_id(), array( 'filename' => basename( $files['file']['name'] ) ) );

		return new WP_REST_Response( array(
			'success' => true,
			'data' => array(
				'id' => $attach_id,
				'url' => wp_get_attachment_url( $attach_id ),
				'type' => $upload['type'],
			),
		), 201 );
	}

	// Analysis
	public function analyze_seo( $request ) {
		$page_id = (int) $request->get_param( 'page_id' );
		$post = get_post( $page_id );

		if ( ! $post ) {
			return new WP_Error( 'wpaic_not_found', 'Page not found.', array( 'status' => 404 ) );
		}

		$title = $post->post_title;
		$content = strip_tags( $post->post_content );
		$words = str_word_count( $content );

		$analysis = array(
			'title_length' => strlen( $title ),
			'content_length' => strlen( $content ),
			'word_count' => $words,
			'has_h1' => preg_match( '/<h1/i', $post->post_content ) > 0,
			'images_count' => preg_match_all( '/<img/i', $post->post_content ),
			'internal_links' => preg_match_all( '/' . preg_quote( home_url(), '/' ) . '/i', $post->post_content ),
			'recommendations' => array(),
		);

		if ( strlen( $title ) < 30 || strlen( $title ) > 60 ) {
			$analysis['recommendations'][] = 'Title should be between 30-60 characters.';
		}
		if ( $words < 300 ) {
			$analysis['recommendations'][] = 'Content is short. Consider adding more content.';
		}

		return new WP_REST_Response( array(
			'success' => true,
			'data' => $analysis,
		), 200 );
	}

	public function analyze_performance( $request ) {
		$page_id = (int) $request->get_param( 'page_id' );
		$post = get_post( $page_id );

		if ( ! $post ) {
			return new WP_Error( 'wpaic_not_found', 'Page not found.', array( 'status' => 404 ) );
		}

		$analysis = array(
			'content_length' => strlen( $post->post_content ),
			'has_caching' => function_exists( 'wp_cache_get' ),
			'recommendations' => array(
				'Use caching plugin',
				'Optimize images',
				'Minify CSS/JS',
			),
		);

		return new WP_REST_Response( array(
			'success' => true,
			'data' => $analysis,
		), 200 );
	}

	public function analyze_aeo( $request ) {
		$page_id = (int) $request->get_param( 'page_id' );
		$post = get_post( $page_id );

		if ( ! $post ) {
			return new WP_Error( 'wpaic_not_found', 'Page not found.', array( 'status' => 404 ) );
		}

		$analysis = array(
			'has_structured_data' => ! empty( get_post_meta( $page_id, '_schema_markup', true ) ),
			'content_length' => strlen( strip_tags( $post->post_content ) ),
			'question_count' => preg_match_all( '/\?/', $post->post_content ),
			'recommendations' => array(
				'Add FAQ schema markup',
				'Use question-based headings',
				'Structure content for featured snippets',
			),
		);

		return new WP_REST_Response( array(
			'success' => true,
			'data' => $analysis,
		), 200 );
	}

	public function analyze_accessibility( $request ) {
		$page_id = (int) $request->get_param( 'page_id' );
		$post = get_post( $page_id );

		if ( ! $post ) {
			return new WP_Error( 'wpaic_not_found', 'Page not found.', array( 'status' => 404 ) );
		}

		$content = $post->post_content;
		$issues = array();

		if ( preg_match_all( '/<img(?!.*alt=)/i', $content ) ) {
			$issues[] = 'Images missing alt attributes';
		}
		if ( ! preg_match( '/<h1/i', $content ) ) {
			$issues[] = 'No H1 tag found';
		}

		$analysis = array(
			'has_issues' => ! empty( $issues ),
			'issues' => $issues,
			'images_without_alt' => preg_match_all( '/<img(?!.*alt=)/i', $content ),
			'has_skip_nav' => strpos( $content, 'skip' ) !== false,
			'recommendations' => array(
				'Add alt text to all images',
				'Ensure proper heading hierarchy',
				'Add ARIA labels where needed',
			),
		);

		return new WP_REST_Response( array(
			'success' => true,
			'data' => $analysis,
		), 200 );
	}

	// Plugins
	public function list_plugins( $request ) {
		$plugins = get_plugins();
		$active = get_option( 'active_plugins', array() );
		$result = array();

		foreach ( $plugins as $slug => $data ) {
			$slug_parts = explode( '/', $slug );
			$result[] = array(
				'name' => $data['Name'],
				'slug' => $slug_parts[0],
				'version' => $data['Version'],
				'active' => in_array( $slug, $active, true ),
				'description' => $data['Description'],
			);
		}

		return new WP_REST_Response( array(
			'success' => true,
			'data' => $result,
		), 200 );
	}

	public function install_plugin( $request ) {
		$slug = $request->get_param( 'slug' );

		if ( empty( $slug ) ) {
			return new WP_Error( 'wpaic_missing_slug', 'Plugin slug required.', array( 'status' => 400 ) );
		}

		require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		$api = plugins_api( 'plugin_information', array( 'slug' => $slug ) );

		if ( is_wp_error( $api ) ) {
			return $api;
		}

		$upgrader = new Plugin_Upgrader( new Automatic_Upgrader_Skin() );
		$result = $upgrader->install( $api->download_link );

		WPAIC_Audit::log( 'plugin_installed', 0, 'plugin', get_current_user_id(), array( 'slug' => $slug ) );

		return new WP_REST_Response( array(
			'success' => true,
			'message' => 'Plugin installed.',
			'data' => array( 'slug' => $slug, 'installed' => ! is_wp_error( $result ) ),
		), 200 );
	}

	public function activate_plugin( $request ) {
		$slug = $request->get_param( 'slug' );

		if ( empty( $slug ) ) {
			return new WP_Error( 'wpaic_missing_slug', 'Plugin slug required.', array( 'status' => 400 ) );
		}

		$plugins = get_plugins();
		$plugin_file = null;

		foreach ( $plugins as $file => $data ) {
			if ( strpos( $file, $slug . '/' ) === 0 ) {
				$plugin_file = $file;
				break;
			}
		}

		if ( ! $plugin_file ) {
			return new WP_Error( 'wpaic_not_found', 'Plugin not found.', array( 'status' => 404 ) );
		}

		$result = activate_plugin( $plugin_file );

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		WPAIC_Audit::log( 'plugin_activated', 0, 'plugin', get_current_user_id(), array( 'slug' => $slug ) );

		return new WP_REST_Response( array(
			'success' => true,
			'message' => 'Plugin activated.',
		), 200 );
	}

	public function deactivate_plugin( $request ) {
		$slug = $request->get_param( 'slug' );

		if ( empty( $slug ) ) {
			return new WP_Error( 'wpaic_missing_slug', 'Plugin slug required.', array( 'status' => 400 ) );
		}

		$plugins = get_plugins();
		$plugin_file = null;

		foreach ( $plugins as $file => $data ) {
			if ( strpos( $file, $slug . '/' ) === 0 ) {
				$plugin_file = $file;
				break;
			}
		}

		if ( ! $plugin_file ) {
			return new WP_Error( 'wpaic_not_found', 'Plugin not found.', array( 'status' => 404 ) );
		}

		deactivate_plugins( $plugin_file );

		WPAIC_Audit::log( 'plugin_deactivated', 0, 'plugin', get_current_user_id(), array( 'slug' => $slug ) );

		return new WP_REST_Response( array(
			'success' => true,
			'message' => 'Plugin deactivated.',
		), 200 );
	}

	// Menus
	public function list_menus() {
		$menus = wp_get_nav_menus();
		$result = array();

		foreach ( $menus as $menu ) {
			$result[] = array(
				'id' => $menu->term_id,
				'name' => $menu->name,
				'slug' => $menu->slug,
				'count' => $menu->count,
			);
		}

		return new WP_REST_Response( array(
			'success' => true,
			'data' => $result,
		), 200 );
	}

	public function get_menu( $request ) {
		$menu_id = (int) $request->get_param( 'id' );
		$menu = wp_get_nav_menu_object( $menu_id );

		if ( ! $menu ) {
			return new WP_Error( 'wpaic_not_found', 'Menu not found.', array( 'status' => 404 ) );
		}

		$items = wp_get_nav_menu_items( $menu_id );

		return new WP_REST_Response( array(
			'success' => true,
			'data' => array(
				'id' => $menu->term_id,
				'name' => $menu->name,
				'slug' => $menu->slug,
				'items' => $items ? array_map( function( $item ) {
					return array(
						'id' => $item->ID,
						'title' => $item->title,
						'url' => $item->url,
						'parent' => $item->menu_item_parent,
						'order' => $item->menu_order,
					);
				}, $items ) : array(),
			),
		), 200 );
	}

	public function list_menu_locations() {
		$locations = get_nav_menu_locations();
		$result = array();

		foreach ( $locations as $location => $menu_id ) {
			$menu = wp_get_nav_menu_object( $menu_id );
			$result[] = array(
				'location' => $location,
				'menu_id' => $menu_id,
				'menu_name' => $menu ? $menu->name : 'None',
			);
		}

		return new WP_REST_Response( array(
			'success' => true,
			'data' => $result,
		), 200 );
	}

	// Taxonomies
	public function list_taxonomies() {
		$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
		$result = array();

		foreach ( $taxonomies as $tax ) {
			$result[] = array(
				'name' => $tax->name,
				'label' => $tax->label,
				'labels' => array(
					'singular' => $tax->labels->singular_name,
					'plural' => $tax->labels->name,
				),
				'hierarchical' => $tax->hierarchical,
			);
		}

		return new WP_REST_Response( array(
			'success' => true,
			'data' => $result,
		), 200 );
	}

	public function list_terms( $request ) {
		$taxonomy = $request->get_param( 'taxonomy' );
		$per_page = $request->get_param( 'per_page' ) ?: 100;
		$page = $request->get_param( 'page' ) ?: 1;
		$search = $request->get_param( 'search' );

		$args = array(
			'taxonomy' => $taxonomy,
			'hide_empty' => false,
			'number' => $per_page,
			'offset' => ( $page - 1 ) * $per_page,
		);

		if ( $search ) {
			$args['search'] = $search;
		}

		$terms = get_terms( $args );

		if ( is_wp_error( $terms ) ) {
			return $terms;
		}

		$result = array_map( function( $term ) {
			return array(
				'id' => $term->term_id,
				'name' => $term->name,
				'slug' => $term->slug,
				'count' => $term->count,
				'parent' => $term->parent,
			);
		}, $terms );

		return new WP_REST_Response( array(
			'success' => true,
			'data' => $result,
		), 200 );
	}

	// Snapshots
	public function list_snapshots() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'wpaic_audit_log';
		$snapshots = $wpdb->get_results( "SELECT * FROM {$table_name} WHERE action IN ('page_duplicated','post_dudated') ORDER BY created_at DESC LIMIT 50" );

		return new WP_REST_Response( array(
			'success' => true,
			'data' => $snapshots ?: array(),
		), 200 );
	}

	public function restore_snapshot( $request ) {
		$snapshot_id = $request->get_param( 'snapshot_id' );

		return new WP_REST_Response( array(
			'success' => false,
			'message' => 'Snapshot restore not yet implemented.',
		), 501 );
	}

	// ==================== USERS ====================

	public function list_users() {
		$users = get_users( array( 'fields' => array( 'ID', 'user_login', 'user_email', 'display_name', 'user_registered' ) ) );
		return new WP_REST_Response( array( 'success' => true, 'data' => array_map( function( $u ) {
			return array(
				'id' => $u->ID, 'login' => $u->user_login, 'email' => $u->user_email,
				'name' => $u->display_name, 'registered' => $u->user_registered,
				'roles' => get_userdata( $u->ID )->roles,
			);
		}, $users ) ), 200 );
	}

	public function get_user( $request ) {
		$user = get_userdata( $request->get_param( 'id' ) );
		if ( ! $user ) return new WP_Error( 'wpaic_not_found', 'User not found.', array( 'status' => 404 ) );
		return new WP_REST_Response( array(
			'success' => true, 'data' => array(
				'id' => $user->ID, 'login' => $user->user_login, 'email' => $user->user_email,
				'name' => $user->display_name, 'roles' => $user->roles,
			),
		), 200 );
	}

	public function create_user( $request ) {
		$args = array(
			'user_login' => $request->get_param( 'username' ),
			'user_email' => $request->get_param( 'email' ),
			'user_pass' => wp_generate_password( 12 ),
		);
		if ( $request->has_param( 'name' ) ) $args['display_name'] = $request->get_param( 'name' );
		if ( $request->has_param( 'role' ) ) $args['role'] = $request->get_param( 'role' );

		$user_id = wp_insert_user( $args );
		if ( is_wp_error( $user_id ) ) return $user_id;

		WPAIC_Audit::log( 'user_created', $user_id, 'user', get_current_user_id(), array( 'login' => $args['user_login'] ) );
		return new WP_REST_Response( array( 'success' => true, 'data' => array( 'id' => $user_id ) ), 201 );
	}

	public function update_user( $request ) {
		$user_id = $request->get_param( 'id' );
		$args = array( 'ID' => $user_id );
		if ( $request->has_param( 'email' ) ) $args['user_email'] = $request->get_param( 'email' );
		if ( $request->has_param( 'name' ) ) $args['display_name'] = $request->get_param( 'name' );
		if ( $request->has_param( 'role' ) ) $args['role'] = $request->get_param( 'role' );

		$result = wp_update_user( $args );
		if ( is_wp_error( $result ) ) return $result;

		WPAIC_Audit::log( 'user_updated', $user_id, 'user', get_current_user_id(), array() );
		return new WP_REST_Response( array( 'success' => true, 'message' => 'User updated.' ), 200 );
	}

	public function delete_user( $request ) {
		$user_id = $request->get_param( 'id' );
		if ( ! get_userdata( $user_id ) ) return new WP_Error( 'wpaic_not_found', 'User not found.', array( 'status' => 404 ) );

		require_once ABSPATH . 'wp-admin/includes/user.php';
		$result = wp_delete_user( $user_id );
		if ( ! $result ) return new WP_Error( 'wpaic_delete_failed', 'Failed to delete user.', array( 'status' => 500 ) );

		WPAIC_Audit::log( 'user_deleted', $user_id, 'user', get_current_user_id(), array() );
		return new WP_REST_Response( array( 'success' => true, 'message' => 'User deleted.' ), 200 );
	}

	// ==================== COMMENTS ====================

	public function list_comments( $request ) {
		$args = array( 'number' => $request->get_param( 'per_page' ) ?: 20, 'paged' => $request->get_param( 'page' ) ?: 1 );
		if ( $request->has_param( 'status' ) ) $args['status'] = $request->get_param( 'status' );
		if ( $request->has_param( 'post_id' ) ) $args['post_id'] = $request->get_param( 'post_id' );

		$comments = get_comments( $args );
		return new WP_REST_Response( array(
			'success' => true, 'data' => array_map( function( $c ) {
				return array( 'id' => $c->comment_ID, 'post_id' => $c->comment_post_ID, 'author' => $c->comment_author,
					'email' => $c->comment_author_email, 'content' => $c->comment_content, 'status' => $c->comment_approved,
					'date' => $c->comment_date );
			}, $comments ),
		), 200 );
	}

	public function get_comment( $request ) {
		$comment = get_comment( $request->get_param( 'id' ) );
		if ( ! $comment ) return new WP_Error( 'wpaic_not_found', 'Comment not found.', array( 'status' => 404 ) );
		return new WP_REST_Response( array( 'success' => true, 'data' => $comment ), 200 );
	}

	public function approve_comment( $request ) {
		$comment_id = $request->get_param( 'id' );
		$result = wp_set_comment_status( $comment_id, 'approve' );
		if ( is_wp_error( $result ) ) return $result;

		WPAIC_Audit::log( 'comment_approved', $comment_id, 'comment', get_current_user_id(), array() );
		return new WP_REST_Response( array( 'success' => true, 'message' => 'Comment approved.' ), 200 );
	}

	public function spam_comment( $request ) {
		$comment_id = $request->get_param( 'id' );
		$result = wp_set_comment_status( $comment_id, 'spam' );
		if ( is_wp_error( $result ) ) return $result;

		WPAIC_Audit::log( 'comment_spammed', $comment_id, 'comment', get_current_user_id(), array() );
		return new WP_REST_Response( array( 'success' => true, 'message' => 'Comment marked as spam.' ), 200 );
	}

	public function delete_comment( $request ) {
		$comment_id = $request->get_param( 'id' );
		$result = wp_delete_comment( $comment_id, true );
		if ( ! $result ) return new WP_Error( 'wpaic_delete_failed', 'Failed to delete comment.', array( 'status' => 500 ) );

		WPAIC_Audit::log( 'comment_deleted', $comment_id, 'comment', get_current_user_id(), array() );
		return new WP_REST_Response( array( 'success' => true, 'message' => 'Comment deleted.' ), 200 );
	}

	// ==================== MEDIA ADDITIONAL ====================

	public function list_media( $request ) {
		$args = array(
			'post_type' => 'attachment', 'post_status' => 'inherit', 'posts_per_page' => $request->get_param( 'per_page' ) ?: 20,
			'paged' => $request->get_param( 'page' ) ?: 1,
		);
		if ( $request->has_param( 'mime_type' ) ) $args['post_mime_type'] = $request->get_param( 'mime_type' );

		$query = new WP_Query( $args );
		$media = array_map( function( $p ) {
			return array( 'id' => $p->ID, 'title' => $p->post_title, 'url' => wp_get_attachment_url( $p->ID ),
				'mime_type' => $p->post_mime_type, 'date' => $p->post_date );
		}, $query->posts );

		return new WP_REST_Response( array( 'success' => true, 'data' => $media ), 200 );
	}

	public function delete_media( $request ) {
		$media_id = $request->get_param( 'id' );
		$result = wp_delete_attachment( $media_id, true );
		if ( ! $result ) return new WP_Error( 'wpaic_delete_failed', 'Failed to delete media.', array( 'status' => 500 ) );

		WPAIC_Audit::log( 'media_deleted', $media_id, 'attachment', get_current_user_id(), array() );
		return new WP_REST_Response( array( 'success' => true, 'message' => 'Media deleted.' ), 200 );
	}

	public function update_media_meta( $request ) {
		$media_id = $request->get_param( 'id' );
		$meta = $request->get_param( 'meta' );
		if ( is_array( $meta ) ) {
			foreach ( $meta as $key => $value ) update_post_meta( $media_id, $key, $value );
		}
		return new WP_REST_Response( array( 'success' => true, 'message' => 'Media meta updated.' ), 200 );
	}

	// ==================== TERMS CRUD ====================

	public function create_term( $request ) {
		$taxonomy = $request->get_param( 'taxonomy' );
		$args = array();
		if ( $request->has_param( 'description' ) ) $args['description'] = $request->get_param( 'description' );
		if ( $request->has_param( 'parent' ) ) $args['parent'] = $request->get_param( 'parent' );

		$result = wp_insert_term( $request->get_param( 'name' ), $taxonomy, $args );
		if ( is_wp_error( $result ) ) return $result;

		WPAIC_Audit::log( 'term_created', $result['term_id'], 'term', get_current_user_id(), array( 'taxonomy' => $taxonomy ) );
		return new WP_REST_Response( array( 'success' => true, 'data' => $result ), 201 );
	}

	public function get_term( $request ) {
		$term = get_term( $request->get_param( 'id' ) );
		if ( ! $term || is_wp_error( $term ) ) return new WP_Error( 'wpaic_not_found', 'Term not found.', array( 'status' => 404 ) );
		return new WP_REST_Response( array( 'success' => true, 'data' => $term ), 200 );
	}

	public function update_term( $request ) {
		$term_id = $request->get_param( 'id' );
		$args = array();
		if ( $request->has_param( 'name' ) ) $args['name'] = $request->get_param( 'name' );
		if ( $request->has_param( 'description' ) ) $args['description'] = $request->get_param( 'description' );
		if ( $request->has_param( 'parent' ) ) $args['parent'] = $request->get_param( 'parent' );

		$result = wp_update_term( $term_id, $request->get_param( 'taxonomy' ), $args );
		if ( is_wp_error( $result ) ) return $result;

		WPAIC_Audit::log( 'term_updated', $term_id, 'term', get_current_user_id(), array() );
		return new WP_REST_Response( array( 'success' => true, 'message' => 'Term updated.' ), 200 );
	}

	public function delete_term( $request ) {
		$term_id = $request->get_param( 'id' );
		$taxonomy = $request->get_param( 'taxonomy' ) ?: '';
		$result = wp_delete_term( $term_id, $taxonomy );
		if ( is_wp_error( $result ) ) return $result;

		WPAIC_Audit::log( 'term_deleted', $term_id, 'term', get_current_user_id(), array() );
		return new WP_REST_Response( array( 'success' => true, 'message' => 'Term deleted.' ), 200 );
	}

	// ==================== SITE SETTINGS ====================

	public function get_site_settings() {
		return new WP_REST_Response( array(
			'success' => true, 'data' => array(
				'blogname' => get_option( 'blogname' ), 'blogdescription' => get_option( 'blogdescription' ),
				'siteurl' => get_option( 'siteurl' ), 'home' => get_option( 'home' ),
				'users_can_register' => get_option( 'users_can_register' ), 'timezone_string' => get_option( 'timezone_string' ),
				'date_format' => get_option( 'date_format' ), 'time_format' => get_option( 'time_format' ),
				'start_of_week' => get_option( 'start_of_week' ), 'language' => get_option( 'WPLANG' ),
			),
		), 200 );
	}

	public function update_site_settings( $request ) {
		$settings = array( 'blogname', 'blogdescription', 'users_can_register', 'timezone_string', 'date_format', 'time_format', 'start_of_week' );
		foreach ( $settings as $key ) {
			if ( $request->has_param( $key ) ) update_option( $key, $request->get_param( $key ) );
		}
		return new WP_REST_Response( array( 'success' => true, 'message' => 'Settings updated.' ), 200 );
	}

	// ==================== THEMES ====================

	public function list_themes() {
		$themes = wp_get_themes();
		$active = get_stylesheet();
		$result = array();
		foreach ( $themes as $slug => $theme ) {
			$result[] = array(
				'slug' => $slug, 'name' => $theme->get( 'Name' ), 'version' => $theme->get( 'Version' ),
				'active' => $slug === $active,
			);
		}
		return new WP_REST_Response( array( 'success' => true, 'data' => $result ), 200 );
	}

	public function activate_theme( $request ) {
		$slug = $request->get_param( 'slug' );
		$result = switch_theme( $slug );
		if ( is_wp_error( $result ) ) return $result;

		WPAIC_Audit::log( 'theme_activated', 0, 'theme', get_current_user_id(), array( 'slug' => $slug ) );
		return new WP_REST_Response( array( 'success' => true, 'message' => 'Theme activated.' ), 200 );
	}

	public function update_theme( $request ) {
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		$upgrader = new Theme_Upgrader( new Automatic_Upgrader_Skin() );
		$result = $upgrader->upgrade( get_stylesheet() );
		return new WP_REST_Response( array(
			'success' => ! is_wp_error( $result ), 'message' => is_wp_error( $result ) ? $result->get_error_message() : 'Theme updated.'
		), is_wp_error( $result ) ? 500 : 200 );
	}

	// ==================== POST META ====================

	public function list_post_meta( $request ) {
		$post_id = $request->get_param( 'id' );
		$meta = get_post_meta( $post_id );
		return new WP_REST_Response( array( 'success' => true, 'data' => $meta ?: array() ), 200 );
	}

	public function update_post_meta( $request ) {
		$post_id = $request->get_param( 'id' );
		$meta = $request->get_param( 'meta' );
		if ( is_array( $meta ) ) {
			foreach ( $meta as $key => $value ) update_post_meta( $post_id, $key, $value );
			WPAIC_Audit::log( 'post_meta_updated', $post_id, 'post', get_current_user_id(), array_keys( $meta ) );
		}
		return new WP_REST_Response( array( 'success' => true, 'message' => 'Post meta updated.' ), 200 );
	}

	public function delete_post_meta( $request ) {
		$post_id = $request->get_param( 'id' );
		$meta_key = $request->get_param( 'meta_key' );
		$result = delete_post_meta( $post_id, $meta_key );
		if ( ! $result ) return new WP_Error( 'wpaic_delete_failed', 'Failed to delete meta.', array( 'status' => 500 ) );

		WPAIC_Audit::log( 'post_meta_deleted', $post_id, 'post', get_current_user_id(), array( 'key' => $meta_key ) );
		return new WP_REST_Response( array( 'success' => true, 'message' => 'Post meta deleted.' ), 200 );
	}

	// ==================== SEARCH ====================

	public function search_content( $request ) {
		$query = $request->get_param( 'query' );
		if ( empty( $query ) ) return new WP_Error( 'wpaic_missing_query', 'Query is required.', array( 'status' => 400 ) );

		$results = array();

		// Search posts and pages
		$posts = get_posts( array( 's' => $query, 'post_status' => 'any', 'posts_per_page' => 10 ) );
		foreach ( $posts as $post ) {
			$results[] = array( 'type' => $post->post_type, 'id' => $post->ID, 'title' => $post->post_title, 'status' => $post->post_status );
		}

		return new WP_REST_Response( array( 'success' => true, 'data' => $results ), 200 );
	}

	// ==================== WIDGETS ====================

	public function list_widgets() {
		$sidebars_widgets = wp_get_sidebars_widgets();
		$result = array();
		foreach ( $sidebars_widgets as $sidebar => $widgets ) {
			if ( 'wp_inactive_widgets' === $sidebar ) continue;
			$result[$sidebar] = $widgets ?: array();
		}
		return new WP_REST_Response( array( 'success' => true, 'data' => $result ), 200 );
	}

	public function update_widget( $request ) {
		$widget_id = $request->get_param( 'id' );
		$settings = $request->get_param( 'settings' );
		$widget_base = preg_replace( '/-\d+$/', '', $widget_id );

		$current = get_option( 'widget_' . $widget_base, array() );
		$widget_num = preg_replace( '/^[^-]+-/', '', $widget_id );
		if ( is_numeric( $widget_num ) ) {
			$current[$widget_num] = $settings;
			update_option( 'widget_' . $widget_base, $current );
		}
		return new WP_REST_Response( array( 'success' => true, 'message' => 'Widget updated.' ), 200 );
	}

	public function list_sidebars() {
		global $wp_registered_sidebars;
		return new WP_REST_Response( array(
			'success' => true, 'data' => array_map( function( $v ) { return array( 'id' => $v['id'], 'name' => $v['name'] ); }, $wp_registered_sidebars )
		), 200 );
	}

	// ==================== BULK OPERATIONS ====================

	public function bulk_update_posts( $request ) {
		$post_ids = $request->get_param( 'post_ids' );
		$updates = $request->get_param( 'updates' );
		$results = array();

		foreach ( $post_ids as $post_id ) {
			$args = array( 'ID' => $post_id );
			if ( isset( $updates['title'] ) ) $args['post_title'] = $updates['title'];
			if ( isset( $updates['content'] ) ) $args['post_content'] = $updates['content'];
			if ( isset( $updates['status'] ) ) $args['post_status'] = $updates['status'];

			$result = wp_update_post( $args, true );
			$results[$post_id] = is_wp_error( $result ) ? $result->get_error_message() : 'updated';
		}

		return new WP_REST_Response( array( 'success' => true, 'data' => $results ), 200 );
	}

	public function bulk_delete_posts( $request ) {
		$post_ids = $request->get_param( 'post_ids' );
		$results = array();

		foreach ( $post_ids as $post_id ) {
			$result = wp_delete_post( $post_id, true );
			$results[$post_id] = $result ? 'deleted' : 'failed';
		}

		return new WP_REST_Response( array( 'success' => true, 'data' => $results ), 200 );
	}

	// Helpers
	private function get_collection_params() {
		return array(
			'per_page' => array( 'required' => false, 'default' => 10, 'sanitize_callback' => 'absint' ),
			'page' => array( 'required' => false, 'default' => 1, 'sanitize_callback' => 'absint' ),
			'search' => array( 'required' => false, 'sanitize_callback' => 'sanitize_text_field' ),
			'status' => array( 'required' => false, 'default' => 'any', 'sanitize_callback' => 'sanitize_text_field' ),
		);
	}

	private function prepare_post_response( $post ) {
		$author = get_user_by( 'id', $post->post_author );

		return array(
			'id' => $post->ID,
			'title' => $post->post_title,
			'content' => $post->post_content,
			'excerpt' => $post->post_excerpt,
			'status' => $post->post_status,
			'type' => $post->post_type,
			'slug' => $post->post_name,
			'permalink' => get_permalink( $post->ID ),
			'created_at' => $post->post_date,
			'modified_at' => $post->post_modified,
			'author' => array(
				'id' => $post->post_author,
				'name' => $author ? $author->display_name : '',
			),
			'featured_image' => get_post_thumbnail_id( $post->ID ) ? array(
				'id' => get_post_thumbnail_id( $post->ID ),
				'url' => get_the_post_thumbnail_url( $post->ID, 'full' ),
			) : null,
		);
	}
}
