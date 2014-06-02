<?php if (sfConfig::get('app_mp_admin_theme_plugin_css_reset')): // reset css ?>
  [?php use_stylesheet('<?php echo sfConfig::get('app_mp_reality_admin_plugin', '/mpRealityAdminPlugin').'/css/reset.css' ?>', 'first') ?]
<?php endif; ?>

<?php if (isset($this->params['css']) && ($this->params['css'] !== false)): // custom CSS ?>
  [?php use_stylesheet('<?php echo $this->params['css'] ?>') ?]
<?php else: // mpTheme CSS ?>
  [?php use_stylesheet('<?php echo sfConfig::get('app_mp_reality_admin_plugin', '/mpRealityAdminPlugin').'/css/admin.css' ?>') ?]
  [?php use_stylesheet('<?php echo sfConfig::get('app_mp_reality_admin_plugin', '/mpRealityAdminPlugin').'/css/ui.selectmenu.css' ?>') ?]
  [?php use_stylesheet('<?php echo sfConfig::get('app_mp_reality_admin_plugin', '/mpRealityAdminPlugin').'/css/customInput.css' ?>') ?]
  [?php use_stylesheet('<?php echo sfConfig::get('app_mp_reality_admin_plugin', '/mpRealityAdminPlugin').'/css/modalBox.css' ?>') ?]
  [?php use_stylesheet('<?php echo sfConfig::get('app_mp_reality_admin_plugin', '/mpRealityAdminPlugin').'/css/tipsy.css' ?>') ?]
  [?php use_stylesheet('<?php echo sfConfig::get('app_mp_reality_admin_plugin', '/mpRealityAdminPlugin').'/css/css3.css' ?>') ?]
<?php endif; ?>

<?php if (isset($this->params['js']) && ($this->params['js'] !== false)): //custom js ?>
[?php use_javascript('<?php echo $this->params['js'] ?>', 'first') ?]
<?php elseif(!isset($this->params['js'])): ?>
[?php use_javascript('<?php echo sfConfig::get('app_mp_reality_admin_plugin', '/mpRealityAdminPlugin').'/js/jquery-1.4.4.min.js' ?>', 'first') ?]
[?php use_javascript('<?php //echo sfConfig::get('app_mp_reality_admin_plugin', '/mpRealityAdminPlugin').'/js/jquery-ui.custom.min.js' ?>', 'first') ?]
[?php use_javascript('<?php echo sfConfig::get('app_mp_reality_admin_plugin', '/mpRealityAdminPlugin').'/js/jquery-ui-1.8.7.custom.min.js' ?>', 'first') ?]
[?php use_javascript('<?php echo sfConfig::get('app_mp_reality_admin_plugin', '/mpRealityAdminPlugin').'/js/jquery.metadata.js' ?>', 'first') ?]
[?php use_javascript('<?php //echo sfConfig::get('app_mp_reality_admin_plugin', '/mpRealityAdminPlugin').'/js/jquery.ui.widget.js' ?>', 'first') ?]
[?php use_javascript('<?php echo sfConfig::get('app_mp_reality_admin_plugin', '/mpRealityAdminPlugin').'/js/ui.selectmenu.js' ?>', 'first') ?]
[?php use_javascript('<?php echo sfConfig::get('app_mp_reality_admin_plugin', '/mpRealityAdminPlugin').'/js/customInput.jquery.js' ?>', 'first') ?]
[?php use_javascript('<?php //echo sfConfig::get('app_mp_reality_admin_plugin', '/mpRealityAdminPlugin').'/js/ui.checkbox.js' ?>', 'first') ?]
[?php use_javascript('<?php echo sfConfig::get('app_mp_reality_admin_plugin', '/mpRealityAdminPlugin').'/js/jquery.blockUI.js' ?>', 'first') ?]
[?php use_javascript('<?php echo sfConfig::get('app_mp_reality_admin_plugin', '/mpRealityAdminPlugin').'/js/jquery.tipsy.js' ?>', 'first') ?]
[?php use_javascript('<?php echo sfConfig::get('app_mp_reality_admin_plugin', '/mpRealityAdminPlugin').'/js/functions.js' ?>', 'first') ?]
[?php use_javascript('<?php echo sfConfig::get('app_mp_reality_admin_plugin', '/mpRealityAdminPlugin').'/js/fonctions.js' ?>', 'first') ?]
<?php endif; ?>

[?php // additionnal javascript (filament group)
  use_javascript('<?php //echo sfConfig::get('app_mp_reality_admin_plugin', '/mpRealityAdminPlugin').'/js/ui.core.js' ?>');
  use_javascript('<?php //echo sfConfig::get('app_mp_reality_admin_plugin', '/mpRealityAdminPlugin').'/js/jquery.bind.js' ?>');
  use_javascript('<?php //echo sfConfig::get('app_mp_reality_admin_plugin', '/mpRealityAdminPlugin').'/js/ui.checkbox.js' ?>');
  use_javascript('<?php //echo sfConfig::get('app_mp_reality_admin_plugin', '/mpRealityAdminPlugin').'/js/jquery.selectbox-0.5_style_2.js' ?>');
  use_javascript('<?php //echo sfConfig::get('app_mp_reality_admin_plugin', '/mpRealityAdminPlugin').'/js/custom_jquery.js' ?>');
  use_javascript('<?php //echo sfConfig::get('app_mp_reality_admin_plugin', '/mpRealityAdminPlugin').'/js/jquery.tooltip.js' ?>');
  use_javascript('<?php //echo sfConfig::get('app_mp_reality_admin_plugin', '/mpRealityAdminPlugin').'/js/jquery.dimensions.js' ?>');
  
?]
