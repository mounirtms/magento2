<?php
namespace Mab\Tombola\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class TombolaPost extends Action
{
    protected $resultJsonFactory;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function execute()
    {
        $postData = $this->getRequest()->getPostValue();

        $clientName = isset($postData['client_name']) ? $postData['client_name'] : '';
        $joinTombola = isset($postData['join_tombola']) ? $postData['join_tombola'] : false;
        $quoteId = isset($postData['quote_id']) ? $postData['quote_id'] : null;
        $customerId = isset($postData['customer_id']) ? $postData['customer_id'] : null;

        // Handle the Tombola data (e.g., save to database or trigger custom action)
        // Example: You can insert into a custom table, or interact with Amasty Custom Form

        // You can return a success message here
        $result = ['status' => 'success', 'message' => 'Tombola data received'];

        $jsonResult = $this->resultJsonFactory->create();
        return $jsonResult->setData($result);
    }
}
