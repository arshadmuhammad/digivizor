<?php
declare(strict_types=1);

namespace Amasty\LazyLoad\Model\OptionSource;

use Amasty\PageSpeedTools\Model\OptionSource\ToOptionArrayTrait;
use Magento\Framework\Data\OptionSourceInterface;

class PreloadStrategy implements OptionSourceInterface
{
    const WEBP_RESOLUTIONS = 0;
    const SKIP_IMAGES = 1;

    use ToOptionArrayTrait;

    public function toArray(): array
    {
        return [
            self::WEBP_RESOLUTIONS => __('WebP Format + Image Resolutions'),
            self::SKIP_IMAGES => __('Original Format of Images'),
        ];
    }
}
