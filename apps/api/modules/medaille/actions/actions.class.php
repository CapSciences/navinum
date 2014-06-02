<?php

/**
 * medaille actions.
 *
 * @package    sf_sandbox
 * @subpackage medaille
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z xavier $
 * @see        class::automedailleActions
 */
class medailleActions extends automedailleActions
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
    $validators['exposition_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['medaille_type_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['interactif_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['image'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['description'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['condition_obtention'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['created_at'] = new sfValidatorDateTime(array('required' => false));
    $validators['updated_at'] = new sfValidatorDateTime(array('required' => false));

    return $validators;
  }

}
