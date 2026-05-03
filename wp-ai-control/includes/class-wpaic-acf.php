<?php
/**
 * Advanced Custom Fields (ACF) integration for WP AI Control.
 * 54 tools for ACF fields, field groups, and post meta.
 *
 * @package WP_AI_Control
 * @subpackage WP_AI_Control/includes
 */

class WPAIC_ACF {

	public static function register_routes() {
		if ( ! class_exists( 'ACF' ) && ! class_exists( 'acf' ) ) {
			return;
		}

		$namespace = WPAIC_REST_NAMESPACE;

		// Field Groups (8 tools)
		register_rest_route( $namespace, '/acf/field-groups', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'list_field_groups' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/acf/field-groups/(?P<id>\d+)', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'get_field_group' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/acf/field-groups', array(
			'methods' => 'POST',
			'callback' => array( __CLASS__, 'create_field_group' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		register_rest_route( $namespace, '/acf/field-groups/(?P<id>\d+)', array(
			'methods' => 'PUT',
			'callback' => array( __CLASS__, 'update_field_group' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		register_rest_route( $namespace, '/acf/field-groups/(?P<id>\d+)', array(
			'methods' => 'DELETE',
			'callback' => array( __CLASS__, 'delete_field_group' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		register_rest_route( $namespace, '/acf/field-groups/(?P<id>\d+)/fields', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'list_fields_in_group' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/acf/field-groups/(?P<id>\d+)/duplicate', array(
			'methods' => 'POST',
			'callback' => array( __CLASS__, 'duplicate_field_group' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		register_rest_route( $namespace, '/acf/field-groups/assign', array(
			'methods' => 'POST',
			'callback' => array( __CLASS__, 'assign_field_group' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		// Fields (12 tools)
		register_rest_route( $namespace, '/acf/fields', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'list_fields' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/acf/fields/(?P<id>\d+)', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'get_field' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/acf/fields', array(
			'methods' => 'POST',
			'callback' => array( __CLASS__, 'create_field' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		register_rest_route( $namespace, '/acf/fields/(?P<id>\d+)', array(
			'methods' => 'PUT',
			'callback' => array( __CLASS__, 'update_field' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		register_rest_route( $namespace, '/acf/fields/(?P<id>\d+)', array(
			'methods' => 'DELETE',
			'callback' => array( __CLASS__, 'delete_field' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		register_rest_route( $namespace, '/acf/fields/duplicate', array(
			'methods' => 'POST',
			'callback' => array( __CLASS__, 'duplicate_field' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		register_rest_route( $namespace, '/acf/fields/export', array(
			'methods' => 'POST',
			'callback' => array( __CLASS__, 'export_fields' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/acf/fields/import', array(
			'methods' => 'POST',
			'callback' => array( __CLASS__, 'import_fields' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		register_rest_route( $namespace, '/acf/fields/validate', array(
			'methods' => 'POST',
			'callback' => array( __CLASS__, 'validate_field' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/acf/fields/bulk-update', array(
			'methods' => 'POST',
			'callback' => array( __CLASS__, 'bulk_update_fields' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		register_rest_route( $namespace, '/acf/fields/(?P<id>\d+)/clone', array(
			'methods' => 'POST',
			'callback' => array( __CLASS__, 'clone_field' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		// Post Meta with ACF (8 tools)
		register_rest_route( $namespace, '/acf/post/(?P<post_id>\d+)/fields', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'get_post_acf_fields' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/acf/post/(?P<post_id>\d+)/fields', array(
			'methods' => 'POST',
			'callback' => array( __CLASS__, 'update_post_acf_fields' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		register_rest_route( $namespace, '/acf/post/(?P<post_id>\d+)/field/(?P<field_key>.+)', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'get_post_acf_field' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/acf/post/(?P<post_id>\d+)/field/(?P<field_key>.+)', array(
			'methods' => 'PUT',
			'callback' => array( __CLASS__, 'update_post_acf_field' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		register_rest_route( $namespace, '/acf/post/(?P<post_id>\d+)/render', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'render_post_acf_fields' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/acf/post/(?P<post_id>\d+)/layouts', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'get_post_layouts' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/acf/post/(?P<post_id>\d+)/flexible-content', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'get_flexible_content' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/acf/post/(?P<post_id>\d+)/repeater/(?P<field_key>.+)', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'get_repeater_field' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		// Options Pages (4 tools)
		register_rest_route( $namespace, '/acf/options-pages', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'list_options_pages' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/acf/options-pages', array(
			'methods' => 'POST',
			'callback' => array( __CLASS__, 'create_options_page' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		register_rest_route( $namespace, '/acf/options-pages/(?P<slug>.+)', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'get_options_page' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/acf/options-pages/(?P<slug>.+)/fields', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'get_options_page_fields' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		// Field Types & Validation (6 tools)
		register_rest_route( $namespace, '/acf/field-types', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'list_field_types' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/acf/field-types/(?P<type>.+)', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'get_field_type' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/acf/validate-rule', array(
			'methods' => 'POST',
			'callback' => array( __CLASS__, 'validate_acf_rule' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/acf/location-rules', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'list_location_rules' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/acf/field-groups/(?P<id>\d+)/rules', array(
			'methods' => 'PUT',
			'callback' => array( __CLASS__, 'update_location_rules' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		register_rest_route( $namespace, '/acf/clone-fields', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'list_cloneable_fields' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		// Bulk Operations (4 tools)
		register_rest_route( $namespace, '/acf/bulk-update-meta', array(
			'methods' => 'POST',
			'callback' => array( __CLASS__, 'bulk_update_acf_meta' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		register_rest_route( $namespace, '/acf/bulk-clone-fields', array(
			'methods' => 'POST',
			'callback' => array( __CLASS__, 'bulk_clone_fields' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		register_rest_route( $namespace, '/acf/export-group/(?P<id>\d+)', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'export_field_group' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/acf/import-group', array(
			'methods' => 'POST',
			'callback' => array( __CLASS__, 'import_field_group' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		// Field Group Analysis (6 tools)
		register_rest_route( $namespace, '/acf/field-groups/usage', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'analyze_field_group_usage' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/acf/field-groups/dependencies', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'get_field_dependencies' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/acf/field-groups/orphaned', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'find_orphaned_fields' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/acf/field-groups/duplicate-check', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'check_duplicate_fields' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/acf/field-groups/conditional-logic', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'get_conditional_logic' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/acf/field-groups/performance', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'analyze_acf_performance' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));
	}

	// ==================== FIELD GROUPS (8 tools) ====================

	public static function list_field_groups( $request ) {
		$groups = acf_get_field_groups();
		$data = array_map( function( $group ) {
			return array(
				'id' => $group['ID'], 'title' => $group['title'], 'key' => $group['key'],
				'position' => $group['position'], 'style' => $group['style'],
				'location' => $group['location'], 'active' => $group['active'],
			);
		}, $groups ?: array() );

		return new WP_REST_Response( array( 'success' => true, 'data' => $data ), 200 );
	}

	public static function get_field_group( $request ) {
		$group = acf_get_field_group( $request->get_param( 'id' ) );
		if ( ! $group ) return new WP_Error( 'wpaic_not_found', 'Field group not found.', array( 'status' => 404 ) );

		return new WP_REST_Response( array( 'success' => true, 'data' => $group ), 200 );
	}

	public static function create_field_group( $request ) {
		$args = array(
			'title' => $request->get_param( 'title' ),
			'key' => $request->get_param( 'key' ) ?: 'group_' . uniqid(),
			'position' => $request->get_param( 'position' ) ?: 'normal',
			'style' => $request->get_param( 'style' ) ?: 'default',
			'location' => $request->get_param( 'location' ) ?: array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'post' ) ),
		);

		$result = acf_update_field_group( $args );
		if ( ! $result ) return new WP_Error( 'wpaic_create_failed', 'Failed to create field group.', array( 'status' => 500 ) );

		WPAIC_Audit::log( 'acf_field_group_created', $result['ID'], 'acf_field_group', get_current_user_id(), array( 'title' => $args['title'] ) );
		return new WP_REST_Response( array( 'success' => true, 'data' => $result ), 201 );
	}

	public static function update_field_group( $request ) {
		$group_id = $request->get_param( 'id' );
		$group = acf_get_field_group( $group_id );
		if ( ! $group ) return new WP_Error( 'wpaic_not_found', 'Field group not found.', array( 'status' => 404 ) );

		if ( $request->has_param( 'title' ) ) $group['title'] = $request->get_param( 'title' );
		if ( $request->has_param( 'position' ) ) $group['position'] = $request->get_param( 'position' );
		if ( $request->has_param( 'style' ) ) $group['style'] = $request->get_param( 'style' );

		$result = acf_update_field_group( $group );
		WPAIC_Audit::log( 'acf_field_group_updated', $group_id, 'acf_field_group', get_current_user_id(), array() );
		return new WP_REST_Response( array( 'success' => true, 'message' => 'Field group updated.' ), 200 );
	}

	public static function delete_field_group( $request ) {
		$group_id = $request->get_param( 'id' );
		$group = acf_get_field_group( $group_id );
		if ( ! $group ) return new WP_Error( 'wpaic_not_found', 'Field group not found.', array( 'status' => 404 ) );

		acf_delete_field_group( $group['key'] );
		WPAIC_Audit::log( 'acf_field_group_deleted', $group_id, 'acf_field_group', get_current_user_id(), array() );
		return new WP_REST_Response( array( 'success' => true, 'message' => 'Field group deleted.' ), 200 );
	}

	public static function list_fields_in_group( $request ) {
		$group_id = $request->get_param( 'id' );
		$group = acf_get_field_group( $group_id );
		if ( ! $group ) return new WP_Error( 'wpaic_not_found', 'Field group not found.', array( 'status' => 404 ) );

		$fields = acf_get_fields( $group['key'] );
		return new WP_REST_Response( array( 'success' => true, 'data' => $fields ?: array() ), 200 );
	}

	public static function duplicate_field_group( $request ) {
		$group_id = $request->get_param( 'id' );
		$group = acf_get_field_group( $group_id );
		if ( ! $group ) return new WP_Error( 'wpaic_not_found', 'Field group not found.', array( 'status' => 404 ) );

		$new_group = $group;
		$new_group['title'] = $group['title'] . ' (Copy)';
		$new_group['key'] = 'group_' . uniqid();
		unset( $new_group['ID'] );

		$result = acf_update_field_group( $new_group );
		WPAIC_Audit::log( 'acf_field_group_duplicated', $group_id, 'acf_field_group', get_current_user_id(), array( 'new_id' => $result['ID'] ) );
		return new WP_REST_Response( array( 'success' => true, 'data' => $result ), 201 );
	}

	public static function assign_field_group( $request ) {
		$group_id = $request->get_param( 'group_id' );
		$location = $request->get_param( 'location' );

		$group = acf_get_field_group( $group_id );
		if ( ! $group ) return new WP_Error( 'wpaic_not_found', 'Field group not found.', array( 'status' => 404 ) );

		$group['location'] = $location;
		acf_update_field_group( $group );

		return new WP_REST_Response( array( 'success' => true, 'message' => 'Field group assigned.' ), 200 );
	}

	// ==================== FIELDS (12 tools) ====================

	public static function list_fields( $request ) {
		$args = array();
		if ( $request->has_param( 'group_id' ) ) $args['group_id'] = $request->get_param( 'group_id' );
		if ( $request->has_param( 'type' ) ) $args['type'] = $request->get_param( 'type' );

		$fields = acf_get_fields( $args );
		return new WP_REST_Response( array( 'success' => true, 'data' => $fields ?: array() ), 200 );
	}

	public static function get_field( $request ) {
		$field = acf_get_field( $request->get_param( 'id' ) );
		if ( ! $field ) return new WP_Error( 'wpaic_not_found', 'Field not found.', array( 'status' => 404 ) );

		return new WP_REST_Response( array( 'success' => true, 'data' => $field ), 200 );
	}

	public static function create_field( $request ) {
		$args = array(
			'label' => $request->get_param( 'label' ),
			'name' => $request->get_param( 'name' ),
			'type' => $request->get_param( 'type' ) ?: 'text',
			'parent' => $request->get_param( 'group_id' ),
		);

		if ( $request->has_param( 'default_value' ) ) $args['default_value'] = $request->get_param( 'default_value' );
		if ( $request->has_param( 'required' ) ) $args['required'] = $request->get_param( 'required' );

		$field = acf_update_field( $args );
		if ( ! $field ) return new WP_Error( 'wpaic_create_failed', 'Failed to create field.', array( 'status' => 500 ) );

		WPAIC_Audit::log( 'acf_field_created', $field['ID'], 'acf_field', get_current_user_id(), array( 'label' => $args['label'] ) );
		return new WP_REST_Response( array( 'success' => true, 'data' => $field ), 201 );
	}

	public static function update_field( $request ) {
		$field_id = $request->get_param( 'id' );
		$field = acf_get_field( $field_id );
		if ( ! $field ) return new WP_Error( 'wpaic_not_found', 'Field not found.', array( 'status' => 404 ) );

		if ( $request->has_param( 'label' ) ) $field['label'] = $request->get_param( 'label' );
		if ( $request->has_param( 'default_value' ) ) $field['default_value'] = $request->get_param( 'default_value' );

		$result = acf_update_field( $field );
		WPAIC_Audit::log( 'acf_field_updated', $field_id, 'acf_field', get_current_user_id(), array() );
		return new WP_REST_Response( array( 'success' => true, 'message' => 'Field updated.' ), 200 );
	}

	public static function delete_field( $request ) {
		$field_id = $request->get_param( 'id' );
		$field = acf_get_field( $field_id );
		if ( ! $field ) return new WP_Error( 'wpaic_not_found', 'Field not found.', array( 'status' => 404 ) );

		acf_delete_field( $field['key'] );
		WPAIC_Audit::log( 'acf_field_deleted', $field_id, 'acf_field', get_current_user_id(), array() );
		return new WP_REST_Response( array( 'success' => true, 'message' => 'Field deleted.' ), 200 );
	}

	public static function duplicate_field( $request ) {
		$field_id = $request->get_param( 'field_id' );
		$field = acf_get_field( $field_id );
		if ( ! $field ) return new WP_Error( 'wpaic_not_found', 'Field not found.', array( 'status' => 404 ) );

		$new_field = $field;
		$new_field['label'] = $field['label'] . ' (Copy)';
		$new_field['name'] = $field['name'] . '_copy';
		$new_field['key'] = 'field_' . uniqid();
		unset( $new_field['ID'] );

		$result = acf_update_field( $new_field );
		return new WP_REST_Response( array( 'success' => true, 'data' => $result ), 201 );
	}

	public static function export_fields( $request ) {
		$field_ids = $request->get_param( 'field_ids' );
		$fields = array();
		foreach ( $field_ids as $id ) {
			$field = acf_get_field( $id );
			if ( $field ) $fields[] = $field;
		}
		return new WP_REST_Response( array( 'success' => true, 'data' => $fields ), 200 );
	}

	public static function import_fields( $request ) {
		$fields = $request->get_param( 'fields' );
		$count = 0;
		foreach ( $fields as $field ) {
			acf_update_field( $field );
			$count++;
		}
		return new WP_REST_Response( array( 'success' => true, 'message' => "Imported {$count} fields." ), 200 );
	}

	public static function validate_field( $request ) {
		$field = $request->get_param( 'field' );
		$errors = array();

		if ( empty( $field['label'] ) ) $errors[] = 'Label is required.';
		if ( empty( $field['name'] ) ) $errors[] = 'Name is required.';
		if ( empty( $field['type'] ) ) $errors[] = 'Type is required.';

		return new WP_REST_Response( array(
			'success' => empty( $errors ), 'valid' => empty( $errors ), 'errors' => $errors
		), 200 );
	}

	public static function bulk_update_fields( $request ) {
		$field_ids = $request->get_param( 'field_ids' );
		$updates = $request->get_param( 'updates' );
		$results = array();

		foreach ( $field_ids as $id ) {
			$field = acf_get_field( $id );
			if ( ! $field ) { $results[$id] = 'not found'; continue; }
			foreach ( $updates as $key => $value ) { $field[$key] = $value; }
			acf_update_field( $field );
			$results[$id] = 'updated';
		}

		return new WP_REST_Response( array( 'success' => true, 'data' => $results ), 200 );
	}

	public static function clone_field( $request ) {
		$field_id = $request->get_param( 'field_id' );
		$new_parent = $request->get_param( 'parent' );

		$field = acf_get_field( $field_id );
		if ( ! $field ) return new WP_Error( 'wpaic_not_found', 'Field not found.', array( 'status' => 404 ) );

		$clone = $field;
		$clone['parent'] = $new_parent;
		$clone['key'] = 'field_' . uniqid();
		unset( $clone['ID'] );

		$result = acf_update_field( $clone );
		return new WP_REST_Response( array( 'success' => true, 'data' => $result ), 201 );
	}

	// ==================== POST META WITH ACF (8 tools) ====================

	public static function get_post_acf_fields( $request ) {
		$post_id = $request->get_param( 'post_id' );
		$group_id = $request->get_param( 'group_id' );

		$data = array();
		if ( $group_id ) {
			$fields = acf_get_fields( $group_id );
			foreach ( $fields as $field ) {
				$data[$field['name']] = get_field( $field['name'], $post_id );
			}
		} else {
			$data = get_fields( $post_id );
		}

		return new WP_REST_Response( array( 'success' => true, 'data' => $data ), 200 );
	}

	public static function update_post_acf_fields( $request ) {
		$post_id = $request->get_param( 'post_id' );
		$fields = $request->get_param( 'fields' );

		foreach ( $fields as $key => $value ) {
			update_field( $key, $value, $post_id );
		}

		WPAIC_Audit::log( 'acf_post_fields_updated', $post_id, 'post', get_current_user_id(), array( 'fields' => array_keys( $fields ) ) );
		return new WP_REST_Response( array( 'success' => true, 'message' => 'ACF fields updated.' ), 200 );
	}

	public static function get_post_acf_field( $request ) {
		$post_id = $request->get_param( 'post_id' );
		$field_key = $request->get_param( 'field_key' );

		$value = get_field( $field_key, $post_id );
		return new WP_REST_Response( array( 'success' => true, 'data' => array( 'value' => $value ) ), 200 );
	}

	public static function update_post_acf_field( $request ) {
		$post_id = $request->get_param( 'post_id' );
		$field_key = $request->get_param( 'field_key' );
		$value = $request->get_param( 'value' );

		update_field( $field_key, $value, $post_id );
		return new WP_REST_Response( array( 'success' => true, 'message' => 'Field updated.' ), 200 );
	}

	public static function render_post_acf_fields( $request ) {
		$post_id = $request->get_param( 'post_id' );
		ob_start();
		acf_form( array( 'post_id' => $post_id, 'form' => false, 'echo' => false ) );
		$html = ob_get_clean();

		return new WP_REST_Response( array( 'success' => true, 'data' => array( 'html' => $html ) ), 200 );
	}

	public static function get_post_layouts( $request ) {
		$post_id = $request->get_param( 'post_id' );
		$layouts = array();

		$fields = get_fields( $post_id );
		if ( is_array( $fields ) ) {
			foreach ( $fields as $key => $value ) {
				$field = acf_get_field( $key );
				if ( $field && $field['type'] === 'layout' ) {
					$layouts[$key] = $value;
				}
			}
		}

		return new WP_REST_Response( array( 'success' => true, 'data' => $layouts ), 200 );
	}

	public static function get_flexible_content( $request ) {
		$post_id = $request->get_param( 'post_id' );
		$field_key = $request->get_param( 'field_key' );

		$layouts = get_field( $field_key, $post_id );
		return new WP_REST_Response( array( 'success' => true, 'data' => $layouts ), 200 );
	}

	public static function get_repeater_field( $request ) {
		$post_id = $request->get_param( 'post_id' );
		$field_key = $request->get_param( 'field_key' );

		$rows = get_field( $field_key, $post_id );
		return new WP_REST_Response( array( 'success' => true, 'data' => $rows ?: array() ), 200 );
	}

	// ==================== OPTIONS PAGES (4 tools) ====================

	public static function list_options_pages() {
		if ( ! function_exists( 'acf_options_page' ) ) {
			return new WP_REST_Response( array( 'success' => true, 'data' => array() ), 200 );
		}

		$pages = acf_options_page()->get_pages();
		return new WP_REST_Response( array( 'success' => true, 'data' => $pages ), 200 );
	}

	public static function create_options_page( $request ) {
		$args = array(
			'page_title' => $request->get_param( 'page_title' ),
			'menu_title' => $request->get_param( 'menu_title' ) ?: $request->get_param( 'page_title' ),
			'menu_slug' => $request->get_param( 'menu_slug' ),
		);

		acf_add_options_page( $args );
		return new WP_REST_Response( array( 'success' => true, 'message' => 'Options page created.' ), 201 );
	}

	public static function get_options_page( $request ) {
		$slug = $request->get_param( 'slug' );
		$pages = acf_options_page()->get_pages();

		$page = isset( $pages[$slug] ) ? $pages[$slug] : null;
		if ( ! $page ) return new WP_Error( 'wpaic_not_found', 'Options page not found.', array( 'status' => 404 ) );

		return new WP_REST_Response( array( 'success' => true, 'data' => $page ), 200 );
	}

	public static function get_options_page_fields( $request ) {
		$slug = $request->get_param( 'slug' );
		$fields = get_fields( 'options_' . $slug );
		return new WP_REST_Response( array( 'success' => true, 'data' => $fields ), 200 );
	}

	// ==================== FIELD TYPES & VALIDATION (6 tools) ====================

	public static function list_field_types() {
		$types = acf_get_field_types();
		$data = array();
		foreach ( $types as $type => $label ) {
			$data[] = array( 'type' => $type, 'label' => $label );
		}
		return new WP_REST_Response( array( 'success' => true, 'data' => $data ), 200 );
	}

	public static function get_field_type( $request ) {
		$type = $request->get_param( 'type' );
		$types = acf_get_field_types();

		if ( ! isset( $types[$type] ) ) {
			return new WP_Error( 'wpaic_not_found', 'Field type not found.', array( 'status' => 404 ) );
		}

		return new WP_REST_Response( array(
			'success' => true, 'data' => array( 'type' => $type, 'label' => $types[$type] )
		), 200 );
	}

	public static function validate_acf_rule( $request ) {
		$rule = $request->get_param( 'rule' );
		$valid = true;
		$errors = array();

		if ( empty( $rule['param'] ) ) { $valid = false; $errors[] = 'Param is required.'; }
		if ( empty( $rule['operator'] ) ) { $valid = false; $errors[] = 'Operator is required.'; }

		return new WP_REST_Response( array(
			'success' => $valid, 'valid' => $valid, 'errors' => $errors
		), 200 );
	}

	public static function list_location_rules() {
		$available = array(
			array( 'param' => 'post_type', 'label' => 'Post Type' ),
			array( 'param' => 'post', 'label' => 'Post' ),
			array( 'param' => 'page', 'label' => 'Page' ),
			array( 'param' => 'post_category', 'label' => 'Post Category' ),
			array( 'param' => 'post_format', 'label' => 'Post Format' ),
			array( 'param' => 'user', 'label' => 'User' ),
			array( 'param' => 'taxonomy', 'label' => 'Taxonomy' ),
			array( 'param' => 'options_page', 'label' => 'Options Page' ),
		);

		return new WP_REST_Response( array( 'success' => true, 'data' => $available ), 200 );
	}

	public static function update_location_rules( $request ) {
		$group_id = $request->get_param( 'id' );
		$location = $request->get_param( 'location' );

		$group = acf_get_field_group( $group_id );
		if ( ! $group ) return new WP_Error( 'wpaic_not_found', 'Field group not found.', array( 'status' => 404 ) );

		$group['location'] = $location;
		acf_update_field_group( $group );

		return new WP_REST_Response( array( 'success' => true, 'message' => 'Location rules updated.' ), 200 );
	}

	public static function list_cloneable_fields() {
		$fields = acf_get_fields();
		$cloneable = array_filter( $fields, function( $f ) {
			return $f['type'] === 'clone';
		});

		return new WP_REST_Response( array( 'success' => true, 'data' => array_values( $cloneable ) ), 200 );
	}

	// ==================== BULK OPERATIONS (4 tools) ====================

	public static function bulk_update_acf_meta( $request ) {
		$post_ids = $request->get_param( 'post_ids' );
		$fields = $request->get_param( 'fields' );
		$results = array();

		foreach ( $post_ids as $post_id ) {
			foreach ( $fields as $key => $value ) {
				update_field( $key, $value, $post_id );
			}
			$results[$post_id] = 'updated';
		}

		return new WP_REST_Response( array( 'success' => true, 'data' => $results ), 200 );
	}

	public static function bulk_clone_fields( $request ) {
		$source_post = $request->get_param( 'source_post_id' );
		$target_posts = $request->get_param( 'target_post_ids' );
		$field_keys = $request->get_param( 'field_keys' );

		$source_fields = array();
		foreach ( $field_keys as $key ) {
			$source_fields[$key] = get_field( $key, $source_post );
		}

		$results = array();
		foreach ( $target_posts as $target_id ) {
			foreach ( $source_fields as $key => $value ) {
				update_field( $key, $value, $target_id );
			}
			$results[$target_id] = 'cloned';
		}

		return new WP_REST_Response( array( 'success' => true, 'data' => $results ), 200 );
	}

	public static function export_field_group( $request ) {
		$group_id = $request->get_param( 'id' );
		$group = acf_get_field_group( $group_id );
		if ( ! $group ) return new WP_Error( 'wpaic_not_found', 'Field group not found.', array( 'status' => 404 ) );

		$fields = acf_get_fields( $group['key'] );
		return new WP_REST_Response( array(
			'success' => true, 'data' => array( 'group' => $group, 'fields' => $fields )
		), 200 );
	}

	public static function import_field_group( $request ) {
		$data = $request->get_param( 'data' );
		$count = 0;

		if ( isset( $data['group'] ) ) {
			acf_update_field_group( $data['group'] );
			$count++;
		}
		if ( isset( $data['fields'] ) ) {
			foreach ( $data['fields'] as $field ) {
				acf_update_field( $field );
				$count++;
			}
		}

		return new WP_REST_Response( array( 'success' => true, 'message' => "Imported {$count} items." ), 200 );
	}

	// ==================== FIELD GROUP ANALYSIS (6 tools) ====================

	public static function analyze_field_group_usage( $request ) {
		$group_id = $request->get_param( 'group_id' );
		$group = acf_get_field_group( $group_id );
		if ( ! $group ) return new WP_Error( 'wpaic_not_found', 'Field group not found.', array( 'status' => 404 ) );

		$usage = array(
			'group' => $group['title'],
			'fields_count' => count( acf_get_fields( $group['key'] ) ),
			'locations' => $group['location'],
			'used_on' => array(),
		);

		// Check posts using this field group
		$posts = get_posts( array( 'post_type' => 'any', 'posts_per_page' => -1 ) );
		foreach ( $posts as $post ) {
			$fields = get_fields( $post->ID );
			if ( $fields ) { $usage['used_on'][] = $post->post_title; }
		}

		return new WP_REST_Response( array( 'success' => true, 'data' => $usage ), 200 );
	}

	public static function get_field_dependencies( $request ) {
		$field_id = $request->get_param( 'field_id' );
		$field = acf_get_field( $field_id );
		if ( ! $field ) return new WP_Error( 'wpaic_not_found', 'Field not found.', array( 'status' => 404 ) );

		return new WP_REST_Response( array(
			'success' => true, 'data' => array(
				'field' => $field['label'],
				'conditional_logic' => isset( $field['conditional_logic'] ) ? $field['conditional_logic'] : array(),
			)
		), 200 );
	}

	public static function find_orphaned_fields() {
		$fields = acf_get_fields();
		$orphaned = array();

		foreach ( $fields as $field ) {
			if ( empty( $field['parent'] ) || $field['parent'] == 0 ) {
				$orphaned[] = array( 'id' => $field['ID'], 'label' => $field['label'] );
			}
		}

		return new WP_REST_Response( array( 'success' => true, 'data' => $orphaned ), 200 );
	}

	public static function check_duplicate_fields() {
		$fields = acf_get_fields();
		$names = array();
		$duplicates = array();

		foreach ( $fields as $field ) {
			$name = $field['name'];
			if ( isset( $names[$name] ) ) {
				$duplicates[] = array( 'name' => $name, 'fields' => array( $names[$name], $field['label'] ) );
			} else {
				$names[$name] = $field['label'];
			}
		}

		return new WP_REST_Response( array( 'success' => true, 'data' => $duplicates ), 200 );
	}

	public static function get_conditional_logic( $request ) {
		$group_id = $request->get_param( 'group_id' );
		$fields = acf_get_fields( $group_id );

		$logic_fields = array();
		foreach ( $fields as $field ) {
			if ( ! empty( $field['conditional_logic'] ) ) {
				$logic_fields[] = array( 'field' => $field['label'], 'logic' => $field['conditional_logic'] );
			}
		}

		return new WP_REST_Response( array( 'success' => true, 'data' => $logic_fields ), 200 );
	}

	public static function analyze_acf_performance( $request ) {
		$groups = acf_get_field_groups();
		$analysis = array(
			'total_groups' => count( $groups ),
			'total_fields' => count( acf_get_fields() ),
			'fields_per_group' => array(),
			'heavy_groups' => array(),
		);

		foreach ( $groups as $group ) {
			$fields = acf_get_fields( $group['key'] );
			$count = count( $fields );
			$analysis['fields_per_group'][$group['title']] = $count;

			if ( $count > 20 ) {
				$analysis['heavy_groups'][] = array( 'group' => $group['title'], 'field_count' => $count );
			}
		}

		return new WP_REST_Response( array( 'success' => true, 'data' => $analysis ), 200 );
	}
}
