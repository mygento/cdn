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
     */
    public function gzipFile($path)
    {
        $minifier = new MatthiasMullie\Minify\CSS($path);
        $minifier->gzip($path . '.gz');
    }

    /**
     * Minify the data
     *
     * @param  string $path Path to read-write the data to.
     */
    public function minifyFile($path)
    {
        $minifier = new MatthiasMullie\Minify\CSS($path);
        $minifier->minify($path . '.min');
    }
}
