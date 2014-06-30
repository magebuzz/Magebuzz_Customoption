<?php

class Magebuzz_Customoption_Model_Customoption extends Mage_Core_Model_Abstract {
    public function _construct() {
        parent::_construct();
        $this->_init('customoption/customoption');
    }
}