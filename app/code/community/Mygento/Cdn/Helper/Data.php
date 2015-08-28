<?php

class Mygento_Cdn_Helper_Data extends Mage_Core_Helper_Abstract
{

    const CACHETAG = 'MYCDN';

    /**
     * Check is file in cache
     *
     * @param string $fileName
     * @return boolean
     */
    public function checkPathInCache($file)
    {
        if (!Mage::app()->useCache(self::CACHETAG)) {
            return false;
        }
        $fileName = $this->getRelativeFile($file);
        if (Mage::app()->getCache()->load('cdn_' . $fileName)) {
            Mage::helper('mycdn')->addLog('[CACHED] ' . $fileName);
            return true;
        }
        Mage::helper('mycdn')->addLog('[NOT_CACHED] ' . $fileName);
        return false;
    }

    /**
     * Save file in cache
     *
     * @param string $fileName
     * @param string $url
     * @return boolean
     */
    public function savePathInCache($fileName, $url)
    {
        if (!Mage::app()->useCache(self::CACHETAG)) {
            return false;
        }
        Mage::app()->getCache()->save($url, 'cdn_' . $filename, array(self::CACHETAG));
    }

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
     * Attempt to detect the MIME type of a file using available extensions
     *
     * This method will try to detect the MIME type of a file. If the fileinfo
     * extension is available, it will be used. If not, the mime_magic
     * extension which is deprected but is still available in many PHP setups
     * will be tried.
     *
     * @param  string $file File path
     * @return string       MIME type
     */
    public function detectFileMimeType($file)
    {
        $type = null;
        // First try with fileinfo functions
        if (function_exists('finfo_open')) {
            $finfo = @finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $type = finfo_file($finfo, $file);
            }
        } elseif (function_exists('mime_content_type')) {
            $type = mime_content_type($file);
        }
        return $type;
    }

    /**
     * Gzip the data
     *
     * @param  string $path Path to read-write the data to.
     * @param  string $type Content-Type
     */
    public function gzipFile($path, $type)
    {
        Varien_Profiler::start('cdn_gzip_file_' . $path);
        $minifier = $this->getMinifier($path, $type);
        $minifier->gzip($path . '.gz');
        Varien_Profiler::stop('cdn_gzip_file_' . $path);
    }

    /**
     * Minify the data
     *
     * @param  string $path Path to read-write the data to.
     * @param  string $type Content-Type
     */
    public function minifyFile($path, $type)
    {
        Varien_Profiler::start('cdn_minify_file_' . $path);
        $minifier = $this->getMinifier($path, $type);
        $minifier->minify($path . '.min');
        Varien_Profiler::stop('cdn_minify_file_' . $path);
    }

    /**
     * Get Minifier
     *
     * @param type $path
     * @param type $type
     * @return \MatthiasMullie\Minify\CSS|\MatthiasMullie\Minify\JS
     */
    private function getMinifier($path, $type)
    {
        if ('text/css' === $type) {
            return new MatthiasMullie\Minify\CSS($path);
        } else {
            return new MatthiasMullie\Minify\JS($path);
        }
    }
}
