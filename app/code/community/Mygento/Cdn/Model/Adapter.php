<?php

class Mygento_Cdn_Model_Adapter
{

    private function getAdapter()
    {
        $adapter = Mage::getStoreConfig('mycdn/general/adapter');
        if (!$adapter) {
            return false;
        }

        try {
            return Mage::getModel('mycdn/adapters_' . $adapter);
        } catch (Exception $ex) {
            Mage::helper('mycdn')->addLog($ex->getMessage());
        }
        return false;
    }

    public function getUrl($file)
    {
        $fileName = Mage::helper('mycdn')->getRelativeFile($file);
        $adapter = $this->getAdapter();
        if ($adapter) {
            return $adapter->getUrl($fileName);
        }
    }

    public function fileExists($file)
    {
        return Mage::helper('mycdn')->checkPathInCache($file);
    }

    public function getFile($downloadName)
    {
        Varien_Profiler::start('cdn_download_file_' . $downloadName);
        $adapter = $this->getAdapter();
        if ($adapter) {
            $fileName = Mage::helper('mycdn')->getRelativeFile($downloadName);
            $image = $adapter->downloadFile($fileName);
            if ($image) {
                $bn = new Zend_Filter_BaseName();
                $image_name = $bn->filter($downloadName);
                $dn = new Zend_Filter_Dir();
                $image_path = $dn->filter($downloadName);
                $file = new Varien_Io_File();
                $file->setAllowCreateFolders(true);
                $file->open(['path' => $image_path]);
                $file->streamOpen($image_name);
                $file->streamLock(true);
                $file->streamWrite($image);
                $file->streamUnlock();
                $file->streamClose();

                Mage::helper('mycdn')->addLog('[DOWNLOADED] File downloaded to ' . $downloadName);
                Varien_Profiler::stop('cdn_download_file_' . $downloadName);

                //saving to cache
                Mage::helper('mycdn')->savePathInCache($fileName, $this->getUrl($fileName));
                return true;
            } else {
                Mage::helper('mycdn')->addLog('[NOT DOWNLOADED] File not downloaded to ' . $downloadName);
            }
        }
        Varien_Profiler::stop('cdn_download_file_' . $downloadName);
        return false;
    }

    /**
     *
     * Upload file to CDN
     * @param string $file
     * @param string $uploadName
     * @param string $content_type
     * @return boolean
     */
    public function uploadFile($file, $uploadName, $content_type = null)
    {
        Varien_Profiler::start('cdn_upload_file_' . $uploadName);
        if ($content_type == null) {
            $content_type = Mage::helper('mycdn')->detectFileMimeType($file);
        }
        $adapter = $this->getAdapter();
        if ($adapter) {
            $fileName = Mage::helper('mycdn')->getRelativeFile($uploadName);
            Mage::helper('mycdn')->addLog('[UPLOAD] ' . $fileName . ' as type ' . $content_type);
            $size = new Zend_Validate_File_Size(['min' => Mage::getStoreConfig('mycdn/general/min')]);
            if (!$size->isValid($file)) {
                return false;
            }
            $result = $adapter->uploadFile($file, $fileName, $content_type);
            if ($result) {
                Mage::helper('mycdn')->addLog('[UPLOADED]' . $fileName);
                Mage::helper('mycdn')->addLog('saving to cache ' . $filename);

                //saving to cache
                Mage::helper('mycdn')->savePathInCache($fileName, $this->getUrl($fileName));
                return true;
            }
            Mage::helper('mycdn')->addLog('[NOT UPLOADED]' . $fileName);

            Varien_Profiler::stop('cdn_upload_file_' . $uploadName);
            return false;
        }
    }

    /**
     * Upload file async to CDN, if enabled
     *
     * @param string $file
     * @param string $uploadName
     * @param string $content_type
     * @param boolean $delete
     * @return boolean
     */
    public function uploadFileAsync($file, $uploadName, $content_type = null, $delete = 0)
    {
        if (Mage::getStoreConfig('mycdn/general/async')) {
            $uploadFile = Mage::helper('mycdn')->getRelativeFile($uploadName);

            //saving cron job
            $job = Mage::getModel('mycdn/job')->loadByUploadName($uploadFile);
            $job->addData(['filename' => $file, 'uploadname' => $uploadFile, 'content_type' => $content_type, 'delete' => $delete])->save();
            return false;
        }
        return $this->uploadFile($file, $uploadName, $content_type);
    }

    /**
     * Get recoursive file list by folder
     * @param string $folder
     * @return array
     */
    public function getFileListRecursive($folder)
    {
        $iter = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST, RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
        );

        $paths = [];
        foreach ($iter as $path => $dir) {
            if (!$dir->isDir()) {
                $paths[$path] = $dir->getExtension();
            }
        }
        return $paths;
    }
}
