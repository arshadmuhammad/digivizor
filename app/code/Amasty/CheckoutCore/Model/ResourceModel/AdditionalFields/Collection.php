<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/


namespace Amasty\CheckoutCore\Model\ResourceModel\AdditionalFields;

/**
 * @method \Amasty\CheckoutCore\Model\AdditionalFields[] getItems()
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(
            \Amasty\CheckoutCore\Model\AdditionalFields::class,
            \Amasty\CheckoutCore\Model\ResourceModel\AdditionalFields::class
        );
    }
}
