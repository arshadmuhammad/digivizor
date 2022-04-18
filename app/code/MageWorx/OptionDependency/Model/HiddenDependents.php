<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionDependency\Model;

use Magento\Framework\DataObject;
use MageWorx\OptionBase\Helper\Data as BaseHelper;
use MageWorx\OptionBase\Model\HiddenDependents as HiddenDependentsStorage;

class HiddenDependents
{
    /**
     * @var BaseHelper
     */
    protected $baseHelper;

    /**
     * @var HiddenDependentsStorage
     */
    protected $hiddenDependentsStorage;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var array
     */
    protected $hiddenValues;

    /**
     * @var array
     */
    protected $hiddenOptions;

    /**
     * @var array
     */
    protected $selectedValues;

    /**
     * @var array
     */
    protected $optionToValuesMap;

    /**
     * @var array
     */
    protected $valueToOptionMap;

    /**
     * @var array
     */
    protected $dependencyRules;

    /**
     * @var boolean
     */
    protected $hasConfigureQuoteItemsHiddenDependents;

    /**
     * @param BaseHelper $baseHelper
     * @param HiddenDependentsStorage $hiddenDependentsStorage
     */
    public function __construct(
        BaseHelper $baseHelper,
        HiddenDependentsStorage $hiddenDependentsStorage
    ) {
        $this->baseHelper              = $baseHelper;
        $this->hiddenDependentsStorage = $hiddenDependentsStorage;
    }

    /**
     * Get hidden dependents
     *
     * @used to hide options/values on their backend rendering
     *
     * @param array $options
     * @param array $dependencyRules
     * @param array $isDefaults
     * @return array
     */
    public function getHiddenDependents($options, $dependencyRules, $isDefaults)
    {
        $this->data = [
            'hidden_options'     => [],
            'hidden_values'      => [],
            'preselected_values' => []
        ];

        if (!$options || !is_array($options)) {
            return $this->data;
        }

        $this->selectedValues    = [];
        $this->optionToValuesMap = [];
        $this->valueToOptionMap  = [];
        $this->dependencyRules   = $dependencyRules;

        $this->collectOptionToValuesMap($options);
        $this->processDependencyRules();
        $this->processIsDefaults($isDefaults);

        $this->data = [
            'hidden_options'     => array_values($this->hiddenOptions),
            'hidden_values'      => array_values($this->hiddenValues),
            'preselected_values' => $this->getPreparedSelectedValues()
        ];

        return $this->data;
    }

    /**
     * Get value to option map from option's array
     *
     * @param array $options
     * @return array
     */
    public function getValueOptionMaps($options)
    {
        $this->optionToValuesMap = [];
        $this->valueToOptionMap  = [];
        $this->collectOptionToValuesMap($options);
        $maps = [
            'valueToOption' => $this->valueToOptionMap,
            'optionToValue' => $this->optionToValuesMap
        ];

        return $maps;
    }


    /**
     * Collect option to values map
     *
     * @param array $options
     * @return void
     */
    protected function collectOptionToValuesMap($options)
    {
        foreach ($options as $option) {
            if (!isset($option['option_id'])
                || empty($option['values'])
                || !empty($option['is_disabled'])
            ) {
                continue;
            }
            $valueIds = [];
            foreach ($option['values'] as $value) {
                if (!isset($value['option_type_id'])
                    || !empty($value['is_disabled'])
                ) {
                    continue;
                }
                $valueIds[]                                       = $value['option_type_id'];
                $this->valueToOptionMap[$value['option_type_id']] = $option['option_id'];
            }
            $this->optionToValuesMap[$option['option_id']] = $valueIds;
        }
    }

    /**
     * Process dependency rules
     *
     * @return void
     */
    protected function processDependencyRules()
    {
        $this->hiddenValues  = [];
        $this->hiddenOptions = [];
        foreach ($this->dependencyRules as $dependencyRule) {
            if ($dependencyRule['condition_type'] === 'or') {
                $this->processDependencyOrRules($dependencyRule);
            } elseif ($dependencyRule['condition_type'] === 'and') {
                $this->processDependencyAndRules($dependencyRule);
            }
        }

        $this->hideOptionIfAllValuesHidden();
    }

    /**
     * Process dependency OR-rules
     *
     * @param array $dependencyRule
     * @return void
     */
    protected function processDependencyOrRules($dependencyRule)
    {
        $isConvertedToAndCondition = false;
        $areConditionsNotPassed    = false;
        foreach ($dependencyRule['conditions'] as $item) {
            $conditionOptionValues = $item['values'];
            if (!$conditionOptionValues
                && !empty($item['id'])
                && !empty($this->optionToValuesMap[$item['id']])
            ) {
                $conditionOptionValues     = $this->optionToValuesMap[$item['id']];
                $isConvertedToAndCondition = true;
            }

            if ($item['type'] === '!eq') {
                foreach ($conditionOptionValues as $conditionOptionValueId) {
                    if ($isConvertedToAndCondition) {
                        if (in_array($conditionOptionValueId, $this->selectedValues)) {
                            $areConditionsNotPassed = true;
                            break;
                        }
                    } else {
                        if (!in_array($conditionOptionValueId, $this->selectedValues)) {
                            $this->addHiddenValuesByRule($dependencyRule);
                        }
                    }
                }
                if ($isConvertedToAndCondition && !$areConditionsNotPassed) {
                    $this->addHiddenValuesByRule($dependencyRule);
                }
            } elseif ($item['type'] === 'eq') {
                foreach ($conditionOptionValues as $conditionOptionValueId) {
                    if (in_array($conditionOptionValueId, $this->selectedValues)) {
                        $this->addHiddenValuesByRule($dependencyRule);
                    }
                }
            }
        }
    }

