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
     * Gzip the data
     *
     * @param  string $path Path to read-write the data to.
     */
    public function gzipFile($path)
    {
        $minifier = new MatthiasMullie\Minify\CSS($path);
        $minifier->gzip($path . '.gz');
    }
}
