<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/


namespace Amasty\CheckoutCore\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class CustomerRegistration implements OptionSourceInterface
{
    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('No')],
            ['value' => 1, 'label' => __('After Placing an Order')],
            ['value' => 2, 'label' => __('While Placing an Order')]
        ];
    }
}
