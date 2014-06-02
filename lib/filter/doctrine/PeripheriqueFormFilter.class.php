<?php

/**
 * Peripherique filter form.
 *
 * @package    sf_sandbox
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class PeripheriqueFormFilter extends BasePeripheriqueFormFilter
{
  public function configure()
  {
  	unset($this["is_tosync"]);
	$this->getWidget('adresse_mac')->setOption('with_empty', false);
	$this->getWidget('adresse_ip')->setOption('with_empty', false);
  }
}
