<?php
class WPAIC_Activator {
    public static function activate() {
        $key = get_option( 'wpaic_api_key', '' );
        if ( empty( $key ) ) {
            if ( function_exists( 'random_bytes' ) ) {
                $key = 'wpaic_' . bin2hex( random_bytes( 16 ) );
            } else {
                // Fallback for PHP < 7.0 (should not happen on WP 5+)
                $key = 'wpaic_' . md5( uniqid( mt_rand(), true ) . time() );
            }
            update_option( 'wpaic_api_key', $key );
        }
        if ( defined( 'WPAIC_VERSION' ) ) {
            update_option( 'wpaic_version', WPAIC_VERSION );
        }
    }
}
