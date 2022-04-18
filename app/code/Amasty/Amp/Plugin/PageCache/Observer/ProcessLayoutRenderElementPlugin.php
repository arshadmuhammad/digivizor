<?php

declare(strict_types=1);

namespace Amasty\Amp\Plugin\PageCache\Observer;

use Amasty\Amp\Model\ConfigProvider;
use Amasty\Amp\Model\Detection\MobileDetect;
use Magento\Framework\Event\Observer;
use Magento\PageCache\Model\Config as CacheConfig;
use Magento\PageCache\Observer\ProcessLayoutRenderElement;

class ProcessLayoutRenderElementPlugin
{
    /**
     * @var CacheConfig
     */
    private $cacheConfig;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var MobileDetect
     */
    private $mobileDetect;

    public function __construct(
        CacheConfig $cacheConfig,
        ConfigProvider $configProvider,
        MobileDetect $mobileDetect
    ) {
        $this->cacheConfig = $cacheConfig;
        $this->configProvider = $configProvider;
        $this->mobileDetect = $mobileDetect;
    }

    /**
     * Prevent wrapping blocks' output with esi:include placeholders on AMP pages with Varnish enabled
     * In case of incompatibility with AMP page layout
     *
     * @param ProcessLayoutRenderElement $subject
     * @param \Closure $proceed
     * @param Observer $observer
     * @return mixed|void
     */
    public function aroundExecute(ProcessLayoutRenderElement $subject, \Closure $proceed, Observer $observer)
    {
        if ($this->configProvider->isRedirectMobile()
            && $this->mobileDetect->isMobile()
            && $this->cacheConfig->getType() == CacheConfig::VARNISH
        ) {
            return;
        }

        return $proceed($observer);
    }
}
