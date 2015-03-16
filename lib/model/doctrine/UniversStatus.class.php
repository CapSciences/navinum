<?php

/**
 * UniversStatus
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    sf_sandbox
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class UniversStatus extends BaseUniversStatus
{
    public function  __toString()
    {
        return $this->getLibelle() ? $this->getLibelle() : '';
    }

    public function save(Doctrine_Connection $conn = null)
    {
        $this->setIsTosync(1);
        parent::save($conn);
    }

    public function __call($method, $arguments)
    {
        if($method == 'setGuid')
        {
            return $this->set('guid', Guid::generate());
        }
        else
        {
            return parent::__call($method, $arguments);
        }
    }

    public function getUniversStatusDataPath()
    {
        return sfConfig::get('sf_web_dir')."/univers_status/";
    }

    public function  delete(Doctrine_Connection $conn = null)
    {
        $guid = $this->getGuid();

        parent::delete($conn);

        $delete_log = new DeleteLog();
        $delete_log->setGuid($guid);
        $delete_log->setModelName(get_class($this));
        $delete_log->save();
    }

}
