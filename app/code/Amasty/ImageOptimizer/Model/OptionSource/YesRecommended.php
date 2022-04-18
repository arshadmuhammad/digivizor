<?php
declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\OptionSource;

use Amasty\PageSpeedTools\Model\OptionSource\ToOptionArrayTrait;
use Magento\Framework\Data\OptionSourceInterface;

class YesRecommended implements OptionSourceInterface
{
    const NO = 0;
    const YES = 1;

    use ToOptionArrayTrait;

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            self::YES => __('Yes (Recommended)'),
            self::NO => __('No'),
        ];
    }
}
