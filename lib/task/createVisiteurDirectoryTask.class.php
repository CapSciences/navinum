<?php

class createVisiteurDirectoryTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'backend'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'prod', 'prod'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'doctrine', 'doctrine'),
      // add your own options here
    ));

    $this->namespace        = 'servervip';
    $this->name             = 'create-visiteur-directory';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [create-user-media|INFO] task create the directory for user media.
Call it with:

  [php symfony create-user-media|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $this->logSection('Create directory', "Visiteur");

    $q = Doctrine_Query::create()
      ->select('guid')
      ->from('Visiteur v')
      ->where("created_at > ?", "2013-12-01 00:00:00");
    $visiteurs =  $q->execute();

    foreach($visiteurs as $visiteur)
    {
        if(!file_exists($visiteur->getVisiteurDataPath())){
            $visiteur->createDataFolder();
            $this->logSection('Create directory for visiteur '.$visiteur->getGuid(), "Visiteur");
        }

    }

  }


}
