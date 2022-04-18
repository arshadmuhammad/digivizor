<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/


namespace Amasty\CheckoutCore\Model\ResourceModel\QuotePasswords;

use Amasty\CheckoutCore\Model\QuotePasswords;
use Amasty\CheckoutCore\Model\ResourceModel\QuotePasswords as ResourceQuotePasswords;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(QuotePasswords::class, ResourceQuotePasswords::class);
    }
}
