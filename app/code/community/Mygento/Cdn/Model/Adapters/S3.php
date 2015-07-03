<?php

class Mygento_Cdn_Model_Adapters_S3
{

    private $accessKey;
    private $secretKey;
    private $bucketName;

    public function __construct()
    {
        $this->accessKey = Mage::getStoreConfig('mycdn/s3/access_key_id');
        $this->secretKey = Mage::getStoreConfig('mycdn/s3/secret_access_key');
        $this->bucketName = Mage::getStoreConfig('mycdn/s3/bucket');
    }

    private function setKeys()
    {
        if ($this->accessKey !== null && $this->secretKey !== null) {
            Mygento_S3::setAuth($this->accessKey, $this->secretKey);
        }
    }

    /**
     * Creates a full URL to the image on the remote server
     *
     * @param string $filename  path (with filename) from the CDN root
     * @return string
     */
    public function getUrl($filename)
    {
        $type = Mage::app()->getStore()->isCurrentlySecure() ? 'url_base_secure' : 'url_base';
        $base_url = Mage::getStoreConfig('mycdn/s3/' . $type);
        $filename = $base_url . '/' . $filename;
        return $filename;
    }

    public function downloadFile($downloadName)
    {
        $this->setKeys();
        $file = Mygento_S3::getObject($this->bucketName, $downloadName);
        if ($file) {
            return $file->body;
        }
        return null;
    }

    public function uploadFile($file, $uploadName, $content_type = 'application/octet-stream')
    {
        $this->setKeys();
        $headers = array('Content-Type' => $content_type);
        if (Mage::getStoreConfig('mycdn/s3/gzip')) {
            switch ($content_type) {
                case 'application/javascript':
                case 'text/css':
                    Mage::helper('mycdn')->gzipFile($file);
                    $headers['Content-Encoding'] = 'gzip';
                    break;
            }
        }

        return Mygento_S3::putObject(Mygento_S3::inputFile($file, false), $this->bucketName, $uploadName, Mygento_S3::ACL_PUBLIC_READ, array(), $headers);
    }
}
