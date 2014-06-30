<?php

class Magebuzz_Customoption_Model_Mysql4_Customoption extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    

        $this->_init('customoption/customoption', 'customoption_id');
    }
	
/* 	public function getOption ($product_id,$option_id) {
		$tbl1=$this->getTable('catalog/product_option');
		$tbl2=$this->getTable('catalog/product_option_title');
		$where = $this->_getReadAdapter()->quoteInto('product_id = ? AND ', $product_id).$this->_getReadAdapter()->quoteInto('tbl1.option_id =?',$option_id);
		$condition=$this->_getReadAdapter()->quoteInto('tbl1.option_id=tbl2.option_id', '');
		$select=$this->_getReadAdapter()->select()->from(array('tbl1'=>$tbl1))->join(array('tbl2'=>$tbl2),$condition)->where($where);
		$option=$this->_getReadAdapter()->fetchAll($select);
		return $option;
	} */
	
	public function getTableName() {
		return $this->getTable('customoption/customoption');
	}
	
 	public function getValue ($option_id) {
		$read = $this->_getReadAdapter();
        $select = $read->select();
		$select->from($this->getTable('customoption/customoption'))->where('option_id = ?', $option_id);
		$value=$this->_getReadAdapter()->fetchAll($select);
		return $value;
	} 
}