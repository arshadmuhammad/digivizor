<?php

namespace Amasty\Amp\Block\Product\Content\View\Options\Type;

use Magento\Catalog\Block\Product\View\Options\Type\Select as MagentoSelect;
use Magento\Catalog\Model\Product\Option;

class Select extends MagentoSelect
{
    /**
     * @return string
     */
    public function getValuesHtml(): string
    {
        $option = $this->getOption();
        $optionType = $option->getType();
        $block = '';
        switch ($optionType) {
            case Option::OPTION_TYPE_DROP_DOWN:
                $block = $this->getData('dropdown');
                break;
            case Option::OPTION_TYPE_MULTIPLE:
                $block = $this->getData('multiple');
                break;
            case Option::OPTION_TYPE_RADIO:
            case Option::OPTION_TYPE_CHECKBOX:
                $block = $this->getData('checkboxRadio');
                break;
        }

        $block->setOption($option)->setProduct($this->getProduct());

        return $block->toHtml();
    }
}
