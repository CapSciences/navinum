<?php

/**
 * exposition_visiteur_needs actions.
 *
 * @package    sf_sandbox
 * @subpackage exposition_visiteur_needs
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z xavier $
 * @see        class::autoexposition_visiteur_needsActions
 */
class exposition_visiteur_needsActions extends autoexposition_visiteur_needsActions
{
  /**
   * Returns the list of validators for a get request.
   * @return  array  an array of validators
   */
  public function getIndexValidators()
  {
    $validators = array();
    $validators['guid'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['exposition_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['has_langue'] = new sfValidatorBoolean(array('required' => false));
    $validators['has_genre'] = new sfValidatorBoolean(array('required' => false));
    $validators['has_date_naissance'] = new sfValidatorBoolean(array('required' => false));
    $validators['has_code_postal'] = new sfValidatorBoolean(array('required' => false));
    $validators['has_ville'] = new sfValidatorBoolean(array('required' => false));
    $validators['has_adresse'] = new sfValidatorBoolean(array('required' => false));
    $validators['has_prenom'] = new sfValidatorBoolean(array('required' => false));
    $validators['has_nom'] = new sfValidatorBoolean(array('required' => false));
    $validators['has_num_mobile'] = new sfValidatorBoolean(array('required' => false));
    $validators['has_facebook_id'] = new sfValidatorBoolean(array('required' => false));
    $validators['has_google_id'] = new sfValidatorBoolean(array('required' => false));
    $validators['has_twitter_id'] = new sfValidatorBoolean(array('required' => false));
    $validators['has_flickr_id'] = new sfValidatorBoolean(array('required' => false));
    $validators['has_dailymotion_id'] = new sfValidatorBoolean(array('required' => false));
    $validators['created_at'] = new sfValidatorDateTime(array('required' => false));
    $validators['updated_at'] = new sfValidatorDateTime(array('required' => false));

    return $validators;
  }
  
  protected function configureFields()
  {
    
    foreach($this->objects as $key=> $object)
    {
      unset($this->objects[$key]['Exposition']);      
    }
  }
}
