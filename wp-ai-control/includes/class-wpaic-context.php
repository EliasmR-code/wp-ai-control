<?php
class WPAIC_Context {
    public function register_routes() {
        register_rest_route( WPAIC_NAMESPACE, '/site-info', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'site_info' ),
            'permission_callback' => '__return_true',
        ));

        register_rest_route( WPAIC_NAMESPACE, '/builder-info', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'builders' ),
            'permission_callback' => '__return_true',
        ));

        register_rest_route( WPAIC_NAMESPACE, '/theme-docs', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'theme_docs' ),
            'permission_callback' => '__return_true',
        ));
    }

    public function theme_docs() {
        $theme    = wp_get_theme();
        $template = get_template_directory();
        $files    = array();
        foreach ( array( 'functions.php', 'style.css', 'README.md', 'readme.txt' ) as $f ) {
            $path = $template . '/' . $f;
            if ( file_exists( $path ) ) $files[] = $f;
        }
        return rest_ensure_response( array(
            'name'       => $theme->get( 'Name' ),
            'version'    => $theme->get( 'Version' ),
            'author'     => $theme->get( 'Author' ),
            'description' => $theme->get( 'Description' ),
            'template'   => get_template(),
            'stylesheet' => get_stylesheet(),
            'uri'        => $theme->get( 'ThemeURI' ),
            'files'      => $files,
        ) );
    }

    public function site_info() {
        return rest_ensure_response( array(
            'name'        => get_bloginfo( 'name' ),
            'description' => get_bloginfo( 'description' ),
            'url'         => get_site_url(),
            'version'     => get_bloginfo( 'version' ),
            'charset'     => get_bloginfo( 'charset' ),
            'language'    => get_bloginfo( 'language' ),
            'theme'       => wp_get_theme()->get( 'Name' ),
            'admin_email' => get_option( 'admin_email' ),
            'timezone'    => get_option( 'timezone_string' ),
        ));
    }

    public function builders() {
        $active = get_option( 'active_plugins', array() );
        $theme  = wp_get_theme()->get( 'Name' );
        $template = get_template();

        $builders = array(
            'gutenberg'    => true,
            'elementor'    => in_array( 'elementor/elementor.php', $active ),
            'divi'         => 'Divi' === $theme,
            'wpbakery'    => in_array( 'js_composer/js_composer.php', $active ),
            'bricks'       => 'bricks' === $template,
            'oxygen'       => 'oxygen' === $template,
            'beaver'       => in_array( 'bb-plugin/fl-builder.php', $active ),
            'brizy'        => in_array( 'brizy/brizy.php', $active ),
            'thrive'       => in_array( 'thrive-visual-editor/thrive-visual-editor.php', $active ),
            'breakdance'   => 'breakdance' === $template,
            'flatsome'     => 'flatsome' === $template,
            'kadence'      => 'kadence' === $template,
        );

        return rest_ensure_response( array(
            'active'   => $active,
            'theme'    => $theme,
            'builders_active' => array_keys( array_filter( $builders ) ),
            'plugins_count'   => count( $active ),
        ));
    }
}
