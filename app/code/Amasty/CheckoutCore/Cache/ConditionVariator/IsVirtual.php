<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/


namespace Amasty\CheckoutCore\Cache\ConditionVariator;

/**
 * Add cache variation for virtual quote.
 */
class IsVirtual implements \Amasty\CheckoutCore\Api\CacheKeyPartProviderInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    public function __construct(\Magento\Checkout\Model\Session $checkoutSession)
    {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @return string
     */
    public function getKeyPart()
    {
        if ($this->checkoutSession->getQuote()->isVirtual()) {
            return 'virtual';
        }

        return 'virtual=fls';
    }
}
