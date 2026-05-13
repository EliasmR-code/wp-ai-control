<?php
class WPAIC_Widgets {
    public function register_routes() {
        register_rest_route( WPAIC_NAMESPACE, '/widgets', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'list_widgets' ),
            'permission_callback' => array( 'WPAIC_Auth', 'check' ),
        ));

        register_rest_route( WPAIC_NAMESPACE, '/sidebars', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'list_sidebars' ),
            'permission_callback' => array( 'WPAIC_Auth', 'check' ),
        ));
    }

    public function list_widgets() {
        global $wp_registered_widgets;
        $widgets = array();
        foreach ( $wp_registered_widgets as $id => $widget ) {
            $widgets[] = array(
                'id'   => $id,
                'name' => $widget['name'],
            );
        }
        return rest_ensure_response( $widgets );
    }

    public function list_sidebars() {
        global $wp_registered_sidebars;
        return rest_ensure_response( array_values( $wp_registered_sidebars ) );
    }
}
