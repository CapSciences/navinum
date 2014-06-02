<?php

require_once dirname(__FILE__).'/../lib/contexteGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/contexteGeneratorHelper.class.php';

/**
 * contexte actions.
 *
 * @package    sf_sandbox
 * @subpackage contexte
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class contexteActions extends autoContexteActions
{
  public function executeAutocomplete(sfWebRequest $request)
  {
    $tags = Doctrine::getTable('Contexte')->getForAutocomplete($request->getParameter('term'));
    $response = '["'.join($tags,'","').'"]';
    return $this->renderText($response);
  }
}
