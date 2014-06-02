<?php

/**
 * mpRealtyAdminPlugin configuration.
 * 
 * @package     mpRealtyAdminPlugin
 * @subpackage  config
 * @author      Mike Plavonil <mikeplavo@gmail.com>
 */
class mpRealtyAdminPluginConfiguration extends sfPluginConfiguration
{
  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
      if (!sfConfig::get('app_mpRealtyAdminPlugin')) {
          throw new sfException('Options manquantes pour mpRealtyAdminPlugin');
      }
  }
}
