  /*
   * Search methods
   */
  protected function processSearchQuery(Doctrine_Query $query, $search)
  {
    $searchParts = explode(' ', $search);
    
    $rootAlias = $query->getRootAlias();
    $translationAlias = $rootAlias.'Translation';
    $table = Doctrine_Core::getTable('<?php echo $this->getModelClass() ?>');
    
    //$query->withI18n($this->getUser()->getCulture(), $this->getModelClass());
    
    foreach($searchParts as $searchPart)
    {
      $ors = array();
      $params = array();
      
      foreach($table->getColumns() as $columnName => $column)
      {        
        switch($column['type'])
        {
          case 'blob':
          case 'clob':
          case 'string':
          case 'enum':
          case 'date':
            $ors[] = $rootAlias.'.'.$columnName.' LIKE ?';
            $params[] = '%'.$searchPart.'%';
            break;
          case 'integer':
          case 'float':
          case 'decimal':
            if (is_numeric($searchPart))
            {
              $ors[] = $rootAlias.'.'.$columnName.' = ?';
              $params[] = $searchPart;
            }
            break;
          case 'boolean':
          case 'time':
          case 'timestamp':
          case 'date':
          default:
        }
      }
      
      if(count($ors))
      {
        $query->addWhere(implode(' OR ', $ors), $params);
      }
    }
  }
    
  
  protected function addSearchQuery($query)
  {
    if (!$search = trim($this->getSearch()))
    {
      return $query;
    }

    return $this->processSearchQuery($query, $search);
  }

  protected function getSearch()
  {
    return $this->getUser()->getAttribute('<?php echo $this->getModuleName() ?>.search', null, 'admin_module');
  }

  protected function setSearch($search)
  {
    $this->getUser()->setAttribute('<?php echo $this->getModuleName() ?>.search', $search, 'admin_module');
  }


