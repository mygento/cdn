<?php

/**
 * @category    Mygento
 * @package     Mygento
 * @copyright   Copyright © 2015 NKS LLC. (http://www.mygento.ru)
 * @license     Apache-2.0
 */
class Mygento_Cdn_Model_Source_Region
{

    public function toOptionArray()
    {
        return array(
            array('value' => 'us-east-1', 'label' => 'US Standard'),
            array('value' => 'us-west-2', 'label' => 'US West (Oregon)'),
            array('value' => 'us-west-1', 'label' => 'US West (N. California)'),
            array('value' => 'eu-west-1', 'label' => 'EU (Ireland)'),
            array('value' => 'eu-central-1', 'label' => 'EU (Frankfurt)'),
            array('value' => 'ap-southeast-1', 'label' => 'Asia Pacific (Singapore)'),
            array('value' => 'ap-southeast-2', 'label' => 'Asia Pacific (Sydney)'),
            array('value' => 'ap-northeast-1', 'label' => 'Asia Pacific (Tokyo)'),
            array('value' => 'sa-east-1', 'label' => 'South America (Sao Paulo)'),
        );
    }
}
