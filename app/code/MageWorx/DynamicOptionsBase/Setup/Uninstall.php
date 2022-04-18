<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\DynamicOptionsBase\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use MageWorx\DynamicOptionsBase\Model\ResourceModel\DynamicOption as DynamicOptionResourceModel;

class Uninstall implements UninstallInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Uninstall constructor.
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function uninstall(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        $connection = $setup->getConnection();

        $connection->dropTable($connection->getTableName(DynamicOptionResourceModel::DYNAMIC_OPTIONS_TABLE));
        $this->removeProductAttributes($setup);

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function removeProductAttributes(SchemaSetupInterface $setup)
    {
        /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create();

        $attributeFrom = $eavSetup->getAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'price_per_unit'
        );

        if (!empty($attributeFrom)) {
            $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'price_per_unit');
        }
    }
}
