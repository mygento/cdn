<?php

class Mygento_Cdn_Model_Adapters_S3
{

    private $client;
    private $bucketName;

    public function __construct()
    {
        $this->client = new Aws\S3\S3Client([
            'region' => Mage::getStoreConfig('mycdn/s3/zone'),
            'version' => 'latest',
            'credentials' => [
                'key' => Mage::getStoreConfig('mycdn/s3/access_key_id'),
                'secret' => Mage::getStoreConfig('mycdn/s3/secret_access_key'),
            ],
        ]);
        $this->bucketName = Mage::getStoreConfig('mycdn/s3/bucket');
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
        $data = array(
            'ACL' => 'public-read',
            'Bucket' => $this->bucketName,
            'Key' => Mage::getStoreConfig('mycdn/s3/access_key_id'),
            'ContentType' => $content_type,
            'SourceFile' => $file,
        );
        if (Mage::getStoreConfig('mycdn/s3/gzip')) {
            switch ($content_type) {
                case 'application/javascript':
                    $data['Expires'] = Mage::getStoreConfig('mycdn/general/js_expires');
                case 'text/css':
                    Mage::helper('mycdn')->gzipFile($file);
                    $data['ContentEncoding'] = 'gzip';
                    $data['Expires'] = Mage::getStoreConfig('mycdn/general/css_expires');
                    $file.='.gz';
                    break;
                case 'image/jpeg':
                case 'image/png':
                case 'image/svg+xml':
                    $data['Expires'] = Mage::getStoreConfig('mycdn/general/image_expires');
                    break;
            }
        }
        $this->client->putObject($data);


        //return Mygento_S3::putObject(Mygento_S3::inputFile($file, false), $this->bucketName, $uploadName, Mygento_S3::ACL_PUBLIC_READ, array(), $headers);
    }
}
