<?php
/**
 * Copyright © 247Commerce, Inc. All rights reserved.
 * See COPYING.txt for license details. 
 * @author 247Commerce Core Team <core@247commerce.com> 
 * 
 */

namespace X247commerce\AssignOrderToUser\Controller\Adminhtml\Assign;
use Magento\Backend\App\Action\Context;
use \Magento\Framework\Controller\ResultFactory;
 
class Index extends \Magento\Backend\App\Action
{

    private $sub_admin_id;
    private $sub_admin_name;

    protected $helperData;

    protected $orderResourceModel;

    protected $orderRepository;

    protected $userFactory;

    public function __construct(
        Context $context,
	\X247commerce\AssignOrderToUser\Helper\Data $helperData,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\ResourceModel\Order $orderResourceModel,
        \Magento\User\Model\UserFactory $userFactory
    ) {
        $this->orderResourceModel = $orderResourceModel;
        $this->orderRepository = $orderRepository;
		$this->helperData = $helperData;
        $this->userFactory = $userFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $data = $this->getRequest()->getParams();

        if(isset($data['sub-admin'])) { 
            $user = $this->getRoleData($data['sub-admin']);
            $this->sub_admin_name = $user->getFirstname().' '.$user->getLastname();
            $this->sub_admin_id = $data['sub-admin'][0];
        }

        // update sub_amdin details into sales_order
        $order = $this->orderRepository->get($data['entity_id']);
        $order->setAssignedUserId($this->sub_admin_id);
        $order->setAssignedUserName($this->sub_admin_name);
        $this->orderResourceModel->save($order);
        $this->messageManager->addSuccessMessage('Order has been successfully assigned!');
        $this->_redirect('sales/order/');
    }

    /*
     * @param inter
     * @return Object
     * will get the subamdin details
     */
    public function getRoleData($userId)
    {
        $user = $this->userFactory->create()->load($userId);
        return $user; 
    }
}