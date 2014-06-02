<?php

class purgeBaseTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    $this->addArguments(array(
      new sfCommandArgument('type', sfCommandArgument::REQUIRED, 'type'),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'backend'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'prod', 'prod'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'doctrine', 'doctrine'),
      // add your own options here
    ));

    $this->namespace        = 'servervip';
    $this->name             = 'purge-base';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [purge-base|INFO] task purge the table log_visite for empty rows.
Call it with:

  [php symfony purge-base|INFO] [TYPE] where type is ['log_visite' || 'visiteur']
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    switch ($arguments['type']) {
      case 'log_visite':
        $q = Doctrine_Query::create()
            ->delete()
            ->from('LogVisite lv')
            ->where('lv.resultats IS NULL OR lv.resultats = "" OR lv.resultats = "vide" OR lv.resultats = "0" OR lv.resultats = "000000000"');
        $log_visites =  $q->execute();
        $this->logSection('Log visite deleted', $log_visites);
        break;

      case 'visiteur':
        $q = Doctrine_Query::create()
            ->delete()
            ->from('Visiteur v')
            ->where('v.email IS NULL OR v.email = "" OR v.pseudo_son = ""');
        $visiteurs =  $q->execute();
        $this->logSection('Visiteur deleted', $visiteurs);
        break;

      default:
        $this->logSection('Type', "Unknown ...");
        break;
    }

  }
}
