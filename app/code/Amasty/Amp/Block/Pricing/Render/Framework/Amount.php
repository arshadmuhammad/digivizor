<?php

namespace Amasty\Amp\Block\Pricing\Render\Framework;

use Amasty\Amp\Model\UrlConfigProvider;

class Amount extends \Magento\Framework\Pricing\Render\Amount
{
    /**
     * @return string[]
     */
    public function getCacheKeyInfo()
    {
        return [$this->getNameInLayout(), UrlConfigProvider::AMP];
    }
}
