<?php
declare(strict_types=1);

namespace Amasty\PageSpeedTools\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * TODO Refactor
 */
class Resolutions implements OptionSourceInterface
{
    const MOBILE = 1;
    const TABLET = 2;

    const RESOLUTIONS = [
        self::TABLET => [
            'dir' => 'amasty' . DIRECTORY_SEPARATOR .  'amopttablet' . DIRECTORY_SEPARATOR,
            'path' => 'tablet_path',
            'width' => 768,
            'min-width' => 480
        ],
        self::MOBILE => [
            'dir' => 'amasty' . DIRECTORY_SEPARATOR .  'amoptmobile' . DIRECTORY_SEPARATOR,
            'path' => 'mobile_path',
            'width' => 480
        ]
    ];

    const WEBP_DIR = 'amasty' . DIRECTORY_SEPARATOR .  'webp' . DIRECTORY_SEPARATOR;

    use ToOptionArrayTrait;

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            self::MOBILE => __('Mobile'),
            self::TABLET => __('Tablet'),
        ];
    }
}
