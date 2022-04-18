<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\OptionSource;

use Amasty\PageSpeedTools\Model\OptionSource\ToOptionArrayTrait;
use Magento\Framework\Data\OptionSourceInterface;

class ResizeAlgorithm implements OptionSourceInterface
{
    const RESIZE = 0;
    const CROP = 1;

    use ToOptionArrayTrait;

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            self::RESIZE => __('Resize'),
            self::CROP => __('Crop'),
        ];
    }
}
