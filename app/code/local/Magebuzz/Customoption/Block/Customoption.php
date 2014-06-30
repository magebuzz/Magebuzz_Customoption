<?php
class Magebuzz_Customoption_Block_Customoption extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getCustomoption()     
     { 
        if (!$this->hasData('customoption')) {
            $this->setData('customoption', Mage::registry('customoption'));
        }
        return $this->getData('customoption');
        
    }
}