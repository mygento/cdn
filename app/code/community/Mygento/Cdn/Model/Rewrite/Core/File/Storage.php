<?php

class Mygento_Cdn_Model_Rewrite_Core_File_Storage extends Mage_Core_Model_File_Storage
{
    const STORAGE_MEDIA_CDN = 3;

    public function getStorageModel($storage = null, $params = [])
    {
        $storageModel = parent::getStorageModel($storage, $params);
        if ($storageModel === false) {
            if (is_null($storage)) {
                $storage = Mage::helper('core/file_storage')->getCurrentStorageCode();
            }
            switch ($storage) {
                case self::STORAGE_MEDIA_CDN:
                    /** @var Mygento_Cdn_Model_Adapter $storageModel */
                    $storageModel = Mage::getModel('mycdn/adapter');
                    break;
                default:
                    return false;
            }
            if (isset($params['init']) && $params['init']) {
                $storageModel->init();
            }
        }
        return $storageModel;
    }
}
