<div class="wrap">
  <h2>API Keys</h2>
  <p>Generate API keys to connect AI tools to your WordPress site.</p>
  <p><strong>Endpoint:</strong> <code><?php echo get_site_url(); ?>/wp-json/wp-ai-control/v1/</code></p>
  <p><strong>Authentication:</strong> Use header <code>Authorization: Bearer YOUR_API_KEY</code></p>

  <?php
  $api_key = get_option('wpaic_api_key', '');
  if (empty($api_key)) {
    $api_key = 'wpaic_' . wp_generate_password(32, false);
    update_option('wpaic_api_key', $api_key);
  }
  ?>

  <h3>Tu API Key</h3>
  <table class="form-table">
    <tr>
      <th>API Key</th>
      <td>
        <input type="text" value="<?php echo esc_attr($api_key); ?>" readonly style="width:400px;background:#f0f0f0;">
        <p class="description">Copia esta key. No se mostrará de nuevo.</p>
      </td>
    </tr>
    <tr>
      <th>Regenerar</th>
      <td>
        <form method="post">
          <?php wp_nonce_field('wpaic_regenerate', 'wpaic_nonce'); ?>
          <button type="submit" name="regenerate" class="button">Generar Nueva Key</button>
        </form>
      </td>
    </tr>
  </table>

  <?php
  if (isset($_POST['regenerate']) && check_admin_referrer('wpaic_regenerate', 'wpaic_nonce')) {
    $new_key = 'wpaic_' . wp_generate_password(32, false);
    update_option('wpaic_api_key', $new_key);
    echo '<div class="notice notice-success"><p>¡Nueva API Key generada!</p></div>';
    echo '<script>window.location.reload();</script>';
  }
  ?>

  <hr>
  <h3>Test Your API</h3>
  <p>Click the button below to test the connection:</p>
  <button id="wpaic-test-api" class="button button-primary">Test API Connection</button>
  <div id="wpaic-test-result" style="margin-top:10px;"></div>

  <script>
  document.getElementById('wpaic-test-api').addEventListener('click', function() {
    fetch('<?php echo get_site_url(); ?>/wp-json/wp-ai-control/v1/site-info')
      .then(r => r.json())
      .then(data => {
        document.getElementById('wpaic-test-result').innerHTML = '<div class="notice notice-success"><p>✓ API Connected! Site: ' + (data.name || 'WordPress') + '</p></div>';
      })
      .catch(err => {
        document.getElementById('wpaic-test-result').innerHTML = '<div class="notice notice-error"><p>✗ API Error: ' + err.message + '</p></div>';
      });
  });
  </script>
</div>