<?php

/**
 * rfid actions.
 *
 * @package    sf_sandbox
 * @subpackage rfid
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z xavier $
 * @see        class::autorfidActions
 */
class rfidActions extends autorfidActions
{
	/**
   * Returns the list of validators for a get request.
   * @return  array  an array of validators
   */
  public function getIndexValidators()
  {
    $validators = array();
    $validators['uid'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['groupe_id'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['type'] = new sfValidatorString(array('max_length' => 64, 'required' => false));
    $validators['valeur1'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['valeur2'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['valeur3'] = new sfValidatorString(array('max_length' => 255, 'required' => false));
    $validators['is_active'] = new sfValidatorBoolean(array('required' => false));
    $validators['is_resettable'] = new sfValidatorBoolean(array('required' => false));
    $validators['is_tosync'] = new sfValidatorBoolean(array('required' => false));
    $validators['created_at'] = new sfValidatorDateTime(array('required' => false));
    $validators['updated_at'] = new sfValidatorDateTime(array('required' => false));

    return $validators;
  }

  public function query($params)
  {
    $q = Doctrine_Query::create()
      ->from($this->model.' '.$this->model);

    if (isset($sort))
    {
      $q->orderBy($sort);
    }

    if(isset($params['navinum_id']))
    {
        $chaine='';
        for ($i=0; $i < strlen($params['navinum_id'])-1; $i+=2)
        {
            $chaine[]= $params['navinum_id'][$i].$params['navinum_id'][$i+1];
        }
        $inverted = implode("", array_reverse($chaine));
        $q->andWhere('('.$this->model.'.navinum_id = ? OR '.$this->model.'.navinum_id = ? )', array($params['navinum_id'], $inverted));
        unset($params['navinum_id']);
    }


    if (isset($params['uid']))
    {
      $values = explode(',', $params['uid']);

      if (count($values) == 1)
      {

        $chaine='';
        for ($i=0; $i < strlen($params['uid'])-1; $i+=2)
        {
            $chaine[]= $params['uid'][$i].$params['uid'][$i+1];
        }
        $inverted = implode("", array_reverse($chaine));
        $q->andWhere('('.$this->model.'.uid = ? OR '.$this->model.'.uid = ? )', array($params['uid'], $inverted));
        unset($params['uid']);
      }
      else
      {
        $q->whereIn($this->model.'.uid', $values);
      }

      unset($params['uid']);
    }
    foreach ($params as $name => $value)
    {
      $q->andWhere($this->model.'.'.$name.' = ?', $value);
    }

    return $q;
  }



}
