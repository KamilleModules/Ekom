<?php

use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Module\ThisApp\Ekom\View\Checkout\EkomCheckoutProcessRenderer;
use Theme\LeeTheme;


KamilleThemeHelper::css("cart.css");
KamilleThemeHelper::css("table-form.css");
KamilleThemeHelper::css("customer/address-book.css");
KamilleThemeHelper::css("peipei/payment-methods.css");

LeeTheme::useLib('featherlight');
LeeTheme::useLib("soko");
LeeTheme::useLib("phoneCountry");
LeeTheme::useLib("prettyCheckbox");
LeeTheme::useLib("simpleselect");

?>


<div class="checkout-two-columns window" id="checkout-process">
    <?php echo EkomCheckoutProcessRenderer::render($v); ?>
</div>
<div class="bionic-marker" data-type="intent" data-value="checkoutProcess"></div>

<script>
    jqueryComponent.ready(function () {

        function closeCurrentModal() {
            var current = $.featherlight.current();
            if (null !== current) {
                current.close();
            }
        }


        var jContainer = $('#checkout-process');
        var api = ekomApi.inst();
        api.on('checkout.dataUpdated', function (data) {
            closeCurrentModal();
            jContainer.empty().append(data.checkoutProcessHtml);
        });


        //----------------------------------------
        // ADDRESS FORM
        //----------------------------------------
        api.on('checkout.placeOrderSuccessAfter', function (data) {
            window.location.href = data.uriRedirect;
        });


        api.on(['user.address.created', "user.address.deleted", 'cart.updated'], function (data) {
            closeCurrentModal();
            jContainer.empty().append(data.checkoutProcessHtml);
        });


        api.on('user.addressFormReady', function (data) {
            closeCurrentModal();
            $.featherlight(data.addressFormHtml);
            var current = $.featherlight.current();
            var jPopup = current.$instance;
            jPopup.find('.soko-simpleselect').simpleselect();
        });

        api.on('user.addressesListReady', function (data) {
            $.featherlight(data.html);
        });
    });
</script>
