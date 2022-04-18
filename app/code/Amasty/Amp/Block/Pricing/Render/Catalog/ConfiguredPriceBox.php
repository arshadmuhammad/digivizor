<?php

namespace Amasty\Amp\Block\Pricing\Render\Catalog;

use Amasty\Amp\Model\UrlConfigProvider;

class ConfiguredPriceBox extends \Magento\Catalog\Pricing\Render\ConfiguredPriceBox
{
    /**
     * @return string[]
     */
    public function getCacheKeyInfo()
    {
        return [$this->getNameInLayout(), UrlConfigProvider::AMP];
    }
}
