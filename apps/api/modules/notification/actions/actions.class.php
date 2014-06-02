<?php

/**
 * notification actions.
 *
 * @package    sf_sandbox
 * @subpackage notification
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z xavier $
 * @see        class::autonotificationActions
 */
class notificationActions extends autonotificationActions
{
  public function getIndexValidators()
  {
  	$validators = array();
    $validators['guid'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['libelle'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['visiteur_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['visite_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['from_model'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['from_model_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['parameter'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['is_tosync'] = new sfValidatorBoolean(array('required' => false));
    $validators['created_at'] = new sfValidatorDateTime(array('required' => false));
    $validators['updated_at'] = new sfValidatorDateTime(array('required' => false));
    $validators['page_size'] = new sfValidatorInteger(array('required' => false));

    return $validators;
  }
  public function getCreateValidators()
  {
    return array(
      'libelle' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'visiteur_id' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('Notification')->getRelation('Visiteur')->getAlias())),
      'visite_id' => new sfValidatorString(array('max_length' => 255)),
      'from_model' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'from_model_id' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'parameter' => new sfValidatorString(array('max_length' => 255, 'required' => false))
    );
  }
  
  public function validateCreate($payload)
  {
  	$validators = $this->getCreateValidators();
    unset($validators['created_at'], $validators['updated_at']);
    $params = $this->parsePayload($payload);
  	$this->validate($params, $validators);
  }
  
  protected function doSave()
  {
    $this->object->save();
    $this->getRequest()->setMethod("GET");
    $this->getRequest()->setParameter('guid', $this->object->getGuid());
    return $this->forward("notification", "index");
  }
  /*
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

    try
    {
      $params = $this->parsePayload($content);
      $this->validateCreate($content, $params);
      $this->getResponse()->setContentType($serializer->getContentType());

      $this->object = $this->createObject();
      $this->updateObjectFromRequest($content);

      if($this->object->hasAlreadyMedaille())
      {
        throw new Exception(sprintf("The medal %s is unique for this visitor %s", $this->object->getMedailleId(), $this->object->getVisiteurId()));
      }
      
      $this->doSave();

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

    return sfView::SUCCESS;
  }
  */

  protected function createObject()
  {
    $object = new $this->model();
    $object->setGuid(Guid::generate());
    return $object;
  }

   /**
   * Create the query for selecting objects, eventually along with related
   * objects
   *
   * @param   array   $params  an array of criterions for the selection
   */
  public function query($params)
  {
    $q = Doctrine_Query::create()->from($this->model.' '.$this->model);

    if (!isset($params['page']))
    {
      $params['page'] = 1;
    }

    $page_size = 10;

    if (isset($params['page_size']))
    {
      $page_size = $params['page_size'];
      unset($params['page_size']);
    }

    $limit = $page_size;
    $q->offset(($params['page'] - 1) * $page_size);
    unset($params['page']);
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

    if (isset($params['created_at']))
    {
        $q->andWhere($this->model.'.created_at >= \''.$params['created_at'].'\'');
        unset($params['created_at']);
    }

    foreach ($params as $name => $value)
    {
      $q->andWhere($this->model.'.'.$name.' = ?', $value);
    }

    return $q;
  }

}
