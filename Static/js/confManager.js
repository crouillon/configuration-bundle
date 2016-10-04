//# sourceURL=bundle/configuration/confManager.js
require(
        ['jquery', '/bundle/configuration/resources/js/formManager.js'],
        function (jQuery, FormManager) {
            'use strict';
            var confManager = {
                /**
                 * Init events binding
                 * @params {none}
                 * @return {object} The interface
                 */
                init: function () {
                    this.setupForms();
                    return this;
                },
                /**
                 * Setup forms from tabs content data-action & data-fields
                 * @params {none}
                 * @returns {confManager} For chaining
                 */
                setupForms: function () {
                    jQuery.each(jQuery('.tab-pane[role=tabpanel]'), function (idx, el) {
                        if ('undefined' !== typeof $(el).attr('id')) {
                            FormManager.displayForm($(el));
                        }
                    });
                    jQuery('section.bundle.conf li[role=presentation] a')[0].click();
                    return this;
                }
            };
            return confManager.init();
        }
);
