<?php use_stylesheet('/mpRealityAdminPlugin/css/admin.css') ?>
<?php use_stylesheet('/mpRealityAdminPlugin/css/css3.css') ?>


<div id="sf_admin_container">
  <?php if (count($categories)): ?>
  <?php foreach ($categories as $key => $category): ?>
  <?php include_partial('bloc_list', array('category' => $category)); ?>
  <?php endforeach; ?>
  <?php endif ?>

  <div style="clear: both"></div>
</div>
