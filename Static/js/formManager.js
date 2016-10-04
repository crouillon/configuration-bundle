//# sourceURL=bundle/configuration/formsmanager.js
MutationObserver = window.MutationObserver || window.WebKitMutationObserver;
define(
        [
            'jquery',
            'component!formbuilder',
            'component!formsubmitter',
            'Core/RequestHandler',
            'Core/Request'
        ],
        function (jQuery, FormBuilder, FormSubmitter, RequestHandler, Request) {
            /**
             * Init events binding
             * @return {object} The interface
             */
            var init = function () {
                return new interface();
            };
            /**
             * Display form matching the provided el
             * @param {Object} el
             * @returns {boolean} true
             */
            var displayForm = function (el) {
                var self = this;
                if ('undefined' === typeof el || 'undefined' === typeof (fields = el.data('fields'))) {
                    return;
                }
                var config = {
                    elements: fields,
                    onSubmit: jQuery.proxy(self.submitForm, self),
                    form: {
                        action: el.data('action')
                    }
                };
                FormBuilder.renderForm(config).done(function (html) {
                    // Add form to tab
                    el.html(html);
                    // And observe form element changes
                    $(el).find('input').off('input').on('input', function (field) {
                        var tab = $(field.target).closest('.tab-pane[role=tabpanel]').attr('id');
                        $('a[role=tab][href=#' + tab + ']').closest('li[role=presentation]').addClass('bg-danger');
                    });
                    $(el).find('select').off('change').on('change', function (field) {
                        var tab = $(field.target).closest('.tab-pane[role=tabpanel]').attr('id');
                        $('a[role=tab][href=#' + tab + ']').closest('li[role=presentation]').addClass('bg-danger');
                    });
                    if (0 < $('ul.linkselector_list').length) {
                        var observer = new MutationObserver(function (mutations, observer) {
                            var tab = $(mutations[0].target).closest('.tab-pane[role=tabpanel]').attr('id');
                            $('a[role=tab][href=#' + tab + ']').closest('li[role=presentation]').addClass('bg-danger');
                        });
                        observer.observe(document.querySelector("ul.linkselector_list"), {childList: true});
                    }
                });
                return true;
            };
            /**
             * Submit form
             * @param {Object} data
             * @param {Object} form
             * @returns {boolean} true
             */
            var submitForm = function (data, form) {
                FormSubmitter.process(data, form).done(function (fields) {
                    for (var key in fields) {
                        if (fields.hasOwnProperty(key) && fields[key] === null) {
                            delete fields[key];
                        }
                    }
                    jQuery('#bundle-admin').prepend(jQuery('<div></div>', {class: 'overlay'})).show();
                    var request = new Request();
                    request.setData(JSON.stringify(fields))
                            .setMethod('POST')
                            .setUrl(jQuery(form).attr('action'))
                            .setContentType('application/json');
                    RequestHandler.send(request).done(function (response) {
                        var tab = jQuery('form#' + form.id).closest('.tab-pane[role=tabpanel]').attr('id');
                        jQuery('a[role=tab][href=#' + tab + ']').closest('li[role=presentation]').removeClass('bg-danger');
                    }).fail(function (exception) {
                        console.log(exception);
                    }).always(function (response) {
                        jQuery('#bundle-admin .overlay').remove();
                    });
                });
                return true;
            };
            /**
             * The public API interface of user object
             * @return {Object} The interface
             */
            var interface = function () {
                return {
                    displayForm: displayForm,
                    submitForm: submitForm
                };
            };
            /**
             * Return init() method result (ie. the interface)
             */
            return init();
        }
);
