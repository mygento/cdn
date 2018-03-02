<?php

class Mygento_Cdn_Helper_Rewrite_Core_File_Storage_Database extends Mage_Core_Helper_File_Storage_Database
{

    /**
     * Check if we use storage
     *
     * @return bool
     */
    public function checkDbUsage()
    {
        if (null === $this->_useDb) {
            $currentStorage = (int) Mage::app()->getConfig()
                    ->getNode(Mage_Core_Model_File_Storage::XML_PATH_STORAGE_MEDIA);
            $this->_useDb = ($currentStorage == Mygento_Cdn_Model_Rewrite_Core_File_Storage::STORAGE_MEDIA_CDN);
        }

        return $this->_useDb;
    }
}
