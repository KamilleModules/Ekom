<?php

use FormTools\Rendering\FormToolsRenderer;
use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Module\Ekom\View\Address\AddressAjaxFormRenderer;
use OnTheFlyForm\Helper\OnTheFlyFormHelper;
use Theme\LeeTheme;

KamilleThemeHelper::css("customer/address-book.css");
KamilleThemeHelper::css("customer-all.css");
KamilleThemeHelper::css("table-form.css");

LeeTheme::useLib("featherlight");
LeeTheme::useLib("onTheFlyForm");


$m = $v['newAddressModel'];


?>

<div class="widget widget-customer-address-book" id="widget-customer-address-book">
    <div class="address-container address-container-shipping">
        <div class="bar-gray">Mes adresses de livraison et/ou facturation</div>
        <div class="button-container">
            <button class="lee-red-button open-new-address-form-btn">AJOUTER UNE ADRESSE</button>
        </div>
        <div class="address-list">
            <!--            <div class="empty-address-block address-block">-->
            <!--                <p>Ajouter une adresse</p>-->
            <!--            </div>-->
            <?php foreach ($v['addressList'] as $a):

                $hasDefault = (true === $a['is_shipping_default'] || true === $a['is_billing_default']);
                $sClass = "";
                if (true === $hasDefault) {
                    $sClass = "default-address";
                }


                ?>
                <div class="address-block <?php echo $sClass; ?>" data-id="<?php echo $a['address_id']; ?>">
                    <div class="padder">
                        <div class="address-info">
                            <div class="title"><?php echo $a['title']; ?></div>
                            <div class="address-line1"><?php echo $a['address_line_1']; ?></div>
                            <div class="address-line2"><?php echo $a['address_line_2']; ?></div>
                            <div class="address-line3"><?php echo $a['address_line_3']; ?></div>
                            <div class="phone">Téléphone: <?php echo $a['phone']; ?></div>
                        </div>
                        <div class="right">
                            <div class="links">
                                <div class="actions">
                                    <a href="#" class="link-update-address no-left-border">Modifier</a>
                                    <a href="#" class="link-delete-address">Supprimer</a>
                                </div>
                            </div>
                            <div class="defaults">
                                <?php if (true === $a['is_billing_default']): ?>
                                    <div class="badge">Adresse de facturation par défaut</div>
                                <?php endif; ?>
                                <?php if (true === $a['is_shipping_default']): ?>
                                    <div class="badge">Adresse de livraison par défaut</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>


    <div class="templates" style="display: none">
        <div id="tpl-new-address-form" class="tpl-new-address-form">
            <?php
            AddressAjaxFormRenderer::create()->render($m);
            ?>
        </div>
    </div>


</div>
<script>
    document.addEventListener("DOMContentLoaded", function (event) {
        $(document).ready(function () {

            var jWidget = $('#widget-customer-address-book');
            var jTplAddressForm = $("#tpl-new-address-form", jWidget);
            var jCreateAddressFormBtn = $('.create-new-address-btn', jTplAddressForm);
            var jUpdateAddressFormBtn = $('.update-address-btn', jTplAddressForm);
            var api = window.ekomApi.inst();

            window.onTheFlyForm.formInit(jTplAddressForm);


            var curAddressId = null;

            function closeCurrentModal() {
                var current = $.featherlight.current();
                current.close();
            }


            $(document).on("click", function (e) {
                if (1 === e.which) {
                    var jTarget = $(e.target);
                    if (jTarget.hasClass("open-new-address-form-btn")) {

                        curAddressId = null;

                        jCreateAddressFormBtn.show();
                        jUpdateAddressFormBtn.hide();


                        jTplAddressForm.find("form")[0].reset();
                        $.featherlight(jTplAddressForm);
                        return false;
                    }
                    else if (
                        jTarget.hasClass("create-new-address-btn") ||
                        jTarget.hasClass("update-address-btn")
                    ) {

                        var jForm = jTarget.closest('form');
                        var data = jForm.serialize();

                        if (jTarget.hasClass("update-address-btn")) {
                            if (null !== curAddressId) {
                                data += '&address_id=' + curAddressId;
                            }
                        }


                        api.user.saveAddress(data, function (data) {
                            window.location.reload();
                        }, function (offModel) {
                            window.onTheFlyForm.handleAjaxCompleteResponse(jForm, offModel);
                        });
                        return false;
                    }
                    else if (jTarget.hasClass("link-delete-address")) {

                        var addrId = jTarget.closest(".address-block").attr("data-id");
                        api.user.deleteAddress(addrId, function (r) {
                            window.location.reload();
                        });
                        return false;
                    }
                    else if (jTarget.hasClass("link-update-address")) {


                        jCreateAddressFormBtn.hide();
                        jUpdateAddressFormBtn.show();

                        var addrId = jTarget.closest(".address-block").attr("data-id");
                        curAddressId = addrId;
                        api.user.getAddressInfo(addrId, function (data) {
                            jTplAddressForm.find("form")[0].reset();
                            $.featherlight(jTplAddressForm, {
                                afterOpen: function () {
                                    var jContent = $.featherlight.current().$content;
                                    window.onTheFlyForm.injectRawValues(jContent, data);
                                }
                            });

                        });
                        return false;
                    }
                    else if (jTarget.hasClass("close-address-form-btn")) {
                        closeCurrentModal();
                        return false;
                    }
                }
            });


        });
    });
</script>

