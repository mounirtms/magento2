<?php
/** 
 * Copyright © 247Commerce, Inc. All rights reserved.
 * See COPYING.txt for license details. 
 * @author 247Commerce Core Team <core@247commerce.com> 
 * 
 */
namespace X247commerce\AssignOrderToUser\Model\Plugins\Sales\Order;

class Grid {
	
	const RESC_ID = 'Magento_Backend::all';
    public static $table = 'sales_order_grid';
	protected $_authSession;
	protected $_aclRetriever;
	
    	public function __construct(
		\Magento\Authorization\Model\Acl\AclRetriever $aclRetriever,
		\Magento\Backend\Model\Auth\Session $authSession
		) {
		$this->_authSession = $authSession;
		$this->_aclRetriever = $aclRetriever;
		
	}
	
    /**
    *@author 247Commerce
    *@info: filter the order based on assigned sub_amdin
    */
    public function afterSearch($intercepter, $collection) {
        $intercepter;
        $user = $this->_authSession->getUser();
		$roleId = $this->_authSession->getUser()->getRole()->getRoleId();
		$selectedResources = $this->_aclRetriever->getAllowedResourcesByRole($roleId);
		
       if ($collection->getMainTable() === $collection->getTable(self::$table)) {    
            if(self::RESC_ID != $selectedResources[0])
                $collection->addFieldToFilter('assigned_user_id',array('finset' =>$user['user_id']));
        }
        else if($collection->getMainTable() == $collection->getTable('sales_invoice_grid')) {
            if(self::RESC_ID != $selectedResources[0])
                $collection->addFieldToFilter('assigned_user_id',array('finset' =>$user['user_id']));
        }
        else if($collection->getMainTable()== $collection->getTable('sales_shipment_grid')) {
            if(self::RESC_ID != $selectedResources[0]) 
                $collection->addFieldToFilter('assigned_user_id',array('finset' =>$user['user_id']));
        }
        else if($collection->getMainTable()== $collection->getTable('sales_creditmemo_grid')) {
            if(self::RESC_ID != $selectedResources[0]) 
                    $collection->addFieldToFilter('assigned_user_id',array('finset' =>$user['user_id']));
        }
        return $collection;

    }

    

}