<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
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

namespace Mageplaza\TableRateShipping\Model\Source;

/**
 * Class Status
 * @package Mageplaza\TableRateShipping\Model\Source
 */
class Status extends AbstractSource
{
    const DISABLE = 0;
    const ENABLE  = 1;

    /**
     * @return array
     */
    public static function getOptionArray()
    {
        return [
            self::DISABLE => __('Disable'),
            self::ENABLE  => __('Enable'),
        ];
    }
}
