<?php

namespace Amasty\PageSpeedOptimizer\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class EnableRecommended implements OptionSourceInterface
{
    const DISABLED = 0;
    const ENABLED = 1;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = [];
        foreach ($this->toArray() as $widgetType => $label) {
            $optionArray[] = ['value' => $widgetType, 'label' => $label];
        }
        return $optionArray;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            self::ENABLED => __('Enable (Recommended)'),
            self::DISABLED => __('Disable'),
        ];
    }
}
