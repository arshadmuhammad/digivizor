<?php

namespace Amasty\Amp\Block\Pricing\Render\Catalog;

use Amasty\Amp\Model\UrlConfigProvider;

class PriceBox extends \Magento\Catalog\Pricing\Render\PriceBox
{
    /**
     * @return string[]
     */
    public function getCacheKeyInfo()
    {
        return [$this->getNameInLayout(), UrlConfigProvider::AMP];
    }
}
