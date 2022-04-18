<?php

/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionBase\Plugin;

use MageWorx\OptionBase\Model\ResourceModel\CollectionUpdaterRegistry;
use Magento\Catalog\Model\ResourceModel\Product\Option\Value\CollectionFactory as OptionValueCollectionFactory;
use Magento\Store\Model\StoreManagerInterface as StoreManager;
use MageWorx\OptionBase\Helper\System as SystemHelper;
use Magento\Framework\Serialize\Serializer\Json as Serializer;
use MageWorx\OptionBase\Model\ConditionValidator;

class CollectProductOptionConditions
{
    /**
     * @var CollectionUpdaterRegistry
     */
    private $collectionUpdaterRegistry;

    /**
     * @var OptionValueCollectionFactory
     */
    protected $optionValueCollectionFactory;

    /**
     * @var StoreManager
     */
    protected $storeManager;

    /**
     * @var SystemHelper
     */
    protected $systemHelper;

    /**
     * @var array
     */
    protected $valuesCollectionCache = [];

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var ConditionValidator
     */
    protected $conditionValidator;

    /**
     * @var string
     */
    protected $customerGroupId;

    /**
     * @param CollectionUpdaterRegistry $collectionUpdaterRegistry
     * @param OptionValueCollectionFactory $optionValueCollectionFactory
     * @param SystemHelper $systemHelper
     * @param StoreManager $storeManager
     * @param \Magento\Framework\App\State $state
     * @param Serializer $serializer
     * @param ConditionValidator $conditionValidator
     */
    public function __construct(
        CollectionUpdaterRegistry $collectionUpdaterRegistry,
        OptionValueCollectionFactory $optionValueCollectionFactory,
        SystemHelper $systemHelper,
        StoreManager $storeManager,
        \Magento\Framework\App\State $state,
        Serializer $serializer,
        ConditionValidator $conditionValidator
    ) {
        $this->collectionUpdaterRegistry    = $collectionUpdaterRegistry;
        $this->optionValueCollectionFactory = $optionValueCollectionFactory;
        $this->systemHelper                 = $systemHelper;
        $this->storeManager                 = $storeManager;
        $this->state                        = $state;
        $this->serializer                   = $serializer;
        $this->conditionValidator           = $conditionValidator;
    }

    /**
     * Set product ID to collection updater registry for future use in collection updaters
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Option\Collection $object
     * @param integer $productId
     * @param integer $storeId
     * @param bool $requiredOnly
     * @return array
     */
    public function beforeGetProductOptions($object, $productId, $storeId, $requiredOnly = false)
    {
        $this->collectionUpdaterRegistry->setCurrentEntityIds([$productId]);
        $this->collectionUpdaterRegistry->setCurrentEntityType('product');

        if ($this->systemHelper->isOptionImportAction()) {
            $this->collectionUpdaterRegistry->setOptionIds([]);
            $this->collectionUpdaterRegistry->setOptionValueIds([]);
        }

        return [$productId, $storeId, $requiredOnly];
    }

    /**
     * Set option/option value IDs to collection updater registry for future use in collection updaters
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Option\Collection $subject
     * @param \Closure $proceed
     * @param integer $storeId
     * @return \Magento\Catalog\Model\ResourceModel\Product\Option\Collection
     */
    public function aroundAddValuesToResult($subject, \Closure $proceed, $storeId = null)
    {
        if ($storeId === null) {
            $storeId = $this->storeManager->getStore()->getId();
        }
        $optionIds = [];
        foreach ($subject as $option) {
            if (!$option->getId()) {
                continue;
            }
            $optionIds[] = $option->getId();
        }

        if ($optionIds) {
            $this->collectionUpdaterRegistry->setOptionIds($optionIds);
        }

        if (!empty($optionIds)) {
            /** @var \Magento\Catalog\Model\ResourceModel\Product\Option\Value\Collection $values */
            $values = $this->optionValueCollectionFactory->create();
            $values->addTitleToResult(
                $storeId
            )->addPriceToResult(
                $storeId
            )->addOptionToFilter(
                $optionIds
            )->setOrder(
                'sort_order',
                'asc'
            )->setOrder(
                'title',
                'asc'
            );

            if ('frontend' === $this->state->getAreaCode()) {

                $hash = hash('sha256', $values->getSelect()->__toString());

                if (!empty($this->valuesCollectionCache[$hash])) {
                    $values = $this->valuesCollectionCache[$hash];
                } else {
                    $values->load();
                    $this->valuesCollectionCache[$hash] = $values;
                }
            }

            $valueIds = [];

            $isGraphQlRequest = false;
            if ('graphql' === $this->state->getAreaCode()) {
                $this->customerGroupId = (int)$this->systemHelper->resolveCurrentCustomerGroupId();
                $isGraphQlRequest      = true;
            }

            foreach ($values as $value) {
                if (!$value->getOptionTypeId()) {
                    continue;
                }
                $valueIds[] = $value->getOptionTypeId();
                $optionId   = $value->getOptionId();
                $option     = $subject->getItemById($optionId);

                // for graphql requests
                if ($isGraphQlRequest) {
                    $this->setPriceForCurrentCustomerGroup('tier_price', $value);
                    $this->setPriceForCurrentCustomerGroup('special_price', $value);
                }

                if ($option) {
                    $option->addValue($value);
                    $value->setOption($option);
                }
            }

            if ($valueIds) {
                $this->collectionUpdaterRegistry->setOptionValueIds($valueIds);
            }
        }

        return $subject;
    }

    /**
     * Set Advanced Pricing price for current customer group
     *
     * @param string $priceType
     * @param \Magento\Catalog\Model\ResourceModel\Product\Option\Value $value
     * @return void
     */
    protected function setPriceForCurrentCustomerGroup($priceType, $value)
    {
        $optionValuePrice             = $value->getDataByKey('default_price');
        $pricesAdvancedPricing        = $value->getDataByKey($priceType);
        $decodedPricesAdvancedPricing = $pricesAdvancedPricing ? $this->serializer->unserialize(
            $pricesAdvancedPricing
        ) : null;

        if ($decodedPricesAdvancedPricing && is_integer($this->customerGroupId)) {
            $priceForCurrentCustomerGroup = [];
            $priceForAllGroups            = [];

            foreach ($decodedPricesAdvancedPricing as $priceItem) {
                if (!$this->conditionValidator->isValidated(
                    $priceItem,
                    $optionValuePrice
                )
                ) {
                    continue;
                }

                switch ($priceItem['customer_group_id']) {
                    case $this->customerGroupId:
                        $priceForCurrentCustomerGroup = $priceItem;
                        break;
                    case \Magento\Customer\Model\Group::CUST_GROUP_ALL:
                        $priceForAllGroups = $priceItem;
                        break;
                }
            }

            $resultPrice = null;
            if (!empty($priceForCurrentCustomerGroup)) {
                $resultPrice = $this->serializer->serialize($priceForCurrentCustomerGroup);
            } else {
                if (!empty($priceForAllGroups)) {
                    $resultPrice = $this->serializer->serialize($priceForAllGroups);
                }
            }

            $value->setData(
                $priceType,
                $resultPrice
            );
        }
    }
}
