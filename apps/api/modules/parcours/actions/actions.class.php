<?php

/**
 * parcours actions.
 *
 * @package    sf_sandbox
 * @subpackage parcours
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z xavier $
 * @see        class::autoparcoursActions
 */
class parcoursActions extends autoparcoursActions
{
  /**
   * Returns the list of validators for a get request.
   * @return  array  an array of validators
   */
  public function getIndexValidators()
  {
  	$validators = array();
    $validators['guid'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['libelle'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['created_at'] = new sfValidatorDateTime(array('required' => false));
    $validators['updated_at'] = new sfValidatorDateTime(array('required' => false));

    return $validators;
  }

  /**
   * Retrieves a  collection of Parcours objects
   * @param   sfWebRequest   $request a request object
   * @return  string
   */
  public function executeIndex(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::GET));
    $params = $request->getParameterHolder()->getAll();
  
    // notify an event before the action's body starts
    $this->dispatcher->notify(new sfEvent($this, 'sfDoctrineRestGenerator.get.pre', array('params' => $params)));
    
    $request->setRequestFormat('html');

        //get the exposition_id param
    if (isset($params['exposition_id']))
    {
      $exposition_id = $params['exposition_id'];
    }

        //get the exposition_id param
    if (isset($params['interactif_id']))
    {
      $interactif_id = $params['interactif_id'];
    }

    $params = $this->cleanupParameters($params);


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

    $this->queryExecute($params);
    $isset_pk = (!isset($isset_pk) || $isset_pk) && isset($params['guid']);
    if ($isset_pk && count($this->objects) == 0)
    {
      $request->setRequestFormat($format);
      $this->forward404();
    }

    //Filter by exposition_id
    if (isset($exposition_id)) {
      $new_object = array();
      foreach ($this->objects as $key => $object)
      {
        $filtered = true;
        if (isset($exposition_id))
        {
              $expositions = Doctrine_Query::create()
                                  ->from('ExpositionsParcours ep')
                                  ->andWhere('ep.parcours_id = ?', $object['guid'])
                                  ->andWhere('ep.exposition_id = ?', $exposition_id)
                                  ->execute(array(), Doctrine::HYDRATE_ARRAY);

              if (count($expositions) > 0)
              {
                $filtered = false;
              }
            }
        if (!$filtered && isset($this->objects[$key])) {
           $new_object[]  = $this->objects[$key];
        }
      }
      $this->objects = $new_object;
    }

    //Filter by exposition_id
    if (isset($interactif_id)) {
      $new_object = array();
      foreach ($this->objects as $key => $object)
      {
        $filtered = true;
        if (isset($interactif_id))
        {
              $interactifs = Doctrine_Query::create()
                                  ->from('ParcoursInteractif pi')
                                  ->andWhere('pi.parcours_id = ?', $object['guid'])
                                  ->andWhere('pi.interactif_id = ?', $interactif_id)
                                  ->execute(array(), Doctrine::HYDRATE_ARRAY);

              if (count($interactifs) > 0)
              {
                $filtered = false;
              }
            }
        if (!$filtered && isset($this->objects[$key])) {
           $new_object[]  = $this->objects[$key];
        }
      }
      $this->objects = $new_object;
    }

    // configure the fields of the returned objects and eventually hide some
    $this->setFieldVisibility();
    $this->configureFields();
    $serializer = $this->getSerializer();
    $this->getResponse()->setContentType($serializer->getContentType());
    $this->output = $serializer->serialize($this->objects, $this->model);
    unset($this->objects);
  }



}
