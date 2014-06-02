<?php

/**
 * RfidGroupeVisiteur filter form.
 *
 * @package    sf_sandbox
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class RfidGroupeVisiteurFormFilter extends BaseRfidGroupeVisiteurFormFilter
{
  public function configure()
  {
	$this->getWidget('email')->setOption('with_empty', false);
  	$this->getWidget('genre')->setOption('with_empty', false);
  	$this->getWidget('age')->setOption('with_empty', false);
  	$this->getWidget('code_postal')->setOption('with_empty', false);
  	$this->getWidget('commentaire')->setOption('with_empty', false);
  }
}
