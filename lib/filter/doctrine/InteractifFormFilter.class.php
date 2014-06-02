<?php

/**
 * Interactif filter form.
 *
 * @package    sf_sandbox
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class InteractifFormFilter extends BaseInteractifFormFilter
{
  public function configure()
  {
    unset($this["is_tosync"]);
  	$this->getWidget('url_end_at')->setOption('with_empty', false);
    $this->getWidget('explications_resultats')->setOption('with_empty', false);
    $this->getWidget('ordre')->setOption('with_empty', false);
    $this->getWidget('categorie')->setOption('with_empty', false);
    $this->getWidget('editeur')->setOption('with_empty', false);
    $this->getWidget('publics')->setOption('with_empty', false);
    $this->getWidget('markets')->setOption('with_empty', false);
    $this->getWidget('langues')->setOption('with_empty', false);
    $this->getWidget('date_diff')->setOption('with_empty', false);
    unset($this['url_fichier_interactif'], $this['url_pierre_de_rosette'], $this['url_illustration'], $this['url_interactif_type'], $this['url_interactif_choice'], $this['url_visiteur_type'], $this['url_start_at'], $this['url_start_at_type'], $this['url_end_at'], $this['url_end_at_type']);
    $this->getWidget('libelle')->setOption('with_empty', false);
    $this->setWidget('created_at',new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormJqueryDateVip(array('format' => '%day%/%month%/%year%')), 'to_date' => new sfWidgetFormJqueryDateVip(array('format' => '%day%/%month%/%year%')), 'with_empty' => false)));
    $this->setWidget('updated_at',new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormJqueryDateVip(array('format' => '%day%/%month%/%year%')), 'to_date' => new sfWidgetFormJqueryDateVip(array('format' => '%day%/%month%/%year%')), 'with_empty' => false)));
    $this->getWidget('parcours_list')->setOption('renderer_class', 'sfWidgetFormSelectDoubleList');
    $this->getWidget('parcours_list')->setOption('renderer_options', array('label_unassociated' => 'Disponibles', 'label_associated' => 'Séléctionnés'));
  }
}
