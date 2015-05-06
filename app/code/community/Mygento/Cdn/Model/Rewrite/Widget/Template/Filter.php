<?php

class Mygento_Cdn_Model_Rewrite_Widget_Template_Filter extends Mage_Widget_Model_Template_Filter
{

    /**
     * Retrieve media file URL directive
     *
     * @param array $construction
     * @return string
     */
    public function mediaDirective($construction)
    {
        if (!Mage::getStoreConfig('mycdn/general/enabled')) {
            return parent::mediaDirective($construction);
        }
        $params = $this->_getIncludeParameters($construction[2]);
        $adapter = Mage::getModel('mycdn/adapter');
        if (!$adapter) {
            return parent::mediaDirective($construction);
        }
        $filename = Mage::getBaseDir('media') .'/'. $params['url'];

        if (is_file($filename)) {
            if ($adapter->fileExists($filename)) {
                return $adapter->getUrl($filename);
            } else {
                $targetFile = Mage::helper('mycdn')->getRelativeFile($filename);
                $result = $adapter->uploadFileAsync($filename, $targetFile);
                if ($result) {
                    return $adapter->getUrl($filename);
                }
            }
        } else {
            if (Mage::helper('mycdn')->getCdnFile($filename)) {
                return $adapter->getUrl($filename);
            }
        }

        return parent::mediaDirective($construction);
    }
}
