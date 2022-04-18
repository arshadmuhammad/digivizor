<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionDependency\Observer;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use MageWorx\OptionBase\Helper\Data as BaseHelper;
use MageWorx\OptionBase\Model\HiddenDependents as HiddenDependentsStorage;
use MageWorx\OptionDependency\Model\HiddenDependents;
use MageWorx\OptionBase\Model\Entity\Base as MageWorxBaseEntity;

class CalculateDependencyState implements ObserverInterface
{
    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var BaseHelper
     */
    protected $baseHelper;

    /**
     * @var MageWorxBaseEntity
     */
    protected $mageWorxBaseEntity;

    /**
     * @var HiddenDependents
     */
    protected $hiddenDependents;

    /**
     * @var HiddenDependentsStorage
     */
    protected $hiddenDependentsStorage;

    /**
     * @param ResourceConnection $resource
     * @param BaseHelper $baseHelper
     * @param MageWorxBaseEntity $mageWorxBaseEntity
     * @param HiddenDependents $hiddenDependents
     * @param HiddenDependentsStorage $hiddenDependentsStorage
     */
    public function __construct(
        ResourceConnection $resource,
        BaseHelper $baseHelper,
        MageWorxBaseEntity $mageWorxBaseEntity,
        HiddenDependents $hiddenDependents,
        HiddenDependentsStorage $hiddenDependentsStorage
    ) {
        $this->resource                = $resource;
        $this->baseHelper              = $baseHelper;
        $this->mageWorxBaseEntity      = $mageWorxBaseEntity;
        $this->hiddenDependents        = $hiddenDependents;
        $this->hiddenDependentsStorage = $hiddenDependentsStorage;
    }

    /**
     * Calculate dependency state for GraphQl query
     *
     * @see \MageWorx\OptionGraphQl\Model\Resolver\DependencyState
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $product        = $observer->getData('product');
        $selectedValues = $observer->getData('selected_values');

        $this->hiddenDependents->calculateHiddenDependents(
            $product,
            $selectedValues
        );

        return $this;
    }
}
