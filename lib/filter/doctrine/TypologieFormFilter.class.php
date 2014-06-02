<?php

/**
 * Typologie filter form.
 *
 * @package    sf_sandbox
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class TypologieFormFilter extends BaseTypologieFormFilter
{
  public function configure()
  {
	unset($this["is_tosync"]);
	/*unset($this["updated_at"]);
	unset($this["created_at"]);*/
	$this->getWidget('libelle')->setOption('with_empty', false);
  }
}
