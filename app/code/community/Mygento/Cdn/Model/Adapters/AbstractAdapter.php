<?php

abstract class Mygento_Cdn_Model_Adapters_AbstractAdapter extends Mage_Core_Model_File_Storage_Abstract
{

    protected $errors = [];

    /**
     * Clear files and directories in storage
     */
    abstract public function clear();

    /**
     * Load object data by filename
     *
     * @param  string $filePath
     */
    abstract public function loadByFilename($filePath);
    
    /**
     * Export files list in defined range
     *
     * @param  int $offset
     * @param  int $count
     * @return array|bool
     */
    abstract public function exportFiles($offset = 0, $count = 100);
    
    /**
     * Import files list
     *
     * @param  array $files
     */
    abstract public function importFiles($files);
    
    /**
     * Store file into database
     *
     * @param  string $filename
     */
    abstract public function saveFile($filename);
    
    /**
     * Check whether file exists in DB
     *
     * @param  string $filePath
     * @return bool
     */
    abstract public function fileExists($filePath);
    
    /**
     * Copy files
     *
     * @param  string $oldFilePath
     * @param  string $newFilePath
     */
    abstract public function copyFile($oldFilePath, $newFilePath);
    
    /**
     * Rename files in database
     *
     * @param  string $oldFilePath
     * @param  string $newFilePath
     */
    abstract public function renameFile($oldFilePath, $newFilePath);
    
    /**
     * Return directory listing
     *
     * @param string $directory
     * @return mixed
     */
    abstract public function getDirectoryFiles($directory);
    

    /**
     * Delete file from database
     *
     * @param string $path
     */
    abstract public function deleteFile($path);
    

    public function init()
    {
        return $this;
    }

    /**
     * @return string
     */
    public function getStorageName()
    {
        return Mage::helper('mycdn')->__('CDN');
    }

    /**
     * Check if there was errors during sync process
     *
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }

    /**
     * Export directories from storage
     *
     * @param  int $offset
     * @param  int $count
     * @return bool|array
     */
    public function exportDirectories($offset = 0, $count = 100)
    {
        return false;
    }

    /**
     * Import directories to storage
     *
     * @param  array $dirs
     */
    public function importDirectories(array $dirs = [])
    {
        return $this;
    }
}
