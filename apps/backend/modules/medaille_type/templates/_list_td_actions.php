<td class="actions_table">
  <ul class="sf_admin_td_actions">
  <?php if ($sf_user->hasPermission('admin') || $sf_user->hasPermission('commissaire')): ?>
    <?php echo $helper->linkToEdit($medaille_type, array(  'label' => 'Modifier',  'action' => 'edit',  'params' =>   array(  ),  'class_suffix' => 'edit',)) ?>
  <?php endif; ?>
  <?php if ($sf_user->hasPermission('admin')): ?>
    <?php echo $helper->linkToDeleteList($medaille_type, array(  'label' => 'Supprimer',  'action' => 'delete',  'params' =>   array(  ),  'confirm' => 'Are you sure?',  'class_suffix' => 'delete',)) ?>
  <?php endif; ?>
  </ul>
</td>