<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionBase\Model\Config;

use Magento\Framework\DataObjectFactory;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\App\RequestInterface;
use MageWorx\OptionBase\Model\Product\Option\Attributes as OptionAttributes;
use MageWorx\OptionBase\Model\Product\Option\Value\Attributes as OptionValueAttributes;
use MageWorx\OptionBase\Helper\Data as BaseHelper;
use MageWorx\OptionBase\Helper\Price as BasePriceHelper;

class Base
{
    /**
     * @var DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var \Magento\Framework\Locale\Format
     */
    protected $localeFormat;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var OptionAttributes
     */
    protected $optionAttributes;

    /**
     * @var OptionValueAttributes
     */
    protected $optionValueAttributes;

    /**
     * @var BaseHelper
     */
    protected $baseHelper;

    /**
     * @var BasePriceHelper
     */
    protected $basePriceHelper;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricingHelper;

    /**
     * @var \Magento\Catalog\Helper\Data
     */
    protected $catalogData;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * Necessary for frontend operations product data keys
     *
     * @var array
     */
    protected $productKeys = [
        'absolute_price',
        'type_id'
    ];

    /**
     * @param RequestInterface $request
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param DataObjectFactory $dataObjectFactory
     * @param \Magento\Framework\Locale\Format $localeFormat
     * @param PriceCurrencyInterface $priceCurrency
     * @param OptionAttributes $optionAttributes
     * @param OptionValueAttributes $optionValueAttributes
     * @param BaseHelper $baseHelper
     * @param BasePriceHelper $basePriceHelper
     */
    public function __construct(
        RequestInterface $request,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        DataObjectFactory $dataObjectFactory,
        \Magento\Framework\Locale\Format $localeFormat,
        PriceCurrencyInterface $priceCurrency,
        OptionAttributes $optionAttributes,
        OptionValueAttributes $optionValueAttributes,
        BaseHelper $baseHelper,
        BasePriceHelper $basePriceHelper
    ) {
        $this->request               = $request;
        $this->eventManager          = $eventManager;
        $this->pricingHelper         = $pricingHelper;
        $this->catalogData           = $catalogData;
        $this->dataObjectFactory     = $dataObjectFactory;
        $this->localeFormat          = $localeFormat;
        $this->priceCurrency         = $priceCurrency;
        $this->optionAttributes      = $optionAttributes;
        $this->optionValueAttributes = $optionValueAttributes;
        $this->baseHelper            = $baseHelper;
        $this->basePriceHelper       = $basePriceHelper;
    }

