<?php

namespace Amasty\Amp\Block\Product\Content\ProductList;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Block\Product\ProductList\Upsell as CatalogUpsell;
use Magento\TargetRule\Block\Catalog\Product\ProductList\Upsell as TargetUpsell;

class Upsell extends Pure
{
    /**
     * @var \Amasty\Amp\Model\UrlConfigProvider
     */
    private $urlConfigProvider;

    public function __construct(
        Template\Context $context,
        ObjectManagerInterface $objectManager,
        \Amasty\Amp\Model\UrlConfigProvider $urlConfigProvider,
        $name = '',
        array $data = []
    ) {
        $name = class_exists(TargetUpsell::class) ? TargetUpsell::class : CatalogUpsell::class;
        $this->urlConfigProvider = $urlConfigProvider;
        parent::__construct($context, $objectManager, $name, $data);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     *
     * @return string
     */
    public function getAmpProductUrl(\Magento\Catalog\Model\Product $product)
    {
        return $this->urlConfigProvider->modifyProductPageUrl($this->getProductUrl($product));
    }

    /**
     * @return array
     */
    public function getBlockData()
    {
        $result = [];
        switch ($this->getData('type')) {
            case 'upsell-rule':
                if ($this->hasItems()) {
                    $result = $this->prepareData($this->getAllItems());
                }
                break;
            case 'upsell':
                if (count($this->getItemCollection()->getItems())) {
                    $result = $this->prepareData($this->getItemCollection()->getItems());
                }
                break;
        }

        return $result;
    }

    /**
     * @param $items
     * @return array
     */
    private function prepareData($items)
    {
        $result['type'] = 'upsell';
        $result['class'] = $result['type'];
        $result['image'] = 'upsell_products_list';
        $result['title'] = __('We found other products you might like!');
        $result['items'] = $items;

        return $result;
    }
}
