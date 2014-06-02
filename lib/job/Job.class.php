<?php
require_once dirname(__FILE__)."/../caNotificationsTools.class.php";

abstract class Job {

  private $visiteur = null;
  private $visiteur_id = null;

  const TYPOLOGIE_1 = "TYPOLOGIE_1";
  const TYPOLOGIE_2 = "TYPOLOGIE_2";
  const TYPOLOGIE_3 = "TYPOLOGIE_3";

  /* LOG_VISITE */
  const LOG_VISITE_1 = "LOG_VISITE_1";
  const LOG_VISITE_2 = "LOG_VISITE_2";
  const LOG_VISITE_3 = "LOG_VISITE_3";
  const LOG_VISITE_4 = "LOG_VISITE_4";
  const LOG_VISITE_5 = "LOG_VISITE_5";
  const LOG_VISITE_6 = "LOG_VISITE_6";
  const LOG_VISITE_7 = "LOG_VISITE_7";
  const LOG_VISITE_8 = "LOG_VISITE_8";

  const EVENT_1 = "EVENT_1";

  private static $notifications = array(
  	self::TYPOLOGIE_1 => array("title" => "Champion", "message" => "Tu as %s XP !!!!", "dest" => "visiteur_id:%s"),
  	self::TYPOLOGIE_2 => array("title" => "Champion", "message" => "Tu as %s dans la typo %s", "dest" => "visiteur_id:%s"),
  	self::TYPOLOGIE_3 => array("title" => "Champion", "message" => "Grâce à toi, Cap Sciences a atteint %s XP !!!!!", "dest" => "visiteur_id:%s"),
  	self::LOG_VISITE_1 => array("title" => "LogVisite", "message" => "tu as visité une des expositions concernés", "dest" => "visiteur_id:%s"),
  	self::LOG_VISITE_2 => array("title" => "LogVisite", "message" => "tu as visité un ensemble d'expositions prédéfini", "dest" => "visiteur_id:%s"),
  	self::LOG_VISITE_3 => array("title" => "LogVisite", "message" => "tu as été dans %s lieux de diffusions", "dest" => "visiteur_id:%s"),
  	self::LOG_VISITE_4 => array("title" => "LogVisite", "message" => "tu as été dans les lieux d'expositions prédéfinis", "dest" => "visiteur_id:%s"),
  	self::LOG_VISITE_5 => array("title" => "LogVisite", "message" => "Meilleur score battu OOOH!", "dest" => "visiteur_id:%s"),
  	self::LOG_VISITE_6 => array("title" => "LogVisite", "message" => "tu viens de battre le meilleur score clap clap", "dest" => "visiteur_id:%s"),
  	self::LOG_VISITE_7 => array("title" => "LogVisite", "message" => "Multi plateforme !!!", "dest" => "visiteur_id:%s"),
  	self::LOG_VISITE_8 => array("title" => "LogVisite", "message" => "temps de connexion > 3 H", "dest" => "visiteur_id:%s"),
    self::EVENT_1 => array("title" => "Event", "message" => "Tu as participé à %s", "dest" => "visiteur_id:%s"),
  );

  public abstract function perform();

  public function getSettings($notification_id)
  {
  	return self::$notifications[$notification];
  }

  public function getMessage($notification_id)
  {
  	return self::$notifications[$notification_id]['message'];
  }

  public function getTitle($notification_id)
  {
  	return self::$notifications[$notification_id]['title'];
  }

  public function getDest($notification_id)
  {
  	$dest = Job::$notifications[$notification_id]['dest'];
  	$result = split(":", $dest);

  	switch ($result[0]) {
  		case 'visiteur_id':
  			$dest = sprintf($dest, $this->getVisiteurId());
  			break;
  	}
  	return $dest;
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
  	return $this->args["visiteur_id"];
  }

  protected function notify($title, $message, $dest,  $type = 'general:notif')
  {

    return caNotificationsTools::getInstance()->sendNotification($type, $dest, array(
       'title' => $title,
       'message' => $message
   ));

  return caNotificationsTools::getInstance()->sendNotification('general:notif', 'visiteur:'.$this->getVisiteurId(), array(
'title' => $title,
'message' => $message
));

  }

  protected function notifyGeneral($title, $message, $dest)
  {
    return $this->notify($title, $message, $dest);
  }
}