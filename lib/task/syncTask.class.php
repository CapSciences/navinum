<?php

class syncTask extends sfBaseTask
{
  protected function configure()
  {

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'sync', 'sync'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'prod', 'prod'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'doctrine', 'doctrine'),
      new sfCommandOption('include', null, sfCommandOption::PARAMETER_OPTIONAL, 'liste des modèles que l\'on veut synchroniser ', ''),
      new sfCommandOption('exclude', null, sfCommandOption::PARAMETER_OPTIONAL, 'tous les modèles sauf ceux listés', ''),
    ));

    $this->namespace        = 'servervip';
    $this->name             = 'sync';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [sync|INFO] task does things.
Call it with:

  [php symfony sync|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
		/*
    if($options['include'] != "" && $options['exclude'] != "")
    {
      throw new Exception("Only one option is authorized");
    }
    */
    if($options['include'] != "")
    {
      $to_replace = split(",", $options['include']);
      sfConfig::set('app_sync_tables', $to_replace);
    }

    if($options['exclude'] != "")
    {
      $tables = sfConfig::get('app_sync_tables');
      $excludes = split(",",$options['exclude']);
      foreach($excludes as $exclude)
      {
        if(($key = array_search($exclude, $tables)) !== false) {
              unset($tables[$key]);
        }
      }
      sfConfig::set('app_sync_tables', $tables);
    }

    $sync = new ServerVipSync($this->configuration);
    $sync->startSync();
  }
}
