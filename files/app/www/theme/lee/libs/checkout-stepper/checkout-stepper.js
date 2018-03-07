if ('undefined' === typeof window.CheckoutStepper) {

    /**
     *
     * Here are the few rules by which this class is governed.
     *
     *
     *
     * 1. Steps are numbered from 1 to X, X being the number of steps.
     * There is no gap between steps (i.e. increment=1 is constant).
     *
     *
     * 2. When a step is opened, by default it implicitly
     * means that all previous steps are done.
     *
     * 3. When a step is done, it can never return to its notset state.
     * 4. By default, all steps start with the notset state, except the one
     *          referenced by the start option, which is open.
     *
     *
     */

    function getNumberOfSteps(jContext) {
        return jContext.find(".step-open").length;
    }


    window.CheckoutStepper = function (conf) {

        var options = $.extend({
            context: null,
            start: 1,
            hide: function (jItem) {
                jItem.hide();
            },
            show: function (jItem) {
                jItem.show();
            }
        }, conf);


        var jContext = null;
        if (null !== options.context) {
            jContext = options.context;
        }


        this.currentStep = 0;
        this.states = {};
        this.jContext = jContext;
        this.doneStates = {};
        this.numberOfSteps = getNumberOfSteps(jContext);
        if (1 > this.numberOfSteps) {
            throw new Error("numberOfSteps cannot be less than 1");
        }
        this.hide = options.hide;
        this.show = options.show;
        var zis = this;


        // put all steps to notset, except the one referenced by options.start
        for (var i = 1; i <= this.numberOfSteps; i++) {
            this.jContext.find(".step-" + i).each(function () {
                zis.hide($(this));
            });
            this.jContext.find(".step-" + i + ".step-notset").each(function () {
                zis.show($(this));
            });
        }
        this.jContext.find(".step-" + options.start + ".step-notset").each(function () {
            zis.hide($(this));
        });
        this.jContext.find(".step-" + options.start + ".step-open").each(function () {
            zis.show($(this));
        });


    };
    window.CheckoutStepper.prototype = {
        openStep: function (stepNumber) {
            var zis = this;


            // open the step
            this.jContext.find(".step-" + stepNumber + ".step-notset, .step-" + stepNumber + ".step-done").each(function () {
                zis.hide($(this));
            });
            this.jContext.find(".step-" + stepNumber + ".step-open").each(function () {
                zis.show($(this));
            });


            // turn all other states either in notset mode or in done mode
            stepNumber = parseInt(stepNumber);
            for (var i = 1; i <= this.numberOfSteps; i++) {
                if (i !== stepNumber) {
                    if (i in this.doneStates) {
                        this.jContext.find(".step-" + i + ".step-notset, .step-" + i + ".step-open").each(function () {
                            zis.hide($(this));
                        });
                        this.jContext.find(".step-" + i + ".step-done").each(function () {
                            zis.show($(this));
                        });
                    }
                    else {
                        this.jContext.find(".step-" + i + ".step-done, .step-" + i + ".step-open").each(function () {
                            zis.hide($(this));
                        });
                        this.jContext.find(".step-" + i + ".step-notset").each(function () {
                            zis.show($(this));
                        });
                    }
                }
            }
        },
        markStepAsDone: function (stepNumber) {
            this.doneStates[stepNumber] = true;
        }
    };
}

