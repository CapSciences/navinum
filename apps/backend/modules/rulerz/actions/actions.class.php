<?php

require_once dirname(__FILE__) . '/../lib/rulerzGeneratorConfiguration.class.php';
require_once dirname(__FILE__) . '/../lib/rulerzGeneratorHelper.class.php';

/**
 * rulerz actions.
 *
 * @package    sf_sandbox
 * @subpackage rulerz
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class rulerzActions extends autoRulerzActions
{
    public function executeShow(sfWebRequest $request)
    {
        /** @var $rulerz Rulerz */
        $rulerz = $this->getRoute()->getObject();

        $currentevents = array();
        foreach ($rulerz->getListeners() as $listener) {
            /** @var $listener RulerzListener */
            $currentevents[] = $listener->getEvent();
        }

        // some flags to filter . and .. and follow symlinks
        $flags = \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::FOLLOW_SYMLINKS;
        // create a simple recursive directory iterator
        $iterator = new \RecursiveDirectoryIterator(sfConfig::get('sf_lib_dir') . '/model/doctrine/', $flags);
        $iterator = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::SELF_FIRST);

				$exclude = array("DeleteLog","SyncLog", "sfGuardUser", "sfGuardGroup", "sfGuardGroupPermission","sfDoctrineGuardPlugin", "sfDoctrineOAuthPlugin", "sfGuardPermission", "sfGuardUserPermission", "sfGuardUserGroup", "sfGuardRememberKey", "sfGuardForgotPassword", "SentMessage", "TemplateMail", "Notification","Rulerz","RulerzListener", "RulerzExecution");
        $models = array();
        foreach ($iterator as $model) {
        		if((strpos($model, '.svn') !== false || strpos($model, '.DS_Store'))  !== false){
        			continue;
        		}
            $model = str_replace('.class', '', pathinfo($model, PATHINFO_FILENAME));
            if (strpos($model, 'Base') === 0 || $model == 'base' || strpos($model, 'Table') === (strlen($model) - strlen('Table')) || in_array($model, $exclude)){
             continue;
						}
            if (!in_array($model . '::CREATE', $currentevents)) {
                $models[] = $model . '::CREATE';
            }

            if (!in_array($model . '::UPDATE', $currentevents)) {
                $models[] = $model . '::UPDATE';
            }
        }

        natcasesort($models);

        $this->models = array_merge($models);
        $this->rulerz = $rulerz;

        $query = Doctrine_Query::create();
        $this->executions = $query->from('RulerzExecution re')->leftJoin('re.Rulerz r')->where('r.guid = ?', array($rulerz->getGuid()))->orderBy('re.created_at DESC')->limit(20)->execute();
    }

    public function executeAddListener(sfWebRequest $request)
    {
        $guid = $request->getParameter('guid');
        /** @var $rulerz Rulerz */
        $rulerz = RulerzTable::getInstance()->find($guid);

        if ($rulerz && $request->isMethod(sfWebRequest::POST)) {
            $eventName = $request->getPostParameter('select_event');

            $query = Doctrine_Query::create();
            $rules = $query->from('Rulerz r')->leftJoin('r.Listeners l')->where('r.guid = ?', array($guid))->andWhere('l.event = ?', array($eventName))->execute();
            if (count($rules) == 0) {
                $listener = new RulerzListener();
                $listener->setRulerz($rulerz);
                $listener->setEvent($eventName);
                $listener->save();

                //$this->getUser()->setFlash('success', 'Lu');
            }
        }

        $this->redirect($this->generateUrl('rulerz_show', array('guid' => $rulerz->getGuid())));
    }

    public function executeDeleteListener(sfWebRequest $request)
    {
        $guid = $request->getParameter('guid');
        $lguid = $request->getParameter('lguid');
        /** @var $rulerz Rulerz */
        $rulerz = RulerzTable::getInstance()->find($guid);
        /** @var $listener RulerzListener */
        $listener = RulerzListenerTable::getInstance()->find($lguid);

        if ($rulerz && $listener && $listener->getRulerzId() == $rulerz->getGuid() && $request->isMethod(sfWebRequest::DELETE)) {
            $listener->delete();
        }

        $this->redirect($this->generateUrl('rulerz_show', array('guid' => $rulerz->getGuid())));
    }

}
