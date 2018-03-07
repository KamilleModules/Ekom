(function () {


    /**
     * Main guidelines
     * ====================
     *
     * - for ajax form interactions, we use the off protocol:
     *          https://github.com/lingtalfi/OnTheFlyForm/blob/master/doc/off-protocol.md
     *
     *
     */
    if ('undefined' === typeof window.ekomApi) {

        //----------------------------------------
        // UTILS
        //----------------------------------------
        var request = function (type, action, data, success, error, module) {
            if ('undefined' === typeof module) {
                module = "Ekom";
            }
            var url = "/service/" + module + "/" + type + "/api?action=" + action;
            $.post(url, data, function (r) {
                if ('gscp' === type) {
                    if ('success' === r.type) {
                        success && success(r.data);
                    }
                    else {
                        if ('undefined' !== typeof error && null !== error) {
                            error(r.data);
                        }
                        else {
                            console.log("gscp error: " + r.data);
                        }
                    }
                }
                else {
                    success(r);
                }
            }, 'json');
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
        window.ekomJsApiOld = function () {
        };
        window.ekomJsApiOld.prototype = {
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
                addItem: function (product_id, qty, productDetails, extraArgs, options) {


                    var _params = {
                        qty: qty,
                        product_id: product_id
                    };

                    for (var name in extraArgs) {
                        _params[name] = extraArgs[name];
                    }

                    if (true === $.isPlainObject(productDetails)) {
                        _params.details = productDetails;
                    }


                    request('gscp', 'cart.addItem', _params, function (model) {


                        if (true === $.isPlainObject(options) && 'onSuccess' in options) {
                            options.onSuccess(model, product_id, productDetails);
                        }

                        obs.notify("cart.itemAdded", model, product_id, qty);
                        obs.notify("cart.updated", model);


                    }, function (msg) {
                        alert(msg);
                    });
                },
                removeItem: function (token) {
                    request('gscp', 'cart.removeItem', {
                        token: token
                    }, function (data) {
                        obs.notify("cart.itemRemoved", token);
                        obs.notify("cart.updated", data);
                    });
                },
                updateItemQuantity: function (token, newQty) {
                    var data = {
                        qty: newQty,
                        token: token
                    };


                    obs.notify("collectParams.updateItemQuantity", data);

                    request('gscp', 'cart.updateItemQuantity', data, function (data) {
                        obs.notify("cart.updated", data);
                    }, function (msg) {
                        alert(msg);
                    });
                },
                addCoupon: function (code, force, onResponse) {
                    if (true === force) {
                        force = 1;
                    }
                    else {
                        force = 0;
                    }
                    request('json', 'cart.addCoupon', {
                        code: code,
                        force: force
                    }, function (data) {
                        if ('error' === data.type) {
                            onResponse(data.errors, 'error');
                        }
                        else if ('confirm' === data.type) {
                            onResponse(data.message, 'confirm');
                        }
                        else {
                            onResponse(data.message, 'success');
                            obs.notify("cart.updated", data.model);
                        }
                    });
                },
                removeCoupon: function (index) {
                    request('json', 'cart.removeCoupon', {
                        index: index
                    }, function (data) {
                        obs.notify("cart.updated", data);
                    });
                }
            },
            checkout: {
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
                placeOrder: function (onSuccess) {
                    request('json', 'checkout.placeOrder', {}, function (data) {
                        if ('success' === data.type) {
                            onSuccess && onSuccess(data.orderModel);
                        }
                        else if ('error' === data.type) {
                            console.log(data.msg);
                        }
                        else if ('publicError' === data.type) {
                            alert(data.msg);
                        }
                    });
                },
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
                deleteAddress: function (addrId, onSuccess) {
                    if (true === confirm("Are you sure you want to execute this action?")) {
                        request("gscp", "user.removeAddress", {
                            address_id: addrId
                        }, function (data) {
                            onSuccess(data);
                            obs.notify("user.address.updated", data);
                            obs.notify("user.address.deleted", data, addrId);
                        });
                    }
                },
                getAddressInfo: function (addrId, onSuccess) {
                    request("gscp", "user.getAddressInfo", {
                        address_id: addrId
                    }, function (data) {
                        onSuccess(data);
                    });
                },
                addProductToWishlist: function (product_id, productDetails) {
                    request("json", "user.addToWishlist", {
                        product_id: product_id,
                        product_details: productDetails
                    }, function (data) {
                        if ('error' === data.type) {
                            alert(data.errMsg); // public error message
                        }
                        else {
                            if ("1" === data.hasChanged) {
                                obs.notify("user.wishlist.updated", data);
                            }
                        }
                    });
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
            },
            debounce: function (func, wait, immediate) {
                return debounce(func, wait, immediate);
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


        $(document).ready(function () {


                //----------------------------------------
                //
                //----------------------------------------
                var o = ekomApi.inst();
                // o.on("cart.updated", function (cartData) {
                //     console.log("ok, has cart data");
                //     console.log(cartData);
                // });

                // o.cart.addItem(6, 1);


            }
        );
    }


})();