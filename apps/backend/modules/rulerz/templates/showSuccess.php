<?php use_helper('I18N', 'Date') ?>
<?php include_partial('rulerz/assets') ?>
<div id="sf_admin_container">


   <div id="sf_admin_content">
       <?php include_partial('rulerz/show', array('executions' => $executions, 'models' => $models, 'rulerz' => $rulerz, 'configuration' => $configuration, 'helper' => $helper)) ?>
  </div>

  <div class="clearfix"></div>
  <div class="sf_admin_actions">
      <?php include_partial('rulerz/show_actions', array('rulerz' => $rulerz, 'configuration' => $configuration, 'helper' => $helper)) ?>
  </div>
</div>
