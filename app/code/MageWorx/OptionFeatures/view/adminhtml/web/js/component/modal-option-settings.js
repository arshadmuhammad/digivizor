/**
 * Copyright © MageWorx. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiRegistry',
    'jquery',
    'underscore',
    'MageWorx_OptionBase/component/abstract-modal-component'
], function (registry, $, _, ModalComponent) {
    'use strict';

    return ModalComponent.extend({

        defaults: {
            pathModal: 'option_settings_modal.content.fieldset'
        },

        /**
         * Initialize fields
         */
        initFields: function () {
            this.initField('mageworx_option_image_mode');
            this.initField('mageworx_option_gallery');
            this.initField('div_class');

            var optionType = registry
                .get(this.entityProvider)
                .get(this.entityDataScope + '.type');

            var selectionLimitFromField = registry.get(
                this.formName + '.' + this.formName + '.' + this.pathModal + '.' + 'selection_limit_from'
            );
            var selectionLimitToField = registry.get(
                this.formName + '.' + this.formName + '.' + this.pathModal + '.' + 'selection_limit_to'
            );
            if (optionType !== 'multiple' && optionType !== 'checkbox') {
                selectionLimitFromField.hide();
                selectionLimitToField.hide(true);
            } else {
                selectionLimitFromField.show();
                this.initField('selection_limit_from');
                selectionLimitToField.show();
                this.initField('selection_limit_to');
            }
        },

        /**
         * Save data before close modal, update button status
         */
        saveData: function () {
            this.processDataItem('mageworx_option_image_mode', this.conditionGreaterThanZero);
            this.processDataItem('mageworx_option_gallery', this.conditionGreaterThanZero);
            this.processDataItem('div_class', this.conditionNonEmptyString);
            this.processDataItem('selection_limit_from', this.conditionNonZero);
            this.processDataItem('selection_limit_to', this.conditionNonZero);
        }
    });
});
