<?php

namespace Mustafa\YalidineDelivery\Model\Cache;

class Api extends \Magento\Framework\Cache\Frontend\Decorator\TagScope
{
    const TYPE_IDENTIFIER = 'yalidine_shipping';
    const CACHE_TAG = 'YALIDINE_SHIPPING';

    /**
     * Api constructor.
     * @param \Magento\Framework\App\Cache\Type\FrontendPool $cacheFrontendPool
     */
    public function __construct(\Magento\Framework\App\Cache\Type\FrontendPool $cacheFrontendPool)
    {
        parent::__construct($cacheFrontendPool->get(self::TYPE_IDENTIFIER), self::CACHE_TAG);
    }

    public function createKey($storeId, $method, $params = [])
    {
        foreach ($params as $key => $param) {
            $params[$key] = base64_encode($param);
        }
        return $storeId . '_' . $method . ':' . implode('_', $params);
    }
}
