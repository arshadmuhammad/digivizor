<?php

namespace Amasty\Amp\Model\Category\Product;

class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
{
    /**
     * @param $product
     * @return string|null
     */
    public function getProductName($product)
    {
        return $this->getData('outputHelper')->productAttribute($product, $product->getName(), 'name');
    }

    /**
     * @param $productUrl
     * @return string
     */
    public function getAmpProductUrl($productUrl)
    {
        return $this->getData('urlConfig')->modifyProductPageUrl($productUrl);
    }

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
     * @param string $url
     * @return string
     */
    public function convertUrl($url)
    {
        return str_replace(['https:', 'http:'], '', $url);
    }
}
