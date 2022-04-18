<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionFeatures\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\CatalogInventory\Model\ResourceModel\Stock as StockResource;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface;
use MageWorx\OptionBase\Helper\Data as BaseHelper;

class SubtractQtyMultiplierQty implements ObserverInterface
{
    /**
     * @var StockResource
     */
    protected $stockResource;

    /**
     * @var StockConfigurationInterface
     */
    protected $stockConfiguration;

    /**
     * @var StockRegistryProviderInterface
     */
    protected $stockRegistryProvider;

    /**
     * @var BaseHelper
     */
    protected $baseHelper;

    /**
     * @param StockResource $stockResource
     * @param StockConfigurationInterface $stockConfiguration
     * @param StockRegistryProviderInterface $stockRegistryProvider
     * @param BaseHelper $baseHelper
     */
    public function __construct(
        BaseHelper $baseHelper,
        StockResource $stockResource,
        StockRegistryProviderInterface $stockRegistryProvider,
        StockConfigurationInterface $stockConfiguration
    ) {
        $this->baseHelper            = $baseHelper;
        $this->stockResource         = $stockResource;
        $this->stockRegistryProvider = $stockRegistryProvider;
        $this->stockConfiguration    = $stockConfiguration;
    }

    /**
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote     = $observer->getEvent()->getQuote();
        $websiteId = $this->stockConfiguration->getDefaultScopeId();

        /** @var \Magento\Quote\Model\Quote\Item $quoteItem */
        $quoteItems = $quote->getAllItems();
        foreach ($quoteItems as $quoteItem) {
            $infoBuyRequest        = $quoteItem->getBuyRequest();
            $qtyMultiplierTotalQty = $infoBuyRequest->getData('qty_multiplier_qty');
            $originalQty           = $infoBuyRequest->getData('original_qty');
            $productId             = $quoteItem->getProduct()->getId();

            $stockItem = $this->stockRegistryProvider->getStockItem(
                $quoteItem->getProduct()->getData($this->baseHelper->getLinkField()),
                $websiteId
            );

            if (!$qtyMultiplierTotalQty || !$stockItem->getManageStock()) {
                continue;
            }

            $this->stockResource->correctItemsQty(
                [
                    $productId => $qtyMultiplierTotalQty - $originalQty
                ],
                $websiteId,
                '-'
            );
        }

        return $this;
    }
}
