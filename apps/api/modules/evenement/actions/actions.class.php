<?php

/**
 * evenement actions.
 *
 * @package    sf_sandbox
 * @subpackage evenement
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z xavier $
 * @see        class::autoevenementActions
 */
class evenementActions extends autoevenementActions
{
  /**
   * Returns the list of validators for a get request.
   * @return  array  an array of validators
   */
  public function getIndexValidators()
  {
  	$validators = array();
    $validators['guid'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['latitude'] = new sfValidatorString(array('max_length' => 32, 'required' => false));
    $validators['longitude'] = new sfValidatorString(array('max_length' => 32, 'required' => false));
    $validators['start_at'] = new sfValidatorDateTime(array('required' => false));
    $validators['end_at'] = new sfValidatorDateTime(array('required' => false));
    $validators['created_at'] = new sfValidatorDateTime(array('required' => false));
    $validators['updated_at'] = new sfValidatorDateTime(array('required' => false));

    return $validators;
  }

}
