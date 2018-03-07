<?php

use Core\Services\X;
use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;
use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Module\Ekom\Api\EkomApi;
use Module\PeiPei\OnTheFlyForm\CreditCardWallet\CreditCardOnTheFlyForm;
use Module\Ekom\Utils\E;
use Theme\LeeTheme;


//--------------------------------------------
// DISCLAIMER
// THIS TEMPLATE ONLY WORKS WITH THE SINGLEADDRESS CHECKOUTMODE!!!
//--------------------------------------------


$uri = "/theme/" . ApplicationParameters::get("theme");


KamilleThemeHelper::css("lee-modal.css");
KamilleThemeHelper::css("ekom-card-combination/payment.css");
LeeTheme::useLib('featherlight');
LeeTheme::useLib('tipr');
LeeTheme::useLib('checkoutStepper');
LeeTheme::useLib('cloneTemplate');


LeeTheme::useLib('stanConfigurableItems');
HtmlPageHelper::js("/modules/PeiPei/js/stan-ccw.js", null, null, false);

$orderModel = $v['orderModel'];


$api = EkomApi::inst();
$countries = $api->countryLayer()->getCountryList();
$countryId = (int)$orderModel['defaultCountry'];
$m = $orderModel['shippingAddressFormModel'];
$m2 = $v['peipei']['creditCardFormModel'];


$orderSections = $orderModel['orderSections'];
$currentStep = $orderModel['currentStep'];

//a($_SESSION);
//az($v);


$sShipToThisAddressBtnClass = (false === $orderModel['shippingAddress']) ? 'dimmed' : '';

?>

