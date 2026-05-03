<?php
/**
 * Admin panel for WP AI Control.
 *
 * @package WP_AI_Control
 * @subpackage WP_AI_Control/admin
 */

class WPAIC_Admin {

	public function __construct() {}

	public function add_admin_menu() {
		add_options_page(
			'WP AI Control',
			'WP AI Control',
			'manage_options',
			'wp-ai-control',
			array( $this, 'render_admin_page' )
		);
	}

	public function enqueue_scripts( $hook ) {
		if ( 'settings_page_wp-ai-control' !== $hook ) {
			return;
		}
		wp_enqueue_style( 'wpaic-admin', WPAIC_PLUGIN_URL . 'includes/admin/css/admin.css', array(), WPAIC_VERSION );
		wp_enqueue_script( 'wpaic-admin', WPAIC_PLUGIN_URL . 'includes/admin/js/admin.js', array( 'jquery' ), WPAIC_VERSION, true );
	}

	public function render_admin_page() {
		$active_tab = $_GET['tab'] ?? 'api-keys';
		$retention = get_option( 'wpaic_audit_retention_days', WPAIC_AUDIT_RETENTION_DEFAULT );
		$retention_options = array( 7 => '7 days', 30 => '30 days', 90 => '90 days', 365 => '365 days' );

		if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['wpaic_save_settings'] ) ) {
			check_admin_referrer( 'wpaic_settings', 'wpaic_nonce' );
			$new_retention = absint( $_POST['wpaic_audit_retention'] );
			if ( in_array( $new_retention, array_keys( $retention_options ), true ) ) {
				update_option( 'wpaic_audit_retention_days', $new_retention );
				echo '<div class="notice notice-success"><p>Settings saved.</p></div>';
				$retention = $new_retention;
			}
		}
		?>
		<div class="wrap">
			<h1>WP AI Control</h1>
			<h2 class="nav-tab-wrapper">
				<a href="?page=wp-ai-control&tab=api-keys" class="nav-tab <?php echo 'api-keys' === $active_tab ? 'nav-tab-active' : ''; ?>">API Keys</a>
				<a href="?page=wp-ai-control&tab=audit-log" class="nav-tab <?php echo 'audit-log' === $active_tab ? 'nav-tab-active' : ''; ?>">Audit Log</a>
				<a href="?page=wp-ai-control&tab=settings" class="nav-tab <?php echo 'settings' === $active_tab ? 'nav-tab-active' : ''; ?>">Settings</a>
			</h2>

			<?php
			switch ( $active_tab ) {
				case 'api-keys':
					include WPAIC_PLUGIN_DIR . 'includes/admin/views/settings.php';
					break;
				case 'audit-log':
					include WPAIC_PLUGIN_DIR . 'includes/admin/views/audit-log.php';
					break;
				case 'settings':
					?>
					<h2>Settings</h2>
					<form method="post" action="options-general.php?page=wp-ai-control&tab=settings">
						<?php wp_nonce_field( 'wpaic_settings', 'wpaic_nonce' ); ?>
						<table class="form-table">
							<tr>
								<th><label for="wpaic_audit_retention">Audit Log Retention</label></th>
								<td>
									<select name="wpaic_audit_retention" id="wpaic_audit_retention">
										<?php foreach ( $retention_options as $days => $label ) : ?>
											<option value="<?php echo $days; ?>" <?php selected( $retention, $days ); ?>><?php echo $label; ?></option>
										<?php endforeach; ?>
									</select>
									<p class="description">How long to keep audit log entries.</p>
								</td>
							</tr>
						</table>
						<?php submit_button( 'Save Settings', 'primary', 'wpaic_save_settings' ); ?>
					</form>
					<?php
					break;
			}
			?>
		</div>
		<?php
	}
}
