<?php

declare(strict_types=1);

namespace Amasty\PageSpeedOptimizer\Observer;

use Amasty\PageSpeedTools\Model\Asset\AssetCollectorInterface;
use Amasty\PageSpeedOptimizer\Model\Asset;
use Amasty\PageSpeedOptimizer\Model\ConfigProvider;
use Magento\Framework\App\Response\HttpInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

class AddLinkHeader implements ObserverInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Asset\CollectorAdapter
     */
    private $collectorAdapter;

    public function __construct(
        ConfigProvider $configProvider,
        StoreManagerInterface $storeManager,
        Asset\CollectorAdapter $collectorAdapter
    ) {
        $this->configProvider = $configProvider;
        $this->storeManager = $storeManager;
        $this->collectorAdapter = $collectorAdapter;
    }

    public function execute(Observer $observer)
    {
        /** @var HttpInterface $response */
        $response = $observer->getResponse();

        if ($response instanceof HttpInterface && $this->configProvider->isServerPushEnabled()) {
            $response->setHeader('Link', $this->buildHeaderLine());
        }
    }

    private function buildHeaderLine()
    {
        $assets = [];
        $baseUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_WEB);
        $assetTypes = $this->configProvider->getServerPushAssetTypes();
        $pushIgnoreList = $this->configProvider->getServerPushIgnoreList();

        /** @var AssetCollectorInterface $collector */
        foreach ($this->collectorAdapter->getByTypes($assetTypes) as $collector) {
            foreach ($collector->getCollectedAssets() as $collectedAssetUrl) {
                if ($this->isAssetUrlIgnored($pushIgnoreList, $collectedAssetUrl)) {
                    continue;
                }

                $assetParts = [
                    sprintf('<%s>', str_replace($baseUrl, '/', $collectedAssetUrl)),
                    'rel=preload',
                    sprintf('as=%s', $collector->getAssetContentType()),
                ];

                if ($collector->getAssetContentType() === 'font') {
                    $assetParts[] = 'crossorigin=anonymous';
                }

                $assets[] = implode('; ', $assetParts);
            }
        }

        return implode(', ', $assets);
    }

    private function isAssetUrlIgnored(array $ignoreList, string $assetUrl): bool
    {
        $isIgnored = false;

        foreach ($ignoreList as $urlPart) {
            if (strpos($assetUrl, $urlPart) !== false) {
                $isIgnored = true;
                break;
            }
        }

        return $isIgnored;
    }
}
