<?php
/**
 * Uninstall WP AI Control.
 *
 * @package WP_AI_Control
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

global $wpdb;

// Delete options.
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'wpaic_%'" );

// Drop custom tables.
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wpaic_api_keys" );
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wpaic_audit_log" );

// Clear scheduled hooks.
wp_clear_scheduled_hook( 'wpaic_purge_audit_log' );
