<?php

class caRulerzTools
{

    private static $instance;

    /**
     * @return caRulerzTools
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function dispatchRulez($eventName, sfDoctrineRecord $entity)
    {
        $query = Doctrine_Query::create();
        $rules = $query->from('Rulerz r')->leftJoin('r.Listeners l')->where('l.event = ?', array($eventName))->execute();

        foreach ($rules as $rule) {
            $this->executeRule($rule, $entity, $eventName);
        }
    }

    private function executeRule(Rulerz $rule, sfDoctrineRecord $entity, $eventName)
    {
        $execution = new RulerzExecution();
        $execution->setRulerz($rule);
        $execution->setEvent($eventName);
        $execution->setEntityUid($entity->getPrimaryKey());

        try {
            ob_start();
            $lua = new Lua();
            $lua->assign('entity', $entity->toArray());

            $this->registerLuaApi($lua);

            $result = $lua->eval($rule->getAction());
            $print = ob_get_contents();
            ob_end_clean();
            $execution->setStatus('SUCCESS');
            $execution->setExecutionData(sprintf("%s\n\nResult : %s", $print, $result));
        } catch (Exception $e) {
            $execution->setStatus('FAILED');
            $execution->setExecutionData($e->__toString());
        }

        $execution->save();
    }

    private function registerLuaApi(Lua $lua)
    {
        // Map getEntity
        $lua->registerCallback('getEntity', function ($type, $guid) {
            $objectTable = call_user_func(array($type . 'Table', 'getInstance'));
            $object = call_user_func(array($objectTable, 'find'), array($guid));
            return $object->toArray();
        });

        // Map sendNotification
        $lua->registerCallback('sendNotification', function ($type, $dest, $options, $model) {
            caNotificationsTools::getInstance()->sendNotification($type, $dest, $options, $model);
        });

        // Map sendMail
        $lua->registerCallback('sendMail', function ($dest, $subject, $message) {
            sfContext::getInstance()->getMailer()->composeAndSend('servervip@capsciences.fr', $dest, $subject, $message);
        });
        
        // Map createMedailleVisiteur
        $lua->registerCallback('createVisiteurMedaille', function ($visiteur_id, $medaille_id, $connection = 'insitu') {

            $medaille = Doctrine_Core::getTable('Medaille')->findOneByGuid($medaille_id);

            $visiteur_medaille = new VisiteurMedaille();
            $visiteur_medaille->setGuid(Guid::generate());
            $visiteur_medaille->setMedailleId($medaille_id);
            $visiteur_medaille->setVisiteurId($visiteur_id);
            $visiteur_medaille->setConnection($connection);
            if(!($visiteur_medaille->hasAlreadyMedaille($connection) && $medaille->getIsUnique())){
                $visiteur_medaille->save();
            }
        });

        // Map date
        $lua->registerCallback('date', function ($format) {
            return date($format);
        });

        // Map varDump
        $lua->registerCallback('varDump', function ($data) {
            return var_dump($data);
        });

        // Map isBestScoreInteractif
        $lua->registerCallback('isBestScoreInteractif', function ($logVisiteObj) {
						
						$interactif_id = $logVisiteObj['interactif_id'];
						$log_visite_guid = $logVisiteObj['guid'];
						$visiteur_id = $logVisiteObj['visiteur_id'];
						$score = $logVisiteObj['score'];
						
            $log_visite_highscore = LogVisiteTable::getInstance()->getHighScoreByInteractif($interactif_id);
           // $log_visite =  Doctrine_Core::getTable('LogVisite')->findOneByGuid($log_visite_guid);

            if($score == $log_visite_highscore['highscore'])
            {
                return true;
            }

            return false;
        });

        // Map isUserBestScoreInteractif
        $lua->registerCallback('isVisiteurBestScoreInteractif', function ($logVisiteObj) {
						
						$interactif_id = $logVisiteObj['interactif_id'];
						$log_visite_guid = $logVisiteObj['guid'];
						$visiteur_id = $logVisiteObj['visiteur_id'];
						$score = $logVisiteObj['score'];
						
            //$log_visite =  Doctrine_Core::getTable('LogVisite')->findOneByGuid($log_visite_guid);
            $log_visite_highscore = LogVisiteTable::getInstance()->getVisiteurHighScoreByInteractif($interactif_id, $visiteur_id);

            if($score == $log_visite_highscore['highscore'] && $visiteur_id == $log_visite_highscore['visiteur_id'])
            {
                return true;
            }

            return false;
        });

        // Map nbLogVisiteExposition
        $lua->registerCallback('nbLogVisiteExposition', function ($exposition_id, $visiteur_id) {
            $count_log_visite = LogVisiteTable::getInstance()->countLogVisiteByExposition($exposition_id, $visiteur_id);
            return $count_log_visite;
        });

        // Map nbLogVisiteExposition
        $lua->registerCallback('hasVisiteMultiPlateforme', function ($logVisiteObj) {
        
        		$interactif_id = $logVisiteObj['interactif_id'];
        		$visiteur_id = $logVisiteObj['visiteur_id'];
        
            $count_log_visite = LogVisiteTable::getInstance()->hasLogVisiteMultiPlateforme($interactif_id, $visiteur_id);
            return $count_log_visite;
        });

        // Map getTotalXP
        $lua->registerCallback('getTotalScore', function () {
            $capscience_total_score = XpTable::getInstance()->getTotalScore();
            return $capscience_total_score;
        });

        // Map nbLogVisiteExposition
        $lua->registerCallback('getTotalScoreByVisiteur', function ($visiteur_id) {
            $visiteur = Doctrine_Core::getTable('Visiteur')->findOneByGuid($visiteur_id);
            $visiteur_score = $visiteur->getTotalXp();
            return $visiteur_score;
        });

        // Map nbLogVisiteExposition
        $lua->registerCallback('getTotalScoreByTypologieAndVisiteur', function ($typologie_id, $visiteur_id) {
            $visiteur_score_by_typlogie = XpTable::getInstance()->getTotalScoreByTypologieAndVisiteur($typologie_id, $visiteur_id);
            return $visiteur_score_by_typlogie;
        });

        // create new notification
        $lua->registerCallback('createNotification', function ($visiteur_id, $libelle, $params = array()) {
            $notif = new Notification();
            $notif->setLibelle($libelle);
            $notif->setVisiteurId($visiteur_id);
            if(isset($params['visite_id']))$notif->setVisiteId($params['visite_id']);
            if(isset($params['from_model']))$notif->setFromModel($params['from_model']);
            if(isset($params['from_model_id']))$notif->setFromModelId($params['from_model_id']);
            if(isset($params['parameter']))$notif->setParameter(json_encode((array)$params['parameter']));
            $notif->save();
        });
  }
}