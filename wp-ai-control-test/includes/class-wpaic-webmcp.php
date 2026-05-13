<?php
class WPAIC_WebMCP {
    public function register_routes() {
        register_rest_route( WPAIC_NAMESPACE, '/mcp', array(
            'methods'             => array( 'GET', 'POST' ),
            'callback'            => array( $this, 'mcp_handler' ),
            'permission_callback' => '__return_true',
        ));
    }

    public function mcp_handler( $request ) {
        $body = $request->get_params();
        $method = $body['method'] ?? 'ping';
        $params = $body['params'] ?? array();

        switch ( $method ) {
            case 'initialize':
            case 'ping':
                return rest_ensure_response( array(
                    'jsonrpc' => '2.0',
                    'result'  => array(
                        'protocolVersion' => '2024-11-05',
                        'serverInfo'      => array(
                            'name'    => 'wp-ai-control',
                            'version' => WPAIC_VERSION,
                        ),
                    ),
                ));

            case 'tools/list':
                $tools = array(
                    array( 'name' => 'wp_get_site_info', 'description' => 'Get WordPress site information' ),
                    array( 'name' => 'wp_get_posts', 'description' => 'Get posts list' ),
                    array( 'name' => 'wp_get_pages', 'description' => 'Get pages list' ),
                    array( 'name' => 'wp_get_users', 'description' => 'Get users list' ),
                    array( 'name' => 'wp_get_media', 'description' => 'Get media files' ),
                    array( 'name' => 'wp_get_categories', 'description' => 'Get categories list' ),
                    array( 'name' => 'wp_get_tags', 'description' => 'Get tags list' ),
                    array( 'name' => 'wp_create_post', 'description' => 'Create a new post' ),
                    array( 'name' => 'wp_create_page', 'description' => 'Create a new page' ),
                );
                return rest_ensure_response( array( 'jsonrpc' => '2.0', 'result' => array( 'tools' => $tools ) ) );

            case 'tools/call':
                return $this->handle_tool_call( $params );

            default:
                return rest_ensure_response( array(
                    'jsonrpc' => '2.0',
                    'error'   => array( 'code' => -32601, 'message' => 'Method not found: ' . esc_html( $method ) ),
                ));
        }
    }

    private function handle_tool_call( $params ) {
        $name = $params['name'] ?? '';
        $args = $params['arguments'] ?? array();

        switch ( $name ) {
            case 'wp_get_site_info':
                $result = array(
                    'name' => get_bloginfo( 'name' ),
                    'url'  => get_site_url(),
                );
                break;
            case 'wp_get_posts':
                $query = new WP_Query( array( 'posts_per_page' => 10, 'post_status' => 'publish' ) );
                $result = array_map( function( $p ) {
                    return array( 'id' => $p->ID, 'title' => $p->post_title );
                }, $query->posts );
                break;
            case 'wp_get_pages':
                $query = new WP_Query( array( 'post_type' => 'page', 'posts_per_page' => 20 ) );
                $result = array_map( function( $p ) {
                    return array( 'id' => $p->ID, 'title' => $p->post_title );
                }, $query->posts );
                break;
            case 'wp_get_users':
                $users = get_users();
                $result = array_map( function( $u ) {
                    return array( 'id' => $u->ID, 'name' => $u->display_name );
                }, $users );
                break;
            case 'wp_create_post':
                $id = wp_insert_post( array(
                    'post_title'   => $args['title'] ?? 'Untitled',
                    'post_content' => $args['content'] ?? '',
                    'post_status'  => $args['status'] ?? 'draft',
                ));
                $result = array( 'post_id' => $id, 'success' => true );
                break;
            case 'wp_create_page':
                $id = wp_insert_post( array(
                    'post_type'    => 'page',
                    'post_title'   => $args['title'] ?? 'Untitled',
                    'post_content' => $args['content'] ?? '',
                    'post_status'  => $args['status'] ?? 'draft',
                ));
                $result = array( 'page_id' => $id, 'success' => true );
                break;
            default:
                return rest_ensure_response( array(
                    'jsonrpc' => '2.0',
                    'error'   => array( 'code' => -32601, 'message' => 'Tool not found: ' . esc_html( $name ) ),
                ));
        }

        return rest_ensure_response( array(
            'jsonrpc' => '2.0',
            'result'  => array(
                'content' => array( array( 'type' => 'text', 'text' => json_encode( $result ) ) ),
            ),
        ));
    }
}
