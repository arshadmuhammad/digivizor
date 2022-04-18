<?php

namespace Amasty\Amp\Block\Category\Product\ProductList;

class ToolbarBottom extends \Magento\Framework\View\Element\Template
{
    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _toHtml()
    {
        return $this->getLayout()->getBlock('product_list_toolbar')->setIsBottom(true)->toHtml();
    }
}
