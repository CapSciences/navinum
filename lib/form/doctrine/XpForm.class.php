<?php

/**
 * Xp form.
 *
 * @package    sf_sandbox
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class XpForm extends BaseXpForm
{
  public function configure()
  {
	$this->setValidator('guid', new sfValidatorString(array('max_length' => 255, 'required' => false)));
    $this->setDefault('guid', Guid::generate());
	$this->setWidget('visiteur_id', new sfWidgetFormInputText());
	$this->getWidget('typologie_id')->setOption('order_by', array('libelle', 'asc'));

	unset($this['updated_at'], $this['created_at']);
  }
}
