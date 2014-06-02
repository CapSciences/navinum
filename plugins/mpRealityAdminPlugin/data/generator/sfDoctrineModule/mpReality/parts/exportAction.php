  public function executeExport(sfWebRequest $request)
  {
    $this->form  = $this->configuration->getExportForm(array(), array('configuration' => $this->configuration));

    if ($request->isMethod('POST'))
    {
      $headers = array();
      $this->form->bind($request->getParameter('export'));
      if ($this->form->isValid())
      {
        foreach ($request->getParameter('export') as $name => $field)
        {
          $headers[] = sfInflector::humanize($name);
          $fields[] = $name;
        }

        $rows = array($this->helper->getExportColumnHeaders($headers));

        $records = $this->getExportRecordsCollection();
        foreach ($records as $record)
        {
          $rows[] = $this->helper->getExportRecordRow($record, $fields);
        }

        $this->getContext()->getResponse()->setContentType('text/xls');
        $this->getContext()->getResponse()->setHttpHeader('Content-Disposition', 'attachment;filename='.$this->configuration->getExportFilename().'.xls;');
        return $this->renderText(implode("\n", $rows));
      }
    }
  }

  protected function getExportRecordsCollection()
  {
    $query = $this->getPager()->getQuery();

    return $query->execute();
  }
