<?php

/**
 * rfid_groupe_visiteur actions.
 *
 * @package    sf_sandbox
 * @subpackage rfid_groupe_visiteur
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z xavier $
 * @see        class::autorfid_groupe_visiteurActions
 */
class rfid_groupe_visiteurActions extends autorfid_groupe_visiteurActions
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
    $validators['rfid_groupe_id'] = new sfValidatorString(array( 'required' => false));
    $validators['langue_id'] = sfValidatorString(array( 'required' => false));
    $validators['email'] = new sfValidatorEmail(array('max_length' => 255, 'required' => false));
    $validators['csp'] = new sfValidatorInteger(array('required' => false));
    $validators['genre'] = new sfValidatorString(array('max_length' => 10, 'required' => false));
    $validators['age'] = new sfValidatorInteger(array('required' => false));
    $validators['code_postal'] = new sfValidatorInteger(array('required' => false));
    $validators['commentaire'] = new sfValidatorString(array('required' => false));
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
    $params = $this->parsePayload($content, true);

    try
    {
      $this->validateCreateGroupeVisiteur($content, $params);
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
    return $this->doSave($params);
  }

  protected function createObject()
  {
    $object = new $this->model();
    $object->setGuid(Guid::generate());
    return $object;
  }

  /**
   * Returns the list of validators for a create request.
   * @return  array  an array of validators
   */
  public function getCreateValidators()
  {
    return array(
      'nom' => new sfValidatorString(array('max_length' => 255)),
      'rfid_groupe_id' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('RfidGroupeVisiteur')->getRelation('RfidGroupe')->getAlias(), 'required' => true)),
      'langue_id' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('RfidGroupeVisiteur')->getRelation('Langue')->getAlias(),  'required' => true)),
      'email' => new sfValidatorEmail(array('max_length' => 255, 'required' => false)),
      'csp' => new sfValidatorString(array('required' => false)),
      'genre' => new sfValidatorString(array('max_length' => 10, 'required' => false)),
      'age' => new sfValidatorInteger(array('required' => false)),
      'code_postal' => new sfValidatorInteger(array('required' => false)),
      'created_at' => new sfValidatorDateTime(array('required' => false)),
      'updated_at' => new sfValidatorDateTime(array('required' => false)),
      'contexte_creation_id' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('Visiteur')->getRelation('Contexte')->getAlias(),  'required' => true)),
    );
  }

  protected function doSave($params)
  {
    $this->object->save();
    $this->object->createAnonymousVisitor($params['contexte_creation_id']);

    //return $this->redirect($this->getController()->genUrl('rfid_groupe_visiteur/index?guid='.$this->object->getGuid()));

    $this->getRequest()->setParameter('guid', $this->object->getGuid());
    $this->getRequest()->setMethod("GET");

    return $this->forward("rfid_groupe_visiteur", "index");
  }

  /**
   * Applies the creation validators to the payload posted to the service
   *
   * @param   string   $payload  A payload string
   */
  public function validateCreateGroupeVisiteur($payload, $params)
  {
    $validators = $this->getCreateValidators();
    $this->validate($params, $validators);
  }

}
