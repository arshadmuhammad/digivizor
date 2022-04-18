<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/


namespace Amasty\CheckoutCore\Model\ResourceModel;

class Fee extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    const MAIN_TABLE = 'amasty_amcheckout_additional_fee';

    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE, 'id');
    }
}