    /**
     * Process dependency AND-rules
     *
     * @param array $dependencyRule
     * @return void
     */
    protected function processDependencyAndRules($dependencyRule)
    {
        $areConditionsPassed = true;
        foreach ($dependencyRule['conditions'] as $item) {
            $conditionOptionValues = $item['values'];
            if (!$conditionOptionValues
                && !empty($item['id'])
                && !empty($this->optionToValuesMap[$item['id']])
            ) {
                $conditionOptionValues = $this->optionToValuesMap[$item['id']];
            }

            if ($item['type'] === '!eq') {
                foreach ($conditionOptionValues as $conditionOptionValueId) {
                    if (in_array($conditionOptionValueId, $this->selectedValues)) {
                        $areConditionsPassed = false;
                        break 2;
                    }
                }
            } elseif ($item['type'] === 'eq') {
                foreach ($conditionOptionValues as $conditionOptionValueId) {
                    if (!in_array($conditionOptionValueId, $this->selectedValues)) {
                        $areConditionsPassed = false;
                        break 2;
                    }
                }
            }
        }
        if ($areConditionsPassed) {
            $this->addHiddenValuesByRule($dependencyRule);
        }
    }

    /**
     * Add hidden values by dependency rule
     *
     * @param array $dependencyRule
     * @return void
     */
    protected function addHiddenValuesByRule($dependencyRule)
    {
        foreach ($dependencyRule['actions']['hide'] as $hideItem) {
            if (!empty($hideItem['values']) && is_array($hideItem['values'])) {
                foreach ($hideItem['values'] as $hideValueId) {
                    if ($hideValueId) {
                        $this->hiddenValues[(int)$hideValueId] = (int)$hideValueId;
                    }
                }
            } else {
                if (!empty($hideItem['id'])) {
                    $this->hiddenOptions[(int)$hideItem['id']] = (int)$hideItem['id'];
                }
                if (!empty($this->optionToValuesMap[$hideItem['id']])
                    && is_array($this->optionToValuesMap[$hideItem['id']])
                ) {
                    foreach ($this->optionToValuesMap[$hideItem['id']] as $hideValueId) {
                        if ($hideValueId) {
                            $this->hiddenValues[(int)$hideValueId] = (int)$hideValueId;
                        }
                    }
                }
            }
        }
    }

    /**
     * Hide option if all values are hidden
     *
     * @return void
     */
    protected function hideOptionIfAllValuesHidden()
    {
        if (empty($this->optionToValuesMap) || !is_array($this->optionToValuesMap)) {
            return;
        }

        foreach ($this->optionToValuesMap as $optionId => $valueIds) {
            if (!$valueIds) {
                continue;
            }
            $areAllValuesHidden = true;
            foreach ($valueIds as $valueId) {
                if (!in_array($valueId, $this->hiddenValues) || substr($valueId, 0, 1) === 'o') {
                    $areAllValuesHidden = false;
                    break;
                }
            }
            if ($areAllValuesHidden) {
                $this->hiddenOptions[(int)$optionId] = (int)$optionId;
            }
        }
    }

    /**
     * Process IsDefaults, rerun dependency rules and isDefaults check, if new selected value is added
     *
     * @param array $isDefaults
     * @return void
     */
    protected function processIsDefaults($isDefaults)
    {
        if (!$isDefaults || !is_array($isDefaults)) {
            return;
        }

        $isAddedNewSelectedValue = false;
        foreach ($isDefaults as $isDefault) {
            if (empty($isDefault['is_default'])
                || in_array($isDefault['option_type_id'], $this->selectedValues)
                || in_array($isDefault['option_type_id'], $this->hiddenValues)
                || empty($this->valueToOptionMap[$isDefault['option_type_id']])
            ) {
                continue;
            }

            $this->selectedValues[] = $isDefault['option_type_id'];

            $isAddedNewSelectedValue = true;
        }

        if ($isAddedNewSelectedValue) {
            $this->processDependencyRules();
            $this->processIsDefaults($isDefaults);
        }
    }

    /**
     * Get prepared selected values
     *
     * @return array
     */
    protected function getPreparedSelectedValues()
    {
        $data = [];
        foreach ($this->selectedValues as $selectedValue) {
            if (!isset($this->valueToOptionMap[$selectedValue])) {
                continue;
            }
            $data[$this->valueToOptionMap[$selectedValue]][] = (int)$selectedValue;
        }
        return $data;
    }

