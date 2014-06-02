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

    $this->getWidget('parcours_list')->setOption('order_by', array('ordre', 'asc'));
    $this->getWidget('parcours_list')->setOption('renderer_class', 'sfWidgetFormSelectDoubleListVip');
    $this->getWidget('parcours_list')->setOption('renderer_options', array('label_unassociated' => 'Disponibles', 'label_associated' => 'Séléctionnés'));
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
}
