<?php

require_once dirname(__FILE__).'/../lib/visiteurGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/visiteurGeneratorHelper.class.php';

/**
 * visiteur actions.
 *
 * @package    sf_sandbox
 * @subpackage visiteur
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class visiteurActions extends autoVisiteurActions
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
        if(!in_array($expo['contexte_id'], $guids)){
	        $guids[] = $expo['contexte_id'];
        }
      }
      $query->whereIn('contexte_creation_id', $guids);
      return $query;
    }
  }
  
  public function executeAutocomplete(sfWebRequest $request)
  {
    $tags = Doctrine::getTable('Visiteur')->getForAutocomplete($request->getParameter('term'));
    $response = '["'.join($tags,'","').'"]';
    return $this->renderText($response);
  }
}
