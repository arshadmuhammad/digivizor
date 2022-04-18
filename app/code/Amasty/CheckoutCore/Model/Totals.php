<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/


namespace Amasty\CheckoutCore\Model;

use Magento\Framework\DataObject;
use Amasty\CheckoutCore\Api\Data\TotalsInterface;

class Totals extends DataObject implements TotalsInterface
{
    /**
     * @inheritdoc
     */
    public function getTotals()
    {
        return $this->getData(self::TOTALS);
    }

    /**
     * @inheritdoc
     */
    public function getShipping()
    {
        return $this->getData(self::SHIPPING);
    }

    /**
     * @inheritdoc
     */
    public function getPayment()
    {
        return $this->getData(self::PAYMENT);
    }
}
