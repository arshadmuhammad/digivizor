<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

declare(strict_types=1);

namespace Amasty\CheckoutCore\Setup\Patch\Data;

use Amasty\CheckoutCore\Model\Field;
use Amasty\CheckoutCore\Model\ResourceModel\Field as FieldResource;
use Magento\Customer\Helper\Address;
use Magento\Customer\Model\ResourceModel\Address\Attribute\CollectionFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddAttributesToManageCheckoutFields implements DataPatchInterface
{
    /**
     * @var Address
     */
    private $customerAddress;

    /**
     * @var CollectionFactory
     */
    private $attributeCollectionFactory;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var Field
     */
    private $fieldSingleton;

    public function __construct(
        Address $customerAddress,
        CollectionFactory $attributeCollectionFactory,
        ResourceConnection $resourceConnection,
        Field $fieldSingleton
    ) {
        $this->customerAddress = $customerAddress;
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->resourceConnection = $resourceConnection;
        $this->fieldSingleton = $fieldSingleton;
    }

    /**
     * @return void
     */
    public function apply()
    {
        $connection = $this->resourceConnection->getConnection();
        $fieldTable = $this->resourceConnection->getTableName(FieldResource::MAIN_TABLE);
        $select = $connection->select()->from($fieldTable)->limit(1);
        if ($connection->fetchOne($select) > 0) {
            return;
        }

        /** @var \Magento\Customer\Model\ResourceModel\Address\Attribute\Collection $attributes */
        $attributes = $this->attributeCollectionFactory->create();
        $inheritedAttributes = $this->fieldSingleton->getInheritedAttributes();

        /** @var \Magento\Customer\Model\Attribute $attribute */
        foreach ($attributes as $attribute) {
            $code = $attribute->getAttributeCode();

            if (isset($inheritedAttributes[$code])) {
                continue;
            }

            if ($code === 'vat_id') {
                $code = 'taxvat';
            }

            if ($code === 'fax') {
                $isEnabled = false;
            } else {
                if (in_array($code, ['prefix', 'suffix', 'middlename', 'taxvat'])) {
                    $isEnabled = (bool)$this->customerAddress->getConfig($code)
                        || $this->isSettingEnabled('customer/address/' . $code . '_show');
                } else {
                    $isEnabled = true;
                }
            }

            $bind = [
                'attribute_id' => $attribute->getId(),
                'label'        => $attribute->getDefaultFrontendLabel(),
                'sort_order'   => $attribute->getSortOrder(),
                'required'     => $attribute->getIsRequired(),
                'width'        => 100,
                'enabled'      => $isEnabled
            ];

            $connection->insert($fieldTable, $bind);
        }
    }

    /**
     * @return string[]
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @return string[]
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @param string $setting
     * @return bool
     */
    private function isSettingEnabled(string $setting): bool
    {
        $connection = $this->resourceConnection->getConnection();

        $select = $connection->select()->from(
            $this->resourceConnection->getTableName('core_config_data'),
            'COUNT(*)'
        )->where(
            'path=?',
            $setting
        )->where(
            'value NOT LIKE ?',
            '0'
        );

        return $connection->fetchOne($select) > 0;
    }
}
