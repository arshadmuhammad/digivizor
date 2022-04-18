<?php

declare(strict_types=1);

namespace Amasty\LazyLoad\Model\Crawler\Combination;

use Amasty\LazyLoad\Model\ConfigProvider;
use Amasty\LazyLoad\Model\OptionSource\Crawler\UserAgents;
use Amasty\PageSpeedTools\Model\DeviceDetect;
use GuzzleHttp\RequestOptions;
use Magento\Framework\App\Http\Context;

class WebpAndDeviceCombination
{
    const DEVICE_AGENT_MAP = [
        DeviceDetect::DESKTOP => [
            UserAgents::WEBP => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko)'
                . ' Chrome/42.0.2311.135 Safari/537.36 Edge/12.246',
            UserAgents::NO_WEBP => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_2) AppleWebKit/601.3.9'
                . ' (KHTML, like Gecko) Version/9.0.2 Safari/601.3.9',
        ],
        DeviceDetect::TABLET => [
            UserAgents::WEBP => 'Mozilla/5.0 (Linux; Android 5.0.2; LG-V410/V41020c Build/LRX22G) AppleWebKit/537.36'
                . ' (KHTML, like Gecko) Version/4.0 Chrome/34.0.1847.118 Safari/537.36',
            UserAgents::NO_WEBP => 'Mozilla/5.0 (iPad; CPU OS 11_0 like Mac OS X) AppleWebKit/604.1.34'
                . ' (KHTML, like Gecko) Version/11.0 Mobile/15A5341f Safari/604.1',
        ],
        DeviceDetect::MOBILE => [
            UserAgents::WEBP => 'Mozilla/5.0 (Linux; Android 6.0.1; Moto G (4)) AppleWebKit/537.36'
                . ' (KHTML, like Gecko) Chrome/88.0.4324.96 Mobile Safari/537.36',
            UserAgents::NO_WEBP => 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15'
                . ' (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1',
        ]
    ];

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(ConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    public function getVariations(): array
    {
        if (!$this->configProvider->isEnabled() || !$this->configProvider->isReplaceImagesUsingUserAgent()) {
            return [];
        }

        $combination = [];
        $webpSupportConfig = $this->configProvider->convertStringToArray(
            $this->configProvider->getCustomValue('amasty_fpc/optimizer/webp_support') ?? UserAgents::WEBP,
            ','
        );
        $deviceConfig = $this->configProvider->convertStringToArray(
            $this->configProvider->getCustomValue('amasty_fpc/optimizer/devices') ?? DeviceDetect::DESKTOP,
            ','
        );

        foreach ($webpSupportConfig as $webpSupport) {
            foreach ($deviceConfig as $device) {
                $combination[] = [
                    'webp' => $webpSupport,
                    'device' => $device
                ];
            }
        }

        return $combination;
    }

    public function getCombinationKey(): string
    {
        return 'crawler_webp_and_device';
    }

    public function modifyRequest(array $combination, array &$requestParams, Context $context)
    {
        if (!$this->configProvider->isEnabled() || !$this->configProvider->isReplaceImagesUsingUserAgent()) {
            return;
        }

        $deviceConfig = $combination[$this->getCombinationKey()] ?? null;

        if ($deviceConfig) {
            $webpSupport = $deviceConfig['webp'];
            $device = $deviceConfig['device'];
            $requestParams[RequestOptions::HEADERS]['User-Agent'] = self::DEVICE_AGENT_MAP[$device][$webpSupport]
                ?? '';
            $context->setValue(
                'amasty_device_type',
                $device,
                DeviceDetect::DESKTOP
            );
            $context->setValue(
                'amasty_is_use_webp',
                $webpSupport === UserAgents::WEBP ? 1 : 0,
                0
            );
        }
    }

    public function prepareLog(array $crawlerLogData, array $combination): array
    {
        $deviceConfig = $combination[$this->getCombinationKey()] ?? null;

        if ($deviceConfig) {
            $crawlerLogData['webp_support'] = $deviceConfig['webp'] ?? null;
            $crawlerLogData['device'] = $deviceConfig['device'] ?? null;
        }

        return $crawlerLogData;
    }
}
