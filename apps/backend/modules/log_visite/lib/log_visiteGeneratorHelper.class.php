<?php

/**
 * log_visite module helper.
 *
 * @package    sf_sandbox
 * @subpackage log_visite
 * @author     Your name here
 * @version    SVN: $Id: helper.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class log_visiteGeneratorHelper extends BaseLog_visiteGeneratorHelper
{
  public function getExportRecordRow($record, $fields)
  {
    $row = array();
    foreach ($fields as $field)
    {
        switch ($field)
        {
          case 'exposition':
            $row[] = $record->getExposition();
            break;
          case 'parcours':
            $row[] = $record->getParcours();
            break;
          case 'interactif':
            $row[] = $record->getInteractif();
            break;
          case 'exposition_contexte':
            $row[] = $record->getExpositionContexte();
            break;
          case 'exposition_organisateur_diffuseur':
            $row[] = $record->getExpositionOrganisateurDiffuseur();
            break;
          case 'exposition_organisateur_editeur':
            $row[] = $record->getExpositionOrganisateurEditeur();
            break;
          case 'visiteur_contexte':
            $row[] = $record->getVisiteurContexte();
            break;
          case 'visiteur_langue':
            $row[] = $record->getVisiteur()->getLangue()->getLibelle();
            break;
          case 'visiteur_genre':
            $row[] = $record->getVisiteurGenre();
            break;
          case 'visiteur_date_naissance':
            $row[] = $record->getVisiteurDateNaissance();
            break;
          case 'visiteur_code_postal':
            $row[] = $record->getVisiteurCodePostal();
            break;
          case 'visiteur_created_at':
            $row[] = $record->getVisiteurCreatedAt();
            break;
          case 'visiteur_preference_media':
            $row[] = $record->getVisiteurPreferenceMedia();
            break;
          case 'visiteur_pseudo_son':
            $row[] = $record->getVisiteurPseudoSon();
            break;
          default:
            $row[] = $record[$field];
            break;
        }
    }
    return implode("\t", $row);
  }
}
