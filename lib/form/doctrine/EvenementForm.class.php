<?php

/**
 * Evenement form.
 *
 * @package    sf_sandbox
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class EvenementForm extends BaseEvenementForm
{
  public function configure()
  {
	unset($this['updated_at'], $this['created_at']);

	$this->setWidget('start_at',new sfWidgetFormI18nDateTime(array('culture' => 'fr')));
  	$this->setWidget('end_at',new sfWidgetFormI18nDateTime(array('culture' => 'fr')));


    $this->setValidator('guid', new sfValidatorString(array('max_length' => 255, 'required' => false)));
    $this->setDefault('guid', Guid::generate());


	  $this->getWidget('exposition_list')->setOption('order_by', array('libelle', 'asc'));
    $this->getWidget('exposition_list')->setOption('renderer_class', 'sfWidgetFormSelectDoubleListVip');
    $this->getWidget('exposition_list')->setOption('renderer_options', array('label_unassociated' => 'Disponibles', 'label_associated' => 'Séléctionnés'));
    

  $this->validatorSchema->setPostValidator( new sfValidatorOr( array (
        new sfValidatorAnd( array (
          new sfValidatorSchemaCompare ('start_at', sfValidatorSchemaCompare::NOT_EQUAL, null ),
          new sfValidatorSchemaCompare ('end_at', sfValidatorSchemaCompare::EQUAL, null )
        )) ,
        new sfValidatorSchemaCompare('start_at', sfValidatorSchemaCompare::LESS_THAN_EQUAL, 'end_at',array('throw_global_error' => false), array('invalid' => 'The start date ("%left_field%") must be before the end date ("%right_field%")' ))
    )));

  }
}
