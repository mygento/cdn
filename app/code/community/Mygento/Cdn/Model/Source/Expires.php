<?php

/**
 * @category    Mygento
 * @package     Mygento
 * @copyright   Copyright © 2015 NKS LLC. (http://www.mygento.ru)
 * @license     Apache-2.0
 */
class Mygento_Cdn_Model_Source_Expires
{

    public function toOptionArray()
    {
        $helper = Mage::helper('cdn');
        return array(
            array('value' => '+1 day', 'label' => $helper->__('Day')),
            array('value' => '+1 week', 'label' => $helper->__('Week')),
            array('value' => '+1 month', 'label' => $helper->__('Month')),
            array('value' => '+1 year', 'label' => $helper->__('Year')),
        );
    }
}
