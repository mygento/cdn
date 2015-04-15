<?php

class Mygento_Cdn_Test_Config_Base extends EcomDev_PHPUnit_Test_Case_Config
{

    /**
     * @test
     */
    public function testValidCodepool()
    {
        $this->assertModuleCodePool('community');
    }

    /**
     * @test
     */
    public function testBlockAlias()
    {
        $this->assertBlockAlias('mycdn/version', 'Mygento_Cdn_Block_Version');
    }

    /**
     * @test
     */
    public function testModelAlias()
    {
        $this->assertModelAlias('mycdn/image', 'Mygento_Cdn_Model_Image');
    }

    /**
     * @test
     */
    public function testHelperAlias()
    {
        $this->assertHelperAlias('mycdn', 'Mygento_Cdn_Helper_Data');
    }
}