    /**
     * Calculate hidden dependents for ShareableLink or GraphQL's "dependencyState" query
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param array $preselectedValues
     *
     * @return void
     */
    public function calculateHiddenDependents($product, $preselectedValues)
    {
        $this->data = [
            'hidden_options'     => [],
            'hidden_values'      => [],
            'preselected_values' => []
        ];

        $options = $this->getOptionsAsArray($product->getOptions());
        if (!$options || !is_array($options)) {
            return;
        }

        $dependencyRulesJson = $product->getDependencyRules();
        try {
            $dependencyRules = $this->baseHelper->jsonDecode($dependencyRulesJson);
        } catch (\Exception $exception) {
            $dependencyRules = [];
        }

        $this->selectedValues    = [];
        $this->optionToValuesMap = [];
        $this->valueToOptionMap  = [];
        $this->dependencyRules   = $dependencyRules;

        $this->collectOptionToValuesMap($options);
        $this->selectedValues = $preselectedValues;
        $this->processDependencyRules();

        $this->data = [
            'hidden_options'     => array_values($this->hiddenOptions),
            'hidden_values'      => array_values($this->hiddenValues),
            'preselected_values' => $this->getPreparedSelectedValues()
        ];

        $this->hasConfigureQuoteItemsHiddenDependents = true;
        $this->hiddenDependentsStorage->setQuoteItemsHiddenDependents($this->data);

        return;
    }

    /**
     * Calculate configure quote items hidden dependents
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param DataObject $buyRequest
     *
     * @return void
     */
    public function calculateConfigureQuoteItemsHiddenDependents($product, $buyRequest)
    {
        if ($this->hasConfigureQuoteItemsHiddenDependents) {
            return;
        }

        $this->data = [
            'hidden_options'     => [],
            'hidden_values'      => [],
            'preselected_values' => []
        ];

        $options = $this->getOptionsAsArray($product->getOptions());
        if (!$options || !is_array($options)) {
            return;
        }

        $dependencyRulesJson = $product->getDependencyRules();
        try {
            $dependencyRules = $this->baseHelper->jsonDecode($dependencyRulesJson);
        } catch (\Exception $exception) {
            $dependencyRules = [];
        }

        $this->selectedValues    = [];
        $this->optionToValuesMap = [];
        $this->valueToOptionMap  = [];
        $this->dependencyRules   = $dependencyRules;

        $this->collectSelectedValuesFromBuyRequest($buyRequest);
        $this->collectOptionToValuesMap($options);
        $this->processDependencyRules();

        $this->data = [
            'hidden_options'     => array_values($this->hiddenOptions),
            'hidden_values'      => array_values($this->hiddenValues),
            'preselected_values' => $this->getPreparedSelectedValues()
        ];

        $this->hasConfigureQuoteItemsHiddenDependents = true;
        $this->hiddenDependentsStorage->setQuoteItemsHiddenDependents($this->data);

        return;
    }

    /**
     * Collect selected values from quote item's buyRequest
     *
     * @param DataObject $buyRequest
     * @return void
     */
    protected function collectSelectedValuesFromBuyRequest($buyRequest)
    {
        $selectedOptions = $buyRequest->getData('options');
        if (!$selectedOptions || !is_array($selectedOptions)) {
            return;
        }
        foreach ($selectedOptions as $selectedValues) {
            if (empty($selectedValues)) {
                continue;
            }
            if (is_array($selectedValues)) {
                foreach ($selectedValues as $selectedValue) {
                    $this->selectedValues[] = $selectedValue;
                }
            } else {
                $this->selectedValues[] = $selectedValues;
            }
        }
    }

    /**
     * Get configure quote items hidden dependents
     *
     * @return array
     */
    public function getConfigureQuoteItemsHiddenDependents()
    {
        if ($this->hasConfigureQuoteItemsHiddenDependents) {
            return $this->data;
        }
        return [];
    }

    /**
     * Convert option's objects to array and retrieve it.
     *
     * @param array $options
     * @return array
     */
    public function getOptionsAsArray($options)
    {
        if (!$options) {
            $options = [];
        }

        $results = [];

        foreach ($options as $option) {
            if (!is_object($option)) {
                continue;
            }
            $result              = [];
            $result['option_id'] = $option->getOptionId();

            if ($this->baseHelper->isSelectableOption($option->getType()) && $option->getValues()) {
                foreach ($option->getValues() as $value) {
                    $i = $value->getOptionTypeId();
                    foreach ($value->getData() as $valueKey => $valueDatum) {
                        $result['values'][$i][$valueKey] = $valueDatum;
                    }
                }
            } else {
                foreach ($option->getData() as $optionKey => $optionDatum) {
                    $result[$optionKey] = $optionDatum;
                }
                $result['values'] = null;
            }

            $results[$option->getOptionId()] = $result;
        }

        return $results;
    }
}
