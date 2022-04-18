<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/


namespace Amasty\CheckoutCore\Model\Quote\ResourceModel;

class Collection extends \Magento\Quote\Model\ResourceModel\Quote\Collection
{
    /**
     * @return int|string
     */
    public function getSize()
    {
        return $this->getConnection()->fetchOne($this->getSelectCountSql(), $this->_bindParams);
    }
}
