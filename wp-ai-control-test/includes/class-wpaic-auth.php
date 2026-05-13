<?php
class WPAIC_Auth {

    private static function get_header( $name ) {
        if ( function_exists( 'getallheaders' ) ) {
            $headers = getallheaders();
        } else {
            $headers = array();
            foreach ( $_SERVER as $k => $v ) {
                if ( substr( $k, 0, 5 ) === 'HTTP_' ) {
                    $headers[ str_replace( ' ', '-', ucwords( strtolower( str_replace( '_', ' ', substr( $k, 5 ) ) ) ) ) ] = $v;
                }
            }
        }
        return isset( $headers[ $name ] ) ? $headers[ $name ] : '';
    }

    public static function check( $request ) {
        $auth = self::get_header( 'Authorization' );
        if ( empty( $auth ) ) {
            return current_user_can( 'edit_posts' );
        }
        $key        = str_replace( 'Bearer ', '', $auth );
        $stored_key = get_option( 'wpaic_api_key', '' );
        if ( empty( $stored_key ) ) {
            return false;
        }
        return hash_equals( $stored_key, $key );
    }

    public static function check_admin() {
        return current_user_can( 'manage_options' );
    }

    public static function get_key() {
        $key = get_option( 'wpaic_api_key', '' );
        if ( empty( $key ) ) {
            if ( function_exists( 'random_bytes' ) ) {
                $key = 'wpaic_' . bin2hex( random_bytes( 16 ) );
            } else {
                $key = 'wpaic_' . md5( uniqid( mt_rand(), true ) . time() );
            }
            update_option( 'wpaic_api_key', $key );
        }
        return $key;
    }
}
