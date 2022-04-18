<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/


namespace Amasty\CheckoutCore\Model\ResourceModel\QuoteCustomFields;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Amasty\CheckoutCore\Api\Data\QuoteCustomFieldsInterface;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \Amasty\CheckoutCore\Model\QuoteCustomFields::class,
            \Amasty\CheckoutCore\Model\ResourceModel\QuoteCustomFields::class
        );
    }

    /**
     * @param int $quoteId
     * @param string $customFieldIndex
     *
     * @return Collection
     */
    public function addFilterByQuoteIdAndCustomField($quoteId, $customFieldIndex)
    {
        return $this->addFieldByQuoteId($quoteId)
        ->addFieldToFilter(QuoteCustomFieldsInterface::NAME, $customFieldIndex);
    }

    /**
     * @param int $quoteId
     *
     * @return Collection
     */
    public function addFieldByQuoteId($quoteId)
    {
        return $this->addFieldToFilter(QuoteCustomFieldsInterface::QUOTE_ID, $quoteId);
    }
}
