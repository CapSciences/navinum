<?php

/**
 * Parcours form.
 *
 * @package    sf_sandbox
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ParcoursForm extends BaseParcoursForm
{
  public function configure()
  {
  	unset($this['updated_at'], $this['created_at'], $this['exposition_list']);

    $this->getWidget('interactif_list')->setOption('order_by', array('ordre', 'asc'));
    $this->getWidget('interactif_list')->setOption('renderer_class', 'sfWidgetFormSelectDoubleListVip');
    $this->getWidget('interactif_list')->setOption('renderer_options', array('label_unassociated' => 'Disponibles', 'label_associated' => 'Séléctionnés'));
    $this->setValidator('guid', new sfValidatorString(array('max_length' => 255, 'required' => false)));
    $this->setDefault('guid', Guid::generate());
    if(!sfContext::getInstance()->getUser()->hasPermission('admin'))
    {
      $this->widgetSchema->setHelp('libelle','<b>Attention :</b> le parcours ne sera visible dans le listing que pendant 8h après l\'enregistrement. Il sera définitivement visible une fois attaché à une exposition.');
    }
  }
}
