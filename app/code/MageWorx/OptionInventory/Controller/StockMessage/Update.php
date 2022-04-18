<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionInventory\Controller\StockMessage;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Serialize\Serializer\Json as Serializer;

/**
 * Class Update.
 * This controller updates options stock message on the product page
 */
class Update extends Action
{
    /**
     * @var \MageWorx\OptionInventory\Model\StockProvider|null
     */
    protected $stockProvider = null;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * Update constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \MageWorx\OptionInventory\Model\StockProvider $stockProvider
     * @param Serializer $serializer
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \MageWorx\OptionInventory\Model\StockProvider $stockProvider,
        Serializer $serializer
    ) {
        parent::__construct($context);
        $this->stockProvider = $stockProvider;
        $this->serializer    = $serializer;
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $this->getRequest()->getParams();
        $optionConfig    = $this->getRequest()->getPost('opConfig');

        if (!$optionConfig) {
            return;
        }
        $options = $this->serializer->unserialize($optionConfig);
        $options = $this->stockProvider->updateOptionsStockMessage($options);
        
        return $this->getResponse()->setBody(\Zend_Json::encode(['result' => $options]));
    }
}
