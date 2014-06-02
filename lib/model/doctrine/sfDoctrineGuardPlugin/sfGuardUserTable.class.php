<?php

/**
 * sfGuardUserTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class sfGuardUserTable extends PluginsfGuardUserTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object sfGuardUserTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('sfGuardUser');
    }

  public function getForAutocomplete($name)
  {
    $results = $this->createQuery('sf_guard_user')
      ->select('sf_guard_user.first_name, sf_guard_user.last_name')
      ->where('sf_guard_user.first_name LIKE ?', '%'.$name.'%')
      ->orWhere('sf_guard_user.last_name LIKE ?', '%'.$name.'%')
      ->orderBy('sf_guard_user.first_name')
      ->limit(20)
      ->execute();
    $results_array = array();

    foreach ($results as $result)
    {
      $results_array[$result->getFirstName()] = sprintf('%s', addslashes($result->getFirstName().' '.$result->getLastName()));
    }

    return $results_array;
  }
}