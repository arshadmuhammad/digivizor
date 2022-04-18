<?php

namespace Amasty\Amp\Observer;

use Magento\Framework\App\ProductMetadata;
use Magento\Framework\Event\ObserverInterface;
use Amasty\Amp\Model\ConfigProvider;

class LayoutLoadBefore implements ObserverInterface
{
    /**
     * @var ConfigProvider
     */
    private $config;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetadata;

    public function __construct(
        ConfigProvider $config,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata
    ) {
        $this->config = $config;
        $this->productMetadata = $productMetadata;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->config->isAmpEnabledOnCurrentPage($observer->getData('full_action_name'))) {
            $update = $observer->getEvent()->getLayout()->getUpdate();
            $this->updateHandles($update);
        }
    }

    /**
     * @param $update
     */
    private function updateHandles($update)
    {
        foreach ($update->getHandles() as $handleName) {
            $type = $this->productMetadata->getEdition() === ProductMetadata::EDITION_NAME ? 'ce' : 'ee';
            $update->removeHandle($handleName);
            if ($handleName == ConfigProvider::CATALOG_PRODUCT_VIEW) {
                $handleName = $type . '_' . $handleName;
            }

            $update->addHandle('amasty_amp_' . $handleName);
        }
    }
}
