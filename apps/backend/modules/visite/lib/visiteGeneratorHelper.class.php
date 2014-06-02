<?php

/**
 * visite module helper.
 *
 * @package    sf_sandbox
 * @subpackage visite
 * @author     Your name here
 * @version    SVN: $Id: helper.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class visiteGeneratorHelper extends BaseVisiteGeneratorHelper
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
          case 'exposition_contexte':
            //$row[] = $record->getExpositionContexte();
            break;
          case 'visiteur_contexte':
            //$row[] = $record->getVisiteurContexte();
            break;
          case 'visiteur_langue':
            $row[] = $record->getVisiteur()->getLangue()->getLibelle();
            break;
          case 'visiteur_email':
            $row[] = $record->getVisiteurEmail();
            break;
          case 'visiteur_nom':
            $row[] = $record->getVisiteurNom();
            break;
          case 'visiteur_prenom':
            $row[] = $record->getVisiteurPrenom();
            break;
          case 'visiteur_pseudo_son':
            $row[] = $record->getVisiteurPseudoSon();
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
          case 'visiteur_preference_media':
            $row[] = $record->getVisiteurPreferenceMedia();
            break;
          case 'visiteur_num_mobile':
            $row[] = $record->getVisiteurNumMobile();
            break;
          case 'visiteur_google_id':
            $row[] = $record->getVisiteurGoogleId();
            break;
          case 'visiteur_twitter_id':
            $row[] = $record->getVisiteurGoogleId();
            break;
          case 'visiteur_flickr_id':
            $row[] = $record->getVisiteurFlickrId();
            break;
          case 'visiteur_dailymotion_id':
            $row[] = $record->getVisiteurDailymotionId();
            break;
          default:
            $row[] = $record[$field];
            break;
        }
    }
    return implode("\t", $row);
  }
}
