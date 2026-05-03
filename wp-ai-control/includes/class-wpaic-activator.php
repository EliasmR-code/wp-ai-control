<?php
/**
 * Plugin activation.
 *
 * @package WP_AI_Control
 */

class WPAIC_Activator {

	public static function activate() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$api_keys_table = $wpdb->prefix . 'wpaic_api_keys';
		$audit_log_table = $wpdb->prefix . 'wpaic_audit_log';

		$sql = "CREATE TABLE IF NOT EXISTS {$api_keys_table} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			api_key varchar(255) NOT NULL,
			key_prefix varchar(50) NOT NULL,
			user_id bigint(20) NOT NULL,
			name varchar(255) DEFAULT '',
			permissions varchar(500) DEFAULT '[\"read\",\"write\"]',
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			last_used datetime NULL,
			is_active tinyint(1) DEFAULT 1,
			PRIMARY KEY (id),
			KEY user_id (user_id),
			KEY is_active (is_active)
		) {$charset_collate};";

		$sql .= "CREATE TABLE IF NOT EXISTS {$audit_log_table} (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			action varchar(100) NOT NULL,
			object_id bigint(20) DEFAULT NULL,
			object_type varchar(50) DEFAULT NULL,
			user_id bigint(20) DEFAULT NULL,
			api_key_id bigint(20) DEFAULT NULL,
			ip_address varchar(45) DEFAULT NULL,
			details longtext,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id),
			KEY action (action),
			KEY created_at (created_at)
		) {$charset_collate};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );

		add_option( 'wpaic_audit_retention_days', WPAIC_AUDIT_RETENTION_DEFAULT );
		add_option( 'wpaic_usage_data', json_encode( array( 'count' => 0, 'reset_date' => date( 'Y-m-01' ) ) ) );
	}
}
