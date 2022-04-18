define([
    'Magento_Ui/js/form/element/abstract',
    'jquery',
    'underscore'
], function (Element, $, _) {
    'use strict';

    return Element.extend({
        defaults: {
            submitUrl: '',
            checkboxSelector: '#amimageoptimizer_delete_previously .amoptimizer-checkbox',
            isError: null,
            message: null
        },

        initObservable: function () {
            this._super().observe([
                'message',
                'isError'
            ]);

            return this;
        },

        prepareData: function (checkboxes) {
            var preparedCheckboxes = [];

            _.each(checkboxes, function (checkbox) {
                preparedCheckboxes.push(checkbox.name);
            });

            return preparedCheckboxes;
        },

        submitCheckboxes: function () {
            var checkboxes = $(this.checkboxSelector).serializeArray(),
                folders = this.prepareData(checkboxes);

            this.message('');

            if (!checkboxes.length) {
                return false;
            }

            $.ajax({
                showLoader: true,
                url: this.submitUrl,
                dataType: 'JSON',
                data: { form_key: FORM_KEY, folders: folders },
                type: 'POST',
                success: function (response) {
                    this.isError(response.error);
                    this.message(response.message);
                }.bind(this)
            });
        }
    });
});
