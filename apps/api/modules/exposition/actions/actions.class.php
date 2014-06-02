<?php

/**
 * exposition actions.
 *
 * @package    sf_sandbox
 * @subpackage exposition
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z xavier $
 * @see        class::autoexpositionActions
 */
class expositionActions extends autoexpositionActions
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
    $validators['contexte_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['organisateur_editeur_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['organisateur_diffuseur_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['synopsis'] = new sfValidatorString(array('required' => false));
    $validators['description'] = new sfValidatorString(array('required' => false));
    $validators['logo'] = new sfValidatorString(array('max_length' => 128, 'required' => false));
    $validators['publics'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['langues'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['url_studio'] = new sfValidatorString(array('max_length' => 128, 'required' => false));
    $validators['url_illustration'] = new sfValidatorString(array('max_length' => 128, 'required' => false));
    $validators['start_at'] = new sfValidatorDateTime(array('required' => false));
    $validators['end_at'] = new sfValidatorDateTime(array('required' => false));
    $validators['created_at'] = new sfValidatorDateTime(array('required' => false));
    $validators['updated_at'] = new sfValidatorDateTime(array('required' => false));

    return $validators;
  }

  /**
   * Retrieves a  collection of Exposition objects
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

    //get the is_active param
    if (isset($params['is_active']))
    {
      $is_active = $params['is_active'];
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
    if (isset($is_active)) {
      $new_object = array();
      foreach ($this->objects as $key => $object)
      {
        if (isset($object["end_at"]))
        {
          $date1 = new DateTime("now");
          $date2 = new DateTime($object["end_at"]);
          if($is_active == true)
          {
            if ($date2 > $date1 ) {
               $new_object[]  = $this->objects[$key];
            }
          }
          else
          {
            if ($date2 < $date1 ) {
               $new_object[]  = $this->objects[$key];
            }
          }
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
