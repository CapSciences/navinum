<?php

require_once dirname(__FILE__).'/../lib/log_visiteGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/log_visiteGeneratorHelper.class.php';

/**
 * log_visite actions.
 *
 * @package    sf_sandbox
 * @subpackage log_visite
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class log_visiteActions extends autoLog_visiteActions
{
  public function executeNew(sfWebRequest $request)
  {
    $this->forward404();
  }

  public function executeDelete(sfWebRequest $request)
  {
    $this->forward404();
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404();
  }
}
