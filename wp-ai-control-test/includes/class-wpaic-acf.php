<?php
class WPAIC_ACF {

    // Internal REST params to strip before writing ACF fields
    private $internal_params = array( 'id', 'post_id', 'context', '_fields', '_embed', '_envelope', '_locale' );

    public function register_routes() {
        if ( ! WPAIC_Plan::has_feature( 'acf' ) ) return;
        if ( ! function_exists( 'acf' ) && ! class_exists( 'ACF' ) ) return;

        // Field groups
        register_rest_route( WPAIC_NAMESPACE, '/acf/field-groups', array(
            array( 'methods' => 'GET',  'callback' => array( $this, 'get_field_groups' ),    'permission_callback' => array( 'WPAIC_Auth', 'check' ) ),
            array( 'methods' => 'POST', 'callback' => array( $this, 'create_field_group' ),  'permission_callback' => array( 'WPAIC_Auth', 'check' ) ),
        ));
        register_rest_route( WPAIC_NAMESPACE, '/acf/field-groups/(?P<id>\d+)', array(
            array( 'methods' => 'GET',    'callback' => array( $this, 'get_field_group' ),    'permission_callback' => array( 'WPAIC_Auth', 'check' ) ),
            array( 'methods' => 'PUT',    'callback' => array( $this, 'update_field_group' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ),
            array( 'methods' => 'DELETE', 'callback' => array( $this, 'delete_field_group' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ),
        ));
        register_rest_route( WPAIC_NAMESPACE, '/acf/field-groups/(?P<id>\d+)/fields', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'get_group_fields' ),
            'permission_callback' => array( 'WPAIC_Auth', 'check' ),
        ));
        register_rest_route( WPAIC_NAMESPACE, '/acf/field-groups/(?P<id>\d+)/duplicate', array(
            'methods'             => 'POST',
            'callback'            => array( $this, 'duplicate_field_group' ),
            'permission_callback' => array( 'WPAIC_Auth', 'check' ),
        ));
        register_rest_route( WPAIC_NAMESPACE, '/acf/field-groups/assign', array(
            'methods'             => 'POST',
            'callback'            => array( $this, 'assign_field_group' ),
            'permission_callback' => array( 'WPAIC_Auth', 'check' ),
        ));

        // Fields CRUD
        register_rest_route( WPAIC_NAMESPACE, '/acf/fields', array(
            array( 'methods' => 'GET',  'callback' => array( $this, 'get_fields' ),  'permission_callback' => array( 'WPAIC_Auth', 'check' ) ),
            array( 'methods' => 'POST', 'callback' => array( $this, 'create_field' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ),
        ));
        register_rest_route( WPAIC_NAMESPACE, '/acf/fields/(?P<field_key>[a-zA-Z0-9_]+)', array(
            array( 'methods' => 'GET',    'callback' => array( $this, 'get_field' ),    'permission_callback' => array( 'WPAIC_Auth', 'check' ) ),
            array( 'methods' => 'PUT',    'callback' => array( $this, 'update_field' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ),
            array( 'methods' => 'DELETE', 'callback' => array( $this, 'delete_field' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ),
        ));
        register_rest_route( WPAIC_NAMESPACE, '/acf/fields/(?P<field_key>[a-zA-Z0-9_]+)/clone', array(
            'methods'             => 'POST',
            'callback'            => array( $this, 'clone_field' ),
            'permission_callback' => array( 'WPAIC_Auth', 'check' ),
        ));

        // Post fields (legacy + new)
        register_rest_route( WPAIC_NAMESPACE, '/acf/groups', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'get_field_groups' ),
            'permission_callback' => array( 'WPAIC_Auth', 'check' ),
        ));
        register_rest_route( WPAIC_NAMESPACE, '/acf/post/(?P<id>\d+)', array(
            array( 'methods' => 'GET',  'callback' => array( $this, 'get_post_fields' ),    'permission_callback' => array( 'WPAIC_Auth', 'check' ) ),
            array( 'methods' => 'POST', 'callback' => array( $this, 'update_post_fields' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ),
        ));
        register_rest_route( WPAIC_NAMESPACE, '/acf/post/(?P<post_id>\d+)/field/(?P<field_key>[a-zA-Z0-9_]+)', array(
            array( 'methods' => 'GET', 'callback' => array( $this, 'get_post_field' ),    'permission_callback' => array( 'WPAIC_Auth', 'check' ) ),
            array( 'methods' => 'PUT', 'callback' => array( $this, 'update_post_field' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ),
        ));
        register_rest_route( WPAIC_NAMESPACE, '/acf/post/(?P<post_id>\d+)/repeater/(?P<field_key>[a-zA-Z0-9_]+)', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'get_post_repeater' ),
            'permission_callback' => array( 'WPAIC_Auth', 'check' ),
        ));
        register_rest_route( WPAIC_NAMESPACE, '/acf/post/(?P<post_id>\d+)/flexible-content', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'get_post_flexible_content' ),
            'permission_callback' => array( 'WPAIC_Auth', 'check' ),
        ));

