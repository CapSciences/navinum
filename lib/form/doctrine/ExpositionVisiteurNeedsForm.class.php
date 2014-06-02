<?php

/**
 * ExpositionVisiteurNeeds form.
 *
 * @package    sf_sandbox
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ExpositionVisiteurNeedsForm extends BaseExpositionVisiteurNeedsForm
{
  public function configure()
  {
  	unset($this['updated_at'], $this['created_at']);
  	$this->setValidator('guid', new sfValidatorString(array('max_length' => 255, 'required' => false)));
    $this->setDefault('guid', Guid::generate());
    $this->getWidget('exposition_id')->setOption('order_by', array('libelle', 'asc'));

    $this->getWidget('langue_list')->setOption('order_by', array('libelle', 'asc'));
    $this->getWidget('langue_list')->setOption('renderer_class', 'sfWidgetFormSelectDoubleListVip');
    $this->getWidget('langue_list')->setOption('renderer_options', array('label_unassociated' => 'Disponibles', 'label_associated' => 'Sélectionnés'));
    $this->getWidget('preference_media_list')->setOption('order_by', array('libelle', 'asc'));
    $this->getWidget('preference_media_list')->setOption('renderer_class', 'sfWidgetFormSelectDoubleListVip');
  	$this->getWidget('preference_media_list')->setOption('renderer_options', array('label_unassociated' => 'Disponibles', 'label_associated' => 'Sélectionnés'));
  }
}
