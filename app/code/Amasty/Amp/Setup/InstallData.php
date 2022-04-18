<?php

namespace Amasty\Amp\Setup;

use Amasty\Base\Model\MagentoVersion as MagentoVersion;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Exception\LocalizedException;

class InstallData implements InstallDataInterface
{
    /**
     * @var MagentoVersion
     */
    private $magentoVersion;

    public function __construct(MagentoVersion $magentoVersion)
    {
        $this->magentoVersion = $magentoVersion;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @throws LocalizedException
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        if (version_compare($this->magentoVersion->get(), '2.3.0', '<')) {
            throw new LocalizedException(
                __(
                    "\nThe current version of Magento is not supported."
                    . " The AMP extension is designed only for Magento versions starting with 2.3 and higher.\n"
                    . "Please contact Amasty Support Team to get information about"
                    . " the custom solutions or please update Magento to 2.3 or higher.\n"
                )
            );
        }
    }
}
