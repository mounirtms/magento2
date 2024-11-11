<?php

namespace Mustafa\YalidineDelivery\Model\Api;

use Mustafa\YalidineDelivery\Model\RateFactory;
use Mustafa\YalidineDelivery\Model\Method;
use Magento\Framework\Exception\LocalizedException;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as RegionCollectionFactory;

class RateManager
{
  
    /**
     * @var RateFactory
     */
    private $rateFactory;
    private $method;
    private $regionCollectionFactory;

    public function __construct(
        RateFactory $rateFactory,
        Method $method,
        RegionCollectionFactory $regionCollectionFactory
    )
    {
        $this->rateFactory = $rateFactory;
        $this->method = $method;
        $this->regionCollectionFactory = $regionCollectionFactory;
    }
    
    /**
     * Save data array to database table.
     *
     * @param array $data
     * @return Data
     * @throws \Exception
     */
    public function saveData($storeId, $type, $title, $maxPrice, $maxWeight, array $data)
    {
        if (empty($data)) {
            throw new LocalizedException(__('Data array is empty.'));
        }
        if($type=='home'){
            $title = $title ?: "Yalidine à domicile";
        }else{
            $title = $title ?: "Yalidine Stop Desk";
        }
              
        $methodData = [
            'storeId' => (int)$storeId,
            'name' => $title,
            'is_active' => 1,
            'select_rate' => 0,
            'weight_type' => 3,
        ];
        
        try {
            $method_id = $this->method->getOrCreateMethodId($methodData);
                
            $rates = $data['data'];
        
            foreach ($rates as $rateData){
              /** @var \Vendor\Delivery\Model\Rate $rateModel */
              // Try to load existing rate
              $rateData['method_id'] = $method_id;
              $rateData['state'] = $this->getRegionIdByWilayaCode($rateData['wilaya_id']);
              $rateModel = $this->rateFactory->create()->loadByUniqueFields($rateData);
              // If rate doesn't exist, create a new one
              if (!$rateModel->getId()) { 
                $rateModel = $this->rateFactory->create();
              }
              // Update rate data and save
              if($type=='home'){
                  $rate = $rateData['home_fee'];
              }else{
                  $rate = $rateData['desk_fee'];
              }
                            
              $rateModel->setData([
                    'id' => $rateModel->getId(), 
                    'method_id' => $method_id,
                    'country' => 'DZ',
                    'state' => $rateData['state'],
                    'zip_from' => '',
                    'zip_to' => '',
                    'price_from' => 0, 
                    'price_to' => (int)$maxPrice ?: 99999,
                    'weight_from' => 0,
                    'weight_to' => (int)$maxWeight ?: 99999,
                    'qty_from' => 0,
                    'qty_to' => 9999,
                    'shipping_type' => 0,
                    'cost_base' => $rate,
                    'cost_percent' => 0,
                    'cost_product' => 0,
                    'cost_weight' => 0,
                    'start_weight' => 0,
                    'city' => '',
                    'name_delivery' => $rateData['wilaya_name'], 
                    'unit_weight_conversion' => 1,
                    'weight_rounding' => 0
                ]); 
              $rateModel->save();
            }
        }catch (\Exception $e) {
            throw new LocalizedException(__('An error occurred while saving rates: ' . $e->getMessage()));
        }
        
        return true;
    }
    
    private function getRegionIdByWilayaCode(int $wilayaCode): ?int
    {
        $regionCollection = $this->regionCollectionFactory->create();
        $regionCollection->addFieldToFilter('code', $wilayaCode);

        $region = $regionCollection->getFirstItem();

        return (int)$region->getId();
    } 
}