<?php
/**
 * Widget management for WP AI Control.
 * 27 tools for complete widget lifecycle management.
 *
 * @package WP_AI_Control
 * @subpackage WP_AI_Control/includes
 */

class WPAIC_Widgets {

	public static function register_routes() {
		$namespace = WPAIC_REST_NAMESPACE;

		// Widgets - Individual (12 tools)
		register_rest_route( $namespace, '/widgets', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'list_widgets' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/widgets/(?P<id>.+)', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'get_widget' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/widgets', array(
			'methods' => 'POST',
			'callback' => array( __CLASS__, 'create_widget' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		register_rest_route( $namespace, '/widgets/(?P<id>.+)', array(
			'methods' => 'PUT',
			'callback' => array( __CLASS__, 'update_widget' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		register_rest_route( $namespace, '/widgets/(?P<id>.+)', array(
			'methods' => 'DELETE',
			'callback' => array( __CLASS__, 'delete_widget' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		register_rest_route( $namespace, '/widgets/(?P<id>.+)/duplicate', array(
			'methods' => 'POST',
			'callback' => array( __CLASS__, 'duplicate_widget' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		register_rest_route( $namespace, '/widgets/available', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'list_available_widgets' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/widgets/(?P<id>.+)/settings', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'get_widget_settings' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/widgets/(?P<id>.+)/settings', array(
			'methods' => 'PUT',
			'callback' => array( __CLASS__, 'update_widget_settings' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		register_rest_route( $namespace, '/widgets/(?P<id>.+)/preview', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'preview_widget' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/widgets/bulk-update', array(
			'methods' => 'POST',
			'callback' => array( __CLASS__, 'bulk_update_widgets' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		register_rest_route( $namespace, '/widgets/bulk-delete', array(
			'methods' => 'POST',
			'callback' => array( __CLASS__, 'bulk_delete_widgets' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		// Sidebars (6 tools)
		register_rest_route( $namespace, '/sidebars', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'list_sidebars' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/sidebars/(?P<id>.+)', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'get_sidebar' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/sidebars', array(
			'methods' => 'POST',
			'callback' => array( __CLASS__, 'register_sidebar' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		register_rest_route( $namespace, '/sidebars/(?P<id>.+)', array(
			'methods' => 'DELETE',
			'callback' => array( __CLASS__, 'unregister_sidebar' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		register_rest_route( $namespace, '/sidebars/(?P<id>.+)/widgets', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'get_sidebar_widgets' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/sidebars/(?P<id>.+)/clear', array(
			'methods' => 'POST',
			'callback' => array( __CLASS__, 'clear_sidebar' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		// Widget Positioning (5 tools)
		register_rest_route( $namespace, '/widgets/(?P<id>.+)/move', array(
			'methods' => 'POST',
			'callback' => array( __CLASS__, 'move_widget' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		register_rest_route( $namespace, '/widgets/(?P<id>.+)/reorder', array(
			'methods' => 'POST',
			'callback' => array( __CLASS__, 'reorder_widget' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		register_rest_route( $namespace, '/sidebars/(?P<sidebar_id>.+)/add-widget', array(
			'methods' => 'POST',
			'callback' => array( __CLASS__, 'add_widget_to_sidebar' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		register_rest_route( $namespace, '/sidebars/(?P<sidebar_id>.+)/remove-widget/(?P<widget_id>.+)', array(
			'methods' => 'DELETE',
			'callback' => array( __CLASS__, 'remove_widget_from_sidebar' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		register_rest_route( $namespace, '/widgets/swap', array(
			'methods' => 'POST',
			'callback' => array( __CLASS__, 'swap_widgets' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		// Widget Analysis (4 tools)
		register_rest_route( $namespace, '/widgets/usage-stats', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'get_widget_usage_stats' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/widgets/orphaned', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'find_orphaned_widgets' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/widgets/duplicates', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'find_duplicate_widgets' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/sidebars/usage', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'analyze_sidebar_usage' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));
	}

	// ==================== WIDGETS - INDIVIDUAL (12 tools) ====================

	public static function list_widgets( $request ) {
		global $wp_registered_widgets;
		$sidebars_widgets = wp_get_sidebars_widgets();

		$all_widgets = array();
		foreach ( $sidebars_widgets as $sidebar => $widgets ) {
			if ( 'wp_inactive_widgets' === $sidebar || empty( $widgets ) ) continue;
			foreach ( $widgets as $widget_id ) {
				if ( isset( $wp_registered_widgets[$widget_id] ) ) {
					$all_widgets[] = array(
						'id' => $widget_id,
						'name' => $wp_registered_widgets[$widget_id]['name'],
						'sidebar' => $sidebar,
						'classname' => $wp_registered_widgets[$widget_id]['callback'][0]->id_base ?? '',
					);
				}
			}
		}

		return new WP_REST_Response( array( 'success' => true, 'data' => $all_widgets ), 200 );
	}

	public static function get_widget( $request ) {
		$widget_id = $request->get_param( 'id' );
		global $wp_registered_widgets;

		if ( ! isset( $wp_registered_widgets[$widget_id] ) ) {
			return new WP_Error( 'wpaic_not_found', 'Widget not found.', array( 'status' => 404 ) );
		}

		$widget = $wp_registered_widgets[$widget_id];
		$base = $widget['callback'][0]->id_base ?? '';
		$number = $widget['callback'][0]->number ?? '';

		$settings = get_option( 'widget_' . $base, array() );

		return new WP_REST_Response( array(
			'success' => true,
			'data' => array(
				'id' => $widget_id,
				'name' => $widget['name'],
				'classname' => $base,
				'number' => $number,
				'settings' => isset( $settings[$number] ) ? $settings[$number] : array(),
			),
		), 200 );
	}

	public static function create_widget( $request ) {
		$id_base = $request->get_param( 'id_base' );
		$sidebar = $request->get_param( 'sidebar' );
		$settings = $request->get_param( 'settings' );

		$widget_options = get_option( 'widget_' . $id_base, array() );
		$new_number = max( array_keys( $widget_options ) ) + 1;
		$widget_options[$new_number] = $settings ?: array();
		update_option( 'widget_' . $id_base, $widget_options );

		$widget_id = $id_base . '-' . $new_number;

		// Add to sidebar
		$sidebars_widgets = wp_get_sidebars_widgets();
		if ( isset( $sidebars_widgets[$sidebar] ) ) {
			$sidebars_widgets[$sidebar][] = $widget_id;
			wp_set_sidebars_widgets( $sidebars_widgets );
		}

		WPAIC_Audit::log( 'widget_created', 0, 'widget', get_current_user_id(), array( 'widget_id' => $widget_id ) );
		return new WP_REST_Response( array( 'success' => true, 'data' => array( 'id' => $widget_id ) ), 201 );
	}

	public static function update_widget( $request ) {
		$widget_id = $request->get_param( 'id' );
		$settings = $request->get_param( 'settings' );

		global $wp_registered_widgets;
		if ( ! isset( $wp_registered_widgets[$widget_id] ) ) {
			return new WP_Error( 'wpaic_not_found', 'Widget not found.', array( 'status' => 404 ) );
		}

		$widget = $wp_registered_widgets[$widget_id];
		$base = $widget['callback'][0]->id_base;
		$number = $widget['callback'][0]->number;

		$widget_options = get_option( 'widget_' . $base, array() );
		if ( $settings ) {
			$widget_options[$number] = array_merge( $widget_options[$number] ?? array(), $settings );
			update_option( 'widget_' . $base, $widget_options );
		}

		WPAIC_Audit::log( 'widget_updated', 0, 'widget', get_current_user_id(), array( 'widget_id' => $widget_id ) );
		return new WP_REST_Response( array( 'success' => true, 'message' => 'Widget updated.' ), 200 );
	}

	public static function delete_widget( $request ) {
		$widget_id = $request->get_param( 'id' );

		// Remove from all sidebars
		$sidebars_widgets = wp_get_sidebars_widgets();
		foreach ( $sidebars_widgets as $sidebar => $widgets ) {
			$key = array_search( $widget_id, $widgets );
			if ( false !== $key ) {
				unset( $sidebars_widgets[$sidebar][$key] );
			}
		}
		wp_set_sidebars_widgets( $sidebars_widgets );

		WPAIC_Audit::log( 'widget_deleted', 0, 'widget', get_current_user_id(), array( 'widget_id' => $widget_id ) );
		return new WP_REST_Response( array( 'success' => true, 'message' => 'Widget deleted.' ), 200 );
	}

	public static function duplicate_widget( $request ) {
		$widget_id = $request->get_param( 'id' );
		global $wp_registered_widgets;

		if ( ! isset( $wp_registered_widgets[$widget_id] ) ) {
			return new WP_Error( 'wpaic_not_found', 'Widget not found.', array( 'status' => 404 ) );
		}

		$widget = $wp_registered_widgets[$widget_id];
		$base = $widget['callback'][0]->id_base;
		$number = $widget['callback'][0]->number;

		$widget_options = get_option( 'widget_' . $base, array() );
		$new_number = max( array_keys( $widget_options ) ) + 1;
		$widget_options[$new_number] = $widget_options[$number];
		update_option( 'widget_' . $base, $widget_options );

		$new_widget_id = $base . '-' . $new_number;

		// Add to same sidebar
		$sidebars_widgets = wp_get_sidebars_widgets();
		foreach ( $sidebars_widgets as $sidebar => $widgets ) {
			$key = array_search( $widget_id, $widgets );
			if ( false !== $key ) {
				array_splice( $sidebars_widgets[$sidebar], $key + 1, 0, $new_widget_id );
				break;
			}
		}
		wp_set_sidebars_widgets( $sidebars_widgets );

		return new WP_REST_Response( array( 'success' => true, 'data' => array( 'id' => $new_widget_id ) ), 201 );
	}

	public static function list_available_widgets() {
		global $wp_widget_factory;

		$available = array();
		foreach ( $wp_widget_factory->widgets as $widget ) {
			$available[] = array(
				'id_base' => $widget->id_base,
				'name' => $widget->name,
				'description' => $widget->widget_options['description'] ?? '',
				'class' => get_class( $widget ),
			);
		}

		return new WP_REST_Response( array( 'success' => true, 'data' => $available ), 200 );
	}

	public static function get_widget_settings( $request ) {
		$widget_id = $request->get_param( 'id' );
		global $wp_registered_widgets;

		if ( ! isset( $wp_registered_widgets[$widget_id] ) ) {
			return new WP_Error( 'wpaic_not_found', 'Widget not found.', array( 'status' => 404 ) );
		}

		$widget = $wp_registered_widgets[$widget_id];
		$base = $widget['callback'][0]->id_base;
		$number = $widget['callback'][0]->number;

		$settings = get_option( 'widget_' . $base, array() );

		return new WP_REST_Response( array(
			'success' => true,
			'data' => isset( $settings[$number] ) ? $settings[$number] : array(),
		), 200 );
	}

	public static function update_widget_settings( $request ) {
		$widget_id = $request->get_param( 'id' );
		$settings = $request->get_param( 'settings' );
		return self::update_widget( $request );
	}

	public static function preview_widget( $request ) {
		$widget_id = $request->get_param( 'id' );
		global $wp_registered_widgets;

		if ( ! isset( $wp_registered_widgets[$widget_id] ) ) {
			return new WP_Error( 'wpaic_not_found', 'Widget not found.', array( 'status' => 404 ) );
		}

		ob_start();
		the_widget( $wp_registered_widgets[$widget_id]['callback'][0] );
		$html = ob_get_clean();

		return new WP_REST_Response( array( 'success' => true, 'data' => array( 'html' => $html ) ), 200 );
	}

	public static function bulk_update_widgets( $request ) {
		$widget_ids = $request->get_param( 'widget_ids' );
		$settings = $request->get_param( 'settings' );
		$results = array();

		foreach ( $widget_ids as $widget_id ) {
			global $wp_registered_widgets;
			if ( ! isset( $wp_registered_widgets[$widget_id] ) ) {
				$results[$widget_id] = 'not found';
				continue;
			}
			$widget = $wp_registered_widgets[$widget_id];
			$base = $widget['callback'][0]->id_base;
			$number = $widget['callback'][0]->number;

			$widget_options = get_option( 'widget_' . $base, array() );
			$widget_options[$number] = array_merge( $widget_options[$number] ?? array(), $settings );
			update_option( 'widget_' . $base, $widget_options );
			$results[$widget_id] = 'updated';
		}

		return new WP_REST_Response( array( 'success' => true, 'data' => $results ), 200 );
	}

	public static function bulk_delete_widgets( $request ) {
		$widget_ids = $request->get_param( 'widget_ids' );
		$results = array();

		foreach ( $widget_ids as $widget_id ) {
			$sidebars_widgets = wp_get_sidebars_widgets();
			foreach ( $sidebars_widgets as $sidebar => $widgets ) {
				$key = array_search( $widget_id, $widgets );
				if ( false !== $key ) {
					unset( $sidebars_widgets[$sidebar][$key] );
				}
			}
			wp_set_sidebars_widgets( $sidebars_widgets );
			$results[$widget_id] = 'deleted';
		}

		return new WP_REST_Response( array( 'success' => true, 'data' => $results ), 200 );
	}

	// ==================== SIDEBARS (6 tools) ====================

	public static function list_sidebars() {
		global $wp_registered_sidebars;
		$sidebars_widgets = wp_get_sidebars_widgets();

		$result = array();
		foreach ( $wp_registered_sidebars as $id => $sidebar ) {
			$result[] = array(
				'id' => $id,
				'name' => $sidebar['name'],
				'description' => $sidebar['description'] ?? '',
				'widget_count' => isset( $sidebars_widgets[$id] ) ? count( $sidebars_widgets[$id] ) : 0,
			);
		}

		return new WP_REST_Response( array( 'success' => true, 'data' => $result ), 200 );
	}

	public static function get_sidebar( $request ) {
		$sidebar_id = $request->get_param( 'id' );
		global $wp_registered_sidebars;

		if ( ! isset( $wp_registered_sidebars[$sidebar_id] ) ) {
			return new WP_Error( 'wpaic_not_found', 'Sidebar not found.', array( 'status' => 404 ) );
		}

		return new WP_REST_Response( array(
			'success' => true,
			'data' => $wp_registered_sidebars[$sidebar_id],
		), 200 );
	}

	public static function register_sidebar( $request ) {
		$args = array(
			'name' => $request->get_param( 'name' ),
			'id' => $request->get_param( 'id' ),
			'description' => $request->get_param( 'description' ) ?: '',
			'before_widget' => $request->get_param( 'before_widget' ) ?: '<div id="%1$s" class="widget %2$s">',
			'after_widget' => $request->get_param( 'after_widget' ) ?: '</div>',
			'before_title' => $request->get_param( 'before_title' ) ?: '<h2 class="widgettitle">',
			'after_title' => $request->get_param( 'after_title' ) ?: '</h2>',
		);

		register_sidebar( $args );

		WPAIC_Audit::log( 'sidebar_registered', 0, 'sidebar', get_current_user_id(), array( 'sidebar_id' => $args['id'] ) );
		return new WP_REST_Response( array( 'success' => true, 'message' => 'Sidebar registered.' ), 201 );
	}

	public static function unregister_sidebar( $request ) {
		$sidebar_id = $request->get_param( 'id' );
		unregister_sidebar( $sidebar_id );

		WPAIC_Audit::log( 'sidebar_unregistered', 0, 'sidebar', get_current_user_id(), array( 'sidebar_id' => $sidebar_id ) );
		return new WP_REST_Response( array( 'success' => true, 'message' => 'Sidebar unregistered.' ), 200 );
	}

	public static function get_sidebar_widgets( $request ) {
		$sidebar_id = $request->get_param( 'id' );
		$sidebars_widgets = wp_get_sidebars_widgets();

		if ( ! isset( $sidebars_widgets[$sidebar_id] ) ) {
			return new WP_Error( 'wpaic_not_found', 'Sidebar not found.', array( 'status' => 404 ) );
		}

		return new WP_REST_Response( array(
			'success' => true,
			'data' => $sidebars_widgets[$sidebar_id],
		), 200 );
	}

	public static function clear_sidebar( $request ) {
		$sidebar_id = $request->get_param( 'id' );
		$sidebars_widgets = wp_get_sidebars_widgets();

		if ( isset( $sidebars_widgets[$sidebar_id] ) ) {
			$sidebars_widgets[$sidebar_id] = array();
			wp_set_sidebars_widgets( $sidebars_widgets );
		}

		return new WP_REST_Response( array( 'success' => true, 'message' => 'Sidebar cleared.' ), 200 );
	}

	// ==================== WIDGET POSITIONING (5 tools) ====================

	public static function move_widget( $request ) {
		$widget_id = $request->get_param( 'id' );
		$from_sidebar = $request->get_param( 'from_sidebar' );
		$to_sidebar = $request->get_param( 'to_sidebar' );
		$position = $request->get_param( 'position' );

		$sidebars_widgets = wp_get_sidebars_widgets();

		// Remove from source
		if ( isset( $sidebars_widgets[$from_sidebar] ) ) {
			$key = array_search( $widget_id, $sidebars_widgets[$from_sidebar] );
			if ( false !== $key ) {
				unset( $sidebars_widgets[$from_sidebar][$key] );
			}
		}

		// Add to target
		if ( isset( $sidebars_widgets[$to_sidebar] ) ) {
			if ( $position ) {
				array_splice( $sidebars_widgets[$to_sidebar], $position, 0, $widget_id );
			} else {
				$sidebars_widgets[$to_sidebar][] = $widget_id;
			}
		}

		wp_set_sidebars_widgets( $sidebars_widgets );

		return new WP_REST_Response( array( 'success' => true, 'message' => 'Widget moved.' ), 200 );
	}

	public static function reorder_widget( $request ) {
		$sidebar_id = $request->get_param( 'sidebar' );
		$order = $request->get_param( 'order' );

		$sidebars_widgets = wp_get_sidebars_widgets();
		if ( isset( $sidebars_widgets[$sidebar_id] ) ) {
			$sidebars_widgets[$sidebar_id] = $order;
			wp_set_sidebars_widgets( $sidebars_widgets );
		}

		return new WP_REST_Response( array( 'success' => true, 'message' => 'Widgets reordered.' ), 200 );
	}

	public static function add_widget_to_sidebar( $request ) {
		$sidebar_id = $request->get_param( 'sidebar_id' );
		$id_base = $request->get_param( 'id_base' );
		$settings = $request->get_param( 'settings' );

		// Create widget first
		$widget_options = get_option( 'widget_' . $id_base, array() );
		$new_number = max( array_keys( $widget_options ) ) + 1;
		$widget_options[$new_number] = $settings ?: array();
		update_option( 'widget_' . $id_base, $widget_options );

		$widget_id = $id_base . '-' . $new_number;

		// Add to sidebar
		$sidebars_widgets = wp_get_sidebars_widgets();
		if ( isset( $sidebars_widgets[$sidebar_id] ) ) {
			$sidebars_widgets[$sidebar_id][] = $widget_id;
			wp_set_sidebars_widgets( $sidebars_widgets );
		}

		return new WP_REST_Response( array( 'success' => true, 'data' => array( 'id' => $widget_id ) ), 201 );
	}

	public static function remove_widget_from_sidebar( $request ) {
		$sidebar_id = $request->get_param( 'sidebar_id' );
		$widget_id = $request->get_param( 'widget_id' );

		$sidebars_widgets = wp_get_sidebars_widgets();
		if ( isset( $sidebars_widgets[$sidebar_id] ) ) {
			$key = array_search( $widget_id, $sidebars_widgets[$sidebar_id] );
			if ( false !== $key ) {
				unset( $sidebars_widgets[$sidebar_id][$key] );
				wp_set_sidebars_widgets( $sidebars_widgets );
			}
		}

		return new WP_REST_Response( array( 'success' => true, 'message' => 'Widget removed from sidebar.' ), 200 );
	}

	public static function swap_widgets( $request ) {
		$widget_id_1 = $request->get_param( 'widget_id_1' );
		$widget_id_2 = $request->get_param( 'widget_id_2' );

		$sidebars_widgets = wp_get_sidebars_widgets();

		foreach ( $sidebars_widgets as $sidebar => $widgets ) {
			$key1 = array_search( $widget_id_1, $widgets );
			$key2 = array_search( $widget_id_2, $widgets );
			if ( false !== $key1 && false !== $key2 ) {
				$sidebars_widgets[$sidebar][$key1] = $widget_id_2;
				$sidebars_widgets[$sidebar][$key2] = $widget_id_1;
				wp_set_sidebars_widgets( $sidebars_widgets );
				break;
			}
		}

		return new WP_REST_Response( array( 'success' => true, 'message' => 'Widgets swapped.' ), 200 );
	}

	// ==================== WIDGET ANALYSIS (4 tools) ====================

	public static function get_widget_usage_stats() {
		global $wp_registered_widgets, $wp_registered_sidebars;
		$sidebars_widgets = wp_get_sidebars_widgets();

		$stats = array(
			'total_widgets' => count( $wp_registered_widgets ),
			'total_sidebars' => count( $wp_registered_sidebars ),
			'widgets_per_sidebar' => array(),
			'most_used_widget' => '',
			'empty_sidebars' => array(),
		);

		$widget_counts = array();
		foreach ( $sidebars_widgets as $sidebar => $widgets ) {
			if ( 'wp_inactive_widgets' === $sidebar ) continue;
			$stats['widgets_per_sidebar'][$sidebar] = count( $widgets );
			if ( empty( $widgets ) ) {
				$stats['empty_sidebars'][] = $sidebar;
			}
			foreach ( $widgets as $widget_id ) {
				$base = preg_replace( '/-\d+$/', '', $widget_id );
				$widget_counts[$base] = isset( $widget_counts[$base] ) ? $widget_counts[$base] + 1 : 1;
			}
		}

		arsort( $widget_counts );
		$stats['most_used_widget'] = key( $widget_counts );

		return new WP_REST_Response( array( 'success' => true, 'data' => $stats ), 200 );
	}

	public static function find_orphaned_widgets() {
		global $wp_registered_widgets;
		$sidebars_widgets = wp_get_sidebars_widgets();

		$active_widgets = array();
		foreach ( $sidebars_widgets as $sidebar => $widgets ) {
			if ( 'wp_inactive_widgets' === $sidebar ) continue;
			$active_widgets = array_merge( $active_widgets, $widgets );
		}

		$orphaned = array();
		foreach ( $wp_registered_widgets as $widget_id => $widget ) {
			if ( ! in_array( $widget_id, $active_widgets ) && 'wp_inactive_widgets' !== $sidebar ) {
				$orphaned[] = $widget_id;
			}
		}

		return new WP_REST_Response( array( 'success' => true, 'data' => $orphaned ), 200 );
	}

	public static function find_duplicate_widgets() {
		global $wp_registered_widgets;
		$settings_map = array();
		$duplicates = array();

		foreach ( $wp_registered_widgets as $widget_id => $widget ) {
			$base = $widget['callback'][0]->id_base;
			$number = $widget['callback'][0]->number;
			$options = get_option( 'widget_' . $base, array() );
			$hash = md5( serialize( $options[$number] ?? array() ) );

			if ( isset( $settings_map[$hash] ) ) {
				$duplicates[] = array(
					'original' => $settings_map[$hash],
					'duplicate' => $widget_id,
				);
			} else {
				$settings_map[$hash] = $widget_id;
			}
		}

		return new WP_REST_Response( array( 'success' => true, 'data' => $duplicates ), 200 );
	}

	public static function analyze_sidebar_usage() {
		global $wp_registered_sidebars;
		$sidebars_widgets = wp_get_sidebars_widgets();

		$analysis = array();
		foreach ( $wp_registered_sidebars as $id => $sidebar ) {
			$widget_count = isset( $sidebars_widgets[$id] ) ? count( $sidebars_widgets[$id] ) : 0;
			$analysis[] = array(
				'sidebar' => $sidebar['name'],
				'widget_count' => $widget_count,
				'status' => $widget_count > 0 ? 'active' : 'empty',
			);
		}

		return new WP_REST_Response( array( 'success' => true, 'data' => $analysis ), 200 );
	}
}
