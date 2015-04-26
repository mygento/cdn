<?php

class Mygento_Cdn_Test_Config_Rewrite extends EcomDev_PHPUnit_Test_Case_Config
{

    /**
     * @test
     */
    public function testImageModelAlias()
    {
        $this->assertModelAlias('catalog/product_image', 'Mygento_Cdn_Model_Rewrite_Catalog_Product_Image');
    }

    /**
     * @test
     */
    public function testPackageAlias()
    {
        $this->assertModelAlias('core/design_package', 'Mygento_Cdn_Model_Rewrite_Core_Design_Package');
    }
    
    /**
     * @test
     */
    public function testWidgetAlias()
    {
        $this->assertModelAlias('widget/template_filter', 'Mygento_Cdn_Model_Rewrite_Widget_Template_Filter');
    }
}
