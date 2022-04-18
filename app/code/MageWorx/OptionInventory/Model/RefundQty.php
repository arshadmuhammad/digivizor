<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionInventory\Model;

use \Magento\Catalog\Model\ResourceModel\Product\Option\Value\CollectionFactory as ValueCollection;

/**
 * Class RefundQty. Refund option values qty when order is cancel or credit memo.
 */
class RefundQty
{
    /**
     * @var ValueCollection
     */
    protected $valueCollection;

    /**
     * RefundQty constructor.
     *
     * @param ValueCollection $valueCollection
     */
    public function __construct(
        ValueCollection $valueCollection
    ) {
        $this->valueCollection = $valueCollection;
    }

    /**
     * Refund qty when order is cancele or credit memo.
     * Walk through the all order $items, find count qty to refund by the $qtyFieldName
     * and refund it for all option values in this order.
     *
     * @param array $items
     * @param string $qtyFieldName
     * @return $this
     */
    public function refund($items, $qtyFieldName)
    {
        foreach ($items as $item) {
            $itemData       = $item->getData();
            $infoBuyRequest = $itemData['product_options']['info_buyRequest'];

            if (!isset($infoBuyRequest['options'])) {
                continue;
            }

            $orderItemQtyReturned = $itemData[$qtyFieldName];
            $itemOptions          = $infoBuyRequest['options'];

            $valueIds = [];
            foreach ($itemOptions as $optionId => $value) {
                if (is_array($value)) {
                    foreach ($value as $valueId) {
                        $valueIds[] = $valueId;
                    }
                } else {
                    $valueIds[] = $value;
                }
            }

            $valuesCollection = $this->valueCollection
                ->create()
                ->getValuesByOption($valueIds)
                ->load();

            if (!$valuesCollection->getSize()) {
                continue;
            }

            foreach ($valueIds as $valueId) {
                $valueModel = $valuesCollection->getItemById($valueId);

                if (!$valueModel) {
                    continue;
                }

                if (!$valueModel->getManageStock()) {
                    continue;
                }

                $totalQtyReturned = $this->getTotalQtyReturned($valueModel, $infoBuyRequest, $orderItemQtyReturned);

                $valueModel->setQty($valueModel->getQty() + $totalQtyReturned);
            }

            $valuesCollection->save();
        }

        return $this;
    }

    /**
     * Calculates and return total qty considering QtyInput of order item
     *
     * @param \Magento\Framework\DataObject $valueModel
     * @param array $infoBuyRequest
     * @param int $orderItemQtyReturned
     * @return int
     */
    public function getTotalQtyReturned($valueModel, $infoBuyRequest, $orderItemQtyReturned)
    {
        $optionId = $valueModel->getOptionId();
        $valueId  = $valueModel->getOptionTypeId();
        if (empty($infoBuyRequest['options_qty']) || empty($infoBuyRequest['options_qty'][$optionId])) {
            return $orderItemQtyReturned;
        }

        $valueQty = 1;
        if (!empty($infoBuyRequest['options_qty'][$optionId][$valueId])) {
            $valueQty = $infoBuyRequest['options_qty'][$optionId][$valueId];
        } elseif (!is_array($infoBuyRequest['options_qty'][$optionId])) {
            $valueQty = $infoBuyRequest['options_qty'][$optionId];
        }
        return $valueQty * $orderItemQtyReturned;
    }
}
