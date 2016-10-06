//# sourceURL=/resources/js/ext-formManager.js
MutationObserver = window.MutationObserver || window.WebKitMutationObserver;
define(
        [
            'Core',
            'jquery',
            'component!formbuilder',
            'component!formsubmitter',
            'Core/RequestHandler',
            'Core/Request',
            'component!Notify'
        ],
        function (Core, jQuery, FormBuilder, FormSubmitter, RequestHandler, Request, Notify) {
            /**
             * @return {object} The interface
             */
            var init = function () {
                return new interface();
            };
            /**
             * Init events binding
             */
            var bindChanges = function () {
                var currentTab = jQuery('#bundle-admin li[role=presentation].active');
                jQuery('#bundle-admin a[role=tab]').off('click').on('click', function (e) {
                    if (currentTab.hasClass('bg-danger')) {
                        Notify.warning('Some changes have not been saved on panel "' + currentTab.get(0).innerText + '"');
                    }
                    currentTab = jQuery(e.currentTarget).closest('li[role=presentation]');
                });

                jQuery('#bundle-admin').dialog().on('dialogbeforeclose', function () {
                    if (0 < jQuery('#bundle-admin').find('li[role=presentation].bg-danger').length) {
                        var confirm = window.confirm('Some changes have not been yet saved, close anyway?');

                        return true === confirm;
                    }

                    return true;
                });
            }
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
                    jQuery(el).find('input,textarea').off('input').on('input', function (field) {
                        var tab = jQuery(field.target).closest('.tab-pane[role=tabpanel]').attr('id');
                        jQuery('#bundle-admin a[role=tab][href=#' + tab + ']').closest('li[role=presentation]').addClass('bg-danger');
                    });
                    jQuery(el).find('select,input').off('change').on('change', function (field) {
                        var tab = jQuery(field.target).closest('.tab-pane[role=tabpanel]').attr('id');
                        jQuery('#bundle-admin a[role=tab][href=#' + tab + ']').closest('li[role=presentation]').addClass('bg-danger');
                    });
                    jQuery(el).find(':checkbox,:radio').off('click').on('click', function (field) {
                        var tab = jQuery(field.target).closest('.tab-pane[role=tabpanel]').attr('id');
                        jQuery('#bundle-admin a[role=tab][href=#' + tab + ']').closest('li[role=presentation]').addClass('bg-danger');
                    });
                    if (0 < jQuery('#bundle-admin').find('ul.nodeselector_list,ul.linkselector_list,ul.media_list').length) {
                        var observer = new MutationObserver(function (mutations, observer) {
                            var tab = jQuery(mutations[0].target).closest('.tab-pane[role=tabpanel]').attr('id');
                            jQuery('#bundle-admin a[role=tab][href=#' + tab + ']').closest('li[role=presentation]').addClass('bg-danger');
                        });
                        var addObserve = function (observer, selector) {
                            if (0 < jQuery('#bundle-admin').find(selector).length) {
                                observer.observe(document.querySelector("#bundle-admin " + selector), {childList: true});
                            }
                        };

                        addObserve(observer, 'ul.nodeselector_list');
                        addObserve(observer, 'ul.linkselector_list');
                        addObserve(observer, 'ul.media_list');
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

                    adminView = Core.get('current_admin_view')
                    adminView.sendRequest('update', jQuery(form).attr('action'), fields).done(function (response) {
                        var tab = jQuery('form#' + form.id).closest('.tab-pane[role=tabpanel]').attr('id');
                        jQuery('#bundle-admin a[role=tab][href=#' + tab + ']').closest('li[role=presentation]').removeClass('bg-danger');

                        jQuery.each(response.notification, function (i, notif) {
                            Notify[notif.type](notif.message);
                        });
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
                    submitForm: submitForm,
                    bindChanges: bindChanges
                };
            };
            /**
             * Return init() method result (ie. the interface)
             */
            return init();
        }
);
