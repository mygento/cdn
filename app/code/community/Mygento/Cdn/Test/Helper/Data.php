<?php

class Mygento_Cdn_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case
{

    /**
     * @test
     * @return Mygento_Cdn_Helper_Data
     */
    public function checkClass()
    {
        /* @var Aoe_Scheduler_Helper_Data $helper */
        $helper = Mage::helper('mycdn');

        $this->assertInstanceOf('Mygento_Cdn_Helper_Data', $helper);
        return $helper;
    }
}
