<?php
declare(strict_types=1);

namespace Amasty\PageSpeedTools\Model\OptionSource;

trait ToOptionArrayTrait
{
    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $optionArray = [];
        foreach ($this->toArray() as $value => $label) {
            $optionArray[] = ['value' => $value, 'label' => $label];
        }
        return $optionArray;
    }
}
