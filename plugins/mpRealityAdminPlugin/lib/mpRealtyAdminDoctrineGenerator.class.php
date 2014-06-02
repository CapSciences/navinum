<?php

/**
 * mpRealtyAdmin theme
 *
 * @author: Mike Plavonil <mikeplavo@gmail.com>
 *
 */
class mpRealtyAdminDoctrineGenerator extends sfDoctrineGenerator
{

  /**
   * Returns HTML code for a field.
   *
   * @param sfModelGeneratorConfigurationField $field The field
   *
   * @return string HTML code
   */
  public function renderField($field)
  {
    $fieldName = $field->getName();

    $html = $this->getColumnGetter($fieldName, true);

    if ($renderer = $field->getRenderer())
    {
      $html = sprintf('%s ? call_user_func_array(%s, array_merge(array(%s), %s)) : "&nbsp;"', $html, $this->asPhp($renderer), $html, $this->asPhp($field->getRendererArguments()));
    }
    else if ($field->isComponent())
    {
      return sprintf("get_component('%s', '%s', array('type' => 'list', '%s' => \$%s))", $this->getModuleName(), $fieldName, $this->getSingularName(), $this->getSingularName());
    }
    else if ($field->isPartial())
    {
      return sprintf("get_partial('%s/%s', array('type' => 'list', '%s' => \$%s))", $this->getModuleName(), $fieldName, $this->getSingularName(), $this->getSingularName());
    }
    else if ('Date' == $field->getType())
    {
      $html = sprintf("false !== strtotime($html) ? format_date(%s, \"%s\") : '&nbsp;'", $html, $field->getConfig('date_format', 'f'));
    }
    else if ('Boolean' == $field->getType())
    {
      $html = "sprintf('<span class=\"bloc bool_%s {field: \'%s\'}\" title=\"%s\"></span>', ".$html." ? 'tick' : 'cross', '".$fieldName."', __('Cliquer pour modifier'))";
    }

    if ($field->isLink())
    {
      $html = sprintf("link_to(%s, '%s', \$%s)", $html, $this->getUrlForAction('edit'), $this->getSingularName());
    }

    return $html;
  }

}
