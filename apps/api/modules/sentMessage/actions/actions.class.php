<?php

/**
 * sentMail actions.
 *
 * @package    sf_sandbox
 * @subpackage sentMail
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z xavier $
 * @see        class::autosentMailActions
 */
class sentMessageActions extends autosentMessageActions
{
	public function getSendValidators()
    {
    	$validators = array();
    	$validators['mail_from'] = new sfValidatorEmail(array('max_length' => 255, 'required' => true));
    	$validators['mail_to'] = new sfValidatorEmail(array('max_length' => 255, 'required' => true));
    	$validators['subject'] = new sfValidatorString(array('max_length' => 255, 'required' => true));
    	$validators['content'] = new sfValidatorString(array('max_length' => 25500, 'required' => true));
      $validators['pj_ext'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
      $validators['pj_filename'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
      $validators['pj_content'] = new sfValidatorString(array('required' => false));
    	return $validators;
  	}

	public function getResetPasswordValidators()
    {
    	$validators = array();
    	$validators['mail'] = new sfValidatorEmail(array('max_length' => 255, 'required' => true));
    	$validators['pseudo'] = new sfValidatorString(array('max_length' => 255, 'required' => true));
    	return $validators;
  	}

  public function getResetPasswordSmsValidators()
    {
      $validators = array();
      $validators['num_mobile'] = new sfValidatorString(array('max_length' => 64, 'required' => true));
      $validators['pseudo'] = new sfValidatorString(array('max_length' => 255, 'required' => true));
      return $validators;
    }


	public function getSendPseudoValidators()
    {
    	$validators = array();
    	$validators['mail'] = new sfValidatorEmail(array('max_length' => 255, 'required' => true));
    	return $validators;
  	}

  public function getSendPseudoSmsValidators()
    {
      $validators = array();
      $validators['num_mobile'] = new sfValidatorString(array('max_length' => 64, 'required' => true));
      $validators['mail'] = new sfValidatorEmail(array('max_length' => 255, 'required' => true));
      return $validators;
    }

  /**
   * Sent an email with the given parameter
   * @param   sfWebRequest   $request a request object
   * @return  string
   */
  public function executeSend(sfWebRequest $request)
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
      $this->validateSend($content, $params);
		  $this->getResponse()->setContentType($serializer->getContentType());

      //$message = new ApiBaseMessage($params['subject'], $this->getPartial('base', array("content" => $params['content'])));
      $message = new ApiBaseMessage($params['subject'], $params['content']);
      $message->setFrom($params['mail_from']);
      $message->setTo($params['mail_to']);

      if(isset($params['pj_ext']) && isset($params['pj_filename']) && isset($params['pj_content']))
      {
       // $message->attach(new Swift_Message_Attachment(new Swift_File(base64_decode($params['pj_content'])), $params['pj_filename'].".".$params['pj_ext']));
       // Swift_Attachment::newInstance($image, 'sample_image.png')
        $message->attach(Swift_Attachment::newInstance(base64_decode($params['pj_content']), $params['pj_filename'].".".$params['pj_ext']));
      }

      $this->getMailer()->send($message);

		  $result = array(array('message' => 'ok'));
      $this->output = $serializer->serialize($result, 'result');
    }
    catch (Exception $e)
    {
      $this->getResponse()->setStatusCode(406);
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
  }

  /**
   * Sent an email with the given parameter with template resetPassord
   * @param   sfWebRequest   $request a request object
   * @return  string
   */
  public function executeResetPassword(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));
    $content = $request->getContent();

    // Restores backward compatibility. Content can be the HTTP request full body, or a form encoded "content" var.
    if (strpos($content, 'content=') === 0)
    {
      $content = $request->getParameter('content');
    }

    $request->setRequestFormat('html');

