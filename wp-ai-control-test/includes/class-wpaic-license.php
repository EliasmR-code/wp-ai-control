<?php
/**
 * License management via WooCommerce + Software License Manager (SLM).
 *
 * HOW TO CONFIGURE (seller side):
 *  1. Install WooCommerce + "Software License Manager" plugin on YOUR store.
 *  2. In SLM → Settings, copy the "Secret Key For License API".
 *  3. Replace WPAIC_SLM_URL with YOUR store URL.
 *  4. Replace WPAIC_SLM_SECRET with YOUR secret key.
 *  5. In WooCommerce, create a product and in the SLM tab set
 *     "Licensed Product" = Yes, "Item Reference" = "WP AI Control".
 *
 * SLM API actions used:
 *   slm_activate   — registers a domain against a key
 *   slm_deactivate — releases the domain slot
 *   slm_check      — returns current key status
 */

// ── Seller configuration ──────────────────────────────────────────────────────
// Replace these two constants with your own store values before distributing.
if ( ! defined( 'WPAIC_SLM_URL' ) ) {
    define( 'WPAIC_SLM_URL', '' );                             // <-- your store URL
}
if ( ! defined( 'WPAIC_SLM_SECRET' ) ) {
    define( 'WPAIC_SLM_SECRET', '' );                          // <-- SLM secret key
}
if ( ! defined( 'WPAIC_SLM_ITEM' ) ) {
    define( 'WPAIC_SLM_ITEM', 'WP AI Control' );              // <-- item_reference in SLM
}
/*
 * Desarrollo / propietario del sitio (sin SLM):
 *  A) define( 'WPAIC_LICENSE_BYPASS', true );  → licencia válida, plan según WPAIC_PLAN_VARIANT o "studio".
 *  B) define( 'WPAIC_ALLOW_DEV_LICENSE', true );
 *      define( 'WPAIC_DEV_LICENSE_KEY', 'tu-clave-secreta' );
 *      Activa en el admin la MISMA cadena en el campo de licencia. No funciona si no defines ambas constantes.
 */
// ─────────────────────────────────────────────────────────────────────────────

class WPAIC_License {

    const OPTION_KEY    = 'wpaic_license_key';
    const OPTION_STATUS = 'wpaic_license_status';
    const OPTION_DOMAIN = 'wpaic_license_domain';
    const OPTION_EXPIRY = 'wpaic_license_expiry';
    const OPTION_PLAN   = 'wpaic_plan_variant';
    const TRANSIENT     = 'wpaic_license_valid';
    const CACHE_TTL     = 43200; // 12 hours

    // ── Public API ────────────────────────────────────────────────────────────

    /**
     * Whether SLM has been configured with real values (not placeholder).
     */
    public static function is_configured() {
        return ! empty( WPAIC_SLM_URL )
            && ! empty( WPAIC_SLM_SECRET )
            && false === strpos( WPAIC_SLM_URL, 'TU-' )
            && false === strpos( WPAIC_SLM_SECRET, 'TU_' );
    }

    /**
     * Returns true when the stored license is active.
     * Uses a transient cache to avoid hitting the remote API on every request.
     */
    public static function is_valid() {
        if ( defined( 'WPAIC_LICENSE_BYPASS' ) && WPAIC_LICENSE_BYPASS ) {
            return true;
        }

        // During AJAX / REST requests use the cached result when available.
        $cached = get_transient( self::TRANSIENT );
        if ( false !== $cached ) {
            return 'valid' === $cached;
        }

        $key = get_option( self::OPTION_KEY, '' );
        if ( empty( $key ) ) {
            return false;
        }

        if ( self::is_unlock_dev_license( $key ) ) {
            set_transient( self::TRANSIENT, 'valid', self::CACHE_TTL );
            update_option( self::OPTION_STATUS, 'active' );
            update_option( self::OPTION_PLAN, 'studio' );
            return true;
        }

        $data = self::remote_check( $key );
        $active = isset( $data['status'] ) && in_array( $data['status'], array( 'active', 'pending' ), true );

        set_transient( self::TRANSIENT, $active ? 'valid' : 'invalid', self::CACHE_TTL );

        update_option( self::OPTION_STATUS, $data['status'] ?? 'unknown' );
        if ( ! empty( $data['date_expiry'] ) ) {
            update_option( self::OPTION_EXPIRY, sanitize_text_field( $data['date_expiry'] ) );
        }
        $plan = self::normalize_plan( $data['plan'] ?? ( $data['tier'] ?? ( $data['package'] ?? '' ) ) );
        if ( $plan ) {
            update_option( self::OPTION_PLAN, $plan );
        }

        return $active;
    }

