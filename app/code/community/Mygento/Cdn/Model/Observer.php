<?php

class Mygento_Cdn_Model_Observer
{

    public function processJob()
    {
        if (!Mage::getStoreConfig('mycdn/general/enabled')) {
            return;
        }

        if (!Mage::getStoreConfig('mycdn/general/async')) {
            return;
        }
        
        Mage::helper('mycdn')->addLog('[CRON] starting');


        $collection = Mage::getModel('mycdn/job')->getCollection();
        foreach ($collection as $job) {
            $job->uploadFile();
        }
        
        Mage::helper('mycdn')->addLog('[CRON] stop');
    }

    public function uploadOnSave($observer)
    {
        if (!Mage::getStoreConfig('mycdn/general/enabled')) {
            return;
        }
        $adapter = Mage::getModel('mycdn/adapter');
        if (!$adapter) {
            return;
        }
        $product = $observer->getEvent()->getProduct();
        if ($product->getIsDuplicate()) {
            return;
        }
        $images = $observer->getEvent()->getImages();
        $config = Mage::getSingleton('catalog/product_media_config');

        foreach ($images['images'] as $image) {
            if (array_key_exists('value_id', $image)) {
                continue;
            }
            Mage::helper('mycdn')->addLog($image);
            $fileName = $image['file'];
            $file = $config->getMediaPath($fileName);
            Mage::helper('mycdn')->addLog($file);
            $adapter->uploadFile($file, $config->getBaseMediaPathAddition() . $fileName);
        }
    }
}
