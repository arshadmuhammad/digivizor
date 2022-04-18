<?php

namespace Amasty\Amp\Block\Product\Content\ProductList;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Block\Product\ProductList\Related as CatalogRelated;
use Magento\TargetRule\Block\Catalog\Product\ProductList\Related as TargetRelated;

class Related extends Pure
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
        $name = class_exists(TargetRelated::class) ? TargetRelated::class : CatalogRelated::class;
        parent::__construct($context, $objectManager, $name, $data);
        $this->urlConfigProvider = $urlConfigProvider;
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
            case 'related-rule':
                if ($this->hasItems()) {
                    $result = $this->prepareData($this->getAllItems());
                }
                break;

            case 'related':
                if ($this->getItems()->getSize()) {
                    $result = $this->prepareData($this->getItems());
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
        $result['type'] = 'related';
        $result['class'] = $result['type'];
        $result['image'] = 'related_products_list';
        $result['title'] = __('Related Products');
        $result['items'] = $items;

        return $result;
    }
}
