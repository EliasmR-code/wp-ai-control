<?php
/*
Plugin Name: WP AI Control
Plugin URI: https://github.com/EliasmR-code/wp-ai-control
Description: Control WordPress via AI agents using REST API and MCP protocol. Supports 12 builders, WooCommerce, and ACF.
Version: 1.0.0
Author: EliasmR
Text Domain: wp-ai-control
Requires PHP: 7.4
Requires at least: 5.0
*/

if ( ! defined( 'WPINC' ) ) { die; }

// PHP version guard — must come before any require_once that uses PHP 7+ syntax
if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
    add_action( 'admin_notices', function() {
        echo '<div class="notice notice-error"><p><strong>WP AI Control</strong> requires PHP 7.4 or higher (Hostinger: hPanel → Avanzado → PHP Configuration). Your server is running PHP ' . esc_html( PHP_VERSION ) . '.</p></div>';
    } );
    return;
}

define( 'WPAIC_VERSION', '1.0.0' );
define( 'WPAIC_DIR', plugin_dir_path( __FILE__ ) );
define( 'WPAIC_URL', plugin_dir_url( __FILE__ ) );
define( 'WPAIC_NAMESPACE', 'wp-ai-control/v1' );

// Evita error fatal si no se subió la carpeta includes/ completa (solo wp-ai-control.php, zip mal hecho, etc.).
$wpaic_required_files = array(
    'includes/class-wpaic-auth.php',
    'includes/class-wpaic-license.php',
    'includes/class-wpaic-plan.php',
    'includes/class-wpaic-api.php',
    'includes/class-wpaic-context.php',
    'includes/class-wpaic-webmcp.php',
    'includes/class-wpaic-widgets.php',
    'includes/class-wpaic-analysis.php',
    'includes/class-wpaic-builder.php',
    'includes/admin/class-wpaic-admin.php',
);
$wpaic_missing = array();
foreach ( $wpaic_required_files as $wpaic_rel ) {
    if ( ! is_readable( WPAIC_DIR . $wpaic_rel ) ) {
        $wpaic_missing[] = $wpaic_rel;
    }
}
if ( ! empty( $wpaic_missing ) ) {
    add_action(
        'admin_notices',
        static function() use ( $wpaic_missing ) {
            if ( ! current_user_can( 'manage_options' ) ) {
                return;
            }
            echo '<div class="notice notice-error"><p><strong>WP AI Control:</strong> ';
            esc_html_e( 'Instalación incompleta: faltan archivos. Sube de nuevo la carpeta entera del plugin; en el servidor debe existir la carpeta', 'wp-ai-control' );
            echo ' <code>includes/</code> ';
            esc_html_e( 'con todos los .php (en Linux el nombre importa: minúsculas', 'wp-ai-control' );
            echo ' <code>includes</code> ';
            esc_html_e( ', no', 'wp-ai-control' );
            echo ' <code>Includes</code>). ';
            esc_html_e( 'Archivos no encontrados:', 'wp-ai-control' );
            echo '</p><ul style="list-style:disc;margin-left:1.5em;">';
            foreach ( $wpaic_missing as $wpaic_f ) {
                echo '<li><code>' . esc_html( $wpaic_f ) . '</code></li>';
            }
            echo '</ul></div>';
        }
    );
    return;
}

// Load all includes directly (no autoloader - maximum compatibility)
require_once WPAIC_DIR . 'includes/class-wpaic-auth.php';
require_once WPAIC_DIR . 'includes/class-wpaic-license.php';
require_once WPAIC_DIR . 'includes/class-wpaic-plan.php';
require_once WPAIC_DIR . 'includes/class-wpaic-api.php';
require_once WPAIC_DIR . 'includes/class-wpaic-context.php';
require_once WPAIC_DIR . 'includes/class-wpaic-webmcp.php';
require_once WPAIC_DIR . 'includes/class-wpaic-widgets.php';
require_once WPAIC_DIR . 'includes/class-wpaic-analysis.php';
require_once WPAIC_DIR . 'includes/class-wpaic-builder.php';
require_once WPAIC_DIR . 'includes/admin/class-wpaic-admin.php';

