<td class="actions_table">
<?php if ($sf_user->hasPermission('admin') || $sf_user->hasPermission('commissaire')): ?>
  <ul class="sf_admin_td_actions">
    <?php echo $helper->linkToEdit($exposition_visiteur_needs, array(  'label' => 'Modifier',  'action' => 'edit',  'params' =>   array(  ),  'class_suffix' => 'edit',)) ?>    
          
    <?php echo $helper->linkToDeleteList($exposition_visiteur_needs, array(  'label' => 'Supprimer',  'action' => 'delete',  'params' =>   array(  ),  'confirm' => 'Are you sure?',  'class_suffix' => 'delete',)) ?>
        
  </ul>
 <?php endif; ?>
</td>
