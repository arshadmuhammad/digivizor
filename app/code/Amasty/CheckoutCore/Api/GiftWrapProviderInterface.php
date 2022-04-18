<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

declare(strict_types=1);

namespace Amasty\CheckoutCore\Api;

interface GiftWrapProviderInterface
{
    /**
     * @return bool
     */
    public function isGiftWrapEnabled(): bool;

    /**
     * @return float
     */
    public function getGiftWrapFee(): float;
}
