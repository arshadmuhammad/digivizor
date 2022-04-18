<?php

declare(strict_types=1);

namespace Amasty\LazyLoad\Model\OptionSource\Crawler;

use Amasty\PageSpeedTools\Model\OptionSource\ToOptionArrayTrait;
use Magento\Framework\Data\OptionSourceInterface;

class UserAgents implements OptionSourceInterface
{
    const WEBP = 'webp';
    const NO_WEBP = 'no_webp';

    use ToOptionArrayTrait;

    public function toArray(): array
    {
        return [
            self::WEBP => __('WebP Support'),
            self::NO_WEBP => __('No WebP Support'),
        ];
    }
}
