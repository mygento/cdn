<?php

class Mygento_Cdn_Model_Adapters_Selectel
{

    private $user;
    private $pass;
    private $bucketName;

    public function __construct()
    {
        $this->user = Mage::getStoreConfig('mycdn/selectel/user');
        $this->pass = Mage::getStoreConfig('mycdn/selectel/passwd');
        $this->bucketName = Mage::getStoreConfig('mycdn/selectel/bucket');
    }

    public function getUrl($filename)
    {
        $type = Mage::app()->getStore()->isCurrentlySecure() ? 'url_base_secure' : 'url_base';
        $base_url = Mage::getStoreConfig('mycdn/selectel/' . $type);
        $filename = $base_url . '/' . $filename;
        return $filename;
    }

    public function downloadFile($downloadName)
    {
        if ($this->user == null || $this->pass == null) {
            return false;
        }
        $storage = new Mygento_SelectelStorage($this->user, $this->pass);
        $container = $storage->getContainer($this->bucketName);
        $result = $container->getFile($downloadName);
        return $result['content'];
    }

    public function uploadFile($file, $uploadName, $content_type = 'application/octet-stream')
    {
        if ($this->user == null || $this->pass == null) {
            return false;
        }
        $storage = new Mygento_SelectelStorage($this->user, $this->pass);
        $container = $storage->getContainer($this->bucketName);
        return $container->putFile($file, $uploadName);
    }
}
