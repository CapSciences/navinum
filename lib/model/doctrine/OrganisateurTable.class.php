<?php

/**
 * OrganisateurTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class OrganisateurTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object OrganisateurTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('Organisateur');
    }

    public function getForAutocomplete($name)
  {
    $results = $this->createQuery('organisateur')
      ->select('organisateur.libelle')
      ->where('organisateur.libelle LIKE ?', '%'.$name.'%')
      ->orderBy('organisateur.libelle')
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