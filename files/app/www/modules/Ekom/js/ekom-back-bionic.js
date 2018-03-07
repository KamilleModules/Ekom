(function () {

    var api = ekomApi.inst();

    window.ekomRequestOnSuccessAfter = function () {
        window.bionicOnActionAfter();
    };


    window.bionicActionHandler = function (jObj, action, params, take) {  // override this function to get started
        switch (action) {

            //----------------------------------------
            // EKOM
            //----------------------------------------
            case 'back.selectShopId':
                var shopId = take('shop_id', params);
                api.back.context.selectShopId(shopId);
                break;
            case 'back.selectCurrencyId':
                var currencyId = take('currency_id', params);
                api.back.context.selectCurrencyId(currencyId);
                break;
            case 'back.selectLangId':
                var langId = take('lang_id', params);
                api.back.context.selectLangId(langId);
                break;
            default:
                console.log("Unknown action: " + action);
                console.log(params);
                break;

        }

    };


})();