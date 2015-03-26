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

    public function upload_file($file, $uploadName)
    {
        if ($this->accessKey !== null && $this->secretKey !== null) {
            Mygento_S3::setAuth($this->accessKey, $this->secretKey);
        }
        
        echo $file."\n";
        echo $uploadName."\n";

        //return self::putObject(self::inputResource(fopen($file, 'rb'), filesize($file)), $bucketName, $uploadName, self::ACL_PUBLIC_READ);
        return Mygento_S3::putObject(Mygento_S3::inputFile($file, false), $this->bucketName, $uploadName, Mygento_S3::ACL_PUBLIC_READ);
    }

}
