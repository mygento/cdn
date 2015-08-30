<?php

class Mygento_Cdn_Model_Image_Gd2 extends Varien_Image_Adapter_Gd2
{

    public function save($destination = null, $newName = null)
    {
        if (!Mage::getStoreConfig('mycdn/general/enabled')) {
            return parent::save($destination, $newName);
        }

        Varien_Profiler::start('cdn_process_image_file');
        parent::save($destination, $newName);
        Varien_Profiler::stop('cdn_process_image_file');

        $fileName = (!isset($destination) ) ? $this->_fileName : $destination;

        if (isset($destination) && isset($newName)) {
            $fileName = $destination . "/" . $newName;
        } elseif (isset($destination) && !isset($newName)) {
            $fileName = $destination;
        } elseif (!isset($destination) && isset($newName)) {
            $fileName = $this->_fileSrcPath . "/" . $newName;
        } else {
            $fileName = $this->_fileSrcPath . $this->_fileSrcName;
        }
        return $this->uploadfile($fileName, $fileName, $destination, $newName);
    }

    private function uploadfile($source, $fileName, $destination, $newName)
    {
        $adapter = Mage::getModel('mycdn/adapter');
        $result = $adapter->uploadFileAsync($source, $fileName, null, 0);
        if (!$result) {
            return parent::save($destination, $newName);
        }
        return $result;
    }
}
