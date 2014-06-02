<?php

/**
 * PreferenceMediaTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class PreferenceMediaTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object PreferenceMediaTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('PreferenceMedia');
    }

  public function getForAutocomplete($name)
  {
    $results = $this->createQuery('preference_media')
      ->select('preference_media.libelle')
      ->where('preference_media.libelle LIKE ?', '%'.$name.'%')
      ->orderBy('preference_media.libelle')
      ->limit(20)
      ->execute();
    $results_array = array();

    foreach ($results as $result)
    {
      $results_array[$result->getLibelle()] = sprintf('%s', addslashes($result->getLibelle()));
    }

    return $results_array;
  }
}