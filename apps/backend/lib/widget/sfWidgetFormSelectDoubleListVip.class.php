<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormSelectDoubleList represents a multiple select displayed as a double list.
 *
 * This widget needs some JavaScript to work. So, you need to include the JavaScripts
 * files returned by the getJavaScripts() method.
 *
 * If you use symfony 1.2, it can be done automatically for you.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWidgetFormSelectDoubleList.class.php 12412 2008-10-29 14:19:06Z fabien $
 */
class sfWidgetFormSelectDoubleListVip extends sfWidgetFormSelectDoubleList
{

  /**
   * Renders the widget.
   *
   * @param  string $name        The element name
   * @param  string $value       The value selected in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    if (is_null($value))
    {
      $value = array();
    }

    $choices = $this->getOption('choices');
    if ($choices instanceof sfCallable)
    {
      $choices = $choices->call();
    }

    $associated = array();
    $unassociated = array();
    foreach ($choices as $key => $option)
    {
      if (in_array(strval($key), $value))
      {
        $associated[$key] = $option;
      }
      else
      {
        $unassociated[$key] = $option;
      }
    }

    // we sort unassociated array by name
    //asort($unassociated);
    array_multisort(array_map('strtolower', $unassociated), $unassociated);
    
    $size = isset($attributes['size']) ? $attributes['size'] : (isset($this->attributes['size']) ? $this->attributes['size'] : 10);

    $associatedWidget = new sfWidgetFormSelect(array('multiple' => true, 'choices' => $associated), array('size' => $size, 'class' => $this->getOption('class_select').'-selected'));
    $unassociatedWidget = new sfWidgetFormSelect(array('multiple' => true, 'choices' => $unassociated), array('size' => $size, 'class' => $this->getOption('class_select')));

    return strtr($this->getOption('template'), array(
      '%class%'              => $this->getOption('class'),
      '%class_select%'       => $this->getOption('class_select'),
      '%id%'                 => $this->generateId($name),
      '%label_associated%'   => $this->getOption('label_associated'),
      '%label_unassociated%' => $this->getOption('label_unassociated'),
      '%associate%'          => sprintf('<a href="#" onclick="%s">%s</a>', 'sfDoubleList.move(\'unassociated_'.$this->generateId($name).'\', \''.$this->generateId($name).'\'); return false;', $this->getOption('associate')),
      '%unassociate%'        => sprintf('<a href="#" onclick="%s">%s</a>', 'sfDoubleList.move(\''.$this->generateId($name).'\', \'unassociated_'.$this->generateId($name).'\'); return false;', $this->getOption('unassociate')),
      '%associated%'         => $associatedWidget->render($name),
      '%unassociated%'       => $unassociatedWidget->render('unassociated_'.$name),
    ));
  }

}
