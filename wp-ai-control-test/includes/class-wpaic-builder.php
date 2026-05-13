<?php
class WPAIC_Builder {

    // Meta keys used by each builder
    private $builder_meta_keys = array(
        'elementor'  => '_elementor_data',
        'divi'       => '_et_pb_use_builder',
        'beaver'     => '_fl_builder_data',
        'bricks'     => '_bricks_page_content_2',
        'oxygen'     => 'ct_builder_shortcodes',
        'wpbakery'   => null, // stored in post_content
        'gutenberg'  => null, // stored in post_content (blocks)
    );

    public function register_routes() {
        if ( ! WPAIC_Plan::has_feature( 'builders' ) ) {
            return;
        }

        register_rest_route( WPAIC_NAMESPACE, '/builder/(?P<builder>[a-zA-Z0-9_-]+)/extract/(?P<page_id>\d+)', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'extract_builder_content' ),
            'permission_callback' => array( 'WPAIC_Auth', 'check' ),
        ));
        register_rest_route( WPAIC_NAMESPACE, '/builder/(?P<builder>[a-zA-Z0-9_-]+)/inject/(?P<page_id>\d+)', array(
            'methods'             => 'POST',
            'callback'            => array( $this, 'inject_builder_content' ),
            'permission_callback' => array( 'WPAIC_Auth', 'check' ),
        ));
    }

    public function extract_builder_content( $request ) {
        $post_id = intval( $request['page_id'] );
        $builder = sanitize_text_field( $request['builder'] );
        $post    = get_post( $post_id );
        $allowed = WPAIC_Plan::allowed_builders();

        if ( ! $post ) return new WP_REST_Response( array( 'error' => 'Post not found' ), 404 );
        if ( ! in_array( $builder, $allowed, true ) ) {
            return new WP_REST_Response( array( 'error' => 'Builder not available in current plan', 'builder' => $builder, 'allowed' => $allowed ), 403 );
        }
        if ( ! array_key_exists( $builder, $this->builder_meta_keys ) ) {
            return new WP_REST_Response( array( 'error' => 'Unknown builder: ' . $builder, 'supported' => array_keys( $this->builder_meta_keys ) ), 400 );
        }

        $meta_key = $this->builder_meta_keys[ $builder ];
        $content  = null;

        if ( null === $meta_key ) {
            // Gutenberg / WPBakery — raw post_content
            $content = $post->post_content;
        } else {
            $content = get_post_meta( $post_id, $meta_key, true );
        }

        // Detect builder presence
        $is_active = false;
        if ( 'elementor' === $builder ) {
            $is_active = ! empty( get_post_meta( $post_id, '_elementor_edit_mode', true ) );
        } elseif ( 'divi' === $builder ) {
            $is_active = 'on' === get_post_meta( $post_id, '_et_pb_use_builder', true );
        } elseif ( 'gutenberg' === $builder ) {
            $is_active = has_blocks( $post->post_content );
        } elseif ( 'beaver' === $builder ) {
            $is_active = ! empty( get_post_meta( $post_id, '_fl_builder_enabled', true ) );
        }

        return rest_ensure_response( array(
            'post_id'   => $post_id,
            'builder'   => $builder,
            'is_active' => $is_active,
            'meta_key'  => $meta_key,
            'content'   => $content,
        ) );
    }

    public function inject_builder_content( $request ) {
        $post_id = intval( $request['page_id'] );
        $builder = sanitize_text_field( $request['builder'] );
        $content = $request->get_param( 'content' );
        $post    = get_post( $post_id );
        $allowed = WPAIC_Plan::allowed_builders();

        if ( ! $post ) return new WP_REST_Response( array( 'error' => 'Post not found' ), 404 );
        if ( ! in_array( $builder, $allowed, true ) ) {
            return new WP_REST_Response( array( 'error' => 'Builder not available in current plan', 'builder' => $builder, 'allowed' => $allowed ), 403 );
        }
        if ( ! array_key_exists( $builder, $this->builder_meta_keys ) ) {
            return new WP_REST_Response( array( 'error' => 'Unknown builder: ' . $builder ), 400 );
        }

        $meta_key = $this->builder_meta_keys[ $builder ];

        if ( null === $meta_key ) {
            // Store in post_content
            $result = wp_update_post( array( 'ID' => $post_id, 'post_content' => wp_kses_post( $content ) ), true );
            if ( is_wp_error( $result ) ) return new WP_REST_Response( array( 'error' => $result->get_error_message() ), 422 );
        } else {
            // Store in meta (allow JSON content from page builders)
            $value = is_string( $content ) ? wp_slash( $content ) : $content;
            update_post_meta( $post_id, $meta_key, $value );
        }

        return rest_ensure_response( array( 'success' => true, 'post_id' => $post_id, 'builder' => $builder ) );
    }
}
