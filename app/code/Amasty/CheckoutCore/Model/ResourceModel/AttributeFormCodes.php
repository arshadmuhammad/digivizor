<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

declare(strict_types=1);

namespace Amasty\CheckoutCore\Model\ResourceModel;

class AttributeFormCodes
{
    const ADMINHTML_CHECKOUT = 'adminhtml_checkout';
    const ADMINHTML_CUSTOMER = 'adminhtml_customer';
    const CUSTOMER_ACCOUNT_CREATE = 'customer_account_create';
    const CUSTOMER_ACCOUNT_EDIT = 'customer_account_edit';

    // Amasty_CustomerAttributes
    const AMASTY_CUSTOM_ATTRIBUTES = 'amasty_custom_attribute';
    const AMASTY_CUSTOM_ATTRIBUTES_REGISTRATION = 'customer_attributes_registration';
    const AMASTY_CUSTOM_ATTRIBUTES_CHECKOUT = 'customer_attributes_checkout';
}
