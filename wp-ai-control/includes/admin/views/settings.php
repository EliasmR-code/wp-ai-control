<div class="wrap">
  <h2>API Keys</h2>
  <p>Generate API keys to connect AI tools (Claude, Cursor, etc.) to your WordPress site.</p>
  <p><strong>Authentication header:</strong> <code>X-WPAIC-API-Key: wpaic_live_xxxxxxxx...</code></p>

  <form method="post" action="">
    <?php wp_nonce_field('wpaic_generate_key', 'wpaic_nonce'); ?>
    <table class="form-table">
      <tr>
        <th><label for="key_name">Key Name</label></th>
        <td><input type="text" id="key_name" name="key_name" placeholder="e.g., Claude Desktop" class="regular-text"></td>
      </tr>
      <tr>
        <th><label for="key_permissions">Permissions</label></th>
        <td>
          <label><input type="checkbox" name="permissions[]" value="read" checked> Read</label><br>
          <label><input type="checkbox" name="permissions[]" value="write" checked> Write</label><br>
          <label><input type="checkbox" name="permissions[]" value="admin"> Admin</label>
        </td>
      </tr>
    </table>
    <?php submit_button('Generate New API Key', 'primary', 'generate_key'); ?>
  </form>

  <?php
  if (isset($_POST['generate_key']) && check_admin_referrer('wpaic_generate_key', 'wpaic_nonce', false)) {
    $name = sanitize_text_field($_POST['key_name']);
    $permissions = isset($_POST['permissions']) ? array_map('sanitize_text_field', $_POST['permissions']) : array('read', 'write');
    $api_key = WPAIC_Auth::generate_api_key(get_current_user_id(), $name, $permissions);

    if (!is_wp_error($api_key)) {
      echo '<div class="notice notice-success"><p><strong>New API Key generated!</strong> Save this key, it won\'t be shown again:</p>';
      echo '<p><code style="background:#f0f0f0;padding:10px;display:block;">' . esc_html($api_key) . '</code></p></div>';
    } else {
      echo '<div class="notice notice-error"><p>' . esc_html($api_key->get_error_message()) . '</p></div>';
    }
  }

  $keys = WPAIC_Auth::list_api_keys(get_current_user_id());
  if ($keys) :
  ?>
  <h3>Your API Keys</h3>
  <table class="wp-list-table widefat fixed striped">
    <thead><tr><th>Name</th><th>Key Prefix</th><th>Permissions</th><th>Created</th><th>Last Used</th><th>Action</th></tr></thead>
    <tbody>
    <?php foreach ($keys as $key) : ?>
      <tr>
        <td><?php echo esc_html($key->name); ?></td>
        <td><code><?php echo esc_html($key->key_prefix . '...'); ?></code></td>
        <td><?php echo esc_html(implode(', ', json_decode($key->permissions ?: '[]', true) ?: array())); ?></td>
        <td><?php echo esc_html($key->created_at); ?></td>
        <td><?php echo $key->last_used ? esc_html($key->last_used) : 'Never'; ?></td>
        <td>
          <form method="post" action="" style="display:inline;">
            <?php wp_nonce_field('wpaic_revoke_key', 'wpaic_nonce'); ?>
            <input type="hidden" name="revoke_key_id" value="<?php echo $key->id; ?>">
            <button type="submit" name="revoke_key" class="button-link" style="color:#a00;" onclick="return confirm('Revoke this key?')">Revoke</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php
  endif;

  if (isset($_POST['revoke_key']) && check_admin_referrer('wpaic_revoke_key', 'wpaic_nonce', false)) {
    $result = WPAIC_Auth::revoke_api_key($_POST['revoke_key_id'], get_current_user_id());
    if (!is_wp_error($result) && $result) {
      echo '<div class="notice notice-success"><p>API key revoked.</p></div>';
      echo '<script>window.location.reload();</script>';
    }
  }
  ?>
</div>
