<?php

class Mygento_Cdn_Model_Adapter
{

    private function getAdapter()
    {
        $adapter = Mage::getStoreConfig('mycdn/general/adapter');
        if (!$adapter) {
            return false;
        }

        try {
            return Mage::getModel('mycdn/adapters_'.$adapter);
        } catch (Exception $ex) {
            Mage::helper('mycdn')->AddLog($ex->getMessage());
        }
        return false;
    }

    public function getUrl($file)
    {
        $adapter = $this->getAdapter();
        if ($adapter) {
            return $adapter->getUrl($file);
        }
    }

    public function fileExists($file)
    {
        $fileName = Mage::helper('mycdn')->getRelativeFile($file);
        Mage::helper('mycdn')->addLog('checking cache for file '.$fileName);
        if (Mage::app()->getCache()->load('cdn_'.$fileName)) {
            Mage::helper('mycdn')->addLog('[cached] '.$fileName);
        }
        return Mage::app()->getCache()->load('cdn_'.$fileName);
    }

    public function upload_file($file, $uploadName)
    {
        Mage::helper('mycdn')->addLog('uploading '.Mage::helper('mycdn')->getRelativeFile($uploadName));
        $adapter = $this->getAdapter();
        if ($adapter) {
            Mage::helper('mycdn')->addLog('chosing adapter: '.get_class($adapter));
            $filename = Mage::helper('mycdn')->getRelativeFile($uploadName);
            $result = $adapter->upload_file($file, $filename);

            if ($result) {
                Mage::helper('mycdn')->addLog('uploaded successfully '.$filename);
                Mage::helper('mycdn')->addLog('saving to cache '.$filename);
                Mage::app()->getCache()->save($this->getUrl($filename), 'cdn_'.$filename, array('MYCDN'));
            } else {
                Mage::helper('mycdn')->addLog('not uploaded '.$filename);
            }
        }
    }

}
