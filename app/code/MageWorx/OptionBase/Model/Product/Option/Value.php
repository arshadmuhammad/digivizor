<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MageWorx\OptionBase\Model\Product\Option;

use Magento\Framework\Api\ExtensionAttributesFactory;
use MageWorx\OptionBase\Api\Data\ProductCustomOptionValuesInterface;

/**
 * Catalog product option select type model
 *
 * @api
 * */
class Value extends \Magento\Catalog\Model\Product\Option\Value implements ProductCustomOptionValuesInterface
{
    /**
     * Value collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\Option\Value\CollectionFactory
     */
    protected $_valueCollectionFactory;

    /**
     * @var ExtensionAttributesFactory
     */
    protected $extensionAttributesFactory;

    /**
     * @var \Magento\Framework\Api\ExtensionAttributesInterface
     */
    protected $extensionAttributes;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\ResourceModel\Product\Option\Value\CollectionFactory $valueCollectionFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Product\Option\Value\CollectionFactory $valueCollectionFactory,
        ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $valueCollectionFactory,
            $resource,
            $resourceCollection,
            $data
        );

        $this->extensionAttributesFactory = $extensionFactory;

        if (isset($data[self::EXTENSION_ATTRIBUTES_KEY]) && is_array($data[self::EXTENSION_ATTRIBUTES_KEY])) {
            $this->populateExtensionAttributes($data[self::EXTENSION_ATTRIBUTES_KEY]);
        }
    }

    /**
     * Retrieve existing extension attributes object.
     *
     * @return \MageWorx\OptionBase\Api\Data\ProductCustomOptionValuesExtensionInterface|null
     */
    public function getExtensionAttributes() {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     *
     * @param \MageWorx\OptionBase\Api\Data\ProductCustomOptionValuesExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \MageWorx\OptionBase\Api\Data\ProductCustomOptionValuesExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Identifier setter
     *
     * @param mixed $value
     * @return $this
     */
    public function setId($value)
    {
        parent::setId($value);
        return $this->setData('id', $value);
    }

    /**
     * Set an extension attributes object.
     *
     * @param \Magento\Framework\Api\ExtensionAttributesInterface $extensionAttributes
     * @return $this
     */
    protected function _setExtensionAttributes(\Magento\Framework\Api\ExtensionAttributesInterface $extensionAttributes)
    {
        $this->_data[self::EXTENSION_ATTRIBUTES_KEY] = $extensionAttributes;
        return $this;
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magento\Framework\Api\ExtensionAttributesInterface
     */
    protected function _getExtensionAttributes()
    {
        if (!$this->getData(self::EXTENSION_ATTRIBUTES_KEY)) {
            $this->populateExtensionAttributes([]);
        }
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * Instantiate extension attributes object and populate it with the provided data.
     *
     * @param array $extensionAttributesData
     * @return void
     */
    private function populateExtensionAttributes(array $extensionAttributesData = [])
    {
        $extensionAttributes = $this->extensionAttributesFactory->create(get_class($this), $extensionAttributesData);
        $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * @inheritdoc
     */
    public function __sleep()
    {
        return array_diff(parent::__sleep(), ['extensionAttributesFactory']);
    }

    /**
     * @inheritdoc
     */
    public function __wakeup()
    {
        parent::__wakeup();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->extensionAttributesFactory = $objectManager->get(ExtensionAttributesFactory::class);
    }
}
