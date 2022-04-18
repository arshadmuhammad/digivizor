<?php

namespace Amasty\PageSpeedOptimizer\Model\OptionSource;

use Magento\Framework\Data\OptionSourceInterface;

class FlatTableRecommendation implements OptionSourceInterface
{
    const NO = 0;
    const YES = 1;

    /**
     * @var \Amasty\Base\Model\MagentoVersion
     */
    private $magentoVersion;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $config;

    public function __construct(
        \Amasty\Base\Model\MagentoVersion $magentoVersion
    ) {
        $this->magentoVersion = $magentoVersion;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = [];
        foreach ($this->toArray() as $value => $label) {
            $optionArray[] = ['value' => $value, 'label' => $label];
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
        if (version_compare($this->magentoVersion->get(), '2.3.0', '>=')) {
            return [
                self::YES => __('Yes'),
                self::NO => __('No (Recommended)'),
            ];
        }

        return [
            self::YES => __('Yes (Recommended)'),
            self::NO => __('No'),
        ];
    }
}
