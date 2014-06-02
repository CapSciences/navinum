<?php if ($actions = $this->configuration->getValue('list.actions')): ?>
<?php if (isset($actions["_new"])): ?>
<span class="f_right new-record"><?php echo $this->addCredentialCondition('[?php echo $helper->linkToNew('.$this->asPhp($actions["_new"]).') ?]', $actions["_new"])."\n" ?></span>
<div class="clearfix"></div>
<?php endif; ?>
<?php endif; ?>
