<?php

namespace Mustafa\YalidineDelivery\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;


class Data extends AbstractHelper
{
    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     */
    private $configWriter;
    private $request;
     
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        WriterInterface $configWriter,
        )
    {
        parent::__construct($context);
        $this->configWriter = $configWriter;
    }

    /**
     * @param $configPath
     * @param null $storeId
     * @param null $scope
     * @return mixed
     */
    public function getConfigData($configPath, $scope , $scopeId)
    {
        return $this->scopeConfig->getValue('carriers/yalidine/' . $configPath, $scope, $scopeId);

    }
    
    public function updateConfigData($configPath, $value, $scope, $scopeId): void
    {
        $this->configWriter->save(
            'carriers/yalidine/' . $configPath,
            $value,
            $scope,
            $scopeId
        );
    }
    
}
