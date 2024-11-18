<?php
namespace Mab\Techno\Controller\Adminhtml\Dashboard;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

class Open extends Action
{
    protected $resultPageFactory;

    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        // You can process data here if needed
        return $this->resultPageFactory->create();
    }

    protected function _isAllowed()
    {
        // Check if the admin user has permission to access this page
        return $this->_authorization->isAllowed('Mab_Techno::dashboard');
    }
}
