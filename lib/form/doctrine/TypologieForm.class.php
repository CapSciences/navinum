<?php

/**
 * Typologie form.
 *
 * @package    sf_sandbox
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class TypologieForm extends BaseTypologieForm
{
  public function configure()
  {
	$this->setValidator('guid', new sfValidatorString(array('max_length' => 255, 'required' => false)));
    $this->setDefault('guid', Guid::generate());

	unset($this['updated_at'], $this['created_at']);
  }
}
