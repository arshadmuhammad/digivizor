<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionFeatures\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductOptionInterface;
use MageWorx\OptionBase\Helper\Data as BaseHelper;
use MageWorx\OptionFeatures\Helper\Data as Helper;

class QtyMultiplier
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var BaseHelper
     */
    protected $baseHelper;

    /**
     * @var array
     */
    protected $buyRequest;

    /**
     * @param Helper $helper
     * @param BaseHelper $baseHelper
     */
    public function __construct(
        Helper $helper,
        BaseHelper $baseHelper
    ) {
        $this->helper     = $helper;
        $this->baseHelper = $baseHelper;
    }

    /**
     * Process buy request options to calculate qty_multiplier total qty
     *
     * @param array $options
     * @param array $buyRequest
     * @param ProductInterface $quoteProduct
     * @return int|float
     */
    public function getTotalQtyMultiplierQuantity(array $options, $buyRequest, $quoteProduct)
    {
        $this->buyRequest = $buyRequest;
        $productQty       = isset($buyRequest['qty']) ? $buyRequest['qty'] : 1;

        $qtyMultiplierTotalQty = 0;
        foreach ($options as $optionId => $values) {
            $option = $quoteProduct->getOptionById($optionId);
            if (!$option) {
                continue;
            }

            if (in_array($option->getType(), $this->baseHelper->getSelectableOptionTypes())) {
                $qtyMultiplierTotalQty += $this->getOptionQtyMultiplierQuantity(
                    $option,
                    $optionId,
                    $values,
                    $productQty
                );
            }
        }
        return $qtyMultiplierTotalQty;
    }

    /**
     * Get total qty_multiplier's qty from selected option values
     *
     * @param ProductOptionInterface|\Magento\Catalog\Model\Product\Option $option
     * @param int $optionId
     * @param array|string $values
     * @param float|int $productQty
     * @return float|int
     */
    protected function getOptionQtyMultiplierQuantity($option, $optionId, $values, $productQty)
    {
        $totalQty      = 0;
        $optionTypeIds = is_array($values) ? $values : explode(',', $values);
        $isOneTime     = $option->getOneTime();

        foreach ($optionTypeIds as $index => $optionTypeId) {
            if (!$optionTypeId) {
                continue;
            }
            $value = $option->getValueById($optionTypeId);

            if (!$value) {
                continue;
            }

            $qtyMultiplier = $value->getQtyMultiplier();

            $optionQty = $this->helper->getOptionQty($this->buyRequest, $optionId, $optionTypeId);
            $totalQty  += $isOneTime
                ? $optionQty * $qtyMultiplier
                : $optionQty * $qtyMultiplier * $productQty;
        }

        return $totalQty;
    }
}
