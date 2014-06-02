<?php

/**
 * preference_media actions.
 *
 * @package    sf_sandbox
 * @subpackage preference_media
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z xavier $
 * @see        class::autopreference_mediaActions
 */
class preference_mediaActions extends autopreference_mediaActions
{
  /**
   * Returns the list of validators for a get request.
   * @return  array  an array of validators
   */
  public function getIndexValidators()
  {
    $validators = array();
    $validators['guid'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['libelle'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['created_at'] = new sfValidatorDateTime(array('required' => false));
    $validators['updated_at'] = new sfValidatorDateTime(array('required' => false));

    return $validators;
  }
}
