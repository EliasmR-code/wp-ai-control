<?php
/**
 * Audit logging for WP AI Control.
 *
 * @package WP_AI_Control
 * @subpackage WP_AI_Control/includes
 */

class WPAIC_Audit {

	public static function log( $action, $object_id = null, $object_type = null, $user_id = null, $details = array() ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'wpaic_audit_log';

		$api_key_id = null;
		$api_key_header = isset( $_SERVER['HTTP_X_WPAIC_API_KEY'] ) ? $_SERVER['HTTP_X_WPAIC_API_KEY'] : '';

		if ( $api_key_header ) {
			$key_record = WPAIC_Auth::validate_api_key( $api_key_header );
			if ( $key_record ) {
				$api_key_id = $key_record->id;
			}
		}

		$wpdb->insert(
			$table_name,
			array(
				'action'      => $action,
				'object_id'   => $object_id,
				'object_type' => $object_type,
				'user_id'     => $user_id ?: get_current_user_id(),
				'api_key_id'  => $api_key_id,
				'ip_address'  => self::get_client_ip(),
				'details'     => wp_json_encode( $details ),
				'created_at'  => current_time( 'mysql' ),
			),
			array( '%s', '%d', '%s', '%d', '%d', '%s', '%s', '%s' )
		);
	}

	public static function purge_old_entries() {
		global $wpdb;

		$retention_days = get_option( 'wpaic_audit_retention_days', WPAIC_AUDIT_RETENTION_DEFAULT );
		$table_name = $wpdb->prefix . 'wpaic_audit_log';

		$wpdb->query( $wpdb->prepare(
			"DELETE FROM {$table_name} WHERE created_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
			$retention_days
		) );
	}

	public static function get_logs( $per_page = 20, $page = 1, $filters = array() ) {
		global $wpdb;

		$table_name = $wpdb->prefix . 'wpaic_audit_log';
		$offset = ( $page - 1 ) * $per_page;

		$where = '1=1';
		$prepare_args = array();

		if ( ! empty( $filters['action'] ) ) {
			$where .= ' AND action = %s';
			$prepare_args[] = $filters['action'];
		}

		if ( ! empty( $filters['object_type'] ) ) {
			$where .= ' AND object_type = %s';
			$prepare_args[] = $filters['object_type'];
		}

		$total = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table_name} WHERE {$where}", $prepare_args ) );

		$prepare_args[] = $per_page;
		$prepare_args[] = $offset;

		$results = $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM {$table_name} WHERE {$where} ORDER BY created_at DESC LIMIT %d OFFSET %d",
			$prepare_args
		) );

		return array(
			'total'       => (int) $total,
			'per_page'    => $per_page,
			'current_page' => $page,
			'total_pages' => ceil( $total / $per_page ),
			'logs'        => $results,
		);
	}

	private static function get_client_ip() {
		$ip_headers = array( 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR' );

		foreach ( $ip_headers as $header ) {
			if ( ! empty( $_SERVER[ $header ] ) ) {
				$ips = explode( ',', $_SERVER[ $header ] );
				return trim( $ips[0] );
			}
		}

		return '0.0.0.0';
	}
}
