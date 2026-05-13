<?php
class WPAIC_WooCommerce {

    public function register_routes() {
        if ( ! WPAIC_Plan::has_feature( 'woocommerce' ) ) return;
        if ( ! class_exists( 'WooCommerce' ) ) return;

        // Products
        register_rest_route( WPAIC_NAMESPACE, '/wc/products', array( 'methods' => 'GET', 'callback' => array( $this, 'get_products' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ) );
        register_rest_route( WPAIC_NAMESPACE, '/wc/products', array( 'methods' => 'POST', 'callback' => array( $this, 'create_product' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ) );
        register_rest_route( WPAIC_NAMESPACE, '/wc/products/(?P<id>\d+)', array( 'methods' => 'GET', 'callback' => array( $this, 'get_product' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ) );
        register_rest_route( WPAIC_NAMESPACE, '/wc/products/(?P<id>\d+)', array( 'methods' => 'PUT', 'callback' => array( $this, 'update_product' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ) );
        register_rest_route( WPAIC_NAMESPACE, '/wc/products/(?P<id>\d+)', array( 'methods' => 'DELETE', 'callback' => array( $this, 'delete_product' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ) );
        register_rest_route( WPAIC_NAMESPACE, '/wc/products/(?P<id>\d+)/stock', array( 'methods' => 'PUT', 'callback' => array( $this, 'update_product_stock' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ) );
        register_rest_route( WPAIC_NAMESPACE, '/wc/products/(?P<id>\d+)/reviews', array( 'methods' => 'GET', 'callback' => array( $this, 'get_product_reviews' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ) );
        register_rest_route( WPAIC_NAMESPACE, '/wc/products/(?P<id>\d+)/reviews', array( 'methods' => 'POST', 'callback' => array( $this, 'update_product_review' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ) );
        register_rest_route( WPAIC_NAMESPACE, '/wc/product-categories', array( 'methods' => 'GET', 'callback' => array( $this, 'get_product_categories' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ) );
        // Orders
        register_rest_route( WPAIC_NAMESPACE, '/wc/orders', array( 'methods' => 'GET', 'callback' => array( $this, 'get_orders' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ) );
        register_rest_route( WPAIC_NAMESPACE, '/wc/orders', array( 'methods' => 'POST', 'callback' => array( $this, 'create_order' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ) );
        register_rest_route( WPAIC_NAMESPACE, '/wc/orders/(?P<id>\d+)', array( 'methods' => 'GET', 'callback' => array( $this, 'get_order' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ) );
        register_rest_route( WPAIC_NAMESPACE, '/wc/orders/(?P<id>\d+)', array( 'methods' => 'DELETE', 'callback' => array( $this, 'delete_order' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ) );
        register_rest_route( WPAIC_NAMESPACE, '/wc/orders/(?P<id>\d+)/status', array( 'methods' => 'PUT', 'callback' => array( $this, 'update_order_status' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ) );
        register_rest_route( WPAIC_NAMESPACE, '/wc/orders/(?P<id>\d+)/notes', array( 'methods' => 'GET', 'callback' => array( $this, 'get_order_notes' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ) );
        register_rest_route( WPAIC_NAMESPACE, '/wc/orders/(?P<id>\d+)/notes', array( 'methods' => 'POST', 'callback' => array( $this, 'add_order_note' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ) );
        register_rest_route( WPAIC_NAMESPACE, '/wc/orders/(?P<id>\d+)/notes/(?P<note_id>\d+)', array( 'methods' => 'DELETE', 'callback' => array( $this, 'delete_order_note' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ) );
        // Inventory & Settings
        register_rest_route( WPAIC_NAMESPACE, '/wc/inventory', array( 'methods' => 'GET', 'callback' => array( $this, 'get_inventory' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ) );
        register_rest_route( WPAIC_NAMESPACE, '/wc/stock-alerts', array( 'methods' => 'GET', 'callback' => array( $this, 'get_stock_alerts' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ) );
        register_rest_route( WPAIC_NAMESPACE, '/wc/settings', array( 'methods' => 'GET', 'callback' => array( $this, 'get_wc_settings' ), 'permission_callback' => array( 'WPAIC_Auth', 'check' ) ) );
    }

    // ── Products ──────────────────────────────────────────────────

    public function get_products( $request ) {
        $args = array(
            'limit'  => $request->get_param( 'per_page' ) ?: 20,
            'page'   => $request->get_param( 'page' ) ?: 1,
            'status' => 'publish',
        );
        if ( $request->get_param( 'search' ) ) $args['s'] = sanitize_text_field( $request->get_param( 'search' ) );
        if ( $request->get_param( 'category' ) ) $args['category'] = sanitize_text_field( $request->get_param( 'category' ) );
        $products = wc_get_products( $args );
        return rest_ensure_response( array_map( function( $p ) {
            return array( 'id' => $p->get_id(), 'name' => $p->get_name(), 'price' => $p->get_price(), 'stock' => $p->get_stock_quantity(), 'sku' => $p->get_sku(), 'status' => $p->get_status() );
        }, $products ) );
    }

    public function create_product( $request ) {
        $product = new WC_Product_Simple();
        $product->set_name( sanitize_text_field( $request->get_param( 'name' ) ) );
        if ( $request->get_param( 'description' ) ) $product->set_description( wp_kses_post( $request->get_param( 'description' ) ) );
        if ( $request->get_param( 'short_description' ) ) $product->set_short_description( wp_kses_post( $request->get_param( 'short_description' ) ) );
        if ( $request->get_param( 'price' ) ) $product->set_regular_price( sanitize_text_field( $request->get_param( 'price' ) ) );
        if ( $request->get_param( 'regular_price' ) ) $product->set_regular_price( sanitize_text_field( $request->get_param( 'regular_price' ) ) );
        if ( null !== $request->get_param( 'stock_quantity' ) ) $product->set_stock_quantity( intval( $request->get_param( 'stock_quantity' ) ) );
        $id = $product->save();
        return rest_ensure_response( array( 'success' => true, 'product_id' => $id ) );
    }

    public function get_product( $request ) {
        $product = wc_get_product( $request['id'] );
        if ( ! $product ) return new WP_REST_Response( array( 'error' => 'Product not found' ), 404 );
        return rest_ensure_response( array(
            'id'            => $product->get_id(),
            'name'          => $product->get_name(),
            'price'         => $product->get_price(),
            'regular_price' => $product->get_regular_price(),
            'stock'         => $product->get_stock_quantity(),
            'sku'           => $product->get_sku(),
            'description'   => $product->get_description(),
            'categories'    => wp_get_post_terms( $product->get_id(), 'product_cat', array( 'fields' => 'names' ) ),
            'status'        => $product->get_status(),
        ) );
    }

    public function update_product( $request ) {
        $product = wc_get_product( $request['id'] );
        if ( ! $product ) return new WP_REST_Response( array( 'error' => 'Product not found' ), 404 );
        if ( $request->get_param( 'name' ) ) $product->set_name( sanitize_text_field( $request->get_param( 'name' ) ) );
        if ( $request->get_param( 'price' ) ) $product->set_regular_price( sanitize_text_field( $request->get_param( 'price' ) ) );
        if ( $request->get_param( 'regular_price' ) ) $product->set_regular_price( sanitize_text_field( $request->get_param( 'regular_price' ) ) );
        if ( $request->has_param( 'stock_quantity' ) ) $product->set_stock_quantity( intval( $request->get_param( 'stock_quantity' ) ) );
        $product->save();
        return rest_ensure_response( array( 'success' => true ) );
    }

    public function delete_product( $request ) {
        $product = wc_get_product( $request['id'] );
        if ( ! $product ) return new WP_REST_Response( array( 'error' => 'Product not found' ), 404 );
        $product->delete( true );
        return rest_ensure_response( array( 'success' => true ) );
    }

    public function update_product_stock( $request ) {
        $product = wc_get_product( $request['id'] );
        if ( ! $product ) return new WP_REST_Response( array( 'error' => 'Product not found' ), 404 );
        $product->set_stock_quantity( intval( $request->get_param( 'stock_quantity' ) ) );
        $product->save();
        return rest_ensure_response( array( 'success' => true, 'stock_quantity' => $product->get_stock_quantity() ) );
    }

    public function get_product_reviews( $request ) {
        $reviews = get_comments( array( 'post_id' => $request['id'], 'type' => 'review' ) );
        return rest_ensure_response( array_map( function( $r ) {
            return array( 'id' => $r->comment_ID, 'author' => $r->comment_author, 'content' => $r->comment_content, 'date' => $r->comment_date, 'rating' => get_comment_meta( $r->comment_ID, 'rating', true ) );
        }, $reviews ) );
    }

    public function update_product_review( $request ) {
        $comment_data = array( 'comment_ID' => 0 );
        if ( $request->get_param( 'content' ) ) $comment_data['comment_content'] = wp_kses_post( $request->get_param( 'content' ) );
        $result = wp_update_comment( $comment_data );
        return rest_ensure_response( array( 'success' => (bool) $result ) );
    }

    public function get_product_categories() {
        $cats = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) );
        if ( is_wp_error( $cats ) ) return rest_ensure_response( array() );
        return rest_ensure_response( array_map( function( $c ) {
            return array( 'id' => $c->term_id, 'name' => $c->name, 'slug' => $c->slug, 'count' => $c->count );
        }, $cats ) );
    }

    // ── Orders ────────────────────────────────────────────────────

    public function get_orders( $request ) {
        $args = array(
            'limit'   => $request->get_param( 'per_page' ) ?: 20,
            'page'    => $request->get_param( 'page' ) ?: 1,
            'orderby' => 'date',
            'order'   => 'DESC',
        );
        if ( $request->get_param( 'status' ) ) $args['status'] = sanitize_text_field( $request->get_param( 'status' ) );
        $orders = wc_get_orders( $args );
        return rest_ensure_response( array_map( function( $o ) {
            $date_created = $o->get_date_created();
            return array(
                'id'       => $o->get_id(),
                'status'   => $o->get_status(),
                'total'    => $o->get_total(),
                'currency' => $o->get_currency(),
                'date'     => $date_created ? $date_created->date( 'Y-m-d H:i:s' ) : null,
            );
        }, $orders ) );
    }

    public function create_order( $request ) {
        $order = wc_create_order();
        if ( $request->get_param( 'billing' ) && is_array( $request->get_param( 'billing' ) ) ) {
            foreach ( $request->get_param( 'billing' ) as $key => $value ) {
                $method = 'set_billing_' . sanitize_key( $key );
                if ( method_exists( $order, $method ) ) $order->$method( sanitize_text_field( $value ) );
            }
        }
        if ( $request->get_param( 'shipping' ) && is_array( $request->get_param( 'shipping' ) ) ) {
            foreach ( $request->get_param( 'shipping' ) as $key => $value ) {
                $method = 'set_shipping_' . sanitize_key( $key );
                if ( method_exists( $order, $method ) ) $order->$method( sanitize_text_field( $value ) );
            }
        }
        $order->save();
        return rest_ensure_response( array( 'success' => true, 'order_id' => $order->get_id() ) );
    }

    public function get_order( $request ) {
        $order = wc_get_order( $request['id'] );
        if ( ! $order ) return new WP_REST_Response( array( 'error' => 'Order not found' ), 404 );
        $items = array();
        foreach ( $order->get_items() as $item ) {
            $items[] = array( 'name' => $item->get_name(), 'qty' => $item->get_quantity(), 'total' => $item->get_total() );
        }
        $date_created = $order->get_date_created();
        return rest_ensure_response( array(
            'id'       => $order->get_id(),
            'status'   => $order->get_status(),
            'total'    => $order->get_total(),
            'currency' => $order->get_currency(),
            'customer' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
            'email'    => $order->get_billing_email(),
            'date'     => $date_created ? $date_created->date( 'Y-m-d H:i:s' ) : null,
            'items'    => $items,
        ) );
    }

    public function delete_order( $request ) {
        $order = wc_get_order( $request['id'] );
        if ( ! $order ) return new WP_REST_Response( array( 'error' => 'Order not found' ), 404 );
        $order->delete( true );
        return rest_ensure_response( array( 'success' => true ) );
    }

    public function update_order_status( $request ) {
        $order = wc_get_order( $request['id'] );
        if ( ! $order ) return new WP_REST_Response( array( 'error' => 'Order not found' ), 404 );
        $order->update_status( sanitize_text_field( $request->get_param( 'status' ) ) );
        return rest_ensure_response( array( 'success' => true, 'status' => $order->get_status() ) );
    }

    public function get_order_notes( $request ) {
        $notes = wc_get_order_notes( array( 'order_id' => $request['id'] ) );
        return rest_ensure_response( array_map( function( $n ) {
            return array( 'id' => $n->id, 'note' => $n->content, 'date' => $n->date_created, 'customer_note' => $n->customer_note );
        }, $notes ) );
    }

    public function add_order_note( $request ) {
        $order = wc_get_order( $request['id'] );
        if ( ! $order ) return new WP_REST_Response( array( 'error' => 'Order not found' ), 404 );
        $note_id = $order->add_order_note(
            wp_kses_post( $request->get_param( 'note' ) ),
            (bool) $request->get_param( 'is_customer_note' )
        );
        return rest_ensure_response( array( 'success' => true, 'note_id' => $note_id ) );
    }

    public function delete_order_note( $request ) {
        $deleted = wc_delete_order_note( $request['note_id'] );
        return rest_ensure_response( array( 'success' => (bool) $deleted ) );
    }

    // ── Inventory ─────────────────────────────────────────────────

    public function get_inventory() {
        $in_stock     = wc_get_products( array( 'limit' => -1, 'stock_status' => 'instock', 'return' => 'ids' ) );
        $out_of_stock = wc_get_products( array( 'limit' => -1, 'stock_status' => 'outofstock', 'return' => 'ids' ) );
        $on_backorder = wc_get_products( array( 'limit' => -1, 'stock_status' => 'onbackorder', 'return' => 'ids' ) );
        return rest_ensure_response( array(
            'in_stock'     => count( $in_stock ),
            'out_of_stock' => count( $out_of_stock ),
            'on_backorder' => count( $on_backorder ),
            'total'        => count( $in_stock ) + count( $out_of_stock ) + count( $on_backorder ),
        ) );
    }

    public function get_stock_alerts() {
        $low_stock_amount = absint( get_option( 'woocommerce_notify_low_stock_amount', 2 ) );
        $products = wc_get_products( array( 'limit' => -1, 'stock_status' => 'instock', 'manage_stock' => true ) );
        $alerts = array();
        foreach ( $products as $product ) {
            $qty = $product->get_stock_quantity();
            if ( null !== $qty && $qty <= $low_stock_amount ) {
                $alerts[] = array( 'id' => $product->get_id(), 'name' => $product->get_name(), 'stock' => $qty, 'sku' => $product->get_sku() );
            }
        }
        $out = wc_get_products( array( 'limit' => -1, 'stock_status' => 'outofstock', 'return' => 'ids' ) );
        return rest_ensure_response( array( 'low_stock' => $alerts, 'out_of_stock_count' => count( $out ) ) );
    }

    public function get_wc_settings() {
        return rest_ensure_response( array(
            'currency'           => get_woocommerce_currency(),
            'currency_symbol'    => get_woocommerce_currency_symbol(),
            'tax_enabled'        => wc_tax_enabled(),
            'prices_include_tax' => wc_prices_include_tax(),
            'store_address'      => get_option( 'woocommerce_store_address' ),
            'store_city'         => get_option( 'woocommerce_store_city' ),
            'store_country'      => get_option( 'woocommerce_default_country' ),
            'weight_unit'        => get_option( 'woocommerce_weight_unit' ),
            'dimension_unit'     => get_option( 'woocommerce_dimension_unit' ),
        ) );
    }
}
