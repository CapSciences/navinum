<?php

require_once dirname(__FILE__).'/../lib/expositionGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/expositionGeneratorHelper.class.php';

/**
 * exposition actions.
 *
 * @package    sf_sandbox
 * @subpackage exposition
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class expositionActions extends autoExpositionActions
{


/*
Exposition:
  tableName:                   exposition
  actAs:
    Timestampable:
    caRulerzNotifier:  
  columns:
    guid:                      { type: string(255), notnull: true, primary: true }
    libelle:                   { type:string(255) , unique: true }
    contexte_id:               { type: string(255) }
    organisateur_editeur_id:   { type: string(255) }
    organisateur_diffuseur_id: { type: string(255) }
    synopsis:                  { type: clob }
    description:               { type: clob }
    logo:                      { type: string(128) }
    publics:                   { type: string(255) }
    langues:                   { type: string(255) }
    url_illustration:          { type: string(255) }
    url_studio:                { type: string(255) }
    start_at:                  { type: date, notnull: false }
    end_at:                    { type: date, notnull: false }
    is_tosync:                 { type: boolean, default: 1 }
  relations:
    Contexte:                  { class: Contexte, local: contexte_id, foreign: guid, onDelete: SET NULL }
    OrganisateurEditeur:       { class: Organisateur, local: organisateur_editeur_id, foreign: guid, onDelete: SET NULL }
    OrganisateurDiffuseur:     { class: Organisateur, local: organisateur_diffuseur_id, foreign: guid, onDelete: SET NULL }
    sfGuardUser:                {refClass: UserExposition, class: sfGuardUser, local: exposition_id, foreign: user_id, type: many }

 */
  public function executeCopy(sfWebRequest $request)
  {
    $exposition = $this->getRoute()->getObject();
    $parcours = $exposition->Parcours->getPrimaryKeys();
    $exposition_copy = $exposition->copy();
    $exposition_copy->setGuid(Guid::generate());
    $exposition_copy->setLibelle($exposition->getLibelle().'-COPY');
    $exposition_copy->setCreatedAt(date('Y-m-d H:i:s'));
    $exposition_copy->setUpdatedAt(date('Y-m-d H:i:s'));
    $exposition_copy->setOrganisateurDiffuseurId(null);
    $exposition_copy->setContexteId(null);
    $exposition_copy->link('Parcours', array_values($parcours));
    $exposition_copy->save();
    return $this->redirect($this->getController()->genUrl('exposition/edit?guid='.$exposition_copy->getGuid()));
  }

  protected function buildQuery()
  {
    $query = parent::buildQuery();

    if ($this->getUser()->hasPermission('admin'))
    {
      return $query;
    }
    else
    {
      $expositions = $this->getUser()->getGuardUser()->getExposition()->toArray();
      $guids = array();
      foreach ($expositions as $expo)
      {
        $guids[] = $expo['guid'];
      }
      $query->whereIn('guid', $guids);
      return $query;
    }
  }

  public function executeAutocomplete(sfWebRequest $request)
  {
    $tags = Doctrine::getTable('Exposition')->getForAutocomplete($request->getParameter('term'));
    $response = '["'.join($tags,'","').'"]';
    return $this->renderText($response);
  }
}
