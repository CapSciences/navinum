<?php
require_once sfConfig::get('sf_lib_dir')."/vendor/vendor/autoload.php";
use Guzzle\Http\Client;

/**
 * visiteur actions.
 *
 * @package    sf_sandbox
 * @subpackage visiteur
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z xavier $
 * @see        class::autovisiteurActions
 */
class visiteurActions extends autovisiteurActions
{
    var $is_anonyme_converted = false;

  /**
   * Returns the list of validators for a get request.
   * @return  array  an array of validators
   */
  public function getSendPhotoValidators()
  {
    $validators = array();
    $validators['guid'] = new sfValidatorString(array('max_length' => 255, 'required' => true));
    $validators['photo'] = new sfValidatorString(array('required' => true));

    return $validators;
  }


    public function getSendDocumentValidators()
    {
        $validators = array();
        $validators['visiteur_id'] = new sfValidatorString(array('max_length' => 255, 'required' => true));
        $validators['filebase64'] = new sfValidatorString(array('required' => true));
        $validators['filename'] = new sfValidatorString(array('required' => true));
        $validators['interactif_id'] = new sfValidatorString(array('required' => true));
        return $validators;
    }

  /**
   * Applies the creation validators to the payload posted to the service
   *
   * @param   string   $payload  A payload string
   */
  public function validateSendPhoto($payload, $params)
  {
    $validators = $this->getSendPhotoValidators();
    $this->validate($params, $validators);
  }

    public function validateSendDocument($payload, $params)
    {
        $validators = $this->getSendDocumentValidators();
        $this->validate($params, $validators);
    }

