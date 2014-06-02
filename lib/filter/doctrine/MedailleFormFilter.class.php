<?php

/**
 * Medaille filter form.
 *
 * @package    sf_sandbox
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class MedailleFormFilter extends BaseMedailleFormFilter
{
  public function configure()
  {
	unset($this["is_tosync"]);
	/*unset($this["updated_at"]);
	unset($this["created_at"]);*/
	$this->getWidget('libelle')->setOption('with_empty', false);
	$this->getWidget('image')->setOption('with_empty', false);
	$this->getWidget('description')->setOption('with_empty', false);
	$this->getWidget('condition_obtention')->setOption('with_empty', false);
  }
}
