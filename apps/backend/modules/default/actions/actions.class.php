<?php

/**
 * default actions.
 *
 * @package    sf_sandbox
 * @subpackage default
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class defaultActions extends sfActions
{
 /**
  * Executes index action
  *
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    $this->nbSms = 0;
    $this->allowedApi = sfConfig::get("app_service_sms_allowed");
    
    if($this->allowedApi  === true)
    {
      $apiID = sfConfig::get("app_service_sms_api");
      $username=sfConfig::get("app_service_sms_username"); 
      $password=sfConfig::get("app_service_sms_password");
      $serviceSms = new ServiceSms($apiID, $username,$password);
      $this->nbSms = $serviceSms->accountBalance();
    }
  }
}
