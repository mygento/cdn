<?php

class Mygento_Cdn_Model_Rewrite_Config_Source_Media_Storage extends Mage_Adminhtml_Model_System_Config_Source_Storage_Media_Storage
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = parent::toOptionArray();
        $options[] = [
            'value' => Mygento_Cdn_Model_Rewrite_Core_File_Storage::STORAGE_MEDIA_CDN,
            'label' => 'CDN'
        ];
        return $options;
    }
}
