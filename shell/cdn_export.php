<?php
require_once 'abstract.php';

class Mygento_Cdn_Shell_Export extends Mage_Shell_Abstract
{

    /**
     * Run script
     *
     */
    public function run()
    {
        /** @var Mage_Core_Helper_File_Storage $helper */
        $helper = Mage::helper('core/file_storage');

        // Stop S3 from syncing to itself
        if (Mygento_Cdn_Model_Rewrite_Core_File_Storage::STORAGE_MEDIA_CDN !== $helper->getCurrentStorageCode()) {
            echo "\033[1mYou are currently not using CDN!\033[0m\n";
            return $this;
        }

        /** @var Mage_Core_Model_File_Storage_File|Mage_Core_Model_File_Storage_Database $sourceModel */
        $sourceModel = $helper->getStorageFileModel();

        /** @var Mygento_Cdn_Model_Adapter $sourceModel */
        $destinationModel = $helper->getStorageModel(Mygento_Cdn_Model_Rewrite_Core_File_Storage::STORAGE_MEDIA_CDN)->getAdapter();

        $offset = 0;
        while (($files = $sourceModel->exportFiles($offset, 1)) !== false) {
            foreach ($files as $file) {
                echo sprintf("Uploading %s to CDN.\n", $file['directory'] . '/' . $file['filename']);
            }
            if (!$this->getArg('dry-run')) {
                $destinationModel->importFiles($files);
            }
            $offset += count($files);
        }
    }
}

$shell = new Mygento_Cdn_Shell_Export();
$shell->run();
