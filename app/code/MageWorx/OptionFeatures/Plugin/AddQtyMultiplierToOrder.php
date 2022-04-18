<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionFeatures\Plugin;

use MageWorx\OptionBase\Helper\Data as BaseHelper;
use MageWorx\OptionFeatures\Model\QtyMultiplier;

class AddQtyMultiplierToOrder
{
    /**
     * @var BaseHelper
     */
    protected $baseHelper;

    /**
     * @var QtyMultiplier
     */
    protected $qtyMultiplier;

    /**
     * @param BaseHelper $baseHelper
     * @param QtyMultiplier $qtyMultiplier
     */
    public function __construct(
        BaseHelper $baseHelper,
        QtyMultiplier $qtyMultiplier
    ) {
        $this->baseHelper    = $baseHelper;
        $this->qtyMultiplier = $qtyMultiplier;
    }

    /**
     * Add qty_multiplier data to info_buyRequest of quote before order placement
     *
     * @param \Magento\Quote\Model\QuoteManagement $subject
     * @param \Magento\Quote\Model\Quote $quote
     * @param array $orderData
     * @return \Magento\Framework\Model\AbstractExtensibleModel|\Magento\Sales\Api\Data\OrderInterface|object|null
     */
    public function beforeSubmit($subject, $quote, $orderData = [])
    {
        if ($quote->getAllVisibleItems() && !$quote->getIsQtyMultiplierApplied()) {
            $quoteItems = $quote->getAllItems();
            /** @var \Magento\Quote\Model\Quote\Item $quoteItem */
            foreach ($quoteItems as $quoteItem) {
                $buyRequest = $quoteItem->getBuyRequest();
                /** @var array $options */
                $options = $buyRequest->getOptions();
                if (!$options || !is_array($options)) {
                    continue;
                }

                $qtyMultiplierTotalQty = $this->qtyMultiplier->getTotalQtyMultiplierQuantity(
                    $options,
                    $buyRequest->toArray(),
                    $quoteItem->getProduct()
                );
                if (!$qtyMultiplierTotalQty) {
                    continue;
                }

                $infoBuyRequest = $quoteItem->getOptionByCode('info_buyRequest');
                $buyRequest->setData('qty_multiplier_qty', $qtyMultiplierTotalQty);
                $infoBuyRequest->setValue($this->baseHelper->encodeBuyRequestValue($buyRequest->getData()));
                $quoteItem->addOption($infoBuyRequest);
            }


            $quote->setIsQtyMultiplierApplied(true);
        }

        return [$quote, $orderData];
    }
}
