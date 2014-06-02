<?php

class createUserMediaTask extends sfBaseTask
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
    $this->name             = 'create-user-media';
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
      ->from('Visiteur v');
    $visiteurs =  $q->execute();

    foreach($visiteurs as $visiteur)
    {
      $visiteur->createDataFolder();
    }

    $this->logSection('Create directory', "Interactif");

    $q = Doctrine_Query::create()
      ->from('Interactif i');
    $interactifs =  $q->execute();

    foreach($interactifs as $interactif)
    {
      $interactif->createDataFolder();
    }


      $this->logSection('Create directory', "Exposition");

      $q = Doctrine_Query::create()
          ->from('Exposition v');
      $expositions =  $q->execute();

      foreach($expositions as $exposition)
      {
          $exposition->createDataFolder();
      }

      $this->logSection('Create directory', "Medaille");
      $fileSystem = new sfFilesystem();
      $fileSystem->mkdirs(sfConfig::get('sf_web_dir')."/medaille");

      $this->logSection('Create directory', "MedailleType");
      $fileSystem = new sfFilesystem();
      $fileSystem->mkdirs(sfConfig::get('sf_web_dir')."/medaille_type");


  }


}
