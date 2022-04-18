<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\DynamicOptionsBase\Setup;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Eav\Setup\EavSetupFactory;

/**
 * Class InstallData
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * InstallData constructor.
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Installs data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->setup = $setup;

        $setup->startSetup();

        /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create();

        $attributeSetId = $eavSetup->getDefaultAttributeSetId(ProductAttributeInterface::ENTITY_TYPE_CODE);
        // Creating attribute group named
        $eavSetup->addAttributeGroup(
            ProductAttributeInterface::ENTITY_TYPE_CODE,
            $attributeSetId,
            "Mageworx Dynamic Options",
            40
        );

        $attributeFrom = $eavSetup->getAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'price_per_unit'
        );
        if (empty($attributeFrom)) {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'price_per_unit',
                [
                    'group'                    => "mageworx-dynamic-options",
                    'type'                     => 'text',
                    'label'                    => 'Price Per Unit',
                    'input'                    => 'text',
                    'required'                 => false,
                    'sort_order'               => 20,
                    'global'                   => ScopedAttributeInterface::SCOPE_STORE,
                    'is_used_in_grid'          => false,
                    'is_visible_in_grid'       => false,
                    'is_filterable_in_grid'    => false,
                    'visible'                  => true,
                    'is_html_allowed_on_front' => false,
                    'visible_on_front'         => false,
                    'default'                  => '0',
                    'system'                   => 0,
                    'user_defined'             => false
                ]
            );
        } else {
            $eavSetup->addAttributeToGroup(
                \Magento\Catalog\Model\Product::ENTITY,
                $attributeSetId,
                "mageworx-dynamic-options",
                'price_per_unit',
                20
            );
        }

        $setup->endSetup();
    }
}
