<td class="actions_table">
  <?php if ($sf_user->hasPermission('admin') || $sf_user->hasPermission('commissaire')): ?>
  <ul class="sf_admin_td_actions">
    <?php echo $helper->linkToEdit($parcours, array(  'label' => 'Modifier',  'action' => 'edit',  'params' =>   array(  ),  'class_suffix' => 'edit',)) ?>    
          
    <?php echo $helper->linkToDeleteList($parcours, array(  'label' => 'Supprimer',  'action' => 'delete',  'params' =>   array(  ),  'confirm' => 'Are you sure?',  'class_suffix' => 'delete',)) ?>
     
    <?php echo $helper->linkToShow($parcours, array(  'label' => 'Voir',  'action' => 'show',  'params' =>   array(  ),  'class_suffix' => 'show',)) ?>

	<li class="sf_admin_action_duplicate">
      <?php echo link_to('Copier', 'parcours/copy?guid='.$parcours->getGuid()) ?>
    </li>

  </ul>
  <?php endif; ?>
</td>
