<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionImportExport\Plugin;

use Magento\ImportExport\Model\ResourceModel\Import\Data as DataSourceModel;
use MageWorx\OptionImportExport\Model\MageTwo\ImportProductRegistry;

class SkipRowsForImportMageTwo
{
    /**
     * @var ImportProductRegistry
     */
    protected $importProductRegistry;

    /**
     * @param ImportProductRegistry $importProductRegistry
     */
    public function __construct(
        ImportProductRegistry $importProductRegistry
    ) {
        $this->importProductRegistry = $importProductRegistry;
    }

    /**
     * Get next bunch of validated rows.
     * Get another bunch of rows if all of 100 rows don't contain product SKU.
     * This is necessary to avoid end of bunch iterations during \Magento\CatalogImportExport\Model\Import\Product::_saveProducts()
     *
     * @param DataSourceModel $subject
     * @param /Closure $proceed
     * @return array|null
     */
    public function aroundGetNextBunch($subject, $proceed)
    {
        $dataRows = $proceed();
        if ($this->importProductRegistry->getIsOptionImport() || !$this->importProductRegistry->isProductAPOImport()) {
            return $dataRows;
        }
        if (empty($dataRows) || !is_array($dataRows)) {
            return $dataRows;
        }
        $filteredRows = $this->filterRows($dataRows);
        while (!$filteredRows && count($dataRows) === 100) {
            $dataRows = $proceed();
            $filteredRows = $this->filterRows($dataRows);
        }
        return $filteredRows;
    }

    /**
     * Filter rows
     *
     * @param array $dataRows
     * @return array
     */
    public function filterRows($dataRows)
    {
        $filteredRows = [];
        foreach ($dataRows as $dataRow) {
            if ((isset($dataRow['sku']) && strlen(trim($dataRow['sku'])))
                || !empty($dataRow['_store'])
            ) {
                $filteredRows[] = $dataRow;
            }
        }
        return $filteredRows;
    }
}