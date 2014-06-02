<?php

class symlinkVisiteursDocumentsToInteractifsTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'api'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'prod', 'prod'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'doctrine', 'doctrine'),
      // add your own options here
    ));


      //$configuration = ProjectConfiguration::getApplicationConfiguration('api' , 'prod' , false);
      //$context = sfContext::createInstance($configuration);


    $this->namespace        = 'servervip';
    $this->name             = 'symlink-visiteurs-documents-to-interactifs';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [csymlink-visiteurs-documents-to-interactifs|INFO] task symlink visiteurs documents to interactifs directory.
Call it with:

  [php symfony symlink-visiteurs-documents-to-interactifs|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {

      $databaseManager = new sfDatabaseManager($this->configuration);
      $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

      $visiteurs_dir = sfConfig::get('sf_root_dir').'/web/visiteur/';
      $interactifs_dir = sfConfig::get('sf_root_dir').'/web/interactif';
      $finder = new sfFinder;
      $fileSystem = new sfFilesystem();
      foreach($finder->in($visiteurs_dir) as $file) {
          //echo 'search in '. $file;
          if(is_file($file) && basename($file) != '.DS_Store') {
              $realpath = str_replace($visiteurs_dir, "", $file);
              $split = explode('/', $realpath);

              if(count($split) > 2){
                  //print_r($split);
                  //exit;
                  $visiteur_id = $split[0];
                  $interactif_id = $split[1];
                  $filename = basename($file);
                  //  check if symlink exist
                  if(Doctrine::getTable('Interactif')->symlinkDocument($interactif_id, $visiteur_id, $filename, $file)){
                      $this->logSection("INFO", "Symlink ".$interactifs_dir.'/'. $interactif_id . '/' . $visiteur_id . '/' . $filename);
                  }
              }
          }
      }

      $this->logSection("INFO", "END Symlink interactif directory");

  }


}
