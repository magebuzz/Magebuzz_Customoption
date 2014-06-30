<?php

class Magebuzz_Customoption_Model_Mysql4_Customoption_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('customoption/customoption');
    }
}