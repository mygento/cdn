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
        $data = array(
            'Bucket' => $this->bucketName,
            'Key' => $downloadName,
        );
        try {
            $result = $this->client->getObject($data);
        } catch (Exception $e) {
            return null;
        }
        if ($result) {
            return $result['Body'];
        }
        return null;
    }

    public function uploadFile($file, $uploadName, $content_type = 'application/octet-stream')
    {
        $params = [
            'ACL' => 'public-read',
            'Bucket' => $this->bucketName,
            'Key' => $uploadName,
            'ContentType' => $content_type,
            'SourceFile' => $file,
        ];

        switch ($content_type) {
            case 'application/javascript':
                $params['Expires'] = Mage::getStoreConfig('mycdn/general/js_expires');
                break;
            case 'text/css':
                $params['Expires'] = Mage::getStoreConfig('mycdn/general/css_expires');
                break;
            case 'image/jpeg':
            case 'image/png':
            case 'image/gif':
            case 'image/svg+xml':
                $params['Expires'] = Mage::getStoreConfig('mycdn/general/images_expires');
                break;
            default:
                $params['Expires'] = Mage::getStoreConfig('mycdn/general/images_expires');
        }
        $data = $this->minify($params, $file, $content_type);
        $result = $this->client->putObject($data);
        $code = $result->get("@metadata")['statusCode'];
        if ($code >= 200 && $code < 300) {
            return true;
        }
        return false;
    }

    /**
     * Minify and Gzip file before upload
     *
     * @param array $data
     * @param string $file
     * @param string $content_type
     * @return type
     */
    private function minify($data, $file, $content_type)
    {
        if (!Mage::getStoreConfig('mycdn/general/minify')) {
            return $data;
        }
        switch ($content_type) {
            case 'application/javascript':
            case 'text/css':
                Mage::helper('mycdn')->gzipFile($file, $content_type);
                $data['ContentEncoding'] = 'gzip';
                $data['SourceFile'] = $file . '.gz';
                break;
        }

        return $data;
    }
}
