<?php
class WPAIC_API {

    public function register_routes() {
        $plan = WPAIC_Plan::get_current();

        // Auth
        register_rest_route( WPAIC_NAMESPACE, '/auth/key', array(
            'methods' => 'GET', 'callback' => array( $this, 'get_key' ),
            'permission_callback' => array( 'WPAIC_Auth', 'check_admin' ),
        ));
        register_rest_route( WPAIC_NAMESPACE, '/auth/key', array(
            'methods' => 'POST', 'callback' => array( $this, 'regenerate_key' ),
            'permission_callback' => array( 'WPAIC_Auth', 'check_admin' ),
        ));

        // Posts
        register_rest_route( WPAIC_NAMESPACE, '/posts', array( 'methods' => 'GET', 'callback' => array( $this, 'get_posts' ), 'permission_callback' => '__return_true' ));
        register_rest_route( WPAIC_NAMESPACE, '/posts', array( 'methods' => 'POST', 'callback' => array( $this, 'create_post' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' )));
        register_rest_route( WPAIC_NAMESPACE, '/posts/(?P<id>\d+)', array( 'methods' => 'GET', 'callback' => array( $this, 'get_post' ), 'permission_callback' => '__return_true' ));
        register_rest_route( WPAIC_NAMESPACE, '/posts/(?P<id>\d+)', array( 'methods' => 'PUT', 'callback' => array( $this, 'update_post' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' )));
        register_rest_route( WPAIC_NAMESPACE, '/posts/(?P<id>\d+)', array( 'methods' => 'DELETE', 'callback' => array( $this, 'delete_post' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' )));
        register_rest_route( WPAIC_NAMESPACE, '/posts/(?P<id>\d+)/meta', array( 'methods' => 'GET', 'callback' => array( $this, 'get_post_meta_all' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ));
        register_rest_route( WPAIC_NAMESPACE, '/posts/(?P<id>\d+)/meta', array( 'methods' => 'POST', 'callback' => array( $this, 'update_post_meta_bulk' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ));
        register_rest_route( WPAIC_NAMESPACE, '/posts/(?P<id>\d+)/meta/(?P<meta_key>[a-zA-Z0-9_-]+)', array( 'methods' => 'DELETE', 'callback' => array( $this, 'delete_post_meta_key' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ));

        // Pages
        register_rest_route( WPAIC_NAMESPACE, '/pages', array( 'methods' => 'GET', 'callback' => array( $this, 'get_pages' ), 'permission_callback' => '__return_true' ));
        register_rest_route( WPAIC_NAMESPACE, '/pages', array( 'methods' => 'POST', 'callback' => array( $this, 'create_page' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' )));
        register_rest_route( WPAIC_NAMESPACE, '/pages/(?P<id>\d+)', array( 'methods' => 'GET', 'callback' => array( $this, 'get_page' ), 'permission_callback' => '__return_true' ));
        register_rest_route( WPAIC_NAMESPACE, '/pages/(?P<id>\d+)', array( 'methods' => 'PUT', 'callback' => array( $this, 'update_page' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' )));
        register_rest_route( WPAIC_NAMESPACE, '/pages/(?P<id>\d+)', array( 'methods' => 'DELETE', 'callback' => array( $this, 'delete_page' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' )));
        register_rest_route( WPAIC_NAMESPACE, '/pages/(?P<id>\d+)/duplicate', array( 'methods' => 'POST', 'callback' => array( $this, 'duplicate_page' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ));

        // Media
        register_rest_route( WPAIC_NAMESPACE, '/media', array( 'methods' => 'GET', 'callback' => array( $this, 'get_media' ), 'permission_callback' => '__return_true' ));
        register_rest_route( WPAIC_NAMESPACE, '/media/(?P<id>\d+)', array( 'methods' => 'GET', 'callback' => array( $this, 'get_media_item' ), 'permission_callback' => '__return_true' ));
        register_rest_route( WPAIC_NAMESPACE, '/media/(?P<id>\d+)', array( 'methods' => 'DELETE', 'callback' => array( $this, 'delete_media' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' )));
        register_rest_route( WPAIC_NAMESPACE, '/media/(?P<id>\d+)/meta', array( 'methods' => 'PUT', 'callback' => array( $this, 'update_media_meta' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ));
        register_rest_route( WPAIC_NAMESPACE, '/media/upload', array( 'methods' => 'POST', 'callback' => array( $this, 'upload_media' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ));

        // Bulk operations
        register_rest_route( WPAIC_NAMESPACE, '/bulk-update-posts', array( 'methods' => 'POST', 'callback' => array( $this, 'bulk_update_posts' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ));
        register_rest_route( WPAIC_NAMESPACE, '/bulk-delete-posts', array( 'methods' => 'POST', 'callback' => array( $this, 'bulk_delete_posts' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ));

        // Users
        if ( WPAIC_Plan::has_feature( 'users', $plan ) ) {
            register_rest_route( WPAIC_NAMESPACE, '/users', array( 'methods' => 'GET', 'callback' => array( $this, 'get_users' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' )));
            register_rest_route( WPAIC_NAMESPACE, '/users', array( 'methods' => 'POST', 'callback' => array( $this, 'create_user' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ));
            register_rest_route( WPAIC_NAMESPACE, '/users/(?P<id>\d+)', array( 'methods' => 'GET', 'callback' => array( $this, 'get_user' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ));
            register_rest_route( WPAIC_NAMESPACE, '/users/(?P<id>\d+)', array( 'methods' => 'PUT', 'callback' => array( $this, 'update_user' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ));
            register_rest_route( WPAIC_NAMESPACE, '/users/(?P<id>\d+)', array( 'methods' => 'DELETE', 'callback' => array( $this, 'delete_user' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ));
        }

        // Categories
        register_rest_route( WPAIC_NAMESPACE, '/categories', array( 'methods' => 'GET', 'callback' => array( $this, 'get_categories' ), 'permission_callback' => '__return_true' ));
        register_rest_route( WPAIC_NAMESPACE, '/categories', array( 'methods' => 'POST', 'callback' => array( $this, 'create_category' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' )));

        // Tags
        register_rest_route( WPAIC_NAMESPACE, '/tags', array( 'methods' => 'GET', 'callback' => array( $this, 'get_tags' ), 'permission_callback' => '__return_true' ));

        // Comments
        register_rest_route( WPAIC_NAMESPACE, '/comments', array( 'methods' => 'GET', 'callback' => array( $this, 'get_comments' ), 'permission_callback' => '__return_true' ));
        register_rest_route( WPAIC_NAMESPACE, '/comments', array( 'methods' => 'POST', 'callback' => array( $this, 'create_comment' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' )));
        register_rest_route( WPAIC_NAMESPACE, '/comments/(?P<id>\d+)', array( 'methods' => 'GET', 'callback' => array( $this, 'get_comment' ), 'permission_callback' => '__return_true' ));
        register_rest_route( WPAIC_NAMESPACE, '/comments/(?P<id>\d+)', array( 'methods' => 'DELETE', 'callback' => array( $this, 'delete_comment' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ));
        register_rest_route( WPAIC_NAMESPACE, '/comments/(?P<id>\d+)/approve', array( 'methods' => 'POST', 'callback' => array( $this, 'approve_comment' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ));
        register_rest_route( WPAIC_NAMESPACE, '/comments/(?P<id>\d+)/spam', array( 'methods' => 'POST', 'callback' => array( $this, 'spam_comment' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ));

        // Navigation menus (used by MCP / CLI)
        register_rest_route( WPAIC_NAMESPACE, '/menus/locations', array( 'methods' => 'GET', 'callback' => array( $this, 'get_menu_locations' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ));
        register_rest_route( WPAIC_NAMESPACE, '/menus/(?P<id>\d+)', array( 'methods' => 'GET', 'callback' => array( $this, 'get_menu' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ));
        register_rest_route( WPAIC_NAMESPACE, '/menus', array( 'methods' => 'GET', 'callback' => array( $this, 'get_menus' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ));

        // Settings
        if ( WPAIC_Plan::has_feature( 'settings', $plan ) ) {
            register_rest_route( WPAIC_NAMESPACE, '/settings', array( 'methods' => 'GET', 'callback' => array( $this, 'get_settings' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' )));
            register_rest_route( WPAIC_NAMESPACE, '/settings', array( 'methods' => 'PUT', 'callback' => array( $this, 'update_settings' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ));
        }

        // Themes
        if ( WPAIC_Plan::has_feature( 'themes', $plan ) ) {
            register_rest_route( WPAIC_NAMESPACE, '/themes', array( 'methods' => 'GET', 'callback' => array( $this, 'get_themes' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' )));
            register_rest_route( WPAIC_NAMESPACE, '/themes/(?P<slug>[a-zA-Z0-9_-]+)/activate', array( 'methods' => 'POST', 'callback' => array( $this, 'activate_theme' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ));
            register_rest_route( WPAIC_NAMESPACE, '/themes/(?P<slug>[a-zA-Z0-9_-]+)/update', array( 'methods' => 'POST', 'callback' => array( $this, 'update_theme' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ));
        }

        // Plugins
        if ( WPAIC_Plan::has_feature( 'plugins', $plan ) ) {
            register_rest_route( WPAIC_NAMESPACE, '/plugins', array( 'methods' => 'GET', 'callback' => array( $this, 'get_plugins' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' )));
            register_rest_route( WPAIC_NAMESPACE, '/plugins/install', array( 'methods' => 'POST', 'callback' => array( $this, 'install_plugin' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ));
            register_rest_route( WPAIC_NAMESPACE, '/plugins/(?P<slug>[a-zA-Z0-9_-]+)/activate', array( 'methods' => 'POST', 'callback' => array( $this, 'activate_plugin' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ));
            register_rest_route( WPAIC_NAMESPACE, '/plugins/(?P<slug>[a-zA-Z0-9_-]+)/deactivate', array( 'methods' => 'POST', 'callback' => array( $this, 'deactivate_plugin' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ));
        }

        // Post Types
        register_rest_route( WPAIC_NAMESPACE, '/post-types', array( 'methods' => 'GET', 'callback' => array( $this, 'get_post_types' ), 'permission_callback' => '__return_true' ));

        register_rest_route( WPAIC_NAMESPACE, '/taxonomies', array( 'methods' => 'GET', 'callback' => array( $this, 'get_taxonomies' ), 'permission_callback' => '__return_true' ));
        register_rest_route( WPAIC_NAMESPACE, '/taxonomies/(?P<taxonomy>[a-zA-Z0-9_-]+)/terms', array( 'methods' => 'GET', 'callback' => array( $this, 'get_taxonomy_terms' ), 'permission_callback' => '__return_true' ));

        // Terms CRUD
        register_rest_route( WPAIC_NAMESPACE, '/terms', array( 'methods' => 'POST', 'callback' => array( $this, 'create_term' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ));
        register_rest_route( WPAIC_NAMESPACE, '/terms/(?P<id>\d+)', array( 'methods' => 'GET', 'callback' => array( $this, 'get_term' ), 'permission_callback' => '__return_true' ));
        register_rest_route( WPAIC_NAMESPACE, '/terms/(?P<id>\d+)', array( 'methods' => 'PUT', 'callback' => array( $this, 'update_term' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ));
        register_rest_route( WPAIC_NAMESPACE, '/terms/(?P<id>\d+)', array( 'methods' => 'DELETE', 'callback' => array( $this, 'delete_term' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ));

        // Search
        register_rest_route( WPAIC_NAMESPACE, '/search', array( 'methods' => 'GET', 'callback' => array( $this, 'search_content' ), 'permission_callback' => '__return_true' ));

        // Snapshots (revisions)
        register_rest_route( WPAIC_NAMESPACE, '/snapshots', array( 'methods' => 'GET', 'callback' => array( $this, 'list_snapshots' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ));
        register_rest_route( WPAIC_NAMESPACE, '/snapshots/rollback', array( 'methods' => 'POST', 'callback' => array( $this, 'restore_snapshot' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ));

        // Usage & Plan
        register_rest_route( WPAIC_NAMESPACE, '/usage', array( 'methods' => 'GET', 'callback' => array( $this, 'get_usage' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ));
        register_rest_route( WPAIC_NAMESPACE, '/plan-info', array( 'methods' => 'GET', 'callback' => array( $this, 'get_plan_info' ), 'permission_callback' => '__return_true' ));
    }

    // Auth
    public function get_key() { return rest_ensure_response( array( 'key' => WPAIC_Auth::get_key() ) ); }
    public function regenerate_key() {
        delete_option( 'wpaic_api_key' );
        return rest_ensure_response( array( 'key' => WPAIC_Auth::get_key() ) );
    }

    // Posts
    public function get_posts( $request ) {
        $per_page = intval( $request->get_param( 'per_page' ) ?: 10 );
        $page     = intval( $request->get_param( 'page' ) ?: 1 );
        $status   = sanitize_text_field( $request->get_param( 'status' ) ?: 'publish' );
        $args     = array( 'post_type' => 'post', 'posts_per_page' => $per_page, 'paged' => $page, 'post_status' => $status );
        if ( $request->get_param( 'search' ) ) $args['s'] = sanitize_text_field( $request->get_param( 'search' ) );
        $query = new WP_Query( $args );
        return rest_ensure_response( array_map( function( $p ) {
            return array( 'id' => $p->ID, 'title' => $p->post_title, 'slug' => $p->post_name, 'status' => $p->post_status, 'date' => $p->post_date, 'excerpt' => $p->post_excerpt );
        }, $query->posts ) );
    }
    public function get_post( $request ) {
        $post = get_post( $request['id'] );
        if ( ! $post ) return new WP_REST_Response( array( 'error' => 'Not found' ), 404 );
        return rest_ensure_response( $post );
    }
    public function create_post( $request ) {
        $id = wp_insert_post( array(
            'post_title'   => sanitize_text_field( $request->get_param( 'title' ) ),
            'post_content' => wp_kses_post( $request->get_param( 'content' ) ?? '' ),
            'post_excerpt' => sanitize_textarea_field( $request->get_param( 'excerpt' ) ?? '' ),
            'post_status'  => sanitize_text_field( $request->get_param( 'status' ) ?? 'draft' ),
            'post_type'    => 'post',
        ), true );
        if ( is_wp_error( $id ) ) return new WP_REST_Response( array( 'error' => $id->get_error_message() ), 422 );
        return rest_ensure_response( array( 'success' => true, 'post_id' => $id ) );
    }
    public function update_post( $request ) {
        $data = array( 'ID' => intval( $request['id'] ) );
        if ( $request->get_param( 'title' ) ) $data['post_title']   = sanitize_text_field( $request->get_param( 'title' ) );
        if ( $request->get_param( 'content' ) ) $data['post_content'] = wp_kses_post( $request->get_param( 'content' ) );
        if ( $request->get_param( 'status' ) ) $data['post_status']  = sanitize_text_field( $request->get_param( 'status' ) );
        $result = wp_update_post( $data, true );
        if ( is_wp_error( $result ) ) return new WP_REST_Response( array( 'error' => $result->get_error_message() ), 422 );
        return rest_ensure_response( array( 'success' => true ) );
    }
    public function delete_post( $request ) {
        wp_delete_post( $request['id'], true );
        return rest_ensure_response( array( 'success' => true ) );
    }

    // Post Meta
    public function get_post_meta_all( $request ) {
        $meta = get_post_meta( intval( $request['id'] ) );
        $result = array();
        foreach ( $meta as $key => $values ) {
            $result[ $key ] = count( $values ) === 1 ? $values[0] : $values;
        }
        return rest_ensure_response( $result );
    }
    public function update_post_meta_bulk( $request ) {
        $post_id = intval( $request['id'] );
        $meta    = $request->get_param( 'meta' );
        if ( ! is_array( $meta ) ) return new WP_REST_Response( array( 'error' => 'meta must be an object' ), 400 );
        foreach ( $meta as $key => $value ) {
            update_post_meta( $post_id, sanitize_key( $key ), $value );
        }
        return rest_ensure_response( array( 'success' => true ) );
    }
    public function delete_post_meta_key( $request ) {
        delete_post_meta( intval( $request['id'] ), sanitize_key( $request['meta_key'] ) );
        return rest_ensure_response( array( 'success' => true ) );
    }

    // Pages
    public function get_pages( $request ) {
        $per_page = intval( $request->get_param( 'per_page' ) ?: 50 );
        $status   = sanitize_text_field( $request->get_param( 'status' ) ?: 'publish' );
        $query    = new WP_Query( array( 'post_type' => 'page', 'posts_per_page' => $per_page, 'post_status' => $status ) );
        return rest_ensure_response( array_map( function( $p ) {
            return array( 'id' => $p->ID, 'title' => $p->post_title, 'slug' => $p->post_name, 'status' => $p->post_status );
        }, $query->posts ) );
    }
    public function get_page( $request ) {
        $post = get_post( $request['id'] );
        if ( ! $post || 'page' !== $post->post_type ) return new WP_REST_Response( array( 'error' => 'Not found' ), 404 );
        return rest_ensure_response( $post );
    }
    public function create_page( $request ) {
        $args = array(
            'post_title'   => sanitize_text_field( $request->get_param( 'title' ) ),
            'post_content' => wp_kses_post( $request->get_param( 'content' ) ?? '' ),
            'post_status'  => sanitize_text_field( $request->get_param( 'status' ) ?? 'draft' ),
            'post_type'    => 'page',
        );
        if ( $request->has_param( 'parent' ) ) {
            $parent = intval( $request->get_param( 'parent' ) );
            if ( $parent > 0 ) {
                $parent_post = get_post( $parent );
                if ( $parent_post && 'page' === $parent_post->post_type ) {
                    $args['post_parent'] = $parent;
                }
            }
        }
        if ( $request->has_param( 'slug' ) ) {
            $slug = sanitize_title( (string) $request->get_param( 'slug' ) );
            if ( '' !== $slug ) {
                $args['post_name'] = $slug;
            }
        }
        $id = wp_insert_post( $args, true );
        if ( is_wp_error( $id ) ) return new WP_REST_Response( array( 'error' => $id->get_error_message() ), 422 );
        return rest_ensure_response( array( 'success' => true, 'page_id' => $id ) );
    }
    public function update_page( $request ) {
        $data = array( 'ID' => intval( $request['id'] ), 'post_type' => 'page' );
        if ( $request->get_param( 'title' ) ) $data['post_title']   = sanitize_text_field( $request->get_param( 'title' ) );
        if ( $request->get_param( 'content' ) ) $data['post_content'] = wp_kses_post( $request->get_param( 'content' ) );
        if ( $request->get_param( 'status' ) ) $data['post_status']  = sanitize_text_field( $request->get_param( 'status' ) );
        if ( $request->has_param( 'parent' ) ) {
            $data['post_parent'] = intval( $request->get_param( 'parent' ) );
        }
        if ( null !== $request->get_param( 'slug' ) && '' !== (string) $request->get_param( 'slug' ) ) {
            $data['post_name'] = sanitize_title( (string) $request->get_param( 'slug' ) );
        }
        $result = wp_update_post( $data, true );
        if ( is_wp_error( $result ) ) return new WP_REST_Response( array( 'error' => $result->get_error_message() ), 422 );
        return rest_ensure_response( array( 'success' => true ) );
    }
    public function delete_page( $request ) {
        wp_delete_post( $request['id'], true );
        return rest_ensure_response( array( 'success' => true ) );
    }
    public function duplicate_page( $request ) {
        $original = get_post( intval( $request['id'] ) );
        if ( ! $original ) return new WP_REST_Response( array( 'error' => 'Not found' ), 404 );
        $new_id = wp_insert_post( array(
            'post_title'   => $original->post_title . ' (Copy)',
            'post_content' => $original->post_content,
            'post_status'  => 'draft',
            'post_type'    => $original->post_type,
        ), true );
        if ( is_wp_error( $new_id ) ) return new WP_REST_Response( array( 'error' => $new_id->get_error_message() ), 422 );
        // Copy meta
        $meta = get_post_meta( $original->ID );
        foreach ( $meta as $key => $values ) {
            foreach ( $values as $value ) {
                add_post_meta( $new_id, $key, maybe_unserialize( $value ) );
            }
        }
        return rest_ensure_response( array( 'success' => true, 'new_page_id' => $new_id ) );
    }

    // Media
    public function get_media( $request ) {
        $per_page = intval( $request->get_param( 'per_page' ) ?: 20 );
        $page     = intval( $request->get_param( 'page' ) ?: 1 );
        $args     = array( 'post_type' => 'attachment', 'posts_per_page' => $per_page, 'paged' => $page, 'post_status' => 'inherit' );
        if ( $request->get_param( 'mime_type' ) ) $args['post_mime_type'] = sanitize_text_field( $request->get_param( 'mime_type' ) );
        $query = new WP_Query( $args );
        return rest_ensure_response( array_map( function( $p ) {
            return array( 'id' => $p->ID, 'title' => $p->post_title, 'url' => wp_get_attachment_url( $p->ID ), 'type' => $p->post_mime_type );
        }, $query->posts ) );
    }
    public function get_media_item( $request ) {
        $post = get_post( $request['id'] );
        if ( ! $post || 'attachment' !== $post->post_type ) return new WP_REST_Response( array( 'error' => 'Not found' ), 404 );
        return rest_ensure_response( array( 'id' => $post->ID, 'title' => $post->post_title, 'url' => wp_get_attachment_url( $post->ID ), 'type' => $post->post_mime_type ) );
    }
    public function delete_media( $request ) {
        wp_delete_attachment( $request['id'], true );
        return rest_ensure_response( array( 'success' => true ) );
    }
    public function update_media_meta( $request ) {
        $id   = intval( $request['id'] );
        $meta = $request->get_param( 'meta' );
        if ( ! is_array( $meta ) ) return new WP_REST_Response( array( 'error' => 'meta must be an object' ), 400 );
        foreach ( $meta as $key => $value ) {
            update_post_meta( $id, sanitize_key( $key ), $value );
        }
        return rest_ensure_response( array( 'success' => true ) );
    }

    public function upload_media( $request ) {
        $url = esc_url_raw( (string) $request->get_param( 'url' ) );
        if ( empty( $url ) || ! preg_match( '#^https?://#i', $url ) ) {
            return new WP_REST_Response( array( 'error' => 'A valid http(s) url is required' ), 400 );
        }
        $path_part = parse_url( $url, PHP_URL_PATH );
        $default_name = is_string( $path_part ) && $path_part !== '' ? basename( $path_part ) : '';
        $filename = sanitize_file_name( (string) ( $request->get_param( 'filename' ) ?: $default_name ) );
        if ( '' === $filename ) {
            $filename = 'upload.bin';
        }
        if ( ! function_exists( 'download_url' ) ) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }
        if ( ! function_exists( 'media_handle_sideload' ) ) {
            require_once ABSPATH . 'wp-admin/includes/media.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';
        }
        $tmp = download_url( $url );
        if ( is_wp_error( $tmp ) ) {
            return new WP_REST_Response( array( 'error' => $tmp->get_error_message() ), 422 );
        }
        $file_array = array(
            'name'     => $filename,
            'tmp_name' => $tmp,
        );
        $id = media_handle_sideload( $file_array, 0, null );
        if ( is_wp_error( $id ) ) {
            if ( is_string( $tmp ) && file_exists( $tmp ) ) {
                @unlink( $tmp );
            }
            return new WP_REST_Response( array( 'error' => $id->get_error_message() ), 422 );
        }
        $alt = sanitize_text_field( (string) $request->get_param( 'alt_text' ) );
        if ( '' !== $alt ) {
            update_post_meta( $id, '_wp_attachment_image_alt', $alt );
        }
        return rest_ensure_response( array(
            'success' => true,
            'id'      => $id,
            'url'     => wp_get_attachment_url( $id ),
        ) );
    }

    // Bulk operations
    public function bulk_update_posts( $request ) {
        $post_ids = $request->get_param( 'post_ids' );
        $updates  = $request->get_param( 'updates' );
        if ( ! is_array( $post_ids ) || ! is_array( $updates ) ) return new WP_REST_Response( array( 'error' => 'post_ids and updates are required' ), 400 );
        $results = array();
        foreach ( $post_ids as $id ) {
            $data = array( 'ID' => intval( $id ) );
            if ( isset( $updates['title'] ) ) $data['post_title']   = sanitize_text_field( $updates['title'] );
            if ( isset( $updates['content'] ) ) $data['post_content'] = wp_kses_post( $updates['content'] );
            if ( isset( $updates['status'] ) ) $data['post_status']  = sanitize_text_field( $updates['status'] );
            $r = wp_update_post( $data, true );
            $results[] = array( 'id' => $id, 'success' => ! is_wp_error( $r ) );
        }
        return rest_ensure_response( array( 'success' => true, 'results' => $results ) );
    }
    public function bulk_delete_posts( $request ) {
        $post_ids = $request->get_param( 'post_ids' );
        if ( ! is_array( $post_ids ) ) return new WP_REST_Response( array( 'error' => 'post_ids is required' ), 400 );
        $results = array();
        foreach ( $post_ids as $id ) {
            $result    = wp_delete_post( intval( $id ), true );
            $results[] = array( 'id' => $id, 'success' => (bool) $result );
        }
        return rest_ensure_response( array( 'success' => true, 'results' => $results ) );
    }

    // Users
    public function get_users() {
        $users = get_users( array( 'number' => 50 ) );
        return rest_ensure_response( array_map( function( $u ) {
            return array( 'id' => $u->ID, 'name' => $u->display_name, 'email' => $u->user_email, 'role' => $u->roles[0] ?? '' );
        }, $users ) );
    }
    public function get_user( $request ) {
        $user = get_userdata( intval( $request['id'] ) );
        if ( ! $user ) return new WP_REST_Response( array( 'error' => 'User not found' ), 404 );
        return rest_ensure_response( array( 'id' => $user->ID, 'name' => $user->display_name, 'email' => $user->user_email, 'login' => $user->user_login, 'roles' => $user->roles, 'registered' => $user->user_registered ) );
    }
    public function create_user( $request ) {
        $userdata = array(
            'user_login'   => sanitize_user( $request->get_param( 'username' ) ),
            'user_email'   => sanitize_email( $request->get_param( 'email' ) ),
            'display_name' => sanitize_text_field( $request->get_param( 'name' ) ?? '' ),
            'role'         => sanitize_text_field( $request->get_param( 'role' ) ?? 'subscriber' ),
        );
        if ( $request->get_param( 'password' ) ) {
            $userdata['user_pass'] = $request->get_param( 'password' );
        } else {
            $userdata['user_pass'] = wp_generate_password( 16, true );
        }
        $user_id = wp_insert_user( $userdata );
        if ( is_wp_error( $user_id ) ) return new WP_REST_Response( array( 'error' => $user_id->get_error_message() ), 422 );
        return rest_ensure_response( array( 'success' => true, 'user_id' => $user_id ) );
    }
    public function update_user( $request ) {
        $userdata = array( 'ID' => intval( $request['id'] ) );
        if ( $request->get_param( 'email' ) ) $userdata['user_email']   = sanitize_email( $request->get_param( 'email' ) );
        if ( $request->get_param( 'name' ) ) $userdata['display_name']  = sanitize_text_field( $request->get_param( 'name' ) );
        if ( $request->get_param( 'role' ) ) $userdata['role']           = sanitize_text_field( $request->get_param( 'role' ) );
        $result = wp_update_user( $userdata );
        if ( is_wp_error( $result ) ) return new WP_REST_Response( array( 'error' => $result->get_error_message() ), 422 );
        return rest_ensure_response( array( 'success' => true ) );
    }
    public function delete_user( $request ) {
        require_once ABSPATH . 'wp-admin/includes/user.php';
        $deleted = wp_delete_user( intval( $request['id'] ) );
        return rest_ensure_response( array( 'success' => (bool) $deleted ) );
    }

    // Categories
    public function get_categories() {
        $cats = get_categories( array( 'hide_empty' => false ) );
        return rest_ensure_response( array_map( function( $c ) {
            return array( 'id' => $c->term_id, 'name' => $c->name, 'slug' => $c->slug, 'count' => $c->count );
        }, $cats ));
    }
    public function create_category( $request ) {
        $result = wp_insert_term(
            sanitize_text_field( $request->get_param( 'name' ) ),
            'category',
            array( 'slug' => sanitize_title( $request->get_param( 'slug' ) ?? '' ) )
        );
        if ( is_wp_error( $result ) ) return new WP_REST_Response( array( 'error' => $result->get_error_message() ), 422 );
        return rest_ensure_response( array( 'success' => true, 'id' => $result['term_id'] ) );
    }

    // Tags
    public function get_tags() {
        $tags = get_tags( array( 'hide_empty' => false ) );
        return rest_ensure_response( array_map( function( $t ) {
            return array( 'id' => $t->term_id, 'name' => $t->name, 'slug' => $t->slug, 'count' => $t->count );
        }, $tags ));
    }

    // Comments
    public function get_comments( $request ) {
        $args = array( 'number' => intval( $request->get_param( 'per_page' ) ?: 20 ), 'offset' => ( ( intval( $request->get_param( 'page' ) ?: 1 ) - 1 ) * intval( $request->get_param( 'per_page' ) ?: 20 ) ) );
        if ( $request->get_param( 'post_id' ) ) $args['post_id'] = intval( $request->get_param( 'post_id' ) );
        if ( $request->get_param( 'status' ) ) $args['status']  = sanitize_text_field( $request->get_param( 'status' ) );
        $comments = get_comments( $args );
        return rest_ensure_response( array_map( function( $c ) {
            return array( 'id' => $c->comment_ID, 'author' => $c->comment_author, 'content' => $c->comment_content, 'date' => $c->comment_date, 'post_id' => $c->comment_post_ID, 'status' => $c->comment_approved );
        }, $comments ) );
    }
    public function get_comment( $request ) {
        $comment = get_comment( intval( $request['id'] ) );
        if ( ! $comment ) return new WP_REST_Response( array( 'error' => 'Comment not found' ), 404 );
        return rest_ensure_response( array( 'id' => $comment->comment_ID, 'author' => $comment->comment_author, 'email' => $comment->comment_author_email, 'content' => $comment->comment_content, 'date' => $comment->comment_date, 'post_id' => $comment->comment_post_ID, 'status' => $comment->comment_approved ) );
    }
    public function create_comment( $request ) {
        $id = wp_insert_comment( array(
            'comment_post_ID'      => intval( $request->get_param( 'post_id' ) ),
            'comment_content'      => wp_kses_post( $request->get_param( 'content' ) ),
            'comment_author'       => sanitize_text_field( $request->get_param( 'author' ) ?? 'Anonymous' ),
            'comment_author_email' => sanitize_email( $request->get_param( 'email' ) ?? '' ),
            'comment_approved'     => 1,
        ) );
        return rest_ensure_response( array( 'success' => true, 'comment_id' => $id ) );
    }
    public function approve_comment( $request ) {
        $result = wp_set_comment_status( intval( $request['id'] ), 'approve' );
        return rest_ensure_response( array( 'success' => (bool) $result ) );
    }
    public function spam_comment( $request ) {
        $result = wp_spam_comment( intval( $request['id'] ) );
        return rest_ensure_response( array( 'success' => (bool) $result ) );
    }
    public function delete_comment( $request ) {
        $result = wp_delete_comment( intval( $request['id'] ), true );
        return rest_ensure_response( array( 'success' => (bool) $result ) );
    }

    // Settings
    public function get_settings() {
        return rest_ensure_response( array(
            'blogname'            => get_option( 'blogname' ),
            'blogdescription'     => get_option( 'blogdescription' ),
            'siteurl'             => get_option( 'siteurl' ),
            'admin_email'         => get_option( 'admin_email' ),
            'timezone'            => get_option( 'timezone_string' ),
            'date_format'         => get_option( 'date_format' ),
            'time_format'         => get_option( 'time_format' ),
            'posts_per_page'      => get_option( 'posts_per_page' ),
            'users_can_register'  => get_option( 'users_can_register' ),
            'permalink_structure' => get_option( 'permalink_structure' ),
        ) );
    }
    public function update_settings( $request ) {
        $allowed = array( 'blogname', 'blogdescription', 'users_can_register', 'timezone_string', 'date_format', 'time_format', 'start_of_week' );
        $updated = array();
        foreach ( $allowed as $key ) {
            if ( null !== $request->get_param( $key ) ) {
                update_option( $key, sanitize_text_field( $request->get_param( $key ) ) );
                $updated[] = $key;
            }
        }
        return rest_ensure_response( array( 'success' => true, 'updated' => $updated ) );
    }

    // Themes
    public function get_themes() {
        $themes = wp_get_themes();
        $active = get_option( 'stylesheet' );
        return rest_ensure_response( array_map( function( $t ) use ( $active ) {
            return array( 'name' => $t->get( 'Name' ), 'version' => $t->get( 'Version' ), 'slug' => $t->get_stylesheet(), 'active' => ( $t->get_stylesheet() === $active ) );
        }, $themes ) );
    }
    public function activate_theme( $request ) {
        $slug = sanitize_text_field( $request['slug'] );
        $theme = wp_get_theme( $slug );
        if ( ! $theme->exists() ) return new WP_REST_Response( array( 'error' => 'Theme not found' ), 404 );
        switch_theme( $slug );
        return rest_ensure_response( array( 'success' => true, 'active_theme' => $slug ) );
    }
    public function update_theme( $request ) {
        require_once ABSPATH . 'wp-admin/includes/theme.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/misc.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        if ( ! class_exists( 'Theme_Upgrader' ) ) {
            return new WP_REST_Response( array( 'error' => 'Theme upgrader not available' ), 501 );
        }
        $slug     = sanitize_text_field( $request['slug'] );
        $upgrader = new Theme_Upgrader( new WP_Ajax_Upgrader_Skin() );
        $result   = $upgrader->upgrade( $slug );
        return rest_ensure_response( array( 'success' => true === $result ) );
    }

    // Plugins
    public function get_plugins() {
        if ( ! function_exists( 'get_plugins' ) ) require_once ABSPATH . 'wp-admin/includes/plugin.php';
        $plugins = get_plugins();
        return rest_ensure_response( array_map( function( $data, $path ) {
            return array( 'name' => $data['Name'], 'version' => $data['Version'], 'active' => is_plugin_active( $path ), 'path' => $path );
        }, $plugins, array_keys( $plugins ) ) );
    }
    public function install_plugin( $request ) {
        require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        require_once ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php';
        $slug       = sanitize_text_field( $request->get_param( 'slug' ) );
        $api        = plugins_api( 'plugin_information', array( 'slug' => $slug ) );
        if ( is_wp_error( $api ) ) return new WP_REST_Response( array( 'error' => $api->get_error_message() ), 422 );
        $upgrader   = new Plugin_Upgrader( new WP_Ajax_Upgrader_Skin() );
        $result     = $upgrader->install( $api->download_link );
        return rest_ensure_response( array( 'success' => ! is_wp_error( $result ) && $result !== false ) );
    }
    public function activate_plugin( $request ) {
        if ( ! function_exists( 'get_plugins' ) ) require_once ABSPATH . 'wp-admin/includes/plugin.php';
        $slug    = sanitize_text_field( $request['slug'] );
        $plugins = get_plugins();
        $file    = null;
        foreach ( array_keys( $plugins ) as $path ) {
            if ( 0 === strpos( $path, $slug . '/' ) || $path === $slug . '.php' ) {
                $file = $path;
                break;
            }
        }
        if ( ! $file ) return new WP_REST_Response( array( 'error' => 'Plugin not found' ), 404 );
        $result = activate_plugin( $file );
        if ( is_wp_error( $result ) ) return new WP_REST_Response( array( 'error' => $result->get_error_message() ), 422 );
        return rest_ensure_response( array( 'success' => true ) );
    }
    public function deactivate_plugin( $request ) {
        if ( ! function_exists( 'get_plugins' ) ) require_once ABSPATH . 'wp-admin/includes/plugin.php';
        $slug    = sanitize_text_field( $request['slug'] );
        $plugins = get_plugins();
        $file    = null;
        foreach ( array_keys( $plugins ) as $path ) {
            if ( 0 === strpos( $path, $slug . '/' ) || $path === $slug . '.php' ) {
                $file = $path;
                break;
            }
        }
        if ( ! $file ) return new WP_REST_Response( array( 'error' => 'Plugin not found' ), 404 );
        deactivate_plugins( $file );
        return rest_ensure_response( array( 'success' => true ) );
    }

    // Taxonomies
    public function get_taxonomies() {
        $raw = get_taxonomies( array(), 'objects' );
        $result = array();
        foreach ( $raw as $slug => $tax ) {
            $result[ $slug ] = array( 'name' => $tax->name, 'label' => $tax->label, 'public' => $tax->public, 'hierarchical' => $tax->hierarchical );
        }
        return rest_ensure_response( $result );
    }

    public function get_taxonomy_terms( $request ) {
        $taxonomy = sanitize_key( $request['taxonomy'] );
        if ( ! taxonomy_exists( $taxonomy ) ) return new WP_REST_Response( array( 'error' => 'Taxonomy not found' ), 404 );
        $args = array(
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
            'number'     => $request->get_param( 'per_page' ) ?: 100,
            'offset'     => ( ( $request->get_param( 'page' ) ?: 1 ) - 1 ) * ( $request->get_param( 'per_page' ) ?: 100 ),
        );
        if ( $request->get_param( 'search' ) ) $args['search'] = sanitize_text_field( $request->get_param( 'search' ) );
        $terms = get_terms( $args );
        if ( is_wp_error( $terms ) ) return new WP_REST_Response( array( 'error' => $terms->get_error_message() ), 500 );
        return rest_ensure_response( array_map( function( $t ) {
            return array( 'id' => $t->term_id, 'name' => $t->name, 'slug' => $t->slug, 'count' => $t->count, 'parent' => $t->parent );
        }, $terms ) );
    }

    // Terms CRUD
    public function create_term( $request ) {
        $name     = sanitize_text_field( $request->get_param( 'name' ) );
        $taxonomy = sanitize_key( $request->get_param( 'taxonomy' ) );
        $args     = array();
        if ( $request->get_param( 'description' ) ) $args['description'] = sanitize_textarea_field( $request->get_param( 'description' ) );
        if ( $request->get_param( 'parent' ) ) $args['parent'] = intval( $request->get_param( 'parent' ) );
        $result = wp_insert_term( $name, $taxonomy, $args );
        if ( is_wp_error( $result ) ) return new WP_REST_Response( array( 'error' => $result->get_error_message() ), 422 );
        return rest_ensure_response( array( 'success' => true, 'id' => $result['term_id'] ) );
    }

    public function get_term( $request ) {
        $term = get_term( $request['id'] );
        if ( ! $term || is_wp_error( $term ) ) return new WP_REST_Response( array( 'error' => 'Term not found' ), 404 );
        return rest_ensure_response( array( 'id' => $term->term_id, 'name' => $term->name, 'slug' => $term->slug, 'taxonomy' => $term->taxonomy, 'description' => $term->description, 'parent' => $term->parent, 'count' => $term->count ) );
    }

    public function update_term( $request ) {
        $term_id  = $request['id'];
        $taxonomy = sanitize_key( $request->get_param( 'taxonomy' ) );
        $args     = array();
        if ( $request->get_param( 'name' ) ) $args['name'] = sanitize_text_field( $request->get_param( 'name' ) );
        if ( $request->get_param( 'description' ) ) $args['description'] = sanitize_textarea_field( $request->get_param( 'description' ) );
        if ( $request->has_param( 'parent' ) ) $args['parent'] = intval( $request->get_param( 'parent' ) );
        $result = wp_update_term( $term_id, $taxonomy, $args );
        if ( is_wp_error( $result ) ) return new WP_REST_Response( array( 'error' => $result->get_error_message() ), 422 );
        return rest_ensure_response( array( 'success' => true ) );
    }

    public function delete_term( $request ) {
        $taxonomy = sanitize_key( $request->get_param( 'taxonomy' ) );
        $result   = wp_delete_term( $request['id'], $taxonomy );
        if ( is_wp_error( $result ) ) return new WP_REST_Response( array( 'error' => $result->get_error_message() ), 422 );
        return rest_ensure_response( array( 'success' => (bool) $result ) );
    }

    // Menus
    public function get_menus() {
        return rest_ensure_response( array_map( function( $m ) {
            return array( 'id' => $m->term_id, 'name' => $m->name, 'slug' => $m->slug, 'count' => $m->count );
        }, wp_get_nav_menus() ) );
    }

    public function get_menu( $request ) {
        $menu = wp_get_nav_menu_object( $request['id'] );
        if ( ! $menu ) return new WP_REST_Response( array( 'error' => 'Menu not found' ), 404 );
        $items = wp_get_nav_menu_items( $request['id'] );
        return rest_ensure_response( array(
            'id'    => $menu->term_id,
            'name'  => $menu->name,
            'slug'  => $menu->slug,
            'items' => $items ? array_map( function( $item ) {
                return array( 'id' => $item->ID, 'title' => $item->title, 'url' => $item->url, 'parent' => $item->menu_item_parent, 'order' => $item->menu_order );
            }, $items ) : array(),
        ) );
    }

    public function get_menu_locations() {
        $locations  = get_nav_menu_locations();
        $registered = get_registered_nav_menus();
        $result     = array();
        foreach ( $registered as $slug => $description ) {
            $menu_id = isset( $locations[ $slug ] ) ? $locations[ $slug ] : 0;
            $menu    = $menu_id ? wp_get_nav_menu_object( $menu_id ) : null;
            $result[] = array( 'slug' => $slug, 'description' => $description, 'menu_id' => $menu_id, 'menu_name' => $menu ? $menu->name : '' );
        }
        return rest_ensure_response( $result );
    }

    // Post Types
    public function get_post_types() {
        $types  = get_post_types( array( 'public' => true ), 'objects' );
        $result = array();
        foreach ( $types as $slug => $type ) {
            $result[ $slug ] = array(
                'name'         => $type->name,
                'label'        => $type->label,
                'singular'     => isset( $type->labels->singular_name ) ? $type->labels->singular_name : $type->label,
                'public'       => $type->public,
                'hierarchical' => $type->hierarchical,
                'has_archive'  => $type->has_archive,
                'rest_base'    => isset( $type->rest_base ) ? $type->rest_base : '',
            );
        }
        return rest_ensure_response( $result );
    }

    // Search
    public function search_content( $request ) {
        $q = sanitize_text_field( $request->get_param( 'query' ) );
        $query = new WP_Query( array( 's' => $q, 'posts_per_page' => 20, 'post_type' => array( 'post', 'page' ), 'post_status' => 'publish' ) );
        return rest_ensure_response( array_map( function( $p ) {
            return array( 'id' => $p->ID, 'type' => $p->post_type, 'title' => $p->post_title, 'slug' => $p->post_name, 'url' => get_permalink( $p->ID ) );
        }, $query->posts ) );
    }

    // Snapshots (via post revisions)
    public function list_snapshots() {
        $revisions = get_posts( array( 'post_type' => 'revision', 'posts_per_page' => 50, 'post_status' => 'inherit' ) );
        return rest_ensure_response( array_map( function( $r ) {
            return array( 'id' => $r->ID, 'parent_id' => $r->post_parent, 'date' => $r->post_modified, 'author' => get_the_author_meta( 'display_name', $r->post_author ) );
        }, $revisions ) );
    }

    public function restore_snapshot( $request ) {
        $snapshot_id = intval( $request->get_param( 'snapshot_id' ) );
        $revision    = get_post( $snapshot_id );
        if ( ! $revision || 'revision' !== $revision->post_type ) return new WP_REST_Response( array( 'error' => 'Snapshot not found' ), 404 );
        $restored = wp_restore_post_revision( $snapshot_id );
        return rest_ensure_response( array( 'success' => (bool) $restored ) );
    }

    // Usage & Plan Info
    public function get_usage() {
        $plan   = WPAIC_Plan::get_current();
        $config = WPAIC_Plan::get_config( $plan );

        return rest_ensure_response( array(
            'plugin_version' => WPAIC_VERSION,
            'plan'           => $plan,
            'sites_limit'    => $config['sites_limit'],
            'workflows_limit'=> $config['workflows_limit'],
            'posts_count'    => wp_count_posts( 'post' )->publish,
            'pages_count'    => wp_count_posts( 'page' )->publish,
            'users_count'    => count_users()['total_users'],
            'media_count'    => wp_count_posts( 'attachment' )->inherit,
        ) );
    }

    public function get_plan_info() {
        $plan            = WPAIC_Plan::get_current();
        $config          = WPAIC_Plan::get_config( $plan );
        $enabled_features = $config['features'];

        return rest_ensure_response( array(
            'plugin'         => 'WP AI Control',
            'version'        => WPAIC_VERSION,
            'plan'           => $plan,
            'plan_label'     => $config['label'],
            'sites_limit'    => $config['sites_limit'],
            'workflows_limit'=> $config['workflows_limit'],
            'tools_count'    => $config['tools_count'],
            'builders_count' => $config['builders_count'],
            'features'       => $enabled_features,
            'builders'       => WPAIC_Plan::allowed_builders( $plan ),
            'woocommerce'    => WPAIC_Plan::has_feature( 'woocommerce', $plan ) && class_exists( 'WooCommerce' ),
            'acf'            => WPAIC_Plan::has_feature( 'acf', $plan ) && ( function_exists( 'acf' ) || class_exists( 'ACF' ) ),
            'rank_math'      => WPAIC_Plan::has_feature( 'rank_math', $plan ) && class_exists( 'WPAIC_RankMath' ) && WPAIC_RankMath::is_active(),
            'addons'         => $config['addons'],
            'builder_woocommerce_enabled' => WPAIC_Plan::is_builder_woocommerce_enabled(),
        ) );
    }
}
