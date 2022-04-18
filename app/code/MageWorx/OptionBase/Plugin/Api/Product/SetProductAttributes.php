<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionBase\Plugin\Api\Product;

use Magento\Catalog\Api\Data\ProductExtension;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Event\ManagerInterface;
use MageWorx\OptionBase\Model\Product\Attributes as MageWorxProductAttributes;

/**
 * Class SetProductAttributes
 *
 * @package MageWorx\OptionBase\Plugin\Api\Product
 */
class SetProductAttributes
{
    /**
     * @var ManagerInterface
     */
    protected $eventManager;

    /**
     * @var MageWorxProductAttributes
     */
    protected $mageWorxProductAttributes;

    /**
     * @var Product[]
     */
    protected $instances = [];

    /**
     * @var Product[]
     */
    protected $instancesById = [];

    /**
     * SetProductAttributes constructor.
     *
     * @param ManagerInterface $eventManager
     * @param MageWorxProductAttributes $mageWorxProductAttributes
     */
    public function __construct(
        ManagerInterface $eventManager,
        MageWorxProductAttributes $mageWorxProductAttributes

    ) {
        $this->eventManager              = $eventManager;
        $this->mageWorxProductAttributes = $mageWorxProductAttributes;
    }

    /**
     * @param \Magento\Catalog\Model\ProductRepository $subject
     * @param $result
     * @param ProductInterface $product
     * @return ProductInterface
     */
    public function afterSave(\Magento\Catalog\Model\ProductRepository $subject, $result, ProductInterface $product)
    {
        /** @var ProductExtension $extensionAttributes */
        $extensionAttributes       = $product->getExtensionAttributes()->__toArray();
        $mageWorxProductAttributes = $this->mageWorxProductAttributes->getData();
        foreach ($mageWorxProductAttributes as $attribute) {
            $attributeName = $attribute->getName();
            if (isset($extensionAttributes[$attributeName])) {
                $product->setData($attributeName, $extensionAttributes[$attributeName]);
            }
        }
        $this->eventManager->dispatch(
            'mageworx_attributes_save_trigger',
            ['product' => $product, 'is_after_template' => false]
        );
        return $product;
    }
}
