<?php

abstract class Mygento_Cdn_Block_Adminhtml_System_Abstract extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    protected $model = '';

    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('mygento/cdn/system/button.phtml');
    }

    public function getAjaxUrl()
    {
        return Mage::helper('adminhtml')->getUrl('mycdn/adminhtml_ajax/' . $this->model);
    }

    /**
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     *
     * @SuppressWarnings("unused")
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml() . $this->getButtonHtml();
    }

    protected function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
            'id' => 'mycdn_' . $this->model . '_button',
            'label' => Mage::helper('mycdn')->__('Upload'),
            'onclick' => 'javascript:upd_' . $this->model . '(); return false;'
            ));

        return $button->toHtml();
    }
}
