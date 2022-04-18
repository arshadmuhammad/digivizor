<?php

namespace Amasty\Amp\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Amasty\Amp\Helper\Deploy;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var Deploy
     */
    private $pubDeployer;

    /**
     * @var UpgradeData\AddPredefinedHome
     */
    private $addPredefinedHome;

    public function __construct(
        Deploy $pubDeployer,
        \Amasty\Amp\Setup\UpgradeData\AddPredefinedHome $addPredefinedHome
    ) {
        $this->pubDeployer = $pubDeployer;
        $this->addPredefinedHome = $addPredefinedHome;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        //run each upgrade. refresh font styles
        $this->pubDeployer->deployFolder(__DIR__ . '/../pub');

        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            $this->addPredefinedHome->execute();
        }

        $setup->endSetup();
    }
}
