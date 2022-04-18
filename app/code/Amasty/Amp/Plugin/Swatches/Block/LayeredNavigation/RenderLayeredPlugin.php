<?php

namespace Amasty\Amp\Plugin\Swatches\Block\LayeredNavigation;

class RenderLayeredPlugin
{
    const AMP_CATEGORY_PRODUCT_LAYERED_RENDERER = 'Amasty_Amp::category/product/layered/renderer.phtml';

    /**
     * @var \Amasty\Amp\Model\ConfigProvider
     */
    private $configProvider;

    public function __construct(\Amasty\Amp\Model\ConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    /**
     * @param \Magento\Swatches\Block\LayeredNavigation\RenderLayered $block
     */
    public function beforeToHtml(\Magento\Swatches\Block\LayeredNavigation\RenderLayered $block)
    {
        if ($this->configProvider->isAmpCategoryPage()) {
            $block->setTemplate(self::AMP_CATEGORY_PRODUCT_LAYERED_RENDERER);
        }
    }
}
