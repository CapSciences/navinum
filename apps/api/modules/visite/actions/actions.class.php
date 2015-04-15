<?php

/**
 * visite actions.
 *
 * @package    sf_sandbox
 * @subpackage visite
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z xavier $
 * @see        class::autovisiteActions
 */
class visiteActions extends autovisiteActions
{
  public function getIndexValidators()
  {
    $validators = array();
    $validators['guid'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['visiteur_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['groupe_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['navinum_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['exposition_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['parcours_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['created_at'] = new sfValidatorDateTime(array('required' => false));
    $validators['updated_at'] = new sfValidatorDateTime(array('required' => false));
    $validators['connexion_id'] = new sfValidatorString(array('required' => false));
    return $validators;
  }

  /**
   * Returns the list of validators for a create request.
   * @return  array  an array of validators
   */
  public function getCreateValidators()
  {
    return array(
      'visiteur_id' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('Visite')->getRelation('Visiteur')->getAlias(), 'required' => true)),
      'navinum_id' => new csRfidValidator(array('model' => Doctrine_Core::getTable('Visite')->getRelation('Rfid')->getAlias(), 'required' => true)),
      'groupe_id' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('Visite')->getRelation('RfidGroupeVisiteur')->getAlias(), 'required' => false)),
      'exposition_id' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('Visite')->getRelation('Exposition')->getAlias(), 'required' => false)),
      'parcours_id' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('Visite')->getRelation('Parcours')->getAlias(), 'required' => false)),
      'interactif_id' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('Visite')->getRelation('Interactif')->getAlias(), 'required' => false)),
      'connexion_id' => new sfValidatorString(array('required' => false)),
      'is_ending' => new sfValidatorString(array('required' => false)),
      'is_tosync' => new sfValidatorBoolean(array('required' => false))
    );
  }


    /**
     * Returns the list of validators for a create request.
     * @return  array  an array of validators
     */
    public function getCreateValidatorsMarket()
    {
        return array(
            'visiteur_id' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('Visite')->getRelation('Visiteur')->getAlias(), 'required' => true)),
            'navinum_id' => new csRfidValidator(array('model' => Doctrine_Core::getTable('Visite')->getRelation('Rfid')->getAlias(), 'required' => false)),
            'groupe_id' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('Visite')->getRelation('RfidGroupeVisiteur')->getAlias(), 'required' => false)),
            'exposition_id' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('Visite')->getRelation('Exposition')->getAlias(), 'required' => false)),
            'parcours_id' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('Visite')->getRelation('Parcours')->getAlias(), 'required' => false)),
            'interactif_id' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('Visite')->getRelation('Interactif')->getAlias(), 'required' => false)),
            'connexion_id' => new sfValidatorString(array('required' => true)),
            'is_ending' => new sfValidatorString(array('required' => false)),
            'is_tosync' => new sfValidatorBoolean(array('required' => false))
        );
    }

  protected function configureFields()
  {
    foreach($this->objects as $key=> $object)
    {
      unset($this->objects[$key]['Parcours']['created_at'], $this->objects[$key]['Parcours']['updated_at']);    	
    }
  }
  
  /**
   * Updates a Visite object
   * @param   sfWebRequest   $request a request object
   * @return  string
   */
  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));
    $content = $request->getContent();
    // Restores backward compatibility. Content can be the HTTP request full body, or a form encoded "content" var.
    if (strpos($content, 'content=') === 0)
    {
      $content = $request->getParameter('content');
    }
    $parameters = $this->parsePayload($content, true);
    if(isset($parameters['connexion_id'])) {
      $connexion_id = $parameters['connexion_id'];
      unset($parameters['connexion_id']);
    }
      $is_ending = false;
      if(isset($parameters['is_ending'])) {
          $is_ending = $parameters['is_ending'];
          unset($parameters['is_ending']);
      }
    
    $navinum_id = $parameters['navinum_id'];

    $visiteur_id = $parameters['visiteur_id'];
    
    if(isset($parameters['parcours_id']) && !$parameters['parcours_id'])
    {
    	unset($parameters['parcours_id']);
    }
    
  	$serializer = $this->getSerializer();
    $content = $serializer->serialize($parameters);
    $request->setRequestFormat('html');

    try
    {
      $this->validateUpdate($content);
    
    }
    catch (Exception $e)
    {
      $this->getResponse()->setStatusCode(406);
      $serializer = $this->getSerializer();
      $this->getResponse()->setContentType($serializer->getContentType());
      $error = $e->getMessage();

      // event filter to enable customisation of the error message.
      $result = $this->dispatcher->filter(
        new sfEvent($this, 'sfDoctrineRestGenerator.filter_error_output'),
        $error
      )->getReturnValue();

      if ($error === $result)
      {
        $error = array(array('message' => $error));
        $this->output = $serializer->serialize($error, 'error');
      }
      else
      {
        $this->output = $serializer->serialize($result);
      }
      return sfView::SUCCESS;
    }
		
    if ($navinum_id)
    {
    
      	$q = Doctrine_Query::create()
        	->from('Rfid rfid');
        	
        $chaine='';
        for ($i=0; $i < strlen($navinum_id)-1; $i+=2)
        {
            $chaine[]= $navinum_id[$i].$navinum_id[$i+1];
        }
        $inverted = implode("", array_reverse($chaine));
        $q->andWhere('(rfid.uid = ? OR rfid.uid = ? )', array($navinum_id, $inverted));
  			$result = $q->execute(array(), Doctrine::HYDRATE_ARRAY);

  			if(count($result)){
    			$navinum_id = $result[0]['uid'];
    			$this->getRequest()->setParameter('navinum_id', $navinum_id);
  			}else{
					 throw new sfException('Unknown rfid '.$navinum_id);
  			}
    }

      $this->object = Doctrine_Core::getTable($this->model)
          ->createQuery('v')
          ->where('v.navinum_id = ?', $navinum_id)
          ->andWhere('v.visiteur_id = ?', $visiteur_id)
          ->andWhere('v.guid != ""')
          ->fetchOne();

    if (!$this->object)
    {
      $this->object = $this->createObject();
      $this->object->set('guid', Guid::generate());
    }

      if(isset($parameters['exposition_id']) && $this->object->getExpositionId() === null){

          $this->object->set('exposition_id', $parameters['exposition_id']);
          $exposition = Doctrine_Core::getTable('Exposition')->findOneBy('guid', $parameters['exposition_id']);

          if($exposition != false && $exposition->getUniversId() != null){
              $univers_id = $exposition->getUniversId();
              $contexte_id = $exposition->getContexteId();

            // find visiteur medaille for univers
                $nb_visiteur_medailles = Doctrine_Core::getTable('VisiteurMedaille')
                  ->createQuery('vm')
                 ->select('count(*) count')
                  ->where('vm.univers_id = ?', $univers_id)
                  ->addWhere('vm.visiteur_id = ?', $visiteur_id)
                  ->count();
                if($nb_visiteur_medailles == 0){
                    // medaille first medaille to insert
                    $first_medaille = Doctrine_Core::getTable('UniversMedaille')
                        ->createQuery('um')
                        ->select('um.medaille_id')
                        ->leftJoin('um.Medaille m')
                        ->where('um.univers_id = ?', $univers_id)
                        ->andWhere('m.is_first_medaille = 1')
                        ->limit(1)
                        ->fetchOne();

                    if($first_medaille != false && $first_medaille->getMedailleId() != null){
                        // insert visiteur medaille
                        VisiteurMedailleTable::createVisiteurMedaille($visiteur_id, $first_medaille->getMedailleId(), $univers_id, $contexte_id);

                        $univers_status =  Doctrine_Core::getTable('UniversStatus')
                            ->createQuery('us')
                            ->where('us.univers_id = ?', $univers_id)
                            ->orderBy('level asc')
                            ->limit(1)
                            ->fetchOne();
                        if($univers_status != false && $univers_status->getGuid() != null){
                            $gain_id = null;
                            $expiration_days = 0;
                            if($univers_status->getGainId() != null){
                                $gain_id = $univers_status->getGainId();
                                $expiration_days = $univers_status->getGain()->getExpirationDays();
                            }
                            VisiteurUniversStatusGainTable::setNewVisiteurUniversStatus($univers_status->getGuid(), $univers_id, $visiteur_id, $gain_id, $expiration_days, $contexte_id);
                        }

                    }
                }
          }
      }

    // update and save it
    $this->updateObjectFromRequest($content);
    $this->object->set('navinum_id', $navinum_id);
    $this->object->set('visiteur_id', $visiteur_id);
      // Is there rfidGroup
      if(!isset($parameters['groupe_id'])){
          $groupe = Doctrine_Query::create()
              ->select('rfgv.guid as groupe_visiteur_id, rfid.uid as navinum_id, rfg.guid as groupe_id')
              ->from('Rfid rfid, RfidGroupe rfg, RfidGroupeVisiteur rfgv')
              ->where('rfid.uid = ?', $navinum_id)
              ->andWhere('rfid.groupe_id = rfg.guid')
              ->andWhere('rfgv.rfid_groupe_id = rfg.guid')
              ->orderBy('rfgv.created_at desc')
              ->fetchOne(array(), Doctrine::HYDRATE_ARRAY);
          if(is_array($groupe) && isset($groupe['groupe_visiteur_id']) && !empty($groupe['groupe_visiteur_id'])){
              $this->object->set('groupe_id', $groupe['groupe_visiteur_id']);
          }
      }

      $response = $this->doSave();





    if(isset($connexion_id) && !empty($connexion_id)) {
      // Notify websocket server
      caNotificationsTools::getInstance()->notifyAuth($this->object['guid'], $connexion_id);
    }

      if(isset($is_ending) && $is_ending == true) {
          // Notify websocket server
          caNotificationsTools::getInstance()->notifyReset($this->object['guid']);
      }

    return $response;
  }




    /**
     * Updates a Visite object
     * @param   sfWebRequest   $request a request object
     * @return  string
     */
    public function executeUpdateMarket(sfWebRequest $request)
    {
        $this->forward404Unless($request->isMethod(sfRequest::POST));
        $content = $request->getContent();
        // Restores backward compatibility. Content can be the HTTP request full body, or a form encoded "content" var.
        if (strpos($content, 'content=') === 0)
        {
            $content = $request->getParameter('content');
        }
        $parameters = $this->parsePayload($content, true);
        if(isset($parameters['connexion_id'])) {
            $connexion_id = $parameters['connexion_id'];
            unset($parameters['connexion_id']);
        }

        $serializer = $this->getSerializer();
        $content = $serializer->serialize($parameters);
        $request->setRequestFormat('html');

        try
        {
            $this->validateUpdateMarket($content);
            $this->object = Doctrine_Core::getTable($this->model)->findOneByConnexionId($connexion_id);

        }
        catch (Exception $e)
        {
            $this->getResponse()->setStatusCode(406);
            $serializer = $this->getSerializer();
            $this->getResponse()->setContentType($serializer->getContentType());
            $error = $e->getMessage();

            // event filter to enable customisation of the error message.
            $result = $this->dispatcher->filter(
                new sfEvent($this, 'sfDoctrineRestGenerator.filter_error_output'),
                $error
            )->getReturnValue();

            if ($error === $result)
            {
                $error = array(array('message' => $error));
                $this->output = $serializer->serialize($error, 'error');
            }
            else
            {
                $this->output = $serializer->serialize($result);
            }
            return sfView::SUCCESS;
        }

        if (!$this->object)
        {
            $this->object = $this->createObject();
            $this->object->set('guid', Guid::generate());
        }

        // update and save it
        $this->updateObjectFromRequest($content);

        $response = $this->doSave();

        if(isset($connexion_id) && !empty($connexion_id)) {
            // Notify websocket server
            caNotificationsTools::getInstance()->notifyAuth($this->object['guid'], $connexion_id);
        }

        if(isset($is_ending) && $is_ending == true) {
            // Notify websocket server
            caNotificationsTools::getInstance()->notifyReset($this->object['guid']);
        }

        return $response;
    }

  protected function doSave()
  {
    $this->object->save();

    $this->getRequest()->setParameter('navinum_id', $this->object->get('navinum_id'));
    $this->getRequest()->setParameter('exposition_id', $this->object->get('exposition_id'));
    $this->getRequest()->setParameter('connexion_id', $this->object->get('connexion_id'));
    $this->getRequest()->setParameter('guid', $this->object->get('guid'));
    $this->getRequest()->setParameter('visiteur_id', $this->object->get('visiteur_id'));

    $this->getRequest()->setMethod("GET");

    return $this->forward("visite", "index");
  }

  /**
   * Applies the update validators to the payload posted to the service
   *
   * @param   string   $payload  A payload string
   */
  public function validateUpdate($payload)
  {
  	$validators = $this->getUpdateValidators();
    //$validators['exposition_id']->setOption('required', true);
    unset($validators['created_at'], $validators['updated_at']);
    $params = $this->parsePayload($payload);
    // est ce que le guid exposition existe?
    if(isset($params['exposition_id']) && $params['exposition_id'])
    {
      $q = Doctrine_Query::create()
        ->from('Exposition e')
        ->where('e.guid = ?', $params['exposition_id']);
      $count =  $q->count();
      if(!$count)
      {
        throw new sfException(sprintf('exposition_id "%s" not exists', $params['exposition_id']));
      }
    }
    
    // est ce que le guid visiteur existe?
    if(isset($params['visiteur_id']) && $params['visiteur_id'])
    {
      $q = Doctrine_Query::create()
        ->from('Visiteur v')
        ->where('v.guid = ?', $params['visiteur_id']);
      $count =  $q->count();
      if(!$count)
      {
        throw new sfException(sprintf('visiteur_id "%s" not exists', $params['visiteur_id']));
      }
    }
    
  	$this->validate($params, $validators);
  }


    /**
     * Applies the update validators to the payload posted to the service
     *
     * @param   string   $payload  A payload string
     */
    public function validateUpdateMarket($payload)
    {
        $validators = $this->getCreateValidatorsMarket();

        unset($validators['created_at'], $validators['updated_at']);
        $params = $this->parsePayload($payload);

        // est ce que le guid visiteur existe?
        if(isset($params['visiteur_id']) && $params['visiteur_id'])
        {
            $q = Doctrine_Query::create()
                ->from('Visiteur v')
                ->where('v.guid = ?', $params['visiteur_id']);
            $count =  $q->count();
            if(!$count)
            {
                throw new sfException(sprintf('visiteur_id "%s" not exists', $params['visiteur_id']));
            }
        }

        $this->validate($params, $validators);
    }
  
  public function queryParcours($params)
  {
    $q = Doctrine_Query::create()

      ->from($this->model.' '.$this->model)

      ->leftJoin($this->model.'.Parcours Parcours');



    if (isset($sort))
    {
      $q->orderBy($sort);
    }

    if (isset($params['guid']))
    {
      $values = explode(',', $params['guid']);

      if (count($values) == 1)
      {
        $q->andWhere($this->model.'.guid = ?', $values[0]);
      }
      else
      {
        $q->whereIn($this->model.'.guid', $values);
      }

      unset($params['guid']);
    }

    foreach ($params as $name => $value)
    {
      $q->andWhere($this->model.'.'.$name.' = ?', $value);
    }

    return $q;
  }

  public function queryExecuteParcours($params)
  {
    $this->objects = $this->dispatcher->filter(
      new sfEvent(
        $this,
        'sfDoctrineRestGenerator.filter_results',
        array()
      ),
      $this->queryParcours($params)->execute(array(), Doctrine::HYDRATE_ARRAY)
    )->getReturnValue();
  }

    /**
   * Create the query for selecting objects, eventually along with related
   * objects
   *
   * @param   array   $params  an array of criterions for the selection
   */
  public function query($params)
  {
    $q = Doctrine_Query::create()

      ->from($this->model.' '.$this->model)
;

    $limit = 1;

    $q->limit($limit);

    $sort = 'created_at desc';

    if (isset($sort))
    {
      $q->orderBy($sort);
    }

    if (isset($params['guid']))
    {
      $values = explode(',', $params['guid']);

      if (count($values) == 1)
      {
        $q->andWhere($this->model.'.guid = ?', $values[0]);
      }
      else
      {
        $q->whereIn($this->model.'.guid', $values);
      }

      unset($params['guid']);
    }

    if(isset($params['navinum_id']))
    {
        $chaine='';
        for ($i=0; $i < strlen($params['navinum_id'])-1; $i+=2)
        {
            $chaine[]= $params['navinum_id'][$i].$params['navinum_id'][$i+1];
        }
        $inverted = implode("", array_reverse($chaine));
        $q->andWhere('('.$this->model.'.navinum_id = ? OR '.$this->model.'.navinum_id = ? )', array($params['navinum_id'], $inverted));
        unset($params['navinum_id']);
    }

    foreach ($params as $name => $value)
    {
      $q->andWhere($this->model.'.'.$name.' = ?', $value);
    }
    return $q;
  }

  public function executeIndex(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::GET));
    $params = $request->getParameterHolder()->getAll();

    // notify an event before the action's body starts
    $this->dispatcher->notify(new sfEvent($this, 'sfDoctrineRestGenerator.get.pre', array('params' => $params)));

    $request->setRequestFormat('html');
    $params = $this->cleanupParameters($params);
    $connexion_id = null;
    if(isset($params['connexion_id'])) {
        $connexion_id = $params['connexion_id'];
        unset($params['connexion_id']);
    }

    try
    {
      $format = $this->getFormat();
      $this->validateIndex($params);
    }
    catch (Exception $e)
    {
      $this->getResponse()->setStatusCode(406);
      $serializer = $this->getSerializer();
      $this->getResponse()->setContentType($serializer->getContentType());
      $error = $e->getMessage();

      // event filter to enable customisation of the error message.
      $result = $this->dispatcher->filter(
        new sfEvent($this, 'sfDoctrineRestGenerator.filter_error_output'),
        $error
      )->getReturnValue();

      if ($error === $result)
      {
        $error = array(array('message' => $error));
        $this->output = $serializer->serialize($error, 'error');
      }
      else
      {
        $this->output = $serializer->serialize($result);
      }

      return sfView::SUCCESS;
    }

    if(isset($params['visiteur_id']))
    {
    	$this->queryExecuteParcours($params);
    	// configure the fields of the returned objects and eventually hide some
	    $this->setFieldVisibilityParcours();
    }
    else
    {
	    $this->queryExecute($params);
	    // configure the fields of the returned objects and eventually hide some
	    $this->setFieldVisibility();
    }

    $this->configureFields();

    $serializer = $this->getSerializer();
    $this->getResponse()->setContentType($serializer->getContentType());
    $this->output = $serializer->serialize($this->objects, $this->model);

    if(!empty($connexion_id) && count($this->objects) == 1) {
      // Notify websocket server
      caNotificationsTools::getInstance()->notifyAuth($this->objects[0]['guid'], $connexion_id);
    }

    unset($this->objects);
  }

  protected function setFieldVisibilityParcours()
  {
    $hidden_keys = array (
    'is_tosync' =>2,
    'created_at' => 3,
    'updated_at' => 4,
    );

    foreach ($this->objects as $i => $object)
    {
      if (is_int($i))
      {
        $this->objects[$i] = array_diff_key($object, $hidden_keys);
      }
    }
  }



    public function executeCollectAnonymousVisite(sfWebRequest $request){
        $this->forward404unless($request->getParameter('visiteur_id') && $request->getParameter('visite_id'));
        $visiteur_id = $request->getParameter('visiteur_id');
        $visite_id = $request->getParameter('visite_id');

        $request->setRequestFormat('html');
        $serializer = $this->getSerializer();

        // check visiteur
        $visiteur = Doctrine_Query::create()
            ->from('Visiteur')
            ->where('guid = ?', $visiteur_id)
            ->andWhereIn('is_anonyme',  array(0, null))
            ->limit(1)
            ->execute();

        if (!$visiteur)
        {
            $this->getResponse()->setStatusCode(406);
            $result = array(array('message' => sprintf('visiteur %s not exist',  $visiteur_id), 'code' => 'E_VISITEUR_00'));
            $this->output = $serializer->serialize($result, 'error');
            return sfView::SUCCESS;
        }

        // check visite
        $visite = Doctrine_Core::getTable('Visite')->findOneByGuid($visite_id);
        if (!$visite)
        {
            $this->getResponse()->setStatusCode(406);
            $result = array(array('message' => sprintf('visite %s not exist',  $visite_id), 'code' => 'E_VISITE_00'));
            $this->output = $serializer->serialize($result, 'error');
            return sfView::SUCCESS;
        }else{
            $details = array();

            // get anonymous_visiteur
            $anonymous_visiteur_id = $visite->getVisiteurId();

            //$anonymous_visiteur_id->deleteActionFromCreateVisiteur();

            // already change
            if($anonymous_visiteur_id == $visiteur_id){
                $this->getResponse()->setStatusCode(406);
                $result = array(array('message' => 'visite already changed'));
                $this->output = $serializer->serialize($result, 'error');
                return sfView::SUCCESS;
            }

            // update Visite
            $visite->setVisiteurId($visiteur_id);
            $visite->save();

            // update LogVisite
            $log_visites = Doctrine_Core::getTable('LogVisite')->findByVisiteurId($anonymous_visiteur_id);
            $details['logVisite'] = count($log_visites);
            foreach($log_visites as $log_visite){
                $log_visite->setVisiteurId($visiteur_id);
                $log_visite->setVisiteId($visite_id);
                $log_visite->save();
            }
            // visiteur medaille
            $visiteur_medailles = Doctrine_Core::getTable('VisiteurMedaille')->findByVisiteurId($anonymous_visiteur_id);
            $details['VisiteurMedaille'] = count($visiteur_medailles);
            foreach($visiteur_medailles as $visiteur_medaille){
                $visiteur_medaille->setVisiteurId($visiteur_id);
                $visiteur_medaille->save();
            }

            // visiteur medaille
            $visiteur_univers = Doctrine_Core::getTable('VisiteurUniversStatusGain')->findByVisiteurId($anonymous_visiteur_id);
            $details['VisiteurUniversStatusGain'] = count($visiteur_univers);
            foreach($visiteur_univers as $visiteur_univer){
                $visiteur_univer->setVisiteurId($visiteur_id);
                $visiteur_univer->save();
            }

            // udate Xp
            $xps = Doctrine_Core::getTable('Xp')->findByVisiteurId($anonymous_visiteur_id);
            $details['Xp'] = count($xps);
            foreach($xps as $xp){
                $xp->setVisiteurId($visiteur_id);
                $xp->save();
            }
            // update Notification
            $notifications = Doctrine_Core::getTable('Notification')->findByVisiteurId($anonymous_visiteur_id);
            $details['Notification'] = count($notifications);
            foreach($notifications as $notification){
                $notification->setVisiteurId($visiteur_id);
                $notification->save();
            }

            $fileSystem = new sfFilesystem();
            $finder = new sfFinder;

            $path = sfConfig::get('sf_web_dir')."/visiteur/".$anonymous_visiteur_id;
            @$fileSystem->mirror(sfConfig::get('sf_web_dir')."/visiteur/".$anonymous_visiteur_id, sfConfig::get('sf_web_dir')."/visiteur/".$visiteur_id, $finder, array('override' => true));

            $result =  array(array_merge(array('message' => 'ok'), array('details' =>$details)));
            $this->output = $serializer->serialize($result, 'result');
            return sfView::SUCCESS;
        }




    }

  public function executeResetnavinum(sfWebRequest $request)
  {
    $this->forward404unless($request->getParameter('navinum_id'));
    
    $navinum_id = $request->getParameter('navinum_id');
    
    $q = Doctrine_Query::create()
    	->from('Rfid rfid');
    	
    $chaine='';
    for ($i=0; $i < strlen($navinum_id)-1; $i+=2)
    {
        $chaine[]= $navinum_id[$i].$navinum_id[$i+1];
    }
    $inverted = implode("", array_reverse($chaine));
    $q->andWhere('(rfid.uid = ? OR rfid.uid = ? )', array($navinum_id, $inverted));
		$result = $q->execute(array(), Doctrine::HYDRATE_ARRAY);

		if(count($result)){
			$navinum_id = $result[0]['uid'];
		}else{
			 throw new sfException('Unknown rfid '.$navinum_id);
		}
    
    $visites = Doctrine_Core::getTable('Visite')->findBy('navinum_id', $navinum_id);
    $result = array();
    foreach ($visites as $visite)
    {
      if ($visite->getNavinumId() != null)
      {
        if($visite->getRfid()->getIsResettable() == 1)
        {
          $visite->setNavinumId(null);
          $visite->save();
          $result[] = array('Visite' => $visite->getGuid());
        }
      }
    }

    $serializer = $this->getSerializer();
    $this->output = $serializer->serialize($result, 'result');
    return sfView::SUCCESS;
  }
}