    /**
     * Get json representation of
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getJsonConfig($product)
    {
        $config = [];
        foreach ($product->getOptions() as $option) {
            /* @var $option \Magento\Catalog\Model\Product\Option */
            if ($option->hasValues()) {
                $tmpPriceValues = [];
                foreach ($option->getValues() as $valueId => $value) {
                    $tmpPriceValues[$valueId] = $this->getPriceConfiguration($value);
                }
                $priceValue = $tmpPriceValues;
            } else {
                $priceValue = $this->getPriceConfiguration($option);
            }
            $config[$option->getId()] = $priceValue;
        }

        $configObj = $this->dataObjectFactory->create();
        $configObj->setData('config', $config);

        //pass the return array encapsulated in an object for the other modules to be able to alter it eg: weee
        $this->eventManager->dispatch('catalog_product_option_price_configuration_after', ['configObj' => $configObj]);

        $config = $configObj->getConfig();

        return $this->baseHelper->jsonEncode($config);
    }

    /**
     * Get price configuration
     *
     * @param \Magento\Catalog\Model\Product\Option\Value|\Magento\Catalog\Model\Product\Option $option
     * @return array
     */
    protected function getPriceConfiguration($option)
    {
        $optionPrice = $option->getPrice(true);
        if ($option->getPriceType() !== \Magento\Catalog\Model\Product\Option\Value::TYPE_PERCENT) {
            $optionPrice = $this->pricingHelper->currency($optionPrice, false, false);
        }
        $data = [
            'prices' => [
                'oldPrice'   => [
                    'amount'      => $this->pricingHelper->currency($option->getRegularPrice(), false, false),
                    'adjustments' => [],
                ],
                'basePrice'  => [
                    'amount' => $this->catalogData->getTaxPrice(
                        $option->getProduct(),
                        $optionPrice,
                        false,
                        null,
                        null,
                        null,
                        null,
                        null,
                        false
                    ),
                ],
                'finalPrice' => [
                    'amount' => $this->catalogData->getTaxPrice(
                        $option->getProduct(),
                        $optionPrice,
                        true,
                        null,
                        null,
                        null,
                        null,
                        null,
                        false
                    ),
                ],
            ],
            'type'   => $option->getPriceType(),
            'name'   => $option->getTitle(),
        ];
        return $data;
    }

    /**
     * Get system data
     *
     * @param string $area
     * @return string (JSON)
     */
    public function getSystemJsonConfig($area)
    {
        $action = '';
        $router = '';
        if ($this->request->getRouteName() == 'checkout') {
            $router = 'checkout';
        }
        if (($this->request->getRouteName() == 'sales' && $this->request->getControllerName() == 'order_create')
            || ($this->request->getFullActionName() == 'mageworx_optionbase_config_get')
        ) {
            $router = 'admin_order_create';
            $action = $this->request->getActionName();
        }

        $data = [
            'area'   => $area == '' ? 'frontend' : $area,
            'router' => $router,
            'action' => $action
        ];

        return $this->baseHelper->jsonEncode($data);
    }

    /**
     * Get necessary for frontend product data
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string (JSON)
     */
    public function getProductJsonConfig($product)
    {
        $productData   = $product->getData();
        $processedData = [];

        foreach ($this->productKeys as $key) {
            if (isset($productData[$key])) {
                $processedData[$key] = $productData[$key];
            }
        }

        $processedData['extended_tier_prices']   = $this->getExtendedTierPricesConfig($product);
        $processedData['regular_price_excl_tax'] = $this->priceCurrency->convert(
            $this->getProductRegularPrice($product, false)
        );
        $processedData['regular_price_incl_tax'] = $this->priceCurrency->convert(
            $this->getProductRegularPrice($product, true)
        );
        $processedData['final_price_excl_tax']   = $this->priceCurrency->convert(
            $this->getProductFinalPrice($product, false)
        );
        $processedData['final_price_incl_tax']   = $this->priceCurrency->convert(
            $this->getProductFinalPrice($product, true)
        );

        if (!empty($productData['price'])) {
            $processedData['price'] = $this->priceCurrency->convert($productData['price']);
        }

        return $this->baseHelper->jsonEncode($processedData);
    }

    /**
     * Get product's tier price config for frontend calculations
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    protected function getExtendedTierPricesConfig($product)
    {
        $tierPrices     = [];
        $tierPricesList = $product->getPriceInfo()->getPrice('tier_price')->getTierPriceList();
        foreach ($tierPricesList as $tierPriceItem) {
            $tierPrices[] = [
                'price_excl_tax' => $this->priceCurrency->convert(
                    $this->getProductFinalPrice($product, false, $tierPriceItem['price_qty'])
                ),
                'price_incl_tax' => $this->priceCurrency->convert(
                    $this->getProductFinalPrice($product, true, $tierPriceItem['price_qty'])
                ),
                'qty'            => $tierPriceItem['price_qty']
            ];
        }
        return $tierPrices;
    }

    /**
     * Get locale price format
     *
     * @return string (JSON)
     */
    public function getLocalePriceFormat()
    {
        $data                = $this->localeFormat->getPriceFormat();
        $data['priceSymbol'] = $this->priceCurrency->getCurrency()->getCurrencySymbol();

        return $this->baseHelper->jsonEncode($data);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param bool|null $includeTax
     * @param int $qty
     * @return float
     */
    public function getProductFinalPrice($product, $includeTax = null, $qty = 1)
    {
        $finalPrice = $product
            ->getPriceModel()
            ->getBasePrice($product, $qty);
        return $this->basePriceHelper->getTaxPrice($product, min($finalPrice, $product->getFinalPrice()), $includeTax);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param null $includeTax
     * @return float
     */
    public function getProductRegularPrice($product, $includeTax = null)
    {
        return $this->basePriceHelper->getTaxPrice($product, $product->getPrice(), $includeTax);
    }

    /**
     * Get type of price display from the tax config
     * Returns 1 - without tax, 2 - with tax, 3 - both
     *
     * @return integer
     */
    public function getPriceDisplayMode()
    {
        return $this->basePriceHelper->getPriceDisplayMode();
    }

    /**
     * Get flag: is catalog price already contains tax
     *
     * @return int
     */
    public function getCatalogPriceContainsTax()
    {
        return $this->basePriceHelper->getCatalogPriceContainsTax();
    }

    /**
     * Get Product ID
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return int
     */
    public function getProductId($product)
    {
        return $product->getData($this->baseHelper->getLinkField());
    }

    /**
     * Store options data in another config,
     * because if we add options data to the main config it generates fatal errors
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string {JSON}
     */
    public function getExtendedOptionsConfig($product)
    {
        $config                = [];
        $optionAttributes      = $this->optionAttributes->getData();
        $optionValueAttributes = $this->optionValueAttributes->getData();
        /** @var \Magento\Catalog\Model\Product\Option $option */
        if (empty($product->getOptions())) {
            return $this->baseHelper->jsonEncode($config);
        }
        foreach ($product->getOptions() as $option) {
            foreach ($optionAttributes as $optionAttribute) {
                $preparedData = $optionAttribute->prepareDataForFrontend($option);
                if (empty($preparedData) || !is_array($preparedData)) {
                    continue;
                }
                foreach ($preparedData as $preparedDataKey => $preparedDataValue) {
                    $config[$option->getId()][$preparedDataKey] = $preparedDataValue;
                }
            }
            /** @var \Magento\Catalog\Model\Product\Option\Value $value */
            if (empty($option->getValues())) {
                $config[$option->getId()]['price_type'] = $option->getPriceType();
                $config[$option->getId()]['price']      = $option->getPrice(false);
                continue;
            }
            foreach ($option->getValues() as $value) {
                foreach ($optionValueAttributes as $optionValueAttribute) {
                    $preparedData = $optionValueAttribute->prepareDataForFrontend($value);
                    if (empty($preparedData) || !is_array($preparedData)) {
                        continue;
                    }
                    foreach ($preparedData as $preparedDataKey => $preparedDataValue) {
                        $config[$option->getId()]['values'][$value->getId()][$preparedDataKey] = $preparedDataValue;
                    }
                }

                $config[$option->getId()]['values'][$value->getId()]['title']      = $value->getTitle();
                $config[$option->getId()]['values'][$value->getId()]['price_type'] = $value->getPriceType();
                $config[$option->getId()]['values'][$value->getId()]['price']      = $value->getPrice(false);
            }
        }

        return $this->baseHelper->jsonEncode($config);
    }
}
