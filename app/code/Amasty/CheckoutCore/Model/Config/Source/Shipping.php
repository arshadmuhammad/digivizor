<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/


namespace Amasty\CheckoutCore\Model\Config\Source;

use Magento\Shipping\Model\Config\Source\Allmethods;

class Shipping extends Allmethods
{
    /**
     * @inheritdoc
     */
    public function toOptionArray($isActiveOnlyFlag = false)
    {
        $options = parent::toOptionArray(true);

        $options[0]['label'] = ' ';

        foreach ($options as &$option) {
            if (is_array($option['value'])) {
                foreach ($option['value'] as &$method) {
                    $method['label'] = preg_replace('#^\[.+?\]\s#', '', $method['label']);
                }
            }
        }

        return $options;
    }
}
