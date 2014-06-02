<td class="actions_table">
  <?php if ($sf_user->hasPermission('admin')): ?>
  <ul class="sf_admin_td_actions">
    <?php echo $helper->linkToEdit($visiteur, array(  'label' => 'Modifier',  'action' => 'edit',  'params' =>   array(  ),  'class_suffix' => 'edit',)) ?>
    <?php echo $helper->linkToDeleteList($visiteur, array(  'label' => 'Supprimer',  'action' => 'delete',  'params' =>   array(  ),  'confirm' => 'Are you sure?',  'class_suffix' => 'delete',)) ?>
  </ul>
  <?php endif; ?>
</td>