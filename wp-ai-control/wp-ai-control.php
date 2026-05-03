<?php
/**
 * Plugin Name: WP AI Control
 * Plugin URI: https://github.com/
 * Description: Control your WordPress site via AI agents using a secure REST API and MCP protocol.
 * Version: 1.0.0
 * Author: Developer
 * Author URI: https://example.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-ai-control
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 *
 * @package WP_AI_Control
 */

declare(strict_types=1);

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! defined( 'WPAIC_VERSION' ) ) {
	define( 'WPAIC_VERSION', '1.0.0' );
}

if ( ! defined( 'WPAIC_PLUGIN_DIR' ) ) {
	define( 'WPAIC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'WPAIC_PLUGIN_URL' ) ) {
	define( 'WPAIC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'WPAIC_PLUGIN_BASENAME' ) ) {
	define( 'WPAIC_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'WPAIC_REST_NAMESPACE' ) ) {
	define( 'WPAIC_REST_NAMESPACE', 'wp-ai-control/v1' );
}

if ( ! defined( 'WPAIC_PLUGIN_FILE' ) ) {
	define( 'WPAIC_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'WPAIC_API_KEY_PREFIX' ) ) {
	define( 'WPAIC_API_KEY_PREFIX', 'wpaic_live_' );
}

if ( ! defined( 'WPAIC_AUDIT_RETENTION_DEFAULT' ) ) {
	define( 'WPAIC_AUDIT_RETENTION_DEFAULT', 30 );
}

if ( ! defined( 'WPAIC_RATE_LIMIT_REQUESTS' ) ) {
	define( 'WPAIC_RATE_LIMIT_REQUESTS', 60 );
}

if ( ! defined( 'WPAIC_RATE_LIMIT_WINDOW' ) ) {
	define( 'WPAIC_RATE_LIMIT_WINDOW', 60 );
}

register_activation_hook( __FILE__, 'wpaic_activate' );
register_deactivation_hook( __FILE__, 'wpaic_deactivate' );

function wpaic_activate() {
	require_once WPAIC_PLUGIN_DIR . 'includes/class-wpaic-activator.php';
	WPAIC_Activator::activate();
}

function wpaic_deactivate() {
	require_once WPAIC_PLUGIN_DIR . 'includes/class-wpaic-deactivator.php';
	WPAIC_Deactivator::deactivate();
}

require WPAIC_PLUGIN_DIR . 'includes/class-wpaic-loader.php';

function wpaic_run() {
	$plugin = new WPAIC_Loader();
	$plugin->run();
}
wpaic_run();

add_filter( 'plugin_action_links_' . WPAIC_PLUGIN_BASENAME, 'wpaic_settings_link' );

function wpaic_settings_link( array $links ): array {
	$settings_link = sprintf(
		'<a href="%s">%s</a>',
		admin_url( 'options-general.php?page=wp-ai-control' ),
		esc_html__( 'Settings', 'wp-ai-control' )
	);
	array_unshift( $links, $settings_link );
	return $links;
}
