[?php

/**
 * <?php echo $this->getModuleName() ?> module configuration.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage <?php echo $this->getModuleName()."\n" ?>
 * @author     ##AUTHOR_NAME##
 */
abstract class Base<?php echo ucfirst($this->getModuleName()) ?>GeneratorHelper extends sfModelGeneratorHelper
{
  public function getUrlForAction($action)
  {
    return 'list' == $action ? '<?php echo $this->params['route_prefix'] ?>' : '<?php echo $this->params['route_prefix'] ?>_'.$action;
  }

  public function linkToShow($object, $params)
  {
    <?php if ($this->params['with_show']): ?>
      return '<li class="sf_admin_action_show">'.link_to(__($params['label'] != 'Show' ? $params['label'] : 'Aperçu', array(), 'sf_admin'), $this->getUrlForAction('show'), $object).'</li>';
    <?php else: ?>
      return '';
    <?php endif; ?>
  }
  
  public function linkToDeleteList($object, $params)
  {
    if ($object->isNew())
    {
      return '';
    }
	
	$link = '<li class="sf_admin_action_delete">'.link_to(__($params['label'], array(), 'sf_admin'), $this->getUrlForAction('delete'), $object, array('method' => 'delete', 'confirm' => !empty($params['confirm']) ? __($params['confirm'], array(), 'sf_admin') : $params['confirm'])).'</li>';

    return str_replace('f.submit()', 'executeJSONResponse(\'POST\', this.href, $(f).serialize(), jQuery(\'#list_bloc_content\'))', $link);
  }
  
  public function linkToDelete($object, $params)
  {
    if ($object->isNew())
    {
      return '';
    }
    

    return link_to('<span class="sprite_icon">'.__($params['label'], array(), 'sf_admin').'</span>', $this->getUrlForAction('delete'), $object, array('method' => 'delete', 'confirm' => !empty($params['confirm']) ? __($params['confirm'], array(), 'sf_admin') : $params['confirm'], 'class' => 'graybutton delete_item f_right'));
  }  
  
  public function linkToSave($object, $params)  
  {
	return '<input type="submit" class="graybutton f_left" '.__($params['label'], array(), 'sf_admin').'/>' ;
  }
  
  public function linkToSaveAndAdd($object, $params)
  {
    if (!$object->isNew())
    {
      return '';
    }

    return '<input type="submit" class="graybutton" value="'.__($params['label'], array(), 'sf_admin').'" name="_save_and_add" />';
  }  
  
  public function linkToList($params)
  {
    return link_to('<span class="sprite_icon">'.__($params['label'], array(), 'sf_admin').'</span>', '@'.$this->getUrlForAction('list'),array('class' => 'graybutton f_left return_to_list'));
  }  
  
  public function linkToEditForShow($object, $params)
  {
    return link_to('<span class="sprite_icon">'.__($params['label'], array(), 'sf_admin').'</span>', $this->getUrlForAction('edit'), $object, array('class' => 'graybutton edit_item f_left'));
  }

/*  public function linkToExport($object, $params)
  {
    return '<li class="sf_admin_action_export">'.link_to(__($params['label'] != 'Show' ? $params['label'] : 'Aperçu', array(), 'sf_admin'), $this->getUrlForAction('show'), $object).'</li>';
  }*/
  

  public function getRouteArrayForAction($action, $module,$object = null)
  {
    $route = array('module' => $module);
    
    if ('list' !== $action)
    {
      $route['action'] = $action;
    }
      
    return $route;
  }

  public function getExportColumnHeaders($headers)
  {
    if(isset($headers[0]) && strpos(strtolower($headers[0]), 'id') === 0) $headers[0] = ' '.$headers[0];
    return implode("\t", $headers);
  }

  public function getExportRecordRow($record, $fields)
  {
    $row = array();
    foreach ($fields as $field)
    {
      $row[] = $record[$field];
    }
    return implode("\t", $row);
  }
}
