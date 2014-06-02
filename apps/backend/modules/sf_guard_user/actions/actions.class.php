<?php

require_once dirname(__FILE__).'/../lib/sf_guard_userGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/sf_guard_userGeneratorHelper.class.php';

/**
 * sf_guard_user actions.
 *
 * @package    sf_sandbox
 * @subpackage sf_guard_user
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class sf_guard_userActions extends autoSf_guard_userActions
{
  public function executeAutocomplete(sfWebRequest $request)
  {
    $tags = Doctrine::getTable('sfGuardUser')->getForAutocomplete($request->getParameter('term'));
    $response = '["'.join($tags,'","').'"]';
    return $this->renderText($response);
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $notice = $form->getObject()->isNew() ? 'The item was created successfully.' : 'The item was updated successfully.';

      try {
      	if($form->isNew() && $form->getValue("password") == "" || is_null($form->getValue("password")))
      	{
      		throw new Exception("invalid password");
      	}
        $sf_guard_user = $form->save();
      } catch (Doctrine_Validator_Exception $e) {

        $errorStack = $form->getObject()->getErrorStack();

        $message = get_class($form->getObject()) . ' has ' . count($errorStack) . " field" . (count($errorStack) > 1 ?  's' : null) . " with validation errors: ";
        foreach ($errorStack as $field => $errors) {
            $message .= "$field (" . implode(", ", $errors) . "), ";
        }
        $message = trim($message, ', ');

        $this->getUser()->setFlash('error', $message);
        return sfView::SUCCESS;
      }
      catch(Doctrine_Connection_Mysql_Exception $e)
      {
        $message = "An object with this label already exist";

        $this->getUser()->setFlash('error', $message);
        return sfView::SUCCESS;
      }
      catch(Exception $e)
      {
		$message = "The password is mandatory";

	    $this->getUser()->setFlash('error', $message);
	    return sfView::SUCCESS;
      }

      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $sf_guard_user)));

      if ($request->hasParameter('_save_and_add'))
      {
        $this->getUser()->setFlash('notice', $notice.' You can add another one below.');

        $this->redirect('@sf_guard_user_new');
      }
      else
      {
        $this->getUser()->setFlash('notice', $notice);

        $route = 'sf_guard_user';

        $action = $form->getObject()->isNew() ? 'new' : 'edit';

        $redirection = strtolower($this->configuration->getValue($action . '.redirection'));

        if (isset($redirection) && 'list' !== $redirection)
        {
          $route .= '_' . $redirection;
        }

        $url = array('sf_route' => $route);
        if (isset($redirection) && 'list' !== $redirection)
        {
          $url['sf_subject'] = $sf_guard_user;
        }

        $this->redirect($url);
      }
    }
    else
    {
      $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
    }
  }

}
