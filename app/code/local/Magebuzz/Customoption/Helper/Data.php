<?php

class Magebuzz_Customoption_Helper_Data extends Mage_Core_Helper_Abstract {
	public function getOption($option_id) {
		$collection=Mage::getModel('customoption/customoption')->getCollection();
		$collection->addFieldToFilter('option_id',$option_id);
		$option=$collection->getData();
		if(empty($option)) {
			return false;
		}
		return $option;
	}
}