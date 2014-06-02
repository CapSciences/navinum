<?php

require_once dirname(__FILE__).'/../lib/organisateurGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/organisateurGeneratorHelper.class.php';

/**
 * organisateur actions.
 *
 * @package    sf_sandbox
 * @subpackage organisateur
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class organisateurActions extends autoOrganisateurActions
{
  public function executeAutocomplete(sfWebRequest $request)
  {
    $tags = Doctrine::getTable('Organisateur')->getForAutocomplete($request->getParameter('term'));
    $response = '["'.join($tags,'","').'"]';
    return $this->renderText($response);
  }
}
