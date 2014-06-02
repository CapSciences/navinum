<?php

/**
 * Peripherique form.
 *
 * @package    sf_sandbox
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class PeripheriqueForm extends BasePeripheriqueForm
{
  public function configure()
  {
	$this->setValidator('guid', new sfValidatorString(array('max_length' => 255, 'required' => false)));
    $this->setDefault('guid', Guid::generate());
    $this->getWidget('flotte_id')->setOption('order_by', array('libelle', 'asc'));

      //$this->setWidget('start_at',new sfWidgetFormI18nDateTime(array('culture' => 'fr')));

    $this->setWidget('interactif_id', new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Interactif'), 'add_empty' => false, 'order_by' => array('libelle', 'asc'))));

///^[0-9]+$/
	//$this->validatorSchema['adresse_mac'] = new sfValidatorRegex(array('pattern' => '/^([0-9A-F]{2}[:-]){5}([0-9A-F]{2})$/'));
	$this->validatorSchema['adresse_mac'] = new sfValidatorString(array('required' => true));
	$this->validatorSchema['adresse_ip'] = new sfValidatorRegex(array('pattern' => '/^\b(?:\d{1,3}\.){3}\d{1,3}\b$/', 'required' => false));
	/* ^([0-9A-F]{2}[:-]){5}([0-9A-F]{2})$ */

	unset($this['updated_at'], $this['created_at']);
  }
}
