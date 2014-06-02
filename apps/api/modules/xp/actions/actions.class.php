<?php

/**
 * xp actions.
 *
 * @package    sf_sandbox
 * @subpackage xp
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z xavier $
 * @see        class::autoxpActions
 */
class xpActions extends autoxpActions
{
  /**
   * Returns the list of validators for a get request.
   * @return  array  an array of validators
   */
  public function getIndexValidators()
  {
  	$validators = array();
    $validators['visiteur_id'] = new sfValidatorString(array('max_length' => 255, 'required' => true));
    $validators['typologie_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['created_at'] = new sfValidatorDateTime(array('required' => false));
    $validators['updated_at'] = new sfValidatorDateTime(array('required' => false));

    return $validators;
  }

  /**
   * Returns the list of validators for a get request.
   * @return  array  an array of validators
   */
  public function getTotalValidators()
  {
  	$validators = array();
    $validators['visiteur_id'] = new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('LogVisite')->getRelation('Visiteur')->getAlias(), 'required' => true));

    return $validators;
  }

  /**
   * Retrieves a  collection of Xp objects
   * @param   sfWebRequest   $request a request object
   * @return  string
   */
  public function executeTotal(sfWebRequest $request)
  {
  	$this->forward404Unless($request->isMethod(sfRequest::GET));
    $params = $request->getParameterHolder()->getAll();

    // notify an event before the action's body starts
    $this->dispatcher->notify(new sfEvent($this, 'sfDoctrineRestGenerator.get.pre', array('params' => $params)));

    $request->setRequestFormat('html');
    $params = $this->cleanupParameters($params);

    try
    {
      $format = $this->getFormat();
      $this->validateTotal($params);
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

    $this->queryTotalExecute($params);
    $isset_pk = (!isset($isset_pk) || $isset_pk) && isset($params['guid']);
    if ($isset_pk && count($this->objects) == 0)
    {
      $request->setRequestFormat($format);
      $this->forward404();
    }


    // configure the fields of the returned objects and eventually hide some
    $this->setFieldVisibility();
    $this->configureFields();

    $serializer = $this->getSerializer();
    $this->getResponse()->setContentType($serializer->getContentType());
    if(is_null($this->objects[0]["total"]))
    {
      $this->objects[0]["total"]=0;
    }
    $this->output = $serializer->serialize($this->objects, $this->model);
    unset($this->objects);
  }


    /**
     * Execute the query for selecting a collection of objects, eventually
     * along with related objects
     *
     * @param   array   $params  an array of criterions for the selection
     */
    public function queryTotalBestVisiteurExecute($params)
    {
        $this->objects = $this->dispatcher->filter(
            new sfEvent(
                $this,
                'sfDoctrineRestGenerator.filter_results',
                array()
            ),
            $this->queryTotalBestVisiteur($params)->execute(array(), Doctrine::HYDRATE_ARRAY)
        )->getReturnValue();
    }

    /**
     * Retrieves a  collection of Xp objects
     * @param   sfWebRequest   $request a request object
     * @return  string
     */
    public function executeTotalBestVisiteur(sfWebRequest $request)
    {
        $this->forward404Unless($request->isMethod(sfRequest::GET));
        $params = $request->getParameterHolder()->getAll();

        // notify an event before the action's body starts
        $this->dispatcher->notify(new sfEvent($this, 'sfDoctrineRestGenerator.get.pre', array('params' => $params)));

        $request->setRequestFormat('html');
        $params = $this->cleanupParameters($params);

        try
        {
            $format = $this->getFormat();
            //$this->validateTotal($params);
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

        $this->queryTotalBestVisiteurExecute($params);
        if(count($this->objects)){
            unset($this->objects[0]['guid']);
        }


        $serializer = $this->getSerializer();
        $this->getResponse()->setContentType($serializer->getContentType());

        $this->output = $serializer->serialize($this->objects, $this->model);
        unset($this->objects);
    }

  /**
   * Applies the get validators to the constraint parameters passed to the
   * webservice
   *
   * @param   array   $params  An array of criterions used for the selection
   */
  public function validateTotal($params)
  {
  	$validators = $this->getTotalValidators();
  	$this->validate($params, $validators);
  }

  /**
   * Execute the query for selecting a collection of objects, eventually
   * along with related objects
   *
   * @param   array   $params  an array of criterions for the selection
   */
  public function queryTotalExecute($params)
  {
    $this->objects = $this->dispatcher->filter(
      new sfEvent(
        $this,
        'sfDoctrineRestGenerator.filter_results',
        array()
      ),
      $this->queryTotal($params)->execute(array(), Doctrine::HYDRATE_ARRAY)
    )->getReturnValue();
  }

  /**
   * Create the query for selecting objects, eventually along with related
   * objects
   *
   * @param   array   $params  an array of criterions for the selection
   */
  public function queryTotal($params)
  {
		    $q = Doctrine_Query::create()
		    	->select("SUM(score) as total")
		    	->from($this->model.' '.$this->model)
		    	->where($this->model.".visiteur_id = ?", $params['visiteur_id']);

    return $q;
  }

    /**
     * Create the query for selecting objects, eventually along with related
     * objects
     *
     * @param   array   $params  an array of criterions for the selection
     */
    public function queryTotalBestVisiteur($params)
    {
        /*
         * SELECT SUM(score) as total, visiteur_id FROM `xp` group by visiteur_id order by score desc limit 1
         */
        $q = Doctrine_Query::create()
            ->select("SUM(score) as total, visiteur_id")
            ->from($this->model.' '.$this->model)
            ->groupBy($this->model.".visiteur_id" )
            ->orderBy('score DESC')
            ->limit(1);
        return $q;
    }

  /**
   * Creates a Xp object
   * @param   sfWebRequest   $request a request object
   * @return  string
   */
  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));
    $content = $request->getContent();

    // Restores backward compatibility. Content can be the HTTP request full body, or a form encoded "content" var.
    if (strpos($content, 'content=') === 0)
    {
      $content = $request->getParameter('content');
    }

    $request->setRequestFormat('html');

	  $serializer = $this->getSerializer();
    $this->getResponse()->setContentType($serializer->getContentType());

    try
    {
      $content = $this->validateCreate($content);
    }
    catch (Exception $e)
    {
      $this->getResponse()->setStatusCode(406);
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

      $this->setTemplate('index');
      return sfView::SUCCESS;
    }

    $params = $this->parsePayload($content, true);
    if(isset($params["guid_update"])){
      $primaryKey = $params["guid_update"];
      $this->object = Doctrine_Core::getTable($this->model)->findOneByGuid($primaryKey);
      $this->forward404Unless($this->object);
    }
    else
    {
      $this->object = $this->createObject();
    }

    $this->updateObjectFromRequest($content);
    return $this->doSave();
  }

  /**
   * Returns the list of validators for a create request.
   * @return  array  an array of validators
   */
  public function getCreateValidators()
  {
    return array(
      'score' => new sfValidatorString(array('max_length' => 255, 'required' => true)),
      'visiteur_id' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('Xp')->getRelation('Visiteur')->getAlias(), 'required' => true)),
      'typologie_id' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('Xp')->getRelation('Typologie')->getAlias(), 'required' => true)),
    );
  }

  protected function doSave()
  {
    if($this->object->getGuid() == "" || $this->object->getGuid() == null)
    {
      $this->object->setGuid(Guid::generate());
    }
    $this->object->save();
    Resque::enqueue('default', 'Job_Typologie', array("visiteur_id" => $this->object->getVisiteurId()));

    $this->getRequest()->setParameter('visiteur_id', $this->object->getVisiteurId());
    $this->getRequest()->setParameter('typologie_id', $this->object->getTypologieId());
    $this->getRequest()->setMethod("GET");
    //return $this->redirect($this->getController()->genUrl('?visiteur_id='.$this->object->getVisiteurId()."&typologie_id=".$this->object->getTypologieId()));
    return $this->forward("xp", "index");
  }

  /**
   * Applies the creation validators to the payload posted to the service
   *
   * @param   string   $payload  A payload string
   */
  public function validateCreate($payload)
  {
  	$validators = $this->getCreateValidators();
    $params = $this->parsePayload($payload);
  	$this->validate($params, $validators);

  	// validate unique set of visiteur_id // typologie_id
	$nb = Doctrine_Query::create()
		//->select("count(*) as nb")
		->from($this->model.' '.$this->model)
		->where($this->model.".visiteur_id = ?", $params['visiteur_id'])
		->andWhere($this->model.".typologie_id = ?", $params['typologie_id'])
		->execute(array(), Doctrine::HYDRATE_ARRAY);
	  if(count($nb) > 0)
	  {
      $serializer = $this->getSerializer();

      if ($serializer)
      {
        $payload_array = $serializer->unserialize($payload);
      }
      $payload_array["score"] = $payload_array["score"] + $nb[0]["score"];
      $payload_array["guid_update"] = $nb[0]["guid"];

      $payload = $serializer->serialize($payload_array);
	  }

    $params = $this->parsePayload($payload, true);

    return $payload;
  }


}
