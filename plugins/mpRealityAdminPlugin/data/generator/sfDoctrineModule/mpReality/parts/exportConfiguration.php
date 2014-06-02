  public function getExportType()
  {
    return '<?php echo $this->escapeString(isset($this->config['export']['type']) ? $this->config['export']['type'] : mpExportRealityTypes::EXPORT_TYPE_XLS) ?>';
<?php unset($this->config['export']['type']) ?>
  }


  public function getExportForm($defaults = array(), $options = array())
  {
    return new <?php echo $this->getModuleName().'ExportRealityForm' ?>(array(),
    array_merge(array('fields' => $this->getExportDisplay(), 'type' => $this->getExporttype(), $options)));
  }

  public function getExportDefaultType()
  {
    return mpExportRealityTypes::EXPORT_TYPE_XLS;
  }




  