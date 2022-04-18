<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionDependency\Model;

use Magento\Framework\App\ResourceConnection;
use MageWorx\OptionDependency\Model\Config;
use MageWorx\OptionBase\Helper\Data as BaseHelper;

class DependencyRules
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
     * @var array
     */
    protected $templateRule = [
        'conditions'     => [],
        'condition_type' => 'or',
        'actions'        => [
            'hide' => [],
        ],
    ];

    /**
     * @var array
     */
    protected $valueWithAndDependencyType = [];

    /**
     * @var array
     */
    protected $optionWithAndDependencyType = [];

    /**
     * @param ResourceConnection $resource
     * @param BaseHelper $baseHelper
     */
    public function __construct(
        ResourceConnection $resource,
        BaseHelper $baseHelper
    ) {
        $this->resource   = $resource;
        $this->baseHelper = $baseHelper;
    }

    /**
     * Combine dependency rules
     *
     * @param array $dependencies
     * @param array $options
     * @return array
     */
    public function combineRules(array $dependencies, array $options)
    {
        $newRules = [];

        $this->valueWithAndDependencyType  = [];
        $this->optionWithAndDependencyType = [];

        foreach ($options as $option) {
            $isEmptyValues = empty($option['values']);
            $isSelectableOption = $this->baseHelper->isSelectableOption($option['type']);


            if (!$isSelectableOption && !$isEmptyValues) {
                $option['values'] = [];
                $isEmptyValues    = true;
            }

            if ($isEmptyValues) {
                if ($isSelectableOption) {
                    continue;
                }
                $values   = [];
                $values[] = [
                    'option_type_id'            => 'o' . $option['option_id'],
                    Config::KEY_DEPENDENCY_TYPE => $option[Config::KEY_DEPENDENCY_TYPE]
                ];
                if (is_array($option)) {
                    $option['values'] = $values;
                } elseif (is_object($option)) {
                    $option->setValues($values);
                    $option->setData('values', $values);
                }
            }

            $mapOverall         = [];
            $mapOrDependencies  = [];
            $mapAndDependencies = [];
            foreach ($option['values'] as $optionValue) {
                if (empty($dependencies[$optionValue['option_type_id']])) {
                    continue;
                }
                if (!empty($optionValue[Config::KEY_DEPENDENCY_TYPE])) {
                    $this->valueWithAndDependencyType[] = $optionValue['option_type_id'];
                }

                foreach ($dependencies[$optionValue['option_type_id']] as $dependency) {
                    if (!empty($optionValue[Config::COLUMN_NAME_OPTION_DEPENDENCY_TYPE])) {
                        $this->fillDependencyMapWithoutDuplicates($mapAndDependencies, $dependency);
                    } else {
                        $this->fillDependencyMapWithoutDuplicates($mapOrDependencies, $dependency);
                    }
                    $this->fillDependencyMapWithoutDuplicates($mapOverall, $dependency);
                }
            }

            $this->collectRulesForFullyDependentValues(
                $newRules,
                $mapOverall,
                (int)$option['option_id'],
                count($option['values'])
            );

            $this->collectRulesByOrDependencies(
                $newRules,
                $mapOrDependencies,
                (int)$option['option_id']
            );

            $this->collectRulesByAndDependencies(
                $newRules,
                $mapAndDependencies,
                (int)$option['option_id']
            );
        }

        return $this->combineRulesByConditions($newRules);
    }

    /**
     * Get dependencies in specific format
     *
     * @param array $rawDependencies
     * @return array
     */
    public function getPreparedDependencies($rawDependencies)
    {
        $dependencies = [];
        foreach ($rawDependencies as $rawDependency) {
            if (empty($rawDependency['child_option_type_id'])) {
                $dependencies['o' . $rawDependency['child_option_id']][] = $rawDependency;
            } else {
                $dependencies[$rawDependency['child_option_type_id']][] = $rawDependency;
            }
        }
        return $dependencies;
    }

    /**
     * Filling map dependencies for non-selectable options, without duplicate values
     *
     * @param array $map
     * @param array $dependency
     * @return void
     */
    protected function fillDependencyMapForNonSelectableTypes(&$map, $dependency)
    {
        if (!isset($map[$dependency['child_option_id']][$dependency['parent_option_id']])
            || !in_array(
                $dependency['parent_option_type_id'],
                $map[$dependency['child_option_id']][$dependency['parent_option_id']]
            )
        ) {
            $map[$dependency['child_option_id']][$dependency['parent_option_id']][]
                = $dependency['parent_option_type_id'];
        }
    }

    /**
     * Filling map dependencies, without duplicate values
     *
     * @param array $map
     * @param array $dependency
     * @return void
     */
    protected function fillDependencyMapWithoutDuplicates(&$map, $dependency)
    {
        if (!isset($map[$dependency['child_option_type_id']][$dependency['parent_option_id']])
            || !in_array(
                $dependency['parent_option_type_id'],
                $map[$dependency['child_option_type_id']][$dependency['parent_option_id']]
            )
        ) {
            $map[$dependency['child_option_type_id']][$dependency['parent_option_id']][]
                = $dependency['parent_option_type_id'];
        }
    }

    /**
     * Get all hide value IDs by condition option value
     *
     * @param array $mapDependencies
     * @param int $currentHideValueId
     * @param int $currentConditionOptionId
     * @param array $currentConditionOptionValue
     * @return array
     */
    protected function getAllHideValueIdsByConditionOptionValue(
        $mapDependencies,
        $currentHideValueId,
        $currentConditionOptionId,
        $currentConditionOptionValue
    ) {
        $result = [];
        foreach ($mapDependencies as $hideValueId => $conditionsOptions) {
            if ($currentHideValueId === $hideValueId
                || empty($conditionsOptions[$currentConditionOptionId])
            ) {
                continue;
            }

            if (!in_array($currentConditionOptionValue, $conditionsOptions[$currentConditionOptionId])) {
                $result[$hideValueId] = $hideValueId;
            }
        }

        return $result;
    }

    /**
     * Fill rules using OR dependencies
     *
     * @param array $newRules
     * @param array $mapDependencies
     * @param int $optionId
     * @return void
     */
    protected function collectRulesByOrDependencies(
        &$newRules,
        $mapDependencies,
        $optionId
    ) {
        if (!count($mapDependencies)) {
            return;
        }

        // RULES: custom by equal conditions
        $existConditions = [];
        foreach ($mapDependencies as $hideValueId => $conditionsOptions) {

            $this->collectRulesNotEqual(
                $newRules,
                (int)$optionId,
                (int)$hideValueId,
                $conditionsOptions
            );

            do {
                foreach ($conditionsOptions as $idOption => $conditionOptionValues) {
                    foreach ($conditionOptionValues as $i => $conditionOptionValue) {
                        $checkHideValues = $this->getAllHideValueIdsByConditionOptionValue(
                            $mapDependencies,
                            (int)$hideValueId,
                            (int)$idOption,
                            $conditionOptionValue
                        );

                        // remove current condition value
                        unset($conditionsOptions[$idOption][$i]);
                        if (!count($conditionsOptions[$idOption])) {
                            unset($conditionsOptions[$idOption]);
                        }

                        // if not hide any element skip iteration
                        if (!count($checkHideValues)) {
                            continue;
                        }

                        // find all neighbors with equal hide values
                        $comparedConditions              = [];
                        $comparedConditions[$idOption][] = $conditionOptionValue;
                        foreach ($conditionsOptions as $checkIdOption => $checkConditionOptionValues) {
                            foreach ($checkConditionOptionValues as $checkConditionOptionValue) {
                                if (array_key_exists(
                                    $checkIdOption . '_' . $checkConditionOptionValue,
                                    $existConditions
                                )) {
                                    continue;
                                }

                                $innerCheckHideValues = $this->getAllHideValueIdsByConditionOptionValue(
                                    $mapDependencies,
                                    (int)$hideValueId,
                                    (int)$checkIdOption,
                                    $checkConditionOptionValue
                                );

                                if (!count(
                                    array_merge(
                                        array_diff($checkHideValues, $innerCheckHideValues),
                                        array_diff($innerCheckHideValues, $checkHideValues)
                                    )
                                )) {
                                    $comparedConditions[$checkIdOption][] = $checkConditionOptionValue;
                                }
                            }
                        }

                        $rule = $this->templateRule;
                        foreach ($comparedConditions as $id => $item) {
                            $values = [];
                            foreach ($item as $val) {
                                $key = $id . '_' . $val;
                                if (!array_key_exists($key, $existConditions)) {
                                    $values[]              = $val;
                                    $existConditions[$key] = 1;
                                }
                            }

                            if (!count($values)) {
                                continue;
                            }

                            $rule['conditions'][] = [
                                'values' => $item,
                                'type'   => 'eq',
                                'id'     => $id,
                            ];
                        }

                        // if already exist conditions in some other rules
                        if (!count($rule['conditions'])) {
                            continue;
                        }

                        $rule['actions']['hide'][$optionId] = [
                            'values' => $checkHideValues,
                            'id'     => $optionId,
                        ];

                        $newRules[] = $rule;
                    }
                    break;
                }
            } while (count($conditionsOptions));
        }
    }

    /**
     * Fill rules using AND dependencies
     *
     * @param array $newRules
     * @param array $mapDependencies
     * @param int $optionId
     * @return void
     */
    protected function collectRulesByAndDependencies(
        &$newRules,
        $mapDependencies,
        $optionId
    ) {
        if (!count($mapDependencies)) {
            return;
        }

        foreach ($mapDependencies as $hideValueId => $conditionsOptions) {
            $this->collectRulesNotEqual(
                $newRules,
                (int)$optionId,
                (int)$hideValueId,
                $conditionsOptions
            );
        }
    }

    /**
     *
     *
     * @param array $newRules
     * @param int $optionId
     * @param int $hideValueId
     * @param array $conditionsOptions
     * @return void
     */
    protected function collectRulesNotEqual(
        &$newRules,
        $optionId,
        $hideValueId,
        $conditionsOptions
    ) {
        if (!is_array($conditionsOptions) || empty($conditionsOptions)) {
            return;
        }

        $rule = $this->templateRule;
        foreach ($conditionsOptions as $id => $item) {
            $rule['conditions'][] = [
                'values' => $item,
                'type'   => '!eq',
                'id'     => $id,
            ];
        }

        $isMoreThanOneCondition = false;
        if (count($rule['conditions']) > 1) {
            $isMoreThanOneCondition = true;
        } else {
            foreach ($rule['conditions'] as $ruleCondition) {
                if (!empty($ruleCondition['values']) && count($ruleCondition['values']) > 1) {
                    $isMoreThanOneCondition = true;
                    break;
                }
            }
        }

        if ($isMoreThanOneCondition && !in_array($hideValueId, $this->valueWithAndDependencyType)) {
            $rule['condition_type'] = 'and';
        }

        if (!$hideValueId) {
            $rule['actions']['hide'][$optionId] = [
                'values' => [],
                'id'     => $optionId,
            ];
        } else {
            $rule['actions']['hide'][$optionId] = [
                'values' => [
                    $hideValueId => $hideValueId
                ],
                'id'     => $optionId,
            ];
        }

        $newRules[] = $rule;
    }

    /**
     * Hide all values, because for all values exist dependency
     *
     * @param array $newRules
     * @param array $mapDependencies
     * @param int $optionId
     * @param int $countOptionValues
     * @return void
     */
    protected function collectRulesForFullyDependentValues(
        &$newRules,
        $mapDependencies,
        $optionId,
        $countOptionValues
    ) {

        $distinctConditionsOptionIds = [];
        foreach ($mapDependencies as $conditionsOption) {
            $distinctConditionsOptionIds = array_unique(
                array_merge(
                    $distinctConditionsOptionIds,
                    array_keys($conditionsOption)
                )
            );
        }

        if (count($mapDependencies) === $countOptionValues && count($distinctConditionsOptionIds)) {
            $rule = $this->templateRule;

            foreach ($distinctConditionsOptionIds as $item) {
                $rule['conditions'][] = [
                    'values' => [],
                    'type'   => '!eq',
                    'id'     => $item,
                ];
            }

            if (count($rule['conditions']) > 1) {
                $rule['condition_type'] = 'and';
            }

            $rule['actions']['hide'][$optionId] = [
                'values' => [],
                'id'     => $optionId,
            ];

            $newRules[] = $rule;
        }
    }

    /**
     * Collapse rules by same conditions
     *
     * @param array $newRules
     * @return array
     */
    protected function combineRulesByConditions($newRules)
    {
        if (!is_array($newRules) || !$newRules) {
            return $newRules;
        }
        $resultingRules = [];

        do {
            $ruleElement = array_shift($newRules);

            foreach ($newRules as $ruleId => $newRule) {
                if ($ruleElement['conditions'] !== $newRule['conditions']
                    || $ruleElement['condition_type'] !== $newRule['condition_type']
                ) {
                    continue;
                }
                foreach ($newRule['actions']['hide'] as $hideOptionId => $hideStructure) {
                    if (isset($ruleElement['actions']['hide'][$hideOptionId])) {
                        if (empty($hideStructure['values'])) {
                            continue;
                        }
                        foreach ($hideStructure['values'] as $hideValueId) {
                            if (isset($ruleElement['actions']['hide'][$hideOptionId]['values'][$hideValueId])) {
                                continue;
                            }
                            $ruleElement['actions']['hide'][$hideOptionId]['values'][$hideValueId] = $hideValueId;
                        }
                    } else {
                        $ruleElement['actions']['hide'][$hideOptionId] = $hideStructure;
                    }
                }
                unset($newRules[$ruleId]);
            }

            $resultingRules[] = $ruleElement;

        } while (count($newRules));

        return $resultingRules;
    }
}
