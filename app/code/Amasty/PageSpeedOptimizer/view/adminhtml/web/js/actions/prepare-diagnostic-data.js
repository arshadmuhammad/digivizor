/**
 * Action Prepare Diagnostic Data
 */

define([
    'jquery',
    'underscore',
    'mage/translate'
], function ($, _) {
    'use strict';

    return {
        audits: {
            'render-blocking-resources': {
                isAmastyDescription: true
            },
            'uses-webp-images': {
                isAmastyDescription: true
            },
            'uses-responsive-images': {
                isAmastyDescription: true
            },
            'redirects': {
                isAmastyDescription: true
            },
            'offscreen-images': {
                isAmastyDescription: true
            },
            'unused-css-rules': {
                isAmastyDescription: true
            },
            'uses-optimized-images': {
                isAmastyDescription: true
            },
            'uses-text-compression': {
                isAmastyDescription: true
            },
            'efficient-animated-content': {
                isAmastyDescription: true
            },
            'uses-rel-preload': {
                isAmastyDescription: true
            },
            'unminified-css': {
                isAmastyDescription: true
            },
            'unminified-javascript': {
                isAmastyDescription: true
            },
            'total-byte-weight': {
                isAmastyDescription: true
            },
            'dom-size': {
                isAmastyDescription: true
            },
            'time-to-first-byte': {
                isAmastyDescription: true
            },
            'uses-rel-preconnect': {},
            'bootup-time': {},
            'third-party-summary': {},
            'uses-long-cache-ttl': {}
        },

        linkText: 'Content > Google Page Speed Optimizer > Image Folder Optimization Settings',

        /**
         * @param {string} type
         * @param {object} data
         * @param {string} imageFolderOptimization
         */
        run: function (type, data, imageFolderOptimization) {
            var warningArray = [],
                key;

            const hightValue = 0.89;

            for (key in data) {
                if (data[key].score === null || data[key].score > hightValue || !this.audits[key]) continue;

                this.audits[key].title = data[key].title;
                this.audits[key].score = data[key].score;
                this.audits[key].id = data[key].id;
                this.audits[key].description = this.parseDescription(data[key].description);
                this.audits[key].displayValue = data[key].displayValue ? data[key].displayValue : '';
                this.audits[key].linkUrl = imageFolderOptimization;


                warningArray.push(this.audits[key]);
            }

            return warningArray;
        },

        getMinimalAudits: function (audits) {
            var minimalAudits = {},
                key;

            for (key in this.audits) {
                if (!_.isUndefined(audits[key])) {
                    minimalAudits[key] = audits[key];
                    delete minimalAudits[key].details;
                    delete minimalAudits[key].scoreDisplayMode;
                    delete minimalAudits[key].numericValue;
                }
            }

            return minimalAudits;
        },

        /**
         * @param {string} text
         * @returns {[]|{description: *}}
         */
        parseDescription: function (text) {
            var descriptionArr = [],
                splittedText,
                matches;

            matches = text.match(/\[[-\W\w\s]+\]\(.*?\)/gm);
            if (matches === null) {
                return { description: text }
            }

            splittedText = text.split(/(\[([-\W\w\s]+?)\]\((.*?)\))/gm);

            for (var i = 0; i < Math.floor(splittedText.length / 4); i++) {
                descriptionArr.push(
                    {
                        prevText: splittedText[i * 4],
                        link: splittedText[i * 4 + 3],
                        linkTitle: splittedText[i * 4 + 2]
                    }
                );
            }

            if (splittedText.length % 4 === 1) {
                descriptionArr.push(
                    {
                        prevText: splittedText[splittedText.length - 1],
                        link: ''
                    }
                );
            }

            descriptionArr.hasLink = true;

            return descriptionArr;
        },

        checkAmastyLink: function (text, imageFolderOptimization) {
            if (text.indexOf(this.linkText) === -1) return '';

            return {
                url: imageFolderOptimization,
                prevText: text.split(this.linkText)[0],
                linkText: this.linkText,
                afterText: text.split(this.linkText)[1]
            };
        },
    };
});
