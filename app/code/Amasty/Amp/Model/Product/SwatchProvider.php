<?php

namespace Amasty\Amp\Model\Product;

use Magento\Framework\Data\CollectionDataSourceInterface;

class SwatchProvider implements CollectionDataSourceInterface
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    /**
     * @var \Magento\Swatches\Helper\Data
     */
    private $swatchHelper;

    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Swatches\Helper\Data $swatchHelper
    ) {
        $this->moduleManager = $moduleManager;
        $this->swatchHelper = $swatchHelper;
    }

    /**
     * @return bool
     */
    public function isSwatchesEnable()
    {
        return $this->moduleManager->isEnabled('Magento_Swatches');
    }

    /**
     * @param $optionIds
     * @return array
     */
    public function getSwatchesData($optionIds)
    {
        return $this->swatchHelper->getSwatchesByOptionsId($optionIds);
    }
}
