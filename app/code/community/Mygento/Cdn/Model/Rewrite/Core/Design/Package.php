<?php

class Mygento_Cdn_Model_Rewrite_Core_Design_Package extends Mage_Core_Model_Design_Package
{

    private function processFiles($srcFiles, $targetFile, $mustMerge, $beforeMergeCallback, $extensionsFilter, $content_type = null)
    {
        $temp = tempnam(Mage::getConfig()->getOptions()->getTmpDir(), 'CDN_');

        Mage::helper('mycdn')->addLog('[MERGE] to ' . $targetFile);
        Mage::helper('core')->mergeFiles($srcFiles, $temp, true, $beforeMergeCallback, $extensionsFilter);

        return $this->uploadfile($srcFiles, $targetFile, $mustMerge, $beforeMergeCallback, $extensionsFilter, $temp, $content_type);
    }

    private function uploadfile($srcFiles, $targetFile, $mustMerge, $beforeMergeCallback, $extensionsFilter, $temp, $content_type = null)
    {
        $adapter = Mage::getModel('mycdn/adapter');
        if (!$adapter) {
            return parent::_mergeFiles($srcFiles, $targetFile, $mustMerge, $beforeMergeCallback, $extensionsFilter);
        }
        $result = $adapter->uploadFileAsync($temp, $targetFile, $content_type);
        $async = Mage::getStoreConfig('mycdn/general/async');
        if (!$async) {
            $ioObject = new Varien_Io_File();
            $ioObject->rm($temp);
        }

        return $result;
    }

    private function needMerge($file)
    {
        return !(Mage::helper('mycdn')->checkPathInCache($file));
    }

    /**
     * Merge specified javascript files and return URL to the merged file on success
     *
     * @param $files
     * @return string
     */
    public function getMergedJsUrl($files)
    {
        if (!Mage::getStoreConfig('mycdn/general/enabled')) {
            return parent::getMergedJsUrl($files);
        }
        $path = 'js' . DS . 'merge';

        $targetFilename = md5(implode(',', $files)) . '.js';

        Mage::helper('mycdn')->addLog('Need to merge ' . $targetFilename . ' =>  ' . ($this->needMerge('js' . DS . $targetFilename) ? 'true' : 'false'));

        if ($this->needMerge($path . DS . $targetFilename)) {
            $result = $this->processFiles($files, $path . DS . $targetFilename, false, null, 'js', 'application/javascript');
            if (!$result) {
                return parent::getMergedJsUrl($files);
            }
        }
        return Mage::getModel('mycdn/adapter')->getUrl('js/merge/' . $targetFilename);
    }

    /**
     * Merge specified css files and return URL to the merged file on success
     *
     * @param $files
     * @return string
     */
    public function getMergedCssUrl($files)
    {
        if (!Mage::getStoreConfig('mycdn/general/enabled')) {
            return parent::getMergedCssUrl($files);
        }

        // secure or unsecure
        $isSecure = Mage::app()->getRequest()->isSecure();
        $mergerDir = $isSecure ? 'merge/css_secure' : 'merge/css';

        $targetFilename = md5(implode(',', $files)) . '.css';
        Mage::helper('mycdn')->addLog($targetFilename . ' need to merge =>  ' . ($this->needMerge($mergerDir . DS . $targetFilename) ? 'true' : 'false'));

        if ($this->needMerge($mergerDir . DS . $targetFilename)) {
            $result = $this->processFiles($files, $mergerDir . DS . $targetFilename, false, array($this, 'beforeMergeCss'), 'css', 'text/css');
            if (!$result) {
                return parent::getMergedCssUrl($files);
            }
        }
        return Mage::getModel('mycdn/adapter')->getUrl($mergerDir . '/' . $targetFilename);
    }
}
