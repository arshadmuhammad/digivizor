/**
 * Added wysiwyg plugin for tinymce global object
 */

require([
    'tiny_mce_4/tinymce.min'
    ], function(tinymce){
        tinymce.create('tinymce.plugins.amamp_image', {

            /**
             * Initialize editor plugin.
             *
             * @param {tinymce.editor} editor - Editor instance that the plugin is initialized in.
             * @param {String} url - Absolute URL to where the plugin is located.
             */
            init: function (editor, url) {
                var self = this;

                if (editor.id === "cms_page_form_amp_content") {
                    varienGlobalEvents.attachEventHandler('wysiwygEncodeContent', function (content) {
                        content = self.encodeImages(content);

                        return content;
                    });

                    varienGlobalEvents.attachEventHandler('wysiwygDecodeContent', function (content) {
                        content = self.decodeImages(content);

                        return content;
                    });
                }
            },

            /**
             * Encode images attributes in content
             *
             * @param {String} content
             * @returns {*}
             */
            encodeImages: function (content) {
                if (content.includes('<amp-img')) {
                    content = content
                        .replace(/<amp-img/g, '<img')
                        .replace(/layout=/g, 'data-mce-layout=')
                        .replace(/>\s*<\/amp-img>/g, ' data-mce-close="true"/>');
                }

                return content;
            },

            /**
             * Decode images attributes in content.
             *
             * @param {String} content
             * @returns {String}
             */
            decodeImages: function (content) {
                if (content.includes('amamp-image -wysiwyg')) {
                    content = content
                        .replace(/<img/g, '<amp-img')
                        .replace(/data-mce-layout=/g, 'layout=')
                        .replace(/ data-mce-close="true" \/>/g, '></amp-img>');
                }

                return content;
            },
        });

        // Register plugin
        tinymce.PluginManager.add('amamp_image', tinymce.plugins.amamp_image);
});
