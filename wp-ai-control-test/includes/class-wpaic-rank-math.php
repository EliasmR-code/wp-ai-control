<?php
/**
 * Rank Math SEO integration (post meta, opciones globales, redirecciones).
 */
class WPAIC_RankMath {

    const OPT_GENERAL = 'rank-math-options-general';
    const OPT_TITLES  = 'rank-math-options-titles';
    const OPT_SITEMAP = 'rank-math-options-sitemap';

    /** @var array<string,string> */
    private static $allowed_keys = array(
        'rank_math_title'                => 'text',
        'rank_math_description'          => 'textarea',
        'rank_math_focus_keyword'        => 'text',
        'rank_math_canonical_url'        => 'url',
        'rank_math_primary_category'     => 'int',
        'rank_math_primary_product_cat'  => 'int',
        'rank_math_facebook_title'       => 'text',
        'rank_math_facebook_description' => 'textarea',
        'rank_math_facebook_image'       => 'url',
        'rank_math_twitter_title'        => 'text',
        'rank_math_twitter_description'  => 'textarea',
        'rank_math_twitter_image'        => 'url',
        'rank_math_twitter_use_facebook' => 'bool',
        'rank_math_rich_snippet'         => 'text',
        'rank_math_snippet_name'         => 'text',
        'rank_math_robots'               => 'robots',
    );

    public function register_routes() {
        if ( ! WPAIC_Plan::has_feature( 'rank_math' ) ) {
            return;
        }

        register_rest_route(
            WPAIC_NAMESPACE,
            '/rank-math/status',
            array(
                'methods'             => 'GET',
                'callback'            => array( $this, 'get_status' ),
                'permission_callback' => array( 'WPAIC_Auth', 'check' ),
            )
        );

        register_rest_route(
            WPAIC_NAMESPACE,
            '/rank-math/settings',
            array(
                array(
                    'methods'             => 'GET',
                    'callback'            => array( $this, 'get_settings' ),
                    'permission_callback' => array( 'WPAIC_Auth', 'check' ),
                ),
                array(
                    'methods'             => 'PUT',
                    'callback'            => array( $this, 'update_settings' ),
                    'permission_callback' => array( 'WPAIC_Auth', 'check' ),
                ),
            )
        );

        register_rest_route(
            WPAIC_NAMESPACE,
            '/rank-math/posts/(?P<id>\d+)',
            array(
                array(
                    'methods'             => 'GET',
                    'callback'            => array( $this, 'get_post_seo' ),
                    'permission_callback' => array( 'WPAIC_Auth', 'check' ),
                ),
                array(
                    'methods'             => 'PUT',
                    'callback'            => array( $this, 'update_post_seo' ),
                    'permission_callback' => array( 'WPAIC_Auth', 'check' ),
                ),
            )
        );

        register_rest_route(
            WPAIC_NAMESPACE,
            '/rank-math/redirections',
            array(
                array(
                    'methods'             => 'GET',
                    'callback'            => array( $this, 'list_redirections' ),
                    'permission_callback' => array( 'WPAIC_Auth', 'check' ),
                ),
                array(
                    'methods'             => 'POST',
                    'callback'            => array( $this, 'create_redirection' ),
                    'permission_callback' => array( 'WPAIC_Auth', 'check' ),
                ),
            )
        );

        register_rest_route(
            WPAIC_NAMESPACE,
            '/rank-math/redirections/(?P<id>\d+)',
            array(
                array(
                    'methods'             => 'GET',
                    'callback'            => array( $this, 'get_redirection' ),
                    'permission_callback' => array( 'WPAIC_Auth', 'check' ),
                ),
                array(
                    'methods'             => 'PUT',
                    'callback'            => array( $this, 'update_redirection' ),
                    'permission_callback' => array( 'WPAIC_Auth', 'check' ),
                ),
                array(
                    'methods'             => 'DELETE',
                    'callback'            => array( $this, 'delete_redirection' ),
                    'permission_callback' => array( 'WPAIC_Auth', 'check' ),
                ),
            )
        );
    }

    public static function is_active() {
        return defined( 'RANK_MATH_VERSION' ) || class_exists( '\RankMath' );
    }

