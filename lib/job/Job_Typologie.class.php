<?php

require_once dirname(__FILE__)."/../caNotificationsTools.class.php";

/**
 * Typologie Notification job
 * use it with : Resque::enqueue('default', 'Job_Typologie', array("visiteur_id" => "GUID"));
 */
class Job_Typologie extends Job
{
  public function perform()
  {
  	$this->notifyToTalAllTypologiesMaxByVisiteur();
  	$this->notifyToTalCapSciencesMax();
  	$this->notifyToTalTypologieMaxByVisiteur();
  }

 protected function notifyToVisiteur($title, $message)
 {
  var_dump("NOTIFY".$message);
  return caNotificationsTools::getInstance()->sendNotification('general:notif', 'visiteur:'.$this->getVisiteurId(), array(
    'title' => $title,
    'message' => $message
  ));
}

  protected function notifyToTalAllTypologiesMaxByVisiteur()
  {
	  $visiteur = $this->getVisiteur();
  	$visiteur_total_xp = $visiteur->getTotalXp();
  	$config_typologies_max = sfConfig::get("app_typologies_max");

  	if($visiteur_total_xp > $config_typologies_max)
  	{
      $message = sprintf($this->getMessage(self::TYPOLOGIE_1), $visiteur_total_xp);
      $this->notifyToVisiteur($this->getTitle(self::TYPOLOGIE_1), $message, $this->getDest(self::TYPOLOGIE_1));
  	}
  	else
  	{
  		var_dump(sprintf("rien a faire %s < %s" , $visiteur_total_xp, $config_typologies_max));
  	}
  }

  protected function notifyToTalTypologieMaxByVisiteur()
  {
  	$config_typologies_max = sfConfig::get("app_typologies_to_notify");

    $visiteur_id = $this->getVisiteurId();
    $visiteur = $this->getVisiteur();
  	foreach($config_typologies_max as $guid => $max)
  	{
  		$visiteur_score_by_typlogie = XpTable::getInstance()->getTotalScoreByTypologieAndVisiteur($guid, $visiteur_id);
  		if($visiteur_score_by_typlogie > $max)
    		{
    			$message = sprintf("Tu as %s dans la typo %s", $visiteur_score_by_typlogie, $guid);
          $this->notifyToVisiteur("Champion", $message);
    		}
    		else
    		{
    			var_dump(sprintf("rien a faire %s < %s" , $visiteur_score_by_typlogie, $max));
    		}
  	}
  }

  protected function notifyToTalCapSciencesMax()
  {
  	$capscience_total_score = XpTable::getInstance()->getTotalScore();
  	$capscience_max_score = sfConfig::get("app_capsciences_max");
	  $visiteur_id = $this->getVisiteurId();

  	if($capscience_total_score > $capscience_max_score)
  	{
  		$message = sprintf("Grace à toi, Cap Sciences a atteint %s XP !!!!!", $capscience_total_score);
      $this->notifyToVisiteur("Champion", $message);
  	}
  	else
  	{
  		var_dump(sprintf("rien a faire %s < %s" , $capscience_total_score, $capscience_max_score));
  	}
  }


}