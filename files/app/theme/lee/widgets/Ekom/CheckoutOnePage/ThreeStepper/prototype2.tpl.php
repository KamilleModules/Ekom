<?php

use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Module\Ekom\Utils\E;

$uri = "/theme/" . ApplicationParameters::get("theme");


?>
<div class="steps">
    <div class="step step-address past">
        <div class="block-title">
            <div class="step-number">1</div>
            <div class="step-title">Adresse de livraison</div>
        </div>
        <div class="step-past-content f-auto">
            lafitte pierre<br>
            6 Rue du Port Feu Hugon<br>
            tours, 37000
        </div>
        <div class="step-past-content">
            <a href="#">Modifier</a>
        </div>
    </div>
    <div class="step step-payment active">
        <div class="block-title">
            <div class="step-number">2</div>
            <div class="step-title">Sélectionnez un mode de paiement</div>
        </div>
        <div class="block-content">

            <div class="header">
                <div class="column1 a-block-title">Vos cartes de paiement</div>
                <div class="column2">Nom du titulaire de la carte</div>
                <div class="column3">Date d'expiration</div>
            </div>
            <div class="body">
                <div class="cards">
                    <ul>
                        <li class="selected">
                            <div class="column1">
                                <input id="card-1" type="radio" name="credit-card" value="1" checked>
                                <img class="credit-card"
                                     src="<?php echo $uri . "/img/icons/credit-cards/mastercard.png"; ?>">
                                Visa / Electron ***-6372
                            </div>
                            <div class="column2">lafitte</div>
                            <div class="column3">09/2018</div>
                        </li>
                        <li>
                            <div class="column1">
                                <input id="card-1" type="radio" name="credit-card" value="1">
                                <img class="credit-card"
                                     src="<?php echo $uri . "/img/icons/credit-cards/paypal.png"; ?>">
                                Visa / Electron ***-6372
                            </div>
                            <div class="column2">lafitte</div>
                            <div class="column3">09/2018</div>
                        </li>
                    </ul>
                </div>
                <div class="extra-action">
                    <a href="#"><img src="<?php echo $uri . "/img/icons/plus.png"; ?>"></a>
                    <img class="credit-card" src="<?php echo $uri . "/img/icons/credit-cards/visa.png"; ?>">
                    <a href="#">Ajouter une nouvelle carte de paiement</a>
                    Leaderfit accepte la plupart des cartes de crédit et de débit
                </div>
            </div>


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
            <button onclick="window.location.href='<?php echo E::link("Ekom_checkoutOnePage") . "?step=3"; ?>'"
                    class="button">Utiliser ce mode de paiement
            </button>
        </div>
    </div>
    <div class="step last">
        <div class="block-title">
            <div class="step-number">3</div>
            <div class="step-title">Articles et expédition</div>
        </div>
    </div>
</div>