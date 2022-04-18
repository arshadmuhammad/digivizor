<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\DynamicOptionsBase\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Catalog\Helper\DataFactory as CatalogHelperFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Upgrade Data script
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * Category setup factory
     *
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * UpgradeData constructor.
     *
     * @param CategorySetupFactory $categorySetupFactory
     * @param CatalogHelper $catalogHelper
     */
    public function __construct(
        CategorySetupFactory $categorySetupFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->categorySetupFactory = $categorySetupFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1', '<')) {

            $catalogSetup = $this->categorySetupFactory->create(['setup' => $setup]);
            $catalogSetup->updateAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'price_per_unit',
                'is_global',
                $this->isPriceGlobal() ?
                    ScopedAttributeInterface::SCOPE_GLOBAL : ScopedAttributeInterface::SCOPE_WEBSITE
            );
        }
    }

    /**
     * Is Global Price
     *
     * @return bool
     */
    public function isPriceGlobal()
    {
        return $this->getPriceScope() == \Magento\Catalog\Helper\Data::PRICE_SCOPE_GLOBAL;
    }

    /**
     * Retrieve Catalog Price Scope
     *
     * @return int|null
     */
    public function getPriceScope()
    {
        $priceScope = $this->scopeConfig->getValue(
            \Magento\Catalog\Helper\Data::XML_PATH_PRICE_SCOPE,
            ScopeInterface::SCOPE_STORE
        );
        return isset($priceScope) ? (int)$priceScope : null;
    }
}