<div class="maincontent gui-checkout">
    <div class="left-col">
        <div>
            <div class="steps checkout-stepper">
                <div class="step step-address past step-1 step-done">
                    <div class="block-title">
                        <div class="step-number">1</div>
                        <div class="step-title">Adresse de livraison</div>
                    </div>
                    <div class="step-past-content f-auto">
                        <span class="shipping-update-fullname"><?php echo $orderModel['shippingAddress']['fName']; ?></span><br>
                        <span class="shipping-update-address"><?php echo $orderModel['shippingAddress']['address']; ?></span><br>
                        <span class="shipping-update-city-postcode-country"><?php echo $orderModel['shippingAddress']['city']; ?>
                            , <?php echo $orderModel['shippingAddress']['postcode']; ?></span>
                    </div>
                    <div class="step-past-content">
                        <a class="step-update-1" href="#">Modifier</a>
                    </div>
                </div>
                <div class="step active step-address step-1 step-open">
                    <div class="block-title">
                        <div class="step-number">1</div>
                        <div class="step-title">Sélectionnez une adresse de livraison</div>
                    </div>
                    <div class="block-content">

                        <div class="header">
                            <div class="title a-block-title">Vos adresses</div>
                            <!--                    <div class="info-link">-->
                            <!--                        <a href="#">Vous faîtes un envoi à plusieurs adresses ?</a>-->
                            <!--                    </div>-->
                        </div>


                        <div class="body">
                            <div class="addresses">
                                <ul class="selectable">

                                    <?php foreach ($orderModel['shippingAddresses'] as $c => $addr):
                                        if ((int)$orderModel['selectedShippingAddressId'] === (int)$addr['address_id']) {
                                            $ch = 'checked';
                                            $sel = 'selected';
                                        } else {
                                            $ch = '';
                                            $sel = '';
                                        }
                                        ?>
                                        <li class="<?php echo $sel; ?> select-address"
                                            data-id="<?php echo $addr['address_id']; ?>">
                                            <input class="select-address" name="address[]"
                                                   id="address-<?php echo $c; ?>"
                                                   type="radio"
                                                   value="1" <?php echo $ch; ?>>
                                            <label for="address-<?php echo $c; ?>" class="select-address">
                                                <b class="select-address"><?php echo $addr['fName']; ?></b>
                                                <?php echo $addr['fAddress']; ?>
                                                <a href="#" class="open-update-address-btn">Modifier</a>
                                            </label>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <div class="extra-action">
                                <a class="open-new-address-form" href="#"><img
                                            src="<?php echo $uri . "/img/icons/plus.png"; ?>"></a> <a
                                        class="open-new-address-form" href="#">Ajouter une nouvelle adresse</a>
                            </div>
                        </div>

                    </div>
                    <div class="block-content-footer">
                        <button class="button save-step-shipping <?php echo $sShipToThisAddressBtnClass; ?>">Envoyer à
                            cette adresse
                        </button>
                    </div>
                </div>
                <div class="step step-2 step-notset">
                    <div class="block-title">
                        <div class="step-number">2</div>
                        <div class="step-title">Mode de paiement</div>
                    </div>
                </div>
                <div class="step step-payment active step-2 step-open stan-configurable-items-payment">
                    <div class="block-title">
                        <div class="step-number">2</div>
                        <div class="step-title">Sélectionnez un mode de paiement</div>
                    </div>
                    <div class="block-content">

                        <?php

                        foreach ($orderModel['paymentMethodBlocks'] as $paymentMethodId => $item):
                            $type = $item['type'];
                            $label = $item['label'];
                            $panel = (array_key_exists("panel", $item)) ? $item['panel'] : [];

                            $sSel = '';
                            $checked = '';
                            if (true === $item['is_preferred']) {
                                $sSel = 'selected';
                                $checked = 'checked';
                            }

                            /**
                             * I used data-checked work around because (I don't know why)
                             * the regular checked didn't seem to work
                             */
                            ?>
                            <div class="body payment-method  stan-configurable-item"
                                 data-id="<?php echo $paymentMethodId; ?>">
                                <div class="cards">
                                    <ul class="selectable">
                                        <li class="select-payment <?php echo $sSel; ?>"
                                            data-id="<?php echo $paymentMethodId; ?>">
                                            <div class="column1 select-payment">
                                                <input <?php echo $checked; ?> id="card-1" type="radio"
                                                                               name="payment-method" value="1"
                                                                               class="select-payment"
                                                                               data-checked="<?php echo (int)$checked; ?>"
                                                >
                                                <img class="credit-card select-payment"
                                                     src="<?php echo $item['img']; ?>">
                                                <span class="label select-payment"><?php echo $label; ?></span>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="stan-configurable-item-panel type-<?php echo $type; ?>"
                                 data-name="<?php echo $type; ?>">

                                <?php if ("creditCardWallet" === $type): ?>
                                    <div class="header">
                                        <div class="column1 a-block-title">Vos cartes de paiement</div>
                                        <div class="column2">Nom du titulaire de la carte</div>
                                        <div class="column3">Date d'expiration</div>
                                    </div>
                                    <div class="body payment-method payment-method-credit-card-wallet">
                                        <div class="cards">
                                            <ul class="ccw-cards-ul">
                                                <?php foreach ($panel['items'] as $card):
                                                    $sClass = "";
                                                    $sChecked = "";
                                                    if (true === $card['is_preferred']) {
                                                        $sClass = "selected";
                                                        $sChecked = "checked";
                                                    }
                                                    ?>
                                                    <li class="<?php echo $sClass; ?> pei-select-card"
                                                        data-id="<?php echo $card['id']; ?>">
                                                        <div class="column1 pei-select-card">
                                                            <input class="pei-select-card" id="card-1" type="radio"
                                                                   name="credit-card"
                                                                   value="1" <?php echo $sChecked; ?>>
                                                            <img class="credit-card pei-select-card"
                                                                 src="<?php echo $card['img']; ?>">
                                                            <?php echo $card['label']; ?>
                                                            ***-<?php echo $card['last_four_digits']; ?>
                                                        </div>
                                                        <div class="column2 pei-select-card"><?php echo $card['owner']; ?></div>
                                                        <div class="column3 pei-select-card"><?php echo $card['fExpirationDate']; ?></div>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                        <div class="extra-action">
                                            <a href="#"><img src="<?php echo $uri . "/img/icons/plus.png"; ?>"></a>
                                            <img class="credit-card" src="<?php echo $panel['newCardImg']; ?>">
                                            <a class="ccw-open-new-card-form" href="#">Ajouter une nouvelle carte de
                                                paiement</a>
                                            Leaderfit accepte la plupart des cartes de crédit et de débit
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>


                        <div class="header header-discount">
                            <div class="a-block-title">Codes chèques-cadeaux et codes promotionnels</div>
                        </div>
                        <div class="body body-discount">
                            <img class="img-plus" src="<?php echo $uri . "/img/icons/plus.png"; ?>">
                            <input class="discount" type="text" placeholder="Indiquez le code de votre chèque cadeau">
                            <button class="button button-gray">Appliquer</button>
                        </div>

                    </div>
                    <div class="block-content-footer">
                        <button class="button save-step-payment">Utiliser ce mode de paiement
                        </button>
                    </div>
                </div>
                <div class="step step-payment past step-2 step-done">
                    <div class="block-title">
                        <div class="step-number">2</div>
                        <div class="step-title">Mode de paiement</div>
                    </div>
                    <div class="step-past-content f-auto payment-method-summaries">

                        <?php
                        $p = $orderModel['paymentMethod'];
                        $label = "";
                        $img = $uri . "/img/icons/credit-cards/mastercard.png";
                        $cardType = "Visa / Electron";
                        $lastFour = "6372";

                        $sClassCcw = '';
                        $sClassCard = '';

                        if (null !== $p) {
                            if ('creditCardWallet' === $p['type']) {
                                $sClassCard = 'style="display: none"';
                                $card = $p['card'];
                                $cardType = $card['type'];
                                $lastFour = $card['last_four_digits'];
                                $img = $card['img'];
                            } else {
                                $sClassCcw = 'style="display: none"';
                                $label = $p['label'];
                                $img = $p['img'];
                            }
                        }

                        ?>
                        <div class="payment-method-summary type-ccw" <?php echo $sClassCcw; ?>>
                            <img class="credit-card payment-update-img"
                                 src="<?php echo $img; ?>">
                            <span class="payment-update-type-label"><?php echo $cardType; ?></span> ***-<span
                                    class="payment-update-last-four-digits"><?php echo $lastFour; ?></span>
                        </div>
                        <div class="payment-method-summary type-general" <?php echo $sClassCard; ?>>
                            <img class="credit-card payment-update-img"
                                 src="<?php echo $img; ?>">
                            <span class="payment-method-label"><?php echo $label; ?></span>
                        </div>


                        <br>
                        <a href="#">Adresse de facturation</a> <span
                                class="payment-update-address"><?php echo $orderModel['billingAddress']['address']; ?></span>
                        <br>
                        Indiquez le code de votre chèque cadeau ou code promotionnel
                        <br>
                        <input class="discount" type="text" placeholder="Saisissez le code">
                        <button class="button button-gray">Appliquer</button>
                    </div>
                    <div class="step-past-content">
                        <a href="#" class="step-update-2">Modifier</a>
                    </div>
                </div>
                <div class="step last step-3 step-notset">
                    <div class="block-title">
                        <div class="step-number">3</div>
                        <div class="step-title">Articles et expédition</div>
                    </div>
                </div>
                <div class="step step-shipping last active step-3 step-open">
                    <div class="block-title">
                        <div class="step-number">3</div>
                        <div class="step-title">Vérification et validation de votre commande</div>
                    </div>
                    <div class="carriers">

                        <?php foreach ($orderSections['sections'] as $section): ?>
                            <div class="block-content carrier">
                                <div class="title title-success a-block-title">
                                    Date de livraison garantie: <span
                                            class="estimated-delivery-date"><?php echo $section['estimatedDeliveryDate']; ?></span>
                                </div>
                                <div class="sender">Articles expédiés par <span
                                            class="carrier-label"><?php echo $section['carrierLabel']; ?></span></div>
                                <div class="shipping-cost">Coût: <span
                                            class="shipping-cost"><?php echo $section['shippingCost']; ?></span></div>
                                <div>Livré à: <span
                                            class="shipping-address"><?php echo $orderModel['shippingAddress']['fAddress']; ?></span>
                                </div>

                                <div class="main-block">
                                    <div class="products-list">


                                        <?php foreach ($section['productsInfo'] as $item): ?>
                                            <div class="product" data-id="<?php echo $item['product_id']; ?>">
                                                <div class="image">
                                                    <img src="<?php echo $item['imageSmall']; ?>">
                                                </div>
                                                <div class="product-info">
                                                    <div class="description"><?php echo $item['description']; ?></div>
                                                    <div class="price"><?php echo $item['linePrice']; ?></div>


                                                    <!-- start-add-on: EkomCardCombination module -->
                                                    <?php if (array_key_exists('eccCombinationSummary', $item)): ?>
                                                        <div class="ekom-card-combination-items">
                                                            <?php
                                                            foreach ($item['eccCombinationSummary'] as $cardLabel => $attr):
                                                                ?>
                                                                <div class="ekom-card-combination-item">
                                                                    <div class="label"><?php echo $cardLabel; ?></div>
                                                                    <div class="attributes">
                                                                        <?php foreach ($attr as $attrName => $attrValue): ?>
                                                                            <span class="attribute"><?php echo $attrValue; ?></span>
                                                                        <?php endforeach; ?>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <!-- end-add-on: EkomCardCombination module -->


                                                    <div class="selector">
                                                        <label>Qté</label>
                                                        <select class="summary-quantity-selector">
                                                            <?php for ($i = 1; $i <= 50; $i++): ?>
                                                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                                            <?php endfor; ?>
                                                        </select>
                                                    </div>

                                                    <div class="seller-info">
                                                        Vendu par: Leaderfit
                                                    </div>

                                                    <div class="availability-extra"></div>
                                                    <div class="options">
                                                        <a href="#">Ajouter des options cadeau</a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>

                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>

                    </div>


                    <div class="block-content total-amount">
                        <div class="button-container">
                            <button class="button step-validate-order">
                                Acheter
                            </button>
                        </div>
                        <div class="info">
                            <div class="total">Montant total : <span class="order-grand-total"></span></div>
                            <div class="legal">
                                En validant votre commande, vous acceptez l'intégralité de nos <a href="#">Conditions
                                    générales
                                    de
                                    vente</a> ainsi
                                que notre politique de gestion de <a href="#">vos informations personnelles</a> ainsi
                                que
                                les
                                Conditions
                                <a href="#">Cookies et Publicité sur Internet</a>.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="display:none" class="templates">
                <ul class="ul-addresses">
                    <li class="{-classSelected-} select-address" data-id="{-address_id-}">
                        <input class="select-address" name="address[]" id="address-{-c-}"
                               type="radio"
                               value="1" {-checked-}>
                        <label for="address-{-c-}" class="select-address">
                            <b class="select-address">{-fName-}</b>
                            {-fAddress-}
                            <a href="#" class="open-update-address-btn">Modifier</a>
                        </label>
                    </li>
                </ul>


                <ul class="ul-ccw">
                    <li class="{-sClass-} pei-select-card"
                        data-id="{-id-}"
                    >
                        <div class="column1 pei-select-card">
                            <input class="pei-select-card" id="card-1" type="radio"
                                   name="credit-card"
                                   value="1" {-checked-}>
                            <img class="credit-card pei-select-card"
                                 data-src="{-img-}">
                            {-label-}
                            ***-{-last_four_digits-}
                        </div>
                        <div class="column2 pei-select-card">{-owner-}</div>
                        <div class="column3 pei-select-card">{-fExpirationDate-}</div>
                    </li>
                </ul>


                <div class="block-content carrier">
                    <div class="title title-success a-block-title">
                        Date de livraison garantie: {-estimated_delivery_date-}
                    </div>
                    <div class="sender">Articles expédiés par {-carrier_label-}</div>
                    <div class="shipping-cost">Coût: {-shipping_cost-}</div>
                    <div>Livré à: <span class="shipping-address"></span></div>

                    <div class="main-block">
                        <div class="products-list">
                            <!-- products here -->
                        </div>
                        <!--                <div class="shipping-details">-->
                        <!--                    <div class="title">Choisissez votre mode de livraison Premium:</div>-->
                        <!--                    <input type="radio" name="carrierType[leaderfit]" value="a" checked>-->
                        <!--                    <label>Livraison en 1 jour ouvré</label>-->
                        <!--                    <span class="green">&mdash; Recevez-le demain, le 18 mai</span>-->
                        <!--                    <br>-->
                        <!--                    <input type="radio" name="carrierType[leaderfit]" value="b">-->
                        <!--                    <label>Livraison Rapide (jusqu'à 3 jours ouvrés)(<a href="#">Voir les détails</a>)</label>-->
                        <!--                </div>-->
                    </div>
                </div>


                <div class="product" data-id="{-product_id-}">
                    <div class="image">
                        <img data-src="{-image-}">
                    </div>
                    <div class="product-info">
                        <div class="description">{-description-}</div>
                        <div class="price">{-linePrice-}</div>

                        <div class="selector">
                            <label>Qté</label>
                            <select class="summary-quantity-selector">
                                <?php for ($i = 1; $i <= 50; $i++): ?>
                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="seller-info">
                            Vendu par: Leaderfit
                        </div>

                        <div class="availability-extra"></div>
                        <div class="options">
                            <a href="#">Ajouter des options cadeau</a>
                        </div>
                    </div>
                </div>


                <div id="modal-address-form" class="lee-modal">
                    <div class="top-bar">Saisir une nouvelle adresse de livraison</div>
                    <!--        <div class="top-bar">Mettre à jour votre adresse d'expédition</div>-->
                    <form action="" method="post" style="width: 500px">
                        <table class="form-table">
                            <tr>
                                <td>Prénom</td>
                                <td>
                                    <input name="<?php echo $m['nameFirstName']; ?>" type="text"
                                           data-error-popout="firstName"
                                           value="<?php echo htmlspecialchars($m['valueFirstName']); ?>">
                                    <div data-error="firstName" class="error"></div>
                                </td>
                            </tr>
                            <tr>
                                <td>Nom</td>
                                <td><input name="<?php echo $m['nameLastName']; ?>" type="text"
                                           data-error-popout="lastName"
                                           value="<?php echo htmlspecialchars($m['valueLastName']); ?>">
                                    <div data-error="lastName" class="error"></div>
                                </td>
                            </tr>
                            <tr>
                                <td>Adresse</td>
                                <td><input name="<?php echo $m['nameAddress']; ?>" type="text"
                                           data-error-popout="address"
                                           value="<?php echo htmlspecialchars($m['valueAddress']); ?>">
                                    <div data-error="address" class="error"></div>
                                </td>
                            </tr>
                            <tr>
                                <td>Code postal</td>
                                <td><input name="<?php echo $m['namePostcode']; ?>" type="text"
                                           data-error-popout="postcode"
                                           value="<?php echo htmlspecialchars($m['valuePostcode']); ?>">
                                    <div data-error="postcode" class="error"></div>
                                </td>
                            </tr>
                            <tr>
                                <td>Ville</td>
                                <td><input name="<?php echo $m['nameCity']; ?>" type="text"
                                           data-error-popout="city"
                                           value="<?php echo htmlspecialchars($m['valueCity']); ?>">
                                    <div data-error="city" class="error"></div>
                                </td>
                            </tr>
                            <tr>
                                <td>Pays</td>
                                <td><select name="<?php echo $m['nameCountry']; ?>"
                                            data-error-popout="country"
                                    >
                                        <?php

                                        foreach ($countries as $k => $val):
                                            $ssel = ($countryId === $k) ? ' selected="selected"' : '';
                                            ?>
                                            <option <?php echo $ssel; ?>
                                                    value="<?php echo $k; ?>"><?php echo $val; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div data-error="country" class="error"></div>
                                </td>
                            </tr>
                            <tr>
                                <td>Numéro de téléphone</td>
                                <td><input name="<?php echo $m['namePhone']; ?>" type="text"
                                           data-error-popout="phone"
                                           value="<?php echo htmlspecialchars($m['valuePhone']); ?>">
                                    <div data-error="phone" class="error"></div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                            <span data-tip="Peut être imprimé sur l'étiquette pour faciliter la livraison (par exemple le code d'accès de la résidence)."
                                  class="hint">Informations complémentaires</span>
                                </td>
                                <td><input name="<?php echo $m['nameExtra']; ?>" type="text"
                                           value="<?php echo htmlspecialchars($m['valueExtra']); ?>"></td>
                            </tr>
                            <tr>
                                <td>Adresse préférée</td>
                                <td><input name="<?php echo $m['nameIsPreferred']; ?>" type="checkbox"
                                           value="1" <?php echo htmlspecialchars($m['checkedIsPreferred']); ?>></td>
                            </tr>
                        </table>
                        <div class="bottom-bar">
                            <button class="lee-button validate create-address-btn">Envoyer à cette adresse</button>
                            <button class="lee-button delete-address-btn">Supprimer cette adresse</button>
                        </div>
                    </form>
                </div>

                <div id="modal-peipei-ccw-card-form" class="lee-modal">
                    <div class="top-bar">Ajouter une carte de paiement</div>


                    <!-- ingenico alias manager -->
                    <div class="container" id="ingenico-iframe">
                        <!--                        <img class="loader active" src="/img/loader/ajax-loader.gif">-->
                        <iframe src="https://lee/page.php"></iframe>


                    </div>
                    <!-- ingenico alias manager -->


                </div>
                <div id="modal-peipei-ccw-card-form2" class="lee-modal">
                    <div class="top-bar">Ajouter une carte de paiement</div>
                    <form action="" method="post" style="width: 500px">
                        <table class="form-table">
                            <tr>
                                <td>Numéro de la carte</td>
                                <td>
                                    <input name="<?php echo $m2['nameNumber']; ?>" type="text"
                                           data-error-popout="number"
                                           value="<?php echo htmlspecialchars($m2['valueNumber']); ?>">
                                    <div data-error="number" class="error"></div>
                                </td>
                            </tr>
                            <tr>
                                <td>Nom du titulaire de la carte</td>
                                <td><input name="<?php echo $m2['nameOwner']; ?>" type="text"
                                           data-error-popout="owner"
                                           value="<?php echo htmlspecialchars($m2['valueOwner']); ?>">
                                    <div data-error="owner" class="error"></div>
                                </td>
                            </tr>
                            <tr>
                                <td>CVV</td>
                                <td><input name="<?php echo $m2['nameCvv']; ?>" type="text"
                                           data-error-popout="cvv"
                                           value="<?php echo htmlspecialchars($m2['valueCvv']); ?>">
                                    <div data-error="cvv" class="error"></div>
                                </td>
                            </tr>
                            <tr>
                                <td>Date d'expiration</td>
                                <td>
                                    <select name="<?php echo $m2['nameExpirationDateMonth']; ?>">
                                        <?php for ($i = 1; $i <= 12; $i++):
                                            $sSel = ((int)$m2['valueExpirationDateMonth'] === $i) ? 'selected="selected"' : '';
                                            ?>
                                            <option <?php echo $sSel; ?>
                                                    value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    <select name="<?php echo $m2['nameExpirationDateYear']; ?>">
                                        <?php
                                        $year = (int)date('Y');
                                        $yearMax = $year + 30;
                                        for ($i = $year; $i <= $yearMax; $i++):
                                            $sSel = ((int)$m2['valueExpirationDateYear'] === $i) ? 'selected="selected"' : '';
                                            ?>
                                            <option <?php echo $sSel; ?>
                                                    value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td>
                                    <input name="<?php echo $m2['nameIsPreferred']; ?>" type="checkbox"
                                           style="vertical-align: middle;"
                                           id="ccw-input-is-preferred"
                                           value="1" <?php echo htmlspecialchars($m2['checkedIsPreferred']); ?>>
                                    <label for="ccw-input-is-preferred">Utiliser comme mon mode de paiement par
                                        défaut</label>
                                </td>
                            </tr>
                        </table>
                        <div class="bottom-bar">
                            <div class="please-wait-container">
                                <img src="/img/loader/ajax-loader.gif" alt="please wait"/>
                            </div>
                            <button class="lee-button validate ccw-create-card-btn">Ajouter votre carte</button>
                            <button class="lee-button ccw-cancel-card">Annuler</button>
                        </div>
                    </form>
                </div>


            </div>
        </div>


        <div class="legal-text">
            <div class="top-gradient"></div>

            <div class="text">
                Besoin d'aide&nbsp;? Consultez notre <a href="#">Pages d'Aide</a> ou <a href="#">contactez-nous</a>
            </div>

            <div class="text">
                Que se passe-t-il quand vous Validez une commande&nbsp;? Vous Confirmez vos achats. Nous vous
                envoyons un e-mail de confirmation une fois que vous avez cliqué sur le bouton «&nbsp;Validez votre
                commande&nbsp;». Ce contrat d'achat n'est rempli que lorque vous recevez un second e-mail vous
                informant de l'envoi de l'article commandé.
            </div>


            <div class="text">
                Si, pour une raison ou une autre, vous n'êtes pas satisfait du produit que vous avez commandé, vous
                pouvez nous le retourner dans les conditions spécifiées ci-après, sous 30 jours, et nous vous
                rembourserons l'intégralité du montant de l'article.<br> Les livres doivent être retournés dans leur
                condition d'origine. Les enregistrements audio ou vidéo (CD, DVD, VHS, etc.) doivent être retournés
                dans leur emballage d'origine, non descellés.<br> De plus, nous serons heureux de vous rembourser
                les frais de port initiaux si le retour résulte d'une erreur de notre part. Envoyez-le à&nbsp;:
                Amazon.fr, Service Retour produits, 1401 rue du Champ Rouge, 45962 ORLEANS Cedex 9, France. Voir la
                <a href="#">Politique en matière de retours</a> d'Amazon.fr. <br>Si vous désirez retourner un
                article acheté sur
                Marketplace, veuillez prendre directement contact avec le vendeur de cet article.
            </div>
        </div>

    </div>
    <div class="right-col sideinfo">
        <div class="right-col-block">
            <div class="block tcenter">
                <div class="sideinfo-step sideinfo-step-1">
                    <button class="button save-step-shipping <?php echo $sShipToThisAddressBtnClass; ?>">Envoyer à cette
                        adresse
                    </button>
                    <p class="text-small">
                        Veuillez choisir une adresse pour passer à l'étape suivante.
                        Vous pourrez encore annuler ou modifier votre commande.
                    </p>
                </div>
                <div class="sideinfo-step sideinfo-step-2">
                    <button class="button save-step-payment">Utiliser ce mode de paiement</button>
                    <p class="text-small">
                        Veuillez sélectionner une méthode de paiement pour continuer.
                        Vous pourrez vérifier votre commande avant validation.
                    </p>
                </div>
                <div class="sideinfo-step sideinfo-step-3">
                    <button class="button step-validate-order">Acheter</button>
                    <p class="text-small">
                        En validant votre commande, vous acceptez l'intégralité de nos <a href="#">Conditions générales
                            de
                            vente</a> ainsi
                        que notre politique de gestion de <a href="#">vos informations personnelles</a> ainsi que les
                        Conditions
                        <a href="#">Cookies et Publicité sur Internet</a>.
                    </p>
                </div>
            </div>
            <div class="block">
                <div class="title">Récapitulatif de commande</div>
                <div class="table">
                    <table>
                        <tr>
                            <td>Articles:</td>
                            <td><span class="lines-total"><?php echo $orderModel['linesTotal']; ?></span></td>
                        </tr>
                        <tr>
                            <td>Coupons:</td>
                            <td><span class="coupon-total-saving"><?php echo $orderModel['couponTotalSaving']; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td>Livraison:</td>
                            <td>
                                <span class="shipping-costs"><?php echo $orderModel['orderSections']['totalShippingCost']; ?></span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="block last-block total-amount">
                <table class="total-amount-table">
                    <tr>
                        <td>Montant total:</td>
                        <td><span class="order-grand-total"><?php echo $orderModel['orderGrandTotal']; ?></span></td>
                    </tr>
                </table>
                <p>
                    Le total de la commande inclut la TVA.
                    <br>
                    <a href="#">Voir les détails</a>
                </p>
            </div>
        </div>

        <div class="right-col-block-footer">
            <a href="#">Comment sont calculés les frais de livraison?</a>
        </div>
    </div>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function (event) {
        $(document).ready(function (e) {


            //----------------------------------------
            // INITIALIZE APIS
            //----------------------------------------
            var api = ekomApi.inst();

            var jStanPayment = $(".stan-configurable-items-payment");
            var oStanPayment = stanConfigurableItems.init("payment", jStanPayment);


            //----------------------------------------
            // CHECKOUT STEPPER
            //----------------------------------------
            var stepper = new CheckoutStepper({
                start: 1,
                context: $('.checkout-stepper')
            });


            //----------------------------------------
            // CHECKOUT ONE PAGE
            //----------------------------------------
            var jModalAddressForm = $("#modal-address-form");
            var jModalPeiPeiCardForm = $("#modal-peipei-ccw-card-form");


            var jGui = $(".gui-checkout");
            var jTemplates = $(".templates", jGui);


            var jTplAddress = $(".ul-addresses li:first", jTemplates);
            var jTplCard = $(".ul-ccw li:first", jTemplates);
            var jTplCarrier = $("> .carrier", jTemplates);
            var jTplCarrierProduct = $("> .product", jTemplates);
            var jCarriers = $(".step-shipping.step-open .carriers", jGui);
            var jSideInfo = $(".sideinfo", jGui);
            jSideInfo.find('.sideinfo-step-2, .sideinfo-step-3').hide();


            function showAliasFormLoader() {
                console.log("showAliasFormLoader");
                var jIngenicoIframe = $("#ingenico-iframe");

                jIngenicoIframe.find(".loader").addClass("active");

                console.log(jIngenicoIframe.find('iframe'));

                jIngenicoIframe.find('iframe').on('load', function () {
                    console.log("iframeLoaded");
                    hideAliasFormLoader();
                });
            }

//
            function hideAliasFormLoader() {
                console.log("hideAliasFormLoader");
                $("#ingenico-iframe").find(".loader").removeClass("active");
            }


            function getSelectedAddressId() {
                return $(".addresses > ul", jGui).find("li.selected").attr("data-id");
            }

            function getSelectedPaymentId() {
                return $(".step-payment.step-open ul.selectable", jGui).find("li.selected:first").attr("data-id");
            }

//
//            function getPaymentItemById(id) {
//                return $(".ccw-cards-ul", jGui).find('li[data-id="' + id + '"]');
//            }

            function closeCurrentModal() {
                var current = $.featherlight.current();
                current.close();
            }

            function selectItemByTarget(jTarget) {
                var jUls = jTarget.closest(".block-content").find('.selectable');
                var jLi = jTarget.closest("li");
                jUls.find('li').each(function () {
                    if (jLi.is($(this))) {
                        $(this).addClass('selected');
                        jLi.find('input').prop("checked", true);
                    }
                    else {
                        $(this).removeClass('selected');
                    }
                });
            }

            function sideInfoStep(stepNumber, orderModel) {
                jSideInfo.find('.sideinfo-step').hide();
                jSideInfo.find('.sideinfo-step-' + stepNumber).show();

                if (stepNumber > 1 && 'undefined' !== typeof orderModel) {
                    jSideInfo.find(".shipping-costs").html(orderModel.orderSections.totalShippingCost);
                    jSideInfo.find(".order-grand-total").html(orderModel.orderGrandTotal);

                }

            }


            function refreshAddresses(addresses) {
                var jUlAddresses = $(".addresses > ul", jGui);
                jUlAddresses.empty();

                var c = 0;
                for (var i in addresses) {

                    var a = jQuery.extend({}, addresses[i]);

                    var selected = "";
                    var checked = "";
                    if (true === a.is_preferred) {
                        selected = "selected";
                        checked = "checked";
                    }

                    a["classSelected"] = selected;
                    a["checked"] = checked;
                    a["c"] = c++;

                    var jLi = $.fn.cloneTemplate(jTplAddress, a);
                    jUlAddresses.append(jLi);
                }
            }


            function updateSelectedAddressInfo(orderModel) {
                var addressModel = orderModel.shippingAddress;
                var jSel = jGui.find(".step-address.step-done");
                jSel.find(".shipping-update-fullname").html(addressModel["fName"]);
                jSel.find(".shipping-update-address").html(addressModel['address']);
                jSel.find(".shipping-update-city-postcode-country").html(addressModel['city'] + ", " + addressModel['postcode'] + ". " + addressModel['country']);
            }

            function updateSelectedPaymentInfo(orderModel) {


                var jContext = jGui.find(".payment-method-summaries");
                var model = orderModel.paymentMethod;
                var type = model.type;
                if ('creditCardWallet' === type) {
                    jContext.find('.type-ccw').show();
                    jContext.find('.type-general').hide();

                    //
                    jContext.find(".payment-update-type-label").html(model.card.label);
                    jContext.find(".payment-update-last-four-digits").html(model.card.last_four_digits);
                    jContext.find(".payment-update-img").attr("src", model.card.img);

                }
                else {
                    jContext.find('.type-ccw').hide();
                    jContext.find('.type-general').show();
                    jContext.find(".payment-method-label").html(model.label);
                    jContext.find(".payment-update-img").attr("src", model.img);
                }

                if ('singleAddress' === orderModel.checkoutMode) {
                    jContext.find(".payment-update-address").html(orderModel.shippingAddress.address);
                }

            }


            function updateOrderSectionsInfo(orderModel) {

                if ('singleAddress' === orderModel.checkoutMode) {
                    var orderSections = orderModel.orderSections;
                    var section = orderSections.sections[0];
                    jCarriers.find('.estimated-delivery-date').html(section.estimatedDeliveryDate);
                    jCarriers.find('.carrier-label').html(section.carrierLabel);
                    jCarriers.find('.shipping-cost').html(section.shippingCost);
                    jCarriers.find('.shipping-address').html(orderModel.shippingAddress.fAddress);
                }


//                if ('singleAddress' !== orderModel.checkoutMode) {
//                var jProducts = null;
//                    jCarriers.empty();
//                    for (var i in orderModel.sections) {
//                        var section = orderModel.sections[i];
//                        var carrier = section['carrier'];
//                        carrier.estimated_delivery_date = carrier.estimated_delivery_date.substr(0, 10);
//
//                        var jTpl = $.fn.cloneTemplate(jTplCarrier, carrier);
//                        jTpl.find(".shipping-address").html(section.address.fAddress);
//
//                        jCarriers.append(jTpl);
//                        jProducts = jTpl.find('.products-list');
//
//
//                        var items = section.items;
//                        var jTpl2;
//                        for (var i in items) {
//                            var item = items[i];
//                            jTpl2 = $.fn.cloneTemplate(jTplCarrierProduct, item);
//                            jTpl2.find('option[value="' + item.quantity + '"]').prop('selected', 'selected');
//                            jTpl2.find('.summary-quantity-selector').on('change', function () {
//                                var productId = $(this).closest(".product").attr('data-id');
//                                console.log($(this).val(), productId);
//
//                            });
//                            jProducts.append(jTpl2);
//                        }
//                    }
//                }


//                jGui.find(".order-grand-total").html(orderModel.summary.orderGrandTotal);
            }


            function refreshCards(cards) {
                var jUl = $(".ccw-cards-ul", jGui);
                jUl.empty();

                var c = 0;
                for (var i in cards) {

                    var a = jQuery.extend({}, cards[i]);

                    var selected = "";
                    var checked = "";
                    if (true === a.selected) {
                        selected = "selected";
                        checked = "checked";
                    }


                    a["sClass"] = selected;
                    a["checked"] = checked;
                    a["c"] = c++;

                    var jLi = $.fn.cloneTemplate(jTplCard, a);
                    jUl.append(jLi);
                }
            }

            function refreshProducts(productsInfo) {

                for (var i in productsInfo) {
                    var p = productsInfo[i];
                    var pId = p.product_id;
                    var linePrice = p.linePrice;
                    jCarriers.find('.product[data-id="' + pId + '"] .price').html(linePrice);
                }
            }

            function refreshSideInfo(m) {

                var shippingCosts = '--';
                shippingCosts = m.orderSections.totalShippingCost;

                jSideInfo.find(".lines-total").html(m.linesTotal);
                jSideInfo.find(".coupon-total-saving").html(m.couponTotalSaving);
                jSideInfo.find(".shipping-costs").html(shippingCosts);
                jSideInfo.find(".order-grand-total").html(m.orderGrandTotal);
            }


            $('.hint').tipr();
            api.on("user.address.updated", function (data) {
                jGui.find('.save-step-shipping').removeClass('dimmed');
                refreshAddresses(data.addresses);
            });
//            api.on("checkout.address.selected", function (data) {
//                console.log(data);
//            });
            api.on("peipei.ccw.cardAdded", function (data) {
                refreshCards(data.cards);
            });
            api.on("checkout.cart.updated", function (data) {
                refreshProducts(data.orderSections.sections[0].productsInfo);
                refreshSideInfo(data);
            });


            function onGscpError(msg) {
                alert(msg);
            }

            //----------------------------------------
            // PEI PEI FUNCTION SECTION
            //----------------------------------------
            function selectPeiPeiItemByTarget(jTarget) {
                var jUl = jTarget.closest(".ccw-cards-ul");
                var jLi = jTarget.closest("li");
                jUl.find('li').each(function () {
                    if (jLi.is($(this))) {
                        $(this).addClass('selected');
                        jLi.find('input').prop("checked", true);
                    }
                    else {
                        $(this).removeClass('selected');
                    }
                });
            }

            /**
             * If this is false, it means the modal form for the address will be an insert.
             * If this is set to an address_id, it means the modal form for the address will be an update.
             */
            var currentAddressId = false;
            var addrId;

            $(document).on("click", function (e) {
                if (1 === e.which) {

                    var jTarget = $(e.target);

                    var jForm;
                    var data;


                    if (jTarget.hasClass("dimmed")) {
                        return false;
                    }
                    else if (jTarget.hasClass("create-address-btn")) {
                        jForm = jTarget.closest("form");
                        data = jForm.serialize();

                        if (false !== currentAddressId) {
                            data += "&address_id=" + currentAddressId;
                        }


                        api.user.createAddress(data, function (data) {
                            closeCurrentModal();
                        }, function (formModel) {
                            window.onTheFlyForm.injectValidationErrors(jForm, formModel);
                        }, function (error) {
                            alert(error);
                        });

                        return false;
                    }
                    else if (jTarget.hasClass("select-address")) {
                        selectItemByTarget(jTarget);
                    }
                    else if (jTarget.hasClass("select-payment")) {
                        selectItemByTarget(jTarget);
                    }
                    else if (jTarget.hasClass("delete-address-btn")) {
                        api.user.deleteAddress(currentAddressId, function () {
                            closeCurrentModal();
                        });
                        return false;
                    }
                    else if (jTarget.hasClass("open-new-address-form")) {
                        currentAddressId = false;
                        jModalAddressForm.find("form")[0].reset();
                        $.featherlight(jModalAddressForm);
                    }
                    else if (jTarget.hasClass("open-update-address-btn")) {
                        addrId = jTarget.closest("li").attr("data-id");
                        currentAddressId = addrId;
                        api.user.getAddressInfo(addrId, function (addr) {

                            var jForm = jModalAddressForm.find("form");
                            onTheFlyForm.injectRawValues(jForm, addr);
                            $.featherlight(jModalAddressForm);

                        });
                        return false;
                    }
                    else if (jTarget.hasClass("save-step-shipping")) {

                        addrId = getSelectedAddressId();
                        api.checkout.setShippingAddressId(addrId, function (data) {
                            var orderModel = data.orderModel;
                            stepper.markStepAsDone(1);
                            stepper.openStep(2);

                            updateSelectedAddressInfo(orderModel);
                            updateOrderSectionsInfo(orderModel);
                            sideInfoStep(2, orderModel);


                        }, onGscpError, {
                            saveAsDefault: true,
                            marker: "shippingDone"
                        });


                        return false;
                    }
                    else if (jTarget.hasClass("save-step-payment")) {

                        oStanPayment.execute(function (id, options) {
                            api.checkout.setPaymentMethod(id, options, function (data) {

                                var orderModel = data.orderModel;
//                                stepper.markStepAsDone(1);
                                stepper.markStepAsDone(2);
                                stepper.openStep(3);
//
                                updateSelectedPaymentInfo(orderModel);
                                sideInfoStep(3, orderModel);


                            }, onGscpError, {
                                marker: "paymentDone",
                                saveAsDefault: false
                            });
                        }, function (errMsg) {
                            console.log("stan error: ", errMsg);
                        });
                        return false;
                    }
                    else if (jTarget.hasClass("step-update-1")) {
                        stepper.openStep(1);
                        sideInfoStep(1);
                        return false;
                    }
                    else if (jTarget.hasClass("step-update-2")) {
                        stepper.openStep(2);
                        sideInfoStep(2);
                        return false;
                    }
                    else if (jTarget.hasClass("step-validate-order")) {
                        api.checkout.placeOrder(function (order) {
                            window.location.href = '<?php echo E::link("Ekom_checkoutOnePageThankYou"); ?>';
                        });

                        return false;
                    }
                    //----------------------------------------
                    // PEIPEI
                    //----------------------------------------
                    else if (jTarget.hasClass("ccw-open-new-card-form")) {
//                        jModalPeiPeiCardForm.find("form")[0].reset();

                        $.featherlight(jModalPeiPeiCardForm, {
                            beforeOpen: function () {
                            },
                            afterOpen: function () {

                                console.log("open");
                                showAliasFormLoader();
                            }
                        });

                        return false;
                    }
                    else if (jTarget.hasClass("ccw-cancel-card")) {
                        closeCurrentModal();
                        return false;
                    }
                    else if (jTarget.hasClass("ccw-create-card-btn")) {

                        jForm = jTarget.closest("form");
                        jForm.addClass('processing');
                        data = jForm.serialize();


                        api.peipei.creditCardWallet.createCard(data, function (data) {
                            jForm.removeClass('processing');
                            closeCurrentModal();
                        }, function (formModel) {
                            jForm.removeClass('processing');
                            window.onTheFlyForm.injectValidationErrors(jForm, formModel);
                        }, function (error) {
                            jForm.removeClass('processing');
                            alert(error);
                        });


                        return false;
                    } else if (jTarget.hasClass("pei-select-card")) {
                        selectPeiPeiItemByTarget(jTarget);
                    }
                }
            });


            jCarriers.find('.summary-quantity-selector').on('change', function () {
                var qty = $(this).val();
                var pId = $(this).closest(".product").attr("data-id");
                api.checkout.updateProductQuantity(pId, qty);
            });


            var step = '<?php echo $currentStep; ?>';
            if ('paymentDone' === step) {
                step = 3;
            }
            else if ('shippingDone' === step) {
                step = 2;
            }
            else {
                step = 1;
            }


            // init step
            for (var i = 0; i < step; i++) {
                stepper.markStepAsDone(i);
            }
            stepper.openStep(step);
            sideInfoStep(step);


            //            $('#api-demo-buttons').on('click', function (e) {
            //                var jTarget = $(e.target);
            //                if (jTarget.hasClass("validate")) {
            //                    stanConfigurableItems.inst().execute(function (id, options) {
            //                        console.log("do something useful with id: " + id + " and the options", options);
            //                    }, function (err) {
            //                        console.log("oops", err);
            //                    });
            //                    return false;
            //                }
            //            });


            if ('dirtyWorkAround') {

                /**
                 * Dirty workaround (#sorrynotime)
                 * for having a checked radio input for payment (it seems that static regular markup
                 * won't do it)
                 */
                var jOtherTarget = jGui.find(".step-payment.step-open li.selected:first");
                jOtherTarget.trigger('click');
//                selectItemByTarget(jOtherTarget);
            }


        })
        ;
    })
    ;
</script>
