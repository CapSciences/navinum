<?php

require_once dirname(__FILE__).'/../lib/exposition_visiteur_needsGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/exposition_visiteur_needsGeneratorHelper.class.php';

/**
 * exposition_visiteur_needs actions.
 *
 * @package    sf_sandbox
 * @subpackage exposition_visiteur_needs
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class exposition_visiteur_needsActions extends autoExposition_visiteur_needsActions
{

	protected function isValidSortColumn($column)
    {
       return Doctrine_Core::getTable(‘ExpositionVisiteurNeeds’)->hasColumn($column) || $column == ‘exposition_name’;
    }

  protected function addSortQuery($query)
  {
    if (array(null, null) == ($sort = $this->getSort()))
    {
      return;
    }
 
    if (!in_array(strtolower($sort[1]), array('asc', 'desc')))
    {
      $sort[1] = 'asc';
    }
 
    switch ($sort[0]) {
      case 'exposition_name':
        $sort[0] = 'e.libelle';
        break;
    }
 
    $query->addOrderBy($sort[0] . ' ' . $sort[1]);
  }

}
