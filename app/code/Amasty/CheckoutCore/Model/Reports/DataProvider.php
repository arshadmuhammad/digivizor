<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutCore
*/


namespace Amasty\CheckoutCore\Model\Reports;

use Amasty\CheckoutCore\Model\StatisticManagement;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var array
     */
    protected $loadedData = [];

    /**
     * @var StatisticManagement
     */
    private $statisticManagement;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        StatisticManagement $statisticManagement,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->statisticManagement = $statisticManagement;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->statisticManagement->calculateStatistic();
    }
}
