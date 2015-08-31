<?php

class Mygento_Cdn_Model_Rewrite_Core_Design_Package extends Mage_Core_Model_Design_Package
{

    /**
     * Merge files in one
     *
     * @param array $srcFiles
     * @param string $uploadFile
     * @param string $targetFile
     * @param boolean $mustMerge
     * @param type $beforeMergeCallback
     * @param string $extensionsFilter
     * @param string $content_type
     * @return boolean
     */
    private function processFiles($srcFiles, $uploadFile, $targetFile, $mustMerge, $beforeMergeCallback, $extensionsFilter, $content_type = null)
    {
        Mage::helper('mycdn')->addLog('[MERGE] to ' . $targetFile);
        Mage::helper('core')->mergeFiles($srcFiles, $targetFile, true, $beforeMergeCallback, $extensionsFilter);
        return $this->uploadFile($srcFiles, $uploadFile, $targetFile, $mustMerge, $beforeMergeCallback, $extensionsFilter, $content_type);
    }

    /**
     * Upload merged files
     *
     * @param array $srcFiles
     * @param string $uploadFile
     * @param string $targetFile
     * @param boolean $mustMerge
     * @param type $beforeMergeCallback
     * @param string $extensionsFilter
     * @param string $content_type
     * @return boolean
     */
    private function uploadFile($srcFiles, $uploadFile, $targetFile, $mustMerge, $beforeMergeCallback, $extensionsFilter, $content_type = null)
    {
        $adapter = Mage::getModel('mycdn/adapter');
        if (!$adapter) {
            return parent::_mergeFiles($srcFiles, $targetFile, $mustMerge, $beforeMergeCallback, $extensionsFilter);
        }
        return $adapter->uploadFileAsync($targetFile, $uploadFile, $content_type);
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
        $mergedFile = md5(implode(',', $files)) . '.js';
        $uploadFileName = 'js' . DS . 'merge' . DS . $mergedFile;
        $targetFileName = $this->_initMergerDir('js') . DS . $mergedFile;

        if ($this->needMerge($uploadFileName)) {
            Mage::helper('mycdn')->addLog('Need to merge ' . $targetFileName . ' =>  ' . $uploadFileName);
            $result = $this->processFiles($files, $uploadFileName, $targetFileName, false, null, 'js', 'application/javascript');
            if (!$result) {
                return parent::getMergedJsUrl($files);
            }
        }
        return Mage::getModel('mycdn/adapter')->getUrl('js/merge/' . $mergedFile);
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
        $mergerDir = $isSecure ? 'css_secure' : 'css';

        $mergedFile = md5(implode(',', $files)) . '.css';
        $uploadFileName = $mergerDir . DS . 'merge' . DS . $mergedFile;
        $targetFileName = $this->_initMergerDir($mergerDir) . DS . $mergedFile;

        if ($this->needMerge($uploadFileName)) {
            Mage::helper('mycdn')->addLog('Need to merge ' . $targetFileName . ' =>  ' . $uploadFileName);
            $result = $this->processFiles($files, $uploadFileName, $targetFileName, false, array($this, 'beforeMergeCss'), 'css', 'text/css');
            if (!$result) {
                return parent::getMergedCssUrl($files);
            }
        }
        return Mage::getModel('mycdn/adapter')->getUrl($mergerDir . '/merge/' . $mergedFile);
    }
}
