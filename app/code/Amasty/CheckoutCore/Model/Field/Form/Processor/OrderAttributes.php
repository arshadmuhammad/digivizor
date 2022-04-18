<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

declare(strict_types=1);

namespace Amasty\CheckoutCore\Model\Field\Form\Processor;

use Amasty\CheckoutCore\Model\Field;
use Amasty\CheckoutCore\Model\Field\Form\GetOrderAttributes;
use Amasty\CheckoutCore\Model\Field\SetAttributeFrontendLabel;
use Amasty\CheckoutCore\Model\ModuleEnable;
use Magento\Eav\Model\ResourceModel\Entity\Attribute as AttributeResource;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class OrderAttributes implements ProcessorInterface
{
    /**
     * @var ModuleEnable
     */
    private $moduleEnable;

    /**
     * @var GetOrderAttributes
     */
    private $getOrderAttributes;

    /**
     * @var SetAttributeFrontendLabel
     */
    private $setAttributeFrontendLabel;

    /**
     * @var AttributeResource
     */
    private $attributeResource;

    public function __construct(
        ModuleEnable $moduleEnable,
        GetOrderAttributes $getOrderAttributes,
        SetAttributeFrontendLabel $setAttributeFrontendLabel,
        AttributeResource $attributeResource
    ) {
        $this->moduleEnable = $moduleEnable;
        $this->getOrderAttributes = $getOrderAttributes;
        $this->setAttributeFrontendLabel = $setAttributeFrontendLabel;
        $this->attributeResource = $attributeResource;
    }

    public function process(array $fields, int $storeId): array
    {
        if (!$this->moduleEnable->isOrderAttributesEnable() || empty($fields)) {
            return $fields;
        }

        $attributes = $this->getOrderAttributes->execute($storeId);
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

            $attribute->setData('is_visible_on_front', $fieldData['enabled']);
            $attribute->setData('sorting_order', $fieldData['sort_order']);

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