    public function get_status() {
        return rest_ensure_response(
            array(
                'active'            => self::is_active(),
                'version'           => defined( 'RANK_MATH_VERSION' ) ? RANK_MATH_VERSION : null,
                'redirections_api'  => class_exists( '\RankMath\Redirections\DB' ),
            )
        );
    }

    /**
     * GET ?group=general|sitemap|titles|all
     */
    public function get_settings( $request ) {
        if ( ! self::is_active() ) {
            return self::inactive_response();
        }

        $group = sanitize_key( (string) $request->get_param( 'group' ) );
        if ( empty( $group ) ) {
            $group = 'general';
        }

        if ( 'all' === $group ) {
            return rest_ensure_response(
                array(
                    'general' => get_option( self::OPT_GENERAL, array() ),
                    'sitemap' => get_option( self::OPT_SITEMAP, array() ),
                    'titles'  => get_option( self::OPT_TITLES, array() ),
                )
            );
        }

        $map = array(
            'general' => self::OPT_GENERAL,
            'sitemap' => self::OPT_SITEMAP,
            'titles'  => self::OPT_TITLES,
        );

        if ( ! isset( $map[ $group ] ) ) {
            return new WP_REST_Response(
                array( 'error' => 'invalid_group', 'message' => 'Use group=general, sitemap, titles or all.' ),
                400
            );
        }

        return rest_ensure_response(
            array(
                'group' => $group,
                'data'  => get_option( $map[ $group ], array() ),
            )
        );
    }

    /**
     * Body: { "group": "general|sitemap|titles", "patch": { "key": "value", ... } }
     */
    public function update_settings( $request ) {
        if ( ! self::is_active() ) {
            return self::inactive_response();
        }

        $group = sanitize_key( (string) $request->get_param( 'group' ) );
        $patch = $request->get_param( 'patch' );

        if ( ! in_array( $group, array( 'general', 'sitemap', 'titles' ), true ) ) {
            return new WP_REST_Response(
                array( 'error' => 'invalid_group', 'message' => 'group must be general, sitemap or titles.' ),
                400
            );
        }

        if ( ! is_array( $patch ) ) {
            return new WP_REST_Response( array( 'error' => 'patch must be an object' ), 400 );
        }

        $option = $group === 'general' ? self::OPT_GENERAL : ( $group === 'sitemap' ? self::OPT_SITEMAP : self::OPT_TITLES );
        $current = get_option( $option, array() );
        if ( ! is_array( $current ) ) {
            $current = array();
        }

        $allowed = self::settings_patch_whitelist( $group );
        $updated = array();

        foreach ( $patch as $key => $value ) {
            $key = sanitize_key( (string) $key );
            if ( '' === $key || ! isset( $allowed[ $key ] ) ) {
                continue;
            }
            $san = self::sanitize_settings_value( $allowed[ $key ], $value );
            if ( '__skip__' === $san ) {
                continue;
            }
            $current[ $key ] = $san;
            $updated[]       = $key;
        }

        if ( empty( $updated ) ) {
            return new WP_REST_Response(
                array( 'error' => 'no_valid_keys', 'message' => 'No patch keys matched the whitelist for this group.' ),
                400
            );
        }

        update_option( $option, $current );
        self::clear_rm_cache();

        return rest_ensure_response(
            array(
                'success' => true,
                'group'   => $group,
                'updated' => $updated,
            )
        );
    }

    public function get_post_seo( $request ) {
        if ( ! self::is_active() ) {
            return self::inactive_response();
        }

        $post_id = intval( $request['id'] );
        $post    = get_post( $post_id );
        if ( ! $post ) {
            return new WP_REST_Response( array( 'error' => 'Post not found' ), 404 );
        }

        $seo = array();
        foreach ( array_keys( self::$allowed_keys ) as $key ) {
            $seo[ $key ] = self::read_meta( $post_id, $key );
        }

        return rest_ensure_response(
            array(
                'post_id'    => $post_id,
                'post_type'  => $post->post_type,
                'post_title' => $post->post_title,
                'seo'        => $seo,
            )
        );
    }

