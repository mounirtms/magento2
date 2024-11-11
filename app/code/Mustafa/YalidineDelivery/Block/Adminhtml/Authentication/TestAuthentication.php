<?php

namespace Mustafa\YalidineDelivery\Block\Adminhtml\Authentication;

class TestAuthentication extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var string
     */
    protected $_template = 'Mustafa_YalidineDelivery::authentication/test_authentication.phtml';

    /**
     * Return element html
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        unset($element);
        return $this->toHtml();
    }

    /**
     * Return ajax url for test authentication button
     *
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->getUrl('mustafa_yalidinedelivery/authentication/testAuthentication');
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()
            ->createBlock(\Magento\Backend\Block\Widget\Button::class)
            ->setData([
                'id'    => 'yalidine_authentication_test',
                'label' => __('Test Authentication'),
            ]);

        return $button->toHtml();
    }
}
