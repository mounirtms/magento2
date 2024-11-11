<?php
namespace Mustafa\YalidineDelivery\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException; // Add for exceptions

class ShipmentSaveBefore implements ObserverInterface
{
    protected $helper; 
    protected $scopeConfig; // To get your specific shipping method code

    public function __construct(
        \Mustafa\YalidineDelivery\Helper\Data $helper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig // Inject ScopeConfigInterface
    ) {
        $this->helper = $helper;
        $this->scopeConfig = $scopeConfig;
    }

    public function execute(Observer $observer)
    {
        $shipment = $observer->getEvent()->getShipment();
        $order = $shipment->getOrder();

        // 1. Check for Your Specific Shipping Method
        $specificShippingMethodCode = $this->scopeConfig->getValue(
            'carriers/yalidine/title', // Path to your shipping method's code in config
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if ($order->getShippingMethod() === $specificShippingMethodCode) {
            $shipmentData = [
                'order_id' => $shipment->getOrderId(),
                'tracking_number' => $shipment->getTrackingNumber(),
                // ... other data
            ];

            try {
                $success = $this->helper->sendShipmentData($shipmentData);

                // 2. Handle Delivery Partner Errors
                if (!$success) {
                    // Partner API indicated an error
                    throw new LocalizedException(__('An error occurred while creating the delivery order. Please try again.')); 
                }

            } catch (\Exception $e) {
                // Log the error for debugging: $this->logger->error($e->getMessage());
                throw new LocalizedException(__('An error occurred during shipment processing.')); // User-friendly message
            }
        }
    }
}