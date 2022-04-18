<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionBase\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use MageWorx\OptionBase\Model\ProductAttributes;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    const MAGEWORX_OPTION_ID                      = 'mageworx_option_id';
    const MAGEWORX_OPTION_TYPE_ID                 = 'mageworx_option_type_id';
    const CATALOG_PRODUCT_OPTION_TABLE            = 'catalog_product_option';
    const CATALOG_PRODUCT_OPTION_TYPE_VALUE_TABLE = 'catalog_product_option_type_value';

    /**
     * @var \MageWorx\OptionBase\Model\Installer
     */
    protected $optionBaseInstaller;

    /**
     * UpgradeSchema constructor.
     *
     * @param \MageWorx\OptionBase\Model\Installer $optionBaseInstaller
     */
    public function __construct(
        \MageWorx\OptionBase\Model\Installer $optionBaseInstaller
    ) {
        $this->optionBaseInstaller = $optionBaseInstaller;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.0.3', '<')) {
            if (!$setup->getConnection()->tableColumnExists(
                $setup->getTable('catalog_product_entity'),
                'mageworx_is_require'
            )) {
                $setup->getConnection()->addColumn(
                    $setup->getTable('catalog_product_entity'),
                    'mageworx_is_require',
                    [
                        'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                        'nullable' => false,
                        'default'  => '0',
                        'comment'  => 'MageWorx Is Required',
                    ]
                );
            }
        }

        if (version_compare($context->getVersion(), '2.0.4', '<')) {
            if ($setup->getConnection()->tableColumnExists(
                $setup->getTable(static::CATALOG_PRODUCT_OPTION_TABLE),
                static::MAGEWORX_OPTION_ID
            )) {
                $triggerName = 'insert_' . static::MAGEWORX_OPTION_ID;
                $setup->getConnection()->dropTrigger($triggerName);
            }

            if ($setup->getConnection()->tableColumnExists(
                $setup->getTable(static::CATALOG_PRODUCT_OPTION_TYPE_VALUE_TABLE),
                static::MAGEWORX_OPTION_TYPE_ID
            )) {
                $triggerName = 'insert_' . static::MAGEWORX_OPTION_TYPE_ID;
                $setup->getConnection()->dropTrigger($triggerName);
            }
        }

        if (version_compare($context->getVersion(), '2.0.5', '<')) {
            if ($setup->getConnection()->isTableExists(
                $setup->getTable('mageworx_optionfeatures_product_attributes')
            )) {
                $tableName = $setup->getTable(ProductAttributes::TABLE_NAME);
                if ($setup->getConnection()->isTableExists($tableName)) {
                    $setup->getConnection()->dropTable($tableName);
                }
                $setup->getConnection()->renameTable(
                    $setup->getTable('mageworx_optionfeatures_product_attributes'),
                    $setup->getTable($tableName)
                );
            }
        }

        $this->optionBaseInstaller->install();

        $setup->endSetup();
    }
}
