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
        $this->assertModelAlias('mycdn/job', 'Mygento_Cdn_Model_Job');
        $this->assertModelAlias('mycdn/adapter', 'Mygento_Cdn_Model_Adapter');
        $this->assertModelAlias('mycdn/source_adapters', 'Mygento_Cdn_Model_Source_Adapters');
    }

    /**
     * @test
     */
    public function testConfig()
    {
        $this->assertConfigNodeHasChild('global/helpers', 'mycdn');
        $this->assertConfigNodeValue('global/helpers/mycdn/class', 'Mygento_Cdn_Helper');
        $this->assertConfigNodeHasChild('global/models', 'mycdn');
        $this->assertConfigNodeValue('global/models/mycdn/class', 'Mygento_Cdn_Model');
        $this->assertConfigNodeHasChild('global/blocks', 'mycdn');
        $this->assertConfigNodeValue('global/blocks/mycdn/class', 'Mygento_Cdn_Block');
    }

    /**
     * @test
     */
    public function testDefaults()
    {
        // the module namespace
        $this->assertConfigNodeHasChild('default', 'mycdn');

        // general presets
        $this->assertConfigNodeHasChild('default/mycdn', 'general');
        $this->assertConfigNodeHasChild('default/mycdn/general', 'enabled');
        $this->assertConfigNodeHasChild('default/mycdn/general', 'debug');
        $this->assertConfigNodeHasChild('default/mycdn/general', 'async');

        $this->assertEquals("0", Mage::getStoreConfig('mycdn/general/enabled'));
        $this->assertEquals("0", Mage::getStoreConfig('mycdn/general/debug'));
        $this->assertEquals("0", Mage::getStoreConfig('mycdn/general/async'));
    }

    /**
     * @test
     */
    public function testModelResourceAlias()
    {
        $this->assertResourceModelAlias('mycdn/job', 'Mygento_Cdn_Model_Resource_Job');
    }

    /**
     * @test
     */
    public function testHelperAlias()
    {
        $this->assertHelperAlias('mycdn', 'Mygento_Cdn_Helper_Data');
    }

    public function testEvent()
    {
        $this->assertEventObserverDefined('global', 'catalog_product_media_save_before', 'mycdn/observer', 'uploadOnSave', 'mycdn_upload');
    }
}
