<?php

/**
 * Exposition filter form.
 *
 * @package    sf_sandbox
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ExpositionFormFilter extends BaseExpositionFormFilter
{
  public function configure()
  {
    unset($this['sf_guard_user_list']);
    $this->getWidget('libelle')->setOption('with_empty', false);
    $this->setWidget('created_at',new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormJqueryDateVip(array('format' => '%day%/%month%/%year%')), 'to_date' => new sfWidgetFormJqueryDateVip(array('format' => '%day%/%month%/%year%')), 'with_empty' => false)));
    $this->setWidget('updated_at',new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormJqueryDateVip(array('format' => '%day%/%month%/%year%')), 'to_date' => new sfWidgetFormJqueryDateVip(array('format' => '%day%/%month%/%year%')), 'with_empty' => false)));
    $this->setWidget('start_at',new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormJqueryDateVip(array('format' => '%day%/%month%/%year%')), 'to_date' => new sfWidgetFormJqueryDateVip(array('format' => '%day%/%month%/%year%')), 'with_empty' => false)));
    $this->setWidget('end_at',new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormJqueryDateVip(array('format' => '%day%/%month%/%year%')), 'to_date' => new sfWidgetFormJqueryDateVip(array('format' => '%day%/%month%/%year%')), 'with_empty' => false)));
    $this->getWidget('parcours_list')->setOption('renderer_class', 'sfWidgetFormSelectDoubleList');
    $this->getWidget('parcours_list')->setOption('renderer_options', array('label_unassociated' => 'Disponibles', 'label_associated' => 'Séléctionnés'));
  }
}
