(function () {


    if ('undefined' === typeof window.stanConfigurableItems) {


        var apis = {};


        function getPanelByItem(jTarget) {
            var jNext = jTarget.next();
            if (jNext.hasClass("stan-configurable-item-panel")) {
                return jNext;
            }
            return false;
        }


        window.stanConfigurableItems = function () {
            this.handlers = {};
            this.jContext = null;
        };
        window.stanConfigurableItems.prototype = {
            _init: function (jContext) {
                this.jContext = jContext;
                var zis = this;


                jContext.on('click', function (e) {
                    var jTarget = $(e.target);
                    var jClosest = jTarget.closest(".stan-configurable-item");
                    if (jClosest.length) {
                        jContext.find('.stan-configurable-item').removeClass("selected");
                        jClosest.addClass("selected");
                        var jNext = getPanelByItem(jClosest);
                        if (false !== jNext) {
                            var name = jNext.attr("data-name");
                            zis._openPanel(jNext);
                        }
                    }
                });


                jContext.find(".stan-configurable-item-panel").each(function () {
                    var name = $(this).attr("data-name");
                    zis._callHandlerMethod(name, "init", null, $(this));
                });
            },
            /**
             *
             * Gets info from the selected item (using the getData method of the module) and call
             * the corresponding callback (onSuccess or onError)
             *
             *
             * @param onSuccess ( id, options )
             * @param onError ( errMsg )
             */
            execute: function (onSuccess, onError) {
                var jSelected = this.jContext.find(".stan-configurable-item.selected:first");
                if (jSelected.length > 0) {
                    var jNext = getPanelByItem(jSelected);
                    if (false !== jNext) {
                        var name = jNext.attr("data-name");
                        var data = this._callHandlerMethod(name, "getData", null, jNext);
                        var id = jSelected.attr("data-id");
                        onSuccess(id, data);
                    }
                    else {
                        onError("panel not found", jSelected);
                    }
                }
                else {
                    onError("no selected item found");
                }
            },
            addHandler: function (name, oHandler) {
                this.handlers[name] = oHandler;
            },
            _callHandlerMethod: function (name, method, fallbackCallback) {

                var args = Array.prototype.slice.call(arguments, 3);


                if (name in this.handlers) {
                    var handler = this.handlers[name];
                    if (method in handler) {
                        return handler[method].apply(null, args);
                    }
                    else {
                        if ('undefined' !== typeof fallbackCallback && null !== fallbackCallback) {
                            return fallbackCallback.apply(null, args);
                        }
                    }
                }
                return false;
            },
            _openPanel: function (jPanel) {
                var name = jPanel.attr("data-name");
                for (var zename in this.handlers) {
                    if (zename === name) {
                        this._callHandlerMethod(name, "openPanel", function (jModulePanel) {
                            jModulePanel.show();
                        }, jPanel);
                    }
                    else {

                        var jOtherPanel = this.jContext.find('.stan-configurable-item-panel[data-name="' + zename + '"]');
                        if (jOtherPanel.length > 0) {
                            this._callHandlerMethod(zename, "closePanel", function (jModulePanel) {
                                jModulePanel.hide();
                            }, jOtherPanel);
                        }
                    }
                }
            }
        };


        //----------------------------------------
        // STATIC METHODS (bound to the class "symbol" rather than a particular instance
        //----------------------------------------
        var initCallbacks = [];

        /**
         * @param apiName
         * @param initCallback ( oApi )
         */
        stanConfigurableItems.moduleAddCallback = function (apiName, initCallback) {
            initCallbacks.push([apiName, initCallback]);
        };

        stanConfigurableItems.init = function (apiName, jContext) {
            var o = new window.stanConfigurableItems();

            for (var i in initCallbacks) {
                var _apiName = initCallbacks[i][0];
                var initCallback = initCallbacks[i][1];
                if (_apiName === apiName) {
                    initCallback(o);
                }
            }
            o._init(jContext);
            apis[apiName] = o;
            return o;
        };


        stanConfigurableItems.inst = function (apiName) {
            if (apiName in apis) {
                return apis[apiName];
            }
            throw new Error("No api with name " + apiName);
        };
    }
})();