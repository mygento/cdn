<?php

class Mygento_Cdn_Model_Image_Gd2 extends Varien_Image_Adapter_Gd2
{

    public function save($destination = null, $newName = null)
    {
        if (!Mage::getStoreConfig('mycdn/general/enabled')) {
            return parent::save($destination, $newName);
        }

        $temp = tempnam(sys_get_temp_dir(), 'cdn');
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
        $this->uploadfile($destination, $newName, $temp, $fileName);
    }

    private function uploadfile($destination, $newName, $temp, $fileName)
    {
        $adapter = Mage::getModel('mycdn/adapter');
        if (!$adapter) {
            return parent::save($destination, $newName);
        }
        $result = $adapter->uploadFileAsync($temp, $fileName, null, 1);
        $async = Mage::getStoreConfig('mycdn/general/async');
        if ($result && !$async) {
            $ioObject = new Varien_Io_File();
            $ioObject->rm($temp);
        }
    }
}
