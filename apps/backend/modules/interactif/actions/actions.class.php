<?php

require_once dirname(__FILE__).'/../lib/interactifGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/interactifGeneratorHelper.class.php';

/**
 * interactif actions.
 *
 * @package    sf_sandbox
 * @subpackage interactif
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class interactifActions extends autoInteractifActions
{
  
  protected function buildQuery()
  {
    $query = parent::buildQuery();

    if ($this->getUser()->hasPermission('admin'))
    {
      return $query;
    }
    else
    {
      $expositions = $this->getUser()->getGuardUser()->getExposition()->toArray();
      $guids = array();
      foreach ($expositions as $expo)
      {
        $guids[] = $expo['guid'];
      }
      $query->leftJoin('r.Parcours p');
      $query->leftJoin('p.Exposition e');
      $clause = '(';
      if(count($guids))
      {
      	$clause .= 'e.guid IN (\''.join('\', \'', $guids).'\') OR ';
      }
      $clause .= 'r.created_at > \'' . date('Y-m-d H:i:s', strtotime('-8 hours')).'\'';
      $clause .= ')';
      $query->andWhere($clause);
      return $query;
    }
  }

  public function executeAutocomplete(sfWebRequest $request)
  {
    $tags = Doctrine::getTable('Interactif')->getForAutocomplete($request->getParameter('term'));
    $response = '["'.join($tags,'","').'"]';
    return $this->renderText($response);
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $arrayFromRequest = $request->getParameter($form->getName());

    $typologies = Doctrine_Core::getTable('Typologie')->findAll();
    $arrayResultScore = array();
    foreach ($typologies as $typologie)
    {
        $id  = 'score_'.$typologie->getGuid();
        $arrayResultScore[$typologie->getGuid()]=$arrayFromRequest[$id];
        unset($arrayFromRequest[$id]);
    }

    $arrayFromRequest["score"] = json_encode($arrayResultScore);

    $form->bind($arrayFromRequest, $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $notice = $form->getObject()->isNew() ? 'The item was created successfully.' : 'The item was updated successfully.';

      try {

        $validatedFile = $form->getValue('file');
        unset($form['file']);

        if($validatedFile)
          $validatedFile->save($form->getObject()->getInteractifDataPath()."/source.zip");

        $interactif = $form->save();

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
      
      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $interactif)));

      if ($request->hasParameter('_save_and_add'))
      {
        $this->getUser()->setFlash('notice', $notice.' You can add another one below.');

        $this->redirect('@interactif_new');
      }
      else
      {
        $this->getUser()->setFlash('notice', $notice);

        $route = 'interactif';

        $action = $form->getObject()->isNew() ? 'new' : 'edit';

        $redirection = strtolower($this->configuration->getValue($action . '.redirection'));

        if (isset($redirection) && 'list' !== $redirection)
        {
          $route .= '_' . $redirection;
        }

        $url = array('sf_route' => $route);
        if (isset($redirection) && 'list' !== $redirection)
        {
          $url['sf_subject'] = $interactif;
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
