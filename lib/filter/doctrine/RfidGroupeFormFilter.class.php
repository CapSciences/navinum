<?php

/**
 * RfidGroupe filter form.
 *
 * @package    sf_sandbox
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class RfidGroupeFormFilter extends BaseRfidGroupeFormFilter
{
  public function configure()
  {
	$this->getWidget('nom')->setOption('with_empty', false);
  }
}
