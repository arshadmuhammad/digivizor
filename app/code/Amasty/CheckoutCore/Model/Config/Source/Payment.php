<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/


namespace Amasty\CheckoutCore\Model\Config\Source;

use Magento\Payment\Model\Config\Source\Allmethods;

class Payment extends Allmethods
{
    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        $options = parent::toOptionArray();

        array_unshift($options, ['value' => '', 'label' => ' ']);

        foreach ($options as $key => $option) {
            if (!isset($options[$key]['value'])) {
                $options[$key]['value'] = null;
            }
        }

        return $options;
    }
}
