<?php

class Mygento_Cdn_Model_Resource_Job extends Mage_Core_Model_Resource_Db_Abstract
{

    public function _construct()
    {
        $this->_init('mycdn/job', 'id');
    }
}
