<?php

namespace Amasty\Amp\Block\Cms\Widget;

use Amasty\Amp\Model\UrlConfigProvider;

class ProductsList extends \Magento\CatalogWidget\Block\Product\ProductsList
{
    /**
     * @var UrlConfigProvider
     */
    private $urlConfigProvider;

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return string
     */
    public function getAmpProductUrl(\Magento\Catalog\Model\Product $product)
    {
        return $this->getUrlConfigProvider()->modifyProductPageUrl($this->getProductUrl($product));
    }

    /**
     * @return UrlConfigProvider
     */
    public function getUrlConfigProvider(): UrlConfigProvider
    {
        if (!$this->urlConfigProvider) {
            $this->urlConfigProvider = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(UrlConfigProvider::class);
        }

        return $this->urlConfigProvider;
    }
}
