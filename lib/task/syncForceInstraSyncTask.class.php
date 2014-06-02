<?php

class syncForceInstraSyncTask extends sfBaseTask
{
  protected function configure()
  {

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'sync', 'sync'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'prod', 'prod'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'doctrine', 'doctrine'),
      new sfCommandOption('date', null, sfCommandOption::PARAMETER_REQUIRED, 'à partir de yyyy-mm-dd mm:hh:ss', ''),
    ));

    $this->namespace        = 'servervip';
    $this->name             = 'syncForceIntra';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [syncCheck|INFO] task does things.
Call it with:

  [php symfony syncForceIntra|INFO]
EOF;
  }




  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();


      if(sfConfig::get('app_sync_type') != 'intranet'){
          throw new sfException('La synchronisation ne peut être lancée que depuis un intranet.');
      }

      $date = $options['date'];
       if(!preg_match('/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/', $date)){
        throw new sfException('Bad Format date. Should be --date=yyyy-mm-dd mm:hh:ss');
       }

    $tables = sfConfig::get('app_sync_tables');
    foreach ($tables as $table)
    {
      	$q = Doctrine_Query::create()
                        ->update($table)
                        ->set('is_tosync', 1)
                        ->where('created_at > ?', $date)
                        ->execute(array(), Doctrine::HYDRATE_ARRAY);
       $this->logSection('syncForceIntra', "update $table", null, 'INFO');


    }    
  }
}
