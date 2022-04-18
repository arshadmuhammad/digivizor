<?php

namespace Amasty\Amp\Block\Product\Content\View;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class Attributes extends \Magento\Catalog\Block\Product\View\Attributes
{
    /**
     * @var \Magento\Catalog\Helper\Output
     */
    private $outputHelper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Catalog\Helper\Output $outputHelper,
        array $data = []
    ) {
        parent::__construct($context, $registry, $priceCurrency, $data);
        $this->outputHelper = $outputHelper;
    }

    /**
     * @return \Magento\Catalog\Helper\Output
     */
    public function getOutputHelper()
    {
        return $this->outputHelper;
    }
}
