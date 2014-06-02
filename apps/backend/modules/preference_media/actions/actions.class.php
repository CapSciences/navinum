<?php

require_once dirname(__FILE__).'/../lib/preference_mediaGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/preference_mediaGeneratorHelper.class.php';

/**
 * preference_media actions.
 *
 * @package    sf_sandbox
 * @subpackage preference_media
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class preference_mediaActions extends autoPreference_mediaActions
{
  public function executeAutocomplete(sfWebRequest $request)
  {
    $tags = Doctrine::getTable('PreferenceMedia')->getForAutocomplete($request->getParameter('term'));
    $response = '["'.join($tags,'","').'"]';
    return $this->renderText($response);
  }
}