        // Options pages
        register_rest_route( WPAIC_NAMESPACE, '/acf/options', array(
            array( 'methods' => 'GET',  'callback' => array( $this, 'get_options' ),    'permission_callback' => array( 'WPAIC_Auth', 'check' ) ),
            array( 'methods' => 'POST', 'callback' => array( $this, 'update_options' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ),
        ));
    }

    // --- Field Groups ---

    public function get_field_groups() {
        $groups = acf_get_field_groups();
        return rest_ensure_response( array_map( function( $g ) {
            return array( 'id' => $g['ID'], 'title' => $g['title'], 'key' => $g['key'], 'active' => $g['active'], 'location' => $g['location'] );
        }, $groups ) );
    }

    public function get_field_group( $request ) {
        $group = acf_get_field_group( intval( $request['id'] ) );
        if ( ! $group ) return new WP_REST_Response( array( 'error' => 'Field group not found' ), 404 );
        $group['fields'] = acf_get_fields( $group );
        return rest_ensure_response( $group );
    }

    public function create_field_group( $request ) {
        $args = array(
            'title'    => sanitize_text_field( $request->get_param( 'title' ) ),
            'location' => $request->get_param( 'location' ) ?? array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'post' ) ) ),
            'active'   => (bool) ( $request->get_param( 'active' ) ?? true ),
        );
        $group = acf_add_local_field_group( $args );
        return rest_ensure_response( array( 'success' => true, 'group' => $group ) );
    }

    public function update_field_group( $request ) {
        $id    = intval( $request['id'] );
        $group = acf_get_field_group( $id );
        if ( ! $group ) return new WP_REST_Response( array( 'error' => 'Field group not found' ), 404 );
        if ( $request->get_param( 'title' ) ) $group['title'] = sanitize_text_field( $request->get_param( 'title' ) );
        if ( $request->get_param( 'active' ) !== null ) $group['active'] = (bool) $request->get_param( 'active' );
        if ( $request->get_param( 'location' ) ) $group['location'] = $request->get_param( 'location' );
        wp_update_post( array( 'ID' => $id, 'post_title' => $group['title'] ) );
        update_post_meta( $id, '_acf_field_group', $group );
        return rest_ensure_response( array( 'success' => true ) );
    }

    public function delete_field_group( $request ) {
        $id = intval( $request['id'] );
        $result = wp_delete_post( $id, true );
        return rest_ensure_response( array( 'success' => (bool) $result ) );
    }

    public function get_group_fields( $request ) {
        $group = acf_get_field_group( intval( $request['id'] ) );
        if ( ! $group ) return new WP_REST_Response( array( 'error' => 'Field group not found' ), 404 );
        $fields = acf_get_fields( $group );
        return rest_ensure_response( $fields ?: array() );
    }

    public function duplicate_field_group( $request ) {
        $id    = intval( $request['id'] );
        $group = acf_get_field_group( $id );
        if ( ! $group ) return new WP_REST_Response( array( 'error' => 'Field group not found' ), 404 );
        if ( function_exists( 'acf_duplicate_field_group' ) ) {
            $new_group = acf_duplicate_field_group( $id );
            return rest_ensure_response( array( 'success' => true, 'new_group' => $new_group ) );
        }
        return new WP_REST_Response( array( 'error' => 'acf_duplicate_field_group not available' ), 501 );
    }

    public function assign_field_group( $request ) {
        $group_id   = intval( $request->get_param( 'group_id' ) );
        $post_types = $request->get_param( 'post_types' ) ?? array();
        $group      = acf_get_field_group( $group_id );
        if ( ! $group ) return new WP_REST_Response( array( 'error' => 'Field group not found' ), 404 );
        $location = array();
        foreach ( (array) $post_types as $pt ) {
            $location[] = array( array( 'param' => 'post_type', 'operator' => '==', 'value' => sanitize_key( $pt ) ) );
        }
        $group['location'] = $location;
        update_post_meta( $group_id, '_acf_field_group', $group );
        return rest_ensure_response( array( 'success' => true ) );
    }

    // --- Fields CRUD ---

    public function get_fields() {
        $groups = acf_get_field_groups();
        $all    = array();
        foreach ( $groups as $g ) {
            $fields = acf_get_fields( $g );
            if ( $fields ) $all = array_merge( $all, $fields );
        }
        return rest_ensure_response( array_map( function( $f ) {
            return array( 'key' => $f['key'], 'label' => $f['label'], 'name' => $f['name'], 'type' => $f['type'], 'parent' => $f['parent'] );
        }, $all ) );
    }

    public function get_field( $request ) {
        $field = acf_get_field( sanitize_text_field( $request['field_key'] ) );
        if ( ! $field ) return new WP_REST_Response( array( 'error' => 'Field not found' ), 404 );
        return rest_ensure_response( $field );
    }

    public function create_field( $request ) {
        $field = array(
            'key'    => 'field_' . uniqid(),
            'label'  => sanitize_text_field( $request->get_param( 'label' ) ),
            'name'   => sanitize_key( $request->get_param( 'name' ) ),
            'type'   => sanitize_text_field( $request->get_param( 'type' ) ?? 'text' ),
            'parent' => intval( $request->get_param( 'group_id' ) ),
        );
        if ( function_exists( 'acf_add_local_field' ) ) {
            acf_add_local_field( $field );
        } else {
            return new WP_REST_Response( array( 'error' => 'ACF field creation not supported' ), 501 );
        }
        return rest_ensure_response( array( 'success' => true, 'field' => $field ) );
    }

    public function update_field( $request ) {
        $key   = sanitize_text_field( $request['field_key'] );
        $field = acf_get_field( $key );
        if ( ! $field ) return new WP_REST_Response( array( 'error' => 'Field not found' ), 404 );
        if ( $request->get_param( 'label' ) ) $field['label'] = sanitize_text_field( $request->get_param( 'label' ) );
        if ( $request->get_param( 'type' ) ) $field['type']   = sanitize_text_field( $request->get_param( 'type' ) );
        update_post_meta( $field['ID'], '_acf_field', $field );
        return rest_ensure_response( array( 'success' => true ) );
    }

    public function delete_field( $request ) {
        $field = acf_get_field( sanitize_text_field( $request['field_key'] ) );
        if ( ! $field ) return new WP_REST_Response( array( 'error' => 'Field not found' ), 404 );
        if ( function_exists( 'acf_delete_field' ) ) {
            acf_delete_field( $field['ID'] );
        } else {
            wp_delete_post( $field['ID'], true );
        }
        return rest_ensure_response( array( 'success' => true ) );
    }

    public function clone_field( $request ) {
        $field = acf_get_field( sanitize_text_field( $request['field_key'] ) );
        if ( ! $field ) return new WP_REST_Response( array( 'error' => 'Field not found' ), 404 );
        if ( function_exists( 'acf_duplicate_field' ) ) {
            $new = acf_duplicate_field( $field['ID'] );
            return rest_ensure_response( array( 'success' => true, 'new_field' => $new ) );
        }
        return new WP_REST_Response( array( 'error' => 'acf_duplicate_field not available' ), 501 );
    }

    // --- Post fields ---

    public function get_post_fields( $request ) {
        $fields = get_fields( intval( $request['id'] ) );
        return rest_ensure_response( $fields ?: array() );
    }

    public function update_post_fields( $request ) {
        $post_id = intval( $request['id'] );
        $params  = $request->get_json_params();
        if ( ! is_array( $params ) ) $params = $request->get_body_params();

        // Strip internal REST params to prevent ACF field injection
        foreach ( $this->internal_params as $key ) {
            unset( $params[ $key ] );
        }

        foreach ( $params as $key => $value ) {
            update_field( sanitize_text_field( $key ), $value, $post_id );
        }
        return rest_ensure_response( array( 'success' => true, 'fields' => get_fields( $post_id ) ) );
    }

    public function get_post_field( $request ) {
        $post_id   = intval( $request['post_id'] );
        $field_key = sanitize_text_field( $request['field_key'] );
        $value     = get_field( $field_key, $post_id );
        return rest_ensure_response( array( 'field' => $field_key, 'value' => $value ) );
    }

    public function update_post_field( $request ) {
        $post_id   = intval( $request['post_id'] );
        $field_key = sanitize_text_field( $request['field_key'] );
        $value     = $request->get_param( 'value' );
        update_field( $field_key, $value, $post_id );
        return rest_ensure_response( array( 'success' => true, 'field' => $field_key, 'value' => get_field( $field_key, $post_id ) ) );
    }

    public function get_post_repeater( $request ) {
        $post_id   = intval( $request['post_id'] );
        $field_key = sanitize_text_field( $request['field_key'] );
        $rows      = array();
        if ( have_rows( $field_key, $post_id ) ) {
            while ( have_rows( $field_key, $post_id ) ) {
                the_row();
                $rows[] = get_row( true );
            }
        }
        return rest_ensure_response( array( 'field' => $field_key, 'rows' => $rows ) );
    }

    public function get_post_flexible_content( $request ) {
        $post_id = intval( $request['post_id'] );
        $fields  = get_fields( $post_id );
        $flex    = array();
        if ( is_array( $fields ) ) {
            foreach ( $fields as $key => $value ) {
                if ( is_array( $value ) && isset( $value[0]['acf_fc_layout'] ) ) {
                    $flex[ $key ] = $value;
                }
            }
        }
        return rest_ensure_response( array( 'post_id' => $post_id, 'flexible_fields' => $flex ) );
    }

    // --- Options ---

    public function get_options() {
        $fields = get_fields( 'option' );
        return rest_ensure_response( $fields ?: array() );
    }

    public function update_options( $request ) {
        $params = $request->get_json_params();
        if ( ! is_array( $params ) ) $params = $request->get_body_params();
        foreach ( $this->internal_params as $key ) unset( $params[ $key ] );
        foreach ( $params as $key => $value ) {
            update_field( sanitize_text_field( $key ), $value, 'option' );
        }
        return rest_ensure_response( array( 'success' => true ) );
    }
}

