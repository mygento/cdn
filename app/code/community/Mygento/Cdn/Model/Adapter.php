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
            return Mage::getModel('mycdn/adapters_' . $adapter);
        } catch (Exception $ex) {
            Mage::helper('mycdn')->addLog($ex->getMessage());
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
        Mage::helper('mycdn')->addLog('checking cache for file ' . $fileName);
        if (Mage::app()->getCache()->load('cdn_' . $fileName)) {
            Mage::helper('mycdn')->addLog('[cached] ' . $fileName);
        }
        return Mage::app()->getCache()->load('cdn_' . $fileName);
    }

    public function getFile($downloadName)
    {
        Varien_Profiler::start('cdn_download_file_' . $downloadName);
        $adapter = $this->getAdapter();
        if ($adapter) {
            $filename = Mage::helper('mycdn')->getRelativeFile($downloadName);
            $image = $adapter->downloadFile($filename);
            if ($image) {
                $fp = fopen($downloadName, 'w');
                fwrite($fp, $image);
                fclose($fp);
                Mage::helper('mycdn')->addLog('[downloaded] File downloaded to ' . $downloadName);
                Varien_Profiler::stop('cdn_download_file_' . $downloadName);
                Mage::app()->getCache()->save($this->getUrl($filename), 'cdn_' . $filename, array('MYCDN'));
                return true;
            } else {
                Mage::helper('mycdn')->addLog('[not downloaded] File not downloaded to ' . $downloadName);
            }
        }
        Varien_Profiler::stop('cdn_download_file_' . $downloadName);
        return false;
    }

    public function uploadFile($file, $uploadName, $content_type = null)
    {
        Varien_Profiler::start('cdn_upload_file_' . $uploadName);
        Mage::helper('mycdn')->addLog('uploading ' . Mage::helper('mycdn')->getRelativeFile($uploadName) . ' as ' . $content_type);
        $adapter = $this->getAdapter();
        if ($adapter) {
            //Mage::helper('mycdn')->addLog('chosing adapter: ' . get_class($adapter));
            $filename = Mage::helper('mycdn')->getRelativeFile($uploadName);
            $result = $adapter->uploadFile($file, $filename, $content_type);
            if ($result) {
                Mage::helper('mycdn')->addLog('uploaded successfully ' . $filename);
                Mage::helper('mycdn')->addLog('saving to cache ' . $filename);
                Mage::app()->getCache()->save($this->getUrl($filename), 'cdn_' . $filename, array('MYCDN'));
            } else {
                Mage::helper('mycdn')->addLog('not uploaded ' . $filename);
            }
            Varien_Profiler::stop('cdn_upload_file_' . $uploadName);
            return $result;
        }
    }
}
