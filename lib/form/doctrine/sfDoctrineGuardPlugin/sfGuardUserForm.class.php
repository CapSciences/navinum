<?php

/**
 * sfGuardUser form.
 *
 * @package    sf_sandbox
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrinePluginFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class sfGuardUserForm extends PluginsfGuardUserForm
{
  public function configure()
  {
    $permissions = Doctrine_Core::getTable('sfGuardPermission')->findAll();
    $choices = array();
    foreach ($permissions as $permission)
    {
      $choices[$permission->getId()] = $permission->getName();
    }
    $this->setWidget('permissions', new sfWidgetFormSelectRadio(array('choices' => $choices)));
    $this->setValidator('permissions', new sfValidatorChoice(array('choices' => array_keys($choices))));
    $this->setValidator('id', new sfValidatorString(array('max_length' => 255, 'required' => false)));
	$this->setDefault('id', Guid::generateId());
    $this->setWidget('exposition_list', new sfWidgetFormDoctrineChoice(array('multiple' => true, 'model' => 'Exposition', 'renderer_class' => 'sfWidgetFormSelectDoubleList')));
    $this->getWidget('exposition_list')->setOption('renderer_options', array('label_unassociated' => 'Disponibles', 'label_associated' => 'Séléctionnés'));
    $this->getWidgetSchema()->moveField('exposition_list', sfWidgetFormSchema::AFTER, 'permissions');
    unset($this['updated_at'], $this['created_at'], $this['last_login'], $this['groups_list'], $this['permissions_list'],  $this['is_super_admin'], $this['algorithm'], $this['salt'], $this['is_tosync']);
  }

  public function  savePermissionsList($con = null)
  {
    $value = $this->getValue('permissions');
    $existing = $this->object->Permissions->getPrimaryKeys();
    $this->object->unlink('Permissions', array_values($existing));
    $this->object->link('Permissions', $value);
  }

  public function updateDefaultsFromObject()
  {
    $permissions = $this->object->Permissions->getPrimaryKeys();
    if (isset($permissions[0]))
    {
      $this->setDefault('permissions', $permissions[0]);
    }
    parent::updateDefaultsFromObject();

    $this->setDefault('password', '');
  }
}
