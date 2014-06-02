<?php

/**
 * ExpositionVisiteurNeeds filter form.
 *
 * @package    sf_sandbox
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ExpositionVisiteurNeedsFormFilter extends BaseExpositionVisiteurNeedsFormFilter
{
  public function configure()
  {
  	$this->setWidget('created_at',new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormJqueryDateVip(array('format' => '%day%/%month%/%year%')), 'to_date' => new sfWidgetFormJqueryDateVip(array('format' => '%day%/%month%/%year%')), 'with_empty' => false)));
    $this->setWidget('updated_at',new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormJqueryDateVip(array('format' => '%day%/%month%/%year%')), 'to_date' => new sfWidgetFormJqueryDateVip(array('format' => '%day%/%month%/%year%')), 'with_empty' => false)));
    $this->getWidget('preference_media_list')->setOption('renderer_class', 'sfWidgetFormSelectDoubleList');
    $this->getWidget('preference_media_list')->setOption('renderer_options', array('label_unassociated' => 'Disponibles', 'label_associated' => 'Séléctionnés'));
  }
}
