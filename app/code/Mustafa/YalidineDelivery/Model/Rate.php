<?php 
namespace Mustafa\YalidineDelivery\Model;

use Magento\Framework\Model\AbstractModel;
use Mustafa\YalidineDelivery\Model\ResourceModel\Rate as RateResourceModel;
use Mustafa\YalidineDelivery\Model\ResourceModel\Rate\Collection;


class Rate extends AbstractModel
{
    
    protected function _construct(
        
    )
    {
        $this->_init(RateResourceModel::class); 
        
    }
    
    /**
     * Load rate by unique fields combination
     * 
     * @param array $data Rate data to search for
     * @return \YourModule\YourModel\Model\Rate
     */
    public function loadByUniqueFields(array $data) 
    {      
        $collection = $this->getCollection()
            ->addFieldToFilter('method_id', $data['method_id'])
            ->addFieldToFilter('state', $data['state']);

        return $collection->getFirstItem();
    } 
}