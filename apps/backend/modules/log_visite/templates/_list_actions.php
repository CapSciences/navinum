<div class="list_batch_actions">
  <?php if ($sf_user->hasPermission('admin') || $sf_user->hasPermission('commissaire')): ?>
  <ul>
    <li class="sf_admin_action_export">
      <?php echo str_replace('action', '', link_to(__('Export', array(), 'messages'), 'log_visite/export', array())); ?>
    </li>
  </ul>
  <?php endif; ?>
</div>
