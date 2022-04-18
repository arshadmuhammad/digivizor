<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

declare(strict_types=1);

namespace Amasty\CheckoutCore\Model\Field\Form;

use Amasty\CheckoutCore\Model\Field;
use Amasty\CheckoutCore\Model\Field\IsCustomFieldAttribute;
use Amasty\CheckoutCore\Model\Field\SetAttributeFrontendLabel;
use Amasty\CheckoutCore\Model\ResourceModel\Field as FieldResource;
use Amasty\CheckoutCore\Model\ResourceModel\GetCustomerAddressAttributeById;
use Magento\Customer\Model\ResourceModel\Attribute as AttributeResource;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.MissingImport)
 */
class SaveField
{
    /**
     * @var FieldResource
     */
    private $fieldResource;

    /**
     * @var IsCustomFieldAttribute
     */
    private $isCustomFieldAttribute;

    /**
     * @var GetCustomerAddressAttributeById
     */
    private $getCustomerAddressAttributeById;

    /**
     * @var SetAttributeFrontendLabel
     */
    private $setAttributeFrontendLabel;

    /**
     * @var AttributeResource
     */
    private $attributeResource;

    /**
     * @var string[]
     */
    private $allowedKeys;

    /**
     * @param FieldResource $fieldResource
     * @param IsCustomFieldAttribute $isCustomFieldAttribute
     * @param GetCustomerAddressAttributeById $getCustomerAddressAttributeById
     * @param SetAttributeFrontendLabel $setAttributeFrontendLabel
     * @param AttributeResource $attributeResource
     * @param string[] $allowedKeys
     */
    public function __construct(
        FieldResource $fieldResource,
        IsCustomFieldAttribute $isCustomFieldAttribute,
        GetCustomerAddressAttributeById $getCustomerAddressAttributeById,
        SetAttributeFrontendLabel $setAttributeFrontendLabel,
        AttributeResource $attributeResource,
        array $allowedKeys = []
    ) {
        $this->fieldResource = $fieldResource;
        $this->isCustomFieldAttribute = $isCustomFieldAttribute;
        $this->getCustomerAddressAttributeById = $getCustomerAddressAttributeById;
        $this->setAttributeFrontendLabel = $setAttributeFrontendLabel;
        $this->attributeResource = $attributeResource;
        $this->allowedKeys = $allowedKeys;
    }

    /**
     * @param Field $field
     * @param array $fieldData
     * @throws \Exception
     */
    public function execute(Field $field, array $fieldData): void
    {
        if (empty($this->allowedKeys)) {
            throw new \UnexpectedValueException('No keys were allowed');
        }

        if (empty($fieldData)) {
            return;
        }

        $field->addData(array_intersect_key($fieldData, array_flip($this->allowedKeys)));
        $this->fieldResource->save($field);

        $attributeId = (int) $fieldData['attribute_id'];
        if ($this->isCustomFieldAttribute->execute($attributeId)) {
            $attribute = $this->getCustomerAddressAttributeById->execute($attributeId);

            if ($attribute) {
                $this->setAttributeFrontendLabel->execute(
                    $attribute,
                    (int) $fieldData['store_id'],
                    $fieldData['label']
                );

                $this->attributeResource->save($attribute);
            }
        }
    }
}