    public function update_post_seo( $request ) {
        if ( ! self::is_active() ) {
            return self::inactive_response();
        }

        $post_id = intval( $request['id'] );
        $post    = get_post( $post_id );
        if ( ! $post ) {
            return new WP_REST_Response( array( 'error' => 'Post not found' ), 404 );
        }

        $updated = array();
        foreach ( self::$allowed_keys as $meta_key => $type ) {
            if ( ! $request->has_param( $meta_key ) ) {
                continue;
            }
            $raw = $request->get_param( $meta_key );
            $val = self::sanitize_meta_value( $type, $raw );
            if ( null === $val && 'rank_math_robots' !== $meta_key ) {
                continue;
            }
            if ( 'rank_math_robots' === $meta_key && ( null === $val || array() === $val ) ) {
                delete_post_meta( $post_id, $meta_key );
                $updated[] = $meta_key;
                continue;
            }
            update_post_meta( $post_id, $meta_key, $val );
            $updated[] = $meta_key;
        }

        if ( empty( $updated ) ) {
            return new WP_REST_Response(
                array(
                    'error'   => 'no_fields',
                    'message' => 'Include at least one allowed rank_math_* field in the JSON body.',
                ),
                400
            );
        }

        self::maybe_refresh_score( $post_id );

        return rest_ensure_response(
            array(
                'success' => true,
                'post_id' => $post_id,
                'updated' => $updated,
                'seo'     => array_intersect_key(
                    self::collect_seo_for_keys( $post_id ),
                    array_flip( $updated )
                ),
            )
        );
    }

    public function list_redirections( $request ) {
        if ( ! self::is_active() ) {
            return self::inactive_response();
        }
        if ( ! class_exists( '\RankMath\Redirections\DB' ) ) {
            return self::redirections_unavailable();
        }

        $args = array(
            'orderby' => sanitize_key( (string) $request->get_param( 'orderby' ) ?: 'id' ),
            'order'   => strtoupper( sanitize_key( (string) $request->get_param( 'order' ) ?: 'DESC' ) ) === 'ASC' ? 'ASC' : 'DESC',
            'limit'   => min( 100, max( 1, intval( $request->get_param( 'per_page' ) ?: 20 ) ) ),
            'paged'   => max( 1, intval( $request->get_param( 'page' ) ?: 1 ) ),
            'search'  => sanitize_text_field( (string) $request->get_param( 'search' ) ),
            'status'  => sanitize_key( (string) $request->get_param( 'status' ) ?: 'any' ),
        );

        $result = \RankMath\Redirections\DB::get_redirections( $args );
        foreach ( $result['redirections'] as &$row ) {
            if ( isset( $row['sources'] ) ) {
                $row['sources'] = maybe_unserialize( $row['sources'] );
            }
        }
        unset( $row );

        return rest_ensure_response(
            array(
                'redirections' => $result['redirections'],
                'total'        => (int) $result['count'],
                'page'         => $args['paged'],
                'per_page'     => $args['limit'],
            )
        );
    }

    public function get_redirection( $request ) {
        if ( ! self::is_active() ) {
            return self::inactive_response();
        }
        if ( ! class_exists( '\RankMath\Redirections\DB' ) ) {
            return self::redirections_unavailable();
        }

        $id = intval( $request['id'] );
        $row = \RankMath\Redirections\DB::get_redirection_by_id( $id, 'all' );
        if ( ! $row ) {
            return new WP_REST_Response( array( 'error' => 'Redirection not found' ), 404 );
        }
        if ( isset( $row['sources'] ) && is_string( $row['sources'] ) ) {
            $row['sources'] = maybe_unserialize( $row['sources'] );
        }

        return rest_ensure_response( $row );
    }

