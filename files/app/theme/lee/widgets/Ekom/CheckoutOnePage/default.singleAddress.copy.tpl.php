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
LeeTheme::useLib('featherlight');
LeeTheme::useLib('tipr');
LeeTheme::useLib('checkoutStepper');
LeeTheme::useLib('cloneTemplate');


$orderModel = $v['orderModel'];


$api = EkomApi::inst();
$countries = $api->countryLayer()->getCountryList();
$countryId = (int)$orderModel['defaultCountry'];
$m = $orderModel['shippingAddressFormModel'];
$m2 = CreditCardOnTheFlyForm::create()->getModel();


$cartModel = EkomApi::inst()->cartLayer()->getCartModel();
$cartTotal = $cartModel['cartTotal'];

$orderSections = $orderModel['orderSections'];
$currentStep = $orderModel['currentStep'];


//a($_SESSION);

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
                        <span class="shipping-update-fullname">lafitte pierre</span><br>
                        <span class="shipping-update-address">6 Rue du Port Feu Hugon</span><br>
                        <span class="shipping-update-city-postcode-country">tours, 37000</span>
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
                        <button class="button save-step-shipping">Envoyer à cette adresse</button>
                    </div>
                </div>
                <div class="step step-2 step-notset">
                    <div class="block-title">
                        <div class="step-number">2</div>
                        <div class="step-title">Mode de paiement</div>
                    </div>
                </div>
                <div class="step step-payment active step-2 step-open stan-configurable-items">
                    <div class="block-title">
                        <div class="step-number">2</div>
                        <div class="step-title">Sélectionnez un mode de paiement</div>
                    </div>
                    <div class="block-content">

                        <?php
                        foreach ($orderModel['paymentMethodBlocks'] as $paymentMethodId => $item):
                            $type = $item['type'];
                            ?>
                            <?php if ("creditCardWallet" === $type): ?>
                            <div class="header">
                                <div class="column1 a-block-title">Vos cartes de paiement</div>
                                <div class="column2">Nom du titulaire de la carte</div>
                                <div class="column3">Date d'expiration</div>
                            </div>
                            <div class="body payment-method payment-method-credit-card-wallet">
                                <div class="cards">
                                    <ul class="selectable ccw-cards-ul">
                                        <?php foreach ($item['items'] as $card):
                                            $sClass = "";
                                            $sChecked = "";
                                            if (true === $card['selected']) {
                                                $sClass = "selected";
                                                $sChecked = "checked";
                                            }
                                            ?>
                                            <li class="<?php echo $sClass; ?> select-payment"
                                                data-id="<?php echo $paymentMethodId; ?>"
                                                data-uid="<?php echo $card['id']; ?>">
                                                <div class="column1 select-payment">
                                                    <input class="select-payment" id="card-1" type="radio"
                                                           name="credit-card"
                                                           value="1" <?php echo $sChecked; ?>>
                                                    <img class="credit-card select-payment"
                                                         src="<?php echo $card['img']; ?>">
                                                    <?php echo $card['label']; ?>
                                                    ***-<?php echo $card['last_four_digits']; ?>
                                                </div>
                                                <div class="column2 select-payment"><?php echo $card['owner']; ?></div>
                                                <div class="column3 select-payment"><?php echo $card['fExpirationDate']; ?></div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <div class="extra-action">
                                    <a href="#"><img src="<?php echo $uri . "/img/icons/plus.png"; ?>"></a>
                                    <img class="credit-card" src="<?php echo $item['newCardImg']; ?>">
                                    <a class="ccw-open-new-card-form" href="#">Ajouter une nouvelle carte de
                                        paiement</a>
                                    Leaderfit accepte la plupart des cartes de crédit et de débit
                                </div>
                            </div>
                        <?php elseif ('paypal' === $type): ?>
                            <div class="body payment-method payment-method-paypal">
                                <div class="cards">
                                    <ul class="selectable">
                                        <?php foreach ($item['items'] as $card): ?>
                                            <li class="select-payment" data-id="<?php echo $paymentMethodId; ?>">
                                                <div class="column1 select-payment">
                                                    <input id="card-1" type="radio" name="credit-card" value="1"
                                                           class="select-payment">
                                                    <img class="credit-card select-payment"
                                                         src="<?php echo $card['img']; ?>"></div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?>

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
                    <div class="step-past-content f-auto">

                        <div class="update-credit-card-wallet">
                            <img class="credit-card payment-update-img"
                                 src="<?php echo $uri . "/img/icons/credit-cards/mastercard.png"; ?>">
                            <span class="payment-update-type-label">Visa / Electron</span> ***-<span
                                    class="payment-update-last-four-digits">6372</span>
                        </div>
                        <div class="update-paypal">
                            <img class="credit-card payment-update-img"
                                 src="<?php echo $uri . "/img/icons/credit-cards/mastercard.png"; ?>">
                            <span>Paypal</span>
                        </div>


                        <br>
                        <a href="#">Adresse de facturation</a> <span
                                class="payment-update-address">6 Rue du Port Feu Hug...</span>
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

                        <?php foreach ($orderSections as $section): ?>
                            <div class="block-content carrier">
                                <div class="title title-success a-block-title">
                                    Date de livraison garantie: <?php echo $section['estimatedDeliveryDate']; ?>
                                </div>
                                <div class="sender">Articles expédiés par <?php echo $section['carrierLabel']; ?></div>
                                <div class="shipping-cost">Coût: <?php echo $section['shippingCost']; ?></div>
                                <div>Livré à: <span class="shipping-address"></span></div>

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
                    <li class="{-sClass-} select-payment"
                        data-id="{-id-}">
                        <div class="column1 select-payment">
                            <input class="select-payment" id="card-1" type="radio"
                                   name="credit-card"
                                   value="1" {-checked-}>
                            <img class="credit-card select-payment"
                                 data-src="{-img-}">
                            {-label-}
                            ***-{-last_four_digits-}
                        </div>
                        <div class="column2 select-payment">{-owner-}</div>
                        <div class="column3 select-payment">{-fExpirationDate-}</div>
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
                    <button class="button save-step-shipping">Envoyer à cette adresse</button>
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
                            <td><?php echo $cartTotal; ?></td>
                        </tr>
                        <tr>
                            <td>Livraison:</td>
                            <td><span class="shipping-costs">--</span></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="block last-block total-amount">
                <table class="total-amount-table">
                    <tr>
                        <td>Montant total:</td>
                        <td><span class="order-grand-total">--</span></td>
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

            var api = ekomApi.inst();
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


            function getSelectedAddressId() {
                return $(".addresses > ul", jGui).find("li.selected").attr("data-id");
            }

            function getSelectedPaymentId() {
                return $(".step-payment.step-open ul.selectable", jGui).find("li.selected:first").attr("data-id");
            }

            function getSelectedPaymentUid() {
                return $(".step-payment.step-open ul.selectable", jGui).find("li.selected:first").attr("data-uid");
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

                var model = orderModel.payment_method;

                var jSel = jGui.find(".step-payment.step-done");
                var type = model.type;

                if (
                    'visa' === type ||
                    'mastercard' === type
                ) {

                    jSel.find(".update-credit-card-wallet").show();
                    jSel.find(".update-paypal").hide();
                    //
                    jSel.find(".payment-update-type-label").html(model["label"]);
                    jSel.find(".payment-update-last-four-digits").html(model["last_four_digits"]);
                }
                else if ("paypal" === type) {
                    jSel.find(".update-credit-card-wallet").hide();
                    jSel.find(".update-paypal").show();
                    //
                }
                jSel.find(".payment-update-img").attr("src", model["img"]);
                jSel.find(".payment-update-address").html(orderModel.sections[0].address.address); // assuming single address mode
            }


            function updateOrderSectionsInfo(orderModel) {

                jCarriers.empty();


                var jProducts = null;
                for (var i in orderModel.sections) {
                    var section = orderModel.sections[i];
                    var carrier = section['carrier'];
                    carrier.estimated_delivery_date = carrier.estimated_delivery_date.substr(0, 10);

                    var jTpl = $.fn.cloneTemplate(jTplCarrier, carrier);
                    jTpl.find(".shipping-address").html(section.address.fAddress);

                    jCarriers.append(jTpl);
                    jProducts = jTpl.find('.products-list');


                    var items = section.items;
                    var jTpl2;
                    for (var i in items) {
                        var item = items[i];
                        jTpl2 = $.fn.cloneTemplate(jTplCarrierProduct, item);
                        jTpl2.find('option[value="' + item.quantity + '"]').prop('selected', 'selected');
                        jTpl2.find('.summary-quantity-selector').on('change', function () {
                            var productId = $(this).closest(".product").attr('data-id');
                            console.log($(this).val(), productId);

                        });
                        jProducts.append(jTpl2);
                    }

                }

                // zz
//                api.


                jGui.find(".order-grand-total").html(orderModel.summary.orderGrandTotal);
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


            $('.hint').tipr();
            api.on("user.address.updated", function (data) {
                refreshAddresses(data.addresses);
            });
            api.on("peipei.ccw.cardAdded", function (data) {
                refreshCards(data.cards);
            });


            function onGscpError(msg) {
                alert(msg);
            }

            /**
             * If this is false, it means the modal form for the address will be an insert.
             * If this is set to an address_id, it means the modal form for the address will be an update.
             */
            var currentAddressId = false;
            var addrId;

            $(document).on("click", function (e) {


                var jTarget = $(e.target);

                var jForm;
                var data;


                if (jTarget.hasClass("create-address-btn")) {
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
                        sideInfoStep(2, orderModel);


                    }, onGscpError, {
                        saveAsDefault: true,
                        marker: "shippingDone"
                    });


                    return false;


                    if ('old' === true) {

                        addrId = getSelectedAddressId();

                        var data = {
                            type: "singleAddress",
                            params: {
                                address_id: addrId
                            }
                        };
                        api.checkout.saveStepShipping(data, function (data) {
                            var orderModel = data.orderModel;
                            stepper.markStepAsDone(1);
                            stepper.openStep(2);


                            updateSelectedAddressInfo(orderModel);

                            sideInfoStep(2, orderModel);

                        }, function (errMsg) {
                            alert(errMsg);
                        });
                        return false;
                    }
                }
                else if (jTarget.hasClass("save-step-payment")) {
                    var paymentId = getSelectedPaymentId();

                    console.log(paymentId);
                    console.log("poupo");
                    return false;
                    api.checkout.setPaymentMethodId(addrId, function (data) {
                        var orderModel = data.orderModel;
                        stepper.markStepAsDone(1);
                        stepper.openStep(2);

                        updateSelectedAddressInfo(orderModel);
                        sideInfoStep(2, orderModel);


                    }, onGscpError, {
                        saveAsDefault: true
                    });


                    api.checkout.saveStepPayment({
                        paymentId: paymentId
                    }, function (data) {

                        var orderModel = data.orderModel;
                        stepper.markStepAsDone(2);
                        stepper.openStep(3);
                        updateSelectedPaymentInfo(orderModel);
                        updateOrderSectionsInfo(orderModel);
                        sideInfoStep(3, orderModel);

                    }, function (errMsg) {
                        alert(errMsg);
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
                    window.location.href = '<?php echo E::link("Ekom_checkoutOnePageThankYou"); ?>';
                    return false;
                }
                //----------------------------------------
                // PEIPEI
                //----------------------------------------
                else if (jTarget.hasClass("ccw-open-new-card-form")) {
                    jModalPeiPeiCardForm.find("form")[0].reset();
                    $.featherlight(jModalPeiPeiCardForm);
                    return false;
                }
                else if (jTarget.hasClass("ccw-cancel-card")) {
                    closeCurrentModal();
                    return false;
                }
                else if (jTarget.hasClass("ccw-create-card-btn")) {

                    jForm = jTarget.closest("form");
                    data = jForm.serialize();


                    api.peipei.creditCardWallet.createCard(data, function (data) {
                        closeCurrentModal();
                    }, function (formModel) {
                        window.onTheFlyForm.injectValidationErrors(jForm, formModel);
                    }, function (error) {
                        alert(error);
                    });


                    return false;
                }
            });


            var step = '<?php echo $currentStep; ?>';
            if ('shippingDone' === step) {
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


        });
    });
</script>
