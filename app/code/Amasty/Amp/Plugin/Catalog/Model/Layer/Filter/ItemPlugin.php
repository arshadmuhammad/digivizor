<?php

namespace Amasty\Amp\Plugin\Catalog\Model\Layer\Filter;

class ItemPlugin
{
    /**
     * @var \Amasty\Amp\Model\UrlConfigProvider
     */
    private $urlConfigProvider;

    /**
     * @var \Amasty\Amp\Model\ConfigProvider
     */
    private $configProvider;

    public function __construct(
        \Amasty\Amp\Model\UrlConfigProvider $urlConfigProvider,
        \Amasty\Amp\Model\ConfigProvider $configProvider
    ) {
        $this->urlConfigProvider = $urlConfigProvider;
        $this->configProvider = $configProvider;
    }

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\Item $model
     * @param string $url
     * @return string
     */
    public function afterGetClearLinkUrl(\Magento\Catalog\Model\Layer\Filter\Item $model, $url)
    {
        return $this->configProvider->isAmpCategoryPage() ? $this->urlConfigProvider->convertToAmpUrl($url) : $url;
    }

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\Item $model
     * @param string $url
     * @return string
     */
    public function afterGetRemoveUrl(\Magento\Catalog\Model\Layer\Filter\Item $model, $url)
    {
        return $this->configProvider->isAmpCategoryPage() ? $this->urlConfigProvider->convertToAmpUrl($url) : $url;
    }
}