    public function create_redirection( $request ) {
        if ( ! self::is_active() ) {
            return self::inactive_response();
        }
        if ( ! class_exists( '\RankMath\Redirections\DB' ) ) {
            return self::redirections_unavailable();
        }

        $sources = self::normalize_redirection_sources_from_request( $request );
        if ( is_wp_error( $sources ) ) {
            return new WP_REST_Response( array( 'error' => $sources->get_error_code(), 'message' => $sources->get_error_message() ), 400 );
        }

        $header = intval( $request->get_param( 'header_code' ) ?: 301 );
        if ( ! in_array( $header, array( 301, 302, 307, 410, 451 ), true ) ) {
            $header = 301;
        }

        $url_to = (string) $request->get_param( 'url_to' );
        if ( in_array( $header, array( 410, 451 ), true ) ) {
            $url_to = '';
        } else {
            $url_to = esc_url_raw( $url_to );
        }

        $status = sanitize_key( (string) $request->get_param( 'status' ) ?: 'active' );
        if ( ! in_array( $status, array( 'active', 'inactive' ), true ) ) {
            $status = 'active';
        }

        $insert_id = \RankMath\Redirections\DB::add(
            array(
                'sources'     => $sources,
                'url_to'      => $url_to,
                'header_code' => $header,
                'status'      => $status,
            )
        );

        if ( ! $insert_id ) {
            return new WP_REST_Response( array( 'error' => 'Could not create redirection' ), 422 );
        }

        self::clear_rm_cache();

        return rest_ensure_response( array( 'success' => true, 'id' => (int) $insert_id ) );
    }

    public function update_redirection( $request ) {
        if ( ! self::is_active() ) {
            return self::inactive_response();
        }
        if ( ! class_exists( '\RankMath\Redirections\DB' ) ) {
            return self::redirections_unavailable();
        }

        $id = intval( $request['id'] );
        $existing = \RankMath\Redirections\DB::get_redirection_by_id( $id, 'all' );
        if ( ! $existing ) {
            return new WP_REST_Response( array( 'error' => 'Redirection not found' ), 404 );
        }

        $args = array( 'id' => $id );

        if ( $request->has_param( 'sources' ) || $request->has_param( 'source_pattern' ) ) {
            $sources = self::normalize_redirection_sources_from_request( $request );
            if ( is_wp_error( $sources ) ) {
                return new WP_REST_Response( array( 'error' => $sources->get_error_code(), 'message' => $sources->get_error_message() ), 400 );
            }
            $args['sources'] = $sources;
        } else {
            $args['sources'] = maybe_unserialize( $existing['sources'] );
        }

        if ( $request->has_param( 'url_to' ) ) {
            $args['url_to'] = esc_url_raw( (string) $request->get_param( 'url_to' ) );
        } else {
            $args['url_to'] = $existing['url_to'];
        }

        $header = $request->has_param( 'header_code' )
            ? intval( $request->get_param( 'header_code' ) )
            : intval( $existing['header_code'] );
        if ( ! in_array( $header, array( 301, 302, 307, 410, 451 ), true ) ) {
            $header = 301;
        }
        $args['header_code'] = $header;
        if ( in_array( $header, array( 410, 451 ), true ) ) {
            $args['url_to'] = '';
        }

        if ( $request->has_param( 'status' ) ) {
            $st = sanitize_key( (string) $request->get_param( 'status' ) );
            if ( in_array( $st, array( 'active', 'inactive', 'trashed' ), true ) ) {
                $args['status'] = $st;
            }
        } else {
            $args['status'] = $existing['status'];
        }

        \RankMath\Redirections\DB::update( $args );
        self::clear_rm_cache();

        return rest_ensure_response( array( 'success' => true, 'id' => $id ) );
    }

    public function delete_redirection( $request ) {
        if ( ! self::is_active() ) {
            return self::inactive_response();
        }
        if ( ! class_exists( '\RankMath\Redirections\DB' ) ) {
            return self::redirections_unavailable();
        }

        $id = intval( $request['id'] );
        $n   = \RankMath\Redirections\DB::delete( array( $id ) );

        return rest_ensure_response( array( 'success' => (bool) $n, 'deleted' => (int) $n ) );
    }

