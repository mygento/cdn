<?php

class Mygento_Cdn_Model_Adapter
{
    private $adapter;

    public function getAdapter()
    {
        $adapter = Mage::getStoreConfig('mycdn/general/adapter');
        if (!$adapter) {
            return false;
        }

        try {
            $this->adapter = Mage::getModel('mycdn/adapters_' . $adapter);
            return $this->adapter;
        } catch (Exception $ex) {
            Mage::helper('mycdn')->addLog($ex->getMessage());
        }
        return false;
    }
}
