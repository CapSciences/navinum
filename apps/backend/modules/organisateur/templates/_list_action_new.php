<?php if ($sf_user->hasPermission('admin') || $sf_user->hasPermission('commissaire')): ?>
<span class="f_right new-record"><?php echo $helper->linkToNew(array(  'params' =>   array(  ),  'class_suffix' => 'new',  'label' => 'New',)) ?>
</span>
<?php endif; ?>
<div class="clearfix"></div>
