(function () {

    /**
     * To override hooks, use the following example:
     *
     * window.ekomJsApi.prototype.hooks.onRequestLoaderEnd = window.ninShadowHelper.onRequestLoaderEnd;
     *
     */
    if ('undefined' === typeof window.ekomApi) {

        window.ekomIntent = null;
        window.ekomRequestOnSuccessAfter = null;

        var instance = null;
        //----------------------------------------
        // UTILS
        //----------------------------------------
        var devError = console.log;
        /**
         * @param target: (module:)?action
         * @param type: ecp|html
         * @param options:
         *          - ?onInvalidError
         *          - ?onPublicError
         */
        var request = function (target, data, onSuccess, options, type) {
            var zis = instance;

            // intent implementation
            var intent = window.ekomIntent; // this should be an array
            window.ekomIntent = null; // reset for the next time

            var module = "Ekom";
            var action = target;
            type = type || "ecp";
            options = $.extend({
                onInvalidError: null,
                onPublicError: null,
                onSuccessMessage: null
            }, options);

            var onInvalidError = options.onInvalidError || instance.hooks.onRequestInvalidError;
            var onPublicError = options.onPublicError || instance.hooks.onRequestPublicError;
            var onSuccessMessage = options.onSuccessMessage || instance.hooks.onRequestSuccessMessage;


            var p = target.split(":", 2);
            if (2 === p.length) {
                module = p[0];
                action = p[1];
            }
            zis.hooks.onRequestLoaderPrepare(action, module);
            zis.hooks.onRequestLoaderStart(action, module);

            var url = "/service/" + module + "/" + type + "/api?action=" + action;
            if (null !== intent) {
                data.intent = intent;
            }
            if ('ecp' === type) {

                $.post(url, data, function (response) {
                    if ($.isPlainObject(response)) {
                        var hasError = false;
                        if ('$$success$$' in response) {
                            onSuccessMessage && onSuccessMessage(response['$$success$$']);
                        }
                        else if ('$$invalid$$' in response) {
                            hasError = true;
                            onInvalidError && onInvalidError(response['$$invalid$$']);
                        }
                        else if ('$$error$$' in response) {
                            hasError = true;
                            onPublicError && onPublicError(response['$$error$$']);
                        }


                        if (false === hasError) {
                            onSuccess && onSuccess(response);
                            window.ekomRequestOnSuccessAfter && window.ekomRequestOnSuccessAfter(response);
                        }


                    }
                    else {
                        devError("A plain object has not been returned, check your console.log");
                        console.log(response);
                    }
                }, 'json').always(function () {

                    zis.hooks.onRequestLoaderEnd(target);
                });
            }
            else {
                console.log("not handled yet");
            }
        };


        // https://davidwalsh.name/javascript-debounce-function
        var debounce = function (func, wait, immediate) {
            var timeout;
            return function () {
                var context = this, args = arguments;
                var later = function () {
                    timeout = null;
                    if (!immediate) func.apply(context, args);
                };
                var callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func.apply(context, args);
            };
        };


        /**
         * Use this function internally,
         * when your api public method allows the user to provide
         * an error callback, which handles an exceptional error.
         * Since the exceptional error is the dev's fault,
         * it should be handled server side already (you might have a trace in the logs),
         * and so on the client side the default behaviour is just to log the error message,
         * so that the local dev can see it in the browser.
         */
        var getErrorCallback = function (error) {
            if ('undefined' !== typeof error && null !== error) {
                return error;
            }
            return function (m) {
                console.log("Exceptional Error from ekomJsApi: " + m);
            };
        };


        //----------------------------------------
        // OBSERVER
        //----------------------------------------
        var observer = function () {
            this.listeners = {};
            this.onceListeners = {};
        };
        observer.prototype = {
            notify: function (eventName) {
                var args = Array.prototype.slice.call(arguments, 1);
                if (eventName in this.listeners) {
                    var listeners = this.listeners[eventName];
                    for (var i  in listeners) {
                        var cb = listeners[i];
                        // console.log(eventName, args, 'end');
                        cb.apply(this, args);
                    }

                    if (eventName in this.onceListeners) {
                        delete this.listeners[eventName];
                    }
                }
            },
            addListener: function (eventName, cb, options) {

                if (false === $.isArray(eventName)) {
                    eventName = [eventName];
                }

                for (var i in eventName) {
                    var event = eventName[i];
                    if (false === (event in this.listeners)) {
                        this.listeners[event] = [];
                    }
                    this.listeners[event].push(cb);


                    if ($.isPlainObject(options) && 'once' in options && true === options.once) {
                        this.onceListeners[event] = true;
                    }
                }
            },
            setListener: function (eventName, cb) {

                if (false === $.isArray(eventName)) {
                    eventName = [eventName];
                }
                for (var i in eventName) {
                    var event = eventName[i];
                    this.listeners[event] = [cb];
                }
            },
            removeListener: function (eventName) {
                if (false === $.isArray(eventName)) {
                    eventName = [eventName];
                }
                for (var i in eventName) {
                    var event = eventName[i];
                    delete this.listeners[event];
                }
            }
        };
        var obs = new observer();


        //----------------------------------------
        // EKOM JS API
        //----------------------------------------
        window.ekomJsApi = function () {
        };
        window.ekomJsApi.prototype = {
            hooks: { // external code can override this
                onRequestInvalidError: function (msg) {
                    console.log("EkomApi: " + msg);
                },
                onRequestPublicError: function (msg) {
                    alert(msg);
                },
                onRequestSuccessMessage: function (msg) {
                    alert(msg);
                },
                onRequestLoaderPrepare: function (action, module) {

                },
                onRequestLoaderStart: function (action, module) {

                },
                onRequestLoaderEnd: function (action, module) {

                }
            },
            utils: {
                request: request,
                sqlDateToFormat: function (sqlDate, format, sep) {
                    if ('undefined' === typeof sep) {
                        sep = '/';
                    }
                    if ('dmy' === format) {
                        // https://stackoverflow.com/questions/6040515/how-do-i-get-month-and-date-of-javascript-in-2-digit-format
                        var oDate = new Date(sqlDate);
                        return ("0" + oDate.getDate()).slice(-2) + sep + ("0" + (oDate.getMonth() + 1)).slice(-2) + sep + oDate.getFullYear();
                    }
                    else {
                        console.log("ekomJsApi: error: unknown format " + format);
                    }
                },
                /**
                 * A parse_str simpler equivalent (not perfect, but enough for simple values).
                 * https://stackoverflow.com/questions/2090551/parse-query-string-in-javascript
                 */
                parseQuery: function (qstr) {
                    var query = {};
                    var a = (qstr[0] === '?' ? qstr.substr(1) : qstr).split('&');
                    for (var i = 0; i < a.length; i++) {
                        var b = a[i].split('=');
                        query[decodeURIComponent(b[0])] = decodeURIComponent(b[1] || '');
                    }
                    return query;
                },
                debounce: function (func, wait, immediate) {
                    return debounce(func, wait, immediate);
                }
            },
            bundle: {
                addToCart: function (bundleId, removedProductIds) {
                    request('gscp', 'bundle.addToCart', {
                        bundleId: bundleId,
                        removedProductIds: removedProductIds
                    }, function (data) {
                        var productId2Qty = data.productId2Qty;
                        for (var product_id in productId2Qty) {
                            var qty = productId2Qty[product_id];
                            obs.notify("cart.itemAdded", data.cartModel, product_id, qty);
                        }
                        obs.notify("cart.updated", data.cartModel);
                    });
                },
                getBundleModel: function (productId, removedProductIds) {
                    request('gscp', 'bundle.getBundleModel', {
                        productId: productId,
                        removedProductIds: removedProductIds
                    }, function (data) {
                        obs.notify("bundle.updated", data, productId, removedProductIds);
                    });
                }
            },
            // cart
            cart: {
                addItem: function (quantity, productId, extraArgs) {
                    var payload = extraArgs;
                    payload.quantity = quantity;
                    payload.product_id = productId;

                    request('cart.addItem', payload, function (data) {
                        obs.notify("cart.itemAdded", data, productId, quantity);
                        obs.notify("cart.updated", data);
                    });
                },
                removeItem: function (token) {
                    request('cart.removeItem', {
                        token: token
                    }, function (data) {
                        obs.notify("cart.itemRemoved", token);
                        obs.notify("cart.updated", data);
                    });
                },
                updateItemQuantity: function (token, newQuantity) {
                    var data = {
                        token: token,
                        quantity: newQuantity
                    };
                    /***
                     * @todo-ling: is the line below deprecated?
                     */
                    // obs.notify("collectParams.updateItemQuantity", data);

                    request('cart.updateItemQuantity', data, function (data) {
                        obs.notify("cart.updated", data);
                    });
                },
                addCoupon: function (code) {
                    request('cart.addCoupon', {
                        code: code
                    }, function (data) {
                        obs.notify("cart.updated", data);
                    });
                },
                removeCoupon: function (code) {
                    request('cart.removeCoupon', {
                        code: code
                    }, function (data) {
                        obs.notify("cart.updated", data);
                    });
                }
            },
            checkout: {
                switchStep: function (step) {
                    request('checkout.switchStep', {
                        _step: step
                    }, function (data) {
                        obs.notify("checkout.dataUpdated", data);
                    });
                },
                updateStep: function (context) {
                    request('checkout.updateStep', context, function (data) {
                        obs.notify("checkout.dataUpdated", data);
                    });
                },
                placeOrder: function () {
                    request('checkout.placeOrder', {}, function (data) {
                        if (data.ok) {
                            obs.notify("checkout.placeOrderSuccessAfter", data);
                        }
                    });
                },
                //----------------------------------------
                // OLD
                //----------------------------------------
                setShippingBillingSynced: function (value, onSuccess, onError) {
                    request("gscp", "checkout.setShippingBillingSynced", {
                        "value": value
                    }, function (d) {
                        obs.notify("checkout.shippingBillingSynced", d);
                        onSuccess && onSuccess(d);
                    }, onError);
                },
                setCarrierName: function (name, onSuccess, onError) {
                    request("gscp", "checkout.setCarrierName", {
                        name: name
                    }, function (d) {
                        obs.notify("checkout.order_updated", d);
                        onSuccess && onSuccess(d);
                    }, onError);
                },
                setShippingAddressId: function (id, onSuccess, onError) {

                    request("gscp", "checkout.setShippingAddressId", {
                        id: id
                    }, function (d) {
                        // obs.notify("checkout.address.selected", d); // old amazon style...
                        obs.notify("checkout.shippingAddressChanged", d);
                        obs.notify("checkout.order_updated", d);
                        onSuccess && onSuccess(d);
                    }, onError);
                },
                setBillingAddressId: function (id, onSuccess, onError) {

                    request("gscp", "checkout.setBillingAddressId", {
                        id: id
                    }, function (d) {
                        obs.notify("checkout.billingAddressChanged", d);
                        // obs.notify("checkout.order_updated", d);
                        onSuccess && onSuccess(d);
                    }, onError);
                },
                setShippingAndBillingAddressId: function (id, onSuccess, onError) {

                    request("gscp", "checkout.setShippingAndBillingAddressId", {
                        id: id
                    }, function (d) {
                        // obs.notify("checkout.address.selected", d); // old amazon style...
                        obs.notify("checkout.shippingAddressChanged", d);
                        obs.notify("checkout.billingAddressChanged", d);
                        obs.notify("checkout.order_updated", d);
                        onSuccess && onSuccess(d);
                    }, onError);
                },
                setPaymentMethod: function (id, paymentMethodOptions, onSuccess, onError) {
                    request("gscp", "checkout.setPaymentMethod", {
                        id: id,
                        options: paymentMethodOptions
                    }, onSuccess, onError);
                },
                updateProductQuantity: function (product_id, newQty) {
                    request('gscp', 'checkout.updateItemQuantity', {
                        qty: newQty,
                        product_id: product_id
                    }, function (data) {
                        obs.notify("checkout.cart.updated", data);
                    });
                },
                // placeOrder: function (onSuccess) {
                //     request('json', 'checkout.placeOrder', {}, function (data) {
                //         if ('success' === data.type) {
                //             onSuccess && onSuccess(data.orderModel);
                //         }
                //         else if ('error' === data.type) {
                //             console.log(data.msg);
                //         }
                //         else if ('publicError' === data.type) {
                //             alert(data.msg);
                //         }
                //     });
                // },
                /**
                 * If data contains the following key:
                 *
                 * - addressId,
                 *
                 * it means update instead of insert.
                 *
                 */
                saveAddress: function (data, onSuccess, onFormDataErroneous, onError) {

                    onError = getErrorCallback(onError);
                    request("json", "checkout.saveAddress", {
                        data: data
                    }, function (data) {
                        if ("success" === data.type) {
                            onSuccess && onSuccess(data);
                            obs.notify("checkout.address.updated", data);
                            obs.notify("checkout.order_updated", data);
                        }
                        else if ('formerror' === data.type) {
                            onFormDataErroneous(data.model);
                        }
                        else {
                            onError(data.error);
                        }
                    });
                }
            },
            comment: {
                createComment: function (data, onSuccess, onFormDataErroneous, onError) {
                    request("json", "comment.createComment", {
                        data: data
                    }, function (data) {
                        if ("error" === data.type) {
                            onError(data.error);
                        }
                        else if ('formerror' === data.type) {
                            onFormDataErroneous(data.model);
                        }
                        else {
                            onSuccess(data);
                            obs.notify("comment.created", data);
                        }

                    });
                }
            },
            user: {
                /**
                 * @todo-ling: redo this later
                 */
                addressForm: function (payload) {
                    if ("undefined" === typeof payload) {
                        payload = {};
                    }
                    request('user.getAddressForm', payload, function (data) {
                        if (true === data.isSuccess) {
                            obs.notify("user.address.created", data);
                        }
                        else {
                            obs.notify("user.addressFormReady", data);
                        }
                    });
                },
                /**
                 * @todo-ling: redo this later
                 */
                addressList: function (params) {
                    request('user.getAddresses', params, function (data) {
                        obs.notify("user.addressesListReady", data);
                    });
                },
                removeAddress: function (addressId) {
                    if (true === confirm("Are you sure you want to execute this action?")) {
                        request("user.removeAddress", {
                            address_id: addressId
                        }, function (data) {
                            obs.notify("user.address.updated", data);
                            obs.notify("user.address.deleted", data);
                        });
                    }
                },
                addProductToWishlist: function (product_id, productDetails) {
                    var payload = {
                        product_id: product_id,
                        details: productDetails
                    };

                    request('user.addProductToWishlist', payload, function (data) {
                        obs.notify("user.wishlist.updated", data);
                    });
                },
                removeWishlist: function () {
                    var payload = {};

                    request('user.removeWishlist', payload, function (data) {
                        obs.notify("user.wishlist.updated", data);
                        obs.notify("user.wishlist.flushed", data);
                    });
                },
                removeWishlistItem: function (product_id) {
                    var payload = {
                        product_id: product_id
                    };
                    request('user.removeWishlistItem', payload, function (data) {
                        obs.notify("user.wishlist.updated", data);
                    });
                },
                subscribeToNewsletter: function (email) {
                    var payload = {
                        email: email
                    };
                    request('user.subscribeToNewsletter', payload, function (data) {
                        // just be happy
                    });
                },
                //----------------------------------------
                //
                //----------------------------------------
                /**
                 * If data contains the following key:
                 *
                 * - addressId,
                 *
                 * it means update instead of insert.
                 *
                 */
                saveAddress: function (data, onSuccess, onFormDataErroneous, onError) {

                    onError = getErrorCallback(onError);


                    request("json", "user.saveAddress", {
                        data: data
                    }, function (data) {
                        if ("success" === data.type) {
                            onSuccess && onSuccess(data);
                            obs.notify("user.address.updated", data);
                        }
                        else if ('formerror' === data.type) {
                            onFormDataErroneous(data.model);
                        }
                        else {
                            onError(data.error);
                        }

                    });
                },
                getAddressInfo: function (addrId, onSuccess) {
                    request("gscp", "user.getAddressInfo", {
                        address_id: addrId
                    }, function (data) {
                        onSuccess(data);
                    });
                },

                deprAddProductToWishlist: function (product_id, productDetails) {
                    var payload = {
                        product_id: product_id,
                        details: productDetails
                    };

                    request('user.addProductToWishlist', payload, function (data) {
                        obs.notify("user.wishlist.updated", data);
                    });
                }
            },
            // cart
            product: {
                getInfo: function (productId, details) {
                    request('product.getInfo', {
                        product_id: productId,
                        details: details
                    }, function (data) {
                        obs.notify("product.infoReady", data);
                    });
                }
            },
            back: {
                context: {
                    selectShopId: function (shopId) {
                        request('back.selectShopId', {
                            shop_id: shopId
                        });
                    },
                    selectCurrencyId: function (id) {
                        request('back.selectCurrencyId', {
                            currency_id: id
                        });
                    },
                    selectLangId: function (id) {
                        request('back.selectLangId', {
                            lang_id: id
                        });
                    }
                }
            },
            // observer
            on: function (eventName, cb) {
                obs.addListener(eventName, cb);
                return this;
            },
            off: function (eventName) {
                obs.removeListener(eventName);
                return this;
            },
            once: function (eventName, cb) {
                obs.addListener(eventName, cb, {
                    once: true
                });
            },
            trigger: function (eventName) { // you can pass any number of args if you want
                var args = Array.prototype.slice.call(arguments, 1);
                var zeArgs = [eventName];
                for (var i in args) {
                    zeArgs.push(args[i]);
                }
                obs.notify.apply(obs, zeArgs);
            }
        };

        //----------------------------------------
        // SPREADING OUT
        //----------------------------------------
        window.ekomApi = {
            inst: function () {
                return new ekomJsApi();
            }
        };
        instance = ekomApi.inst();
    }
})();