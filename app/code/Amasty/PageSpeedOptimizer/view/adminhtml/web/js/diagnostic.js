define([
    'uiCollection',
    'jquery',
    'Amasty_PageSpeedOptimizer/js/actions/create-tab',
    'Amasty_PageSpeedOptimizer/js/actions/prepare-diagnostic-data',
    'Magento_Ui/js/modal/alert'
], function (Collection, $, createTab, prepareData, message) {
    'use strict';

    return Collection.extend({
        defaults: {
            saveUrl: '',
            loadUrl: '',
            isBefore: false,
            types: {
                mobile: 1,
                desktop: 2
            },
            savedVersions: [],
            diagnosticDate: '',
            contentData: [],
            isAfter: false,
            after: [],
            totalValuation: 0,
            circleLength: 314,
            isShowMobile: true,
            isShowRecommendations: true,
            element: {
                tab: '[data-amoptimizer-js="tab"]',
                diagnostic: '[data-amoptimizer-js="diagnostic"]',
                container: '[data-amoptimizer-js="container"]',
                linkBlock: '[data-amoptimizer-js="link-container"]'
            },
            score: {
                mobile: null,
                desktop: null
            },
            isActive: false,
            pageUrl: '',
            maxHeight: 0
        },

        initialize: function () {
            this._super();

            this.getDiagnosticData();
            this.addEvents();
        },

        initObservable: function () {
            this._super().observe([
                'diagnosticDate',
                'contentData',
                'after',
                'totalValuation',
                'isShowMobile',
                'isShowRecommendations',
                'isActive',
                'pageUrl',
                'maxHeight'
            ]);

            return this;
        },

        saveData: function (data, version) {
            data = JSON.stringify(data);
            $.ajax({
                url: this.saveUrl,
                type: 'POST',
                showLoader: true,
                data: {
                    form_key: FORM_KEY,
                    data: {
                        result: data,
                        is_before: Number(this.isBefore),
                        version: version
                    }
                },
                success: function (response) {
                    if (!response.error) {
                        if (this.checkSavedVersions(version)) {
                            this.getDiagnosticData();
                        }
                    }
                }.bind(this)
            });
        },

        getDiagnosticData: function () {
            $.ajax({
                url: this.loadUrl,
                type: 'GET',
                processData: false,
                contentType: 'application/json',
                showLoader: true,
                success: function (response) {
                    if (!response.error) {
                        this.renderData(response);
                    }
                }.bind(this)
            });
        },

        checkSavedVersions: function (version) {
            this.savedVersions.push(version);

            return this.savedVersions.length === 2;
        },

        renderData: function (response) {
            if (!response.length) {
                this.isBefore = true;

                return;
            }

            response.forEach(function (data) {
                if (Number(data.is_before)) {
                    this.initInfoblock(JSON.parse(data.result), data.version, Number(data.is_before));
                } else if (data.result) {
                    this.after.push(data);
                    this.isShowRecommendations(false);
                }
            }.bind(this));
        },

        getDate: function (date) {
            var date = new Date(date);

            return date.toLocaleDateString();
        },

        addEvents: function () {
            $(this.element.diagnostic).on('click', this.startDiagnostic.bind(this));
        },

        startDiagnostic: function () {
            if (this.after().length) {
                this.isBefore = true;
                this.after([]);
            }

            this.savedVersions.length = 0;

            this.ajaxCall('desktop').done(function () {
                this.ajaxCall('mobile');
            }.bind(this));
        },

        toggleStateActive: function (type) {
            if ((this.isShowMobile() && type !== 'mobile') || (!this.isShowMobile() && type === 'mobile')) {
                this.isShowMobile(!this.isShowMobile());
                this.getTotalScore(this.score[type]);
            }

            this.resizeContent(type);
        },

        waitElement: function (selector, callback) {
            if ($(selector).length) {
                callback.call(this, selector);
            } else {
                setTimeout(this.waitElement.bind(this, selector, callback), 100);
            }
        },

        setMaxHeight: function (element) {
            this.maxHeight($(element).find(this.element.linkBlock).outerHeight() + 'px');
        },

        resizeContent: function (type) {
            this.waitElement('[data-amoptimizer-js="' + type + '"]', this.setMaxHeight);
        },

        /**
         * @param {string} totalValuation
         */
        getTotalScore: function (totalValuation) {
            this.totalValuation(Math.ceil(totalValuation * 100));
        },

        ajaxCall: function (version) {
            return $.ajax({
                url: 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed',
                type: 'GET',
                showLoader: true,
                data: { url: this.baseUrl, strategy: version, locale: this.locale },
                success: function (response) {
                    this.saveData(this.getParsedData(response), version);
                }.bind(this),
                error: function (response) {
                    var text = JSON.parse(response.responseText);

                    message({ content: text.error.message });
                    this.checkSavedVersions(version);
                }.bind(this)
            });
        },

        getParsedData: function (response) {
            return {
                audits: prepareData.getMinimalAudits(response.lighthouseResult.audits),
                totalValuation: response.lighthouseResult.categories.performance.score,
                pageUrl: response.id,
                date: response.analysisUTCTimestamp
            };
        },

        initInfoblock: function (response, version) {
            var data,
                totalValuation;

            data = response.audits;
            totalValuation = response.totalValuation;
            this.score[version] = totalValuation;
            this.isActive(true);
            this.pageUrl(response.pageUrl);
            this.contentData.push(data);

            if (this.isBefore) {
                this.isShowRecommendations(true);
            }

            this.isBefore = false;

            $(this.element.container).removeClass('-line');

            if (version === 'mobile') {
                this.getTotalScore(totalValuation);
                this.diagnosticDate(this.getDate(response.date));
                this.toggleStateActive(version);
            }

            createTab(this, version, prepareData.run(version, data, this.imageFolderOptimization), 'before-');
        }
    });
});
