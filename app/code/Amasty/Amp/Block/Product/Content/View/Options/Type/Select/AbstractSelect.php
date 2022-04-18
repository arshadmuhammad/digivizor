<?php

namespace Amasty\Amp\Block\Product\Content\View\Options\Type\Select;

use Amasty\Amp\Model\ConfigProvider;
use Magento\Catalog\Api\Data\ProductCustomOptionValuesInterface;
use Magento\Catalog\Block\Product\View\Options\AbstractOptions;
use Magento\Catalog\Pricing\Price\CustomOptionPriceInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class AbstractSelect extends AbstractOptions
{
    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Catalog\Helper\Data $catalogData,
        PriceCurrencyInterface $priceCurrency,
        ConfigProvider $configProvider,
        array $data = []
    ) {
        parent::__construct($context, $pricingHelper, $catalogData, $data);
        $this->priceCurrency = $priceCurrency;
        $this->configProvider = $configProvider;
    }

    /**
     * @param $value
     * @return string
     */
    public function formatPrice($value): string
    {
        /** @noinspection PhpMethodParametersCountMismatchInspection */
        return $this->_formatPrice(
            [
                'is_percent' => $value->getPriceType() === 'percent',
                'pricing_value' => $value->getPrice($value->getPriceType() === 'percent')
            ]
        );
    }

    /**
     * Return formatted price
     *
     * @param array $value
     * @param bool $flag
     * @return string
     */
    protected function _formatPrice($value, $flag = true)
    {
        if ($value['pricing_value'] == 0) {
            return '';
        }

        $sign = '+';
        if ($value['pricing_value'] < 0) {
            $sign = '-';
            $value['pricing_value'] = 0 - $value['pricing_value'];
        }

        $priceStr = $sign;

        $customOptionPrice = $this->getProduct()->getPriceInfo()->getPrice('custom_option_price');
        $context = [CustomOptionPriceInterface::CONFIGURATION_OPTION_FLAG => true];
        $optionAmount = $customOptionPrice->getCustomAmount($value['pricing_value'], null, $context);

        switch ($this->configProvider->getTaxDisplayType()) {
            case \Magento\Tax\Model\Config::DISPLAY_TYPE_EXCLUDING_TAX:
                $priceStr .= $this->priceCurrency->format($optionAmount->getBaseAmount());
                break;
            case \Magento\Tax\Model\Config::DISPLAY_TYPE_INCLUDING_TAX:
                $priceStr .= $this->priceCurrency->format($optionAmount->getValue());
                break;
            case \Magento\Tax\Model\Config::DISPLAY_TYPE_BOTH:
                $priceStr .= $this->priceCurrency->format($optionAmount->getValue())
                    . __(' (Excl. tax: ')
                    . $this->priceCurrency->format($optionAmount->getBaseAmount())
                    . ')';
                break;
        }

        return $priceStr;
    }
}
