<?php

namespace Amasty\Amp\Block\Pricing\Render\Bundle;

use Amasty\Amp\Model\UrlConfigProvider;

class FinalPriceBox extends \Magento\Bundle\Pricing\Render\FinalPriceBox
{
    /**
     * @return string[]
     */
    public function getCacheKeyInfo()
    {
        return [$this->getNameInLayout(), UrlConfigProvider::AMP];
    }
}