if ( file_exists( WPAIC_DIR . 'includes/class-wpaic-woocommerce.php' ) ) {
    require_once WPAIC_DIR . 'includes/class-wpaic-woocommerce.php';
}
if ( file_exists( WPAIC_DIR . 'includes/class-wpaic-acf.php' ) ) {
    require_once WPAIC_DIR . 'includes/class-wpaic-acf.php';
}
if ( file_exists( WPAIC_DIR . 'includes/class-wpaic-rank-math.php' ) ) {
    require_once WPAIC_DIR . 'includes/class-wpaic-rank-math.php';
}

register_activation_hook( __FILE__, function() {
    try {
        require_once WPAIC_DIR . 'includes/class-wpaic-activator.php';
        WPAIC_Activator::activate();
    } catch ( Throwable $e ) {
        if ( ! function_exists( 'deactivate_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        if ( function_exists( 'deactivate_plugins' ) ) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
        }
        if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
            error_log( 'WP AI Control activation: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() );
        }
        wp_die(
            esc_html( 'WP AI Control: la activación falló — ' . $e->getMessage() . ' (revisa la versión de PHP ≥ 7.4 y el registro de errores).' ),
            esc_html__( 'Error al activar el plugin', 'wp-ai-control' ),
            array( 'response' => 500, 'back_link' => true )
        );
    }
});

register_deactivation_hook( __FILE__, function() {
    delete_option( 'wpaic_version' );
    // Do NOT deactivate license on plugin deactivation — only on explicit user action.
});

add_action( 'admin_menu', array( 'WPAIC_Admin', 'add_menu' ) );

// Admin notice when license is inactive
add_action( 'admin_notices', function() {
    // Only show to admins, skip the plugin's own settings page
    if ( ! current_user_can( 'manage_options' ) ) return;
    if ( isset( $_GET['page'] ) && 'wpaic' === $_GET['page'] ) return;
    if ( ! WPAIC_License::is_valid() ) {
        $url = admin_url( 'options-general.php?page=wpaic&tab=license' );
        echo '<div class="notice notice-warning is-dismissible"><p>';
        printf(
            wp_kses(
                /* translators: %s: link to license page */
                __( '<strong>WP AI Control:</strong> Your license is not active. <a href="%s">Activate your license</a> to enable all features.', 'wp-ai-control' ),
                array( 'strong' => array(), 'a' => array( 'href' => array() ) )
            ),
            esc_url( $url )
        );
        echo '</p></div>';
    }
} );

add_action( 'rest_api_init', function() {
    // Always register public / license-check routes
    ( new WPAIC_Context() )->register_routes();
    ( new WPAIC_WebMCP() )->register_routes();

    // All other routes require a valid license
    if ( ! WPAIC_License::is_valid() ) {
        // Register a catch-all that returns a 402 so the MCP gets a clear error
        register_rest_route( WPAIC_NAMESPACE, '/(?P<path>.+)', array(
            'methods'             => array( 'GET', 'POST', 'PUT', 'DELETE', 'PATCH' ),
            'callback'            => function() {
                return new WP_REST_Response( array(
                    'code'    => 'license_inactive',
                    'message' => 'WP AI Control: license is not active. Please activate your license at Settings → WP AI Control.',
                ), 402 );
            },
            'permission_callback' => '__return_true',
        ) );
        return;
    }

    try {
        $plan = WPAIC_Plan::get_current();

        ( new WPAIC_API() )->register_routes();
        if ( WPAIC_Plan::has_feature( 'widgets', $plan ) ) {
            ( new WPAIC_Widgets() )->register_routes();
        }
        if ( WPAIC_Plan::has_feature( 'analysis', $plan ) ) {
            ( new WPAIC_Analysis() )->register_routes();
        }
        if ( WPAIC_Plan::has_feature( 'builders', $plan ) ) {
            ( new WPAIC_Builder() )->register_routes();
        }
        if ( class_exists( 'WPAIC_WooCommerce' ) && WPAIC_Plan::has_feature( 'woocommerce', $plan ) ) {
            ( new WPAIC_WooCommerce() )->register_routes();
        }
        if ( class_exists( 'WPAIC_ACF' ) && WPAIC_Plan::has_feature( 'acf', $plan ) ) {
            ( new WPAIC_ACF() )->register_routes();
        }
        if ( class_exists( 'WPAIC_RankMath' ) && WPAIC_Plan::has_feature( 'rank_math', $plan ) ) {
            ( new WPAIC_RankMath() )->register_routes();
        }
    } catch ( Exception $e ) {
        if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
            error_log( 'WP AI Control REST init error: ' . $e->getMessage() );
        }
    }
});
