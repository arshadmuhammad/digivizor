<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

namespace Amasty\CheckoutCore\Api;

use Amasty\CheckoutCore\Api\Data\FeeInterface;

interface FeeRepositoryInterface
{
    /**
     * @param FeeInterface $fee
     * @return FeeInterface
     */
    public function save(FeeInterface $fee);

    /**
     * @param int $feeId
     * @return FeeInterface
     */
    public function getById($feeId);

    /**
     * @param Data\FeeInterface $fee
     * @return bool true on success
     */
    public function delete(FeeInterface $fee);

    /**
     * @param int $feeId
     * @return bool true on success
     */
    public function deleteById($feeId);

    /**
     * @param int $quoteId
     * @return FeeInterface
     */
    public function getByQuoteId($quoteId);

    /**
     * @param int $orderId
     * @return FeeInterface
     */
    public function getByOrderId($orderId);
}
