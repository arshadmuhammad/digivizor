<?php

declare(strict_types=1);

namespace Amasty\LazyLoad\Model\Asset\Collector;

use Amasty\PageSpeedTools\Model\Asset\AssetCollectorInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class PreloadImageCollector implements AssetCollectorInterface
{
    const REGEX = '<img[^>]+src=["|\'](?<asset_url>.*\.(svg|png|webp|jpeg|jpg))["|\']';

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var array
     */
    private $collectedAssets = [];

    public function __construct(StoreManagerInterface $storeManager)
    {
        $this->storeManager = $storeManager;
    }

    public function getAssetContentType(): string
    {
        return 'image';
    }

    public function getCollectedAssets(): array
    {
        return $this->collectedAssets;
    }

    public function execute(string $output)
    {
        return null;
    }

    public function addImageAsset(string $assetUrl)
    {
        $baseUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_WEB);

        if (strstr($assetUrl, $baseUrl) && preg_match('/' . self::REGEX . '/is', $assetUrl, $matches)) {
            $this->collectedAssets[] = $matches['asset_url'];
        }
    }
}