    /**
     * Activates a license key for this domain.
     *
     * @param  string $key License key entered by the user.
     * @return array  { success: bool, message: string }
     */
    public static function activate( $key ) {
        $key    = sanitize_text_field( trim( $key ) );
        $domain = self::get_domain();

        if ( empty( $key ) ) {
            return array( 'success' => false, 'message' => __( 'Please enter a license key.', 'wp-ai-control' ) );
        }

        if ( self::is_unlock_dev_license( $key ) ) {
            update_option( self::OPTION_KEY, $key );
            update_option( self::OPTION_STATUS, 'active' );
            update_option( self::OPTION_DOMAIN, $domain );
            update_option( self::OPTION_PLAN, 'studio' );
            delete_transient( self::TRANSIENT );
            return array( 'success' => true, 'message' => __( 'License activated successfully!', 'wp-ai-control' ) );
        }

        if ( ! self::is_configured() ) {
            return array(
                'success' => false,
                'message' => __( 'The license server is not configured (WPAIC_SLM_URL / WPAIC_SLM_SECRET). Add them in wp-config.php or use WPAIC_LICENSE_BYPASS for local testing.', 'wp-ai-control' ),
            );
        }

        $response = wp_remote_get(
            add_query_arg( array(
                'slm_action'        => 'slm_activate',
                'secret_key'        => WPAIC_SLM_SECRET,
                'license_key'       => $key,
                'registered_domain' => $domain,
                'item_reference'    => rawurlencode( WPAIC_SLM_ITEM ),
            ), WPAIC_SLM_URL ),
            array( 'timeout' => 15, 'sslverify' => true )
        );

        if ( is_wp_error( $response ) ) {
            return array( 'success' => false, 'message' => $response->get_error_message() );
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( isset( $body['result'] ) && 'success' === $body['result'] ) {
            update_option( self::OPTION_KEY, $key );
            update_option( self::OPTION_STATUS, 'active' );
            update_option( self::OPTION_DOMAIN, $domain );
            $plan = self::normalize_plan( $body['plan'] ?? ( $body['tier'] ?? ( $body['package'] ?? '' ) ) );
            if ( $plan ) {
                update_option( self::OPTION_PLAN, $plan );
            }
            delete_transient( self::TRANSIENT );
            return array( 'success' => true, 'message' => __( 'License activated successfully!', 'wp-ai-control' ) );
        }

        // SLM returns result=error with a descriptive message
        $msg = isset( $body['message'] ) ? $body['message'] : __( 'Activation failed. Check your license key.', 'wp-ai-control' );
        return array( 'success' => false, 'message' => $msg );
    }

    /**
     * Deactivates the stored license for this domain, freeing up one activation slot.
     *
     * @return array { success: bool, message: string }
     */
    public static function deactivate() {
        $key    = get_option( self::OPTION_KEY, '' );
        $domain = get_option( self::OPTION_DOMAIN, self::get_domain() );

        if ( empty( $key ) ) {
            return array( 'success' => false, 'message' => __( 'No license key found.', 'wp-ai-control' ) );
        }

        if ( ! self::is_unlock_dev_license( $key ) ) {
            wp_remote_get(
                add_query_arg( array(
                    'slm_action'        => 'slm_deactivate',
                    'secret_key'        => WPAIC_SLM_SECRET,
                    'license_key'       => $key,
                    'registered_domain' => $domain,
                    'item_reference'    => rawurlencode( WPAIC_SLM_ITEM ),
                ), WPAIC_SLM_URL ),
                array( 'timeout' => 15, 'sslverify' => true )
            );
        }

        // Clear locally regardless of remote response
        delete_option( self::OPTION_KEY );
        delete_option( self::OPTION_STATUS );
        delete_option( self::OPTION_DOMAIN );
        delete_option( self::OPTION_EXPIRY );
        delete_option( self::OPTION_PLAN );
        delete_transient( self::TRANSIENT );

        return array( 'success' => true, 'message' => __( 'License deactivated.', 'wp-ai-control' ) );
    }

    /** Returns the raw stored license key (masked for display). */
    public static function get_key() {
        return get_option( self::OPTION_KEY, '' );
    }

    /** Returns stored status string: active | expired | blocked | pending | inactive | unknown */
    public static function get_status() {
        return get_option( self::OPTION_STATUS, 'inactive' );
    }

    /** Returns expiry date string or empty string. */
    public static function get_expiry() {
        return get_option( self::OPTION_EXPIRY, '' );
    }

    public static function get_plan() {
        if ( defined( 'WPAIC_PLAN_VARIANT' ) ) {
            return self::normalize_plan( WPAIC_PLAN_VARIANT, 'studio' );
        }

        $stored = get_option( self::OPTION_PLAN, '' );
        if ( ! empty( $stored ) ) {
            return self::normalize_plan( $stored, 'studio' );
        }

        $key = self::get_key();
        if ( ! empty( $key ) ) {
            if ( false !== stripos( $key, 'maker' ) ) {
                return 'maker';
            }
            if ( false !== stripos( $key, 'builder' ) ) {
                return 'builder';
            }
            if ( false !== stripos( $key, 'studio' ) ) {
                return 'studio';
            }
        }

        return 'studio';
    }

    public static function set_plan( $plan ) {
        update_option( self::OPTION_PLAN, self::normalize_plan( $plan, 'studio' ) );
    }

    /** Returns a masked version of the key for display: wpaic_abc...xyz */
    public static function get_key_masked() {
        $key = self::get_key();
        if ( strlen( $key ) < 8 ) return $key;
        return substr( $key, 0, 10 ) . '...' . substr( $key, -4 );
    }

    /**
     * Force a fresh remote check, busting the transient cache.
     * Useful after the user clicks "Refresh Status".
     */
    public static function refresh() {
        delete_transient( self::TRANSIENT );
        return self::is_valid();
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    private static function remote_check( $key ) {
        if ( self::is_unlock_dev_license( $key ) ) {
            return array(
                'status'      => 'active',
                'plan'        => 'studio',
                'date_expiry' => '',
            );
        }

        if ( ! self::is_configured() ) {
            return array( 'status' => 'inactive' );
        }

        $response = wp_remote_get(
            add_query_arg( array(
                'slm_action' => 'slm_check',
                'secret_key' => WPAIC_SLM_SECRET,
                'license_key' => sanitize_text_field( $key ),
            ), WPAIC_SLM_URL ),
            array( 'timeout' => 10, 'sslverify' => true )
        );

        if ( is_wp_error( $response ) ) {
            return array( 'status' => 'error' );
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );
        return is_array( $body ) ? $body : array( 'status' => 'error' );
    }

    private static function get_domain() {
        $host = wp_parse_url( get_site_url(), PHP_URL_HOST );
        // Strip www. to prevent activation mismatches
        return preg_replace( '/^www\./i', '', $host );
    }

    /**
     * Clave local solo si defines WPAIC_ALLOW_DEV_LICENSE y WPAIC_DEV_LICENSE_KEY en wp-config.php
     * (no hay valor por defecto en el código: tú eliges la cadena).
     */
    private static function is_unlock_dev_license( $key ) {
        if ( ! defined( 'WPAIC_ALLOW_DEV_LICENSE' ) || ! WPAIC_ALLOW_DEV_LICENSE ) {
            return false;
        }
        if ( ! defined( 'WPAIC_DEV_LICENSE_KEY' ) || '' === (string) WPAIC_DEV_LICENSE_KEY ) {
            return false;
        }
        return hash_equals( (string) WPAIC_DEV_LICENSE_KEY, (string) $key );
    }

    private static function normalize_plan( $plan, $default = '' ) {
        $plan = sanitize_key( (string) $plan );
        $map = array(
            'maker'   => 'maker',
            'basic'   => 'maker',
            'starter' => 'maker',
            'builder' => 'builder',
            'pro'     => 'builder',
            'studio'  => 'studio',
            'agency'  => 'studio',
            'elite'   => 'studio',
        );

        if ( isset( $map[ $plan ] ) ) {
            return $map[ $plan ];
        }

        return sanitize_key( (string) $default );
    }
}
