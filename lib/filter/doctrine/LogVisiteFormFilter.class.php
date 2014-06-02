<?php

/**
 * LogVisite filter form.
 *
 * @package    sf_sandbox
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class LogVisiteFormFilter extends BaseLogVisiteFormFilter
{
  public function configure()
  {
    unset($this['interactif_id']);
    unset($this['created_at']);
    unset($this['updated_at']);
    unset($this['is_tosync']);

    $this->setWidget('start_at',new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormJqueryDateVip(array('format' => '%day%/%month%/%year%')), 'to_date' => new sfWidgetFormJqueryDateVip(array('format' => '%day%/%month%/%year%')), 'with_empty' => false)));
    $this->setWidget('end_at',  new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormJqueryDateVip(array('format' => '%day%/%month%/%year%')), 'to_date' => new sfWidgetFormJqueryDateVip(array('format' => '%day%/%month%/%year%')), 'with_empty' => false)));

    $this->setValidator('end_at', new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))));
    $this->setValidator('start_at', new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))));
//    $this->getWidget('start_at')->setOption('with_empty', false);
//    $this->getWidget('end_at')->setOption('with_empty', false);
    $this->getWidget('resultats')->setOption('with_empty', false);

    $this->getWidget('interactif_libelle')->setOption('with_empty', false);
    $this->getWidget('score')->setOption('with_empty', false);

    $this->setWidget('visiteur_id', new sfWidgetFormFilterInput(array("with_empty" => false)));
    $this->setWidget('visite_id', new sfWidgetFormFilterInput(array('with_empty' => false)));
    $this->setWidget('visiteur_pseudo_son', new sfWidgetFormFilterInput(array('with_empty' => false)));
    $this->setValidator('visite_id', new sfValidatorPass(array('required' => false)));
    $this->setValidator('visiteur_id', new sfValidatorPass(array('required' => false)));
    $this->setValidator('visiteur_pseudo_son', new sfValidatorPass(array('required' => false)));

    $this->getWidgetSchema()->moveField('end_at', sfWidgetFormSchema::AFTER, 'start_at');
  }

  public function getFields()
  {
    $fields = parent::getFields();
    $fields['visite_id'] = 'Text';
    $fields['visiteur_id'] = 'Text';
    $fields['visiteur_pseudo_son'] = 'Text';
    return $fields;
  }

  public function addVisiteurPseudoSonColumnQuery(Doctrine_Query $query, $field, $value)
  {
   //Pour le xxxx tu peux utiliser $query->getRootAlias() pour récupérer l'alias racine de ta requete
   if( $value['text'] )
   {
     $query->leftjoin($query->getRootAlias().".Visiteur v");
     $query->andWhere('v.pseudo_son like ?', "%".$value['text']."%");
   }
  }

}
