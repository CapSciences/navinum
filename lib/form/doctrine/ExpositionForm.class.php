<?php

/**
 * Exposition form.
 *
 * @package    sf_sandbox
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ExpositionForm extends BaseExpositionForm
{
  public function configure()
  {
    unset($this['updated_at'], $this['created_at']);

    $default_theme = Doctrine_Core::getTable('Theme')->findOneBy('libelle', 'exposition');

    $theme = '';
    if($default_theme){
        $theme = $default_theme->getGuid();
    }
    $this->setDefault('theme_id', $theme);

    $this->getWidget('parcours_list')->setOption('order_by', array('ordre', 'asc'));
    $this->getWidget('parcours_list')->setOption('renderer_class', 'sfWidgetFormSelectDoubleListVip');
    $this->getWidget('parcours_list')->setOption('renderer_options', array('label_unassociated' => 'Disponibles', 'label_associated' => 'Sélectionnés'));
    $this->setValidator('guid', new sfValidatorString(array('max_length' => 255, 'required' => false)));
    $this->setDefault('guid', Guid::generate());

    $this->setWidget('start_at',new sfWidgetFormJqueryDateVip(array('empty_values' => array('year' => '', 'month' => '', 'day' => ''), 'can_be_empty' => true)));
    $this->setWidget('end_at',new sfWidgetFormJqueryDateVip(array('empty_values' => array('year' => '', 'month' => '', 'day' => ''), 'can_be_empty' => true)));

  $this->validatorSchema->setPostValidator( new sfValidatorOr( array (
        new sfValidatorAnd( array (
          new sfValidatorSchemaCompare ('start_at', sfValidatorSchemaCompare::NOT_EQUAL, null ),
          new sfValidatorSchemaCompare ('end_at', sfValidatorSchemaCompare::EQUAL, null )
        )) ,
        new sfValidatorSchemaCompare('start_at', sfValidatorSchemaCompare::LESS_THAN_EQUAL, 'end_at',array('throw_global_error' => false), array('invalid' => 'The start date ("%left_field%") must be before the end date ("%right_field%")' ))
    )));

  }
  
  protected function doSave($con = null)
  {
    parent::doSave($con);
    if(!sfContext::getInstance()->getUser()->hasPermission('admin'))
    {
	    $exposition_list = new UserExposition();
	    $exposition_list->setUserId(sfContext::getInstance()->getUser()->getGuardUser()->getId());
	    $exposition_list->setExpositionId($this->getObject()->getGuid());
	    $exposition_list->save();
    }
  }

    public function saveParcoursList($con = null)
    {
        if (!$this->isValid())
        {
            throw $this->getErrorSchema();
        }

        if (!isset($this->widgetSchema['parcours_list']))
        {
            // somebody has unset this widget
            return;
        }

        if (null === $con)
        {
            $con = $this->getConnection();
        }

        $existing = $this->object->Parcours->getPrimaryKeys();
        $values = $this->getValue('parcours_list');
        if (!is_array($values))
        {
            $values = array();
        }

        $unlink = array_diff($existing, $values);
        if (count($unlink))
        {
            $this->object->unlink('Parcours', array_values($unlink));
            $delete_log = new DeleteLog();
            $delete_log->setGuid(Guid::generate());
            $delete_log->setExtra('exposition_id: "'. $this->getObject()->getGuid() . '"|parcours_id: "'. implode('", "', array_values($unlink)).'"');
            $delete_log->setModelName('ExpositionsParcours');
            $delete_log->save();
        }

        $link = array_diff($values, $existing);
        if (count($link))
        {
            $this->object->link('Parcours', array_values($link));
        }
    }

    public function savesfGuardUserList($con = null)
    {
        if (!$this->isValid())
        {
            throw $this->getErrorSchema();
        }

        if (!isset($this->widgetSchema['sf_guard_user_list']))
        {
            // somebody has unset this widget
            return;
        }

        if (null === $con)
        {
            $con = $this->getConnection();
        }

        $existing = $this->object->sfGuardUser->getPrimaryKeys();
        $values = $this->getValue('sf_guard_user_list');
        if (!is_array($values))
        {
            $values = array();
        }

        $unlink = array_diff($existing, $values);
        if (count($unlink))
        {
            $this->object->unlink('sfGuardUser', array_values($unlink));

            $delete_log = new DeleteLog();
            $delete_log->setGuid(Guid::generate());
            $delete_log->setExtra('exposition_id: "'. $this->getObject()->getGuid() . '"|user_id: "'. implode('", "', array_values($unlink)).'"');
            $delete_log->setModelName('UserExposition');
            $delete_log->save();
        }

        $link = array_diff($values, $existing);
        if (count($link))
        {
            $this->object->link('sfGuardUser', array_values($link));
        }
    }
}
