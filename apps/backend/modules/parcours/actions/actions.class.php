<?php

require_once dirname(__FILE__).'/../lib/parcoursGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/parcoursGeneratorHelper.class.php';

/**
 * parcours actions.
 *
 * @package    sf_sandbox
 * @subpackage parcours
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class parcoursActions extends autoParcoursActions
{
  
  public function executeCopy(sfWebRequest $request)
  {
    $parcours = $this->getRoute()->getObject();

    $interactifs = $parcours->Interactif->getPrimaryKeys();

    $parcours_copy = $parcours->copy();
    $parcours_copy->setGuid(Guid::generate());
    $parcours_copy->setLibelle($parcours->getLibelle()."-copie");
    $parcours_copy->setCreatedAt(date('Y-m-d H:i:s'));
    $parcours_copy->setUpdatedAt(date('Y-m-d H:i:s'));
    $parcours_copy->link('Interactif', array_values($interactifs));
    $parcours_copy->save();

    return $this->redirect($this->getController()->genUrl('parcours/edit?guid='.$parcours_copy->getGuid()));
  }


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

      $query->leftJoin('r.Exposition e');
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
    $tags = Doctrine::getTable('Parcours')->getForAutocomplete($request->getParameter('term'));
    $response = '["'.join($tags,'","').'"]';
    return $this->renderText($response);
  }
}
