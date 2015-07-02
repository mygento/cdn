<?php

class Mygento_Cdn_Model_Image extends Varien_Image
{
    /**
     *
     * @SuppressWarnings("unused")
     */
    protected function _getAdapter($adapter = null)
    {
        if (!isset($this->_adapter)) {
            $this->_adapter = Mage::getModel('mycdn/image_gd2');
        }
        return $this->_adapter;
    }
}
