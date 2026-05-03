<?php
/**
 * Plugin activation.
 */

class WPAIC_Activator {

	public static function activate() {
		update_option( 'wpaic_activated', true );
	}
}