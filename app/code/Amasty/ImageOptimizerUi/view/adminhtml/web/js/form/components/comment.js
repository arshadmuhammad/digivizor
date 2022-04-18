define([
    'Magento_Ui/js/form/components/html'
], function (Html) {
    'use strict';

    return Html.extend({
        copyText: function (index) {
            var copyInput = document.querySelector('[data-amoptimizer-js="copy' + index + '"]');

            copyInput.select();
            document.execCommand('copy');
        }
    });
});
