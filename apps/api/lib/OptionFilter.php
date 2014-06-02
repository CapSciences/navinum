<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Processes the "OPTIONS headers dor cors" cookie @see http://remysharp.com/2011/04/21/getting-cors-working/.
 * 
 * This filter should be added to the application filters.yml file **above**
 * the security filter:
 * 
 *    option_:
 *      class: OptionFiltrer
 * 
 *    security: ~
 * 
 * @package    symfony
 * @subpackage plugin
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfGuardRememberMeFilter.class.php 32872 2011-08-02 15:15:56Z gimler $
 */
class OptionFilter extends sfFilter
{
  /**
   * Executes the filter chain.
   *
   * @param sfFilterChain $filterChain
   */
  public function execute($filterChain)
  {
    if ($this->isFirstCall())
    {
      // respond to preflights
      if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
      {
        // return only the headers and not the content
        // only allow CORS if we're doing a GET - i.e. no saving for now.
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']) && ($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] == 'GET' || $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] == 'POST'))
        {
          $this->context->getResponse()->setHeaderOnly(true);
          $this->context->getResponse()->send();
        }
      }
    }

    $filterChain->execute();
  }
}
