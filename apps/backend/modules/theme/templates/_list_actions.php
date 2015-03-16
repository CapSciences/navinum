<div class="list_batch_actions">
  <?php if ($sf_user->hasPermission('admin') || $sf_user->hasPermission('commissaire')): ?>
  <ul>
    <?php echo $helper->linkToNew(array('params' => array(), 'class_suffix' => 'new', 'label' => 'New',)) ?>
    <li class="sf_admin_action_export">
      <?php echo str_replace('action', '', link_to(__('Export', array(), 'messages'), 'theme/export', array())); ?>
    </li>
  </ul>
  <?php endif; ?>
</div>

