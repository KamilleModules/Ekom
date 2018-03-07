<?php

use Kamille\Architecture\ApplicationParameters\ApplicationParameters;

$uri = "/theme/" . ApplicationParameters::get("theme");

?>
<div class="success-message widget-order-thank-you-summary window">


    <div class="icon">
        <img src="<?php echo $uri . "/img/icons/valid.png"; ?>">
    </div>
    <div class="order-summary">
        <div class="title">Merci, votre commande a été passée.</div>
        <div class="info">
            Nous vous avons envoyé un message électronique de confirmation.
            <br>
            <span class="success">Nouveau</span> Suivez votre commande.
            Obtenir <a href="#">Appli Amazon</a>.
        </div>

        <div class="concrete-info">
            Numéro de commande : 406-4313845-8293118
        </div>
        <div class="products-list">
            <ul>
                <li>
                    SODIAL(R) Magique Wiggly Vis Sans Fin Floue ... sera expédié à <a href="#">lafitte pierre</a> de
                    Trend Mall.
                    <br>
                    Date de livraison estimée : <b>6 juin 2017 - 15 juin 2017</b>
                </li>
            </ul>
        </div>
        <div class="order-details">
            <a href="#">Consulter ou modifier votre commande</a>
        </div>
    </div>

    <div class="social-post-module" style="display: none">Todo</div>
</div>
