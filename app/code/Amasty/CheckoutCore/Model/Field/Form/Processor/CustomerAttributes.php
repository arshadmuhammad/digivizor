<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

declare(strict_types=1);

namespace Amasty\CheckoutCore\Model\Field\Form\Processor;

use Amasty\CheckoutCore\Model\Field;
use Amasty\CheckoutCore\Model\Field\Form\GetCustomerAttributes;
use Amasty\CheckoutCore\Model\Field\Form\SelectFormCodes;
use Amasty\CheckoutCore\Model\Field\SetAttributeFrontendLabel;
use Amasty\CheckoutCore\Model\ModuleEnable;
use Magento\Customer\Model\ResourceModel\Attribute as AttributeResource;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class CustomerAttributes implements ProcessorInterface
{
    /**
     * @var ModuleEnable
     */
    private $moduleEnable;

    /**
     * @var GetCustomerAttributes
     */
    private $getCustomerAttributes;

    /**
     * @var SetAttributeFrontendLabel
     */
    private $setAttributeFrontendLabel;

    /**
     * @var SelectFormCodes
     */
    private $selectFormCodes;

    /**
     * @var AttributeResource
     */
    private $attributeResource;

    public function __construct(
        ModuleEnable $moduleEnable,
        GetCustomerAttributes $getCustomerAttributes,
        SetAttributeFrontendLabel $setAttributeFrontendLabel,
        SelectFormCodes $selectFormCodes,
        AttributeResource $attributeResource
    ) {
        $this->moduleEnable = $moduleEnable;
        $this->getCustomerAttributes = $getCustomerAttributes;
        $this->setAttributeFrontendLabel = $setAttributeFrontendLabel;
        $this->selectFormCodes = $selectFormCodes;
        $this->attributeResource = $attributeResource;
    }

    public function process(array $fields, int $storeId): array
    {
        if (!$this->moduleEnable->isCustomerAttributesEnable() || empty($fields)) {
            return $fields;
        }

        $attributes = $this->getCustomerAttributes->execute($storeId);
        if (empty($attributes)) {
            return $fields;
        }

        foreach ($attributes as $attribute) {
            $attributeId = (int) $attribute->getAttributeId();

            if (!isset($fields[$attributeId])) {
                continue;
            }

            $fieldData = $fields[$attributeId];
            if (!empty($fieldData['use_default'])) {
                unset($fields[$attributeId]);
                continue;
            }

            $attribute->setData('sort_order', $fieldData['sort_order'] + 1000);
            $attribute->setData('used_in_product_listing', $fieldData['enabled']);
            $attribute->setData('used_in_forms', $this->selectFormCodes->execute($attribute, $fieldData));

            $this->setAttributeFrontendLabel->execute(
                $attribute,
                $storeId,
                $fieldData['label']
            );

            if ($storeId === Field::DEFAULT_STORE_ID) {
                $attribute->setIsRequired(isset($fieldData['required']));
            }

            $this->attributeResource->save($attribute);
            unset($fields[$attributeId]);
        }

        return $fields;
    }
}
