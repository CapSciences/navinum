<?php

class resetnavinumTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'sync'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'prod', 'prod'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'doctrine', 'doctrine'),
      // add your own options here
    ));

    $this->namespace        = 'servervip';
    $this->name             = 'reset-navinum';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [reset-navinum|INFO] task does things.
Call it with:

  [php symfony reset-navinum|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

//    $visites = Doctrine_Core::getTable('Visite')->findByNavinumId();
    $q = Doctrine_Query::create()
        ->select('v.guid')
        ->from('Visite v')
        ->leftJoin('v.Rfid r')
        ->where('v.guid != \'\'')
        ->andWhere('v.navinum_id IS NOT NULL')
        ->andWhere('r.is_resettable = 1')
        ->andWhere('v.navinum_id != \'\'');
    //die($q->getSqlQuery());
    $visites =  $q->execute(null, Doctrine_Core::HYDRATE_ARRAY);
    foreach ($visites as $visite)
    {

        $q = Doctrine::getTable('Visite')
            ->createQuery()
            ->update()
            ->set('navinum_id', 'NULL')
            ->where('guid = ?', $visite['guid'])
            ->execute(null, Doctrine_Core::HYDRATE_ARRAY);


        $this->logSection('Visite', $visite['guid']);

    }
  }
}
