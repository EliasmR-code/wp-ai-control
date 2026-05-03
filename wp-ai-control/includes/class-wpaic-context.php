<?php
/**
 * Site context and introspection for WP AI Control.
 *
 * @package WP_AI_Control
 * @subpackage WP_AI_Control/includes
 */

class WPAIC_Context {

	public static function get_site_info() {
		global $wpdb;

		$active_plugins = get_option( 'active_plugins', array() );
		$plugins_info = array();

		foreach ( $active_plugins as $plugin_path ) {
			$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin_path );
			if ( $plugin_data['Name'] ) {
				$plugins_info[] = array(
					'name'    => $plugin_data['Name'],
					'version' => $plugin_data['Version'],
					'slug'    => dirname( $plugin_path ),
				);
			}
		}

		return array(
			'site_name'    => get_bloginfo( 'name' ),
			'site_url'     => get_bloginfo( 'url' ),
			'admin_email'  => get_bloginfo( 'admin_email' ),
			'wordpress_version' => get_bloginfo( 'version' ),
			'php_version'  => phpversion(),
			'active_theme' => array(
				'name'    => wp_get_theme()->get( 'Name' ),
				'version' => wp_get_theme()->get( 'Version' ),
				'slug'    => get_stylesheet(),
			),
			'active_plugins' => $plugins_info,
			'permalink_structure' => get_option( 'permalink_structure' ),
			'language'      => get_locale(),
			'users_count'   => count_users()['total_users'],
			'posts_count'   => (int) wp_count_posts( 'post' )->publish,
			'pages_count'   => (int) wp_count_posts( 'page' )->publish,
			'rest_url'      => get_rest_url() . WPAIC_REST_NAMESPACE,
			'is_multisite'  => is_multisite(),
		);
	}

	public static function get_theme_docs() {
		$theme = wp_get_theme();
		$stylesheet = get_stylesheet();

		$template_files = array();
		$theme_dir = get_stylesheet_directory();

		if ( is_dir( $theme_dir ) ) {
			$files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $theme_dir ) );
			foreach ( $files as $file ) {
				if ( $file->isFile() && in_array( $file->getExtension(), array( 'php', 'css', 'js' ), true ) ) {
					$template_files[] = str_replace( $theme_dir . '/', '', $file->getPathname() );
				}
			}
		}

		return array(
			'name'          => $theme->get( 'Name' ),
			'version'       => $theme->get( 'Version' ),
			'description'   => $theme->get( 'Description' ),
			'author'        => $theme->get( 'Author' ),
			'text_domain'   => $theme->get( 'TextDomain' ),
			'template_files' => $template_files,
			'supports'      => get_theme_support( 'post-thumbnails' ) ? 'post-thumbnails' : 'none',
			'has_woocommerce_support' => current_theme_supports( 'woocommerce' ),
		);
	}

	public static function get_builder_info() {
		$builders = array();

		// Gutenberg (always available)
		$builders['gutenberg'] = array(
			'detected' => true,
			'version'  => get_bloginfo( 'version' ),
			'supported' => true,
			'deep_intelligence' => true,
		);

		// Elementor
		if ( is_plugin_active( 'elementor/elementor.php' ) ) {
			$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/elementor/elementor.php' );
			$builders['elementor'] = array(
				'detected' => true,
				'version'  => $plugin_data['Version'],
				'supported' => true,
				'deep_intelligence' => true,
			);
		}

		// Divi (Theme #4 & 5)
		if ( 'Divi' === wp_get_theme()->get( 'Name' ) || 'divi' === get_stylesheet() ) {
			$theme = wp_get_theme();
			$builders['divi'] = array(
				'detected' => true,
				'version'  => $theme->get( 'Version' ),
				'supported' => true,
				'deep_intelligence' => true,
				'version_detected' => function_exists( 'et_get_option' ) ? '5' : '4',
			);
		}

		// WPBakery (Visual Composer legacy)
		if ( is_plugin_active( 'js_composer_salient/js_composer.php' ) || is_plugin_active( 'js_composer/js_composer.php' ) ) {
			$builders['wpbakery'] = array(
				'detected' => true,
				'version'  => 'unknown',
				'supported' => true,
				'deep_intelligence' => false,
			);
		}

		// Bricks Builder
		if ( is_plugin_active( 'bricks/bricks.php' ) ) {
			$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/bricks/bricks.php' );
			$builders['bricks'] = array(
				'detected' => true,
				'version'  => $plugin_data['Version'],
				'supported' => true,
				'deep_intelligence' => true,
			);
		}

		// Oxygen Builder
		if ( is_plugin_active( 'oxygen/oxygen.php' ) ) {
			$builders['oxygen'] = array(
				'detected' => true,
				'version'  => 'unknown',
				'supported' => true,
				'deep_intelligence' => true,
			);
		}

		// Beaver Builder
		if ( is_plugin_active( 'beaver-builder-lite/bb-plugin.php' ) || is_plugin_active( 'beaver-builder-pro/bb-plugin.php' ) ) {
			$builders['beaver'] = array(
				'detected' => true,
				'version'  => 'unknown',
				'supported' => true,
				'deep_intelligence' => false,
			);
		}

		// Brizy
		if ( is_plugin_active( 'brizy/brizy.php' ) ) {
			$builders['brizy'] = array(
				'detected' => true,
				'version'  => 'unknown',
				'supported' => true,
				'deep_intelligence' => false,
			);
		}

		// Thrive Architect
		if ( is_plugin_active( 'thrive-architect/thrive-architect.php' ) ) {
			$builders['thrive'] = array(
				'detected' => true,
				'version'  => 'unknown',
				'supported' => true,
				'deep_intelligence' => false,
			);
		}

		// Breakdance
		if ( is_plugin_active( 'breakdance/breakdance.php' ) ) {
			$builders['breakdance'] = array(
				'detected' => true,
				'version'  => 'unknown',
				'supported' => true,
				'deep_intelligence' => true,
			);
		}

		// Flatsome UX Builder
		if ( 'Flatsome' === wp_get_theme()->get( 'Name' ) || is_plugin_active( 'flatsome/flatsome.php' ) ) {
			$builders['flatsome'] = array(
				'detected' => true,
				'version'  => wp_get_theme()->get( 'Version' ),
				'supported' => true,
				'deep_intelligence' => true,
			);
		}

		// Visual Composer
		if ( is_plugin_active( 'visual-composer/plugin-wordpress.php' ) ) {
			$builders['visual_composer'] = array(
				'detected' => true,
				'version'  => 'unknown',
				'supported' => true,
				'deep_intelligence' => false,
			);
		}

		// Kadence Theme
		if ( 'Kadence' === wp_get_theme()->get( 'Name' ) || is_plugin_active( 'kadence/kadence.php' ) ) {
			$builders['kadence'] = array(
				'detected' => true,
				'version'  => wp_get_theme()->get( 'Version' ),
				'supported' => true,
				'deep_intelligence' => true,
			);
		}

		// Kadence Blocks
		if ( is_plugin_active( 'kadence-blocks/kadence-blocks.php' ) ) {
			$plugin_data = get_plugin_data( WP_PLUGIN_DIR . '/kadence-blocks/kadence-blocks.php' );
			$builders['kadence_blocks'] = array(
				'detected' => true,
				'version'  => $plugin_data['Version'],
				'supported' => true,
				'deep_intelligence' => true,
			);
		}

		return array(
			'builders' => $builders,
			'current_theme' => get_stylesheet(),
			'supported_count' => count( array_filter( $builders, function( $b ) { return $b['supported']; } ) ),
			'total_builders' => 12,
		);
	}
}
