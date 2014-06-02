<?php

require_once dirname(__FILE__).'/../lib/rfid_groupeGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/rfid_groupeGeneratorHelper.class.php';

/**
 * rfid_groupe actions.
 *
 * @package    sf_sandbox
 * @subpackage rfid_groupe
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class rfid_groupeActions extends autoRfid_groupeActions
{
  public function executeDissociate(sfWebRequest $request)
  {
    $this->rfid_groupe = $this->getRoute()->getObject();
	$rfids = Doctrine_Core::getTable('Rfid')->findByGroupeId($this->rfid_groupe->getGuid());
	$collection = new Doctrine_Collection('Rfid');

	foreach($rfids as $rfid)
	{
		$rfid->setGroupeId(null);
		$collection->add($rfid);
	}

	$collection->save();
    $this->redirect('@rfid_groupe');
  }
}
