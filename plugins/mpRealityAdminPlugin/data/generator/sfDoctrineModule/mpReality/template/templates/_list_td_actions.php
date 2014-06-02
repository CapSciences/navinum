<td class="actions_table">
  <ul class="sf_admin_td_actions">
<?php foreach ($this->configuration->getValue('list.object_actions') as $name => $params): ?>
<?php if ('_delete' == $name): ?>
    <?php echo $this->addCredentialCondition('[?php echo $helper->linkToDeleteList($'.$this->getSingularName().', '.$this->asPhp($params).') ?]', $params) ?>

<?php elseif ('_edit' == $name): ?>
    <?php echo $this->addCredentialCondition('[?php echo $helper->linkToEdit($'.$this->getSingularName().', '.$this->asPhp($params).') ?]', $params) ?>
    
  <?php elseif ('_show' == $name): ?>
    <?php echo $this->addCredentialCondition('[?php echo $helper->linkToShow($'.$this->getSingularName().', '.$this->asPhp($params).') ?]', $params) ?>
      
 
    <?php else: ?>
        <li class="sf_admin_action_<?php echo $params['class_suffix'] ?>">
      [?php if (method_exists($helper, 'linkTo<?php echo ucfirst(ltrim($name, '_')) ?>')): ?>
        <?php echo $this->addCredentialCondition('[?php echo $helper->linkTo'.ucfirst(ltrim($name, '_')).'($'.$this->getSingularName().', '.$this->asPhp($params).') ?]', $params) ?>
      [?php else: ?]
        <?php echo $this->addCredentialCondition($this->getLinkToAction($name, $params, true), $params) ?>
      [?php endif; ?]
        </li>
    <?php endif; ?>
        
<?php endforeach; ?>
  </ul>
</td>