    try
    {
	  	$params = $this->parsePayload($content);
      $this->validateResetPassword($content, $params);
		  $serializer = $this->getSerializer();
		  $this->getResponse()->setContentType($serializer->getContentType());

	    $visiteur = Doctrine_Core::getTable('Visiteur')->findOneBy("emailAndpseudo_son",array($params['mail'], $params['pseudo']));
    	if (!$visiteur)
    	{
        $this->getResponse()->setStatusCode(406);
        $result = array(array('message' => 'visitor unknown', 'code' => 'E_SMS_00'));
        $this->output = $serializer->serialize($result, 'error');
    	}
    	else
    	{
    		  $randomPass = strtolower(substr(Guid::generate(), 0, 4));
      		$visiteur->setPasswordSon($randomPass);
      		$visiteur->save();
      		unset($visiteur);

			    $message = new ApiResetPasswordMessage($this->getPartial('resetPassword', array("password" => $randomPass)));
      		$message->setTo($params['mail']);

    		$this->getMailer()->send($message);

      		$result = array(array('message' => 'ok', 'password' => $randomPass));
      		$this->output = $serializer->serialize($result, 'result');
    	}

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

      $this->setTemplate('index');
      return sfView::SUCCESS;
    }
  }

  /**
   * Sent an email with the given parameter with template resetPassord
   * @param   sfWebRequest   $request a request object
   * @return  string
   */
  public function executeResetPasswordSms(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));
    $content = $request->getContent();

    // Restores backward compatibility. Content can be the HTTP request full body, or a form encoded "content" var.
    if (strpos($content, 'content=') === 0)
    {
      $content = $request->getParameter('content');
    }

    $request->setRequestFormat('html');

    try
    {
      $params = $this->parsePayload($content);
      $this->validateResetPasswordSms($content, $params);
      $serializer = $this->getSerializer();
      $this->getResponse()->setContentType($serializer->getContentType());

      $visiteur = Doctrine_Core::getTable('Visiteur')->findOneBy("num_mobileAndpseudo_son",array($params['num_mobile'], $params['pseudo']));
      if (!$visiteur)
      {
        $this->getResponse()->setStatusCode(406);
        $result = array(array('message' => 'visitor unknown', 'code' => 'E_SMS_00'));
        $this->output = $serializer->serialize($result, 'error');
      }
      else
      {
          $randomPass = strtolower(substr(Guid::generate(), 0, 4));
          $visiteur->setPasswordSon($randomPass);
          $visiteur->save();
          $num_mobile = $visiteur->getNumMobile();

          unset($visiteur);

          if(sfConfig::get("app_service_sms_allowed") === true)
          {
            $apiID = sfConfig::get("app_service_sms_api");
            $username=sfConfig::get("app_service_sms_username"); 
            $password=sfConfig::get("app_service_sms_password");
            $serviceSms = new ServiceSms($apiID, $username,$password);

            $message = sprintf(sfConfig::get("app_message_resetPassword"), $randomPass);
            $sendID = $serviceSms->sendMessage($num_mobile,$message);
          }

          $result = array(array('message' => 'ok', 'password' => $randomPass));
          $this->output = $serializer->serialize($result, 'result');
      }

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

      $this->setTemplate('index');
      return sfView::SUCCESS;
    }
  }

  /**
   * Sent an email with the given parameter with template resetPassord
   * @param   sfWebRequest   $request a request object
   * @return  string
   */
  public function executeSendPseudo(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));
    $content = $request->getContent();

    // Restores backward compatibility. Content can be the HTTP request full body, or a form encoded "content" var.
    if (strpos($content, 'content=') === 0)
    {
      $content = $request->getParameter('content');
    }

    $request->setRequestFormat('html');

    try
    {
	  	$params = $this->parsePayload($content);
      $this->validateSendPseudo($content, $params);
		  $serializer = $this->getSerializer();
		  $this->getResponse()->setContentType($serializer->getContentType());

	    $visiteurs = Doctrine_Core::getTable('Visiteur')->findBy("email",array($params['mail']));
    	if (!$visiteurs || count($visiteurs) < 1)
    	{
        $this->getResponse()->setStatusCode(406);
        $result = array(array('message' => 'visitor unknown', 'code' => 'E_SMS_00'));

      	$this->output = $serializer->serialize($result, 'error');
    	}
    	else
    	{
        $pseudo = "";
        foreach($visiteurs as $visiteur)
        {
          $pseudo .= $visiteur->getPseudoSon().",";
          unset($visiteur);
        }

        if($pseudo != "")
        {
            $pseudo = substr($pseudo, 0, strlen($pseudo) -1);
        }

        $message = new ApiSendPseudoMessage($this->getPartial('sendPseudo', array("pseudo" => $pseudo)));
        $message->setTo($params['mail']);

    		$this->getMailer()->send($message);

      	$result = array(array('message' => 'ok', 'pseudo' => $pseudo));
      	$this->output = $serializer->serialize($result, 'result');
    	}

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

      $this->setTemplate('index');
      return sfView::SUCCESS;
    }
  }


  /**
   * Sent an email with the given parameter with template resetPassord
   * @param   sfWebRequest   $request a request object
   * @return  string
   */
  public function executeSendPseudoSms(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));
    $content = $request->getContent();

    // Restores backward compatibility. Content can be the HTTP request full body, or a form encoded "content" var.
    if (strpos($content, 'content=') === 0)
    {
      $content = $request->getParameter('content');
    }

    $request->setRequestFormat('html');

    try
    {
      $params = $this->parsePayload($content);
      $this->validateSendPseudoSms($content, $params);
      $serializer = $this->getSerializer();
      $this->getResponse()->setContentType($serializer->getContentType());

      //$visiteurs = Doctrine_Core::getTable('Visiteur')->findBy("email",array($params['mail']));
	$visiteurs = Doctrine_Core::getTable('Visiteur')->findBy("emailAndnum_mobile",array($params['mail'], $params['num_mobile']));

      if (!$visiteurs || count($visiteurs) < 1)
      {
        $this->getResponse()->setStatusCode(406);
        $result = array(array('message' => 'visitor unknown', 'code' => 'E_SMS_00'));
        $this->output = $serializer->serialize($result, 'error');
      }
      else
      {
        $pseudo = "";
        foreach($visiteurs as $visiteur)
        {
          $pseudo .= $visiteur->getPseudoSon().",";
          unset($visiteur);
        }

        if($pseudo != "")
        {
            $pseudo = substr($pseudo, 0, strlen($pseudo)-1);
        }

        if(sfConfig::get("app_service_sms_allowed") === true)
        {
          $apiID = sfConfig::get("app_service_sms_api");
          $username=sfConfig::get("app_service_sms_username");
          $password=sfConfig::get("app_service_sms_password");
          $serviceSms = new ServiceSms($apiID, $username,$password);
          $message = sprintf(sfConfig::get("app_message_pseudoRetrieval"), $pseudo);
          $sendID = $serviceSms->sendMessage($params['num_mobile'],$message);
        }

        $result = array(array('message' => 'ok', 'pseudo' => $pseudo));
        $this->output = $serializer->serialize($result, 'result');
      }

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

      $this->setTemplate('index');
      return sfView::SUCCESS;
    }
  }

  /**
   * Applies the update validators to the payload posted to the service
   *
   * @param   string   $payload  A payload string
   */
  public function validateSend($payload, $params)
  {
  	$validators = $this->getSendValidators();
  	$this->validate($params, $validators);
  }

  /**
   * Applies the update validators to the payload posted to the service
   *
   * @param   string   $payload  A payload string
   */
  public function validateResetPassword($payload, $params)
  {
  	$validators = $this->getResetPasswordValidators();
  	$this->validate($params, $validators);
  }

  /**
   * Applies the update validators to the payload posted to the service
   *
   * @param   string   $payload  A payload string
   */
  public function validateResetPasswordSms($payload, $params)
  {
    $validators = $this->getResetPasswordSmsValidators();
    $this->validate($params, $validators);
  }


  /**
   * Applies the update validators to the payload posted to the service
   *
   * @param   string   $payload  A payload string
   */
  public function validateSendPseudo($payload, $params)
  {
  	$validators = $this->getSendPseudoValidators();
  	$this->validate($params, $validators);
  }

  /**
   * Applies the update validators to the payload posted to the service
   *
   * @param   string   $payload  A payload string
   */
  public function validateSendPseudoSms($payload, $params)
  {
    $validators = $this->getSendPseudoSmsValidators();
    $this->validate($params, $validators);
  }

}
