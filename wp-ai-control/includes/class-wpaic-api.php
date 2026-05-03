<?php
/**
 * REST API for WP AI Control - Simplified.
 */

class WPAIC_API {

	public function __construct() {}

	public function register_routes() {
		register_rest_route( WPAIC_REST_NAMESPACE, '/auth/generate-key', array(
			'methods' => 'POST',
			'callback' => array( $this, 'generate_api_key' ),
			'permission_callback' => array( $this, 'admin_permission_check' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/site-info', array(
			'methods' => 'GET',
			'callback' => array( $this, 'get_site_info' ),
			'permission_callback' => '__return_true',
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/posts', array(
			'methods' => 'GET',
			'callback' => array( $this, 'get_posts' ),
			'permission_callback' => '__return_true',
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/posts', array(
			'methods' => 'POST',
			'callback' => array( $this, 'create_post' ),
			'permission_callback' => array( $this, 'check_write_permission' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/pages', array(
			'methods' => 'GET',
			'callback' => array( $this, 'get_pages' ),
			'permission_callback' => '__return_true',
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/media', array(
			'methods' => 'GET',
			'callback' => array( $this, 'get_media' ),
			'permission_callback' => '__return_true',
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/media', array(
			'methods' => 'POST',
			'callback' => array( $this, 'upload_media' ),
			'permission_callback' => array( $this, 'check_write_permission' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/users', array(
			'methods' => 'GET',
			'callback' => array( $this, 'get_users' ),
			'permission_callback' => array( $this, 'admin_permission_check' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/categories', array(
			'methods' => 'GET',
			'callback' => array( $this, 'get_categories' ),
			'permission_callback' => '__return_true',
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/tags', array(
			'methods' => 'GET',
			'callback' => array( $this, 'get_tags' ),
			'permission_callback' => '__return_true',
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/comments', array(
			'methods' => 'GET',
			'callback' => array( $this, 'get_comments' ),
			'permission_callback' => '__return_true',
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/settings', array(
			'methods' => 'GET',
			'callback' => array( $this, 'get_settings' ),
			'permission_callback' => array( $this, 'admin_permission_check' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/themes', array(
			'methods' => 'GET',
			'callback' => array( $this, 'get_themes' ),
			'permission_callback' => array( $this, 'admin_permission_check' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/plugins', array(
			'methods' => 'GET',
			'callback' => array( $this, 'get_plugins' ),
			'permission_callback' => array( $this, 'admin_permission_check' ),
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/menus', array(
			'methods' => 'GET',
			'callback' => array( $this, 'get_menus' ),
			'permission_callback' => '__return_true',
		));

		register_rest_route( WPAIC_REST_NAMESPACE, '/taxonomies', array(
			'methods' => 'GET',
			'callback' => array( $this, 'get_taxonomies' ),
			'permission_callback' => '__return_true',
		));
	}

	public function admin_permission_check() {
		return current_user_can( 'manage_options' );
	}

	public function check_write_permission() {
		$headers = getallheaders();
		$auth = isset( $headers['Authorization'] ) ? $headers['Authorization'] : '';
		if ( empty( $auth ) ) {
			return current_user_can( 'edit_posts' );
		}
		$key = str_replace( 'Bearer ', '', $auth );
		$saved_key = get_option( 'wpaic_api_key', '' );
		return $key === $saved_key;
	}

	public function generate_api_key( $request ) {
		global $wpdb;
		$table = $wpdb->prefix . 'wpaic_api_keys';
		$key = 'wpaic_' . wp_generate_password( 32, false );
		$wpdb->insert( $table, array(
			'api_key' => $key,
			'key_prefix' => 'wpaic_live',
			'user_id' => get_current_user_id(),
			'name' => 'API Key',
			'permissions' => json_encode( array( 'read', 'write' ) ),
		));
		return rest_ensure_response( array( 'success' => true, 'api_key' => $key ) );
	}

	public function get_site_info() {
		return rest_ensure_response( array(
			'name' => get_bloginfo( 'name' ),
			'description' => get_bloginfo( 'description' ),
			'url' => get_site_url(),
			'wp_version' => get_bloginfo( 'version' ),
			'plugins' => count( get_option( 'active_plugins' ) ),
			'theme' => wp_get_theme()->get( 'Name' ),
		));
	}

	public function get_posts( $request ) {
		$per_page = $request->get_param( 'per_page' ) ?: 10;
		$page = $request->get_param( 'page' ) ?: 1;
		$args = array(
			'posts_per_page' => $per_page,
			'paged' => $page,
			'post_status' => 'publish',
		);
		$query = new WP_Query( $args );
		$posts = array();
		foreach ( $query->posts as $post ) {
			$posts[] = array(
				'id' => $post->ID,
				'title' => $post->post_title,
				'content' => $post->post_content,
				'excerpt' => $post->post_excerpt,
				'status' => $post->post_status,
				'date' => $post->post_date,
				'author' => $post->post_author,
			);
		}
		return rest_ensure_response( $posts );
	}

	public function create_post( $request ) {
		$title = $request->get_param( 'title' );
		$content = $request->get_param( 'content' );
		$status = $request->get_param( 'status' ) ?: 'draft';

		$post_id = wp_insert_post( array(
			'post_title' => $title,
			'post_content' => $content,
			'post_status' => $status,
			'post_type' => 'post',
		));

		if ( is_wp_error( $post_id ) ) {
			return rest_ensure_response( array( 'error' => $post_id->get_error_message() ), 500 );
		}

		return rest_ensure_response( array( 'success' => true, 'post_id' => $post_id ) );
	}

	public function get_pages( $request ) {
		$args = array( 'post_type' => 'page', 'posts_per_page' => 20 );
		$query = new WP_Query( $args );
		$pages = array();
		foreach ( $query->posts as $post ) {
			$pages[] = array( 'id' => $post->ID, 'title' => $post->post_title, 'status' => $post->post_status );
		}
		return rest_ensure_response( $pages );
	}

	public function get_media( $request ) {
		$args = array( 'post_type' => 'attachment', 'posts_per_page' => 20 );
		$query = new WP_Query( $args );
		$media = array();
		foreach ( $query->posts as $post ) {
			$media[] = array( 'id' => $post->ID, 'url' => wp_get_attachment_url( $post->ID ), 'title' => $post->post_title );
		}
		return rest_ensure_response( $media );
	}

	public function upload_media( $request ) {
		$url = $request->get_param( 'url' );
		if ( empty( $url ) ) {
			return rest_ensure_response( array( 'error' => 'URL required' ), 400 );
		}
		$media_id = media_sideload_attachment( $url, 0 );
		if ( is_wp_error( $media_id ) ) {
			return rest_ensure_response( array( 'error' => $media_id->get_error_message() ), 500 );
		}
		return rest_ensure_response( array( 'success' => true, 'media_id' => $media_id ) );
	}

	public function get_users( $request ) {
		$users = get_users();
		$result = array();
		foreach ( $users as $user ) {
			$result[] = array( 'id' => $user->ID, 'name' => $user->display_name, 'email' => $user->user_email, 'role' => $user->roles[0] );
		}
		return rest_ensure_response( $result );
	}

	public function get_categories() {
		$categories = get_categories( array( 'hide_empty' => 0 ) );
		$result = array();
		foreach ( $categories as $cat ) {
			$result[] = array( 'id' => $cat->term_id, 'name' => $cat->name, 'slug' => $cat->slug );
		}
		return rest_ensure_response( $result );
	}

	public function get_tags() {
		$tags = get_tags( array( 'hide_empty' => 0 ) );
		$result = array();
		foreach ( $tags as $tag ) {
			$result[] = array( 'id' => $tag->term_id, 'name' => $tag->name, 'slug' => $tag->slug );
		}
		return rest_ensure_response( $result );
	}

	public function get_comments( $request ) {
		$post_id = $request->get_param( 'post_id' );
		$args = $post_id ? array( 'post_id' => $post_id ) : array();
		$comments = get_comments( $args );
		$result = array();
		foreach ( $comments as $comment ) {
			$result[] = array( 'id' => $comment->comment_ID, 'author' => $comment->comment_author, 'content' => $comment->comment_content, 'date' => $comment->comment_date );
		}
		return rest_ensure_response( $result );
	}

	public function get_settings() {
		return rest_ensure_response( array(
			'blogname' => get_option( 'blogname' ),
			'blogdescription' => get_option( 'blogdescription' ),
			'permalink' => get_option( 'permalink_structure' ),
			'users_can_register' => get_option( 'users_can_register' ),
		));
	}

	public function get_themes() {
		$themes = wp_get_themes();
		$result = array();
		foreach ( $themes as $theme ) {
			$result[] = array( 'name' => $theme->get( 'Name' ), 'version' => $theme->get( 'Version' ) );
		}
		return rest_ensure_response( $result );
	}

	public function get_plugins() {
		if ( ! function_exists( 'get_plugins' ) ) {
			include ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$plugins = get_plugins();
		$result = array();
		foreach ( $plugins as $path => $data ) {
			$result[] = array( 'name' => $data['Name'], 'version' => $data['Version'], 'active' => is_plugin_active( $path ) );
		}
		return rest_ensure_response( $result );
	}

	public function get_menus() {
		$menus = wp_get_nav_menus();
		$result = array();
		foreach ( $menus as $menu ) {
			$result[] = array( 'id' => $menu->term_id, 'name' => $menu->name );
		}
		return rest_ensure_response( $result );
	}

	public function get_taxonomies() {
		$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
		$result = array();
		foreach ( $taxonomies as $tax ) {
			$result[] = array( 'name' => $tax->name, 'label' => $tax->label );
		}
		return rest_ensure_response( $result );
	}
}