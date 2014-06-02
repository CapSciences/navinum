<?php

/**
 * RfidGroupeVisiteur form.
 *
 * @package    sf_sandbox
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class RfidGroupeVisiteurForm extends BaseRfidGroupeVisiteurForm
{
  public function configure()
  {
  	$this->validatorSchema->setOption('allow_extra_fields', true);
  	//$this->validatorSchema->setOption('filter_extra_fields', false);
	$this->setValidator('guid', new sfValidatorString(array('max_length' => 255, 'required' => false)));
    $this->setDefault('guid', Guid::generate());

	unset($this['updated_at'], $this['created_at'], $this['is_tosync']);

	  $this->widgetSchema['contexte_creation_id'] = new sfWidgetFormDoctrineChoice(array('model' => "Contexte", 'add_empty' => false));
    $this->validatorSchema['contexte_creation_id'] = new sfValidatorDoctrineChoice(array('model' => "Contexte", 'required' => false));
  }

  
}
