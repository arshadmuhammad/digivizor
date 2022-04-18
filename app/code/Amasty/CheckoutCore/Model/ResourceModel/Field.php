<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

declare(strict_types=1);

namespace Amasty\CheckoutCore\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Field extends AbstractDb
{
    const MAIN_TABLE = 'amasty_amcheckout_field';

    /**
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE, 'id');
    }
}
