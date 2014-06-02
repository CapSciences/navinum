<?php

/**
 * Rfid filter form.
 *
 * @package    sf_sandbox
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class RfidFormFilter extends BaseRfidFormFilter
{
  public function configure()
  {

    $this->setWidget('groupe_id' , new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('RfidGroupe'), 'add_empty' => true)));
    $this->setWidget('type'      , new sfWidgetFormSelect(array('choices' => array_merge(array(''), sfConfig::get('app_rfid_types', array())))));
    $this->setWidget('is_active' , new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))));
    $this->setWidget('is_resettable' , new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))));
    $this->setWidget('is_tosync'     , new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))));
    $this->setWidget('created_at'    , new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)));
    $this->setWidget('updated_at'    , new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)));

    unset($this['updated_at'], $this['created_at'], $this['is_tosync'], $this['uid']);
  }
}
