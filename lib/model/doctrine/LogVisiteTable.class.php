<?php

/**
 * LogVisiteTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class LogVisiteTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object LogVisiteTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('LogVisite');
    }

    public function countLogVisiteByExposition($exposition_id, $visiteur_id)
    {
		$results = $this->createQuery('lv')
	      ->select('count(*) as count_log_visite')
    	  ->where('lv.exposition_id = ?', $exposition_id)
    	  ->andWhere('lv.visiteur_id = ?', $visiteur_id)
      	->execute(array(), Doctrine::HYDRATE_ARRAY);

      	return $results[0]['count_log_visite'];
    }

    public function getHighScoreByInteractif($interactif_id)
    {
	    $results = $this->createQuery('lv')
    	  ->select("max(lv.score) as highscore, guid, interactif_id, visiteur_id, start_at, end_at, visite_id")
    	  ->where("interactif_id = ? ", $interactif_id)
    	  ->orderBy("score")
    	  ->limit(2)
    	  ->execute(array(), Doctrine::HYDRATE_ARRAY);
    	  
    	  return $results[0];
				
    }
    
    public function getVisiteurHighScoreByInteractif($interactif_id, $visiteur_id)
    {
	    $results = $this->createQuery('lv')
    	  ->select("max(lv.score) as highscore, guid, interactif_id, visiteur_id, start_at, end_at, visite_id")
    	  ->where("interactif_id = ? ", $interactif_id)
				->andWhere("visiteur_id = ? ", $visiteur_id)
				->orderBy("score")
				->limit(2)
				->execute(array(), Doctrine::HYDRATE_ARRAY);
      	  
      	return $results[0];
    }

    public function hasLogVisiteMultiPlateforme($interactif_id, $visiteur_id)
    {
			$insitu = $this->createQuery('lv')
	      ->select('count(connection) as count')
    	  ->where('lv.interactif_id = ?', $interactif_id)
    	  ->andWhere("visiteur_id = ? ", $visiteur_id)
    	  ->andWhere("connection = ? ", 'insitu')
      	->execute(array(), Doctrine::HYDRATE_ARRAY);
      	
       $mobile = $this->createQuery('lv')
	      ->select('count(connection) as count')
    	  ->where('lv.interactif_id = ?', $interactif_id)
    	  ->andWhere("visiteur_id = ? ", $visiteur_id)
    	  ->andWhere("connection = ? ", 'mobile')
      	->execute(array(), Doctrine::HYDRATE_ARRAY);
      	
    	  if(($insitu[0]['count'] == 1 && $mobile[0]['count'] > 1 ) || ($insitu[0]['count'] > 1 && $mobile[0]['count'] == 1 )){
      		return true;
      	}
      	return false;

    }



}