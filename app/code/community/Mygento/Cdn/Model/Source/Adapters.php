<?php

class Mygento_Cdn_Model_Source_Adapters
{

    public function toOptionArray()
    {
        return array(
            array('value' => 's3', 'label' => 'S3'),
        );
    }

}
