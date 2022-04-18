<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

declare(strict_types=1);

namespace Amasty\CheckoutCore\Plugin\Customer\Address;

use Amasty\CheckoutCore\Plugin\AttributeMerger;

class DataProvider
{
    const AMCHECKOUT_CUSTOM_FIELDS = [
        'custom_field_1',
        'custom_field_2',
        'custom_field_3'
    ];

    /**
     * @var AttributeMerger
     */
    private $attributeMerger;

    public function __construct(
        AttributeMerger $attributeMerger
    ) {
        $this->attributeMerger = $attributeMerger;
    }

    /**
     * @param \Magento\Customer\Model\Address\DataProvider $subject
     * @param array $attributes
     *
     * @return array
     */
    public function afterGetMeta(\Magento\Customer\Model\Address\DataProvider $subject, $attributes)
    {
        $attributeConfig = $this->attributeMerger->getFieldConfig();

        foreach (self::AMCHECKOUT_CUSTOM_FIELDS as $customField) {
            if (isset($attributeConfig[$customField])
                && isset($attributes['general']['children'][$customField])
                && !$attributeConfig[$customField]->getEnabled()
            ) {
                unset($attributes['general']['children'][$customField]);
            }
        }

        return $attributes;
    }
}
