define([
    'uiComponent',
    'jquery'
], function (Component, $) {
    'use strict';

    return Component.extend({
        defaults: {
            device: '',
            recommendations: '.amoptimizer-recommendation-block',
            links: '.amoptimizer-link-block',
            css: {
                hide: '-hide',
                active: '-active'
            },
            prefix: '',
            templatePrefix: 'Amasty_PageSpeedOptimizer/recommendations/',
            noneUniversalIds: [
                'dom-size',
                'total-byte-weight',
                'uses-rel-preload',
                'efficient-animated-content',
                'unused-css-rules'
            ]

        },

        initObservable: function () {
            this._super().observe(['contentData']);

            return this;
        },

        linkClick: function (item, e) {
            var elem = $(this.prefix + '[data-amoptimizer-js="' + item.id + '"]');

            $(this.prefix + this.links).removeClass(this.css.active);
            $(e.currentTarget).addClass(this.css.active);
            $(this.prefix + this.recommendations).addClass(this.css.hide);
            elem.removeClass(this.css.hide);
        },

        getRecommendationTemplate: function (id) {
            if (this.noneUniversalIds.indexOf(id) !== -1) {
                return this.templatePrefix + 'none-universal';
            }

            return this.templatePrefix + id;
        }

    });
});
