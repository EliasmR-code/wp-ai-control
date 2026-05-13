<?php
class WPAIC_Admin {

    public static function add_menu() {
        add_options_page(
            'WP AI Control',
            'WP AI Control',
            'manage_options',
            'wpaic',
            array( __CLASS__, 'render' )
        );
    }

    public static function render() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You do not have permission to access this page.', 'wp-ai-control' ) );
        }

        $notice         = array();
        $active_tab     = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'api';

        // ── Handle API key regeneration ──────────────────────────────────────
        if ( 'api' === $active_tab && isset( $_POST['wpaic_regenerate'] ) && isset( $_POST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'wpaic_regenerate' ) ) {
            delete_option( 'wpaic_api_key' );
            WPAIC_Auth::get_key();
            $notice = array( 'type' => 'success', 'msg' => __( 'New API Key generated!', 'wp-ai-control' ) );
        }

        // ── Handle license activation ────────────────────────────────────────
        if ( 'license' === $active_tab && isset( $_POST['wpaic_license_action'] ) && isset( $_POST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'wpaic_license' ) ) {
            $action = sanitize_key( $_POST['wpaic_license_action'] );
            if ( 'activate' === $action ) {
                $result = WPAIC_License::activate( sanitize_text_field( wp_unslash( $_POST['wpaic_license_key'] ?? '' ) ) );
            } elseif ( 'deactivate' === $action ) {
                $result = WPAIC_License::deactivate();
            } elseif ( 'refresh' === $action ) {
                WPAIC_License::refresh();
                $result = array( 'success' => true, 'message' => __( 'License status refreshed.', 'wp-ai-control' ) );
            } elseif ( 'set_plan' === $action ) {
                WPAIC_License::set_plan( sanitize_key( wp_unslash( $_POST['wpaic_plan_variant'] ?? 'studio' ) ) );
                WPAIC_Plan::set_builder_woocommerce_enabled( ! empty( $_POST['wpaic_builder_woocommerce_enabled'] ) );
                $result = array( 'success' => true, 'message' => __( 'Plan variant updated.', 'wp-ai-control' ) );
            }
            if ( isset( $result ) ) {
                $notice = array( 'type' => $result['success'] ? 'success' : 'error', 'msg' => $result['message'] );
            }
        }

        $api_key    = WPAIC_Auth::get_key();
        $api_url    = get_site_url() . '/wp-json/' . WPAIC_NAMESPACE . '/';
        $lic_key    = WPAIC_License::get_key();
        $lic_status = WPAIC_License::get_status();
        $lic_expiry = WPAIC_License::get_expiry();
        $lic_masked = WPAIC_License::get_key_masked();
        $lic_active = in_array( $lic_status, array( 'active', 'pending' ), true );
        $plan_key   = WPAIC_Plan::get_current();
        $plan_cfg   = WPAIC_Plan::get_config( $plan_key );
        $all_plans  = WPAIC_Plan::all();
        $builder_woo_enabled = WPAIC_Plan::is_builder_woocommerce_enabled();

        $status_colors = array(
            'active'   => '#00a32a',
            'pending'  => '#dba617',
            'expired'  => '#d63638',
            'blocked'  => '#d63638',
            'inactive' => '#787c82',
            'unknown'  => '#787c82',
            'error'    => '#d63638',
        );
        $status_color = $status_colors[ $lic_status ] ?? '#787c82';
        ?>
        <div class="wrap">
            <h1>WP AI Control</h1>

            <?php if ( ! empty( $notice ) ) : ?>
            <div class="notice notice-<?php echo esc_attr( $notice['type'] ); ?> is-dismissible">
                <p><?php echo esc_html( $notice['msg'] ); ?></p>
            </div>
            <?php endif; ?>

            <nav class="nav-tab-wrapper" style="margin-bottom:20px;">
                <a href="?page=wpaic&tab=api"
                   class="nav-tab <?php echo 'api' === $active_tab ? 'nav-tab-active' : ''; ?>">
                    <?php esc_html_e( 'API & Connection', 'wp-ai-control' ); ?>
                </a>
                <a href="?page=wpaic&tab=license"
                   class="nav-tab <?php echo 'license' === $active_tab ? 'nav-tab-active' : ''; ?>">
                    <?php esc_html_e( 'License', 'wp-ai-control' ); ?>
                    <?php if ( ! $lic_active ) : ?>
                        <span style="background:#d63638;color:#fff;border-radius:3px;padding:1px 6px;font-size:11px;margin-left:4px;">
                            <?php esc_html_e( 'Inactive', 'wp-ai-control' ); ?>
                        </span>
                    <?php endif; ?>
                </a>
            </nav>

            <?php if ( 'license' === $active_tab ) : ?>
            <!-- ── LICENSE TAB ──────────────────────────────────────────── -->
            <h2><?php esc_html_e( 'License', 'wp-ai-control' ); ?></h2>

            <table class="form-table">
                <tr>
                    <th><?php esc_html_e( 'Status', 'wp-ai-control' ); ?></th>
                    <td>
                        <strong style="color:<?php echo esc_attr( $status_color ); ?>;text-transform:capitalize;">
                            <?php echo esc_html( $lic_status ?: 'inactive' ); ?>
                        </strong>
                        <?php if ( $lic_expiry ) : ?>
                            &nbsp;&mdash;&nbsp;<?php esc_html_e( 'Expires:', 'wp-ai-control' ); ?>
                            <strong><?php echo esc_html( $lic_expiry ); ?></strong>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php if ( $lic_key ) : ?>
                <tr>
                    <th><?php esc_html_e( 'License Key', 'wp-ai-control' ); ?></th>
                    <td>
                        <code><?php echo esc_html( $lic_masked ); ?></code>
                    </td>
                </tr>
                <?php endif; ?>
                <tr>
                    <th><?php esc_html_e( 'Plan', 'wp-ai-control' ); ?></th>
                    <td>
                        <strong><?php echo esc_html( $plan_cfg['label'] ); ?></strong>
                        <span class="description" style="display:block;">
                            <?php echo esc_html( sprintf( 'Sites: %d | Workflows: %d | Tools: %d | Builders: %d', $plan_cfg['sites_limit'], $plan_cfg['workflows_limit'], $plan_cfg['tools_count'], $plan_cfg['builders_count'] ) ); ?>
                        </span>
                    </td>
                </tr>
            </table>

            <h3><?php esc_html_e( 'Plan Variant', 'wp-ai-control' ); ?></h3>
            <p><?php esc_html_e( 'Select which variant is enabled for this installation.', 'wp-ai-control' ); ?></p>
            <form method="post">
                <?php wp_nonce_field( 'wpaic_license' ); ?>
                <input type="hidden" name="wpaic_license_action" value="set_plan">
                <select name="wpaic_plan_variant">
                    <?php foreach ( $all_plans as $key => $cfg ) : ?>
                        <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $plan_key, $key ); ?>>
                            <?php echo esc_html( $cfg['label'] . ' (' . $cfg['tools_count'] . ' tools)' ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <p style="margin-top:10px;">
                    <label>
                        <input type="checkbox" name="wpaic_builder_woocommerce_enabled" value="1" <?php checked( $builder_woo_enabled ); ?>>
                        <?php esc_html_e( 'Enable WooCommerce addon for Builder plan', 'wp-ai-control' ); ?>
                    </label>
                </p>
                <?php submit_button( __( 'Save Plan Variant', 'wp-ai-control' ), 'secondary', 'submit', false ); ?>
            </form>

            <?php if ( $lic_active ) : ?>
            <!-- Deactivate form -->
            <h3><?php esc_html_e( 'Deactivate License', 'wp-ai-control' ); ?></h3>
            <p><?php esc_html_e( 'Deactivating frees up one activation slot so you can use the license on another site.', 'wp-ai-control' ); ?></p>
            <form method="post">
                <?php wp_nonce_field( 'wpaic_license' ); ?>
                <input type="hidden" name="wpaic_license_action" value="deactivate">
                <button type="submit" class="button button-secondary"
                        onclick="return confirm('<?php esc_attr_e( 'Deactivate license on this site?', 'wp-ai-control' ); ?>')">
                    <?php esc_html_e( 'Deactivate License', 'wp-ai-control' ); ?>
                </button>
                &nbsp;
                <button type="submit" form="wpaic-refresh-form" class="button">
                    <?php esc_html_e( 'Refresh Status', 'wp-ai-control' ); ?>
                </button>
            </form>
            <form id="wpaic-refresh-form" method="post">
                <?php wp_nonce_field( 'wpaic_license' ); ?>
                <input type="hidden" name="wpaic_license_action" value="refresh">
            </form>

            <?php else : ?>
            <!-- Activate form -->
            <h3><?php esc_html_e( 'Activate License', 'wp-ai-control' ); ?></h3>
            <p>
                <?php esc_html_e( 'Enter the license key you received after purchase.', 'wp-ai-control' ); ?>
                <?php if ( WPAIC_License::is_configured() ) : ?>
                <a href="<?php echo esc_url( WPAIC_SLM_URL ); ?>" target="_blank" rel="noopener">
                    <?php esc_html_e( 'Buy a license', 'wp-ai-control' ); ?>
                </a>
                <?php endif; ?>
            </p>
            <form method="post">
                <?php wp_nonce_field( 'wpaic_license' ); ?>
                <input type="hidden" name="wpaic_license_action" value="activate">
                <table class="form-table">
                    <tr>
                        <th><label for="wpaic_license_key"><?php esc_html_e( 'License Key', 'wp-ai-control' ); ?></label></th>
                        <td>
                            <input type="text" id="wpaic_license_key" name="wpaic_license_key"
                                   value="" placeholder="wpaic-XXXX-XXXX-XXXX-XXXX"
                                   style="width:360px;" autocomplete="off">
                            <p class="description">
                                <?php esc_html_e( 'This will activate the plugin for the domain:', 'wp-ai-control' ); ?>
                                <strong><?php echo esc_html( wp_parse_url( get_site_url(), PHP_URL_HOST ) ); ?></strong>
                            </p>
                        </td>
                    </tr>
                </table>
                <?php submit_button( __( 'Activate License', 'wp-ai-control' ), 'primary', 'submit', true ); ?>
            </form>
            <?php endif; ?>

            <?php else : ?>
            <!-- ── API TAB ─────────────────────────────────────────────── -->
            <h2><?php esc_html_e( 'API Key &amp; Connection', 'wp-ai-control' ); ?></h2>

            <?php if ( ! $lic_active ) : ?>
            <div class="notice notice-warning inline">
                <p>
                    <?php esc_html_e( 'Your license is not active. API endpoints are disabled.', 'wp-ai-control' ); ?>
                    <a href="?page=wpaic&tab=license"><?php esc_html_e( 'Activate now →', 'wp-ai-control' ); ?></a>
                </p>
            </div>
            <?php endif; ?>

            <p><strong><?php esc_html_e( 'Endpoint:', 'wp-ai-control' ); ?></strong>
               <code><?php echo esc_url( $api_url ); ?></code></p>

            <table class="form-table">
                <tr>
                    <th><?php esc_html_e( 'API Key', 'wp-ai-control' ); ?></th>
                    <td>
                        <input type="text" value="<?php echo esc_attr( $api_key ); ?>" readonly
                               style="width:400px;background:#f0f0f0;" id="wpaic-api-key">
                        <button type="button" class="button"
                                onclick="navigator.clipboard.writeText(document.getElementById('wpaic-api-key').value)">
                            <?php esc_html_e( 'Copy', 'wp-ai-control' ); ?>
                        </button>
                        <p class="description">
                            <?php esc_html_e( 'Authorization header:', 'wp-ai-control' ); ?>
                            <code>Bearer <?php echo esc_html( $api_key ); ?></code>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th><?php esc_html_e( 'Regenerate', 'wp-ai-control' ); ?></th>
                    <td>
                        <form method="post">
                            <?php wp_nonce_field( 'wpaic_regenerate' ); ?>
                            <button class="button" name="wpaic_regenerate"
                                    onclick="return confirm('<?php esc_attr_e( 'Generate a new API key? The old key will stop working.', 'wp-ai-control' ); ?>')">
                                <?php esc_html_e( 'Generate New Key', 'wp-ai-control' ); ?>
                            </button>
                        </form>
                    </td>
                </tr>
            </table>

            <hr>
            <h3><?php esc_html_e( 'Test Connection', 'wp-ai-control' ); ?></h3>
            <button id="wpaic-test" class="button button-primary">
                <?php esc_html_e( 'Test API', 'wp-ai-control' ); ?>
            </button>
            <div id="wpaic-result" style="margin-top:10px;"></div>

            <script>
            document.getElementById('wpaic-test').addEventListener('click', function() {
                fetch('<?php echo esc_url( $api_url . 'site-info' ); ?>')
                    .then(r => r.json())
                    .then(data => {
                        document.getElementById('wpaic-result').innerHTML =
                            '<div class="notice notice-success inline"><p>Connected! Site: <strong>' +
                            (data.name || JSON.stringify(data)) + '</strong></p></div>';
                    })
                    .catch(err => {
                        document.getElementById('wpaic-result').innerHTML =
                            '<div class="notice notice-error inline"><p>Error: ' + err.message + '</p></div>';
                    });
            });
            </script>

            <hr>
            <h3><?php esc_html_e( 'MCP Configuration', 'wp-ai-control' ); ?></h3>
            <?php
            /**
             * Public URL of the hosted wp-ai-control MCP server (Streamable HTTP, multi-tenant).
             * Override per deployment with: add_filter( 'wpaic_mcp_url', fn() => 'https://your-host/mcp' );
             */
            $mcp_url = apply_filters( 'wpaic_mcp_url', 'https://wp-ai-control-production.up.railway.app/mcp' );
            $mcp_config_json = wp_json_encode(
                array(
                    'mcpServers' => array(
                        'wp-ai-control' => array(
                            'url' => $mcp_url,
                        ),
                    ),
                ),
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            );
            ?>
            <p><?php esc_html_e( 'Multi-tenant hosted MCP server. Paste this snippet into Cursor (.cursor/mcp.json) or Claude Desktop (claude_desktop_config.json). Each tool call must include this site URL and your API Key (shown above).', 'wp-ai-control' ); ?></p>
            <p>
                <strong><?php esc_html_e( 'MCP endpoint:', 'wp-ai-control' ); ?></strong>
                <code><?php echo esc_html( $mcp_url ); ?></code>
                <button type="button" class="button button-small"
                        onclick="navigator.clipboard.writeText('<?php echo esc_js( $mcp_url ); ?>');this.innerText='<?php echo esc_js( __( 'Copied!', 'wp-ai-control' ) ); ?>';setTimeout(()=>this.innerText='<?php echo esc_js( __( 'Copy URL', 'wp-ai-control' ) ); ?>',1500);">
                    <?php esc_html_e( 'Copy URL', 'wp-ai-control' ); ?>
                </button>
            </p>
            <p style="margin-top:14px;"><strong><?php esc_html_e( 'Client config (Cursor / Claude Desktop):', 'wp-ai-control' ); ?></strong>
                <button type="button" class="button button-small"
                        onclick="navigator.clipboard.writeText(document.getElementById('wpaic-mcp-config').innerText);this.innerText='<?php echo esc_js( __( 'Copied!', 'wp-ai-control' ) ); ?>';setTimeout(()=>this.innerText='<?php echo esc_js( __( 'Copy JSON', 'wp-ai-control' ) ); ?>',1500);">
                    <?php esc_html_e( 'Copy JSON', 'wp-ai-control' ); ?>
                </button>
            </p>
            <pre id="wpaic-mcp-config" style="background:#1e1e1e;color:#d4d4d4;padding:16px;border-radius:4px;overflow:auto;font-size:13px;"><?php echo esc_html( $mcp_config_json ); ?></pre>
            <p class="description">
                <?php esc_html_e( 'Health check:', 'wp-ai-control' ); ?>
                <code><?php echo esc_html( preg_replace( '#/mcp/?$#', '/health', $mcp_url ) ); ?></code> →
                <code>{"ok":true,"service":"wp-ai-control-mcp"}</code>.
            </p>

            <?php endif; ?>
        </div>
        <?php
    }
}
