(function () {

    var api = ekomApi.inst();
    window.bionicActionHandler = function (jObj, action, params, take) {  // override this function to get started

        //----------------------------------------
        // EKOM BIONIC SPECIFIC
        //----------------------------------------
        /**
         * @todo-ling: to externalize bionic, you need to import the code below
         * as a hook
         */
        /**
         * @todo-ling
         * Note: hookAfter system works, but maybe we don't need it.
         * If you go for it, don't forget to update the bionic documentation
         */
        switch (action) {

            //----------------------------------------
            // EKOM
            //----------------------------------------
            // cart
            //----------------------------------------
            case 'cart.addItem':
                var quantity = take('quantity', params);
                var productId = take('product_id', params);
                api.cart.addItem(quantity, productId, params);
                break;
            case 'cart.updateItemQuantity':
                var token = take('token', params);
                var newQuantity = take('quantity', params);
                api.cart.updateItemQuantity(token, newQuantity);
                break;
            case 'cart.removeItem':
                var token = take('token', params);
                api.cart.removeItem(token);
                break;

            // coupon
            //----------------------------------------
            case 'cart.addCoupon':
                var code = take('code', params);
                api.cart.addCoupon(code);
                break;
            case 'cart.removeCoupon':
                var code = take('code', params);
                api.cart.removeCoupon(code);
                break;

            // product box
            //----------------------------------------
            case 'product.getInfo':
                // hookAfter(jBionicElement, 'product.infoReady');
                var productId = take('product_id', params);
                var details = take('details', params, {});
                api.product.getInfo(productId, details);
                break;


            // user
            //----------------------------------------
            case 'user.addProductToWishlist':
                var productId = take('product_id', params);
                var details = take('details', params, {});
                api.user.addProductToWishlist(productId, details);
                break;
            case 'user.addressForm':
                api.user.addressForm(params);
                break;
            /**
             * Used in userAccount.address_book
             */
            case 'user.removeAddress':
                var addressId = take('address_id', params);
                api.user.removeAddress(addressId);
                break;
            /**
             * Used in checkout
             */
            case 'user.addressList':
                var type = take('type', params);
                api.user.addressList(params);
                break;
            case 'user.addProductToWishlist':
                var product_id = take('product_id', params);
                var productDetails = take('product_details', params);
                api.user.addProductToWishlist(product_id, productDetails);
                break;
            case 'user.removeWishlist':
                api.user.removeWishlist();
                break;
            case 'user.removeWishlistItem':
                var product_id = take('product_id', params);
                api.user.removeWishlistItem(product_id);
                break;
            case 'user.subscribeToNewsletter':
                var email = take('email', params);
                api.user.subscribeToNewsletter(email);
                break;


            // checkout
            //----------------------------------------
            case 'checkout.switchStep':
                var step = take('step', params);
                api.checkout.switchStep(step);
                break;
            case 'checkout.updateStep':
                api.checkout.updateStep(params);
                break;
            case 'checkout.placeOrder':
                api.checkout.placeOrder();
                break;


            //----------------------------------------
            // EKOM ESTIMATE CART
            //----------------------------------------
            case 'estimateCart.addItem':
                var quantity = take('quantity', params);
                var productId = take('product_id', params);
                api.ekomEstimateJsApi.cart.addItem(quantity, productId, params);
                break;
            case 'estimateCart.updateItemQuantity':
                var token = take('token', params);
                var newQuantity = take('quantity', params);
                api.ekomEstimateJsApi.cart.updateItemQuantity(token, newQuantity);
                break;
            case 'estimateCart.removeItem':
                var token = take('token', params);
                api.ekomEstimateJsApi.cart.removeItem(token);
                break;
            case 'estimateCart.ekomCart2EkomEstimateCart':
                api.ekomEstimateJsApi.cart.ekomCart2EkomEstimateCart();
                break;
            case 'estimateCart.ekomEstimateCart2EkomCart':
                api.ekomEstimateJsApi.cart.ekomEstimateCart2EkomCart();
                break;
            // coupon
            //----------------------------------------
            case 'estimateCart.addCoupon':
                var code = take('code', params);
                api.ekomEstimateJsApi.cart.addCoupon(code);
                break;
            case 'estimateCart.removeCoupon':
                var code = take('code', params);
                api.ekomEstimateJsApi.cart.removeCoupon(code);
                break;
            //----------------------------------------
            // PEIPEI
            //----------------------------------------
            case 'peipei.openIngenicoCreditCardForm':
                var card_id = take('card_id', params, null);
                api.peipei.creditCardWallet.openIngenicoCreditCardForm(card_id);
                break;
            case 'peipei.removeCard':
                var card_id = take('card_id', params, null);
                api.peipei.creditCardWallet.removeCard(card_id);
                break;
            //----------------------------------------
            // THISAPP
            //----------------------------------------
            case 'thisApp.subscribeToLfPointsCatalogOverture':
                api.thisApp.subscribeToLfPointsCatalogOverture(params);
                break;
            default:
                console.log("Unknown action: " + action);
                console.log(params);
                break;

        }

    };


})();