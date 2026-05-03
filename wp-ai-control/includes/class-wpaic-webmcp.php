<?php
/**
 * WebMCP (Web Model Context Protocol) support for WP AI Control.
 * Allows browser-based AI tools (Chrome 146+, etc.) to connect directly.
 *
 * @package WP_AI_Control
 * @subpackage WP_AI_Control/includes
 */

class WPAIC_WebMCP {

	public static function register_routes() {
		$namespace = WPAIC_REST_NAMESPACE;

		// MCP Protocol endpoint
		register_rest_route( $namespace, '/mcp', array(
			'methods' => 'POST',
			'callback' => array( __CLASS__, 'handle_mcp_request' ),
			'permission_callback' => array( __CLASS__, 'check_mcp_auth' ),
		));

		// SSE (Server-Sent Events) endpoint for streaming
		register_rest_route( $namespace, '/mcp/sse', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'handle_sse_connection' ),
			'permission_callback' => array( __CLASS__, 'check_mcp_auth' ),
		));
	}

	public static function check_mcp_auth( $request ) {
		// Check API key from header or query param
		$api_key = $request->get_header( 'x-wpaic-api-key' );
		if ( empty( $api_key ) ) {
			$api_key = $request->get_param( 'api_key' );
		}

		if ( empty( $api_key ) ) {
			return new WP_Error( 'wpaic_missing_auth', 'API key required.', array( 'status' => 401 ) );
		}

		$valid = WPAIC_Auth::validate_api_key( $api_key );
		if ( ! $valid ) {
			return new WP_Error( 'wpaic_invalid_key', 'Invalid API key.', array( 'status' => 401 ) );
		}

		// Check rate limiting
		if ( ! WPAIC_Auth::check_rate_limit( $valid->api_key ) ) {
			return new WP_Error( 'wpaic_rate_limit', 'Rate limit exceeded.', array( 'status' => 429 ) );
		}

		return true;
	}

	public static function handle_mcp_request( $request ) {
		$body = $request->get_json_params();
		$method = $body['method'] ?? '';
		$params = $body['params'] ?? array();
		$id = $body['id'] ?? null;

		switch ( $method ) {
			case 'initialize':
				return self::mcp_initialize( $id );

			case 'tools/list':
				return self::mcp_list_tools( $id );

			case 'tools/call':
				return self::mcp_call_tool( $id, $params );

			case 'resources/list':
				return self::mcp_list_resources( $id );

			case 'prompts/list':
				return self::mcp_list_prompts( $id );

			default:
				return new WP_REST_Response( array(
					'jsonrpc' => '2.0',
					'id' => $id,
					'error' => array(
						'code' => -32601,
						'message' => 'Method not found: ' . $method,
					),
				), 200 );
		}
	}

	private static function mcp_initialize( $id ) {
		return new WP_REST_Response( array(
			'jsonrpc' => '2.0',
			'id' => $id,
			'result' => array(
				'protocolVersion' => '2024-11-05',
				'serverInfo' => array(
					'name' => 'wp-ai-control',
					'version' => WPAIC_VERSION,
				),
				'capabilities' => array(
					'tools' => array( 'listChanged' => false ),
				),
			),
		), 200 );
	}

	private static function mcp_list_tools( $id ) {
		$tools = self::get_all_tool_definitions();

		return new WP_REST_Response( array(
			'jsonrpc' => '2.0',
			'id' => $id,
			'result' => array(
				'tools' => $tools,
			),
		), 200 );
	}

	private static function mcp_call_tool( $id, $params ) {
		$tool_name = $params['name'] ?? '';
		$arguments = $params['arguments'] ?? array();

		// Map tool name to REST endpoint
		$endpoint = self::map_tool_to_endpoint( $tool_name, $arguments );
		if ( is_wp_error( $endpoint ) ) {
			return new WP_REST_Response( array(
				'jsonrpc' => '2.0',
				'id' => $id,
				'error' => array(
					'code' => -32602,
					'message' => $endpoint->get_error_message(),
				),
			), 200 );
		}

		// Call the REST API internally
		$request = new WP_REST_Request( $endpoint['method'], $endpoint['route'] );
		foreach ( $arguments as $key => $value ) {
			$request->set_param( $key, $value );
		}
		$request->set_header( 'authorization', 'Bearer ' . $_SERVER['HTTP_X_WPAIC_API_KEY'] ?? '' );

		$response = rest_do_request( $request );

		if ( is_wp_error( $response ) ) {
			return new WP_REST_Response( array(
				'jsonrpc' => '2.0',
				'id' => $id,
				'error' => array(
					'code' => -32603,
					'message' => $response->get_error_message(),
				),
			), 200 );
		}

		$data = $response->get_data();
		$content = isset( $data['data'] ) ? $data['data'] : $data;

		return new WP_REST_Response( array(
			'jsonrpc' => '2.0',
			'id' => $id,
			'result' => array(
				'content' => array(
					array(
						'type' => 'text',
						'text' => is_string( $content ) ? $content : wp_json_encode( $content, JSON_PRETTY_PRINT ),
					),
				),
			),
		), 200 );
	}

	private static function mcp_list_resources( $id ) {
		return new WP_REST_Response( array(
			'jsonrpc' => '2.0',
			'id' => $id,
			'result' => array(
				'resources' => array(
					array(
						'uri' => 'wpai://site-info',
						'name' => 'WordPress Site Information',
					),
				),
			),
		), 200 );
	}

	private static function mcp_list_prompts( $id ) {
		return new WP_REST_Response( array(
			'jsonrpc' => '2.0',
			'id' => $id,
			'result' => array(
				'prompts' => array(
					array(
						'name' => 'analyze-seo-prompt',
						'description' => 'Analyze SEO for a page',
						'arguments' => array(
							array( 'name' => 'page_id', 'required' => true ),
						),
					),
				),
			),
		), 200 );
	}

	private static function get_all_tool_definitions() {
		$tools = array();

		// Define all 166 tools with their MCP schema
		$tool_defs = array(
			// Context (3)
			array( 'name' => 'get-site-context', 'description' => 'Get WordPress site information', 'inputSchema' => array( 'type' => 'object', 'properties' => array() ) ),
			array( 'name' => 'get-builder-info', 'description' => 'Detect active page builder', 'inputSchema' => array( 'type' => 'object', 'properties' => array() ) ),
			array( 'name' => 'get-theme-docs', 'description' => 'Get theme documentation', 'inputSchema' => array( 'type' => 'object', 'properties' => array() ) ),

			// Pages (5)
			array( 'name' => 'list-pages', 'description' => 'List pages', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'search' => array( 'type' => 'string' ), 'status' => array( 'type' => 'string' ), 'per_page' => array( 'type' => 'number' ), 'page' => array( 'type' => 'number' ) ) ) ),
			array( 'name' => 'read-page', 'description' => 'Read a page', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'id' => array( 'type' => 'number', 'required' => true ) ) ) ),
			array( 'name' => 'update-page', 'description' => 'Update a page', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'id' => array( 'type' => 'number', 'required' => true ), 'title' => array( 'type' => 'string' ), 'content' => array( 'type' => 'string' ), 'status' => array( 'type' => 'string' ) ) ) ),
			array( 'name' => 'delete-page', 'description' => 'Delete a page', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'id' => array( 'type' => 'number', 'required' => true ) ) ) ),
			array( 'name' => 'create-page-duplicate', 'description' => 'Duplicate a page', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'id' => array( 'type' => 'number', 'required' => true ) ) ) ),

			// Posts (4)
			array( 'name' => 'list-posts', 'description' => 'List posts', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'search' => array( 'type' => 'string' ), 'status' => array( 'type' => 'string' ) ) ) ),
			array( 'name' => 'read-post', 'description' => 'Read a post', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'id' => array( 'type' => 'number', 'required' => true ) ) ) ),
			array( 'name' => 'update-post', 'description' => 'Update a post', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'id' => array( 'type' => 'number', 'required' => true ), 'title' => array( 'type' => 'string' ), 'content' => array( 'type' => 'string' ) ) ) ),
			array( 'name' => 'delete-post', 'description' => 'Delete a post', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'id' => array( 'type' => 'number', 'required' => true ) ) ) ),

			// Builder (2)
			array( 'name' => 'extract-builder-content', 'description' => 'Extract builder content', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'page_id' => array( 'type' => 'number', 'required' => true ), 'builder' => array( 'type' => 'string', 'required' => true ) ) ) ),
			array( 'name' => 'inject-builder-content', 'description' => 'Inject builder content', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'page_id' => array( 'type' => 'number', 'required' => true ), 'builder' => array( 'type' => 'string', 'required' => true ), 'content' => array( 'type' => 'object', 'required' => true ) ) ) ),

			// Media (4)
			array( 'name' => 'upload-media', 'description' => 'Upload media', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'url' => array( 'type' => 'string' ), 'filename' => array( 'type' => 'string' ) ) ),
			array( 'name' => 'list-media', 'description' => 'List media', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'mime_type' => array( 'type' => 'string' ) ) ) ),
			array( 'name' => 'delete-media', 'description' => 'Delete media', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'id' => array( 'type' => 'number', 'required' => true ) ) ) ),
			array( 'name' => 'update-media-meta', 'description' => 'Update media meta', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'id' => array( 'type' => 'number', 'required' => true ), 'meta' => array( 'type' => 'object' ) ) ) ),

			// Analysis (4)
			array( 'name' => 'analyze-seo', 'description' => 'Analyze SEO', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'page_id' => array( 'type' => 'number', 'required' => true ) ) ) ),
			array( 'name' => 'analyze-performance', 'description' => 'Analyze performance', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'page_id' => array( 'type' => 'number', 'required' => true ) ) ) ),
			array( 'name' => 'analyze-aeo', 'description' => 'Analyze AEO', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'page_id' => array( 'type' => 'number', 'required' => true ) ) ) ),
			array( 'name' => 'analyze-accessibility', 'description' => 'Analyze accessibility', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'page_id' => array( 'type' => 'number', 'required' => true ) ) ) ),

			// Plugins (4)
			array( 'name' => 'list-plugins', 'description' => 'List plugins', 'inputSchema' => array( 'type' => 'object', 'properties' => array() ) ),
			array( 'name' => 'install-plugin', 'description' => 'Install plugin', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'slug' => array( 'type' => 'string', 'required' => true ) ) ) ),
			array( 'name' => 'activate-plugin', 'description' => 'Activate plugin', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'slug' => array( 'type' => 'string', 'required' => true ) ) ) ),
			array( 'name' => 'deactivate-plugin', 'description' => 'Deactivate plugin', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'slug' => array( 'type' => 'string', 'required' => true ) ) ) ),

			// Menus (3)
			array( 'name' => 'list-menus', 'description' => 'List menus', 'inputSchema' => array( 'type' => 'object', 'properties' => array() ) ),
			array( 'name' => 'get-menu', 'description' => 'Get menu', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'id' => array( 'type' => 'number', 'required' => true ) ) ) ),
			array( 'name' => 'list-menu-locations', 'description' => 'List menu locations', 'inputSchema' => array( 'type' => 'object', 'properties' => array() ) ) ),

			// Taxonomies (2)
			array( 'name' => 'list-taxonomies', 'description' => 'List taxonomies', 'inputSchema' => array( 'type' => 'object', 'properties' => array() ) ),
			array( 'name' => 'list-terms', 'description' => 'List terms', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'taxonomy' => array( 'type' => 'string', 'required' => true ) ) ) ),
		);

		// Add Users (5)
		$tool_defs = array_merge( $tool_defs, array(
			array( 'name' => 'list-users', 'description' => 'List users', 'inputSchema' => array( 'type' => 'object', 'properties' => array() ) ),
			array( 'name' => 'get-user', 'description' => 'Get user', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'id' => array( 'type' => 'number', 'required' => true ) ) ) ),
			array( 'name' => 'create-user', 'description' => 'Create user', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'username' => array( 'type' => 'string' ), 'email' => array( 'type' => 'string' ) ) ) ),
			array( 'name' => 'update-user', 'description' => 'Update user', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'id' => array( 'type' => 'number', 'required' => true ) ) ) ),
			array( 'name' => 'delete-user', 'description' => 'Delete user', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'id' => array( 'type' => 'number', 'required' => true ) ) ) ),
		));

		// Add Comments (5)
		$tool_defs = array_merge( $tool_defs, array(
			array( 'name' => 'list-comments', 'description' => 'List comments', 'inputSchema' => array( 'type' => 'object', 'properties' => array() ) ),
			array( 'name' => 'get-comment', 'description' => 'Get comment', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'id' => array( 'type' => 'number', 'required' => true ) ) ) ),
			array( 'name' => 'approve-comment', 'description' => 'Approve comment', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'id' => array( 'type' => 'number', 'required' => true ) ) ) ),
			array( 'name' => 'spam-comment', 'description' => 'Spam comment', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'id' => array( 'type' => 'number', 'required' => true ) ) ) ),
			array( 'name' => 'delete-comment', 'description' => 'Delete comment', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'id' => array( 'type' => 'number', 'required' => true ) ) ) ),
		));

		// Add WooCommerce (21)
		$tool_defs = array_merge( $tool_defs, array(
			array( 'name' => 'list-products', 'description' => 'List WooCommerce products', 'inputSchema' => array( 'type' => 'object', 'properties' => array() ) ),
			array( 'name' => 'get-product', 'description' => 'Get product', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'id' => array( 'type' => 'number', 'required' => true ) ) ) ),
			array( 'name' => 'create-product', 'description' => 'Create product', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'name' => array( 'type' => 'string' ) ) ) ),
			array( 'name' => 'update-product', 'description' => 'Update product', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'id' => array( 'type' => 'number', 'required' => true ) ) ) ),
			array( 'name' => 'delete-product', 'description' => 'Delete product', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'id' => array( 'type' => 'number', 'required' => true ) ) ) ),
		));

		// Add ACF (simplified for WebMCP)
		$tool_defs = array_merge( $tool_defs, array(
			array( 'name' => 'list-field-groups', 'description' => 'List ACF field groups', 'inputSchema' => array( 'type' => 'object', 'properties' => array() ) ),
			array( 'name' => 'get-field-group', 'description' => 'Get field group', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'id' => array( 'type' => 'number', 'required' => true ) ) ) ),
			array( 'name' => 'create-field-group', 'description' => 'Create field group', 'inputSchema' => array( 'type' => 'object', 'properties' => array( 'title' => array( 'type' => 'string' ) ) ) ),
		));

		// Add Widgets (simplified)
		$tool_defs = array_merge( $tool_defs, array(
			array( 'name' => 'list-widgets', 'description' => 'List widgets', 'inputSchema' => array( 'type' => 'object', 'properties' => array() ) ),
			array( 'name' => 'list-sidebars', 'description' => 'List sidebars', 'inputSchema' => array( 'type' => 'object', 'properties' => array() ) ),
		));

		// Format for MCP
		foreach ( $tool_defs as $tool ) {
			$tools[] = array(
				'name' => $tool['name'],
				'description' => $tool['description'],
				'inputSchema' => $tool['inputSchema'],
			);
		}

		return $tools;
	}

	private static function map_tool_to_endpoint( $tool_name, &$arguments ) {
		// Map tool names to REST API endpoints
		$mapping = array(
			'get-site-context' => array( 'GET', '/site-info' ),
			'get-builder-info' => array( 'GET', '/builder-info' ),
			'get-theme-docs' => array( 'GET', '/theme-docs' ),
			'list-pages' => array( 'GET', '/pages' ),
			'read-page' => array( 'GET', '/pages/{id}' ),
			'update-page' => array( 'PUT', '/pages/{id}' ),
			'delete-page' => array( 'DELETE', '/pages/{id}' ),
			'create-page-duplicate' => array( 'POST', '/pages/{id}/duplicate' ),
			'list-posts' => array( 'GET', '/posts' ),
			'read-post' => array( 'GET', '/posts/{id}' ),
			'update-post' => array( 'PUT', '/posts/{id}' ),
			'delete-post' => array( 'DELETE', '/posts/{id}' ),
			'extract-builder-content' => array( 'GET', '/builder/{builder}/extract/{page_id}' ),
			'inject-builder-content' => array( 'POST', '/builder/{builder}/inject/{page_id}' ),
			'upload-media' => array( 'POST', '/media/upload' ),
			'list-media' => array( 'GET', '/media' ),
			'delete-media' => array( 'DELETE', '/media/{id}' ),
			'update-media-meta' => array( 'PUT', '/media/{id}/meta' ),
			'analyze-seo' => array( 'GET', '/analyze/seo/{page_id}' ),
			'analyze-performance' => array( 'GET', '/analyze/performance/{page_id}' ),
			'analyze-aeo' => array( 'GET', '/analyze/aeo/{page_id}' ),
			'analyze-accessibility' => array( 'GET', '/analyze/accessibility/{page_id}' ),
			'list-plugins' => array( 'GET', '/plugins' ),
			'install-plugin' => array( 'POST', '/plugins/install' ),
			'activate-plugin' => array( 'POST', '/plugins/{slug}/activate' ),
			'deactivate-plugin' => array( 'POST', '/plugins/{slug}/deactivate' ),
			'list-menus' => array( 'GET', '/menus' ),
			'get-menu' => array( 'GET', '/menus/{id}' ),
			'list-menu-locations' => array( 'GET', '/menus/locations' ),
			'list-taxonomies' => array( 'GET', '/taxonomies' ),
			'list-terms' => array( 'GET', '/taxonomies/{taxonomy}/terms' ),
			'list-users' => array( 'GET', '/users' ),
			'get-user' => array( 'GET', '/users/{id}' ),
			'create-user' => array( 'POST', '/users' ),
			'update-user' => array( 'PUT', '/users/{id}' ),
			'delete-user' => array( 'DELETE', '/users/{id}' ),
			'list-comments' => array( 'GET', '/comments' ),
			'get-comment' => array( 'GET', '/comments/{id}' ),
			'approve-comment' => array( 'POST', '/comments/{id}/approve' ),
			'spam-comment' => array( 'POST', '/comments/{id}/spam' ),
			'delete-comment' => array( 'DELETE', '/comments/{id}' ),
			'list-products' => array( 'GET', '/wc/products' ),
			'get-product' => array( 'GET', '/wc/products/{id}' ),
			'create-product' => array( 'POST', '/wc/products' ),
			'update-product' => array( 'PUT', '/wc/products/{id}' ),
			'delete-product' => array( 'DELETE', '/wc/products/{id}' ),
			'list-field-groups' => array( 'GET', '/acf/field-groups' ),
			'get-field-group' => array( 'GET', '/acf/field-groups/{id}' ),
			'create-field-group' => array( 'POST', '/acf/field-groups' ),
			'list-widgets' => array( 'GET', '/widgets' ),
			'list-sidebars' => array( 'GET', '/sidebars' ),
		);

		if ( ! isset( $mapping[$tool_name] ) ) {
			return new WP_Error( 'wpaic_tool_not_found', 'Tool not found: ' . $tool_name );
		}

		$endpoint = $mapping[$tool_name];
		$method = $endpoint[0];
		$route = $endpoint[1];

		// Replace path parameters
		preg_match_all( '/\{([^}]+)\}/', $route, $matches );
		if ( ! empty( $matches[1] ) ) {
			foreach ( $matches[1] as $param ) {
				if ( isset( $arguments[$param] ) ) {
					$route = str_replace( '{' . $param . '}', $arguments[$param], $route );
					unset( $arguments[$param] );
				}
			}
		}

		return array( 'method' => $method, 'route' => WPAIC_REST_NAMESPACE . $route );
	}

	public static function handle_sse_connection( $request ) {
		// Set headers for SSE
		header( 'Content-Type: text/event-stream' );
		header( 'Cache-Control: no-cache' );
		header( 'Connection: keep-alive' );
		header( 'X-Accel-Buffering: no' );

		// Send initial connection event
		echo "event: connection\r\n";
		echo "data: {\"type\":\"connected\"}\r\n\r\n";
		flush();

		// Keep connection alive and send periodic pings
		$start_time = time();
		while ( ( time() - $start_time ) < 60 ) { // 60 second timeout
			echo "event: ping\r\n";
			echo "data: {\"timestamp\":" . time() . \"}\r\n\r\n";
			flush();
			sleep( 15 );
		}

		exit();
	}
}
