<?php

namespace Mustafa\YalidineDelivery\Model\ResourceModel\Rate;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mustafa\YalidineDelivery\Model\Rate;
use Mustafa\YalidineDelivery\Model\ResourceModel\Rate as RateResourceModel; 

class Collection extends AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Rate::class, RateResourceModel::class);
    }
}