/**
 * Action Create Tab
 */

define([
    'uiLayout',
    'mageUtils'
], function (layout, utils) {
    'use strict';

    return function (context, name,  data, prefix) {
        var prefix = prefix || '',
            element = utils.extend({}, {
            'name': prefix + 'chat-item-' + name,
            'component': 'Amasty_PageSpeedOptimizer/js/tab-column',
            'template': 'Amasty_PageSpeedOptimizer/tabs/content',
            'contentData': data,
            'isMobile': name === 'mobile',
            'prefix': '.-' + name + ' '
        });

        layout([element]);

        context.insertChild(prefix + 'chat-item-' + name);
    };
});
