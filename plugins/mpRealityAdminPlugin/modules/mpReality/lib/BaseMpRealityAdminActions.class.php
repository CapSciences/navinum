<?php

/**
 * MpRealityAdmin base actions.
 *
 * @package    plugins
 * @subpackage mpRealityAdmin
 * @author     Mike Plavonil <mikeplavo@gmail.com>
 * @version    SVN: $Id: BaseMpRealityAdminActions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class BaseMpRealityAdminActions extends sfActions
{
  public function executeDashboard(sfWebRequest $request)
  {
    $this->categories = mpDashboardReality::getCategories();
  }
}