    /**
     * @return WP_Error|array<int,array<string,string>>
     */
    private static function normalize_redirection_sources_from_request( $request ) {
        if ( $request->has_param( 'sources' ) && is_array( $request->get_param( 'sources' ) ) ) {
            $raw = $request->get_param( 'sources' );
            if ( ! is_array( $raw ) ) {
                return new WP_Error( 'invalid_sources', 'sources must be a non-empty array of {pattern, comparison}.' );
            }
            $out = array();
            foreach ( $raw as $item ) {
                if ( ! is_array( $item ) || empty( $item['pattern'] ) ) {
                    continue;
                }
                $cmp = sanitize_key( (string) ( $item['comparison'] ?? 'exact' ) );
                if ( ! in_array( $cmp, array( 'exact', 'contains', 'start', 'end', 'regex' ), true ) ) {
                    $cmp = 'exact';
                }
                $row = array(
                    'pattern'     => sanitize_text_field( (string) $item['pattern'] ),
                    'comparison'  => $cmp,
                );
                if ( ! empty( $item['ignore'] ) ) {
                    $row['ignore'] = sanitize_key( (string) $item['ignore'] );
                }
                $out[] = $row;
            }
            if ( empty( $out ) ) {
                return new WP_Error( 'invalid_sources', 'sources must include at least one valid pattern.' );
            }
            return $out;
        }

        $pattern = $request->get_param( 'source_pattern' );
        if ( empty( $pattern ) ) {
            return new WP_Error( 'missing_source', 'Provide sources[] or source_pattern (and optional comparison).' );
        }
        $cmp = sanitize_key( (string) $request->get_param( 'comparison' ) ?: 'exact' );
        if ( ! in_array( $cmp, array( 'exact', 'contains', 'start', 'end', 'regex' ), true ) ) {
            $cmp = 'exact';
        }

        return array(
            array(
                'pattern'    => sanitize_text_field( (string) $pattern ),
                'comparison' => $cmp,
            ),
        );
    }

    /**
     * @return array<string,string> key => type (toggle|text|int|sluglist|robotslist|hours|redir_code)
     */
    private static function settings_patch_whitelist( $group ) {
        if ( 'general' === $group ) {
            $map = array(
                'strip_category_base'                     => 'toggle',
                'attachment_redirect_urls'                => 'toggle',
                'attachment_redirect_default'             => 'text',
                'nofollow_external_links'                 => 'toggle',
                'nofollow_image_links'                    => 'toggle',
                'new_window_external_links'               => 'toggle',
                'add_img_alt'                             => 'toggle',
                'img_alt_format'                          => 'text',
                'add_img_title'                           => 'toggle',
                'img_title_format'                        => 'text',
                'breadcrumbs'                             => 'toggle',
                'breadcrumbs_separator'                   => 'text',
                'breadcrumbs_home'                        => 'toggle',
                'breadcrumbs_home_label'                  => 'text',
                'breadcrumbs_archive_format'              => 'text',
                'breadcrumbs_search_format'               => 'text',
                'breadcrumbs_404_label'                   => 'text',
                'breadcrumbs_ancestor_categories'         => 'toggle',
                'breadcrumbs_blog_page'                   => 'toggle',
                '404_monitor_mode'                        => 'text',
                '404_monitor_limit'                       => 'int',
                '404_monitor_ignore_query_parameters'     => 'toggle',
                'redirections_header_code'                  => 'redir_code',
                'redirections_debug'                      => 'toggle',
                'console_caching_control'                 => 'int',
                'console_email_reports'                   => 'toggle',
                'console_email_frequency'                 => 'text',
                'wc_remove_product_base'                  => 'toggle',
                'wc_remove_category_base'                 => 'toggle',
                'wc_remove_category_parent_slugs'         => 'toggle',
                'rss_before_content'                      => 'text',
                'rss_after_content'                       => 'text',
                'wc_remove_generator'                     => 'toggle',
                'remove_shop_snippet_data'                => 'toggle',
                'frontend_seo_score'                      => 'toggle',
                'frontend_seo_score_post_types'           => 'sluglist',
                'frontend_seo_score_position'             => 'text',
                'setup_mode'                              => 'text',
                'content_ai_post_types'                   => 'sluglist',
                'content_ai_country'                      => 'text',
                'content_ai_tone'                         => 'text',
                'content_ai_audience'                     => 'text',
                'content_ai_language'                     => 'text',
                'analytics_stats'                         => 'toggle',
                'toc_block_title'                         => 'text',
                'toc_block_list_style'                    => 'text',
                'llms_post_types'                         => 'sluglist',
            );
            return $map;
        }

        if ( 'sitemap' === $group ) {
            $keys = array(
                'items_per_page', 'include_images', 'include_featured_image', 'html_sitemap',
                'html_sitemap_display', 'html_sitemap_sort', 'html_sitemap_seo_titles', 'authors_sitemap',
                'exclude_roles',
            );
            $types = array( 'int', 'toggle', 'toggle', 'toggle', 'text', 'text', 'text', 'toggle', 'sluglist' );
            $out   = array_combine( $keys, $types );
            // allow pt_*_sitemap and tax_*_sitemap toggles
            $all = get_option( self::OPT_SITEMAP, array() );
            if ( is_array( $all ) ) {
                foreach ( array_keys( $all ) as $k ) {
                    if ( preg_match( '/^(pt|tax)_[a-z0-9_-]+_sitemap$/', $k ) ) {
                        $out[ $k ] = 'toggle';
                    }
                }
            }
            return $out;
        }

        // titles — claves globales (edición de plantillas por tipo: usar la UI de Rank Math o ampliar whitelist).
        return array(
            'noindex_empty_taxonomies'           => 'toggle',
            'title_separator'                    => 'text',
            'capitalize_titles'                  => 'toggle',
            'twitter_card_type'                  => 'text',
            'knowledgegraph_type'                => 'text',
            'knowledgegraph_name'                => 'text',
            'website_name'                       => 'text',
            'local_business_type'                => 'text',
            'local_address_format'               => 'text',
            'opening_hours'                      => 'hours',
            'opening_hours_format'               => 'toggle',
            'homepage_title'                     => 'text',
            'homepage_description'               => 'textarea',
            'homepage_custom_robots'             => 'toggle',
            'disable_author_archives'            => 'toggle',
            'url_author_base'                    => 'text',
            'author_custom_robots'               => 'toggle',
            'author_robots'                      => 'robotslist',
            'author_archive_title'               => 'text',
            'author_add_meta_box'                => 'toggle',
            'disable_date_archives'              => 'toggle',
            'date_archive_title'                 => 'text',
            'search_title'                       => 'text',
            '404_title'                          => 'text',
            'date_archive_robots'                => 'robotslist',
            'noindex_search'                     => 'toggle',
            'noindex_archive_subpages'           => 'toggle',
            'noindex_password_protected'         => 'toggle',
            'author_slack_enhanced_sharing'      => 'toggle',
            'pt_download_default_rich_snippet'   => 'text',
            'remove_product_cat_snippet_data'    => 'toggle',
            'remove_product_tag_snippet_data'    => 'toggle',
        );
    }

