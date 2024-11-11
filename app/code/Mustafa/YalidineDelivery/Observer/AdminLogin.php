<?php

namespace Mustafa\YalidineDelivery\Observer;

use Mustafa\YalidineDelivery\Model\Config\Source\YesNoTest;
use Mustafa\YalidineDelivery\Model\Service\Notification as NotificationService;
use Mustafa\YalidineDelivery\Helper\Data;
use Mustafa\YalidineDelivery\Model\Cache\Api as ApiCache;
use Mustafa\YalidineDelivery\Model\Api\Connector;

//on admin login valid authentication check
class AdminLogin implements \Magento\Framework\Event\ObserverInterface
{
    protected $apiCache;
    protected $connector;
    protected $helper;
    protected $notificationService;

    public function __construct(
        ApiCache $apiCache,
        Connector $connector,
        Data $helper,
        NotificationService $notificationService
    ) {
        $this->apiCache = $apiCache;
        $this->connector = $connector;
        $this->helper = $helper;
        $this->notificationService = $notificationService;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->getConfigData('active','default',0) || $this->helper->getConfigData('active','default',0) == YesNoTest::OPTION_TEST) {
            // plugin not active or in test mode
            return;
        }


    }
}
