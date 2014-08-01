<?php

class Magebuzz_Customoption_Model_Mysql4_Customoption extends Mage_Core_Model_Mysql4_Abstract
{
  public function _construct()
  {
    $this->_init('customoption/customoption', 'customoption_id');
  }

  public function getTableName()
  {
    return $this->getTable('customoption/customoption');
  }

  public function getValue($option_id)
  {
    $read = $this->_getReadAdapter();
    $select = $read->select();
    $select->from($this->getTable('customoption/customoption'))->where('option_id = ?', $option_id);
    $value = $this->_getReadAdapter()->fetchAll($select);
    return $value;
  }
}