/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
define([
    'jquery',
    'dynamicOptionsDefaultCalculator',
    'Magento_Catalog/js/price-utils',
    'qTip',
    'underscore',
    'priceBox',
    'mage/validation',
    'jquery-ui-modules/widget'
], function ($, defaultCalculator, utils, qTip, _) {
    'use strict';

    $.widget('mageworx.dynamicOptions', {

        options: {},
        original_regular_price_excl_tax: null,
        original_regular_price_incl_tax: null,
        original_final_price_excl_tax: null,
        original_final_price_incl_tax: null,
        extended_tier_prices: {},

        /**
         * Triggers one time at first run (from base.js)
         * @param optionConfig
         * @param productConfig
         * @param base
         * @param self
         */
        firstRun: function firstRun(optionConfig, productConfig, base, self) {
            this.priceBox = $('.price-box', form);

            var form = base.getFormElement(),
                config = base.options,
                options = $(config.optionsSelector, form);

            options.filter('input[type="text"], textarea').each(function (index, element) {
                var $element = $(element),
                    optionId = utils.findOptionId($element);
                $('#mageworx_dynamic_option_hint_icon_' + optionId).qtip({
                    content: {
                        text: $('#mageworx_dynamic_option_hint_' + optionId).html()
                    },
                    style: {
                        classes: 'qtip-light'
                    },
                    position: {
                        target: false
                    }
                });
                $('#mageworx_dynamic_option_hint_' + optionId).hide();
            });
        },

        /**
         * Triggers each time after the all updates when option was changed (from the base.js)
         * @param base
         * @param productConfig
         */
        applyChanges: function (base, productConfig)
        {
            var self = this,
                exit = false,
                form = base.getFormElement(),
                config = base.options,
                options = $(config.optionsSelector, form),
                dynamicOptions = this.options['options_data'];

            if (dynamicOptions.length === 0) {
                return;
            }

            this.initProductPrice(productConfig);

            options.filter('input[type="text"], textarea').each(function (index, element) {
                var $element = $(element),
                    optionId = utils.findOptionId($element),
                    value = parseFloat($element.val());

                if ($element.closest('.field').css('display') == 'none') {
                    return;
                }

                if (typeof dynamicOptions[optionId] !== 'undefined') {
                    if (Number.isNaN(value) && $element.val() === '') {
                        exit = true;

                        return;
                    }

                    if (!$.validator.validateElement($element)) {
                        exit = true;
                        return;
                    }
                    dynamicOptions[optionId]['value'] = value;
                }


                if (parseInt(productConfig['absolute_price'])) {
                    self.original_regular_price_excl_tax = 0;
                    self.original_regular_price_incl_tax = 0;
                    self.original_final_price_excl_tax = 0;
                    self.original_final_price_incl_tax = 0
                }
            });

            if (exit) {
                productConfig.regular_price_excl_tax = self.original_regular_price_excl_tax;
                productConfig.regular_price_incl_tax = self.original_regular_price_incl_tax;
                productConfig.final_price_excl_tax = self.original_final_price_excl_tax;
                productConfig.final_price_incl_tax = self.original_final_price_incl_tax;

                return;
            }

            productConfig['isUsedDynamicOptions'] = true;

            var dynamicPriceExclTax = defaultCalculator.calculate(dynamicOptions, this.options.prices.basePrice.amount),
                dynamicPriceInclTax = defaultCalculator.calculate(dynamicOptions, this.options.prices.finalPrice.amount);

            if (productConfig['type_id'] === 'configurable') {
                var additionalPrice = {};
                additionalPrice['mwDynamicOptions'] = {
                    'basePrice': {
                        'amount': dynamicPriceExclTax
                    },
                    'finalPrice': {
                        'amount': dynamicPriceInclTax
                    }
                };

                this.priceBox.trigger('updatePrice', additionalPrice);
            } else {
                productConfig.regular_price_excl_tax = self.original_regular_price_excl_tax + dynamicPriceExclTax;
                productConfig.regular_price_incl_tax = self.original_regular_price_incl_tax + dynamicPriceInclTax;
                productConfig.final_price_excl_tax = self.original_final_price_excl_tax + dynamicPriceExclTax;
                productConfig.final_price_incl_tax = self.original_final_price_incl_tax + dynamicPriceInclTax;

                this.productPerItemRegularPriceExclTax =  self.original_regular_price_excl_tax + dynamicPriceExclTax;
                this.productPerItemFinalPriceInclTax = self.original_regular_price_incl_tax + dynamicPriceInclTax;
                this.productPerItemFinalPriceExclTax = self.original_final_price_excl_tax + dynamicPriceExclTax;
                this.productPerItemFinalPriceInclTax = self.original_final_price_incl_tax + dynamicPriceInclTax;

                if (!_.isUndefined(productConfig.extended_tier_prices) && productConfig.extended_tier_prices.length > 0)
                {
                    var tierPrices = productConfig.extended_tier_prices;

                    _.each(tierPrices, function (tier, index) {
                        productConfig.extended_tier_prices[index]['price_incl_tax'] =
                            self.extended_tier_prices[index]['price_incl_tax'] + dynamicPriceInclTax;
                        productConfig.extended_tier_prices[index]['price_incl_tax'] =
                            self.extended_tier_prices[index]['price_incl_tax'] + dynamicPriceInclTax;
                    });
                }

                this.setProductPrice(base);
            }
        },

        setProductPrice: function (base)
        {
            // Set product prices according to price's display mode on the product view page
            // 1 - without tax
            // 2 - with tax
            // 3 - both (with and without tax)
            if (base.getPriceDisplayMode() == 1) {
                base.setProductRegularPrice(this.productPerItemRegularPriceExclTax);
                base.setProductFinalPrice(this.productPerItemFinalPriceExclTax);
                base.setAdditionalProductRegularPrice(this.productPerItemRegularPriceExclTax);
                base.setAdditionalProductFinalPrice(this.productPerItemFinalPriceExclTax);
            } else {
                base.setProductRegularPrice(this.productPerItemRegularPriceInclTax);
                base.setProductFinalPrice(this.productPerItemFinalPriceInclTax);
                base.setAdditionalProductRegularPrice(this.productPerItemRegularPriceInclTax);
                base.setAdditionalProductFinalPrice(this.productPerItemFinalPriceInclTax);
            }

            base.setProductPriceExclTax(this.productPerItemFinalPriceExclTax);
            base.setAdditionalProductPriceExclTax(this.productPerItemFinalPriceExclTax);
        },

        /**
         * Initialize Product Price
         *
         * @param productConfig
         * @private
         */
        initProductPrice: function (productConfig)
        {
            if (this.original_regular_price_excl_tax === null) {
                this.original_regular_price_excl_tax = productConfig.regular_price_excl_tax;
            }
            if (this.original_regular_price_incl_tax === null) {
                this.original_regular_price_incl_tax = productConfig.regular_price_incl_tax;
            }
            if (this.original_final_price_excl_tax === null) {
                this.original_final_price_excl_tax = productConfig.final_price_excl_tax;
            }
            if (this.original_final_price_incl_tax === null) {
                this.original_final_price_incl_tax = productConfig.final_price_incl_tax;
            }

            if (this.original_final_price_incl_tax === null) {
                this.original_final_price_incl_tax = productConfig.final_price_incl_tax;
            }


            if (!_.isUndefined(productConfig.extended_tier_prices) && productConfig.extended_tier_prices.length > 0)
            {
                this.extended_tier_prices = utils.deepClone(productConfig.extended_tier_prices);
            }
        },
    });

    return $.mageworx.dynamicOptions;
});
