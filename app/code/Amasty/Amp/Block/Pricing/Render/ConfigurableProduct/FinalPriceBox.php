<?php

namespace Amasty\Amp\Block\Pricing\Render\ConfigurableProduct;

use Amasty\Amp\Model\UrlConfigProvider;

class FinalPriceBox extends \Magento\ConfigurableProduct\Pricing\Render\FinalPriceBox
{
    /**
     * @return string[]
     */
    public function getCacheKeyInfo()
    {
        return [$this->getNameInLayout(), UrlConfigProvider::AMP];
    }
}
