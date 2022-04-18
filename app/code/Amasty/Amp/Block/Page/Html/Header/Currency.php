<?php

namespace Amasty\Amp\Block\Page\Html\Header;

class Currency extends \Magento\Directory\Block\Currency
{
    /**
     * @param $code
     * @return string
     */
    public function getStoreUrlAmp($code)
    {
        return $this->_urlBuilder->getUrl('directory/currency/switch', ['currency' => $code]);
    }
}
