<?php

namespace Mustafa\YalidineDelivery\Controller\Adminhtml\System;

use Mustafa\YalidineDelivery\Helper\Data;
use Mustafa\YalidineDelivery\Model\Api\Connector;
use Mustafa\YalidineDelivery\Model\Api\RateManager;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;

class UpdateRates extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Sales::shipment';
    protected $connector;
    protected $helper;
    protected $rateManager;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Connector $connector,
        RateManager $rateManager,
        Data $helper
    ) {
        $this->connector = $connector;
        $this->rateManager = $rateManager;
        $this->helper = $helper;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface
     */
    public function execute()
    {
        $storeId = $this->getRequest()->getParam('storeId');
        $userId = $this->helper->getConfigData('user','stores',$storeId);
        $apiKey = $this->helper->getConfigData('key','stores',$storeId);
        $titleHome = $this->getRequest()->getParam('title_home');
        $titleDesk = $this->getRequest()->getParam('title_desk');
        $maxPrice = $this->getRequest()->getParam('max_price');
        $maxWeight = $this->getRequest()->getParam('max_weight');
        if (!$userId) {
            $response = [
                'status' => 'failed',
                'message' => 'Please Insert  Api ID and Token and authenticate first',
           ];
        }else {
            $this->helper->updateConfigData('title_home',$titleHome, 'stores', $storeId);
            $this->helper->updateConfigData('title_desk',$titleDesk, 'stores', $storeId);
            $this->helper->updateConfigData('max_price',$maxPrice, 'stores', $storeId);
            $this->helper->updateConfigData('max_weight',$maxWeight, 'stores', $storeId);
            $connect = $this->connector->request('GET','deliveryfees',[
                'userId'=>$userId,
                'apiKey'=>$apiKey
            ]);
            if ($connect) {
                $data = json_decode($connect->getBody()->getContents(), true);
                
                $db_home_update = $this->rateManager->saveData($storeId,'home', $titleHome, $maxPrice, $maxWeight, $data);
                $db_desk_update = $this->rateManager->saveData($storeId,'desk', $titleDesk, $maxPrice, $maxWeight, $data);
                $response = [
                    'status'  => $data ? 'success' : 'failed',
                    'data' => $data ? __('Connection successful to Yalidine') : __('Authentication failed. Please try again or contact customer service for help'),
                    'message'    => $db_home_update ? __('Rates updated in Database') : __('Something went wrong. Please try again or contact customer service for help'),
                ];
            } else {
                $response = [
                    'status' => 'failed',
                    'message' => 'Could not connect to Yalidine Please confirm Api ID and Token and re-authenticate',
                    'data'    => 'userKey = '.$userId,
                ]; 
            } 
        }
        return $this->resultFactory
            ->create(ResultFactory::TYPE_JSON)
            ->setData($response);
    }
}