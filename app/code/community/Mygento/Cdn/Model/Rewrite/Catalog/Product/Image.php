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

}
