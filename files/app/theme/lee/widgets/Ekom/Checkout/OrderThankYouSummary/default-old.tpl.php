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
            Numéro de commande : {reference}<br>
            Numéro de tracking colis : {trackingNumber}<br>

        </div>
        <div style="margin-top: 10px;">
            Les articles suivants seront expédiés à <a href="#">{shippingUserName}</a> par
            Leaderfit.
            <br>
            Date de livraison estimée : <b>{estimatedDeliveryDate}</b>
        </div>
        <div class="products-list">
            <table style="margin-top: 10px;">
                <?php foreach ($v['products'] as $p): ?>
                    <tr>
                        <td>
                            <img width="120" style="vertical-align: middle"
                                 src="<?php echo $p['img']; ?>">
                        </td>
                        <td>
                            <a href="<?php echo $p['uri']; ?>"><?php echo $p['label']; ?></a>: <?php echo $p['quantity']; ?>
                            exemplaire(s)
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <div class="order-details">
            <a href="{uriCustomerOrders}">Consulter ou modifier votre commande</a>
        </div>
    </div>

    <div class="social-post-module" style="display: none">Todo</div>
</div>
