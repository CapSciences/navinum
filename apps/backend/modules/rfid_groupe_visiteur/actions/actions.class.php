<?php

require_once dirname(__FILE__).'/../lib/rfid_groupe_visiteurGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/rfid_groupe_visiteurGeneratorHelper.class.php';

/**
 * rfid_groupe_visiteur actions.
 *
 * @package    sf_sandbox
 * @subpackage rfid_groupe_visiteur
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class rfid_groupe_visiteurActions extends autoRfid_groupe_visiteurActions
{
  public function executeCreate(sfWebRequest $request)
  {
    $this->form = $this->configuration->getForm();
    $this->rfid_groupe_visiteur = $this->form->getObject();

    $this->processForm($request, $this->form);
    $this->setTemplate('new');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $notice = $form->getObject()->isNew() ? 'The item was created successfully.' : 'The item was updated successfully.';

      try {

        $rfid_groupe_visiteur = $form->save();
        $rfid_groupe_visiteur->createAnonymousVisitor($form->getValue('contexte_creation_id'));

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

      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $rfid_groupe_visiteur)));

      if ($request->hasParameter('_save_and_add'))
      {
        $this->getUser()->setFlash('notice', $notice.' You can add another one below.');

        $this->redirect('@rfid_groupe_visiteur_new');
      }
      else
      {
        $this->getUser()->setFlash('notice', $notice);

        $route = 'rfid_groupe_visiteur';

        $action = $form->getObject()->isNew() ? 'new' : 'edit';

        $redirection = strtolower($this->configuration->getValue($action . '.redirection'));

        if (isset($redirection) && 'list' !== $redirection)
        {
          $route .= '_' . $redirection;
        }

        $url = array('sf_route' => $route);
        if (isset($redirection) && 'list' !== $redirection)
        {
          $url['sf_subject'] = $rfid_groupe_visiteur;
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
