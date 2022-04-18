<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionBase\Plugin;

use MageWorx\OptionBase\Model\ResourceModel\CollectionUpdaterFactory;
use MageWorx\OptionBase\Model\ResourceModel\CollectionUpdaterRegistry;
use Magento\Framework\App\ResourceConnection;

class ExtendOptionCollection
{
    protected $collectionUpdaterFactory;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var CollectionUpdaterRegistry
     */
    protected $collectionUpdaterRegistry;

    /**
     * BeforeLoad constructor.
     *
     * @param CollectionUpdaterFactory $collectionUpdaterFactory
     * @param CollectionUpdaterRegistry $collectionUpdaterRegistry
     * @param ResourceConnection $resource
     */
    public function __construct(
        CollectionUpdaterFactory $collectionUpdaterFactory,
        CollectionUpdaterRegistry $collectionUpdaterRegistry,
        ResourceConnection $resource
    ) {
        $this->collectionUpdaterFactory  = $collectionUpdaterFactory;
        $this->collectionUpdaterRegistry = $collectionUpdaterRegistry;
        $this->resource                  = $resource;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Option\Collection $collection
     * @param bool $printQuery
     * @param bool $logQuery
     * @return array
     */
    public function beforeLoad($collection, $printQuery = false, $logQuery = false)
    {
        if (!$this->collectionUpdaterRegistry->getIsAppliedGroupConcat()) {
            $this->resource->getConnection()->query('SET SESSION group_concat_max_len = 100000;');
            $this->collectionUpdaterRegistry->setIsAppliedGroupConcat(true);
        }
        $this->collectionUpdaterFactory->create($collection)->update();

        return [$printQuery, $logQuery];
    }
}
