<?php

namespace Amasty\Amp\Plugin\Catalog\Block\Product;

class ImagePlugin
{
    /**
     * @var \Amasty\Amp\Model\ConfigProvider
     */
    private $configProvider;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    public function __construct(
        \Amasty\Amp\Model\ConfigProvider $configProvider,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->configProvider = $configProvider;
        $this->request = $request;
    }

    /**
     * @param $block
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function beforeToHtml(\Magento\Catalog\Block\Product\Image $block)
    {
        if ($this->configProvider->isAmpEnabledOnCurrentPage($this->request->getFullActionName())) {
            $block->setTemplate('Amasty_Amp::product/content/image_with_borders.phtml');
        }

        return $block;
    }
}
