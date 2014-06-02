public function getPagerClass()
{
return '<?php echo isset($this->config['list']['pager_class']) ? $this->config['list']['pager_class'] : 'sfDoctrinePager' ?>';
<?php unset($this->config['list']['pager_class']) ?>
}



public function getPagerMaxPerPage()
{
  $max_per_page = sfContext::getInstance()->getUser()->getAttribute('<?php echo $this->getModuleName() ?>.max_per_page', null, 'admin_module');


  if ($max_per_page === null)
  {
    return <?php echo isset($this->config['list']['max_per_page']) ? (integer) $this->config['list']['max_per_page'] : 20 ?>;
<?php unset($this->config['list']['max_per_page']) ?>
  }
  else
  {
    return $max_per_page;
  }
}