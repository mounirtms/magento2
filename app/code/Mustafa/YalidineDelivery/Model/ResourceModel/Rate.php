<?php

namespace Mustafa\YalidineDelivery\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Rate extends AbstractDb
{
    /**
     * @var string
     */
    protected $_mainTable = 'amasty_table_rate';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('amasty_table_rate', 'id');
    }
    


}