    /**
     * @param string $type
     * @param mixed  $value
     * @return mixed|string
     */
    private static function sanitize_settings_value( $type, $value ) {
        switch ( $type ) {
            case 'toggle':
                $v = is_scalar( $value ) ? strtolower( (string) $value ) : '';
                return in_array( $v, array( 'on', 'off' ), true ) ? $v : '__skip__';
            case 'int':
                return max( 0, intval( $value ) );
            case 'redir_code':
                $c = intval( $value );
                return in_array( $c, array( 301, 302, 307 ), true ) ? (string) $c : '301';
            case 'sluglist':
                if ( ! is_array( $value ) ) {
                    return '__skip__';
                }
                $slugs = array();
                foreach ( $value as $s ) {
                    $slugs[] = sanitize_key( (string) $s );
                }
                return array_values( array_filter( array_unique( $slugs ) ) );
            case 'robotslist':
                if ( ! is_array( $value ) ) {
                    return '__skip__';
                }
                $clean = array();
                foreach ( $value as $r ) {
                    $r = sanitize_key( (string) $r );
                    if ( $r ) {
                        $clean[] = $r;
                    }
                }
                return array_values( array_unique( $clean ) );
            case 'hours':
                if ( ! is_array( $value ) ) {
                    return '__skip__';
                }
                $hours = array();
                foreach ( $value as $row ) {
                    if ( ! is_array( $row ) || empty( $row['day'] ) ) {
                        continue;
                    }
                    $hours[] = array(
                        'day'  => sanitize_text_field( (string) $row['day'] ),
                        'time' => sanitize_text_field( (string) ( $row['time'] ?? '' ) ),
                    );
                }
                return $hours;
            case 'textarea':
                if ( is_array( $value ) ) {
                    return '__skip__';
                }
                return sanitize_textarea_field( is_scalar( $value ) ? (string) $value : '' );
            case 'text':
            default:
                if ( is_array( $value ) ) {
                    return '__skip__';
                }
                return sanitize_text_field( is_scalar( $value ) ? (string) $value : '' );
        }
    }

