<div class="list_batch_actions">
  <ul>
    <?php echo $helper->linkToNew(array('params' => array(), 'class_suffix' => 'new', 'label' => 'New',)) ?>
    <li class="sf_admin_action_export">
      <?php echo str_replace('action', '', link_to(__('Export', array(), 'messages'), 'preference_media/export', array())); ?>
    </li>
  </ul>
</div>

