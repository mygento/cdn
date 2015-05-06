<?php

class Mygento_Cdn_Model_Resource_Job_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('mycdn/job');
    }
}
