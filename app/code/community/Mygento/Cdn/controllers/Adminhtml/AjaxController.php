<?php

class Mygento_Cdn_Adminhtml_AjaxController extends Mage_Adminhtml_Controller_Action
{

    public function skinAction()
    {
        $adapter = Mage::getModel('mycdn/adapter');
        if (!$adapter) {
            return;
        }
        $paths = $adapter->getFileListRecursive('skin/frontend');
        $this->uploadFiles($adapter, $paths);
        $this->getResponse()->setBody('success');
    }

    public function jsAction()
    {
        $adapter = Mage::getModel('mycdn/adapter');
        if (!$adapter) {
            return;
        }
        $paths = $adapter->getFileListRecursive('js');
        $this->uploadFiles($adapter, $paths);
        $this->getResponse()->setBody('success');
    }

    private function uploadFiles($adapter, $paths)
    {
        $i = 0;
        foreach ($paths as $filename => $filetype) {
            switch ($filetype) {
                case 'js':
                    $content_type = 'application/javascript';
                    break;
                case 'css':
                    $content_type = 'text/css';
                    break;
                case 'svg':
                    $content_type = 'image/svg+xml';
                    break;
                case 'jpg':
                case 'jpeg':
                    $content_type = 'image/jpeg';
                    break;
                case 'png':
                    $content_type = 'image/png';
                    break;
                default:
                    $content_type = null;
            }
            if ($adapter->uploadFile($filename, $filename, $content_type)) {
                $i++;
            }
        }
        return $i;
    }
}
