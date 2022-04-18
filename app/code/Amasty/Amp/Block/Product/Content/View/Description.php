<?php

namespace Amasty\Amp\Block\Product\Content\View;

class Description extends \Magento\Catalog\Block\Product\View\Description
{
    /**
     * @return string
     */
    protected function _toHtml()
    {
        $html = '';
        if ($this->getProduct()) {
            $html = parent::_toHtml();
        }

        return $html;
    }

    /**
     * @return string
     */
    public function getAttributeValue()
    {
        $attributeType = $this->getAtType();
        $helper = $this->getData('outputHelper');
        $call = $this->getAtCall();
        $code = $this->getAtCode();
        $product = $this->getProduct();

        if ($attributeType && $attributeType == 'text') {
            $attributeValue = $helper->productAttribute($product, $product->$call(), $code)
                ? $product->getAttributeText($code)
                : '';
        } else {
            $attributeValue = $helper->productAttribute($product, $product->$call(), $code);
        }

        return $attributeValue;
    }

    /**
     * @return string
     */
    public function getAttributeLabel()
    {
        $attributeLabel = $this->getAtLabel();
        $renderLabel = $attributeLabel !== 'none';

        if ($attributeLabel && $attributeLabel == 'default') {
            $attributeLabel = $this->getProduct()
                ->getResource()
                ->getAttribute($this->getAtCode())
                ->getStoreLabel();
        }

        return $renderLabel ? $attributeLabel : '';
    }
}
