define([
    'Magento_Ui/js/form/element/abstract',
    'jquery'
], function (Element, $) {
    'use strict';

    return Element.extend({
        defaults: {
            jpegValue: '',
            jpegSelect: null,
            containerElement: null,
            jpegOptimisationSelector: null,
            selectValue: null,
            imageUrl: '',
            listens: {
                selectValue: 'changeImage'
            }
        },

        initialize: function () {
            this._super();

            // for system config page
            this.jpegSelect = $('#' + this.jpegOptimisationSelector);
            this.jpegValue = this.jpegSelect.val();
            this.jpegSelect.on('change', this.setNextImage.bind(this));

            return this;
        },

        initObservable: function () {
            this._super().observe([
                'selectValue',
                'imageUrl'
            ]);

            return this;
        },

        changeImage: function (value) {
            this.imageUrl(this.images[value]);
        },

        setDefaultImage: function (element) {
            this.containerElement = element;
            this.changeImage(this.jpegValue || this.selectValue());
        },

        setNextImage: function () {
            this.jpegValue = this.jpegSelect.val();
            this.changeImage(this.jpegValue);
        }
    });
});