    private static function clear_rm_cache() {
        if ( class_exists( '\RankMath\Helper' ) ) {
            \RankMath\Helper::clear_cache();
        }
    }

    private static function inactive_response() {
        return new WP_REST_Response(
            array( 'error' => 'rank_math_inactive', 'message' => 'Rank Math is not installed or not activated.' ),
            503
        );
    }

    private static function redirections_unavailable() {
        return new WP_REST_Response(
            array(
                'error'   => 'redirections_unavailable',
                'message' => 'Rank Math redirections module or DB class is not available.',
            ),
            503
        );
    }

    /**
     * @param int    $post_id
     * @param string $meta_key
     * @return mixed
     */
    private static function read_meta( $post_id, $meta_key ) {
        $v = get_post_meta( $post_id, $meta_key, true );
        if ( '' !== $v && null !== $v && false !== $v ) {
            return self::maybe_unserialize_meta( $v );
        }
        $alt = '_' . $meta_key;
        $v2  = get_post_meta( $post_id, $alt, true );
        if ( '' !== $v2 && null !== $v2 && false !== $v2 ) {
            return self::maybe_unserialize_meta( $v2 );
        }
        return null;
    }

    /**
     * @param int $post_id
     * @return array<string,mixed>
     */
    private static function collect_seo_for_keys( $post_id ) {
        $out = array();
        foreach ( array_keys( self::$allowed_keys ) as $key ) {
            $out[ $key ] = self::read_meta( $post_id, $key );
        }
        return $out;
    }

    /**
     * @param mixed $v
     * @return mixed
     */
    private static function maybe_unserialize_meta( $v ) {
        if ( is_array( $v ) || is_object( $v ) ) {
            return $v;
        }
        if ( is_string( $v ) ) {
            return maybe_unserialize( $v );
        }
        return $v;
    }

    /**
     * @param string $type
     * @param mixed  $raw
     * @return mixed|null
     */
    private static function sanitize_meta_value( $type, $raw ) {
        switch ( $type ) {
            case 'text':
                if ( null === $raw ) {
                    return null;
                }
                return sanitize_text_field( is_scalar( $raw ) ? (string) $raw : '' );
            case 'textarea':
                if ( null === $raw ) {
                    return null;
                }
                return sanitize_textarea_field( is_scalar( $raw ) ? (string) $raw : '' );
            case 'url':
                if ( null === $raw || '' === $raw ) {
                    return null;
                }
                return esc_url_raw( is_scalar( $raw ) ? (string) $raw : '' );
            case 'int':
                if ( null === $raw || '' === $raw ) {
                    return null;
                }
                return absint( $raw );
            case 'bool':
                return (bool) $raw;
            case 'robots':
                if ( null === $raw || '' === $raw ) {
                    return null;
                }
                if ( is_string( $raw ) ) {
                    $decoded = json_decode( $raw, true );
                    if ( JSON_ERROR_NONE === json_last_error() && is_array( $decoded ) ) {
                        $raw = $decoded;
                    } else {
                        return null;
                    }
                }
                if ( ! is_array( $raw ) ) {
                    return null;
                }
                $clean = array();
                foreach ( $raw as $k => $v ) {
                    $sk = sanitize_key( (string) $k );
                    $sv = sanitize_key( is_scalar( $v ) ? (string) $v : '' );
                    if ( $sk && $sv ) {
                        $clean[ $sk ] = $sv;
                    }
                }
                return $clean;
            default:
                return null;
        }
    }

    /**
     * @param int $post_id
     */
    private static function maybe_refresh_score( $post_id ) {
        if ( ! function_exists( 'rank_math' ) ) {
            return;
        }
        $app = rank_math();
        if ( ! is_object( $app ) ) {
            return;
        }
        if ( method_exists( $app, 'get' ) ) {
            $score = $app->get( 'score' );
            if ( $score && method_exists( $score, 'calculate_score' ) ) {
                $score->calculate_score( $post_id );
            }
        }
        clean_post_cache( $post_id );
    }
}
