(function () {
    if ('undefined' === typeof window.onTheFlyForm) {


        function removeBoundError(jTarget) {
            var id = jTarget.attr('data-error-popout');
            if ("undefined" !== typeof id) {
                var jForm = jTarget.closest("form");
                var jError = jForm.find('[data-error="' + id + '"]');
                if (jError.length) {
                    jError.hide();
                }
            }
        }


        /**
         * Analyze the given model, and takes one or more of the following actions:
         *
         * - display the success message (form level)
         * - display the error message (form level)
         * - display the validation error message(s) (control level)
         *
         */
        function onOffProtocolComplete(jContext, model) {
            var jSuccessMessage = jContext.find(".off-success-message-container");
            var jErrorMessage = jContext.find(".off-error-message-container");


            var jSuccessTextHolder = jSuccessMessage.find('.text-holder');
            if (0 === jSuccessTextHolder.length) {
                jSuccessTextHolder = jSuccessMessage;
            }
            var jErrorTextHolder = jErrorMessage.find('.text-holder');
            if (0 === jErrorTextHolder.length) {
                jErrorTextHolder = jErrorMessage;
            }


            if (true === model.isSuccess) {
                if ('' !== model.successMessage) { // allows the template author to provide default value
                    jSuccessTextHolder.html(model.successMessage);
                }
                jSuccessMessage.show();
            }
            else {

                if (true === model.validationOk) {
                    if ('' !== model.errorMessage) { // allows the template author to provide default value
                        jErrorTextHolder.html(model.errorMessage);
                    }
                    jErrorMessage.show();
                }
                else {
                    window.onTheFlyForm.injectValidationErrors(jContext, model);
                }
            }

        }


        window.onTheFlyForm = {

            /**
             * Ensures that every erroneous control of the given context
             * will have its corresponding error message removed when
             * the control is clicked or focused.
             *
             */
            formInit: function (jContext) {


                // add data-error-popout attribute dynamically
                jContext.find('[name]').each(function () {
                    $(this).attr('data-error-popout', $(this).attr('name'));
                    $(this).on("focus.onTheFlyFormInit", function (e) {
                        removeBoundError($(this));
                    });
                });


                // add data-error-popout behaviour
                jContext.on('click.onTheFlyFormInit', function (e) {
                    var jTarget = $(e.target);
                    removeBoundError(jTarget);
                });
            },
            /**
             * Clean the form from every error message (form level and control level),
             * and then re-inject the form with the validation error messages (control level)
             * found in the given model.
             */
            injectValidationErrors: function (jForm, model) {

                window.onTheFlyForm.cleanValidationErrorMessages(jForm);


                for (var key in model) {
                    if (0 === key.indexOf("error")) {
                        var errMsg = model[key];

                        if ('' !== errMsg) {


                            var suffix = key.substr(5);
                            var name = "name" + suffix;

                            if (name in model) {


                                var target = model[name];


                                var jErr = jForm.find('[data-error="' + target + '"]');
                                var jErrText = jErr.find('[data-error-text]');
                                if (0 === jErrText.length) {
                                    jErrText = jErr;
                                }

                                jErr.removeClass('hidden');
                                jErr.show();
                                jErrText.html(errMsg);


                                // // does it have a popout set?
                                // var jPopout = jForm.find('[data-error-popout="' + target + '"]');
                                // if (jPopout.length > 0) {
                                //     (function (jPop, jPopErr) {
                                //         jPop.off('focus.onTheFlyForm').on('focus.onTheFlyform', function () {
                                //             jPopErr.hide();
                                //         });
                                //     })(jPopout, jErr);
                                // }

                            }
                        }
                    }
                }
            },
            /**
             * Inject values into a form
             */
            injectRawValues: function (jContext, key2Values) {
                for (var key in key2Values) {
                    var value = key2Values[key];

                    var jControl = jContext.find('[name="' + key + '"]');


                    // single checkbox?
                    if (jControl.is(':checkbox')) {
                        if (1 === parseInt(value)) {
                            jControl.prop("checked", true);
                        }
                    }
                    // other input types
                    else {
                        // in onTheFlyForm so far, we deal only with simple names with no brackets
                        // however in a near future, brackets might be required.
                        // if so, try using jquery's [name^="pppp"] pattern instead (starts with)
                        jControl.val(value);
                    }

                }
            },
            /**
             * implements the off-protocol
             */
            // postForm: function (jTarget, uriService, onSuccess, onFormErrorAfter, onError) {
            //
            //     var jForm = jTarget.closest("form");
            //     window.onTheFlyForm.cleanForm(jForm);
            //     var itemData = jForm.serialize();
            //     $.post(uriService, {
            //         data: itemData
            //     }, function (r) {
            //         window.onTheFlyForm.handleAjaxResponse(jForm, r, onSuccess, onFormErrorAfter, onError);
            //     }, 'json');
            // },
            /**
             * Removes the following elements from the onTheFlyForm:
             *
             * - successMessage (form level)
             * - errorMessage (form level)
             * - validation error messages (control level)
             */
            // cleanForm: function (jContext) {
            //     var jSuccessMessage = jContext.find(".off-success-message-container");
            //     var jErrorMessage = jContext.find(".off-error-message-container");
            //
            //     jSuccessMessage.hide();
            //     jErrorMessage.hide();
            //     window.onTheFlyForm.cleanValidationErrorMessages(jContext);
            //
            // },
            cleanValidationErrorMessages: function (jContext) {
                /**
                 * Clean validation error messages
                 */
                var jErrorFields = jContext.find("[data-error]");
                jErrorFields.hide();
            },
            /**
             * Implements the off-protocol
             *
             */
            handleAjaxResponse: function (jContext, r, onSuccess, onFormErrorAfter, onError) {

                if ("success" === r.type) {
                    onOffProtocolComplete(jContext, r.model);
                    onSuccess(r.data);
                }
                else if ("formerror" === r.type) {
                    onOffProtocolComplete(jContext, r.model);
                    if (null !== onFormErrorAfter && 'undefined' !== typeof onFormErrorAfter) {
                        onFormErrorAfter();
                    }
                }
                else if ("error" === r.type) {
                    if ('undefined' === typeof onError) {
                        onError = function (m) {
                            console.log("exception error from on-the-fly-form: " + m);
                        };
                    }
                    onError(r.error);
                }
            },
            handleAjaxCompleteResponse: function (jContext, offModel) {
                onOffProtocolComplete(jContext, offModel);
            }
        };
    }
})();