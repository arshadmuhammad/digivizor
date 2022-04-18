<?php
declare(strict_types=1);

namespace Amasty\LazyLoad\Plugin\Framework\App\Http;

use Amasty\LazyLoad\Model\ConfigProvider;
use Amasty\PageSpeedTools\Model\DeviceDetect;
use Magento\Framework\App\Http\Context as HttpContext;

class Context
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var DeviceDetect
     */
    private $deviceDetect;

    public function __construct(
        DeviceDetect $deviceDetect,
        ConfigProvider $configProvider
    ) {
        $this->deviceDetect = $deviceDetect;
        $this->configProvider = $configProvider;
    }

    public function beforeGetVaryString(HttpContext $subject)
    {
        if (!$this->configProvider->isEnabled() || !$this->configProvider->isReplaceImagesUsingUserAgent()) {
            return;
        }

        $subject->setValue(
            'amasty_device_type',
            $this->deviceDetect->getDeviceType(),
            DeviceDetect::DESKTOP
        );
        $subject->setValue(
            'amasty_is_use_webp',
            (int)$this->deviceDetect->isUseWebP(),
            0
        );
    }
}
