<?php


namespace MagArs\Tweaks\Setup;


use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface {

    protected $categorySetupFactory;

    public function __construct(
        \Magento\Catalog\Setup\CategorySetupFactory $categorySetupFactory
    ) {
        $this->categorySetupFactory = $categorySetupFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
        if(version_compare($context->getVersion(), '1.0.1', '<')) {
            $setup = $this->categorySetupFactory->create(['setup' => $setup]);
            $setup->addAttribute(
                \Magento\Catalog\Model\Category::ENTITY,
                'category_name_2',
                [
                    'type' => 'text',
                    'label' => 'Category Name 2',
                    'input' => 'text',
                    'sort_order' => 1,
                    'global' => ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'General Information',
                ]
            );
        }
    }
}
