<?php

class Mygento_Cdn_Model_Image_Gd2 extends Varien_Image_Adapter_Gd2
{

    public function save($destination = null, $newName = null)
    {
        if (!Mage::getStoreConfig('mycdn/general/enabled')) {
            return parent::save($destination, $newName);
        }
        $orig_destination = $destination;
        $orig_newName = $newName;

        $temp = tempnam(Mage::getConfig()->getOptions()->getTmpDir(), 'CDN_');
        parent::save($temp);

        $fileName = (!isset($destination) ) ? $this->_fileName : $destination;

        if (isset($destination) && isset($newName)) {
            $fileName = $destination . "/" . $newName;
        } elseif (isset($destination) && !isset($newName)) {
            $info = pathinfo($destination);
            $fileName = $destination;
            $destination = $info['dirname'];
        } elseif (!isset($destination) && isset($newName)) {
            $fileName = $this->_fileSrcPath . "/" . $newName;
        } else {
            $fileName = $this->_fileSrcPath . $this->_fileSrcName;
        }
        $this->uploadfile($orig_destination, $orig_newName, $temp, $fileName);
    }

    private function uploadfile($destination, $newName, $temp, $fileName)
    {
        $adapter = Mage::getModel('mycdn/adapter');
        if (!$adapter) {
            return parent::save($destination, $newName);
        }
        $result = $adapter->uploadFileAsync($temp, $fileName, null, 1);
        $async = Mage::getStoreConfig('mycdn/general/async');
        if (!$async) {
            $ioObject = new Varien_Io_File();
            $ioObject->rm($temp);
        }
        if (!$result) {
            return parent::save($destination, $newName);
        }
    }
}
