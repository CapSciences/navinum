<?php

/**
 * RfidGroupeVisiteur
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    sf_sandbox
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class RfidGroupeVisiteur extends BaseRfidGroupeVisiteur
{
	public function createAnonymousVisitor($contexte_creation_id)
    {
      try
      {
      	// selection des rfids
      	$rfids = Doctrine_Query::create()
    	->from("Rfid r")
    	->where("r.groupe_id = ?", $this->getRfidGroupeId())
    	->execute(array(), Doctrine::HYDRATE_ARRAY);

    	if(!empty($rfids))
    	{
	    	// creation d'une nouvelle visite
		  	//$collectionVisiteur = new Doctrine_Collection('Visiteur');
        //$collectionVisite = new Doctrine_Collection('Visite');

	        foreach($rfids as $rfid)
	        {

	            $visiteur = new Visiteur();
	            $visiteur = $visiteur->createAnonymous($contexte_creation_id);
              $visiteur->save();


                //$collectionVisiteur->add($visiteur);

              $visite = new Visite();
              $visite->setGuid(Guid::generate());
              $visite->setGroupeId($this->guid);
              $visite->setNavinumId($rfid['uid']);
              $visite->setVisiteurId($visiteur->getGuid());
              $visite->save();
              //$collectionVisite->add($visite);
	        }
	        //$collectionVisite->save();
          //$collectionVisiteur->save();
		}

      }
      catch(Exception $e){
		throw new sfException($e->getMessage());
      }
    }

  public function  __toString() {
     return $this->getNom() ? $this->getNom() : '';
  }

  public function __call($method, $arguments)
  {
    if($method == 'setGuid')
    {
      return $this->set('guid', Guid::generate());
    }
    else
    {
      return parent::__call($method, $arguments);
    }
  }

    public function save(Doctrine_Connection $conn = null)
    {
        if($this->isNew())
        {
            $this->createDataFolder();
        }
        $this->setIsTosync(1);
        parent::save($conn);
    }

    public function getGroupeVisiteurDataPath()
    {
        return sfConfig::get('sf_web_dir')."/groupe_visiteur/".$this->guid;
    }

    public function createDataFolder($dir = "")
    {
        $fileSystem = new sfFilesystem();
        $oldumask = umask(0);
        $fileSystem->mkdirs($this->getGroupeVisiteurDataPath(), 0755);
        $fileSystem->chmod($this->getGroupeVisiteurDataPath(), 0755);
        umask($oldumask);

        if($dir != ''){
            $oldumask = umask(0);
            $fileSystem->mkdirs($this->getGroupeVisiteurDataPath() . '/' . $dir, 0755);
            $fileSystem->chmod($this->getGroupeVisiteurDataPath() . '/' . $dir, 0755);
            umask($oldumask);
        }


    }

  public function  delete(Doctrine_Connection $conn = null)
  {
    $guid = $this->getGuid();

    parent::delete($conn);

    $delete_log = new DeleteLog();
    $delete_log->setGuid($guid);
    $delete_log->setModelName(get_class($this));
    $delete_log->save();
  }


}
