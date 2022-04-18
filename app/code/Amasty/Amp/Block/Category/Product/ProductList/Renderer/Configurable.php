<?php

namespace Amasty\Amp\Block\Category\Product\ProductList\Renderer;

class Configurable extends \Magento\Swatches\Block\Product\Renderer\Listing\Configurable
{
    /**
     * @return array
     */
    public function getSwatchesData()
    {
        $attributesData = $this->getSwatchAttributesData();
        $allOptionIds = $this->getConfigurableOptionsIds($attributesData);
        $this->setData('allow_products', null);
        $swatchesData = $this->swatchHelper->getSwatchesByOptionsId($allOptionIds);
        $config = [];
        foreach ($attributesData as $attributeId => $attributeDataArray) {
            if (isset($attributeDataArray['options'])) {
                $config[$attributeId] = $this->addSwatchDataForAttribute(
                    $attributeDataArray['options'],
                    $swatchesData,
                    $attributeDataArray
                );
            }
        }

        return $config;
    }
}
