<?php
namespace Mab\Techno\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;

class HeaderButton extends Template
{
    // Inject the required context to render the button
    public function __construct(Context $context, array $data = [])
    {
        parent::__construct($context, $data);
    }

    // Get the URL for opening the dashboard in a new window
    public function getOpenDashboardUrl()
    {
        return $this->getUrl('techno/adminhtml_dashboard/open'); // Define the URL for the controller action
    }
}
