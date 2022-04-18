<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/

declare(strict_types=1);

namespace Amasty\CheckoutCore\Model\ResourceModel;

use Magento\Customer\Model\Attribute;
use Magento\Customer\Model\AttributeFactory;
use Magento\Customer\Model\ResourceModel\Attribute as AttributeResource;

class GetCustomerAddressAttributeById
{
    /**
     * @var AttributeFactory
     */
    private $attributeFactory;

    /**
     * @var AttributeResource
     */
    private $attributeResource;

    public function __construct(
        AttributeFactory $attributeFactory,
        AttributeResource $attributeResource
    ) {
        $this->attributeFactory = $attributeFactory;
        $this->attributeResource = $attributeResource;
    }

    public function execute(int $attributeId): ?Attribute
    {
        /** @var Attribute $attribute */
        $attribute = $this->attributeFactory->create();
        $this->attributeResource->load($attribute, $attributeId);

        return (int) $attribute->getId() === $attributeId ? $attribute : null;
    }
}
