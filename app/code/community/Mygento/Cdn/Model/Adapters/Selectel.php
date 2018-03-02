<?php

class Mygento_Cdn_Model_Adapters_Selectel extends Mygento_Cdn_Model_Adapters_AbstractAdapter
{

    private $user;
    private $pass;
    private $bucketName;

    public function __construct()
    {
        $this->user = Mage::getStoreConfig('mycdn/selectel/user');
        $this->pass = Mage::getStoreConfig('mycdn/selectel/passwd');
        $this->bucketName = Mage::getStoreConfig('mycdn/selectel/bucket');
    }

    public function clear()
    {
        die('clear');
    }

    /**
     * Load object data by filename
     *
     * @param  string $filePath
     */
    public function loadByFilename($filePath)
    {

        $result = $this->downloadFile($filePath);
        //var_dump($result);
        if ($result) {
            Mage::helper('mycdn')->addLog('loadByFilename found: ' . $filePath);
            $this->setData('id', $filePath);
            $this->setData('filename', $filePath);
            $this->setData('content', $result);
        } else {
            Mage::helper('mycdn')->addLog('loadByFilename not found: ' . $filePath);
            $this->unsetData();
        }
        return $this;
    }

    /**
     * Export files list in defined range
     *
     * @param  int $offset
     * @param  int $count
     * @return array|bool
     */
    public function exportFiles($offset = 0, $count = 100)
    {
        die('exportFiles');
    }

    /**
     * Import files list
     *
     * @param  array $files
     */
    public function importFiles($files)
    {
        Mage::helper('mycdn')->addLog('import Files');
        foreach ($files as $file) {
            $name = $file['directory'] ? $file['directory'] . DS . $file['filename'] : $file['filename'];
            try {
                $this->uploadFile(Mage::getBaseDir('media') . DS . $name, $name);
            } catch (Exception $e) {
                $this->errors[] = $e->getMessage();
                Mage::logException($e);
                echo $e->getMessage();
            }
        }
        Mage::helper('mycdn')->addLog('import DONE');
    }

    /**
     * Store file into database
     *
     * @param  string $filename
     */
    public function saveFile($filename)
    {
        Mage::helper('mycdn')->addLog('saveFile: ' . $filename);
        $this->uploadFile(Mage::getBaseDir('media') . DS . $filename, $filename);
    }

    /**
     * Check whether file exists in DB
     *
     * @param  string $filePath
     * @return bool
     */
    public function fileExists($filePath)
    {
        die('fileExists ' . $filePath);
    }

    /**
     * Copy files
     *
     * @param  string $oldFilePath
     * @param  string $newFilePath
     */
    public function copyFile($oldFilePath, $newFilePath)
    {
        die('copyFile ' . $oldFilePath . ' ' . $newFilePath);
    }

    /**
     * Rename files in database
     *
     * @param  string $oldFilePath
     * @param  string $newFilePath
     */
    public function renameFile($oldFilePath, $newFilePath)
    {
        die('renameFile ' . $oldFilePath . ' ' . $newFilePath);
    }

    /**
     * Return directory listing
     *
     * @param string $directory
     * @return mixed
     */
    public function getDirectoryFiles($directory)
    {
        die('getDirectoryFiles ' . $directory);
    }

    /**
     * Delete file from database
     *
     * @param string $path
     */
    public function deleteFile($path)
    {
        die('deleteFile ' . $path);
    }

    public function getUrl($filename)
    {
        $type = Mage::app()->getStore()->isCurrentlySecure() ? 'url_base_secure' : 'url_base';
        $base_url = Mage::getStoreConfig('mycdn/selectel/' . $type);
        $filename = $base_url . '/' . $filename;
        return $filename;
    }

    public function downloadFile($downloadName)
    {
        if ($this->user == null || $this->pass == null) {
            return false;
        }
        $storage = new Mygento_SelectelStorage($this->user, $this->pass);
        $container = $storage->getContainer($this->bucketName);
        $result = $container->getFile($downloadName);
        if ($result['header']['HTTP-Code'] === 200) {
            return $result['content'];
        }
        return false;
    }

    public function uploadFile($file, $uploadName, $content_type)
    {
        if ($this->user == null || $this->pass == null) {
            return false;
        }
        $storage = new Mygento_SelectelStorage($this->user, $this->pass);
        $container = $storage->getContainer($this->bucketName);
        if (Mage::getStoreConfig('mycdn/general/minify')) {
            switch ($content_type) {
                case 'application/javascript':
                case 'text/css':
                    Mage::helper('mycdn')->minifyFile($file, $content_type);
                    $file .= '.min';
                    break;
            }
        }
        return $container->putFile($file, $uploadName);
    }
}
