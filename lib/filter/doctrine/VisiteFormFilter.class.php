<?php

/**
 * Visite filter form.
 *
 * @package    sf_sandbox
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class VisiteFormFilter extends BaseVisiteFormFilter
{
  public function configure()
  {
  	unset($this['navinum_id']);
  	unset($this['exposition_id']);
  	unset($this['parcours_id']);
  	unset($this['is_tosync']);
  	unset($this['created_at']);
  	unset($this['updated_at']);
    $this->setWidget('visiteur_id', new sfWidgetFormFilterInput(array("with_empty" => false)));
	  $this->setWidget('groupe_id', new sfWidgetFormFilterInput(array('with_empty' => false)));
	  $this->setValidator('groupe_id', new sfValidatorPass(array('required' => false)));
	  $this->setValidator('visiteur_id', new sfValidatorPass(array('required' => false)));

	  $this->getWidget('parcours_libelle')->setOption('with_empty', false);
  	$this->getWidget('exposition_libelle')->setOption('with_empty', false);
  }

  public function getFields()
  {
  	$fields = parent::getFields();
  	$fields['groupe_id'] = 'Text';
  	$fields['visiteur_id'] = 'Text';
  	return $fields;
  }

  public function addGroupeIdColumnQuery(Doctrine_Query $query, $field, $value)
  {
   //Pour le xxxx tu peux utiliser $query->getRootAlias() pour récupérer l'alias racine de ta requete
   if( $value['text'] )
   {
   	 $query->leftjoin($query->getRootAlias().".RfidGroupeVisiteur g");
     $query->andWhere('g.nom like ?', "%".$value['text']."%");
   }
  }

  public function addVisiteurIdColumnQuery(Doctrine_Query $query, $field, $value)
  {
   //Pour le xxxx tu peux utiliser $query->getRootAlias() pour récupérer l'alias racine de ta requete
   if( $value['text'] )
   {
   	 $query->leftjoin($query->getRootAlias().".Visiteur v");
     $query->andWhere('v.pseudo_son like ?', "%".$value['text']."%");
   }
  }
}
