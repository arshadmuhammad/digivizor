<?php

namespace Amasty\Amp\Block\Category\Navigation;

class State extends \Magento\LayeredNavigation\Block\Navigation\State
{
    /**
     * @var string
     */
    protected $_template = 'Amasty_Amp::category/layer/state.phtml';

    /**
     * @return string
     */
    public function getClearUrl()
    {
        return $this->getData('urlConfig')->convertToAmpUrl(parent::getClearUrl());
    }
}
