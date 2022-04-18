<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionAdvancedPricing\Plugin;

use MageWorx\OptionAdvancedPricing\Helper\Data as Helper;
use MageWorx\OptionAdvancedPricing\Model\SpecialPrice as SpecialPriceModel;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use MageWorx\OptionBase\Helper\Price as BasePriceHelper;
use Magento\Framework\Json\DecoderInterface;

class ExtendPriceConfig extends \Magento\Catalog\Block\Product\View\Options
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var SpecialPriceModel
     */
    protected $specialPriceModel;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var BasePriceHelper
     */
    protected $basePriceHelper;

    /**
     * @var DecoderInterface
     */
    protected $jsonDecoder;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Catalog\Model\Product\Option $option
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Stdlib\ArrayUtils $arrayUtils
     * @param Helper $helper
     * @param SpecialPriceModel $specialPriceModel
     * @param PriceCurrencyInterface $priceCurrency
     * @param BasePriceHelper $basePriceHelper
     * @param DecoderInterface $jsonDecoder
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Catalog\Model\Product\Option $option,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        Helper $helper,
        SpecialPriceModel $specialPriceModel,
        PriceCurrencyInterface $priceCurrency,
        BasePriceHelper $basePriceHelper,
        DecoderInterface $jsonDecoder,
        array $data = []
    ) {
        $this->helper            = $helper;
        $this->specialPriceModel = $specialPriceModel;
        $this->priceCurrency     = $priceCurrency;
        $this->basePriceHelper   = $basePriceHelper;
        $this->jsonDecoder       = $jsonDecoder;
        parent::__construct($context, $pricingHelper, $catalogData, $jsonEncoder, $option, $registry, $arrayUtils);
    }

    /**
     * Extend price config with suitable special price on frontend
     *
     * @param \Magento\Catalog\Model\Product\Type\Price $subject
     * @param callable $proceed
     * @return mixed
     */
    public function aroundGetJsonConfig($subject, $proceed)
    {
        if (!$subject->hasOptions()) {
            return $proceed();
        }

        if (!$this->helper->isSpecialPriceEnabled()) {
            return $proceed();
        }

        $defaultConfig  = $this->jsonDecoder->decode($proceed());
        $extendedConfig = $defaultConfig;

        foreach ($subject->getOptions() as $option) {
            /* @var $option \Magento\Catalog\Model\Product\Option */
            $values = $option->getValues();

            if (!empty($values) && $option->hasValues()) {
                foreach ($values as $valueId => $value) {

                    $config          = $this->getExtendedJsonConfig($defaultConfig, $option, $valueId);
                    $config['title'] = $value->getTitle();
                    $specialPrice    = $this->specialPriceModel->getActualSpecialPrice($value, true);
                    $needIncludeTax  = $this->basePriceHelper->getCatalogPriceContainsTax(
                        $this->getProduct()->getStoreId()
                    );
                    $isSpecialPrice  = false;

                    if ($specialPrice !== null) {
                        if ($needIncludeTax) {
                            $basePrice = min(
                                $config['prices']['basePrice']['amount'],
                                $specialPrice * ($config['prices']['basePrice']['amount'] / $config['prices']['finalPrice']['amount'])
                            );
                        } else {
                            $basePrice = min($config['prices']['basePrice']['amount'], $specialPrice);
                        }
                        $finalPrice = min($config['prices']['finalPrice']['amount'], $specialPrice);

                        if ($specialPrice < $config['prices']['finalPrice']['amount']) {
                            $isSpecialPrice = true;
                        }
                    } else {
                        $basePrice  = $config['prices']['basePrice']['amount'];
                        $finalPrice = $config['prices']['finalPrice']['amount'];
                    }

                    $config['prices']['basePrice']['amount']  = $this->basePriceHelper->getTaxPrice(
                        $this->getProduct(),
                        $basePrice,
                        $needIncludeTax
                    );
                    $config['prices']['finalPrice']['amount'] = $this->basePriceHelper->getTaxPrice(
                        $this->getProduct(),
                        $finalPrice,
                        $needIncludeTax || $isSpecialPrice
                    );

                    if ($specialPrice !== null) {
                        $config['special_price_display_node'] = $this->helper->getSpecialPriceDisplayNode(
                            $config['prices'],
                            $this->specialPriceModel->getSpecialPriceItem()
                        );
                    }

                    $extendedConfig[$option->getId()][$valueId] = array_merge(
                        $defaultConfig[$option->getId()][$valueId],
                        $config
                    );
                }
            } else {
                $config          = $this->getExtendedJsonConfig($defaultConfig, $option, null);
                $config['title'] = $option->getTitle();

                $extendedConfig[$option->getId()] = array_merge(
                    $defaultConfig[$option->getId()],
                    $config
                );
            }
        }

        return $this->_jsonEncoder->encode($extendedConfig);
    }

    /**
     * @param $defaultConfig
     * @param $option
     * @param $valueId
     */
    public function getExtendedJsonConfig($defaultConfig, $option, $valueId)
    {
        $config                = [];
        $defaultConfigOptionId = $defaultConfig[$option->getId()];

        if ($valueId !== null) {
            $config['prices']['oldPrice']['amount'] =
                $defaultConfigOptionId[$valueId]['prices']['oldPrice']['amount'];
        } else {
            $config['prices']['oldPrice']['amount'] =
                $defaultConfigOptionId['prices']['oldPrice']['amount'];
        }

        $config['prices']['oldPrice']['amount_excl_tax'] = $config['prices']['oldPrice']['amount'];
        $config['prices']['oldPrice']['amount_incl_tax'] = $this->basePriceHelper->getTaxPrice(
            $this->getProduct(),
            $config['prices']['oldPrice']['amount'],
            true
        );

        if ($valueId !== null) {
            $config['prices']['basePrice']['amount']  =
                $defaultConfigOptionId[$valueId]['prices']['basePrice']['amount'];
            $config['prices']['finalPrice']['amount'] =
                $defaultConfigOptionId[$valueId]['prices']['finalPrice']['amount'];
        } else {
            $config['prices']['basePrice']['amount']  =
                $defaultConfigOptionId['prices']['basePrice']['amount'];
            $config['prices']['finalPrice']['amount'] =
                $defaultConfigOptionId['prices']['finalPrice']['amount'];
        }

        $config['valuePrice'] = $this->priceCurrency->format(
            $config['prices']['oldPrice']['amount'],
            false
        );

        return $config;
    }
}
