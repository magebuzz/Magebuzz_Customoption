<?php

class Magebuzz_Customoption_Block_Adminhtml_Customoption_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('customoption_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('customoption')->__('Product Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('customoption')->__('Custom Options'),
          'title'     => Mage::helper('customoption')->__('Custom Options'),
          'content'   => $this->getLayout()->createBlock('customoption/adminhtml_customoption_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}