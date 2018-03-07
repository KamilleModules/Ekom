<?php

use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Module\Ekom\Utils\E;

$uri = "/theme/" . ApplicationParameters::get("theme");


?>
<div class="steps">
    <div class="step active step-address">
        <div class="block-title">
            <div class="step-number">1</div>
            <div class="step-title">Sélectionnez une adresse de livraison</div>
        </div>
        <div class="block-content">

            <div class="header">
                <div class="title a-block-title">Vos adresses</div>
                <div class="info-link">
                    <a href="#">Vous faîtes un envoi à plusieurs adresses ?</a>
                </div>
            </div>


            <div class="body">
                <div class="addresses">
                    <ul>
                        <li class="selected">
                            <input id="address-1" type="radio" value="1" checked>
                            <label for="address-1">
                                <b>lafitte pierre</b>
                                6 Rue du Port Feu Hugon, tours, 37000 France
                                <a href="#">Modifier</a>
                            </label>
                        </li>
                    </ul>
                </div>
                <div class="extra-action">
                    <a href="#"><img src="<?php echo $uri . "/img/icons/plus.png"; ?>"></a> <a href="#">Ajouter
                        une nouvelle adresse</a>
                </div>
            </div>

        </div>
        <div class="block-content-footer">
            <button onclick="window.location.href='<?php echo E::link("Ekom_checkoutOnePage") . "?step=2"; ?>'"
                    class="button">Envoyer à cette adresse
            </button>
        </div>
    </div>
    <div class="step">
        <div class="block-title">
            <div class="step-number">2</div>
            <div class="step-title">Mode de paiement</div>
        </div>
    </div>
    <div class="step last">
        <div class="block-title">
            <div class="step-number">3</div>
            <div class="step-title">Articles et expédition</div>
        </div>
    </div>
</div>