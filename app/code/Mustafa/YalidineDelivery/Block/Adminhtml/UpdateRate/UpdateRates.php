<?php

namespace Mustafa\YalidineDelivery\Block\Adminhtml\UpdateRate;

class UpdateRates extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var string
     */
    protected $_template = 'Mustafa_YalidineDelivery::rate/update_rates.phtml';

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
        return $this->getUrl('mustafa_yalidinedelivery/system/updaterates');
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
                'id'    => 'yalidine_update_rates',
                'label' => __('Update Rates'),
            ]);

        return $button->toHtml();
    }
}