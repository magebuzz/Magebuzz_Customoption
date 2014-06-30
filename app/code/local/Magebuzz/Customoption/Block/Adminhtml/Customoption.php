<?php
class Magebuzz_Customoption_Block_Adminhtml_Customoption extends Mage_Adminhtml_Block_Widget_Grid_Container {
	public function __construct() {
	    $this->_controller = 'adminhtml_customoption';
	    $this->_blockGroup = 'customoption';
	    $this->_headerText = Mage::helper('customoption')->__('Manage Optioned Products');
	    $this->_addButtonLabel = Mage::helper('customoption')->__('Add Item');
	    parent::__construct();
		$this->_removeButton('add');
	}
}