<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/


namespace Amasty\CheckoutCore\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class MultipleShippingAddressOptions implements OptionSourceInterface
{
    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Grid'),
                'value' => 0
            ],
            [
                'label' => __('Dropdown Menu'),
                'value' => 1
            ]
        ];
    }
}
