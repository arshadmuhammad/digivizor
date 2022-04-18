<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/


namespace Amasty\CheckoutCore\Cache\ConditionVariator;

use Amasty\CheckoutCore\Api\CacheKeyPartProviderInterface;

/**
 * Add cache variation for each store ID
 */
class StoreId implements CacheKeyPartProviderInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    public function __construct(\Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    /**
     * @return string
     */
    public function getKeyPart()
    {
        return 'store=' . $this->storeManager->getStore()->getId();
    }
}
