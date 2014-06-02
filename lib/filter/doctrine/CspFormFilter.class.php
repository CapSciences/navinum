<?php

/**
 * Csp filter form.
 *
 * @package    sf_sandbox
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class CspFormFilter extends BaseCspFormFilter
{
  public function configure()
  {
  	unset($this['created_at']);
  	unset($this['updated_at']);
  	unset($this['is_tosync']);
	$this->getWidget('libelle')->setOption('with_empty', false);

  }
}
