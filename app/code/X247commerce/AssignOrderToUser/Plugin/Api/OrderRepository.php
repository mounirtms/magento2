<?php

namespace X247commerce\AssignOrderToUser\Plugin\api;

use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Integration\Model\Oauth\TokenFactory;

class OrderRepository
{

    const ASSIGNED_USER = 'assigned_user_id';
    const ASSIGNED_USERNAME = 'assigned_user_name';
	const RESC_ID = 'Magento_Backend::all';
	
	/** @var \Magento\Framework\App\State */
    private $appState;
    /** @var \Magento\Store\Model\StoreManagerInterface */
    private $storeManager;
    /** @var \Magento\Framework\Api\FilterFactory */
    private $filterFactory;
    /** @var  \Magento\Framework\Api\Search\FilterGroupFactory */
    private $filterGroupFactory;
	
	protected $_aclRetriever;
	protected $userFactory;
	
	protected $tokenModelFactory;

    /**
     * Order Extension Attributes Factory
     *
     * @var OrderExtensionFactory
     */
    protected $extensionFactory;

    /**
     * OrderRepositoryPlugin constructor
     *
     * @param OrderExtensionFactory $extensionFactory
     */
    public function __construct(OrderExtensionFactory $extensionFactory,
	\Magento\Framework\App\State $appState,
    \Magento\Store\Model\StoreManagerInterface $storeManager,
    \Magento\Framework\Api\FilterFactory $filterFactory,
    \Magento\Framework\Api\Search\FilterGroupFactory $filterGroupFactory,
	\Magento\Authorization\Model\Acl\AclRetriever $aclRetriever,
	\Magento\User\Model\UserFactory $userFactory,
	TokenFactory $tokenModelFactory)
    {
		$this->appState = $appState;
        $this->storeManager = $storeManager;
        $this->filterFactory = $filterFactory;
        $this->filterGroupFactory = $filterGroupFactory;
        $this->extensionFactory = $extensionFactory;		
		$this->tokenModelFactory = $tokenModelFactory;	
		$this->userFactory = $userFactory;		
		$this->_aclRetriever = $aclRetriever;
    }

    /**
     * Add "ASSIGNED_USERNAME" extension attribute to order data object to make it accessible in API data
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $order
     *
     * @return OrderInterface
     */
    public function afterGet(OrderRepositoryInterface $subject, OrderInterface $order)
    {
				
		$extensionAttributes = $order->getExtensionAttributes();
        $extensionAttributes = $extensionAttributes ? $extensionAttributes : $this->extensionFactory->create();
		
        $assigned_userid = $order->getData(self::ASSIGNED_USER);
        $extensionAttributes->setAssignedUserId($assigned_userid);
        $order->setExtensionAttributes($extensionAttributes);
		$assigned_username = $order->getData(self::ASSIGNED_USERNAME);
        $extensionAttributes->setAssignedUserName($assigned_username);
        $order->setExtensionAttributes($extensionAttributes);
		

        return $order;
    }
	
	public function beforeGetList(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    )
    {
		
		$token_header = $_SERVER['HTTP_AUTHORIZATION'];
		$api_token = str_replace("bearer ","",$token_header);
		$tokenDetails = $this->tokenModelFactory->create()->loadByToken($api_token);		
		$adminId = $tokenDetails->getAdminId();
		$user = $this->userFactory->create()->load($adminId);
		$roleId = $user->getRole()->getRoleId();
		$selectedResources = $this->_aclRetriever->getAllowedResourcesByRole($roleId);
		if(self::RESC_ID != $selectedResources[0]){
		
			/** @var array of \Magento\Framework\Api\Search\FilterGroup $filterGroup */
			$filterGroups = $searchCriteria->getFilterGroups();
				
			$filter = $this->filterFactory->create();
			$filter->setField('assigned_user_id')
				   ->setConditionType('in')
				   ->setValue($adminId);
			$filters = [$filter];

			/** @var \Magento\Framework\Api\Search\FilterGroup $filterGroup */
			$filterGroup = $this->filterGroupFactory->create();
			$filterGroup->setFilters($filters);
			$filterGroups[] = $filterGroup;

			$searchCriteria->setFilterGroups($filterGroups);
		}
		
		return [$searchCriteria];
	}

    /**
     * Add "ASSIGNED_USERNAME" extension attribute to order data object to make it accessible in API data
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderSearchResultInterface $searchResult
     *
     * @return OrderSearchResultInterface
     */
    public function afterGetList(OrderRepositoryInterface $subject, OrderSearchResultInterface $searchResult)
    {
		$orders = $searchResult->getItems();
		
        foreach ($orders as &$order) {
			$extensionAttributes = $order->getExtensionAttributes();
            $extensionAttributes = $extensionAttributes ? $extensionAttributes : $this->extensionFactory->create();
            $assigned_userid = $order->getData(self::ASSIGNED_USER);            
            $extensionAttributes->setAssignedUserId($assigned_userid);
            $order->setExtensionAttributes($extensionAttributes);
			
			$assigned_username = $order->getData(self::ASSIGNED_USERNAME);            
            $extensionAttributes->setAssignedUserName($assigned_username);
            $order->setExtensionAttributes($extensionAttributes);
        }

        return $searchResult;
    }
}