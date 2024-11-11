<?php
/** 
 * Copyright © 247Commerce, Inc. All rights reserved.
 * See COPYING.txt for license details. 
 * @author 247Commerce Core Team <core@247commerce.com> 
 * 
 */
namespace X247commerce\AssignOrderToUser\Ui\Component\Listing\Columns;

class Subadmins implements \Magento\Framework\Option\ArrayInterface
{
    protected $_helperData;
    public function __construct(\X247commerce\AssignOrderToUser\Helper\Data $helperData) {
    	$this->_helperData = $helperData;
    }
    
    /**
     * @return array
     */
    public function toOptionArray() {
	return $order_managing_admins = $this->_helperData->getOrderManageAdmins("subadmins");
    }
}