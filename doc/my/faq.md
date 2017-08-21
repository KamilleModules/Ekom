Ekom FAQ
===========
2017-08-19





How can I extend the ekomJsApi from my module?
----------------------------------------------

You need to use the Hooks::Ekom_feedJsApiLoader hook,
which is itself called by the X::Ekom_jsApiLoader service,
which itself is called from E::loadEkomJsApi, 
which the theme author can call from anywhere (usually in a commonly included file).

Then, at the js code level, you can use the following example, which comes from the PeiPei module, as a starting point:

```js
(function () {


    if ('undefined' !== typeof window.ekomApi) { // you need to call ekomJsApi.js file first!!



        window.ekomJsApi.prototype.peipei = {
            creditCardWallet: {
                createCard: function (data, onSuccess, onFormDataErroneous, onError) {
                    ekomApi.inst().utils.request("json", "creditCardWallet.createCard", {
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
                            ekomApi.inst().trigger("peipei.ccw.cardAdded", data);
                        }

                    }, null, "PeiPei");
                },
            }
        };
    }


})();

```







 