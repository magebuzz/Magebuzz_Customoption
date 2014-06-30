<?php

class Magebuzz_Customoption_Block_Adminhtml_Customoption_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'customoption';
        $this->_controller = 'adminhtml_customoption';
        
        $this->_updateButton('save', 'label', Mage::helper('customoption')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('customoption')->__('Delete'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('customoption_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'customoption_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'customoption_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('customoption_data') && Mage::registry('customoption_data')->getId() ) {
            return Mage::helper('customoption')->__("%s", $this->htmlEscape(Mage::registry('customoption_data')->getName()));
        } else {
            return Mage::helper('customoption')->__('Add Item');
        }
    }
}