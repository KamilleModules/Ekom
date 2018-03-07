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
    <div class="step step-payment past ">
        <div class="block-title">
            <div class="step-number">2</div>
            <div class="step-title">Mode de paiement</div>
        </div>
        <div class="step-past-content f-auto">
            <img class="credit-card"
                 src="<?php echo $uri . "/img/icons/credit-cards/mastercard.png"; ?>">
            Visa / Electron ***-6372
            <br>
            <a href="#">Adresse de facturation</a> 6 Rue du Port Feu Hug...
            <br>
            Indiquez le code de votre chèque cadeau ou code promotionnel
            <br>
            <input class="discount" type="text" placeholder="Saisissez le code">
            <button class="button button-gray">Appliquer</button>
        </div>
        <div class="step-past-content">
            <a href="#">Modifier</a>
        </div>
    </div>
    <div class="step step-shipping last active">
        <div class="block-title">
            <div class="step-number">3</div>
            <div class="step-title">Vérification et validation de votre commande</div>
        </div>
        <div class="carriers">
            <div class="block-content carrier">
                <div class="title title-success a-block-title">
                    Date de livraison garantie: 18 mai 2017
                    <span class="neutral">Si vous commandez dans les 10 heures et 8 minutes qui suivent! (<a href="#">Détails</a>)</span>
                </div>
                <div class="sender">Articles expédiés par Leaderfit EU S.A.R.L</div>
                <div class="main-block">
                    <div class="products-list">
                        <div class="product">
                            <div class="image">
                                <img src="<?php echo $uri . "/img/products/chaussettes.jpg"; ?>">
                            </div>
                            <div class="product-info">
                                <div class="description">
                                    Fixation tête caméra de sport - Harnais tête pour gopro neocam pro sjcam compatible
                                    avec toutes les caméras de sport
                                </div>
                                <div class="price">
                                    EUR 14,90 <img src="<?php echo $uri . "/img/icons/premium.png"; ?>">
                                </div>

                                <div class="selector">
                                    <label>Qté</label>
                                    <select>
                                        <option value="1">1</option>
                                    </select>
                                </div>

                                <div class="seller-info">
                                    Vendu par: Leaderfit
                                </div>

                                <div class="availability-extra">
                                    Il ne reste plus que 10 exemplaire(s) en stock.
                                </div>
                                <div class="options">
                                    <a href="#">Ajouter des options cadeau</a>
                                </div>
                            </div>
                        </div>
                        <div class="product">
                            <div class="image">
                                <img src="<?php echo $uri . "/img/products/kettle-bell.jpg"; ?>">
                            </div>
                            <div class="product-info">
                                <div class="description">
                                    Caméra Sports/TopElek Caméra embarquée étanche 30m Haute Définition/Caméra Action
                                    Sport avec 12MP image, Full HD 1080p à 30fps Vidéo,170 °Grand-Angle,30m Etanche,2
                                    pouces LCD Display,Accessoires multiples
                                </div>
                                <div class="price">
                                    EUR 42,98
                                </div>

                                <div class="selector">
                                    <label>Qté</label>
                                    <select>
                                        <option value="1">1</option>
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
                    </div>
                    <div class="shipping-details">
                        <div class="title">Choisissez votre mode de livraison Premium:</div>
                        <input type="radio" name="carrierType[leaderfit]" value="a" checked>
                        <label>Livraison en 1 jour ouvré</label>
                        <span class="green">&mdash; Recevez-le demain, le 18 mai</span>
                        <br>
                        <input type="radio" name="carrierType[leaderfit]" value="b">
                        <label>Livraison Rapide (jusqu'à 3 jours ouvrés)(<a href="#">Voir les détails</a>)</label>
                    </div>
                </div>
            </div>
            <div class="block-content carrier">
                <div class="title a-block-title">
                    Date de livraison estimée : 6 juin 2017 - 15 juin 2017
                </div>
                <div class="sender">Articles expédiés par Leaderfit EU S.A.R.L</div>
                <div class="main-block">
                    <div class="products-list">
                        <div class="product">
                            <div class="image">
                                <img src="<?php echo $uri . "/img/products/roue.jpg"; ?>">
                            </div>
                            <div class="product-info">
                                <div class="description">
                                    SODIAL(R) Magique Wiggly Vis Sans Fin Floue Jouets
                                </div>
                                <div class="seller-name">
                                    Sodial
                                </div>
                                <div class="price">
                                    EUR 0,92
                                </div>

                                <div class="selector">
                                    <label>Qté</label>
                                    <select>
                                        <option value="1">1</option>
                                    </select>
                                </div>

                                <div class="seller-info">
                                    Vendu par: Trendmail
                                </div>

                                <div class="availability-extra"></div>
                                <div class="options">
                                    Options de cadeau non disponibles.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="shipping-details">
                        <div class="title">Choisissez votre mode de livraison:</div>
                        <input type="radio" name="carrierType[sodial]" value="b" checked>
                        <label>Livraison Rapide <span class="green">&mdash; Recevez-le demain, le 18 mai</span>
                    </div>
                </div>
            </div>
        </div>


        <div class="block-content total-amount">
            <div class="button-container">
                <button class="button" onclick="window.location.href='<?php echo E::link("Ekom_checkoutOnePageThankYou"); ?>'; return false">Acheter</button>
            </div>
            <div class="info">
                <div class="total">Montant total :EUR 58,80</div>
                <div class="legal">
                    En validant votre commande, vous acceptez l'intégralité de nos <a href="#">Conditions générales de
                        vente</a> ainsi
                    que notre politique de gestion de <a href="#">vos informations personnelles</a> ainsi que les
                    Conditions
                    <a href="#">Cookies et Publicité sur Internet</a>.
                </div>
            </div>
        </div>
    </div>
</div>