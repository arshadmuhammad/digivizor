<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/


namespace Amasty\CheckoutCore\Component;

use Magento\Ui\Component\Form as UiFrom;

class Form extends UiFrom
{
    /**
     * {@inheritdoc}
     */
    public function getDataSourceData()
    {
        return $this->getContext()->getDataProvider()->getData();
    }
}
