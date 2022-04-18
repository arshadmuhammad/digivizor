/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'jquery',
    'underscore',
    'jquery-ui-modules/widget'
], function ($, _) {
    'use strict';

    $.widget('mageworx.optionDependency', {
        options: {
            dataType: {
                option: 'data-option_id',
                value: 'data-option_type_id'
            },
            addToCartSelector: '#product_addtocart_form',
            options: [],
            firstRunProcessed: []
        },
        baseObject: {},

        /**
         * Triggers one time at first run (from base.js)
         * @param optionConfig
         * @param productConfig
         * @param base
         * @param self
         */
        firstRun: function firstRun(optionConfig, productConfig, base, self)
        {
            this.options.options = [];
            this.initOptions();
            this.baseObject = base;

            var $this = this;
            $.each(self.options, function (index, element) {
                $this.options[index] = element;
            });

            $('.mageworx-need-wrap').wrap('<span>');

            var $needDisableDateValidationFields = $('.mageworx-disable-date-validation');
            if ($needDisableDateValidationFields.length > 0) {
                $needDisableDateValidationFields.find('select').attr('data-validate', '{"datetime-validation": false}');
                this.disableDatetimeValidation($needDisableDateValidationFields);
            }

            if (!_.isUndefined(self.options.dependencyRulesJson) && self.options.dependencyRulesJson.length !== 0) {
                this.options.dependencyRules = JSON.parse(self.options.dependencyRulesJson);
            }

            window.apoData = {};
            $.each(this.options.options, function (index, option) {
                window.apoData[option.id] = [];
            });

            if (!_.isUndefined(self.options.selectedValues)) {
                $.each(self.options.selectedValues, function (index, values) {
                    window.apoData[index] = values;
                });
            }

            if (self.options.isAdmin) {
                $.each(optionConfig, function(item, i) {
                    var field = $('[data-option_id="'+item+'"]') ? $('[data-option_id="'+item+'"]') : $('[data-option_type_id="'+item+'"]');
                    if(field.css('display') == 'none') {
                        field.removeClass('required');
                        if (field.find('input[type="file"]').length < 1 || self.options.isAdmin) {
                            field.find('input, select, textarea, .field').removeClass('required');
                            field.find('input, select, textarea, .field').removeClass('required-entry');
                        }
                    }
                });
            }
        },

        /**
         * Triggers each time when option is updated\changed (from the base.js)
         * @param option
         * @param optionConfig
         * @param productConfig
         * @param base
         */
        update: function update(option, optionConfig, productConfig, base)
        {
            var self = this;

            var optionField = $(option).closest('[data-option_id]');
            var optionId = optionField.attr('data-option_id');
            var optionObject = self.getOptionObject(optionId, 'option');

            var optionTypeField = $(option).find('[data-option_type_id]').first();
            if (optionTypeField.length < 1) {
                optionTypeField = $(option).closest('[data-option_type_id]');
            }

            var object = optionObject;
            if (optionTypeField) {
                var valueId = parseInt(optionTypeField.attr('data-option_type_id'));
                object = self.getOptionObject(valueId, 'value');
            }

            if ($.inArray(optionObject.type, ['drop_down', 'multiple']) !== -1) {
                if (optionObject.type === 'drop_down') {
                    // For dropdown - for selected select options only
                    $('#' + option.attr('id') + ' option:selected').each(function () {
                        self.toggleDropdown(optionObject, self.getOptionObject($(this).attr('data-option_type_id'), 'value'));
                    });
                } else {
                    // For multiselect - for all select options
                    var selectedMultiselectValues = $('#' + option.attr('id') + ' option:selected');
                    if (selectedMultiselectValues.length > 0) {
                        self.toggleMultiselect(optionObject, selectedMultiselectValues);
                    } else {
                        self.resetMultiselect(optionObject);
                    }
                }
            } else if ($.inArray(optionObject.type, ['checkbox', 'radio']) !== -1) {
                if (optionObject.type === 'radio') {
                    if ($(option).is(':checked')) {
                        self.toggleRadio(optionObject, object);
                    }
                } else {
                    if ($(option).is(':checked')) {
                        self.toggleCheckbox(optionObject, object);
                    } else {
                        self.resetCheckbox(optionObject, object);
                    }
                }
            }

            self.options.needDependencyRulesProcessing = true;
            while (self.options.needDependencyRulesProcessing) {
                self.options.needDependencyRulesProcessing = false;
                self.processDependencyRules();
            }
            self.options.hiddenValues = self.options.valuesToHide;
            self.options.valuesToHide = [];
            self.options.hiddenOptions = self.options.optionsToHide;
            self.options.optionsToHide = [];
        },

        /**
         * Toggle dropdown
         *
         * @param option
         * @param changedValue
         */
        toggleDropdown: function (option, changedValue) {
            var self = this;

            // For --Please Select-- - unselect all selected values
            if (typeof changedValue.id === "undefined" && _.isArray(window.apoData[option.id])) {
                $.each(window.apoData[option.id], function (i, value) {
                    var index = window.apoData[option.id].indexOf(parseInt(value));
                    if (index !== -1) {
                        window.apoData[option.id].splice(index, 1);
                    }
                });
            }
            // For select "normal" value
            if (typeof changedValue.id !== "undefined") {
                // Toggle unselected values
                if (_.isArray(window.apoData[option.id])) {
                    $.each(window.apoData[option.id], function (i, value) {
                        var index = window.apoData[option.id].indexOf(parseInt(value));
                        if (value !== changedValue.id && index !== -1) {
                            window.apoData[option.id].splice(index, 1);
                        }
                    });
                }

                // Toggle selected value
                if (_.isUndefined(window.apoData[option.id])) {
                    window.apoData[option.id] = [];
                }
                window.apoData[option.id].push(parseInt(changedValue.id));
            }
        },

        /**ы
         * Toggle multiselect
         *
         * @param option
         * @param changedValues
         */
        toggleMultiselect: function (option, changedValues) {
            var self = this;

            var changedValueObjects = [];
            $.each(changedValues, function (index, changedValue) {
                changedValueObjects.push(parseInt($(changedValue).attr('data-option_type_id')));
            });

            // For select "normal" value
            // Toggle unselected values
            $.each(window.apoData[option.id], function (i, value) {
                var currentIndex = changedValueObjects.indexOf(parseInt(value));
                if (currentIndex === -1) {
                    var index = window.apoData[option.id].indexOf(parseInt(value));
                    window.apoData[option.id].splice(index, 1);
                }
            });

            $.each(changedValues, function (index, changedValue) {
                // Toggle selected value
                var changedValueObject = self.getOptionObject($(changedValue).attr('data-option_type_id'), 'value');
                var currentIndex = window.apoData[option.id].indexOf(parseInt(changedValueObject.id));
                if (currentIndex === -1) {
                    if (_.isUndefined(window.apoData[option.id])) {
                        window.apoData[option.id] = [];
                    }
                    window.apoData[option.id].push(parseInt(changedValueObject.id));
                }
            });
        },

        /**
         * Reset multiselect
         *
         * @param option
         */
        resetMultiselect: function (option) {
            var self = this;

            // unselect all values, which already in apoData)
            $.each(window.apoData[option.id], function (index, value) {
                var currentIndex = window.apoData[option.id].indexOf(parseInt(value));
                if (currentIndex !== -1) {
                    window.apoData[option.id].splice(currentIndex, 1);
                }
            });
            window.apoData[option.id] = [];
        },

        /**
         * Toggle radio
         *
         * @param option
         * @param changedValue
         */
        toggleRadio: function (option, changedValue) {
            var self = this;

            // For select "normal" value
            if (typeof changedValue.id !== "undefined") {
                // Toggle unselected values
                if (_.isUndefined(window.apoData)) {
                    window.apoData = {};
                }
                if (_.isArray(window.apoData[option.id])) {
                    $.each(window.apoData[option.id], function (i, value) {
                        var index = window.apoData[option.id].indexOf(parseInt(value));
                        if (value.id !== changedValue.id && index !== -1) {
                            window.apoData[option.id].splice(index, 1);
                        }
                    });
                }

                // Toggle selected value
                if (_.isUndefined(window.apoData[option.id])) {
                    window.apoData[option.id] = [];
                }
                window.apoData[option.id].push(parseInt(changedValue.id));
            }
        },

        /**
         * Toggle checkbox
         *
         * @param option
         * @param changedValue
         */
        toggleCheckbox: function (option, changedValue) {
            var self = this;

            // For select "normal" value
            if (typeof changedValue.id !== "undefined") {
                // Toggle selected value
                if (_.isUndefined(window.apoData[option.id])) {
                    window.apoData[option.id] = [];
                }
                window.apoData[option.id].push(parseInt(changedValue.id));
            }
        },

        /**
         * Reset checkbox
         *
         * @param option
         * @param changedValue
         */
        resetCheckbox: function (option, changedValue) {
            var self = this;

            // Toggle unselected value
            var currentIndex = window.apoData[option.id].indexOf(parseInt(changedValue.id));
            if (currentIndex !== -1) {
                window.apoData[option.id].splice(currentIndex, 1);
            }
        },

        /**
         * Process dependency rules
         */
        processDependencyRules: function () {
            var self = this;
            self.options.optionsToHide = [];
            self.options.valuesToHide = [];
            $.each(self.options.dependencyRules, function (index, rule) {
                if (rule.condition_type === 'and') {
                    self.processDependencyAndRules(rule);
                } else {
                    self.processDependencyOrRules(rule);
                }
            });

            self.hideOptionIfAllValuesHidden();
            self.runShowProcessor();
        },

        /**
         * Process dependency OR-type rules
         *
         * @param dependencyRule
         */
        processDependencyOrRules: function (dependencyRule) {
            var self = this;

            var isConvertedToAndCondition = false;
            var areConditionsNotPassed = false;

            $.each(dependencyRule.conditions, function (index, condition) {
                var conditionOptionValues = condition.values;
                if (conditionOptionValues.length < 1
                    && condition.id
                    && self.options.optionToValueMap[condition.id]
                ) {
                    conditionOptionValues     = self.options.optionToValueMap[condition.id];
                    isConvertedToAndCondition = true;
                }

                if (condition.type === '!eq') {
                    $.each(conditionOptionValues, function (i, conditionOptionValueId) {
                        var optionId = self.options.valueToOptionMap[conditionOptionValueId];
                        var index = -1;
                        if (!_.isUndefined(optionId)) {
                            index = window.apoData[optionId].indexOf(parseInt(conditionOptionValueId));
                        }
                        if (isConvertedToAndCondition) {
                            if (index !== -1) {
                                areConditionsNotPassed = true;
                                return false;
                            }
                        } else {
                            if (index === -1) {
                                self.processHiddenValuesByRule(dependencyRule);
                            }
                        }
                    });

                    if (isConvertedToAndCondition && !areConditionsNotPassed) {
                        self.processHiddenValuesByRule(dependencyRule);
                    }
                } else {
                    $.each(conditionOptionValues, function (i, conditionOptionValueId) {
                        var optionId = self.options.valueToOptionMap[conditionOptionValueId];
                        var index = -1;
                        if (!_.isUndefined(optionId)) {
                            index = window.apoData[optionId].indexOf(parseInt(conditionOptionValueId));
                        }
                        if (index !== -1) {
                            self.processHiddenValuesByRule(dependencyRule);
                        }
                    });
                }
            });
        },

        /**
         * Process dependency AND-type rules
         *
         * @param dependencyRule
         */
        processDependencyAndRules: function (dependencyRule) {
            var self = this;

            var areConditionsPassed = true;

            $.each(dependencyRule.conditions, function (index, condition) {
                if (areConditionsPassed === false) {
                    return false;
                }
                var conditionOptionValues = condition.values;
                if (conditionOptionValues.length < 1
                    && condition.id
                    && self.options.optionToValueMap[condition.id]
                ) {
                    conditionOptionValues = self.options.optionToValueMap[condition.id];
                }

                if (condition.type === '!eq') {
                    $.each(conditionOptionValues, function (i, conditionOptionValueId) {
                        var optionId = self.options.valueToOptionMap[conditionOptionValueId];
                        var index = -1;
                        if (!_.isUndefined(optionId)) {
                            index = window.apoData[optionId].indexOf(parseInt(conditionOptionValueId));
                        }
                        if (index !== -1) {
                            areConditionsPassed = false;
                            return false;
                        }
                    });
                } else {
                    $.each(conditionOptionValues, function (i, conditionOptionValueId) {
                        var optionId = self.options.valueToOptionMap[conditionOptionValueId];
                        var index = -1;
                        if (!_.isUndefined(optionId)) {
                            index = window.apoData[optionId].indexOf(parseInt(conditionOptionValueId));
                        }
                        if (index === -1) {
                            areConditionsPassed = false;
                            return false;
                        }
                    });
                }
            });
            if (areConditionsPassed) {
                self.processHiddenValuesByRule(dependencyRule);
            }
        },

        /**
         * Process hidden values by rule
         *
         * @param dependencyRule
         */
        processHiddenValuesByRule: function (dependencyRule) {
            var self = this;

            $.each(dependencyRule.actions.hide, function (i, hideItem) {
                var option = self.getOptionObject(hideItem.id, 'option');
                if ($.inArray(option.type, ['drop_down', 'multiple', 'checkbox', 'radio']) === -1) {
                    if ($.inArray(parseInt(hideItem.id), self.options.optionsToHide) === -1) {
                        self.options.optionsToHide.push(parseInt(hideItem.id));
                    }
                } else {
                    $.each(hideItem.values, function (iv, value) {
                        var index = window.apoData[hideItem.id].indexOf(parseInt(value));
                        if (index !== -1) {
                            self.options.needDependencyRulesProcessing = true;
                            window.apoData[hideItem.id].splice(index, 1);
                            var object = self.getOptionObject(value, 'value');
                        }
                        if ($.inArray(parseInt(value), self.options.valuesToHide) === -1) {
                            self.options.valuesToHide.push(parseInt(value));
                        }
                    });
                }
                self.runHideProcessor(hideItem);
            });
        },

        /**
         * Show option or value
         */
        runShowProcessor: function () {
            var self = this;

            $.each(self.options.hiddenOptions, function (i, option) {
                var index = self.options.optionsToHide.indexOf(parseInt(option));
                if (index === -1) {
                    var object = self.getOptionObject(option, 'option');
                    if (object !== '') {
                        self.show(object, true);
                    }
                }
            });

            $.each(self.options.hiddenValues, function (i, value) {
                var index = self.options.valuesToHide.indexOf(parseInt(value));
                if (index === -1) {
                    var object = self.getOptionObject(value, 'value');
                    if (object !== '') {
                        self.show(object.getOption(), true);
                        self.show(object, false);
                    }
                }
            });
        },

        /**
         * Hide option if all values are hidden
         */
        hideOptionIfAllValuesHidden: function () {
            var self = this;

            $.each(self.options.optionToValueMap, function (option, values) {
                var areAllValuesHidden = true;
                if (values.length < 1) {
                    return;
                }
                $.each(values, function (i, value) {
                    if ($.inArray(parseInt(value), self.options.valuesToHide) === -1) {
                        areAllValuesHidden = false;
                        return false;
                    }
                });
                if (areAllValuesHidden) {
                    if ($.inArray(parseInt(option), self.options.optionsToHide) !== -1) {
                        return;
                    }
                    self.options.optionsToHide.push(parseInt(option));
                    var isOption = true;
                    var index = self.options.hiddenOptions.indexOf(parseInt(option));
                    if (index !== -1) {
                        return;
                    }
                    var object = self.getOptionObject(option, 'option');
                    if (object !== '') {
                        self.hide(object, isOption);
                    }
                }
            });
        },

        /**
         * Show option or value
         *
         * @param object
         * @param isOption
         */
        show: function (object, isOption) {
            var self = this;

            var isRequired = false;
            var field = isOption ? $('[data-option_id="'+object.id+'"]') : $('[data-option_type_id="'+object.id+'"]');

            if (isOption && typeof self.options.optionRequiredConfig != 'undefined') {
                isRequired = typeof self.options.optionRequiredConfig[object.id] != 'undefined' ?
                    self.options.optionRequiredConfig[object.id] :
                    false;
            }

            if (!isOption && field.css('display') === 'none') {
                self.baseObject.addNewlyShowedOptionValue(object.id);
            }
            if (!isOption) {
                var type = object.getOption().type;
                if ($.inArray(type, ['drop_down', 'multiple']) !== -1) {
                    if (field.parent().prop("tagName").toLowerCase() === 'span') {
                        field.unwrap('span');
                    }
                }
            }
            field.show();
            if (isOption && isRequired) {
                if (field.hasClass('date') || field.find('.datetime-picker').length > 0) {
                    self.enableDatetimeValidation(field);
                } else {
                    field.addClass('required');
                    if (field.find('input[type="file"]').length < 1 || self.options.isAdmin) {
                        field.find('input, select, textarea, .field').addClass('required');
                        field.find('input, select, textarea, .field').addClass('required-entry');
                    }
                }
            }
        },

        /**
         * Hide option or value
         *
         * @param hideItem
         */
        runHideProcessor: function (hideItem) {
            var self = this;

            var isOption = false;

            if (!_.isEmpty(hideItem.values)) {
                $.each(hideItem.values, function (i, value) {
                    var index = self.options.hiddenValues.indexOf(parseInt(value));
                    if (index === -1) {
                        var object = self.getOptionObject(value, 'value');
                        if (object !== '') {
                            self.hide(object, isOption);
                        }
                    }
                });
            } else {
                isOption = true;
                var index = self.options.hiddenOptions.indexOf(parseInt(hideItem.id));
                if (index === -1) {
                    var object = self.getOptionObject(hideItem.id, 'option');
                    if (object !== '') {
                        self.hide(object, isOption);
                    }
                }
            }
        },

        /**
         * Hide option or value
         *
         * @param object
         * @param isOption
         */
        hide: function (object, isOption) {
            var self = this;

            var isRequired = false;
            var field = isOption ? $('[data-option_id="'+object.id+'"]') : $('[data-option_type_id="'+object.id+'"]');

            if (isOption && typeof self.options.optionRequiredConfig != 'undefined') {
                isRequired = typeof self.options.optionRequiredConfig[object.id] != 'undefined' ?
                    self.options.optionRequiredConfig[object.id] :
                    false;
            }

            if (!isOption) {
                var type = object.getOption().type;
                if ($.inArray(type, ['drop_down', 'multiple']) !== -1) {
                    if (field.parent().prop("tagName").toLowerCase() !== 'span') {
                        field.wrap('<span>');
                    }
                }
            }
            field.hide();
            if (isOption && isRequired) {
                if (field.hasClass('date') || field.find('.datetime-picker').length > 0) {
                    self.disableDatetimeValidation(field);
                } else {
                    field.removeClass('required');
                    if (field.find('input[type="file"]').length < 1 || self.options.isAdmin) {
                        field.find('input, select, textarea, .field').removeClass('required');
                        field.find('input, select, textarea, .field').removeClass('required-entry');
                    }
                }
            }

            object.reset();
        },

        /**
         * Get option object
         *
         * @param id
         * @param type
         */
        getOptionObject: function (id, type)
        {
            var object = '';
            $.each(this.options.options, function (index, option) {
                if (type === 'option' && parseInt(option.id) === parseInt(id)) {
                    object = option;
                    return false;
                }
                $.each(option.values, function (index, value) {
                    if (type === 'value' && parseInt(value.id) === parseInt(id)) {
                        object = value;
                        return false;
                    }
                });
            });

            return object;
        },

        /**
         * Initialize option objects
         */
        initOptions: function () {
            var self = this,
                getType,
                reset;

            /**
             * Retrieve option type, by value id.
             * Used for detect parent dependent option type.
             *
             * @param valueId
             * @returns {string}
             */
            getType = function (valueId) {
                var type = '';

                $.each(self.options.options, function (index, option) {
                    $.each(option.values, function (index, value) {
                        if (valueId === value.id) {
                            type = value.getOption().type;
                            return;
                        }
                    });
                    if (type) {
                        return;
                    }
                });

                return type;
            };


            /**
             * Reset value
             *
             * @param value
             */
            reset = function (value) {
                var isOption = !_.isUndefined(value.type);
                if (isOption) {
                    return this;
                }

                var field = $('[data-option_type_id="'+value.id+'"]');
                if (field.css('display') !== 'none') {
                    return this;
                }

                var type = value.getOption().type;
                var element = null;

                // checkbox and radio
                if ($.inArray(type, ['checkbox', 'radio']) !== -1) {
                    element = field.children('input');
                    element.removeAttr('checked');
                }

                // drop-down and multiselect
                if ($.inArray(type, ['drop_down', 'multiple']) !== -1) {
                    element = field.closest('select');
                    field.removeAttr('selected');
                }

                // update product price
                var priceOptions = $(self.options.addToCartSelector).data('magePriceOptions');
                if (!_.isUndefined(priceOptions) && !_.isNull(element)) {
                    priceOptions._onOptionChanged({target: element});
                }

                return this;
            },

                $('[data-option_id]').each(function (index, option) {

                    var values = [];
                    var optionObj = {}; // create emty option object to transfer the link to it to value

                    $(option).find('[data-option_type_id]').each(function (index, value) {
                        var valueObj = {
                            id: $(value).attr('data-option_type_id'),
                            _getType: function (valueId) { // return option type by value
                                return getType(valueId);
                            },
                            reset: function () {
                                return reset(this);
                            },
                            getOption: function () {
                                return optionObj;
                            }
                        };

                        values.push(valueObj);
                    });

                    optionObj = {
                        id: parseInt($(option).attr('data-option_id')),
                        type: self.options.optionTypes[$(option).attr('data-option_id')],
                        values: values,
                        _getType: function (valueId) { // return option type by value
                            return getType(valueId);
                        },
                        reset: function () {
                            return reset(this);
                        }
                    };

                    self.options.options.push(optionObj);
                });

            return this;
        },

        /**
         * Disable datetime validation
         *
         * @param field
         */
        disableDatetimeValidation: function (field) {
            this.setDatetimeValidation(field, false);
        },

        /**
         * Enable datetime validation
         *
         * @param field
         */
        enableDatetimeValidation: function (field) {
            this.setDatetimeValidation(field, true);
        },

        /**
         * Enable/Disable datetime validation
         *
         * @param field
         * @param enable
         */
        setDatetimeValidation: function (field, enable) {
            var fromKey = enable ? 'date' : 'datetime';
            var toKey = enable ? 'datetime' : 'date';
            var datetimeValidationField = field.find("input:hidden[name^='validate_" + fromKey + "_']");
            if (!_.isUndefined(datetimeValidationField) && datetimeValidationField.length > 0) {
                datetimeValidationField.attr(
                    'name',
                    datetimeValidationField.attr('name').replace(fromKey, toKey)
                );
                datetimeValidationField.attr(
                    'class',
                    datetimeValidationField.attr('class').replace(fromKey, toKey)
                );
            }
            field.find('select').attr('data-validate', '{"datetime-validation": ' + enable + '}');
        }
    });

    return $.mageworx.optionDependency;
});
