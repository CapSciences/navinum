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
    return sfView::NONE;
  }
  
  public function executeError404(){
    $this->getResponse()->setStatusCode(404);
//  	$this->redirect404();
  }


  private function phpBridgeApi($url, $vars, $method = 'GET'){
      $ch = curl_init();
      switch($method){
          case 'POST':
              curl_setopt($ch, CURLOPT_URL, $url);
              curl_setopt($ch, CURLOPT_POST, 1);
              curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
          break;
          case 'PUT':
              //$put = json_decode($vars);
              curl_setopt($ch, CURLOPT_URL, $url);
              curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
              //curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($put));
              curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
          break;
          default:
              $get = json_decode($vars);
              $query = http_build_query($get);
              curl_setopt($ch, CURLOPT_URL, $url.'?'.$query);
          break;
      }
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
      curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
      curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL



      $Rec_Data = curl_exec($ch);
      $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      if($http_status == '404'){
          $Rec_Data = '{"error":{"code":"404","message":"Not Found"}}';
      }
      echo $Rec_Data;
      ob_start();
      curl_close($ch);
      return sfView::NONE;
  }

  public function executePhpBridgeApi(sfWebRequest $request){
      $service = $request->getParameter('service');
      $get = base64_decode($request->getParameter('get'));
      $post = base64_decode($request->getParameter('post'));
      $put = base64_decode($request->getParameter('put'));
      $api_host = sfConfig::get('app_api_host');
      $api_login = sfConfig::get('app_api_login');
      $api_password = sfConfig::get('app_api_password');

      $url = 'http://' . $api_login.':'.$api_password.'@'.$api_host.$service;
      if($post){
          return $this->phpBridgeApi($url, $post, 'POST');

      }else if($put){
          return $this->phpBridgeApi($url, $put, 'PUT');

      }else{
          return $this->phpBridgeApi($url, $get, 'GET');
      }
  }

  public function executePhpBridgeApiTest(sfWebRequest $request){
      $service = $request->getParameter('service');
      $get = base64_decode($request->getParameter('get'));
      $post = base64_decode($request->getParameter('post'));
      $put = base64_decode($request->getParameter('put'));
      $api_host = sfConfig::get('app_api_test_host');
      $api_login = sfConfig::get('app_api_test_login');
      $api_password = sfConfig::get('app_api_test_password');

      $url = 'http://' . $api_login.':'.$api_password.'@'.$api_host.$service;
      if($post){
          return $this->phpBridgeApi($url, $post, 'POST');

      }else if($put){
          return $this->phpBridgeApi($url, $put, 'PUT');

      }else{
          return $this->phpBridgeApi($url, $get, 'GET');
      }
  }

  public function executeLogReport(){
    // Outputs all POST parameters to a text file. The file name is the date_time of the report reception
    $date = date('Y-m-d_H-i-s');
    $fileName = $date.'.txt';

    $file = fopen(sfConfig::get('sf_upload_dir').'/'.$fileName,'w') or die('Could not create report file: ' . sfConfig::get('sf_upload_dir').'/'.$fileName);

     //print_r($_POST) ;
     // die(sfConfig::get('sf_upload_dir').'/'.$fileName);

      foreach($_POST as $key => $value) {
        $reportLine = $key." = ".$value."\n";
        fwrite($file, $reportLine) or die ('Could not write to report file ' . $reportLine);
    }
    fclose($file);
	try{
		$message = Swift_Message::newInstance()
		->setFrom('capsciences-bug-report@localhost')
		->setTo('sredeuil@clever-age.com')
		->setCc('bmoreau@clever-age.com')
		->setSubject($_POST['PACKAGE_NAME']. ' V' .$_POST['APP_VERSION_NAME'] . ' ' .$date)
		->setBody($_POST['STACK_TRACE'])
		->attach(Swift_Attachment::fromPath(sfConfig::get('sf_upload_dir').'/'.$fileName));
		$this->getMailer()->send($message);
	}catch(Exception $e){
		print_r($e->getMessage());	
	}
    return sfView::NONE;
  }
}
