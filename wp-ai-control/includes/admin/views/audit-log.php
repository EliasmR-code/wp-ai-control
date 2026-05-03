<div class="wrap">
  <h2>Audit Log</h2>
  <p>Recent API activity (retention: <?php echo get_option('wpaic_audit_retention_days', WPAIC_AUDIT_RETENTION_DEFAULT); ?> days)</p>

  <?php
  $page = isset($_GET['log_page']) ? absint($_GET['log_page']) : 1;
  $logs = WPAIC_Audit::get_logs(20, $page);

  if ($logs['logs']) :
  ?>
  <table class="wp-list-table widefat fixed striped">
    <thead><tr><th>Date</th><th>Action</th><th>Type</th><th>Object ID</th><th>IP</th><th>Details</th></tr></thead>
    <tbody>
    <?php foreach ($logs['logs'] as $log) : ?>
      <tr>
        <td><?php echo esc_html($log->created_at); ?></td>
        <td><?php echo esc_html($log->action); ?></td>
        <td><?php echo esc_html($log->object_type ?: '-'); ?></td>
        <td><?php echo $log->object_id ? esc_html($log->object_id) : '-'; ?></td>
        <td><?php echo esc_html($log->ip_address ?: '-'); ?></td>
        <td><?php echo esc_html(substr($log->details, 0, 100)); ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <div class="tablenav">
    <div class="tablenav-pages">
      <?php if ($page > 1) : ?>
        <a href="?page=wp-ai-control&tab=audit-log&log_page=<?php echo $page-1; ?>" class="button">Previous</a>
      <?php endif; ?>
      <span class="displaying-num">Page <?php echo $page; ?> of <?php echo $logs['total_pages']; ?></span>
      <?php if ($page < $logs['total_pages']) : ?>
        <a href="?page=wp-ai-control&tab=audit-log&log_page=<?php echo $page+1; ?>" class="button">Next</a>
      <?php endif; ?>
    </div>
  </div>
  <?php else : ?>
    <p>No audit log entries found.</p>
  <?php endif; ?>
</div>
