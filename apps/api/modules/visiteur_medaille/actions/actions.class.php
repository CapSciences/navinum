<?php

/**
 * visiteur_medaille actions.
 *
 * @package    sf_sandbox
 * @subpackage visiteur_medaille
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z xavier $
 * @see        class::autovisiteur_medailleActions
 */
class visiteur_medailleActions extends autovisiteur_medailleActions
{
  /**
   * Returns the list of validators for a get request.
   * @return  array  an array of validators
   */
  public function getIndexValidators()
  {
  	$validators = array();
    $validators['guid'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['visiteur_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['medaille_type_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['medaille_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['interactif_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['univers_id'] = new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('VisiteurMedaille')->getRelation('Univers')->getAlias(), 'required' => false));
    $validators['exposition_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['contexte_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['created_at'] = new sfValidatorDateTime(array('required' => false));
    $validators['updated_at'] = new sfValidatorDateTime(array('required' => false));
    $validators['sort_by'] = new sfValidatorChoice(array('choices' => array (
          0 => 'visiteur_id',
          1 => 'exposition_id',
          2 => 'medaille_type_id',
          3 => 'Medaille.libelle',
          4 => 'created_at'
      ), 'required' => false));
    $validators['sort_order'] = new sfValidatorChoice(array('choices' => array('asc', 'desc'), 'required' => false));
    $validators['connection'] = new sfValidatorChoice(array('choices' => array('insitu', 'mobile', 'internet'), 'required' => false));
    return $validators;
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
      ->select($this->model.'.*')
      ->addSelect("count(*) as nombre")
      ->addSelect("Medaille.*")
      ->addSelect("Exposition.libelle")
      ->addSelect("Interactif.libelle")
      ->from($this->model.' '.$this->model)
      ->leftJoin($this->model.'.Medaille Medaille')
      ->leftJoin('Medaille.Exposition Exposition')
      ->leftJoin('Medaille.Interactif Interactif')
      ->groupBy($this->model.'.medaille_id')
      ->addGroupBy($this->model.'.visiteur_id');
    /*
    if (isset($params['sort']) && in_array($params['sort'], array('visiteur_id', 'exposition_id', 'medaille_type_id', 'Medaille.libelle')))
    {

      $q->orderBy($params['sort']);
    }
    */

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

    if (isset($params['visiteur_id']))
    {
      $values = explode(',', $params['visiteur_id']);
      if (count($values) == 1)
      {
        $q->andWhere($this->model.'.visiteur_id = ?', $values[0]);
      }
      else
      {
        $q->whereIn($this->model.'.visiteur_id', $values);
      }

      unset($params['visiteur_id']);
    }

    if (isset($params['interactif_id']))
    {
      $values = explode(',', $params['interactif_id']);
      if (count($values) == 1)
      {
        $q->andWhere('Medaille.interactif_id = ?', $values[0]);
      }
      else
      {
        $q->whereIn('Medaille.interactif_id', $values);
      }

      unset($params['interactif_id']);
    }

    if (isset($params['exposition_id']))
    {
      $values = explode(',', $params['exposition_id']);
      if (count($values) == 1)
      {
        $q->andWhere('Medaille.exposition_id = ?', $values[0]);
      }
      else
      {
        $q->whereIn('Medaille.exposition_id', $values);
      }

      unset($params['exposition_id']);
    }

    if(isset($params['medaille_type_id'])){
        $values = explode(',', $params['medaille_type_id']);
        if (count($values) == 1)
        {
            $q->andWhere('Medaille.medaille_type_id = ?', $values[0]);
        }
        else
        {
            $q->whereIn('Medaille.medaille_type_id', $values);
        }

        unset($params['medaille_type_id']);
    }

      if (isset($params['sort_by']))
      {
          if($params['sort_by'] == 'medaille_type_id'){
            $q->leftJoin('Medaille.MedailleType mt');
            $q->addSelect('mt.guid, mt.libelle');
          }

          $sort = $params['sort_by'];
          unset($params['sort_by']);

          if (isset($params['sort_order']))
          {
              $sort .= ' '.$params['sort_order'];
              unset($params['sort_order']);
          }

      }

      if (isset($sort))
      {
          $q->orderBy($sort);
      }

      foreach ($params as $name => $value)
    {
      $q->andWhere($this->model.'.'.$name.' = ?', $value);
    }

    return $q;
  }

    public function executeCountByMedailleType(sfWebRequest $request)
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
            $params['sort_by'] = 'medaille_type_id';
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

        $new_object = array();
        foreach($this->objects as $array){
            $medaille_type_guid = $array['Medaille']['MedailleType']['guid'];
            $medaille_type_libelle = $array['Medaille']['MedailleType']['libelle'];

            $medaille = $array['Medaille'];
            unset($medaille['MedailleType']);

            if($medaille_type_guid){
                $new_object[$medaille_type_libelle]['guid'] = $medaille_type_guid;
                $new_object[$medaille_type_libelle]['libelle'] = $medaille_type_libelle;
                $new_object[$medaille_type_libelle]['Medaille'][] = $medaille;
            }
        }

        ksort($new_object);

        $this->objects = array();

        foreach($new_object as $array){
            $this->objects[] = $array;
        }
        //exit;

        // configure the fields of the returned objects and eventually hide some
        //$this->setFieldVisibility();
        //$this->configureFields();

        $serializer = $this->getSerializer();
        $this->getResponse()->setContentType($serializer->getContentType());
        $this->output = $serializer->serialize($this->objects, $this->model);
        unset($this->objects);
    }


    /**
   * Returns the list of validators for a create request.
   * @return  array  an array of validators
   */
  public function getCreateValidators()
  {
    $validators = array();
    $validators['guid'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['force'] = new sfValidatorBoolean(array('required' => false));
    $validators['visiteur_id'] = new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('VisiteurMedaille')->getRelation('Visiteur')->getAlias(),'required' => true));
    $validators['medaille_id'] = new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('VisiteurMedaille')->getRelation('Medaille')->getAlias(),'required' => true));
    $validators['univers_id'] = new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('VisiteurMedaille')->getRelation('Univers')->getAlias(), 'required' => false));
    $validators['connection'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['contexte_id'] = new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('VisiteurMedaille')->getRelation('Contexte')->getAlias(), 'required' => false));
    return $validators;
  }

  /**
   * Creates a VisiteurMedaille object
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

    try
    {
      $params = $this->parsePayload($content);
      $this->validateCreate($content, $params);
      $this->getResponse()->setContentType($serializer->getContentType());

        $this->object = $this->createObject();
        $this->updateObjectFromRequest($content);
        //$connection =  isset($params['connection'])?$params['connection']: 'insitu';
        if($this->object->hasAlreadyMedaille($params['univers_id'], $params['contexte_id']))
        {
          throw new Exception(sprintf("The medal %s is unique for this visitor %s", $this->object->getMedailleId(), $this->object->getVisiteurId()));
        }
        $this->doSave();

        VisiteurUniversStatusGainTable::hasNewStatus($this->object->getVisiteurId(), $this->object->getMedailleId(), $this->object->getContexteId());

        $output = array(array('message' => "ok"));
        $this->output = $serializer->serialize($output);


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

  /**
   * Applies the creation validators to the payload posted to the service
   *
   * @param   string   $payload  A payload string
   */
  public function validateCreate($payload, $params)
  {
    $validators = $this->getCreateValidators();
    $this->validate($params, $validators);
  }

  protected function createObject()
  {
    $object = new $this->model();
    $object->setGuid(Guid::generate());
    return $object;
  }


}
