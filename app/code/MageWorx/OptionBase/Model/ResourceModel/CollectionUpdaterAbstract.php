<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionBase\Model\ResourceModel;

use \Magento\Catalog\Model\ResourceModel\Product\Option\Collection as OptionCollection;
use \Magento\Catalog\Model\ResourceModel\Product\Option\Value\Collection as ValueCollection;
use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\App\ResourceConnection;
use MageWorx\OptionBase\Model\ResourceModel\CollectionUpdaterRegistry;
use MageWorx\OptionBase\Model\Product\Option\CollectionUpdaters as OptionCollectionUpdaters;
use MageWorx\OptionBase\Model\Product\Option\Value\CollectionUpdaters as ValueCollectionUpdaters;
use MageWorx\OptionBase\Helper\Data;

abstract class CollectionUpdaterAbstract
{
    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var OptionCollection|ValueCollection
     */
    protected $collection;

    /**
     * @var CollectionUpdaterRegistry
     */
    protected $collectionUpdaterRegistry;

    /**
     * @var OptionCollectionUpdaters
     */
    protected $optionCollectionUpdaters;

    /**
     * @var ValueCollectionUpdaters
     */
    protected $valueCollectionUpdaters;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var array
     */
    protected $conditions;

    /**
     * @param $collection AbstractCollection
     * @param ResourceConnection $resource
     * @param CollectionUpdaterRegistry $collectionUpdaterRegistry
     * @param OptionCollectionUpdaters $optionCollectionUpdaters
     * @param ValueCollectionUpdaters $valueCollectionUpdaters
     * @param Data $helperData
     * @param array $conditions
     */
    public function __construct(
        AbstractCollection $collection,
        ResourceConnection $resource,
        CollectionUpdaterRegistry $collectionUpdaterRegistry,
        OptionCollectionUpdaters $optionCollectionUpdaters,
        ValueCollectionUpdaters $valueCollectionUpdaters,
        Data $helperData,
        $conditions = []
    ) {
        $this->collection                = $collection;
        $this->resource                  = $resource;
        $this->collectionUpdaterRegistry = $collectionUpdaterRegistry;
        $this->optionCollectionUpdaters  = $optionCollectionUpdaters;
        $this->valueCollectionUpdaters   = $valueCollectionUpdaters;
        $this->conditions                = $conditions;
        $this->helperData                = $helperData;
    }

    abstract public function process();

    /**
     * Add updaters to collection
     *
     * @return void
     */
    public function update()
    {
        $entityIds  = $this->collectionUpdaterRegistry->getCurrentEntityIds() ?: [];
        $entityType = $this->collectionUpdaterRegistry->getCurrentEntityType() ?: 'product';

        $optionValueIds = $this->collectionUpdaterRegistry->getOptionValueIds();
        $optionIds      = $this->collectionUpdaterRegistry->getOptionIds();

        $this->conditions['value_id']    = $optionValueIds ? $optionValueIds : [];
        $this->conditions['option_id']   = $optionIds ? $optionIds : [];
        $this->conditions['entity_ids']  = $entityIds;
        $this->conditions['entity_type'] = $entityType;
        $this->conditions['row_ids']     = $this->collectionUpdaterRegistry->getCurrentRowIds();

        if (empty($optionIds)) {
            $this->collectOptionConditions();
        }

        $this->process();
    }

    /**
     * Collect option IDs condition by link ID
     *
     * @return void
     */
    protected function collectOptionConditions()
    {
        if ($this->conditions['entity_type'] === 'group') {
            $linkField = 'group_id';
            $linkIds = $this->conditions['entity_ids'];
            $tableName = $this->resource->getTableName('mageworx_optiontemplates_group_option');
        } else {
            $linkField = 'product_id';
            if ($this->helperData->isEnterprise()) {
                $linkIds = $this->conditions['row_ids'];
            } else {
                $linkIds = $this->conditions['entity_ids'];
            }
            $tableName = $this->resource->getTableName('catalog_product_option');
        }

        if ($linkIds) {
            $linkFieldCondition = $linkField . $this->helperData->getComparisonConditionPart($linkIds);

            $select = $this->resource->getConnection()
                                     ->select()
                                     ->from(
                                         $tableName,
                                         'option_id'
                                     )
                                     ->where($linkFieldCondition);

            $this->conditions['option_id'] = $this->resource->getConnection()->fetchCol($select);
        }
    }
}
