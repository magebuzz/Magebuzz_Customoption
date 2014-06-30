<?php
class Magebuzz_Customoption_Adminhtml_CustomoptionController extends Mage_Adminhtml_Controller_action {
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('customoption/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('catalog/product')->load($id);
		$collection=Mage::getModel('catalog/product_option')->getCollection();
		$collection->addFieldToFilter('product_id',$id);
		$collection->getSelect()
					->distinct()
					->join(array('tbl2'=>Mage::getSingleton('core/resource')->getTableName('catalog_product_option_title')),'tbl2.option_id=main_table.option_id');
		$options=$collection->getData();
		foreach ($options as $option) {
			$option_id=$option['option_id'];
			$value=Mage::getModel('customoption/customoption')->getResource()->getValue($option['option_id']);
			$model->setData('value['.$option_id.']',$value[0]['option_type_id']);
		}
		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('customoption_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('customoption/items');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('customoption/adminhtml_customoption_edit'))
				->_addLeft($this->getLayout()->createBlock('customoption/adminhtml_customoption_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('customoption')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
	
	protected function _getReadConnection() {
		return Mage::getModel('core/resource')->getConnection('core_read');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			$product_id=$this->getRequest()->getParam('id');
			$model = Mage::getModel('customoption/customoption');
			/* $model->setData($data)
				->setId($this->getRequest()->getParam('id')); */
/* 			$opion_data=$model->getCollection()->getData();
			if(!empty($opion_data)) {
				foreach ($opion_data as $key) {
					$model->load($key['customoption_id'])->delete();
				}
			} */
			$tablename=Mage::getModel('customoption/customoption')->getResource()->getTableName();
			$connection=Mage::getSingleton('core/resource')->getConnection('core_write');
			$fields=array();
			foreach($data['value'] as $option_id=>$option_type_id) {
			//	$option=Mage::getModel('customoption/customoption')->getResource()->getOption($product_id,$option_id);
				$option=Mage::helper('customoption')->getOption($option_id);
				if(!$option) {
					$model->setData('option_id',$option_id);
					$model->setData('option_type_id',$option_type_id);
					$model->save();
					$model->unsetData();
				}
				else {
					if($option[0]['option_type_id']!=$option_type_id) {
						$fields['option_type_id']=$option_type_id;
						$where=$connection->quoteInto('option_id=? ',$option_id);
						$connection->update($tablename, $fields, $where);
						$connection->commit();
					}
					
				}
			}
			try {	
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('customoption')->__('Option was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $product_id));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('customoption')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('customoption/customoption');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $customoptionIds = $this->getRequest()->getParam('customoption');
        if(!is_array($customoptionIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($customoptionIds as $customoptionId) {
                    $customoption = Mage::getModel('customoption/customoption')->load($customoptionId);
                    $customoption->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($customoptionIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $customoptionIds = $this->getRequest()->getParam('customoption');
        if(!is_array($customoptionIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($customoptionIds as $customoptionId) {
                    $customoption = Mage::getSingleton('customoption/customoption')
                        ->load($customoptionId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($customoptionIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'customoption.csv';
        $content    = $this->getLayout()->createBlock('customoption/adminhtml_customoption_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'customoption.xml';
        $content    = $this->getLayout()->createBlock('customoption/adminhtml_customoption_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
	
	public function productlistGridAction() {
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function gridAction() {
		$this->loadLayout();
		$this->renderLayout();
	}
		
}