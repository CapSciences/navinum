<?php

require_once dirname(__FILE__).'/../lib/visiteur_medailleGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/visiteur_medailleGeneratorHelper.class.php';

/**
 * visiteur_medaille actions.
 *
 * @package    sf_sandbox
 * @subpackage visiteur_medaille
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class visiteur_medailleActions extends autoVisiteur_medailleActions
{
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $notice = $form->getObject()->isNew() ? 'The item was created successfully.' : 'The item was updated successfully.';

      try {
      	$values =$form->getValues();

      	if($form->getObject()->hasAlreadyMedailleWithParameters($values['visiteur_id'], $values['medaille_id']))
      	{
			    $message = sprintf("The medal %s is unique for this visitor %s",  $values['medaille_id'], $values['visiteur_id']);
	        $this->getUser()->setFlash('error', $message);
	        return sfView::SUCCESS;
      	}
        $visiteur_medaille = $form->save();

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

      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $visiteur_medaille)));

      if ($request->hasParameter('_save_and_add'))
      {
        $this->getUser()->setFlash('notice', $notice.' You can add another one below.');

        $this->redirect('@visiteur_medaille_new');
      }
      else
      {
        $this->getUser()->setFlash('notice', $notice);

        $route = 'visiteur_medaille';

        $action = $form->getObject()->isNew() ? 'new' : 'edit';

        $redirection = strtolower($this->configuration->getValue($action . '.redirection'));

        if (isset($redirection) && 'list' !== $redirection)
        {
          $route .= '_' . $redirection;
        }

        $url = array('sf_route' => $route);
        if (isset($redirection) && 'list' !== $redirection)
        {
          $url['sf_subject'] = $visiteur_medaille;
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
