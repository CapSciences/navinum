[?php use_helper('I18N', 'Date') ?]
[?php include_partial('<?php echo $this->getModuleName() ?>/assets') ?]
<div id="sf_admin_container">
  <h1>[?php echo <?php echo $this->getI18NString('show.title') ?> ?]</h1>

   <div id="sf_admin_content">
      [?php include_partial('<?php echo $this->getModuleName() ?>/show', array('form' => $form, '<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>, 'configuration' => $configuration, 'helper' => $helper)) ?]
  </div>
      
  <div class="clearfix"></div>
  <div class="sf_admin_actions">
      [?php include_partial('<?php echo $this->getModuleName() ?>/show_actions', array('form' => $form, '<?php echo $this->getSingularName() ?>' => $<?php echo $this->getSingularName() ?>, 'configuration' => $configuration, 'helper' => $helper)) ?]
  </div>  
</div>
