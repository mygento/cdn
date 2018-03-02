<?php

class Mygento_Cdn_Model_Rewrite_Core_File_Storage_Database extends Mage_Core_Model_File_Storage_Database
{

    public function loadByFilename($filePath)
    {
        $storage = Mage::helper('core/file_storage')->getCurrentStorageCode();
        if ($storage == Mygento_Cdn_Model_Rewrite_Core_File_Storage::STORAGE_MEDIA_CDN) {
            $adapter = Mage::getModel('mycdn/adapter')->getAdapter();
            $adapter->loadByFilename($filePath);
            if ($adapter->getData('id')) {
                $this->setData('id', $adapter->getData('id'));
                $this->setData('filename', $adapter->getData('filename'));
                $this->setData('content', $adapter->getData('content'));
            }
            return $this;
        }
        return parent::loadByFilename($filePath);
    }

    /**
     * Return directory listing
     *
     * @param string $directory
     * @return mixed
     */
    public function getDirectoryFiles($directory)
    {
        $directory = Mage::helper('core/file_storage_database')->getMediaRelativePath($directory);
        try {
            return $this->_getResource()->getDirectoryFiles($directory);
        } catch (Exception $e) {
            return [];
        }
    }

    public function getId()
    {
        if ($this->isEnabledCdn()) {
            return $this->getData('id');
        }
        return parent::getId();
    }

    private function isEnabledCdn()
    {
        $storage = Mage::helper('core/file_storage')->getCurrentStorageCode();
        return $storage == Mygento_Cdn_Model_Rewrite_Core_File_Storage::STORAGE_MEDIA_CDN;
    }

    /**
     * Store file into database
     *
     * @param  string $filename
     * @return Mage_Core_Model_File_Storage_Database
     */
    public function saveFile($filename)
    {
        if (!$this->isEnabledCdn()) {
            parent::saveFile($filename);
            return $this;
        }
        $adapter = Mage::getModel('mycdn/adapter')->getAdapter();
        $adapter->saveFile($filename);
        return $this;
    }
}
