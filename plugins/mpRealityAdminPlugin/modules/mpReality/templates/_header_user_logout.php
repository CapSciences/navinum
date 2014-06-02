<?php if ($sf_user->isAuthenticated() && method_exists($sf_user->getRawValue(), 'getGuardUser')) : ?>
<div id="controlpanel">
  <ul>
    <li><?php echo __('Hello', array(), 'mpRealityAdmin') ?> <strong><?php echo $sf_user->getUsername(); ?></strong> | </li>
    <li><a class="last logout" href="<?php echo url_for('@logout') ?>"><?php echo __('Logout', array(), 'mpRealityAdmin')  ?></a></li>
  </ul>
</div>
<?php endif; ?>