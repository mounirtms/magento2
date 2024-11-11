<?php

namespace Mustafa\SessionCleaner\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage ->getConfig()->getTitle()->prepend(__("Hello")); //Page title
        // Set the Template to be rendered
        $layout=$resultPage->getLayout();
        $block = $layout->createBlock('Magento\Framework\View\Element\Template') ->setTemplate('Mustafa_SessionCleaner::sessioncleaner/index.phtml')->toHtml();
        if($layout->getBlock('content')){
          $layout-> getBlock('content')->append($block);
        }
        
        return $resultPage;
    }
}
