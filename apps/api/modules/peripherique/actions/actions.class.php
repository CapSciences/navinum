<?php

/**
 * peripherique actions.
 *
 * @package    sf_sandbox
 * @subpackage peripherique
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z xavier $
 * @see        class::autoperipheriqueActions
 */
class peripheriqueActions extends autoperipheriqueActions
{
  public function getIndexValidators()
  {
  	$validators = array();
    $validators['guid'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['adresse_mac'] = new sfValidatorString(array('max_length' => 64, 'required' => false));
    $validators['adresse_ip'] = new sfValidatorString(array('max_length' => 32, 'required' => false));
    $validators['flotte_id'] = new sfValidatorInteger(array('required' => false));
    $validators['is_tosync'] = new sfValidatorBoolean(array('required' => false));
    $validators['created_at'] = new sfValidatorDateTime(array('required' => false));
    $validators['updated_at'] = new sfValidatorDateTime(array('required' => false));

    return $validators;
  }
}
