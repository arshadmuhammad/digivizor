<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\DynamicOptionsBase\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use MageWorx\DynamicOptionsBase\Api\Data\DynamicOptionInterface;
use MageWorx\DynamicOptionsBase\Model\Source\MeasurementUnits;
use MageWorx\DynamicOptionsBase\Model\ResourceModel\DynamicOption as DynamicOptionResourceModel;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $this->createDynamicOptionsTable($setup);

        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     */
    public function createDynamicOptionsTable(SchemaSetupInterface $installer)
    {
        /**
         * Create table 'DYNAMIC_OPTIONS_TABLE'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable(DynamicOptionResourceModel::DYNAMIC_OPTIONS_TABLE)
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary'  => true,
            ],
            'ID'
        )
                           ->addColumn(
                               DynamicOptionInterface::PRODUCT_ID,
                               \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                               null,
                               [
                                   'unsigned' => true,
                                   'nullable' => false,
                               ],
                               'Product ID'
                           )->addColumn(
                               DynamicOptionInterface::OPTION_ID,
                               \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                               null,
                               [
                               'unsigned' => true,
                               'nullable' => false,
                               ],
                               'Option ID'
                           )->addColumn(
                               DynamicOptionInterface::STEP,
                               \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                               '10,2',
                               [
                               'unsigned' => true,
                               'nullable' => true,
                               ],
                               'Step'
                           )->addColumn(
                               DynamicOptionInterface::MIN_VALUE,
                               \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                               '10,2',
                               [
                               'unsigned' => true,
                               'nullable' => true,
                               ],
                               'Min Value'
                           )->addColumn(
                               DynamicOptionInterface::MAX_VALUE,
                               \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                               '10,2',
                               [
                               'unsigned' => true,
                               'nullable' => true,
                               ],
                               'Max Value'
                           )->addColumn(
                               DynamicOptionInterface::MEASUREMENT_UNIT,
                               Table::TYPE_TEXT,
                               255,
                               [
                               'nullable' => false,
                               'default'  => MeasurementUnits::METER
                               ],
                               'Measurement Unit'
                           )->addForeignKey(
                               $installer->getFkName(
                                   DynamicOptionResourceModel::DYNAMIC_OPTIONS_TABLE,
                                   DynamicOptionInterface::OPTION_ID,
                                   'catalog_product_option',
                                   'option_id'
                               ),
                               DynamicOptionInterface::OPTION_ID,
                               $installer->getTable('catalog_product_option'),
                               'option_id',
                               Table::ACTION_CASCADE
                           )
                           ->addIndex(
                               $installer->getIdxName(
                                   DynamicOptionResourceModel::DYNAMIC_OPTIONS_TABLE,
                                   [DynamicOptionInterface::PRODUCT_ID, DynamicOptionInterface::OPTION_ID],
                                   \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                               ),
                               [DynamicOptionInterface::PRODUCT_ID, DynamicOptionInterface::OPTION_ID],
                               ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
                           )->setComment(
                               'Mageworx Dynamic Options Table'
                           );
        $installer->getConnection()->createTable($table);
    }
}
