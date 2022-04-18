<?php

namespace Amasty\Amp\Block\Product\Content;

use Magento\Catalog\Block\Product\View as ProductView;

class View extends ProductView
{
    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param bool $templateType
     * @param bool $displayIfNoReviews
     * @return string
     */
    public function getReviewsSummaryHtml(
        \Magento\Catalog\Model\Product $product,
        $templateType = false,
        $displayIfNoReviews = false
    ) {
        return $this->getData('reviewRenderer')->getReviewsSummaryHtml($product, $templateType, $displayIfNoReviews);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param array $additional
     * @return string
     */
    public function getSubmitUrl($product, $additional = [])
    {
        return str_replace(['https:', 'http:'], '', parent::getSubmitUrl($product, $additional));
    }

    /**
     * @param $product
     * @return bool
     */
    public function isShowOptionsContainer($product)
    {
        return $product->isSaleable() && in_array($this->getOptionsContainer(), ['container1', 'container2']);
    }
}
