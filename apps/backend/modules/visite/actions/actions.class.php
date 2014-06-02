<?php

require_once dirname(__FILE__).'/../lib/visiteGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/visiteGeneratorHelper.class.php';

/**
 * visite actions.
 *
 * @package    sf_sandbox
 * @subpackage visite
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class visiteActions extends autoVisiteActions
{
  public function executeNew(sfWebRequest $request)
  {
    $this->forward404();
  }

  public function executeDelete(sfWebRequest $request)
  {
    $this->forward404();
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404();
  }

  protected function getPager()
  {
    $pager = $this->configuration->getPager('Visite');
    $query = $this->buildQuery();
    $pager->setQuery($query);
    $pager->setPage($this->getPage());
    $pager->init();

    return $pager;
  }

  protected function buildQuery()
  {
    $tableMethod = $this->configuration->getTableMethod();
    if (null === $this->filters)
    {
      $this->filters = $this->configuration->getFilterForm($this->getFilters());
    }

    $this->filters->setTableMethod($tableMethod);
		
		$query = $this->filters->buildQuery($this->getFilters());
	  $query->leftJoin('r.Visiteur');
	  //$query->leftJoin('r.Parcours');
	  //$query->leftJoin('r.Exposition');
	  //$query->andWhere('r.guid = v.guid');
		
    $this->addSearchQuery($query);
    
	  //die($query->getSqlQuery());
    $this->addSortQuery($query);
    $event = $this->dispatcher->filter(new sfEvent($this, 'admin.build_query'), $query);
    $query = $event->getReturnValue();
    return $query;
  }

    public function executeIndex(sfWebRequest $request)
  {
    // searching
    if ($request->hasParameter('search'))
    {
      $this->setSearch($request->getParameter('search'));
      $request->setParameter('page', 1);
    }
  
    // filtering
    if ($request->getParameter('filters'))
    {
      $this->setFilters($request->getParameter('filters'));
    }
    
    // sorting
    if ($request->getParameter('sort'))
    {
      $this->setSort(array($request->getParameter('sort'), $request->getParameter('sort_type')));
    }

  //maxPerPage
    if ($request->getParameter('maxPerPage'))
    {
      $this->setMaxPerPage($request->getParameter('maxPerPage'));
      $this->setPage(1);
    }
  
  
    // pager
    if ($request->getParameter('page'))
    {
      $this->setPage($request->getParameter('page'));
    }

    $this->pager = $this->getPager();
    $this->sort = $this->getSort();


    if ($request->isXmlHttpRequest())
    {
      $partialFilter = null;
      sfConfig::set('sf_web_debug', false);
      $this->setLayout(false);
      sfProjectConfiguration::getActive()->loadHelpers(array('I18N', 'Date'));
      
      if ($request->hasParameter('search'))
      {
        $partialSearch = $this->getPartial('visite/search', array('configuration' => $this->configuration));
      }
      
      if ($request->hasParameter('_reset')) 
      {
        $partialFilter = $this->getPartial('visite/filters', array('form' => $this->filters, 'configuration' => $this->configuration));
      }
      
      $partialList = $this->getPartial('visite/list', array('pager' => $this->pager, 'sort' => $this->sort, 'helper' => $this->helper));
      
      if (isset($partialSearch)) 
      {
        $partialList .= '#__filter__#'.$partialSearch;
      }
      if (isset($partialFilter))
      {
        $partialList .= '#__filter__#'.$partialFilter;
      }
      return $this->renderText($partialList);
    }
return sfView::SUCCESS;
  }

}
