<?php

namespace Amasty\Amp\Controller;

use Amasty\Amp\Model\UrlConfigProvider;

class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Amasty\Amp\Model\ConfigProvider
     */
    private $config;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    private $url;

    public function __construct(
        \Amasty\Amp\Model\ConfigProvider $config,
        \Magento\Backend\Model\UrlInterface $url
    ) {
        $this->config = $config;
        $this->url = $url;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool|\Magento\Framework\App\ActionInterface
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $identifier = $request->getPathInfo();
        $adminFrontName = $this->url->getAreaFrontName();
        $isNotAdminUrl = strpos($identifier, $adminFrontName) === false;

        if ($isNotAdminUrl && $this->isAmpUrl($identifier)) {
            $request->setPathInfo(str_replace('/' . UrlConfigProvider::AMP . '/', '/', $identifier));
            $request->setParam('is_amp', true);
        }

        return false;
    }

    private function isAmpUrl(string $identifier): bool
    {
        return preg_match('@/amp(?=/|$)@m', $identifier);
    }
}
