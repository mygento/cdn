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
        $fileName = Mage::helper('mycdn')->getRelativeFile($file);
        Mage::helper('mycdn')->addLog('checking cache for file ' . $fileName);
        if (Mage::app()->getCache()->load('cdn_' . $fileName)) {
            Mage::helper('mycdn')->addLog('[cached] ' . $fileName);
        } else {
            Mage::helper('mycdn')->addLog('[non cached] ' . $fileName);
        }
        return Mage::app()->getCache()->load('cdn_' . $fileName);
    }

    public function getFile($downloadName)
    {
        Varien_Profiler::start('cdn_download_file_' . $downloadName);
        $adapter = $this->getAdapter();
        if ($adapter) {
            $filename = Mage::helper('mycdn')->getRelativeFile($downloadName);
            $image = $adapter->downloadFile($filename);
            if ($image) {
                $bn = new Zend_Filter_BaseName();
                $image_name = $bn->filter($downloadName);
                $dn = new Zend_Filter_Dir();
                $image_path = $dn->filter($downloadName);
                $file = new Varien_Io_File();
                $file->setAllowCreateFolders(true);
                $file->open(array('path' => $image_path));
                $file->streamOpen($image_name);
                $file->streamLock(true);
                $file->streamWrite($image);
                $file->streamUnlock();
                $file->streamClose();
                Mage::helper('mycdn')->addLog('[downloaded] File downloaded to ' . $downloadName);
                Varien_Profiler::stop('cdn_download_file_' . $downloadName);
                Mage::app()->getCache()->save($this->getUrl($filename), 'cdn_' . $filename, array('MYCDN'));
                return true;
            } else {
                Mage::helper('mycdn')->addLog('[not downloaded] File not downloaded to ' . $downloadName);
            }
        }
        Varien_Profiler::stop('cdn_download_file_' . $downloadName);
        return false;
    }

    public function uploadFile($file, $uploadName, $content_type = null)
    {
        Varien_Profiler::start('cdn_upload_file_' . $uploadName);
        if ($content_type == null) {
            $content_type = Mage::helper('mycdn')->detectFileMimeType($file);
        }
        Mage::helper('mycdn')->addLog('uploading ' . Mage::helper('mycdn')->getRelativeFile($uploadName) . ' as type ' . $content_type);

        $adapter = $this->getAdapter();
        if ($adapter) {
            $filename = Mage::helper('mycdn')->getRelativeFile($uploadName);
            $size = new Zend_Validate_File_Size(array('min' => Mage::getStoreConfig('mycdn/general/min')));
            if (!$size->isValid($file)) {
                return false;
            }
            $result = $adapter->uploadFile($file, $filename, $content_type);
            if ($result) {
                Mage::helper('mycdn')->addLog('uploaded successfully ' . $filename);
                Mage::helper('mycdn')->addLog('saving to cache ' . $filename);
                Mage::app()->getCache()->save($this->getUrl($filename), 'cdn_' . $filename, array('MYCDN'));
            } else {
                Mage::helper('mycdn')->addLog('not uploaded ' . $filename);
            }
            Varien_Profiler::stop('cdn_upload_file_' . $uploadName);
            return $result;
        }
    }

    public function uploadFileAsync($file, $uploadName, $content_type = null, $delete = 0)
    {
        if (Mage::getStoreConfig('mycdn/general/async')) {
            $uploadFile = Mage::helper('mycdn')->getRelativeFile($uploadName);
            $job = Mage::getModel('mycdn/job')->loadByUploadName($uploadFile);
            if (!$job->getId()) {
                $job->setData(array('filename' => $file, 'uploadname' => $uploadFile, 'content_type' => $content_type, 'delete' => $delete))->save();
            }
            return false;
        }
        return $this->uploadFile($file, $uploadName, $content_type);
    }

    public function getFileListRecursive($folder)
    {
        $iter = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST,
            RecursiveIteratorIterator::CATCH_GET_CHILD // Ignore "Permission denied"
        );

        $paths = array();
        foreach ($iter as $path => $dir) {
            if (!$dir->isDir()) {
                $paths[$path] = $dir->getExtension();
            }
        }
        return $paths;
    }
}
