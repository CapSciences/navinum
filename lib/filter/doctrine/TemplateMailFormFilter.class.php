<?php

/**
 * TemplateMail filter form.
 *
 * @package    sf_sandbox
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class TemplateMailFormFilter extends BaseTemplateMailFormFilter
{
  public function configure()
  {
	unset($this["is_tosync"]);
	/*unset($this["updated_at"]);
	unset($this["created_at"]);*/
	$this->getWidget('subject')->setOption('with_empty', false);
	$this->getWidget('content')->setOption('with_empty', false);
  }
}
