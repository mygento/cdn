<?php

class Mygento_Cdn_Model_Observer
{

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
            $adapter->upload_file($file, $config->getBaseMediaPathAddition() . $fileName);
        }
    }
}
