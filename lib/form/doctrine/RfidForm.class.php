<?php

/**
 * Rfid form.
 *
 * @package    sf_sandbox
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class RfidForm extends BaseRfidForm
{
  public function configure()
  {
	 $this->setWidget('uid', new sfWidgetFormInputText());
     $choices = sfConfig::get('app_rfid_types', array());

     $this->setWidget('type', new sfWidgetFormSelect(array('choices' => $choices)));
	 $this->setDefault('type', 2);
	 unset($this['updated_at'], $this['created_at'], $this['is_tosync']);
     $this->setValidator('uid', new sfValidatorString(array('max_length' => 255, 'required' => false)));
  }
}
