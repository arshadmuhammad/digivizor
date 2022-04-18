<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionInventory\Observer;

use \Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Event\Observer as EventObserver;
use \Magento\Quote\Model\Quote\Item as QuoteItem;

/**
 * Class UpdateOptionsMessages.
 * This observer updates options stock message
 */
class UpdateOptionsMessages implements ObserverInterface
{
    /**
     * @var \MageWorx\OptionInventory\Model\StockProvider|null
     */
    protected $stockProvider = null;
    /**
     * @var \MageWorx\OptionInventory\Helper\Data
     */
    protected $helperData;

    /**
     * UpdateOptionsMessages constructor.
     *
     * @param \MageWorx\OptionInventory\Model\StockProvider $stockProvider
     */
    public function __construct(
        \MageWorx\OptionInventory\Model\StockProvider $stockProvider,
        \MageWorx\OptionInventory\Helper\Data $helperData
    ) {
        $this->stockProvider = $stockProvider;
        $this->helperData    = $helperData;
    }

    /**
     * @param EventObserver $observer
     * @return mixed
     */
    public function execute(EventObserver $observer)
    {
        if ($this->helperData->isEnabledOptionInventory()) {
            $configObj = $observer->getEvent()->getData('configObj');
            $options   = $configObj->getData('config');
            $options   = $this->stockProvider->updateOptionsStockMessage($options);

            $configObj->setData('config', $options);
        }
    }
}
