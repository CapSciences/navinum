<td class="actions_table">
  <ul class="sf_admin_td_actions">
  <?php if ($sf_user->hasPermission('admin')): ?>
    <?php echo $helper->linkToEdit($visiteur_medaille, array(  'label' => 'Modifier',  'action' => 'edit',  'params' =>   array(  ),  'class_suffix' => 'edit',)) ?>
  <?php endif; ?>
  <?php if ($sf_user->hasPermission('admin')): ?>
    <?php echo $helper->linkToDeleteList($visiteur_medaille, array(  'label' => 'Supprimer',  'action' => 'delete',  'params' =>   array(  ),  'confirm' => 'Are you sure?',  'class_suffix' => 'delete',)) ?>
  <?php endif; ?>
  </ul>
</td>