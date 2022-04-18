<?php

namespace Amasty\Amp\Block\Pricing\Render\Catalog;

use Amasty\Amp\Model\UrlConfigProvider;

class FinalPriceBox extends \Magento\Catalog\Pricing\Render\FinalPriceBox
{
    /**
     * @return string[]
     */
    public function getCacheKeyInfo()
    {
        return [$this->getNameInLayout(), UrlConfigProvider::AMP];
    }
}
