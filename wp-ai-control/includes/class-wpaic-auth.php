<?php
/**
 * Authentication and API key management for WP AI Control.
 *
 * @package WP_AI_Control
 * @subpackage WP_AI_Control/includes
 */

class WPAIC_Auth {

	public static function generate_api_key( $user_id, $name = null, $permissions = null ) {
		global $wpdb;

		$user = get_user_by( 'id', $user_id );
		if ( ! $user || ! user_can( $user, 'manage_options' ) ) {
			return new WP_Error( 'wpaic_insufficient_permissions', __( 'User does not have permission to generate API keys.', 'wp-ai-control' ) );
		}

		$table_name = $wpdb->prefix . 'wpaic_api_keys';

		$existing_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table_name} WHERE user_id = %d AND is_active = 1", $user_id ) );

		if ( $existing_count >= 10 ) {
			return new WP_Error( 'wpaic_max_keys_reached', __( 'Maximum API key limit reached (10 keys).', 'wp-ai-control' ) );
		}

		$raw_key = WPAIC_API_KEY_PREFIX . bin2hex( random_bytes( 16 ) );

		$key_prefix = substr( $raw_key, 0, 20 );

		$hashed_key = wp_hash_password( $raw_key );

		if ( null === $permissions ) {
			$permissions = array( 'read', 'write' );
		}

		$result = $wpdb->insert(
			$table_name,
			array(
				'api_key'     => $hashed_key,
				'key_prefix'  => $key_prefix,
				'user_id'     => $user_id,
				'name'        => $name ? $name : __( 'Default Key', 'wp-ai-control' ),
				'permissions' => wp_json_encode( $permissions ),
				'created_at'  => current_time( 'mysql' ),
				'is_active'   => 1,
			),
			array( '%s', '%s', '%d', '%s', '%s', '%s', '%d' )
		);

		if ( false === $result ) {
			return new WP_Error( 'wpaic_key_generation_failed', __( 'Failed to generate API key.', 'wp-ai-control' ) );
		}

		return $raw_key;
	}

	public static function validate_api_key( $api_key ) {
		global $wpdb;

		if ( empty( $api_key ) || strpos( $api_key, WPAIC_API_KEY_PREFIX ) !== 0 ) {
			return false;
		}

		$table_name = $wpdb->prefix . 'wpaic_api_keys';

		$keys = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE is_active = %d", 1 ) );

		if ( ! $keys ) {
			return false;
		}

		foreach ( $keys as $key_record ) {
			if ( wp_check_password( $api_key, $key_record->api_key ) ) {
				$wpdb->update(
					$table_name,
					array( 'last_used' => current_time( 'mysql' ) ),
					array( 'id' => $key_record->id ),
					array( '%s' ),
					array( '%d' )
				);

				return $key_record;
			}
		}

		return false;
	}

	public static function revoke_api_key( $key_id, $user_id ) {
		global $wpdb;

		$user = get_user_by( 'id', $user_id );
		if ( ! $user || ! user_can( $user, 'manage_options' ) ) {
			return new WP_Error( 'wpaic_insufficient_permissions', __( 'Insufficient permissions.', 'wp-ai-control' ) );
		}

		$table_name = $wpdb->prefix . 'wpaic_api_keys';

		$key = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $key_id ) );

		if ( ! $key ) {
			return new WP_Error( 'wpaic_key_not_found', __( 'API key not found.', 'wp-ai-control' ) );
		}

		if ( $key->user_id !== $user_id && ! current_user_can( 'administrator' ) ) {
			return new WP_Error( 'wpaic_insufficient_permissions', __( 'You do not have permission to revoke this key.', 'wp-ai-control' ) );
		}

		$result = $wpdb->delete( $table_name, array( 'id' => $key_id ), array( '%d' ) );

		return false !== $result;
	}

	public static function list_api_keys( $user_id ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'wpaic_api_keys';

		return $wpdb->get_results( $wpdb->prepare( "SELECT id, user_id, name, key_prefix, permissions, last_used, created_at, is_active FROM {$table_name} WHERE user_id = %d ORDER BY created_at DESC", $user_id ) ) ?: array();
	}

	public static function check_rate_limit( $api_key_hash ) {
		$transient_key = 'wpaic_rl_' . md5( $api_key_hash );
		$data = get_transient( $transient_key );

		if ( false === $data ) {
			set_transient( $transient_key, array( 'count' => 1, 'reset' => time() + WPAIC_RATE_LIMIT_WINDOW ), WPAIC_RATE_LIMIT_WINDOW );
			return true;
		}

		if ( $data['count'] >= WPAIC_RATE_LIMIT_REQUESTS ) {
			return false;
		}

		$data['count']++;
		set_transient( $transient_key, $data, WPAIC_RATE_LIMIT_WINDOW );
		return true;
	}

	public static function check_permission( $key_record, $required ) {
		$permissions = json_decode( $key_record->permissions, true );

		if ( ! is_array( $permissions ) ) {
			return false;
		}

		if ( in_array( 'admin', $permissions, true ) ) {
			return true;
		}

		if ( 'write' === $required && in_array( 'write', $permissions, true ) ) {
			return true;
		}

		if ( 'read' === $required && ( in_array( 'read', $permissions, true ) || in_array( 'write', $permissions, true ) ) ) {
			return true;
		}

		return false;
	}
}
