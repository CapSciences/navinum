<?php

/**
 * Xp filter form.
 *
 * @package    sf_sandbox
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class XpFormFilter extends BaseXpFormFilter
{
  public function configure()
  {
	unset($this["is_tosync"]);
	/*unset($this["updated_at"]);
	unset($this["created_at"]);*/
	$this->getWidget('score')->setOption('with_empty', false);
	$this->setWidget('visiteur_id', new sfWidgetFormFilterInput(array("with_empty" => false)));
  }
}
