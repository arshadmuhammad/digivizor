<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/


namespace Amasty\CheckoutCore\Plugin;

use Amasty\CheckoutCore\Model\Config;

class LayoutProcessor
{
    /**
     * @var array
     */
    protected $orderFixes = [];

    /**
     * @var Config
     */
    private $checkoutConfig;

    public function __construct(Config $checkoutConfig)
    {
        $this->checkoutConfig = $checkoutConfig;
    }

    /**
     * @param $field
     * @param $order
     */
    public function setOrder($field, $order)
    {
        $this->orderFixes[$field] = $order;
    }

    /**
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $result
     * @return array
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        $result
    ) {
        if ($this->checkoutConfig->isEnabled()) {
            $layoutRoot = &$result['components']['checkout']['children']['steps']['children']['shipping-step']
                           ['children']['shippingAddress']['children'];
            $layoutRoot['customer-email']['component'] = 'Amasty_CheckoutCore/js/view/form/element/email';

            foreach ($this->orderFixes as $code => $order) {
                $layoutRoot['shipping-address-fieldset']['children'][$code]['sortOrder'] = $order;
            }
        }

        return $result;
    }
}
