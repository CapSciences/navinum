<?php

require_once dirname(__FILE__)."/../caNotificationsTools.class.php";

/**
 * LogVisite Notification job
 * use it with : Resque::enqueue('default', 'Job_LogVisite', array("exposition_id" => "GUID"));
 */
class Job_LogVisite extends Job
{
  private $log_visite = null;
  private $visiteur = null;
  private $exposition = null;

  protected function notifyToVisiteur($title, $message)
  {
    var_dump(sprintf("NOTIFY %s : %s : %s",$title, $message, $this->getVisiteurId()));
    return caNotificationsTools::getInstance()->sendNotification('general:notif', 'visiteur:'.$this->getVisiteurId(), array(
       'title' => $title,
       'message' => $message
   ));

  }
  protected function notifyToInteractif($title, $message)
  {
    var_dump(sprintf("NOTIFY %s : %s : %s",$title, $message, $this->getVisiteurId()));
    return caNotificationsTools::getInstance()->sendNotification('general:notif', 'interactif:'.$this->getInteractifId(), array(
       'title' => $title,
       'message' => $message
   ));
  }

  public function perform()
  {
  	$this->notifyFirstLogVisiteExposition();
    $this->notifyBestScore();
    $this->notifyMultiPlateforme();
    $this->notifyEvent();
  }

  protected function notifyFirstLogVisiteExposition()
  {
    $count_log_visite = LogVisiteTable::getInstance()->countLogVisiteByExposition($this->getExpositionId());

  	if($count_log_visite == 1)
  	{
      $message = sprintf($this->getMessage(self::LOG_VISITE_1), $this->getExpositionId());
      $this->notifyToVisiteur($this->getTitle(self::LOG_VISITE_1), $message);
  	}
  	else
  	{
  		var_dump(sprintf("rien a faire notifyFirstLogVisiteExposition %s", $count_log_visite ));
  	}
  }

  protected function notifyBestScore()
  {
    $log_visite_highscore = LogVisiteTable::getInstance()->getHighScoreByInteractif($this->getInteractifId());
    $log_visite = $this->getLogVisite();

    /* TODO -> voir plus de précision*/
    if($log_visite->getScore() == $log_visite_highscore['highscore'] && $log_visite->getVisiteurId() != $log_visite_highscore['visiteur_id'])
    {
      $message = sprintf($this->getMessage(self::LOG_VISITE_5), $this->getExpositionId());
      $this->notifyToInteractif($this->getTitle(self::LOG_VISITE_5), $message);
    }
    else
    {
      var_dump(sprintf("rien a faire notifyBestScore %s : %s : %s : " , $log_visite->getScore(), $log_visite_highscore['highscore'], $log_visite->getVisiteurId()));
    }

    if($log_visite->getGuid() == $log_visite_highscore['guid'])
    {
      $message = sprintf($this->getMessage(self::LOG_VISITE_6), $this->getExpositionId());
      $this->notifyToVisiteur($this->getTitle(self::LOG_VISITE_6), $message);
    }
    else
    {
      var_dump(sprintf("rien a faire notifyBestScore %s : %s : %s : " , $log_visite->getGuid(), $log_visite_highscore['guid'], $log_visite->getVisiteurId()));
    }
  }

  protected function notifyMultiPlateforme()
  {
    $count_log_visite = LogVisiteTable::getInstance()->hasLogVisiteMultiPlateforme($this->getInteractifId());

    if($count_log_visite == true)
    {
      $message = sprintf($this->getMessage(self::LOG_VISITE_7), $this->getExpositionId());
      $this->notifyToVisiteur($this->getTitle(self::LOG_VISITE_7), $message);
    }
    else
    {
      var_dump(sprintf("rien a faire notifyMultiPlateforme %s", $count_log_visite ));
    }
  }

  protected function notifyEvent()
  {

    if($this->getExposition() != null)
    {
      $evenements = $this->getExposition()->getEvenement();
      foreach($evenements as $evenement)
      {
        if($evenement->getStartAt() < $this->getLogVisite()->getCreatedAt() && ($evenement->getEndAt() == null || $evenement->getEndAt() == ""))
        {
            $message = sprintf($this->getMessage(self::EVENT_1), $this->getExposition()->getLibelle());
            $this->notifyToVisiteur($this->getTitle(self::EVENT_1), $message);
        }
        else
        {
          if($evenement->getStartAt() < $this->getLogVisite()->getCreatedAt() && $evenement->getEndAt() != null && $evenement->getEndAt() > $this->getLogVisite()->getCreatedAt())
          {
            $message = sprintf($this->getMessage(self::EVENT_1), $this->getExposition()->getLibelle());
            $this->notifyToVisiteur($this->getTitle(self::EVENT_1), $message);
          }
          else
          {
            var_dump(sprintf("rien a faire notifyEvent"));
          }
        }
      }
    }
    else
    {
      var_dump(sprintf("rien a faire notifyEvent"));
    }
  }

  protected function getExpositionId()
  {
    if($this->getLogVisite())
    {
      return $this->getLogVisite()->getExpositionId();
    }
    return null;
  }

  protected function getExposition()
  {
    if($this->getLogVisite() && $this->exposition == null)
    {
      return $this->getLogVisite()->getExposition();
    }
    return $this->exposition;
  }


  protected function getVisiteur()
  {
    if($this->visiteur == null)
    {
      $visiteur_id = $this->args["visiteur_id"];
      $this->visiteur = Doctrine_Core::getTable('Visiteur')->findOneByGuid($visiteur_id);
    }

    return $this->visiteur;
  }

  protected function getVisiteurId()
  {
    return $this->getLogVisite()->getVisiteurId();
  }

  protected function getInteractifId()
  {
    if($this->getLogVisite())
    {
      return $this->getLogVisite()->getInteractifId();
    }
    return null;
  }

  protected function getLogVisiteId()
  {
    return $this->args["log_visite_id"];
  }

  protected function getLogVisite()
  {

    if($this->log_visite == null)
    {
      $this->log_visite = Doctrine_Core::getTable('LogVisite')->findOneByGuid($this->getLogVisiteId());
    }

    return $this->log_visite;
  }
}