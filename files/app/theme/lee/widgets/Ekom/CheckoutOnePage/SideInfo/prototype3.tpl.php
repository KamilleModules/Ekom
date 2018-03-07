<?php


use Module\Ekom\Utils\E;


?>
<div class="right-col-block">
    <div class="block tcenter">
        <button onclick="window.location.href='<?php echo E::link("Ekom_checkoutOnePageThankYou"); ?>'"
                class="button">Acheter
        </button>
        <p class="text-small">
            En validant votre commande, vous acceptez l'intégralité de nos <a href="#">Conditions générales de
                vente</a> ainsi
            que notre politique de gestion de <a href="#">vos informations personnelles</a> ainsi que les
            Conditions
            <a href="#">Cookies et Publicité sur Internet</a>.
        </p>
    </div>
    <div class="block">
        <div class="title">Récapitulatif de commande</div>
        <div class="table">
            <table>
                <tr>
                    <td>Articles:</td>
                    <td>EUR 58,80</td>
                </tr>
                <tr>
                    <td>Livraison:</td>
                    <td>EUR 00,00</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="block last-block total-amount">
        <table class="total-amount-table">
            <tr>
                <td>Montant total:</td>
                <td>EUR 58,80</td>
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
    <div>
        <a href="#">Comment sont calculés les frais de livraison?</a>
    </div>
    <div class="mt7">
        La livraison Amazon Premium a été appliquée aux articles de votre commande pouvant en bénéficier.
        (<a href="#">Pourquoi tous mes articles ne peuvent-ils pas bénéficier d'Amazon Premium ?</a>)
    </div>
</div>