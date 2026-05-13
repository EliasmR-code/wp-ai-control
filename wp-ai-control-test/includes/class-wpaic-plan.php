<?php
class WPAIC_Plan {

    const OPTION_BUILDER_WOO = 'wpaic_builder_woocommerce_enabled';

    private static $plans = array(
        'maker' => array(
            'label'          => 'Maker',
            'sites_limit'    => 1,
            'workflows_limit'=> 20,
            'tools_count'    => 130,
            'features'       => array( 'content', 'plugins', 'widgets', 'analysis', 'builders', 'rank_math' ),
            'builders'       => array( 'gutenberg', 'elementor', 'divi', 'wpbakery', 'bricks', 'oxygen', 'beaver', 'brizy' ),
            'addons'         => array(),
        ),
        'builder' => array(
            'label'          => 'Builder',
            'sites_limit'    => 5,
            'workflows_limit'=> 26,
            'tools_count'    => 155,
            'features'       => array( 'content', 'plugins', 'widgets', 'analysis', 'builders', 'users', 'settings', 'themes', 'acf', 'rank_math' ),
            'builders'       => array( 'gutenberg', 'elementor', 'divi', 'wpbakery', 'bricks', 'oxygen', 'beaver', 'brizy', 'thrive', 'breakdance', 'flatsome', 'kadence' ),
            'addons'         => array( 'woocommerce_optional' ),
        ),
        'studio' => array(
            'label'          => 'Studio',
            'sites_limit'    => 25,
            'workflows_limit'=> 40,
            'tools_count'    => 176,
            'features'       => array( 'content', 'plugins', 'widgets', 'analysis', 'builders', 'users', 'settings', 'themes', 'acf', 'woocommerce', 'rank_math' ),
            'builders'       => array( 'gutenberg', 'elementor', 'divi', 'wpbakery', 'bricks', 'oxygen', 'beaver', 'brizy', 'thrive', 'breakdance', 'flatsome', 'kadence' ),
            'addons'         => array( 'woocommerce_included' ),
        ),
    );

    public static function all() {
        return self::$plans;
    }

    public static function normalize( $plan ) {
        $plan = sanitize_key( (string) $plan );
        if ( isset( self::$plans[ $plan ] ) ) {
            return $plan;
        }
        return 'studio';
    }

    public static function get_current() {
        if ( class_exists( 'WPAIC_License' ) ) {
            return self::normalize( WPAIC_License::get_plan() );
        }
        if ( defined( 'WPAIC_PLAN_VARIANT' ) ) {
            return self::normalize( WPAIC_PLAN_VARIANT );
        }
        return 'studio';
    }

    public static function get_config( $plan = null ) {
        $key = self::normalize( null === $plan ? self::get_current() : $plan );
        $cfg = self::$plans[ $key ];

        if ( 'builder' === $key && self::is_builder_woocommerce_enabled() ) {
            $cfg['features'][] = 'woocommerce';
            // Builder + WooCommerce addon effectively unlocks the same tool count as Studio.
            $cfg['tools_count'] = 176;
            $cfg['addons'][] = 'woocommerce_enabled';
        }

        $cfg['builders_count'] = count( $cfg['builders'] );

        return $cfg;
    }

    public static function has_feature( $feature, $plan = null ) {
        $feature = sanitize_key( (string) $feature );
        $cfg = self::get_config( $plan );
        return in_array( $feature, $cfg['features'], true );
    }

    public static function is_builder_woocommerce_enabled() {
        return (bool) get_option( self::OPTION_BUILDER_WOO, false );
    }

    public static function set_builder_woocommerce_enabled( $enabled ) {
        update_option( self::OPTION_BUILDER_WOO, (bool) $enabled );
    }

    public static function allowed_builders( $plan = null ) {
        $cfg = self::get_config( $plan );
        return $cfg['builders'];
    }

    public static function denied_response( $feature ) {
        $plan   = self::get_current();
        $config = self::get_config( $plan );
        return new WP_REST_Response( array(
            'error'   => 'feature_not_available',
            'feature' => sanitize_key( (string) $feature ),
            'plan'    => $plan,
            'message' => sprintf( 'Feature not available in %s plan.', $config['label'] ),
        ), 403 );
    }
}
