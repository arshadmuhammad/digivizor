<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionSkuPolicy\Plugin;

use Magento\Tax\Model\Sales\Total\Quote\CommonTaxCollector;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Tax\Api\Data\TaxDetailsItemInterface;
use Magento\Store\Model\Store;
use Magento\Tax\Helper\Data as TaxHelper;
use Magento\Tax\Model\Config as TaxConfig;
use MageWorx\OptionBase\Helper\System as SystemHelper;

class AvoidSettingWrongCustomPriceForExcludedItem
{
    /**
     * @var TaxHelper
     */
    protected $taxHelper;

    /**
     * @var TaxConfig
     */
    protected $taxConfig;

    /**
     * @var SystemHelper
     */
    protected $systemHelper;

    /**
     * @param TaxHelper $taxHelper
     * @param TaxConfig $taxConfig
     * @param SystemHelper $systemHelper
     */
    public function __construct(
        TaxHelper $taxHelper,
        TaxConfig $taxConfig,
        SystemHelper $systemHelper
    ) {
        $this->taxHelper    = $taxHelper;
        $this->taxConfig    = $taxConfig;
        $this->systemHelper = $systemHelper;
    }

    /**
     * Update tax related fields for quote item
     * Extend MAGETWO-97423 fix: avoid setting wrong Custom Price for excluded by SKU Policy item and apply Magento fix only on admin order
     * @see https://github.com/magento/magento2/commit/7c6d41a07595e6fee7d416fe4921739d1e32e19e#diff-c77beda2cff635990682d095e4a650a1
     *
     * @param CommonTaxCollector $subject
     * @param \Closure $proceed
     * @param AbstractItem $quoteItem
     * @param TaxDetailsItemInterface $itemTaxDetails
     * @param TaxDetailsItemInterface $baseItemTaxDetails
     * @param Store $store
     *
     * @return CommonTaxCollector $subject
     */
    public function aroundUpdateItemTaxInfo(
        CommonTaxCollector $subject,
        \Closure $proceed,
        AbstractItem $quoteItem,
        TaxDetailsItemInterface $itemTaxDetails,
        TaxDetailsItemInterface $baseItemTaxDetails,
        Store $store
    ) {
        $quoteItem->setPrice($baseItemTaxDetails->getPrice());
        if ($quoteItem->getCustomPrice()
            && $this->taxHelper->applyTaxOnCustomPrice()
            && $this->systemHelper->isAdmin()
        ) {
            $quoteItem->setCustomPrice($baseItemTaxDetails->getPrice());
        }
        $quoteItem->setConvertedPrice($itemTaxDetails->getPrice());
        $quoteItem->setPriceInclTax($itemTaxDetails->getPriceInclTax());
        $quoteItem->setRowTotal($itemTaxDetails->getRowTotal());
        $quoteItem->setRowTotalInclTax($itemTaxDetails->getRowTotalInclTax());
        $quoteItem->setTaxAmount($itemTaxDetails->getRowTax());
        $quoteItem->setTaxPercent($itemTaxDetails->getTaxPercent());
        $quoteItem->setDiscountTaxCompensationAmount($itemTaxDetails->getDiscountTaxCompensationAmount());

        $quoteItem->setBasePrice($baseItemTaxDetails->getPrice());
        $quoteItem->setBasePriceInclTax($baseItemTaxDetails->getPriceInclTax());
        $quoteItem->setBaseRowTotal($baseItemTaxDetails->getRowTotal());
        $quoteItem->setBaseRowTotalInclTax($baseItemTaxDetails->getRowTotalInclTax());
        $quoteItem->setBaseTaxAmount($baseItemTaxDetails->getRowTax());
        $quoteItem->setTaxPercent($baseItemTaxDetails->getTaxPercent());
        $quoteItem->setBaseDiscountTaxCompensationAmount($baseItemTaxDetails->getDiscountTaxCompensationAmount());

        if ($this->taxConfig->discountTax($store)) {
            $quoteItem->setDiscountCalculationPrice($itemTaxDetails->getPriceInclTax());
            $quoteItem->setBaseDiscountCalculationPrice($baseItemTaxDetails->getPriceInclTax());
        } else {
            $quoteItem->setDiscountCalculationPrice($itemTaxDetails->getPrice());
            $quoteItem->setBaseDiscountCalculationPrice($baseItemTaxDetails->getPrice());
        }

        return $subject;
    }
}
