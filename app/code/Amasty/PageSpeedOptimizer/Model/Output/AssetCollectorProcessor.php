<?php

declare(strict_types=1);

namespace Amasty\PageSpeedOptimizer\Model\Output;

use Amasty\PageSpeedOptimizer\Model\Asset;
use Amasty\PageSpeedOptimizer\Model\ConfigProvider;
use Amasty\PageSpeedTools\Model\Output\OutputProcessorInterface;

class AssetCollectorProcessor implements OutputProcessorInterface
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var Asset\CollectorAdapter
     */
    private $collectorAdapter;

    public function __construct(
        ConfigProvider $configProvider,
        Asset\CollectorAdapter $collectorAdapter
    ) {
        $this->configProvider = $configProvider;
        $this->collectorAdapter = $collectorAdapter;
    }

    public function process(string &$output): bool
    {
        if (!$this->configProvider->isServerPushEnabled()) {
            return true;
        }

        foreach ($this->configProvider->getServerPushAssetTypes() as $assetType) {
            $collector = $this->collectorAdapter->get($assetType);
            $collector->execute($output);
        }

        return true;
    }
}
