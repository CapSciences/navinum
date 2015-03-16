<?php

/**
 * UniversStatus filter form.
 *
 * @package    sf_sandbox
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class UniversStatusFormFilter extends BaseUniversStatusFormFilter
{
  public function configure()
  {
      $this->getWidget('libelle')->setOption('with_empty', false);
      $this->getWidget('level')->setOption('with_empty', false);
      $this->setWidget('created_at',new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormJqueryDateVip(array('format' => '%day%/%month%/%year%')), 'to_date' => new sfWidgetFormJqueryDateVip(array('format' => '%day%/%month%/%year%')), 'with_empty' => false)));

      $this->setWidget('gain_id', new sfWidgetFormDoctrineChoice(array('model' => 'Gain', 'order_by' => array('libelle', 'asc'), 'add_empty' => true)));
      $this->getWidgetSchema()->moveField('gain_id', sfWidgetFormSchema::AFTER, 'univers_id');
      $this->setValidator('gain_id', new sfValidatorDoctrineChoice(array('model' => 'Gain', 'column' => 'guid', 'required' => false)));


  }
}
