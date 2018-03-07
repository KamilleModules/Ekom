<?php


use Module\Ekom\Utils\E;


?>
<div class="right-col-block">
    <div class="block tcenter">
        <button onclick="window.location.href='<?php echo E::link("Ekom_checkoutOnePage") . "?step=3"; ?>'"
                class="button">Utiliser ce mode de paiement
        </button>
        <p class="text-small">
            Veuillez sélectionner une méthode de paiement pour continuer.
            Vous pourrez vérifier votre commande avant validation.
        </p>
    </div>
    <div class="block">
        <div class="title">Récapitulatif de commande</div>
        <div class="table">
            <table>
                <tr>
                    <td>Articles:</td>
                    <td>EUR 43,90</td>
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
                <td>EUR 43,90</td>
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