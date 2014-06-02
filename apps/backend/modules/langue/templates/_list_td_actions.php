<td class="actions_table">
  <?php if ($sf_user->hasPermission('admin') || $sf_user->hasPermission('commissaire')): ?>
  <ul class="sf_admin_td_actions">
    <?php echo $helper->linkToEdit($langue, array(  'label' => 'Modifier',  'action' => 'edit',  'params' =>   array(  ),  'class_suffix' => 'edit',)) ?>

    <?php echo $helper->linkToDeleteList($langue, array(  'label' => 'Supprimer',  'action' => 'delete',  'params' =>   array(  ),  'confirm' => 'Are you sure?',  'class_suffix' => 'delete',)) ?>

  </ul>
  <?php endif; ?>
</td>