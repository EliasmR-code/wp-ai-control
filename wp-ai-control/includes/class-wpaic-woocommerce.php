<?php
/**
 * WooCommerce integration for WP AI Control.
 * 21 tools for products, orders, and inventory.
 *
 * @package WP_AI_Control
 * @subpackage WP_AI_Control/includes
 */

class WPAIC_WooCommerce {

	public static function register_routes() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		$namespace = WPAIC_REST_NAMESPACE;

		// Products (9 tools)
		register_rest_route( $namespace, '/wc/products', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'list_products' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/wc/products/(?P<id>\d+)', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'get_product' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/wc/products', array(
			'methods' => 'POST',
			'callback' => array( __CLASS__, 'create_product' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		register_rest_route( $namespace, '/wc/products/(?P<id>\d+)', array(
			'methods' => 'PUT',
			'callback' => array( __CLASS__, 'update_product' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		register_rest_route( $namespace, '/wc/products/(?P<id>\d+)', array(
			'methods' => 'DELETE',
			'callback' => array( __CLASS__, 'delete_product' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		register_rest_route( $namespace, '/wc/products/(?P<id>\d+)/stock', array(
			'methods' => 'PUT',
			'callback' => array( __CLASS__, 'update_product_stock' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		register_rest_route( $namespace, '/wc/product-categories', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'list_product_categories' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/wc/products/(?P<id>\d+)/reviews', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'get_product_reviews' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/wc/products/(?P<id>\d+)/reviews', array(
			'methods' => 'POST',
			'callback' => array( __CLASS__, 'update_product_review' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		// Orders (8 tools)
		register_rest_route( $namespace, '/wc/orders', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'list_orders' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/wc/orders/(?P<id>\d+)', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'get_order' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/wc/orders', array(
			'methods' => 'POST',
			'callback' => array( __CLASS__, 'create_order' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		register_rest_route( $namespace, '/wc/orders/(?P<id>\d+)/status', array(
			'methods' => 'PUT',
			'callback' => array( __CLASS__, 'update_order_status' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		register_rest_route( $namespace, '/wc/orders/(?P<id>\d+)', array(
			'methods' => 'DELETE',
			'callback' => array( __CLASS__, 'delete_order' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		register_rest_route( $namespace, '/wc/orders/(?P<id>\d+)/notes', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'get_order_notes' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/wc/orders/(?P<id>\d+)/notes', array(
			'methods' => 'POST',
			'callback' => array( __CLASS__, 'add_order_note' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		register_rest_route( $namespace, '/wc/orders/(?P<id>\d+)/notes/(?P<note_id>\d+)', array(
			'methods' => 'DELETE',
			'callback' => array( __CLASS__, 'delete_order_note' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));

		// Inventory (2 tools)
		register_rest_route( $namespace, '/wc/inventory', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'get_inventory_report' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/wc/stock-alerts', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'list_stock_alerts' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		// Store Settings (2 tools)
		register_rest_route( $namespace, '/wc/settings', array(
			'methods' => 'GET',
			'callback' => array( __CLASS__, 'get_store_settings' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_read' ),
		));

		register_rest_route( $namespace, '/wc/settings', array(
			'methods' => 'PUT',
			'callback' => array( __CLASS__, 'update_store_settings' ),
			'permission_callback' => array( 'WPAIC_API', 'api_key_permission_check_write' ),
		));
	}

	// ==================== PRODUCTS ====================

	public static function list_products( $request ) {
		$args = array(
			'status' => 'publish',
			'limit' => $request->get_param( 'per_page' ) ?: 20,
			'page' => $request->get_param( 'page' ) ?: 1,
		);
		if ( $request->has_param( 'search' ) ) $args['s'] = $request->get_param( 'search' );
		if ( $request->has_param( 'category' ) ) $args['category'] = $request->get_param( 'category' );

		$products = wc_get_products( $args );
		$data = array_map( function( $product ) {
			return array(
				'id' => $product->get_id(),
				'name' => $product->get_name(),
				'slug' => $product->get_slug(),
				'price' => $product->get_price(),
				'regular_price' => $product->get_regular_price(),
				'sale_price' => $product->get_sale_price(),
				'stock_quantity' => $product->get_stock_quantity(),
				'stock_status' => $product->get_stock_status(),
				'type' => $product->get_type(),
				'status' => $product->get_status(),
				'permalink' => get_permalink( $product->get_id() ),
			);
		}, $products );

		return new WP_REST_Response( array( 'success' => true, 'data' => $data ), 200 );
	}

	public static function get_product( $request ) {
		$product = wc_get_product( $request->get_param( 'id' ) );
		if ( ! $product ) return new WP_Error( 'wpaic_not_found', 'Product not found.', array( 'status' => 404 ) );

		return new WP_REST_Response( array(
			'success' => true,
			'data' => array(
				'id' => $product->get_id(), 'name' => $product->get_name(),
				'slug' => $product->get_slug(), 'price' => $product->get_price(),
				'description' => $product->get_description(), 'short_description' => $product->get_short_description(),
				'stock_quantity' => $product->get_stock_quantity(), 'stock_status' => $product->get_stock_status(),
				'images' => $product->get_gallery_image_ids(),
			),
		), 200 );
	}

	public static function create_product( $request ) {
		$product_data = array(
			'post_title' => $request->get_param( 'name' ),
			'post_content' => $request->get_param( 'description' ) ?: '',
			'post_excerpt' => $request->get_param( 'short_description' ) ?: '',
			'post_status' => 'publish',
			'post_type' => 'product',
		);

		$product_id = wp_insert_post( $product_data );
		if ( is_wp_error( $product_id ) ) return $product_id;

		$product = wc_get_product( $product_id );
		if ( $request->has_param( 'price' ) ) $product->set_price( $request->get_param( 'price' ) );
		if ( $request->has_param( 'regular_price' ) ) $product->set_regular_price( $request->get_param( 'regular_price' ) );
		if ( $request->has_param( 'stock_quantity' ) ) $product->set_stock_quantity( $request->get_param( 'stock_quantity' ) );
		$product->save();

		WPAIC_Audit::log( 'wc_product_created', $product_id, 'product', get_current_user_id(), array( 'name' => $request->get_param( 'name' ) ) );
		return new WP_REST_Response( array( 'success' => true, 'data' => array( 'id' => $product_id ) ), 201 );
	}

	public static function update_product( $request ) {
		$product = wc_get_product( $request->get_param( 'id' ) );
		if ( ! $product ) return new WP_Error( 'wpaic_not_found', 'Product not found.', array( 'status' => 404 ) );

		if ( $request->has_param( 'name' ) ) $product->set_name( $request->get_param( 'name' ) );
		if ( $request->has_param( 'price' ) ) $product->set_price( $request->get_param( 'price' ) );
		if ( $request->has_param( 'regular_price' ) ) $product->set_regular_price( $request->get_param( 'regular_price' ) );
		if ( $request->has_param( 'stock_quantity' ) ) $product->set_stock_quantity( $request->get_param( 'stock_quantity' ) );
		$product->save();

		WPAIC_Audit::log( 'wc_product_updated', $product->get_id(), 'product', get_current_user_id(), array() );
		return new WP_REST_Response( array( 'success' => true, 'message' => 'Product updated.' ), 200 );
	}

	public static function delete_product( $request ) {
		$product_id = $request->get_param( 'id' );
		$result = wp_delete_post( $product_id, true );
		if ( ! $result ) return new WP_Error( 'wpaic_delete_failed', 'Failed to delete product.', array( 'status' => 500 ) );

		WPAIC_Audit::log( 'wc_product_deleted', $product_id, 'product', get_current_user_id(), array() );
		return new WP_REST_Response( array( 'success' => true, 'message' => 'Product deleted.' ), 200 );
	}

	public static function update_product_stock( $request ) {
		$product = wc_get_product( $request->get_param( 'id' ) );
		if ( ! $product ) return new WP_Error( 'wpaic_not_found', 'Product not found.', array( 'status' => 404 ) );

		$product->set_stock_quantity( $request->get_param( 'stock_quantity' ) );
		$product->save();

		return new WP_REST_Response( array( 'success' => true, 'message' => 'Stock updated.' ), 200 );
	}

	public static function list_product_categories() {
		$terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) );
		return new WP_REST_Response( array(
			'success' => true,
			'data' => array_map( function( $term ) {
				return array( 'id' => $term->term_id, 'name' => $term->name, 'slug' => $term->slug );
			}, is_wp_error( $terms ) ? array() : $terms ),
		), 200 );
	}

	public static function get_product_reviews( $request ) {
		$comments = get_comments( array( 'post_id' => $request->get_param( 'id' ), 'type' => 'review' ) );
		return new WP_REST_Response( array( 'success' => true, 'data' => $comments ), 200 );
	}

	public static function update_product_review( $request ) {
		$review_id = $request->get_param( 'id' );
		$args = array( 'ID' => $review_id );
		if ( $request->has_param( 'content' ) ) $args['comment_content'] = $request->get_param( 'content' );
		if ( $request->has_param( 'rating' ) ) update_comment_meta( $review_id, 'rating', $request->get_param( 'rating' ) );

		$result = wp_update_comment( $args );
		return new WP_REST_Response( array( 'success' => ! is_wp_error( $result ), 'message' => is_wp_error( $result ) ? $result->get_error_message() : 'Review updated.' ), is_wp_error( $result ) ? 500 : 200 );
	}

	// ==================== ORDERS ====================

	public static function list_orders( $request ) {
		$args = array(
			'type' => 'shop_order',
			'status' => $request->get_param( 'status' ) ?: 'any',
			'limit' => $request->get_param( 'per_page' ) ?: 20,
			'page' => $request->get_param( 'page' ) ?: 1,
		);
		$orders = wc_get_orders( $args );

		$data = array_map( function( $order ) {
			return array(
				'id' => $order->get_id(),
				'status' => $order->get_status(),
				'total' => $order->get_total(),
				'currency' => $order->get_currency(),
				'date_created' => $order->get_date_created() ? $order->get_date_created()->date( 'Y-m-d H:i:s' ) : '',
				'billing' => $order->get_address( 'billing' ),
				'shipping' => $order->get_address( 'shipping' ),
			);
		}, $orders );

		return new WP_REST_Response( array( 'success' => true, 'data' => $data ), 200 );
	}

	public static function get_order( $request ) {
		$order = wc_get_order( $request->get_param( 'id' ) );
		if ( ! $order ) return new WP_Error( 'wpaic_not_found', 'Order not found.', array( 'status' => 404 ) );

		return new WP_REST_Response( array(
			'success' => true,
			'data' => array(
				'id' => $order->get_id(), 'status' => $order->get_status(),
				'total' => $order->get_total(), 'items' => $order->get_items(),
				'billing' => $order->get_address( 'billing' ), 'shipping' => $order->get_address( 'shipping' ),
			),
		), 200 );
	}

	public static function create_order( $request ) {
		$order = wc_create_order();
		if ( is_wp_error( $order ) ) return $order;

		if ( $request->has_param( 'billing' ) ) $order->set_address( $request->get_param( 'billing' ), 'billing' );
		if ( $request->has_param( 'shipping' ) ) $order->set_address( $request->get_param( 'shipping' ), 'shipping' );

		$order->calculate_totals();
		$order->save();

		WPAIC_Audit::log( 'wc_order_created', $order->get_id(), 'order', get_current_user_id(), array() );
		return new WP_REST_Response( array( 'success' => true, 'data' => array( 'id' => $order->get_id() ) ), 201 );
	}

	public static function update_order_status( $request ) {
		$order = wc_get_order( $request->get_param( 'id' ) );
		if ( ! $order ) return new WP_Error( 'wpaic_not_found', 'Order not found.', array( 'status' => 404 ) );

		$order->update_status( $request->get_param( 'status' ) );
		WPAIC_Audit::log( 'wc_order_status_updated', $order->get_id(), 'order', get_current_user_id(), array( 'status' => $request->get_param( 'status' ) ) );

		return new WP_REST_Response( array( 'success' => true, 'message' => 'Order status updated.' ), 200 );
	}

	public static function delete_order( $request ) {
		$order = wc_get_order( $request->get_param( 'id' ) );
		if ( ! $order ) return new WP_Error( 'wpaic_not_found', 'Order not found.', array( 'status' => 404 ) );

		$order->delete( true );
		WPAIC_Audit::log( 'wc_order_deleted', $request->get_param( 'id' ), 'order', get_current_user_id(), array() );
		return new WP_REST_Response( array( 'success' => true, 'message' => 'Order deleted.' ), 200 );
	}

	public static function get_order_notes( $request ) {
		$notes = wc_get_order_notes( $request->get_param( 'id' ) );
		return new WP_REST_Response( array( 'success' => true, 'data' => $notes ?: array() ), 200 );
	}

	public static function add_order_note( $request ) {
		$order_id = $request->get_param( 'id' );
		$result = wc_add_order_note( $order_id, $request->get_param( 'note' ), $request->get_param( 'is_customer_note' ) ?: false );
		return new WP_REST_Response( array( 'success' => (bool) $result, 'message' => $result ? 'Note added.' : 'Failed to add note.' ), $result ? 200 : 500 );
	}

	public static function delete_order_note( $request ) {
		$note_id = $request->get_param( 'note_id' );
		$result = wp_delete_comment( $note_id, true );
		return new WP_REST_Response( array( 'success' => (bool) $result, 'message' => $result ? 'Note deleted.' : 'Failed to delete note.' ), $result ? 200 : 500 );
	}

	// ==================== INVENTORY ====================

	public static function get_inventory_report() {
		$products = wc_get_products( array( 'status' => 'publish', 'limit' => -1 ) );
		$report = array(
			'total_products' => count( $products ),
			'in_stock' => 0, 'out_of_stock' => 0, 'low_stock' => 0,
		);

		foreach ( $products as $product ) {
			$stock = $product->get_stock_quantity();
			if ( $stock > 5 ) $report['in_stock']++;
			elseif ( $stock > 0 ) $report['low_stock']++;
			else $report['out_of_stock']++;
		}

		return new WP_REST_Response( array( 'success' => true, 'data' => $report ), 200 );
	}

	public static function list_stock_alerts() {
		$products = wc_get_products( array( 'status' => 'publish', 'limit' => -1 ) );
		$alerts = array();

		foreach ( $products as $product ) {
			$stock = $product->get_stock_quantity();
			if ( $stock <= 5 ) {
				$alerts[] = array(
					'id' => $product->get_id(), 'name' => $product->get_name(),
					'stock' => $stock, 'status' => $stock <= 0 ? 'out_of_stock' : 'low_stock',
				);
			}
		}

		return new WP_REST_Response( array( 'success' => true, 'data' => $alerts ), 200 );
	}

	// ==================== STORE SETTINGS ====================

	public static function get_store_settings() {
		return new WP_REST_Response( array(
			'success' => true,
			'data' => array(
				'store_address' => get_option( 'woocommerce_store_address' ),
				'store_city' => get_option( 'woocommerce_store_city' ),
				'currency' => get_option( 'woocommerce_currency' ),
				'currency_pos' => get_option( 'woocommerce_currency_pos' ),
				'price_thousand_sep' => get_option( 'woocommerce_price_thousand_sep' ),
				'price_decimal_sep' => get_option( 'woocommerce_price_decimal_sep' ),
			),
		), 200 );
	}

	public static function update_store_settings( $request ) {
		$settings = array( 'woocommerce_store_address', 'woocommerce_store_city', 'woocommerce_currency', 'woocommerce_currency_pos' );
		foreach ( $settings as $key ) {
			if ( $request->has_param( $key ) ) update_option( $key, $request->get_param( $key ) );
		}
		return new WP_REST_Response( array( 'success' => true, 'message' => 'Store settings updated.' ), 200 );
	}
}
