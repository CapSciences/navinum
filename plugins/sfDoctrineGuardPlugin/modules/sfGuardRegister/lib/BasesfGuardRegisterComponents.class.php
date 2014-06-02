<?php

class BasesfGuardRegisterComponents extends sfComponents
{
  public function executeForm()
  {
    $class = sfConfig::get('app_sf_guard_plugin_register_form', 'sfGuardRegisterForm');
    $this->form = new $class();
  }
}
