<?php
class assignGroupeForm extends sfForm
{

  public function configure()
  {
    parent::configure();

    $this->disableCSRFProtection();
    $this->setWidget('rfid_groupe_id', new sfWidgetFormDoctrineChoice(array('model' => 'RfidGroupe', 'add_empty' => true)));

    $this->setWidget('rfid_ids', new sfWidgetFormInputHidden());
    $this->setValidator('rfid_ids', new sfValidatorPass(array('required' => true)));

    $this->setValidator('rfid_groupe_id' , new sfValidatorDoctrineChoice(array('required' => true, 'model' => 'RfidGroupe', 'column' => 'guid')));

    $this->getWidgetSchema()->setNameFormat('assign[%s]');
    $this->widgetSchema->setLabels(array(''    => 'Groupe à assigner'));
  }
}