<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/


namespace Amasty\CheckoutCore\Api;

interface GuestAdditionalFieldsManagementInterface
{
    /**
     * @param string $cartId
     * @param \Amasty\CheckoutCore\Api\Data\AdditionalFieldsInterface $fields
     *
     * @return bool
     */
    public function save($cartId, $fields);
}
