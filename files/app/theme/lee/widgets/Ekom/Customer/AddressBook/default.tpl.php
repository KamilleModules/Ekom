<?php

use FormTools\Rendering\FormToolsRenderer;
use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Module\Ekom\View\Address\AddressAjaxFormRenderer;
use Module\ThisApp\Ekom\View\User\AddressBookRenderer;
use OnTheFlyForm\Helper\OnTheFlyFormHelper;
use Theme\LeeTheme;


KamilleThemeHelper::css("customer/address-book.css");
LeeTheme::useLib('featherlight');
LeeTheme::useLib("soko");
LeeTheme::useLib("phoneCountry");
LeeTheme::useLib("prettyCheckbox");
LeeTheme::useLib("simpleselect");


?>
<div class="bionic-marker" data-type="intent" data-value="addressBook"></div>


<div class="widget widget-customer-address-book" id="widget-customer-address-book">
    <?php echo AddressBookRenderer::render($v['addressList']); ?>
</div>


<script>
    jqueryComponent.ready(function () {

        var api = ekomApi.inst();
        var jContext = $('#widget-customer-address-book');


        function closeCurrentModal() {
            var current = $.featherlight.current();
            if (null !== current) {
                current.close();
            }
        }


        api.on('user.addressFormReady', function (data) {
            closeCurrentModal();
            $.featherlight(data.addressFormHtml);
            var current = $.featherlight.current();
            var jPopup = current.$instance;
            jPopup.find('.soko-simpleselect').simpleselect();
        });


        api.on('user.address.created', function (data) {
            closeCurrentModal();
            jContext.empty().append(data.addressBookHtml);
        });

        api.on('user.address.deleted', function (data) {
            jContext.empty().append(data.addressBookHtml);
        });

    });
</script>

