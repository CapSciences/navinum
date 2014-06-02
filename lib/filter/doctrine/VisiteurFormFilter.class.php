<?php

/**
 * Visiteur filter form.
 *
 * @package    sf_sandbox
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class VisiteurFormFilter extends BaseVisiteurFormFilter
{
  public function configure()
  {
    unset($this["is_tosync"]);
  	$this->getWidget('ville')->setOption('with_empty', false);
  	$this->getWidget('date_naissance')->setOption('with_empty', false);
  	$this->getWidget('nom')->setOption('with_empty', false);
  	$this->getWidget('pseudo_son')->setOption('with_empty', false);
  	$this->getWidget('code_postal')->setOption('with_empty', false);
  	$this->getWidget('genre')->setOption('with_empty', false);
    $this->getWidget('type')->setOption('with_empty', false);
  	$this->getWidget('preference_media_list')->setOption('renderer_class', 'sfWidgetFormSelectDoubleList');
    $this->getWidget('preference_media_list')->setOption('renderer_options', array('label_unassociated' => 'Disponibles', 'label_associated' => 'Séléctionnés'));

    $this->setWidget('date_naissance',new sfWidgetFormFilterDate(array(
            'from_date' => new sfWidgetFormJqueryDateVip(array('format' => '%day%/%month%/%year%', 'date_range' => array('min' => date('Y')-100, 'max' => date('Y')))), 
            'to_date' => new sfWidgetFormJqueryDateVip(array('format' => '%day%/%month%/%year%', 'date_range' => array('min' => date('Y')-100, 'max' => date('Y')))
                  ), 'with_empty' => false)));
    $this->setWidget('created_at',new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormJqueryDateVip(array('format' => '%day%/%month%/%year%')), 'to_date' => new sfWidgetFormJqueryDateVip(array('format' => '%day%/%month%/%year%')), 'with_empty' => false)));
    $this->setWidget('updated_at',new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormJqueryDateVip(array('format' => '%day%/%month%/%year%')), 'to_date' => new sfWidgetFormJqueryDateVip(array('format' => '%day%/%month%/%year%')), 'with_empty' => false)));

    unset($this['adresse'], $this['email'], $this['password_son'], $this['prenom'], $this['url_avatar'], $this['num_mobile'], $this['google_id'], $this['dailymotion_id'], $this['twitter_id'], $this['flickr_id'], $this['facebook_id']);
  }
}
