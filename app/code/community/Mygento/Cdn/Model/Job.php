<?php

class Mygento_Cdn_Model_Job extends Mage_Core_Model_Abstract
{

    public function _construct()
    {
        parent::_construct();
        $this->_init('mycdn/job');
    }

    /**
     * Load entity by attribute
     *
     * @param type $name
     * @return boolean
     *
     */
    public function loadByUploadName($name)
    {
        $collection = $this->getResourceCollection()
            ->addFieldToFilter('uploadname', $name)
            ->setPageSize(1);
        foreach ($collection as $object) {
            return $object;
        }
        return $this;
    }

    /**
     * Upload file to CDN async
     */
    public function uploadFile()
    {
        $adapter = Mage::getModel('mycdn/adapter');
        if (!$adapter) {
            return;
        }
        if (!is_file($this->getData('filename'))) {
            Mage::helper('mycdn')->addLog('[CRON] No file ' . $this->getData('filename'));
            $this->delete();
            return;
        }

        Mage::helper('mycdn')->addLog('[CRON] processing id = ' . $this->getId());
        Mage::helper('mycdn')->addLog($this->getData());
        $result = $adapter->uploadFile($this->getData('filename'), $this->getData('uploadname'), $this->getData('content_type'));

        if ($result && $this->getData('delete')) {
            $ioObject = new Varien_Io_File();
            $ioObject->rm($this->getData('filename'));
        }

        if ($result) {
            Mage::helper('mycdn')->addLog('[CRON] delete ' . $this->getData('filename'));
            $this->delete();
        }
    }
}
