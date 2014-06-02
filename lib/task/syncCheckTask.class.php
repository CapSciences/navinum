<?php

class syncCheckTask extends sfBaseTask
{
  protected function configure()
  {

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'sync', 'sync'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'prod', 'prod'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'doctrine', 'doctrine'),
    ));

    $this->namespace        = 'servervip';
    $this->name             = 'syncCheck';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [syncCheck|INFO] task does things.
Call it with:

  [php symfony syncCheck|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_VERBOSE, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $this->logSection("syncCheck", '== Checking sync with '.sfConfig::get('app_sync_internet_url') . ' ==') ;
    curl_setopt($ch, CURLOPT_URL, sfConfig::get('app_sync_internet_url').'/default/syncCheck');
    curl_setopt($ch, CURLOPT_POST, 0);
    $results = curl_exec ($ch);
    curl_close($ch);
    $array_results = explode('&', $results);
    foreach($array_results as $result)
    {
    	$tables = explode('=', $result);
    	$count = Doctrine_Query::create()
                       ->from($tables[0].' t')
                       ->count();
      $str = str_pad($tables[0], 50) . ' Expected : '.str_pad($tables[1], 4).' | Got : '.$count;
//      if($tables[1] == $count)$str .= 'OK'; else $str .= 'NOK';
      $this->logSection('syncCheck', $str, null, ($tables[1] == $count)?'INFO': 'ERROR');
//      echo "\n";
    }    
  }
}
