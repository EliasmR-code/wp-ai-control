<?php
class WPAIC_Analysis {

    public function register_routes() {
        register_rest_route( WPAIC_NAMESPACE, '/analyze/seo/(?P<page_id>\d+)', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'analyze_seo' ),
            'permission_callback' => array( 'WPAIC_Auth', 'check' ),
        ));
        register_rest_route( WPAIC_NAMESPACE, '/analyze/performance/(?P<page_id>\d+)', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'analyze_performance' ),
            'permission_callback' => array( 'WPAIC_Auth', 'check' ),
        ));
        register_rest_route( WPAIC_NAMESPACE, '/analyze/aeo/(?P<page_id>\d+)', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'analyze_aeo' ),
            'permission_callback' => array( 'WPAIC_Auth', 'check' ),
        ));
        register_rest_route( WPAIC_NAMESPACE, '/analyze/accessibility/(?P<page_id>\d+)', array(
            'methods'             => 'GET',
            'callback'            => array( $this, 'analyze_accessibility' ),
            'permission_callback' => array( 'WPAIC_Auth', 'check' ),
        ));
    }

    public function analyze_seo( $request ) {
        $post_id = intval( $request['page_id'] );
        $post    = get_post( $post_id );
        if ( ! $post ) return new WP_REST_Response( array( 'error' => 'Not found' ), 404 );

        $content      = strip_tags( $post->post_content );
        $title        = $post->post_title;
        $excerpt      = $post->post_excerpt;
        $word_count   = str_word_count( $content );
        $has_meta_desc = ! empty( get_post_meta( $post_id, '_yoast_wpseo_metadesc', true ) ) || ! empty( get_post_meta( $post_id, '_aioseo_description', true ) );

        // Extract headings from content
        preg_match_all( '/<h([1-6])[^>]*>(.*?)<\/h\1>/i', $post->post_content, $headings );
        $h1_count = count( array_filter( $headings[1], function( $l ) { return '1' === $l; } ) );
        $h2_count = count( array_filter( $headings[1], function( $l ) { return '2' === $l; } ) );

        // Extract images and check alt text
        preg_match_all( '/<img[^>]+>/i', $post->post_content, $imgs );
        $total_images = count( $imgs[0] );
        $images_without_alt = count( array_filter( $imgs[0], function( $img ) {
            return ! preg_match( '/alt=["\'][^"\']+["\']/', $img );
        } ) );

        $recommendations = array();
        if ( $word_count < 300 ) $recommendations[] = 'Content is short (< 300 words). Consider adding more content.';
        if ( $h1_count === 0 ) $recommendations[] = 'No H1 heading found.';
        if ( $h1_count > 1 ) $recommendations[] = 'Multiple H1 headings found. Use only one.';
        if ( $h2_count === 0 ) $recommendations[] = 'No H2 headings. Add subheadings to structure the content.';
        if ( ! $has_meta_desc ) $recommendations[] = 'No meta description found (Yoast/AiOSEO).';
        if ( empty( $excerpt ) ) $recommendations[] = 'No excerpt set.';
        if ( $images_without_alt > 0 ) $recommendations[] = $images_without_alt . ' image(s) missing alt text.';

        return rest_ensure_response( array(
            'post_id'             => $post_id,
            'title'               => $title,
            'word_count'          => $word_count,
            'h1_count'            => $h1_count,
            'h2_count'            => $h2_count,
            'has_meta_description' => $has_meta_desc,
            'total_images'        => $total_images,
            'images_without_alt'  => $images_without_alt,
            'url'                 => get_permalink( $post_id ),
            'score'               => max( 0, 100 - ( count( $recommendations ) * 15 ) ),
            'recommendations'     => $recommendations,
        ) );
    }

    public function analyze_performance( $request ) {
        $post_id = intval( $request['page_id'] );
        $post    = get_post( $post_id );
        if ( ! $post ) return new WP_REST_Response( array( 'error' => 'Not found' ), 404 );

        // Count assets in content
        preg_match_all( '/<img[^>]+>/i', $post->post_content, $imgs );
        preg_match_all( '/<script[^>]*>.*?<\/script>/si', $post->post_content, $scripts );
        preg_match_all( '/https?:\/\/[^"\'>\s]+\.(?:css|js)/i', $post->post_content, $external );

        $issues = array();
        if ( count( $imgs[0] ) > 10 ) $issues[] = 'Page has more than 10 images. Consider lazy loading.';
        if ( count( $external[0] ) > 5 ) $issues[] = 'More than 5 external resource references detected.';

        // Check if content has inline styles
        if ( substr_count( $post->post_content, 'style=' ) > 20 ) {
            $issues[] = 'Many inline styles detected. Consider moving to CSS files.';
        }

        return rest_ensure_response( array(
            'post_id'            => $post_id,
            'image_count'        => count( $imgs[0] ),
            'inline_scripts'     => count( $scripts[0] ),
            'external_resources' => count( $external[0] ),
            'content_size_bytes' => strlen( $post->post_content ),
            'issues'             => $issues,
            'recommendations'    => empty( $issues ) ? array( 'No major performance issues detected.' ) : $issues,
        ) );
    }

    public function analyze_aeo( $request ) {
        $post_id = intval( $request['page_id'] );
        $post    = get_post( $post_id );
        if ( ! $post ) return new WP_REST_Response( array( 'error' => 'Not found' ), 404 );

        $content = $post->post_content;

        // Check for structured data (schema)
        $has_schema        = false !== strpos( $content, 'application/ld+json' );
        $has_faq           = false !== stripos( $content, 'FAQPage' ) || false !== stripos( $content, 'wp-block-faq' );
        $has_how_to        = false !== stripos( $content, 'HowTo' );
        $has_lists         = preg_match_all( '/<(?:ul|ol)[^>]*>/i', $content, $m ) > 2;
        $has_tables        = preg_match( '/<table/i', $content ) === 1;
        $word_count        = str_word_count( strip_tags( $content ) );
        $has_clear_answers = preg_match( '/<h[2-4][^>]*>.*?<\/h[2-4]>/si', $content ) === 1;

        $recommendations = array();
        if ( ! $has_schema ) $recommendations[] = 'Add structured data (JSON-LD schema markup).';
        if ( ! $has_faq ) $recommendations[] = 'Consider adding an FAQ section to target answer boxes.';
        if ( $word_count < 500 ) $recommendations[] = 'Content is short for AEO; aim for 500+ words.';
        if ( ! $has_lists ) $recommendations[] = 'Add bullet/numbered lists for better answer extraction.';

        return rest_ensure_response( array(
            'post_id'           => $post_id,
            'has_schema'        => $has_schema,
            'has_faq'           => $has_faq,
            'has_how_to'        => $has_how_to,
            'has_lists'         => $has_lists,
            'has_tables'        => $has_tables,
            'word_count'        => $word_count,
            'has_clear_answers' => $has_clear_answers,
            'aeo_score'         => max( 0, 100 - ( count( $recommendations ) * 20 ) ),
            'recommendations'   => $recommendations,
        ) );
    }

    public function analyze_accessibility( $request ) {
        $post_id = intval( $request['page_id'] );
        $post    = get_post( $post_id );
        if ( ! $post ) return new WP_REST_Response( array( 'error' => 'Not found' ), 404 );

        $content = $post->post_content;

        preg_match_all( '/<img[^>]+>/i', $content, $imgs );
        $images_no_alt = array_filter( $imgs[0], function( $img ) {
            return ! preg_match( '/alt=["\'][^"\']+["\']/', $img );
        } );

        preg_match_all( '/<a[^>]+>/i', $content, $links );
        $links_no_text = array_filter( $links[0], function( $link ) {
            return ! preg_match( '/(?:title|aria-label)=["\'][^"\']+["\']/', $link );
        } );

        $has_lang         = false !== strpos( get_bloginfo( 'language' ), '-' );
        $has_headings_order = preg_match( '/<h1/i', $content ) && preg_match( '/<h2/i', $content );
        $issues           = array();

        if ( count( $images_no_alt ) > 0 ) $issues[] = count( $images_no_alt ) . ' image(s) missing alt text (WCAG 1.1.1).';
        if ( ! $has_headings_order ) $issues[] = 'Heading hierarchy may be broken (should start with H1).';

        return rest_ensure_response( array(
            'post_id'             => $post_id,
            'images_missing_alt'  => count( $images_no_alt ),
            'links_analyzed'      => count( $links[0] ),
            'lang_attribute'      => $has_lang,
            'heading_order_ok'    => $has_headings_order,
            'wcag_issues'         => $issues,
            'accessibility_score' => max( 0, 100 - ( count( $issues ) * 25 ) ),
        ) );
    }
}
