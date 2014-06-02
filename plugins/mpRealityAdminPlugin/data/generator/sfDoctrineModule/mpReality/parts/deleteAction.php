  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->dispatcher->notify(new sfEvent($this, 'admin.delete_object', array('object' => $this->getRoute()->getObject())));
	
	$this->getRoute()->getObject()->delete();

    $this->getUser()->setFlash('notice', 'The item was deleted successfully.');
    
    if ($request->isXmlHttpRequest()) {
      sfProjectConfiguration::getActive()->loadHelpers(array('I18N'));
      sfConfig::set('sf_web_debug', false);
      $this->getResponse()->setContentType('application/json');

      $response['type'] = 'notice';
      $response['msg'] = __('The item was deleted successfully.', array(), 'sf_admin');

      $response['redirectToUrl'] = $this->getController()->genUrl('@<?php echo $this->getUrlForAction('list') ?>');
      return $this->renderText(json_encode($response));
    } else {
      $this->redirect('@<?php echo $this->getUrlForAction('list') ?>');
    }
  }


