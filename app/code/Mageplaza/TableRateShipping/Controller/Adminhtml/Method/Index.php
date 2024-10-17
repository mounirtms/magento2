<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_TableRateShipping
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\TableRateShipping\Controller\Adminhtml\Method;

use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\Result\Redirect;
use Mageplaza\TableRateShipping\Controller\Adminhtml\AbstractMethod;

/**
 * Class Index
 * @package Mageplaza\TableRateShipping\Controller\Adminhtml\Method
 */
class Index extends AbstractMethod
{
    /**
     * @return Page|Redirect
     */
    public function execute()
    {
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Shipping Methods'));

        return $resultPage;
    }
}
