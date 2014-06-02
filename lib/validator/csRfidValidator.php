<?php

class csRfidValidator extends sfValidatorDoctrineChoice
{
 
  /**
   * @see sfValidatorDoctrineChoice
   */
  protected function doClean($value)
  {
    if ($query = $this->getOption('query'))
    {
      $query = clone $query;
    }
    else
    {
      $query = Doctrine_Core::getTable($this->getOption('model'))->createQuery();
    }

    if ($this->getOption('multiple'))
    {
      if (!is_array($value))
      {
        $value = array($value);
      }

      if (isset($value[0]) && !$value[0])
      {
        unset($value[0]);
      }

      $count = count($value);

      if ($this->hasOption('min') && $count < $this->getOption('min'))
      {
        throw new sfValidatorError($this, 'min', array('count' => $count, 'min' => $this->getOption('min')));
      }

      if ($this->hasOption('max') && $count > $this->getOption('max'))
      {
        throw new sfValidatorError($this, 'max', array('count' => $count, 'max' => $this->getOption('max')));
      }

      $query->andWhereIn(sprintf('%s.%s', $query->getRootAlias(), $this->getColumn()), $value);

      if ($query->count() != count($value))
      {
        throw new sfValidatorError($this, 'invalid', array('value' => $value));
      }
    }
    else
    {
     	 $value2 = '';
     	 $chaine='';
        for ($i=0; $i < strlen($value)-1; $i+=2)
        {
            $chaine[]= $value[$i].$value[$i+1];
        }
        $value2 = implode("", array_reverse($chaine));
        
        $query->andWhere(sprintf('(%s.%s = \''.$value.'\' OR %s.%s = \''.$value2.'\')', $query->getRootAlias(), $this->getColumn(), $query->getRootAlias(), $this->getColumn()));
      	
      	$result = $query->execute(array(), Doctrine::HYDRATE_ARRAY);
      	
      if (!count($result))
      {
        throw new sfValidatorError($this, 'invalid', array('value' => $value));
      }else{
      	$value = $result[0]['uid'];
      }
    }
    //die($value);
    return $value;
  }


}
