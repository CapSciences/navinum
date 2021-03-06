<?php

/**
 * LogVisite
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    sf_sandbox
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class LogVisite extends BaseLogVisite
{
  public function getExposition()
  {
    return $this->getVisite()->getExposition();
  }

  public function getExpositionContexte()
  {
    return $this->getExposition()->getContexte();
  }

  public function getExpositionOrganisateurDiffuseur()
  {
    return $this->getExposition()->getOrganisateurDiffuseur();
  }

  public function getExpositionOrganisateurEditeur()
  {
    return $this->getExposition()->getOrganisateurEditeur();
  }

  public function getParcours()
  {
    return $this->getVisite()->getParcours();
  }

  public function getVisiteurCreatedAt()
  {
    return $this->getVisiteur()->getCreatedAt();
  }

  public function getVisiteurContexte()
  {
    return $this->getVisiteur()->getContexte();
  }

  public function getVisiteurCodeLangue()
  {
    return $this->getVisiteur()->getCodeLangue();
  }

  public function getVisiteurGenre()
  {
    return $this->getVisiteur()->getGenre();
  }

  public function getVisiteurDateNaissance()
  {
    return $this->getVisiteur()->getDateNaissance();
  }

  public function getVisiteurCodePostal()
  {
    return $this->getVisiteur()->getCodePostal();
  }

  public function getVisiteurPseudoSon()
  {
    return $this->getVisiteur()->getPseudoSon();
  }

  public function getVisiteurPreferenceMedia()
  {
    $preference_medias = $this->getVisiteur()->getPreferenceMedia();
    $libelle = '';
    foreach ($preference_medias as $media)
    {
      $libelle .= $media->getLibelle().', ';
    }
    return $libelle;
  }

  public function save(Doctrine_Connection $conn = null)
  {
    $this->setIsTosync(1);

    if($this->getInteractifId() != null && $this->getInteractif()->getGuid() != '' && $this->getInteractif()->getLibelle())
    {
      $this->setInteractifLibelle($this->getInteractif()->getLibelle());
    }
    parent::save($conn);
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
}
