<?php

namespace Amasty\Amp\Setup;

use Amasty\Amp\Setup\UpgradeSchema\AddDataForCms;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    private $addDataForCms;

    public function __construct(
        AddDataForCms $addDataForCms
    ) {
        $this->addDataForCms = $addDataForCms;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            $this->addDataForCms->execute($setup);
        }

        $setup->endSetup();
    }
}