  /**
   * Returns the list of validators for a post request for action anonymous.
   * @return  array  an array of validators
   */
  public function getCreateAnonymousValidators()
  {
    $validators = array();
    $validators['contexte_creation_id'] = new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('Visiteur')->getRelation('Contexte')->getAlias()));
    $validators['pseudo'] = new sfValidatorString(array('max_length' => 255, 'required' => true));

    return $validators;
  }

  public function executeSendPhoto(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $request->setRequestFormat('html');

    $content = $request->getContent();
    // Restores backward compatibility. Content can be the HTTP request full body, or a form encoded "content" var.
    if (strpos($content, 'content=') === 0)
    {
      $content = $request->getParameter('content');
    }

    $parameters = $this->parsePayload($content, true);

    $serializer = $this->getSerializer();
    $this->getResponse()->setContentType($serializer->getContentType());
    try
    {
      $this->validateSendPhoto($content, $parameters);
    }
    catch(Exception $e)
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

    $this->visiteur = Doctrine::getTable($this->model)->findOneByGuid($parameters["guid"]);

    if(!$this->visiteur)
    {
      $this->getResponse()->setStatusCode(406);
      $error = array("message" => "bad guid");
      $this->output = $serializer->serialize($error, 'error');
      return sfView::SUCCESS;
    }

      if($this->visiteur->setPhoto($parameters["photo"]) === false)
      {
          $this->getResponse()->setStatusCode(406);
          $error = array("message" => "error writing photo file");
          $this->output = $serializer->serialize($error, 'error');
          return sfView::SUCCESS;
      }

    $result = array('message' => 'ok');
    $this->output = $serializer->serialize($result, 'result');
    // set anonymous log_viiste associated
    /*
    caNotificationsTools::getInstance()->sendNotification('general:notif', 'visiteur:'.$this->visiteur->getGuid(), array(
        'title' => sprintf('Clic clac %s', $this->visiteur->getPseudoSon()),
        'message' => 'J\'ai bien enregistré ta photo'
    ), array(
        'model' => 'visiteur',
        'model_id' => $this->visiteur->getGuid()
    ));
    */
    return sfView::SUCCESS;
  }


    public function executeSendDocument(sfWebRequest $request)
    {

        $this->forward404Unless($request->isMethod(sfRequest::POST));

        $request->setRequestFormat('html');

        $content = $request->getContent();
        // Restores backward compatibility. Content can be the HTTP request full body, or a form encoded "content" var.
        if (strpos($content, 'content=') === 0)
        {
            $content = $request->getParameter('content');
        }

        $parameters = $this->parsePayload($content, true);

        $serializer = $this->getSerializer();
        $this->getResponse()->setContentType($serializer->getContentType());
        try
        {
            $this->validateSendDocument($content, $parameters);
        }
        catch(Exception $e)
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

        $this->visiteur = Doctrine::getTable($this->model)->findOneByGuid($parameters["visiteur_id"]);

        if(!$this->visiteur)
        {
            $this->getResponse()->setStatusCode(406);
            $error = array("message" => "bad visiteur_id");
            $this->output = $serializer->serialize($error, 'error');
            return sfView::SUCCESS;
        }

        if($this->visiteur->setDocument($parameters["filebase64"], $parameters["filename"], $parameters["interactif_id"]) === false)
        {
            $this->getResponse()->setStatusCode(406);
            $error = array("message" => "error writing document file");
            $this->output = $serializer->serialize($error, 'error');
            return sfView::SUCCESS;
        }else{
            // symlink document to interactif
            $target = sfConfig::get('sf_root_dir').'/web/interactif/'.$parameters["interactif_id"].'/'.$parameters["visiteur_id"].'/'.$parameters["filename"];
            Doctrine::getTable('Interactif')->symlinkDocument( $parameters["interactif_id"], $parameters["visiteur_id"], $parameters["filename"], $target);
        }

        $result = array('message' => 'ok');
        $this->output = $serializer->serialize($result, 'result');

        return sfView::SUCCESS;
    }

  public function executeCreateAnonymous(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $request->setRequestFormat('html');

    $content = $request->getContent();

    // Restores backward compatibility. Content can be the HTTP request full body, or a form encoded "content" var.
    if (strpos($content, 'content=') === 0)
    {
      $content = $request->getParameter('content');
    }

    $parameters = $this->parsePayload($content, true);

    $serializer = $this->getSerializer();
    $this->getResponse()->setContentType($serializer->getContentType());

    try
    {
      $this->validateCreateAnonymous($content, $parameters);

      $visiteur = new Visiteur();
      $visiteur = $visiteur->createAnonymous($parameters['contexte_creation_id']);
      $visiteur->setPseudoSon($parameters['pseudo']);
      $visiteur->save();
      $result = $visiteur->getGuid();
    }
    catch(Exception $e)
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

    $this->getRequest()->setParameter('guid', $result);
    $this->getRequest()->setMethod("GET");

    return $this->forward("visiteur", "index");
  }


  /**
   * Returns the list of validators for a get request.
   * @return  array  an array of validators
   */
  public function getPhotoValidators()
  {
    $validators = array();
    $validators['guid'] = new sfValidatorString(array('max_length' => 255, 'required' => true));

    return $validators;
  }

  public function executeGetPhoto(sfWebRequest $request)
  {
    $request->setRequestFormat('html');
    $this->forward404Unless($request->isMethod(sfRequest::GET));
    $primaryKey = $request->getParameter('guid');

    $serializer = $this->getSerializer();
    $this->getResponse()->setContentType("image/jpeg");

    if(is_null($primaryKey))
    {
      $this->getResponse()->setStatusCode(406);
      $error = array("message" => "parameter guid mandatory");
      $this->output = $serializer->serialize($error, 'error');
      return sfView::SUCCESS;
    }

    $this->item = Doctrine::getTable($this->model)->findOneByGuid($primaryKey);
    if(!$this->item)
    {
      $this->getResponse()->setStatusCode(406);
      $error = array("message" => "unknown visitor");
      $this->output = $serializer->serialize($error, 'error');
      return sfView::SUCCESS;
    }

    $this->output = $this->item->getPhoto();

    // set anonymous log_viiste associated
    return sfView::SUCCESS;
  }

    public function executeBrowseDocuments(sfWebRequest $request)
    {
        $request->setRequestFormat('html');
        $this->forward404Unless($request->isMethod(sfRequest::GET));
        $visiteurs = $request->getParameter('visiteur_id');
        $interactifs = $request->getParameter('interactif_id');
        $interactifs_info = array();
        //$with_visiteur_infos = $request->getParameter('with_visiteur_infos');

        $serializer = $this->getSerializer();
        $this->getResponse()->setContentType($serializer->getContentType());
        if(is_null($visiteurs))
        {
            $this->getResponse()->setStatusCode(406);
            $error = array("message" => "parameter visiteur_id mandatory");
            $this->output = $serializer->serialize(array($error), 'error');
            return sfView::SUCCESS;
        }
        $visiteur_keys = array();
        $interactif_keys = array();

        $visiteur_keys = explode(',', $visiteurs);
        if($interactifs){
            $interactif_keys = explode(',', $interactifs);
        }


        $page = $request->getParameter('page');
        if (!$page)
        {
            $page = 1;
        }

        $page_size = $request->getParameter('page_size');
        if (!$page_size)
        {
            $page_size = 5000;
        }

        $offset = (($page - 1) * $page_size);
        $limit = $page_size;

        $results = array();
        foreach($visiteur_keys as $visiteur_id){
            $item = Doctrine::getTable($this->model)->findOneByGuid($visiteur_id);

            if(!$item && count($visiteur_keys) == 1)
            {
                $this->getResponse()->setStatusCode(406);
                $error = array("message" => "unknown visitor");
                $this->output = $serializer->serialize(array($error), 'error');
                return sfView::SUCCESS;
            }

            $visiteur_info = array(
                //'guid' =>   $item->getGuid(),
                'pseudo_son' =>   $item->getPseudoSon()
            );

            $results[$visiteur_id] = $visiteur_info;

            if(count($interactif_keys) > 0){
                $docs = array();
                foreach($interactif_keys as $interactif_id){
                    $doc = $item->browseDocuments($visiteur_id, $interactif_id, array('page_size' => $page_size, 'offset' => $offset));
                    if(count($doc)){
                        $docs[] = $doc;
                    }
                }
                if(count($docs)){
                    $results[$visiteur_id] = array_merge($results[$visiteur_id], array("documents" => $docs));
                }

            }else{
                $doc = $item->browseDocuments($visiteur_id, "", array('page_size' => $page_size, 'offset' => $offset));
                if(count($doc)){
                    $results[$visiteur_id] = array_merge($results[$visiteur_id], array("documents" => $doc));
                }
            }

        }
        if(count($results) > 0){
            $results = array($results);
        }
        $this->output = $serializer->serialize($results , 'result');
        return sfView::SUCCESS;

    }
  
  /**
   * Returns the list of validators for a get request.
   * @return  array  an array of validators
   */
  public function getIndexValidators()
  {
    $validators = array();
    $validators['guid'] = new sfValidatorString(array('max_length' => 25500, 'required' => false));
    $validators['password_son'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['email'] = new sfValidatorEmail(array('max_length' => 255, 'required' => false));
    $validators['contexte_creation_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['langue_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
//    $validators['is_groupe'] = new sfValidatorBoolean(array('required' => false));
    $validators['genre'] = new sfValidatorString(array('max_length' => 10, 'required' => false));
    $validators['date_naissance'] = new sfValidatorDate(array('required' => false));
    $validators['code_postal'] = new sfValidatorString(array('max_length' => 10, 'required' => false));
    $validators['preference_media_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['prenom'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['nom'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['pseudo_son'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['url_avatar'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['num_mobile'] = new sfValidatorString(array('max_length' => 64, 'required' => false));
    $validators['facebook_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['google_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['twitter_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['flickr_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['dailymotion_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['url_avatar'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['created_at'] = new sfValidatorDateTime(array('required' => false));
    $validators['updated_at'] = new sfValidatorDateTime(array('required' => false));
    $validators['is_active'] = new sfValidatorBoolean(array('required' => false));
    $validators['is_anonyme'] = new sfValidatorBoolean(array('required' => false));
    return $validators;
  }

  
  protected function configureFields()
  {
    foreach($this->objects as $key=> $object)
    {
      unset($this->objects[$key]['Contexte']['created_at'], $this->objects[$key]['Contexte']['updated_at']);
      if(array_key_exists('PreferenceMediaVisiteur', $object))
      {
	      foreach($object['PreferenceMediaVisiteur'] as $key2 => $preferenceMedia)
	      {
		      unset($this->objects[$key]['PreferenceMediaVisiteur'][$key2]['created_at'], $this->objects[$key]['PreferenceMediaVisiteur'][$key2]['updated_at']);      
	      }
      }
    }
  }


  /**
   * Removes a Visiteur object, based on its
   * primary key
   * @param   sfWebRequest   $request a request object
   * @return  string
   */
  public function executeDelete(sfWebRequest $request)
  {
    $request->setRequestFormat('html');
    $this->forward404Unless($request->isMethod(sfRequest::GET));
    $primaryKey = $request->getParameter('guid');
    $params = $request->getParameterHolder()->getAll();

    $serializer = $this->getSerializer();
    $this->getResponse()->setContentType($serializer->getContentType());

    if(is_null($primaryKey))
    {
      $this->getResponse()->setStatusCode(406);
      $error = array("message" => "parameter guid mandatory");
      $this->output = $serializer->serialize($error, 'error');
      return sfView::SUCCESS;
    }

    $this->item = Doctrine::getTable($this->model)->findOneByGuid($primaryKey);
    if(!$this->item)
    {
      $this->getResponse()->setStatusCode(406);
      $error = array("message" => "unknown visitor");
      $this->output = $serializer->serialize($error, 'error');
      return sfView::SUCCESS;
    }

    $this->item->delete();

    $result = array('message' => 'ok');
    $this->output = $serializer->serialize($result, 'result');
    // set anonymous log_viiste associated
    return sfView::SUCCESS;
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

      $visiteur = Doctrine_Core::getTable('Visiteur')->findOneBy("pseudo_son",array($params['pseudo_son']));
      if ($visiteur)
      {
        $this->getResponse()->setStatusCode(406);
        $result = array(array('message' => sprintf('visitor already exist %s ', $params['pseudo_son']), 'code' => 'E_VISITEUR_00'));
        $this->output = $serializer->serialize($result, 'error');
        return sfView::SUCCESS;
      }
        /*
      $visiteur = Doctrine_Core::getTable('Visiteur')->findOneBy("password_sonAndpseudo_son",array(md5($params['password_son']), $params['pseudo_son']));
      if ($visiteur)
      {
        $this->getResponse()->setStatusCode(406);
        $result = array(array('message' => sprintf('visitor already exist %s : %s ', $params['password_son'], $params['pseudo_son']), 'code' => 'E_VISITEUR_01'));
        $this->output = $serializer->serialize($result, 'error');
        return sfView::SUCCESS;
      }
        */
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
    $this->object->setIsAnonyme(0);
    $this->updateObjectFromRequest($content);
    $this->clear_password = $params['password_son'];


    /** @var $object Visiteur */
      $object = $this->doSave();


    return $this->doSave();
  }

  /**
   * Creates a VisiteurMedaille object
   * @param   sfWebRequest   $request a request object
   * @return  string
   */
  public function executeCreateFacebook(sfWebRequest $request)
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
//die('here');
    try
    {
      $params = $this->parsePayload($content);
      $this->validateCreateFacebook($content, $params);
      $this->getResponse()->setContentType($serializer->getContentType());

      $visiteur = Doctrine_Core::getTable('Visiteur')->findOneBy("facebook_id",array($params['facebook_id']));

      if ($visiteur)
      {

          $serializer = $this->getSerializer();
          $this->getResponse()->setContentType($serializer->getContentType());

          $this->getRequest()->setParameter('guid', $visiteur->getGuid());
          $this->getRequest()->setMethod("GET");

          return $this->forward("visiteur", "index");

          //$this->output = $serializer->serialize($visiteur, $this->model);
        //$this->getResponse()->setStatusCode(406);
        //$result = array(array('message' => sprintf('visitor already exist %s', $params['facebook_id']), 'code' => 'E_VISITEUR_02', 'guid' => $visiteur->getGuid()));
        return sfView::SUCCESS;
      }
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
    $this->object->setIsAnonyme(0);
    // Create a client and provide a base URL
    /*$url = 'http://graph.facebook.com/'.$params["facebook_photo"].'/picture';
    $client = new Client();

    // Download the image to a PHP temp stream
    $imageResponse = $client->get($url)->send();
    // Be sure to seek to the beginning of the entity body
    $imageResponse->getBody()->seek(0);

    if($this->object->setPhoto((string)$imageResponse->getBody()) === false)
    {
      $this->getResponse()->setStatusCode(406);
      $error = array("message" => "error writing photo file");
      $this->output = $serializer->serialize($error, 'error');
      return sfView::SUCCESS;
    }*/

    $url = "http://graph.facebook.com/".$params["facebook_photo"]."/picture";
    $content_file = file_get_contents($url);
    $this->object->createDataFolder();
    file_put_contents($this->object->getVisiteurDataPath().'/photo.jpg', $content_file);
    $this->object->setHasPhoto(true);

    $this->updateObjectFromRequest($content);
    return $this->doSave();
  }

  /**
   * Applies the creation validators to the payload posted to the service
   *
   * @param   string   $payload  A payload string
   */
  public function validateCreateAnonymous($payload, $params)
  {
    $validators = $this->getCreateAnonymousValidators();
    $this->validate($params, $validators);
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

  /**
   * Applies the creation validators to the payload posted to the service
   *
   * @param   string   $payload  A payload string
   */
  public function validateCreateFacebook($payload, $params)
  {
    $validators = $this->getCreateFacebookValidators();
    $this->validate($params, $validators);
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
      'password_son' => new sfValidatorString(array('max_length' => 255, 'required' => true)),
      'email' => new sfValidatorEmail(array('max_length' => 255, 'required' => true)),
      'contexte_creation_id' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('Visiteur')->getRelation('Contexte')->getAlias(), 'required' => false)),
      'langue_id' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('Visiteur')->getRelation('Langue')->getAlias(), 'required' => false)),
      'type' => new sfValidatorString(array('max_length' => 32, 'required' => false)),
      'has_photo' => new sfValidatorBoolean(array('required' => false)),
      'genre' => new sfValidatorString(array('max_length' => 10, 'required' => false)),
      'date_naissance' => new sfValidatorDate(array('required' => false)),
      'adresse' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'code_postal' => new sfValidatorString(array('max_length' => 10, 'required' => false)),
      'ville' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'prenom' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'nom' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'csp_id' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('Visiteur')->getRelation('Csp')->getAlias(), 'required' => false)),
      'pseudo_son' => new sfValidatorString(array('max_length' => 255, 'required' => true)),
      'url_avatar' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'num_mobile' => new sfValidatorString(array('max_length' => 64, 'required' => false)),
      'facebook_id' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'google_id' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'twitter_id' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'flickr_id' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'dailymotion_id' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'is_anonyme' => new sfValidatorBoolean(array('required' => false)),
      'is_active' => new sfValidatorBoolean(array('required' => false)),
      'is_tosync' => new sfValidatorBoolean(array('required' => false)),
      'has_newsletter' => new sfValidatorBoolean(array('required' => false)),
      'preference_media_id' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
    );
  }

  /**
   * Returns the list of validators for a create request.
   * @return  array  an array of validators
   */
  public function getCreateFacebookValidators()
  {
    return array(
      'password_son' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'email' => new sfValidatorEmail(array('max_length' => 255, 'required' => true)),
      'contexte_creation_id' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('Visiteur')->getRelation('Contexte')->getAlias(), 'required' => false)),
      'langue_id' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('Visiteur')->getRelation('Langue')->getAlias(), 'required' => false)),
      'type' => new sfValidatorString(array('max_length' => 32, 'required' => false)),
      'has_photo' => new sfValidatorBoolean(array('required' => false)),
      'genre' => new sfValidatorString(array('max_length' => 10, 'required' => false)),
      'date_naissance' => new sfValidatorDate(array('required' => false)),
      'adresse' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'code_postal' => new sfValidatorString(array('max_length' => 10, 'required' => false)),
      'ville' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'prenom' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'nom' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'csp_id' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('Visiteur')->getRelation('Csp')->getAlias(), 'required' => false)),
      'pseudo_son' => new sfValidatorString(array('max_length' => 255, 'required' => true)),
      'url_avatar' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'num_mobile' => new sfValidatorString(array('max_length' => 64, 'required' => false)),
      'facebook_id' => new sfValidatorString(array('max_length' => 255, 'required' => true)),
      'google_id' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'twitter_id' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'flickr_id' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'dailymotion_id' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'is_anonyme' => new sfValidatorBoolean(array('required' => false)),
      'is_active' => new sfValidatorBoolean(array('required' => false)),
      'is_tosync' => new sfValidatorBoolean(array('required' => false)),
      'has_newsletter' => new sfValidatorBoolean(array('required' => false)),
      'facebook_photo' =>  new sfValidatorString(array('max_length' => 255, 'required' => false)),
    );
  }

  /**
   * Returns the list of validators for a create request.
   * @return  array  an array of validators
   */
  public function getUpdateValidators()
  {
    return array(
      'guid' => new sfValidatorString(array('max_length' => 255, 'required' => true)),
      'password_son' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'email' => new sfValidatorEmail(array('max_length' => 255, 'required' => false)),
      'contexte_creation_id' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('Visiteur')->getRelation('Contexte')->getAlias(), 'required' => false)),
      'langue_id' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('Visiteur')->getRelation('Langue')->getAlias(), 'required' => false)),
      'type' => new sfValidatorString(array('max_length' => 32, 'required' => false)),
      'has_photo' => new sfValidatorBoolean(array('required' => false)),
      'genre' => new sfValidatorString(array('max_length' => 10, 'required' => false)),
      'date_naissance' => new sfValidatorDate(array('required' => false)),
      'adresse' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'code_postal' => new sfValidatorString(array('max_length' => 10, 'required' => false)),
      'ville' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'prenom' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'nom' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'csp_id' => new sfValidatorDoctrineChoice(array('model' => Doctrine_Core::getTable('Visiteur')->getRelation('Csp')->getAlias(), 'required' => false)),
      'pseudo_son' => new sfValidatorString(array('max_length' => 255, 'required' => true)),
      'url_avatar' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'num_mobile' => new sfValidatorString(array('max_length' => 64, 'required' => false)),
      'facebook_id' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'google_id' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'twitter_id' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'flickr_id' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'dailymotion_id' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'is_anonyme' => new sfValidatorBoolean(array('required' => false)),
      'has_newsletter' => new sfValidatorBoolean(array('required' => false)),
      'is_active' => new sfValidatorBoolean(array('required' => false)),
      'is_tosync' => new sfValidatorBoolean(array('required' => false)),
      'preference_media_id' => new sfValidatorString(array('max_length' => 255, 'required' => false)),
    );
  }

  /**
   * Returns the list of validators for a create request.
   * @return  array  an array of validators
   */
  public function getCreateVisiteurPreferenceValidators()
  {
    return array(
      'guid' => new sfValidatorDoctrineChoice(array('model' => "Visiteur", 'required' => true)),
      'preference_media_id' => new sfValidatorPass(array('required' => true)),
    );
  }


  /**
   * Creates a VisiteurPreferenceMediai object
   * @param   sfWebRequest   $request a request object
   * @return  string
   */
  public function executeCreateVisiteurPreference(sfWebRequest $request)
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
      $this->validateCreateVisiteur($content, $params);
      $this->getResponse()->setContentType($serializer->getContentType());

      Doctrine_Query::create()
      ->delete('PreferenceMediaVisiteur mv')
      ->where('mv.visiteur_id = ?', $params["guid"])
      ->execute();

        $array_ids = split(",", $params["preference_media_id"]);
        foreach ($array_ids as $id)
        {
          if($id != "")
          {
            $relation = new PreferenceMediaVisiteur();
            $relation->setVisiteurId($params["guid"]);
            $relation->setPreferenceMediaId($id);
            $relation->save();
          }
        }
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

    $this->getRequest()->setParameter('guid', $params["guid"]);
    $this->getRequest()->setMethod("GET");

    return $this->forward("visiteur", "index");
  }

  /**
   * Applies the creation validators to the payload posted to the service
   *
   * @param   string   $payload  A payload string
   */
  public function validateCreateVisiteur($payload, $params)
  {
    $validators = $this->getCreateVisiteurPreferenceValidators();
    $this->validate($params, $validators);
  }


  /**
   * Updates a Visiteur object
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
    $this->preference_media_ids = (array_key_exists('preference_media_id', $parameters))?$parameters['preference_media_id']: "";

  	$serializer = $this->getSerializer();
    $content = $serializer->serialize($parameters);

  	try
    {
      $this->validateUpdate($content);

        if(isset($parameters['pseudo_son'])){

            $visiteur = Doctrine::getTable('Visiteur')->isUpdatePseudoUnique($parameters['guid'], $parameters['pseudo_son']);

            if (count($visiteur))
            {

                $this->getResponse()->setStatusCode(406);
                $result = array(array('message' => sprintf('visitor already exist %s ', $parameters['pseudo_son']), 'code' => 'E_VISITEUR_00'));
                $this->output = $serializer->serialize($result, 'error');
                return sfView::SUCCESS;
            }
        }
        /*
        if(isset($parameters['password_son']) && isset($parameters['pseudo_son'])){
            $visiteur = Doctrine::getTable('Visiteur')->isUpdatePseudoAndPasswordUnique($parameters['guid'], $parameters['pseudo_son'], md5($parameters['password_son']));
            if ($visiteur)
            {
                $this->getResponse()->setStatusCode(406);
                $result = array(array('message' => sprintf('visitor already exist %s : %s ', $parameters['password_son'], $parameters['pseudo_son']), 'code' => 'E_VISITEUR_01'));
                $this->output = $serializer->serialize($result, 'error');
                return sfView::SUCCESS;
            }
        }
        */

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

    $primaryKey = $request->getParameter('guid');
    if(empty($primaryKey))
    {
      $primaryKey = $parameters["guid"];
    }
    $this->object = Doctrine_Core::getTable($this->model)->findOneByGuid($primaryKey);
    if($parameters["is_anonyme"] == false && $this->object->getIsAnonyme() == true){
        $this->is_anonyme_converted = true;
    }
    // update and save it
    $this->updateObjectFromRequest($content);
    return $this->doSave();
  }

  protected function doSave()
  {
    $is_new = $this->object->isNew();
    $this->object->save();


      if(($is_new || $this->is_anonyme_converted == true) && $this->object->getEmail() != ''){

          //die($this->getPartial('newVisiteur', array("password" => $this->clear_password, "pseudo" => $this->object->getPseudoSon(), "email" => $this->object->getEmail())));

          $message = new ApiCreateVisiteurMessage($this->getPartial('newVisiteur', array("password" => $this->clear_password, "pseudo" => $this->object->getPseudoSon(), "email" => $this->object->getEmail())));
          $message->setTo($this->object->getEmail());

          $this->getMailer()->send($message);
      }



    $this->getRequest()->setParameter('guid', $this->object->getGuid());
    $this->getRequest()->setMethod("GET");

    return $this->forward("visiteur", "index");
  }
  
  /**
   * Applies the update validators to the payload posted to the service
   *
   * @param   string   $payload  A payload string
   */
  public function validateUpdate($payload)
  {
    $params = $this->parsePayload($payload, true);
  	$validators = $this->getUpdateValidators();
    $validators['email']->setMessage('invalid', 'Email invalide');

    //if(!$params['contexte_creation_id']){
    //	throw new sfException('contexte_creation_id required');
    //}
    
    if(!isset($params['date_naissance']) || !$params['date_naissance'])
    {
    	unset($params['date_naissance']);
    }
    // est ce que le contexte creation existe
    if(isset($params['contexte_creation_id']))
    {
	    $q = Doctrine_Query::create()
	      ->from('Contexte c')
	      ->where('c.guid = ?', $params['contexte_creation_id']);
	    $count =  $q->count();
	    if(!$count)
	    {
	      throw new sfException(sprintf('contexte_creation_id "%s" not exists', $params['contexte_creation_id']));
	    }
    }
    unset($validators['created_at'], $validators['updated_at']);
  	$this->validate($params, $validators);
  }

  /**
   * Retrieves a  collection of Visiteur objects
   * @param   sfWebRequest   $request a request object
   * @return  string
   */
  public function executeIndex(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::GET));
    $params = $request->getParameterHolder()->getAll();
    //$params['is_active'] = true;
    // notify an event before the action's body starts
    $this->dispatcher->notify(new sfEvent($this, 'sfDoctrineRestGenerator.get.pre', array('params' => $params)));

    $request->setRequestFormat('html');
    $params = $this->cleanupParameters($params);

    try
    {
      if(empty($params))
      {
        throw new sfException("the service must have at least one parameter");
      }

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
    $isset_pk = (!isset($isset_pk) || $isset_pk) && isset($params['guid']);

    if(!$isset_pk){
        $params['is_active'] = true;
    }

    $this->queryExecute($params);
    foreach($this->objects as &$objects){
        if($objects['has_photo'] != true){
            $objects['has_photo'] = false;
        }
    }

    // configure the fields of the returned objects and eventually hide some
    $this->setFieldVisibility();
    $this->configureFields();

    $serializer = $this->getSerializer();
    $this->getResponse()->setContentType($serializer->getContentType());
    $this->output = $serializer->serialize($this->objects, $this->model);
    unset($this->objects);
  }

  public function executeDisabled(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::GET));
    $params = $request->getParameterHolder()->getAll();
    $this->forward404Unless(isset($params['guid']));

    $serializer = $this->getSerializer();
    $this->getResponse()->setContentType('application/xml');

    $visiteur = Doctrine_Core::getTable('Visiteur')->findOneBy('guid', $params['guid']);
    if (!$visiteur)
    {
    	$result = array(array('message' => 'bad guid'));
      $this->output = $serializer->serialize($result, 'error');
    }
    else
    {
      $visiteur->setIsActive(false);
      $visiteur->save();
      unset($visiteur);
      $result = array(array('message' => 'ok'));
      $this->output = $serializer->serialize($result, 'result');
    }
    return sfView::SUCCESS;
  }
  
  public function executeEnabled(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::GET));
    $params = $request->getParameterHolder()->getAll();
    $this->forward404Unless(isset($params['guid']));

    $serializer = $this->getSerializer();
    $this->getResponse()->setContentType('application/xml');

    $visiteur = Doctrine_Core::getTable('Visiteur')->findOneBy('guid', $params['guid']);
    if (!$visiteur)
    {
    	$result = array(array('message' => 'bad guid'));
      $this->output = $serializer->serialize($result, 'error');
    }
    else
    {
      $visiteur->setIsActive(true);
      $visiteur->save();
      unset($visiteur);
      $result = array(array('message' => 'ok'));
      $this->output = $serializer->serialize($result, 'result');
    }
    return sfView::SUCCESS;
  }
  
  /**
   * Manages the visibility of fields in record collections and in relations.
   * This method will hide some fields, based on the configuration file
   *
   * @return  void
   */
  protected function setFieldVisibility()
  {
    foreach ($this->objects as $i => $object)
    {
      unset(
        $this->objects[$i]['is_tosync'],
        $this->objects[$i]['Contexte']['is_tosync'],
        $this->objects[$i]['Langue']['is_tosync'],
        $this->objects[$i]['Langue']['updated_at'],
        $this->objects[$i]['Langue']['created_at']
      );
      if(isset($this->objects[$i]['PreferenceMediaVisiteur']))
      {
        foreach($this->objects[$i]['PreferenceMediaVisiteur'] as $a => $object2)
        {
          if(isset($this->objects[$i]['PreferenceMediaVisiteur'][$a]['is_tosync']))
          {
            unset($this->objects[$i]['PreferenceMediaVisiteur'][$a]['is_tosync']);
          }
        }
      }
    }
  }


  /**
   * Returns the list of validators for a get request.
   * @return  array  an array of validators
   */
  public function getNeedsValidators()
  {
    $validators = array();
    $validators['guid'] = new sfValidatorDoctrineChoice(array('model' => 'Visiteur', 'required' => true));
    $validators['exposition_visiteur_needs_id'] = new sfValidatorDoctrineChoice(array('model' => 'ExpositionVisiteurNeeds', 'required' => true));

    return $validators;
  }

  /**
   * Applies the get validators to the constraint parameters passed to the
   * webservice
   *
   * @param   array   $params  An array of criterions used for the selection
   */
  public function validateNeeds($params)
  {    
    $validators = $this->getNeedsValidators();
    $this->validate($params, $validators);
  }


  /**
   * Retrieves a  collection of needs objects for a visitor
   * @param   sfWebRequest   $request a request object
   * @return  string
   */
  public function executeNeeds(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::GET));
    $params = $request->getParameterHolder()->getAll();

    // notify an event before the action's body starts
    $this->dispatcher->notify(new sfEvent($this, 'sfDoctrineRestGenerator.get.pre', array('params' => $params)));

    $request->setRequestFormat('html');
    $params = $this->cleanupParameters($params);

    try
    {
      if(empty($params))
      {
        throw new sfException("the service must have at least one parameter");
      }

      $format = $this->getFormat();
      $this->validateNeeds($params);
      $visiteur = Doctrine::getTable($this->model)->findOneByGuid($params["guid"]);
      $needs = Doctrine::getTable('ExpositionVisiteurNeeds')->findOneByGuid($params["exposition_visiteur_needs_id"]);
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

    $serializer = $this->getSerializer();
    $this->getResponse()->setContentType($serializer->getContentType());
    $this->output = $serializer->serialize($visiteur->getNeeds($needs));
  }

}
