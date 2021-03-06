<?php

class Magebuzz_Customoption_Block_Adminhtml_Customoption_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
    parent::__construct();
    $this->setId('customoptionGrid');
    $this->setUseAjax(TRUE);
    $this->setDefaultSort('entity_id');
    //	$this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {

    $store = $this->_getStore();
    $collection = Mage::getModel('catalog/product')->getCollection()
      ->addFieldToFilter('has_options', array('neq' => 0))
      ->addFieldToFilter('type_id', 'simple')
      ->addAttributeToSelect('sku')
      ->addAttributeToSelect('name')
      ->addAttributeToSelect('attribute_set_id')
      ->addAttributeToSelect('type_id');

    if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
      $collection->joinField('qty',
        'cataloginventory/stock_item',
        'qty',
        'product_id=entity_id',
        '{{table}}.stock_id=1',
        'left');
    }
    if ($store->getId()) {
      //$collection->setStoreId($store->getId());
      $adminStore = Mage_Core_Model_App::ADMIN_STORE_ID;
      $collection->addStoreFilter($store);
      $collection->joinAttribute('name', 'catalog_product/name', 'entity_id', null, 'inner', $adminStore);
      $collection->joinAttribute('custom_name', 'catalog_product/name', 'entity_id', null, 'inner', $store->getId());
      $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner', $store->getId());
      $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId());
      $collection->joinAttribute('price', 'catalog_product/price', 'entity_id', null, 'left', $store->getId());
    } else {
      $collection->addAttributeToSelect('price');
      $collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
      $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
    }
    $this->setCollection($collection);
    return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
    $this->addColumn('entity_id',
      array(
        'header' => Mage::helper('catalog')->__('ID'),
        'width'  => '50px',
        'type'   => 'number',
        'index'  => 'entity_id',
      ));
    $this->addColumn('name',
      array(
        'header' => Mage::helper('catalog')->__('Name'),
        'index'  => 'name',
      ));

    $store = $this->_getStore();
    if ($store->getId()) {
      $this->addColumn('custom_name',
        array(
          'header' => Mage::helper('catalog')->__('Name in %s', $store->getName()),
          'index'  => 'custom_name',
        ));
    }

    $this->addColumn('type',
      array(
        'header'  => Mage::helper('catalog')->__('Type'),
        'width'   => '60px',
        'index'   => 'type_id',
        'type'    => 'options',
        'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
      ));

    $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
      ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
      ->load()
      ->toOptionHash();

    $this->addColumn('set_name',
      array(
        'header'  => Mage::helper('catalog')->__('Attrib. Set Name'),
        'width'   => '100px',
        'index'   => 'attribute_set_id',
        'type'    => 'options',
        'options' => $sets,
      ));

    $this->addColumn('sku',
      array(
        'header' => Mage::helper('catalog')->__('SKU'),
        'width'  => '80px',
        'index'  => 'sku',
      ));

    $store = $this->_getStore();
    $this->addColumn('price',
      array(
        'header'        => Mage::helper('catalog')->__('Price'),
        'type'          => 'price',
        'currency_code' => $store->getBaseCurrency()->getCode(),
        'index'         => 'price',
      ));

    if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
      $this->addColumn('qty',
        array(
          'header' => Mage::helper('catalog')->__('Qty'),
          'width'  => '100px',
          'type'   => 'number',
          'index'  => 'qty',
        ));
    }

    $this->addColumn('visibility',
      array(
        'header'  => Mage::helper('catalog')->__('Visibility'),
        'width'   => '70px',
        'index'   => 'visibility',
        'type'    => 'options',
        'options' => Mage::getModel('catalog/product_visibility')->getOptionArray(),
      ));

    $this->addColumn('status',
      array(
        'header'  => Mage::helper('catalog')->__('Status'),
        'width'   => '70px',
        'index'   => 'status',
        'type'    => 'options',
        'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
      ));

    if (!Mage::app()->isSingleStoreMode()) {
      $this->addColumn('websites',
        array(
          'header'   => Mage::helper('catalog')->__('Websites'),
          'width'    => '100px',
          'sortable' => FALSE,
          'index'    => 'websites',
          'type'     => 'options',
          'options'  => Mage::getModel('core/website')->getCollection()->toOptionHash(),
        ));
    }

    if (Mage::helper('catalog')->isModuleEnabled('Mage_Rss')) {
      $this->addRssList('rss/catalog/notifystock', Mage::helper('catalog')->__('Notify Low Stock RSS'));
    }
    return parent::_prepareColumns();
  }

  public function getRowUrl($row)
  {
    return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

  public function getGridUrl()
  {
    return $this->getData('grid_url')
      ? $this->getData('grid_url')
      : $this->getUrl('*/*/productlistGrid', array('_current' => TRUE, 'id' => $this->getRequest()->getParam('id')));
  }


  protected function _getStore()
  {
    $storeId = (int)$this->getRequest()->getParam('store', 0);
    return Mage::app()->getStore($storeId);
  }


}