<?php

namespace Amasty\Amp\Plugin\Framework\View\Page;

use Magento\Framework\View\Page\Config;

class ConfigPlugin
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $configProvider;

    public function __construct(\Amasty\Amp\Model\ConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    /**
     * @param Config $pageConfig
     * @param \Closure $proceed
     * @param $layout
     * @return Config
     */
    public function aroundSetPageLayout(Config $pageConfig, \Closure $proceed, $layout)
    {
        $result = $pageConfig;
        if (!$this->configProvider->isAmpEnabledOnCurrentPage()) {
            $result = $proceed($layout);
        }

        return $result;
    }
}
