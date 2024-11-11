<?php
/**
 * Copyright © 247Commerce, Inc. All rights reserved.
 * See COPYING.txt for license details. 
 * @author 247Commerce Core Team <core@247commerce.com> 
 * 
 */
namespace X247commerce\AssignOrderToUser\Helper;
use \Magento\Framework\App\Helper\AbstractHelper;
class Data extends AbstractHelper
{
    const RESOURCE = 'Magento_Sales::actions';
    const ALLOW = '%allow%';

    protected $authSession;
    private $_objectManager;

    protected $invoiceResourceModel;

    protected $invoiceRepository;

    protected $shipmentResourceModel;

    protected $shipmentRepository;

    protected $creditmemoResourceModel;

    protected $creditmemoRepository;

    protected $_userCollectionFactory;
    protected $resourceData;
	private $invoice;
	public $sub_admins = array();
	public $options = array();
    public $admin_collection;
        

	public function __construct(\Magento\Backend\Model\Auth\Session $authSession,
	\Magento\Sales\Model\Order\Invoice $invoice,
	\Magento\Sales\Api\InvoiceRepositoryInterface $invoiceRepository,
        \Magento\Sales\Model\ResourceModel\Order\Invoice $invoiceResourceModel,

        \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository,
        \Magento\Sales\Model\ResourceModel\Order\Shipment $shipmentResourceModel,

        \Magento\Sales\Api\CreditmemoRepositoryInterface $creditmemoRepository,
        \Magento\Sales\Model\ResourceModel\Order\Creditmemo $creditmemoResourceModel,

        \Magento\User\Model\ResourceModel\User\CollectionFactory $userCollectionFactory,
		\Magento\Framework\App\ResourceConnection $resourceData,

	\Magento\Framework\ObjectManagerInterface $objectmanager) {
            $this->_objectManager = $objectmanager;
            $this->invoiceResourceModel = $invoiceResourceModel;
            $this->invoiceRepository = $invoiceRepository;

            $this->shipmentResourceModel = $shipmentResourceModel;
            $this->shipmentRepository = $shipmentRepository;

            $this->creditmemoResourceModel = $creditmemoResourceModel;
            $this->creditmemoRepository = $creditmemoRepository;

            $this->_userCollectionFactory = $userCollectionFactory;
			$this->resourceData = $resourceData;

	    $this->authSession = $authSession;
	    $this->invoice = $invoice;
	}

	/**
	 * Get user collection
	 */
	public function getUserCollection()
	{
	    return $this->_userFactory->create();
	}

	public function getCurrentUser()
	{
	    return $this->authSession->getUser();
	}


	public function getOrderManageAdmins($case=false) {
        // will get all the sub_admin, who are all have the authorization to access the sales details
        $collection = $this->_userCollectionFactory->create();
        $collection->getSelect()->joinLeft(array('b' => $collection->getTable('authorization_role')),
                                               		'main_table.user_id = b.user_id and main_table.is_active = 1');
        $collection->getSelect()->joinLeft(array('c' => $collection->getTable('authorization_rule')),
                                               'b.parent_id = c.role_id')
		->where("c.resource_id = '".self::RESOURCE. "' and c.permission like '". self::ALLOW."'");
        
		switch ($case) {  
                    case "assignpopup":
		 	if($collection->getSize()) {
                            foreach ($collection as $key => $_collection) {
                                $this->sub_admins[$key]['username'] = $_collection->getFirstname().' '.$_collection->getLastname();
                                $this->sub_admins[$key]['user_id'] = $_collection->getUserId();
                                $this->sub_admins[$key]['resource_id'] = $_collection->getResourceId();
                            }
			}
			$this->admin_collection =  $this->sub_admins;  
		   	break;  
                    case "subadmins":  
		  	if($collection->getSize()) {
                            foreach ($collection as $key => $_sub_admins) {
                            $this->options[] = ['label' => $_sub_admins->getFirstname().' '.$_sub_admins->getLastname(),
                                                'value' =>$_sub_admins->getFirstname().' '.$_sub_admins->getLastname()];	
                            }
			}
			$this->admin_collection =  $this->options;
		   	break;  
                    case "size":
                        $this->admin_collection = $collection->getSize();
                        break;
                    default:  
		     $this->admin_collection =  $collection;	  
		}  
                return $this->admin_collection;
	}

        
    /**
     *@param $order Object
     *@param $invoiceId 
     *@return null 
     *@author 247Commerce
     *@info will update the sub_admin detatils
     */
	public function UpdateInvoice($order,$invoiceId) {
	$invoice = $this->invoiceRepository->get($invoiceId);
        $invoice->setAssignedUserId($order->getData('assigned_user_id'));
        $invoice->setAssignedUserName($order->getData('assigned_user_name'));
        $this->invoiceResourceModel->save($invoice);
	}
        
        /**
         *@param $order Object
         *@param $shipmentId 
         *@return null 
         *@author 247Commerce
         *@info will update the sub_admin detatils
         */
	
	public function UpdateShipment($shipmentId,$order) {
		$shipment = $this->shipmentRepository->get($shipmentId);
        $shipment->setAssignedUserId($order->getData('assigned_user_id'));
        $shipment->setAssignedUserName($order->getData('assigned_user_name'));
        $this->shipmentResourceModel->save($shipment);
	}
	
        /**
         *@param $order Object
         *@param $creditmemoId 
         *@return null 
         *@author 247Commerce
         *@info will update the sub_admin detatils
         */
	public function UpdateCrditMemo($creditmemoId,$order) {
		$creditmemo = $this->creditmemoRepository->get($creditmemoId);
        $creditmemo->setAssignedUserId($order->getData('assigned_user_id'));
        $creditmemo->setAssignedUserName($order->getData('assigned_user_name'));
        $this->creditmemoResourceModel->save($creditmemo);
	}

}