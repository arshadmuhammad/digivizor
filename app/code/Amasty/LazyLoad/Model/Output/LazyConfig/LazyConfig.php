<?php
declare(strict_types=1);

namespace Amasty\LazyLoad\Model\Output\LazyConfig;

use Amasty\LazyLoad\Model\ConfigProvider;
use Amasty\LazyLoad\Model\OptionSource\PreloadStrategy;
use Amasty\PageSpeedTools\Model\Output\PageType\GetConfigPathByPageType;
use Amasty\PageSpeedTools\Model\DeviceDetect;
use Magento\Framework\DataObject;

class LazyConfig extends DataObject
{
    const IS_REPLACE_WITH_USER_AGENT = 'is_replace_with_user_agent';
    const USER_AGENT_IGNORE_LIST = 'user_agent_ignore_list';
    const IS_LAZY = 'is_lazy';
    const LAZY_IGNORE_LIST = 'lazy_ignore_list';
    const LAZY_SKIP_IMAGES = 'lazy_skip_images';
    const LAZY_PRELOAD_STRATEGY = 'lazy_preload_strategy';
    const LAZY_SCRIPT = 'lazy_script';

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var GetConfigPathByPageType
     */
    private $getConfigPathByPageType;

    /**
     * @var DeviceDetect
     */
    private $deviceDetect;

    /**
     * @var string
     */
    private $pageType;

    public function __construct(
        DeviceDetect $deviceDetect,
        GetConfigPathByPageType $getConfigPathByPageType,
        ConfigProvider $configProvider,
        string $pageType = '',
        array $data = []
    ) {
        parent::__construct($data);
        $this->configProvider = $configProvider;
        $this->getConfigPathByPageType = $getConfigPathByPageType;
        $this->deviceDetect = $deviceDetect;
        $this->pageType = $pageType;

        if ($this->configProvider->isEnabled()) {
            $this->initialize();
        }
    }

    protected function initialize()
    {
        if (empty($this->getPageType())) {
            $configPath = $this->getConfigPathByPageType->execute();
        } else {
            $configPath = $this->getConfigPathByPageType->execute($this->getPageType());
        }
        $this->setData(self::IS_REPLACE_WITH_USER_AGENT, $this->configProvider->isReplaceImagesUsingUserAgent());
        $isCustomLazyEnabled = (bool)$this->configProvider->getConfig(
            $configPath . ConfigProvider::PART_ENABLE_CUSTOM_LAZY
        );

        if ($isCustomLazyEnabled) {
            $isLazy = $this->configProvider->getConfig($configPath . ConfigProvider::PART_IS_LAZY);
        } else {
            $isLazy = $this->configProvider->isLazyLoad();
        }
        $this->setData(self::IS_LAZY, $isLazy);

        $userAgentIgnoreList = [];
        if ($this->configProvider->isReplaceImagesUsingUserAgent() && !empty($this->deviceDetect->getDeviceType())) {
            $type = '_' . $this->deviceDetect->getDeviceType();
            $userAgentIgnoreList = $this->configProvider->getReplaceImagesUsingUserAgentIgnoreList();
        } else {
            $type = '';
        }

        $skipImages = false;
        $skipStrategy = PreloadStrategy::SKIP_IMAGES;
        if ($isCustomLazyEnabled) {
            $lazyScript = $this->configProvider->getConfig($configPath . ConfigProvider::PART_SCRIPT);
            $ignoreList = $this->configProvider->convertStringToArray(
                $this->configProvider->getConfig($configPath . ConfigProvider::PART_IGNORE)
            );

            if ($this->configProvider->getConfig($configPath . ConfigProvider::PART_PRELOAD)) {
                $skipImages = $this->configProvider->getConfig($configPath . ConfigProvider::PART_SKIP . $type);
                $skipStrategy = $this->configProvider->getConfig($configPath . ConfigProvider::PART_STRATEGY);
            }
        } else {
            $lazyScript = $this->configProvider->lazyLoadScript();
            $ignoreList = $this->configProvider->getIgnoreImages();
            if ($this->configProvider->isPreloadImages()) {
                $skipImages = $this->configProvider->skipImagesCount($type);
                $skipStrategy = $this->configProvider->getSkipStrategy();
            }
        }

        if ($skipImages === false) {
            $skipImages = 0;
        }

        $this->setData(self::LAZY_IGNORE_LIST, $ignoreList);
        $this->setData(self::USER_AGENT_IGNORE_LIST, $userAgentIgnoreList);
        $this->setData(self::LAZY_SKIP_IMAGES, $skipImages);
        $this->setData(self::LAZY_PRELOAD_STRATEGY, $skipStrategy);
        $this->setData(self::LAZY_SCRIPT, $lazyScript);
    }

    public function getPageType(): ?string
    {
        return $this->pageType;
    }

    public function setPageType(?string $pageType): void
    {
        $this->pageType = $pageType;
    }
}
