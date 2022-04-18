<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/


namespace Amasty\CheckoutCore\Api;

/**
 * Cache variator interface.
 * Return cache key/identifier part.
 * @since 3.0.0
 */
interface CacheKeyPartProviderInterface
{
    /**
     * @return string
     */
    public function getKeyPart();
}
