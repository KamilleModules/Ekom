<?php


use Module\Ekom\Utils\E;


?>
<div class="right-col-block">
    <div class="block tcenter">
        <button onclick="window.location.href='<?php echo E::link("Ekom_checkoutOnePage") . "?step=2"; ?>'"
                class="button">Envoyer à cette adresse
        </button>
        <p class="text-small">
            Veuillez choisir une adresse pour passer à l'étape suivante.
            Vous pourrez encore annuler ou modifier votre commande.
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
                    <td>--</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="block last-block total-amount">
        <table class="total-amount-table">
            <tr>
                <td>Montant total:</td>
                <td>--</td>
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