<?php

/**
 * Visiteur form.
 *
 * @package    sf_sandbox
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class VisiteurForm extends BaseVisiteurForm
{
  public function configure()
  {
  	$this->setValidator('guid', new sfValidatorString(array('max_length' => 255, 'required' => false)));
    $this->setDefault('guid', Guid::generate());
    $this->setWidget('date_naissance',new sfWidgetFormJqueryDateVip(array('empty_values' => array('year' => '', 'month' => '', 'day' => ''), 'can_be_empty' => true, 'date_range' => array('min' => date('Y')-100, 'max' => date('Y')))));
  	$this->getWidget('preference_media_list')->setOption('renderer_class', 'sfWidgetFormSelectDoubleList');
    $this->getWidget('preference_media_list')->setOption('renderer_options', array('label_unassociated' => 'Disponibles', 'label_associated' => 'Séléctionnés'));
    $this->setDefault('langue_id', '');
    $this->getWidget('langue_id')->setOption('add_empty', true);

    $this->setValidator('password_son', new sfValidatorString(array('max_length' => 255, 'required' => true)));
    $this->setValidator('pseudo_son', new sfValidatorString(array('max_length' => 255, 'required' => true)));
    $this->setValidator('email', new sfValidatorEmail(array('max_length' => 255, 'required' => true)));

  	unset($this['updated_at'], $this['created_at'], $this['is_tosync']);
  }
}
