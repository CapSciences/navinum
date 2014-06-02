<?php

require_once dirname(__FILE__).'/../lib/vendor/symfony/lib/autoload/sfCoreAutoload.class.php';
sfCoreAutoload::register();
require_once dirname(__FILE__).'/../lib/vendor/vendor/autoload.php';

class ProjectConfiguration extends sfProjectConfiguration
{
  public function setup()
  {
    $this->enablePlugins(array(
      'sfDoctrinePlugin',
      'sfDoctrineGuardPlugin',
      'sfFormExtraPlugin',
      'sfDoctrineRestGeneratorPlugin', 
      'mpRealityAdminPlugin',
      'sfMySQLDumpPlugin',
      'sfResquePlugin'
    ));
  }
  
  public function configureDoctrine(Doctrine_Manager $manager)
{
  $manager->setCollate('utf8_unicode_ci');
  $manager->setCharset('utf8');
    //$manager->setAttribute(Doctrine::ATTR_QUERY_CACHE, new Doctrine_Cache_Apc());
}
}
