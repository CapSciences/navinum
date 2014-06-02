<?php
class mpExportRealityForm extends sfForm
{

  public function configure()
  {
    parent::configure();

$this->disableCSRFProtection();
    //$this->setWidget('title', new sfWidgetFormInput());
    //$this->setValidator('title', new sfValidatorString(array('required' => true)));
    foreach ($this->getFields() as $fieldDecorator)
    {
      $this->setWidget($fieldDecorator, new sfWidgetFormInputCheckbox());
      $this->setValidator($fieldDecorator, new sfValidatorBoolean());
      $this->setDefault($fieldDecorator, true);
    }

    $this->getWidgetSchema()->setNameFormat('export[%s]');

    $this->setDefault('title', $this->getOption('title'));
    $this->setDefault('type', $this->getOption('type'));
  }

  public function getFields()
  {
    return $this->getOption('fields', array());
  }
}