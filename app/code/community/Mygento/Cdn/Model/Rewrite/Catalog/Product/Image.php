<?php

class Mygento_Cdn_Model_Rewrite_Catalog_Product_Image extends Mage_Catalog_Model_Product_Image
{

    /**
     * Sets the images processor to the CDN version of varien_image and calls the parent
     * method to return it.
     *
     * @return Mygento_Cdn_Model_Image
     */
    public function getImageProcessor()
    {
        if (!Mage::getStoreConfig('mycdn/general/enabled')) {
            return parent::getImageProcessor();
        }
        if (!$this->_processor) {
            $this->_processor = Mage::getModel('mycdn/image', $this->getBaseFile());
        }
        return parent::getImageProcessor();
    }

    /**
     * Checks to see if the image has been verified lately by checking in the cache or fails
     * back to the parent method as appropriate.
     *
     * @return bool
     */
    public function isCached()
    {
        if (!Mage::getStoreConfig('mycdn/general/enabled')) {
            return parent::isCached();
        }
        $adapter = Mage::getModel('mycdn/adapter');
        return $adapter->fileExists($this->_newFile);
    }

    /**
     * Provides the URL to the image on the CDN or fails back to the parent method as appropriate.
     *
     * @return string
     */
    public function getUrl()
    {
        if (!Mage::getStoreConfig('mycdn/general/enabled')) {
            return parent::getUrl();
        }

        return Mage::getModel('mycdn/adapter')->getUrl($this->_newFile);
    }

    /**
     * First check this file on FS
     * If it doesn't exist - try to download it from CDN
     *
     * @param string $filename
     * @return bool
     */
    protected function _fileExists($filename)
    {
        if (!Mage::getStoreConfig('mycdn/general/enabled')) {
            return parent::_fileExists($filename);
        }
        if (is_file($filename)) {
            return true;
        } else {
            Mage::helper('mycdn')->addLog('[download] no source on server -> ' . $filename);
            return Mage::helper('mycdn')->getCdnFile($filename);
        }
    }
}
