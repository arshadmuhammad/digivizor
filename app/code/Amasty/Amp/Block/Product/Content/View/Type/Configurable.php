<?php

namespace Amasty\Amp\Block\Product\Content\View\Type;

use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable as MagentoConfigurable;

class Configurable extends MagentoConfigurable
{
    /**
     * @return bool
     */
    public function isSwatchType()
    {
        return $this->getData('swatchProvider')->isSwatchesEnable();
    }

    /**
     * @param $optionIds
     * @return array
     */
    public function getSwatchesData($optionIds)
    {
        return $this->getData('swatchProvider')->getSwatchesData($optionIds);
    }

    /**
     * @param string $type
     * @param string $filename
     * @return string
     */
    public function getSwatchPath($type, $filename)
    {
        return $this->getData('mediaHelper')->getSwatchAttributeImage($type, $filename);
    }
}
