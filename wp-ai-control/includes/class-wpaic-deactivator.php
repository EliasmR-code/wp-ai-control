<?php
/**
 * Plugin deactivation.
 *
 * @package WP_AI_Control
 */

class WPAIC_Deactivator {

	public static function deactivate() {
		wp_clear_scheduled_hook( 'wpaic_purge_audit_log' );
	}
}
