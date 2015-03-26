<?php

class Mygento_Cdn_Model_Image_Gd2 extends Varien_Image_Adapter_Gd2
{

    public function save($destination = null, $newName = null)
    {
        if (Mage::getStoreConfig('mycdn/general/enabled')) {
            $temp = tempnam(sys_get_temp_dir(), 'cdn');
            parent::save($temp);
            echo '"></a>';
            echo $temp."\n";
            echo $destination."\n";
            echo $newName."\n";

            $fileName = (!isset($destination) ) ? $this->_fileName : $destination;

            if (isset($destination) && isset($newName)) {
                $fileName = $destination."/".$newName;
            } elseif (isset($destination) && !isset($newName)) {
                $info = pathinfo($destination);
                print_r($info);
                $fileName = $destination;
                $destination = $info['dirname'];
            } elseif (!isset($destination) && isset($newName)) {
                $fileName = $this->_fileSrcPath."/".$newName;
            } else {
                $fileName = $this->_fileSrcPath.$this->_fileSrcName;
            }


            $fileName = str_replace(Mage::getBaseDir('media'), '', $fileName);

            $adapter = Mage::getStoreConfig('mycdn/general/adapter');
            if (!$adapter) {
                return parent::save($destination, $newName);
            }

            try {
                $model = Mage::getModel('mycdn/adapters_'.$adapter);
                print_r($model);
                $result = $model->upload_file($temp, $fileName);
                if ($result) {
                    unlink($temp);
                }
                print_r($result);
            } catch (Exception $ex) {
                echo 'error:'.$ex->getMessage();
                die();
                return parent::save($destination, $newName);
            }
            die();
        } else {
            return parent::save($destination, $newName);
        }
    }

}
