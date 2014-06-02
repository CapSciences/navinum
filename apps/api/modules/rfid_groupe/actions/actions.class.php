<?php

/**
 * rfid_groupe actions.
 *
 * @package    sf_sandbox
 * @subpackage rfid_groupe
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z xavier $
 * @see        class::autorfid_groupeActions
 */
class rfid_groupeActions extends autorfid_groupeActions
{
  /**
   * Returns the list of validators for a get request.
   * @return  array  an array of validators
   */
  public function getIndexValidators()
  {
  	$validators = array();
    $validators['guid'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['nom'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['created_at'] = new sfValidatorDateTime(array('required' => false));
    $validators['updated_at'] = new sfValidatorDateTime(array('required' => false));

    return $validators;
  }


  /**
   * Creates a rfid_groupe object :
   *     affecter tous les badges rfid groupe sélectionnés
   *     générer des visiteurs anonymes (autant qu'il y a de badges)
   *     générer une visite à chaque visiteur
   * 
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
      $this->validateCreate($content);
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

    $this->object = $this->createObject();
    $this->updateObjectFromRequest($content);
  $this->doSave();
  }

  /**
   * Returns the list of validators for a create request.
   * @return  array  an array of validators
   */
  public function getCreateValidators()
  {
    return array(
      'nom' => new sfValidatorInteger(array('required' => true)),
      'rfid_groupe_id' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('Xp')->getRelation('Visiteur')->getAlias(), 'required' => true)),
      'langue_id' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('Xp')->getRelation('Typologie')->getAlias(), 'required' => true)),
      'code_postal' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('Xp')->getRelation('Typologie')->getAlias(), 'required' => true)),
      'email' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('Xp')->getRelation('Typologie')->getAlias(), 'required' => true)),
      'csp' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('Xp')->getRelation('Typologie')->getAlias(), 'required' => true)),
      'genre' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('Xp')->getRelation('Typologie')->getAlias(), 'required' => true)),
      'age' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('Xp')->getRelation('Typologie')->getAlias(), 'required' => true)),
    );
  }

  protected function doSave()
  {
    $this->object->setGuid(Guid::generate());
    $this->object->save();
    $this->output = $this->getSerializer()->serialize($this->object->getGuid());
    return sfView::SUCCESS;
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
    ->select("count(*) as nb")
    ->from($this->model.' '.$this->model)
    ->where($this->model.".visiteur_id = ?", $params['visiteur_id'])
    ->andWhere($this->model.".typologie_id = ?", $params['typologie_id'])
    ->execute(array(), Doctrine::HYDRATE_ARRAY);

  if($nb[0]["nb"] > 0 )
  {
        throw new sfException(sprintf('visiteur_id "%s" has already a score for this typologie %s', $params['visiteur_id'], $params['typologie_id']));
  }
  }


}
