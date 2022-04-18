define([
    'uiComponent',
    'Amasty_PageSpeedOptimizer/js/actions/create-tab',
    'Amasty_PageSpeedOptimizer/js/actions/prepare-diagnostic-data',
], function (Component, createTab, prepareData) {
    'use strict';

    return Component.extend({
        defaults: {
            contentData: [],
            diagnosticDate: '',
            isAfter: true,
            isActive:  false,
            imports: {
                contentData: '${ "diagnostic" }:after',
                isActive: '${ "diagnostic" }:isActive'
            },
            listens: {
                contentData: 'renderRecommendations'
            },
            totalValuation: '',
            circleLength: 314,
            imageFolderOptimization: '',
            isShowMobile: true,
            isShowRecommendations: false,
            pageUrl: ''
        },

        initObservable: function () {
            this._super().observe([
                'contentData',
                'diagnosticDate',
                'totalValuation',
                'isShowMobile',
                'isShowRecommendations',
                'isActive',
                'pageUrl'
            ]);

            return this;
        },

        renderRecommendations: function () {
            if (this.contentData().length) {
                this.contentData().forEach(function (data) {
                    var result = JSON.parse(data.result);

                    createTab(
                        this,
                        data.version,
                        prepareData.run(data.version, result.audits, this.imageFolderOptimization, Number(data.is_before))
                    );

                    this.pageUrl(result.pageUrl);
                    this.diagnosticDate(this.getDate(result));
                }.bind(this));
                this.getTotalScore('mobile')
            } else {
                this.diagnosticDate(false);
            }
        },

        getDate: function (data) {
            var date = new Date(data.date);

            return date.toLocaleDateString();
        },

        getTotalScore: function (type) {
            var totalData;

            this.contentData().forEach(function (data) {
                if (data.version === type) {
                    totalData = JSON.parse(data.result)
                }
            }.bind(this));

            if (totalData) {
                this.totalValuation(Math.ceil(totalData.totalValuation * 100));
            }
        },

        toggleStateActive: function (type) {
            if ((this.isShowMobile() && type === 'mobile') || (!this.isShowMobile() && type !== 'mobile')) {
                return;
            }

            this.isShowMobile(!this.isShowMobile());
            this.getTotalScore(type);
        }
    });
});
