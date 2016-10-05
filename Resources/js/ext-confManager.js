//# sourceURL=/resources/js/ext-confManager.js
require(
        ['jquery', '/resources/js/ext-formManager.js'],
        function (jQuery, FormManager) {
            'use strict';
            var confManager = {
                /**
                 * Init events binding
                 * @params {none}
                 * @return {object} The interface
                 */
                init: function () {
                    FormManager.bindChanges();
                    this.setupForms();
                    return this;
                },
                /**
                 * Setup forms from tabs content data-action & data-fields
                 * @params {none}
                 * @returns {confManager} For chaining
                 */
                setupForms: function () {
                    jQuery.each(jQuery('#bundle-admin .tab-pane[role=tabpanel]'), function (idx, el) {
                        if ('undefined' !== typeof jQuery(el).attr('id')) {
                            FormManager.displayForm(jQuery(el));
                        }
                    });
                    jQuery('section.bundle.conf li[role=presentation] a')[0].click();
                    return this;
                }
            };

            return confManager.init();
        }
);
