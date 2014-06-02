<?php

/**
 * Rulerz form.
 *
 * @package    sf_sandbox
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class RulerzForm extends BaseRulerzForm
{
  public function configure()
  {
      $this->useFields(array(
          'guid',
          'libelle',
          'action'
      ));

      $this->widgetSchema['action'] = new sfWidgetFormTextarea();


  }
}
