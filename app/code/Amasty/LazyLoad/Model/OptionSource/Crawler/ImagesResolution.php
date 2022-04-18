<?php

declare(strict_types=1);

namespace Amasty\LazyLoad\Model\OptionSource\Crawler;

use Amasty\PageSpeedTools\Model\DeviceDetect;
use Amasty\PageSpeedTools\Model\OptionSource\ToOptionArrayTrait;
use Magento\Framework\Data\OptionSourceInterface;

class ImagesResolution implements OptionSourceInterface
{
    use ToOptionArrayTrait;

    public function toArray(): array
    {
        return [
            DeviceDetect::DESKTOP => __('Desktop'),
            DeviceDetect::TABLET => __('Tablet'),
            DeviceDetect::MOBILE => __('Mobile'),
        ];
    }
}
