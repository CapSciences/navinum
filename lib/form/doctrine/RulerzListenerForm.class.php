<?php

/**
 * RulerzListener form.
 *
 * @package    sf_sandbox
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class RulerzListenerForm extends BaseRulerzListenerForm
{
  public function configure()
  {
      $this->useFields(array(
          'event',
      ));
  }
}
