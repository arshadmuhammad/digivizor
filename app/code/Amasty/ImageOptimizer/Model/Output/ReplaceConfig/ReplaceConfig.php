<?php

declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\Output\ReplaceConfig;

use Amasty\ImageOptimizer\Model\ConfigProvider;
use Amasty\PageSpeedTools\Model\Output\PageType\GetConfigPathByPageType;
use Magento\Framework\DataObject;

class ReplaceConfig extends DataObject
{
    const IS_REPLACE_WITH_USER_AGENT = 'is_replace_with_user_agent';
    const IS_REPLACE_IMAGES = 'is_replace_images';
    const REPLACE_IMAGES_IGNORE_LIST = 'replace_images_ignore_list';

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var GetConfigPathByPageType
     */
    private $getConfigPathByPageType;

    public function __construct(
        ConfigProvider $configProvider,
        GetConfigPathByPageType $getConfigPathByPageType,
        array $data = []
    ) {
        parent::__construct($data);
        $this->configProvider = $configProvider;
        $this->getConfigPathByPageType = $getConfigPathByPageType;
        $this->initialize();
    }

    private function initialize()
    {
        $configPath = $this->getConfigPathByPageType->execute();
        $isEnabledCustomReplace = (bool)$this->configProvider->getConfig(
            $configPath . ConfigProvider::PART_ENABLE_CUSTOM_REPLACE
        );
        $this->setData(
            self::IS_REPLACE_WITH_USER_AGENT,
            $this->configProvider->isReplaceImagesUsingUserAgent()
        );

        if ($isEnabledCustomReplace) {
            $isReplaceImages = (bool)$this->configProvider->getConfig(
                $configPath . ConfigProvider::PART_REPLACE_WITH_WEBP
            );
        } else {
            $isReplaceImages = $this->configProvider->isReplaceWithWebp();
        }
        $this->setData(self::IS_REPLACE_IMAGES, $isReplaceImages);
        $userAgentIgnoreList = [];

        if ($this->configProvider->isReplaceImagesUsingUserAgent()) {
            $userAgentIgnoreList = $this->configProvider->getReplaceImagesUsingUserAgentIgnoreList();
        }

        if ($isEnabledCustomReplace) {
            $replaceImagesIgnoreList = $this->configProvider->convertStringToArray(
                $this->configProvider->getConfig($configPath . ConfigProvider::PART_REPLACE_IGNORE)
            );
        } else {
            $replaceImagesIgnoreList = $this->configProvider->getReplaceIgnoreList();
        }

        $this->setData(self::REPLACE_IMAGES_IGNORE_LIST, array_merge($replaceImagesIgnoreList, $userAgentIgnoreList));
    }
}
