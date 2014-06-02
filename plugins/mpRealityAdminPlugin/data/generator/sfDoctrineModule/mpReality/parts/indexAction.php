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
        $partialSearch = $this->getPartial('<?php echo $this->getModuleName() ?>/search', array('configuration' => $this->configuration));
      }
      
      if ($request->hasParameter('_reset')) 
      {
        $partialFilter = $this->getPartial('<?php echo $this->getModuleName() ?>/filters', array('form' => $this->filters, 'configuration' => $this->configuration));
      }
      
      $partialList = $this->getPartial('<?php echo $this->getModuleName() ?>/list', array('pager' => $this->pager, 'sort' => $this->sort, 'helper' => $this->helper));
      
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
  }
