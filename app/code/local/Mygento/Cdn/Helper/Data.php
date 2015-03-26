<?php

class Mygento_Cdn_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function AddLog($text)
    {
        if (Mage::getStoreConfig('cdn/general/debug')) {
            Mage::log($text, null, 'cdn.log');
        }
    }

}

?>