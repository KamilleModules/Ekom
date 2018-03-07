<?php

use FormTools\Rendering\FormToolsRenderer;
use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
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
            <form action="" method="post" style="width: 500px" class="table-form">


                <p class="off-success-message off-success-message-container">Success message</p>

                <p class="off-error-message off-error-message-container">Error message</p>


                <?php OnTheFlyFormHelper::generateKey($m); ?>


                <table>
                    <tr>
                        <td>Prénom</td>
                        <td>
                            <input name="{m:nameFirstName}" type="text"
                                   value="{m:valueFirstName}">
                        </td>
                    </tr>
                    <tr class="hidden" data-error="{m:nameFirstName}">
                        <td></td>
                        <td data-error-text="1" class="error"></td>
                    </tr>
                    <tr>
                        <td>Nom</td>
                        <td><input name="{m:nameLastName}" type="text"
                                   value="{m:valueLastName}">
                        </td>
                    </tr>
                    <tr class="hidden" data-error="{m:nameLastName}">
                        <td></td>
                        <td data-error-text="1" class="error"></td>
                    </tr>
                    <tr>
                        <td>Adresse</td>
                        <td><input name="{m:nameAddress}" type="text"
                                   value="{m:valueAddress}">
                        </td>
                    </tr>
                    <tr class="hidden" data-error="{m:nameAddress}">
                        <td></td>
                        <td data-error-text="1" class="error"></td>
                    </tr>
                    <tr>
                        <td>Code postal</td>
                        <td><input name="{m:namePostcode}" type="text"
                                   value="{m:valuePostcode}">
                        </td>
                    </tr>
                    <tr class="hidden" data-error="{m:namePostcode}">
                        <td></td>
                        <td data-error-text="1" class="error"></td>
                    </tr>
                    <tr>
                        <td>Ville</td>
                        <td><input name="{m:nameCity}" type="text"
                                   value="{m:valueCity}">
                        </td>
                    </tr>
                    <tr class="hidden" data-error="{m:nameCity}">
                        <td></td>
                        <td data-error-text="1" class="error"></td>
                    </tr>
                    <tr>
                        <td>Pays</td>
                        <td><select name="{m:nameCountryId}"
                        >
                            <?php OnTheFlyFormHelper::selectOptions($m['optionsCountryId'], $m['valueCountryId']); ?>
                        </select>
                        </td>
                    </tr>
                    <tr class="hidden" data-error="{m:nameCountryId}">
                        <td></td>
                        <td data-error-text="1" class="error"></td>
                    </tr>
                    <tr>
                        <td>Numéro de téléphone</td>
                        <td><input name="{m:namePhone}" type="text"
                                   value="{m:valuePhone}">
                        </td>
                    </tr>
                    <tr class="hidden" data-error="{m:namePhone}">
                        <td></td>
                        <td data-error-text="1" class="error"></td>
                    </tr>
                    <tr>
                        <td>
                            <span data-tip="Peut être imprimé sur l'étiquette pour faciliter la livraison (par exemple le code d'accès de la résidence)."
                                  class="hint">Informations complémentaires</span>
                        </td>
                        <td><input name="{m:nameSupplement}" type="text"
                                   value="{m:valueSupplement}"></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="indent-left">
                            <label>
                                <input type="checkbox" name="{m:nameIsDefaultBillingAddress}"
                                       value="1"
                                >
                                En faire mon adresse de facturation par
                                défaut</label>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="indent-left">
                            <label>
                                <input type="checkbox" name="{m:nameIsDefaultShippingAddress}"
                                       value="1"
                                >
                                En faire mon adresse de livraison par défaut
                            </label>
                        </td>
                    </tr>
                </table>
                <div class="table-form-bottom">
                    <button class="submit-btn create-new-address-btn">Créer cette adresse</button>
                    <button class="submit-btn update-address-btn">Mettre à jour cette adresse</button>
                    <button>Annuler</button>
                </div>
            </form>
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
                }
            });


        });
    });
</script>

