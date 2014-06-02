<?php

/**
 * default actions.
 *
 * @package    sf_sandbox
 * @subpackage default
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class defaultActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $files = $request->getFiles();
    $params = $request->getPostParameters();

    $this->forward404if(!isset($params['guid']) || !isset($params['from_date_sync']) || !isset($params['to_date_sync']) || !isset($params['origin']) || !isset($files['dump_intranet']));

    $dump_intranet = sfConfig::get('sf_root_dir').'/'.sfConfig::get('app_sync_dump_dir').'/'.$files['dump_intranet']['name'];
    rename($files['dump_intranet']['tmp_name'], $dump_intranet);

    $sync_log = new SyncLog();
    $sync_log->setGuid($params['guid']);
    $sync_log->setFromDatetimeSync($params['from_date_sync']);
    $sync_log->setToDatetimeSync($params['to_date_sync']);
    $sync_log->setOrigin($params['origin']);

    $sync = new ServerVipSync($this->getContext()->getConfiguration(), $sync_log, 'internet');
    $dump_internet = $sync->internetSync();
    
    $file = fopen($dump_internet, 'r');
    $data = fread($file, filesize($dump_internet));
    fclose($file);

    $this->getResponse()->setContent($data);
    return sfView::NONE;
  }
  
  public function executeInitTestDatabase(sfWebRequest $request)
  {
  	$this->forward404if(!$request->getParameter('just_for_test'));
  	Doctrine_Core::loadData(dirname(__FILE__).'/../../../../../data/fixtures/test/fixtures.yml');
  	return sfView::NONE;
  }
  
  public function executeSyncAjax(sfWebRequest $request)
  {
    $sync = new ServerVipSync($this->getContext()->getConfiguration());
    $sync->startSync();
    return sfView::NONE;
  }
  
  public function executeSyncCheck(sfWebRequest $request)
  {
  	$tables = sfConfig::get('app_sync_tables');
  	$result = array();
  	foreach ($tables as $table)
  	{
  		$count = Doctrine_Query::create()
                       ->from($table.' t')
                       ->count();
      $result[] = $table.'='.$count;
  	}
  	echo join('&', $result);
    return sfView::NONE;
  }
  
  

}
