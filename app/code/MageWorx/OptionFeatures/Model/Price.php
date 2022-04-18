<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionFeatures\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Model\AbstractModel;
use MageWorx\OptionBase\Helper\Data as BaseHelper;
use Magento\Framework\Event\ManagerInterface;

class Price extends DataObject
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var DataObject
     */
    protected $specialPriceModel;

    /**
     * @var DataObject
     */
    protected $tierPriceModel;

    /**
     * @var BaseHelper
     */
    protected $baseHelper;

    /**
     * Core event manager proxy
     *
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param DataObject $specialPriceModel
     * @param DataObject $tierPriceModel
     * @param BaseHelper $baseHelper
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        DataObject $specialPriceModel,
        DataObject $tierPriceModel,
        ManagerInterface $eventManager,
        BaseHelper $baseHelper
    ) {
        $this->productRepository = $productRepository;
        $this->specialPriceModel = $specialPriceModel;
        $this->tierPriceModel    = $tierPriceModel;
        $this->baseHelper        = $baseHelper;
        $this->eventManager      = $eventManager;
        parent::__construct();
    }

    /**
     * Get actual price using suitable special and tier prices
     *
     * @param \Magento\Catalog\Model\Product\Option $option
     * @param \Magento\Catalog\Model\Product\Option\Value $value
     * @return float|null
     */
    public function getPrice($option, $value)
    {
        if (!($this->specialPriceModel instanceof AbstractModel)
            || !($this->tierPriceModel instanceof AbstractModel)
        ) {
            return $value->getPrice(true);
        }

        $originalProduct = $option->getProduct();
        $infoBuyRequest  = $this->baseHelper->getInfoBuyRequest($originalProduct);

        $valueQty   = $this->getValueQty($option, $value, $infoBuyRequest);
        $productQty = $this->getProductQty();
        if (empty($productQty)) {
            $productQty = !empty($infoBuyRequest['qty']) ? $infoBuyRequest['qty'] : 1;
        }

        $originalProductOptions = $originalProduct->getData('options');
        foreach ($originalProductOptions as $originalProductOption) {
            $originalProductOptionValues = $originalProductOption->getValues();
            if (!empty($originalProductOptionValues[$value->getOptionTypeId()])) {
                $originalValue = $originalProductOptionValues[$value->getOptionTypeId()];
                break;
            }
        }
        if (empty($originalValue)) {
            return $value->getPrice(true);
        }

        $specialPrice         = $this->specialPriceModel->getActualSpecialPrice($originalValue);
        $tierPrices           = $this->tierPriceModel->getSuitableTierPrices($originalValue);
        $suitableTierPrice    = null;
        $suitableTierPriceQty = null;

        $isOneTime = $option->getData('one_time');
        if ($isOneTime) {
            $totalQty = $valueQty;
        } else {
            $totalQty = $productQty * $valueQty;
        }

        if (!isset($tierPrices[$totalQty])) {
            foreach ($tierPrices as $tierPriceItemQty => $tierPriceItem) {
                if ($suitableTierPriceQty < $tierPriceItemQty && $totalQty >= $tierPriceItemQty) {
                    $suitableTierPrice    = $tierPriceItem;
                    $suitableTierPriceQty = $tierPriceItemQty;
                }
            }
        } else {
            $suitableTierPrice = $tierPrices[$totalQty];
        }

        $actualTierPrice = isset($suitableTierPrice['price_incl_tax']) ? $suitableTierPrice['price_incl_tax'] : null;

        if ($suitableTierPrice && ($actualTierPrice < $specialPrice || $specialPrice === null)) {
            $price = $actualTierPrice;
        } elseif ($specialPrice !== null) {
            $price = $specialPrice;
        } else {
            if ($originalValue->getPriceType() == 'percent') {
                $productFinalPrice = $originalProduct->getPriceModel()->getBasePrice($originalProduct, $totalQty);
                $originalProduct->setFinalPrice($productFinalPrice);
                $this->eventManager->dispatch(
                    'catalog_product_get_final_price',
                    ['product' => $originalProduct, 'qty' => $totalQty]
                );
                $productFinalPrice = $originalProduct->getData('final_price');

                $price = $productFinalPrice * $originalValue->getPrice() / 100;
            } else {
                $price = $originalValue->getPrice();
            }
        }

        return $price;
    }

    /**
     * Get selected value qty
     *
     * @param \Magento\Catalog\Model\Product\Option $option
     * @param \Magento\Catalog\Model\Product\Option\Value $value
     * @param array $infoBuyRequest
     * @return float
     */
    protected function getValueQty($option, $value, $infoBuyRequest)
    {
        $valueQty = 1;
        if (!empty($infoBuyRequest['options_qty'][$option->getOptionId()][$value->getOptionTypeId()])) {
            $valueQty = $infoBuyRequest['options_qty'][$option->getOptionId()][$value->getOptionTypeId()];
        } elseif (!empty($infoBuyRequest['options_qty'][$option->getOptionId()])) {
            $valueQty = $infoBuyRequest['options_qty'][$option->getOptionId()];
        }
        return $valueQty;
    }
}
