<?php

use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Architecture\Registry\ApplicationRegistry;
use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;
use Module\Ekom\Api\EkomApi;
use Module\Ekom\Utils\E;
use Theme\LeeTheme;

LeeTheme::useLib('jquery');
LeeTheme::useLib('onTheFlyForm');



$uri = "/theme/" . ApplicationParameters::get("theme");

HtmlPageHelper::css($uri . "/css/payment.css");


$cartModel = EkomApi::inst()->cartLayer()->getCartModel();


?>
<div>
    <div class="topbar">
        <div class="inner window">
            <a href="/" class="logo left">
                <img src="<?php echo $uri . "/img/logo-small.png"; ?>" alt="Secured">
            </a>

            <span class="middle font-title">Passer la commande ( <a href="<?php echo E::link("Ekom_cart"); ?>"><?php echo $cartModel['totalQuantity']; ?> articles</a> )</span>

            <a href="#https://www.amazon.fr/gp/help/customer/display.html?ie=UTF8&nodeId=3329781&ref_=ox_spc_privacy"
               class="right">
                <img src="<?php echo $uri . "/img/icons/secured.png"; ?>" alt="Secured">
            </a>
        </div>
    </div>


    <div class="maincontent window">

            <?php $l->widget('threeStepper'); ?>
        <div class="left-col">

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
        <div class="right-col">
            <?php $l->widget('sideInfo'); ?>
        </div>

    </div>
</div>