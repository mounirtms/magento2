<?php

namespace Mustafa\YalidineDelivery\Controller\Adminhtml\Authentication;

use Mustafa\YalidineDelivery\Helper\Data;
use Mustafa\YalidineDelivery\Model\Api\Connector;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;

class TestAuthentication extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Sales::shipment';
    protected $connector;
    protected $helper;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Connector $connector,
        Data $helper
    ) {
        $this->connector = $connector;
        $this->helper = $helper;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface
     */
    public function execute()
    {
        $storeId = $this->getRequest()->getParam('storeId');
        $userId = $this->getRequest()->getParam('userId');
        $apiKey = $this->getRequest()->getParam('apiKey');
        if ($userId === '' || $apiKey==='') {
            $userId = $this->helper->getConfigData('user','stores',$storeId);
            $apiKey = $this->helper->getConfigData('key','stores',$storeId);
            if ($userId === null) {
                $response = [
                    'status' => 'failed',
                    'message' => 'Please Insert Api User and Key',
                    ];
                return $this->resultFactory
                ->create(ResultFactory::TYPE_JSON)
                ->setData($response);
            }
        }

        $authentication = $this->connector->testAuthenticate($userId, $apiKey, $storeId);

        $response = [
            'status'  => $authentication ? 'success' : 'failed',
            'message' => $authentication ? __('Authentication successful') : __('Authentication failed. Please try again or contact customer service for help'),
            'data'    => $authentication,

        ];

        return $this->resultFactory
            ->create(ResultFactory::TYPE_JSON)
            ->setData($response);
    }
}