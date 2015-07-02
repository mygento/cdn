<?php

class Mygento_Cdn_Helper_Data extends Mage_Core_Helper_Abstract
{

    public function addLog($text)
    {
        if (Mage::getStoreConfig('mycdn/general/debug')) {
            Mage::log($text, null, 'mycdn.log');
        }
    }

    public function getRelativeFile($file)
    {
        return ltrim(str_replace(Mage::getBaseDir('media'), '', $file), '/');
    }

    public function getCdnFile($file)
    {
        $adapter = Mage::getModel('mycdn/adapter');
        if (!$adapter) {
            return false;
        }
        return $adapter->getFile($file);
    }

    /**
     * Minify & gzip the data & (optionally) saves it to a file.
     *
     * @param  string[optional] $path Path to write the data to.
     * @param  int[optional]    $level Compression level, from 0 to 9.
     * @return string           The minified & gzipped data.
     */
    public function gzipFile($path)
    {
        $gz = new Mage_Archive_Gz();
        $gz->pack($path, $path.'_min2');
    }
}
