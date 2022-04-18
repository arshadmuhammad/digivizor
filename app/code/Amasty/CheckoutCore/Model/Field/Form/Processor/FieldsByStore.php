<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

declare(strict_types=1);

namespace Amasty\CheckoutCore\Model\Field\Form\Processor;

use Amasty\CheckoutCore\Model\Field;
use Amasty\CheckoutCore\Model\FieldFactory;
use Amasty\CheckoutCore\Model\Field\Form\SaveField;
use Amasty\CheckoutCore\Model\ResourceModel\Field as FieldResource;
use Amasty\CheckoutCore\Model\ResourceModel\Field\CollectionFactory;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class FieldsByStore implements ProcessorInterface
{
    const USE_DEFAULT = 'use_default';

    /**
     * @var CollectionFactory
     */
    private $fieldCollectionFactory;

    /**
     * @var SaveField
     */
    private $saveField;

    /**
     * @var FieldFactory
     */
    private $fieldFactory;

    /**
     * @var FieldResource
     */
    private $fieldResource;

    public function __construct(
        CollectionFactory $fieldCollectionFactory,
        SaveField $saveField,
        FieldFactory $fieldFactory,
        FieldResource $fieldResource
    ) {
        $this->fieldCollectionFactory = $fieldCollectionFactory;
        $this->saveField = $saveField;
        $this->fieldFactory = $fieldFactory;
        $this->fieldResource = $fieldResource;
    }

    public function process(array $fields, int $storeId): array
    {
        if (empty($fields)) {
            return [];
        }

        if ($storeId === Field::DEFAULT_STORE_ID) {
            return $fields;
        }

        $fieldCollection = $this->fieldCollectionFactory->create();
        $fieldCollection->addFilterByStoreId($storeId);

        $defaultFieldCollection = $this->fieldCollectionFactory->create();
        $defaultFieldCollection->addFilterByStoreId(Field::DEFAULT_STORE_ID);

        foreach ($fields as $attributeId => $fieldData) {
            $field = $fieldCollection->getItemByColumnValue(Field::ATTRIBUTE_ID, (string) $attributeId);
            $defaultField = $defaultFieldCollection->getItemByColumnValue(
                Field::ATTRIBUTE_ID,
                (string) $attributeId
            );

            $this->processFieldData($field, $defaultField, $fieldData, $attributeId, $storeId);
            unset($fields[$attributeId]);
        }

        return $fields;
    }

    /**
     * @param Field|null $field
     * @param Field $defaultField
     * @param array $fieldData
     * @param int $attributeId
     * @param int $storeId
     * @throws \Exception
     */
    private function processFieldData(
        ?Field $field,
        Field $defaultField,
        array $fieldData,
        int $attributeId,
        int $storeId
    ): void {
        if (!isset($fieldData[self::USE_DEFAULT])) {
            if (!$field) {
                $field = $this->fieldFactory->create();
            }

            $this->saveField->execute(
                $field,
                $this->prepareFieldData($fieldData, $attributeId, $storeId)
            );

            return;
        }

        if ($field) {
            $this->processWithField($field, $fieldData, $attributeId, $storeId);
            return;
        }

        if ((int) $defaultField->getData(Field::ENABLED) === (int) $fieldData[Field::ENABLED]) {
            return;
        }

        $this->saveField->execute(
            $this->fieldFactory->create(),
            $this->prepareFieldData($fieldData, $attributeId, $storeId)
        );
    }

    /**
     * @param Field|null $field
     * @param array $fieldData
     * @param int $attributeId
     * @param int $storeId
     * @throws \Exception
     */
    private function processWithField(
        ?Field $field,
        array $fieldData,
        int $attributeId,
        int $storeId
    ): void {
        if ((int) $field->getData(Field::ENABLED) === (int) $fieldData[Field::ENABLED]) {
            $this->fieldResource->delete($field);
            return;
        }

        $this->saveField->execute(
            $field,
            $this->prepareFieldData($fieldData, $attributeId, $storeId)
        );
    }

    private function prepareFieldData(array $fieldData, int $attributeId, int $storeId): array
    {
        $fieldData[Field::REQUIRED] = isset($fieldData[Field::REQUIRED]);
        $fieldData[Field::ATTRIBUTE_ID] = $fieldData[Field::ATTRIBUTE_ID] ?? $attributeId;
        $fieldData[Field::STORE_ID] = $storeId;
        return $fieldData;
    }
}
