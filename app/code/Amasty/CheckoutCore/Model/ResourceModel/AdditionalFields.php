<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/


namespace Amasty\CheckoutCore\Model\ResourceModel;

use Amasty\CheckoutCore\Api\Data\AdditionalFieldsInterface;

class AdditionalFields extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const MAIN_TABLE = 'amasty_amcheckout_additional';

    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE, AdditionalFieldsInterface::ID);
    }
}
