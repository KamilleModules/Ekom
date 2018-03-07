<?php

use Kamille\Utils\ThemeHelper\KamilleThemeHelper;


KamilleThemeHelper::css("customer/dashboard.css");


?>
<div class="widget widget-dashboard">
    <div class="line">
        <div class="block">
            <div class="top-space-order"></div>
            <div class="title">Mes commandes</div>
            <div class="links">
                <a href="<?php echo $v['lastOrderLink']; ?>">Ma dernière <em>commande</em></a>
                <a href="<?php echo $v['pendingOrdersLink']; ?>"><em><?php echo $v['nbPendingOrders']; ?>
                        commande(s)</em> en cours</a>
                <a href="<?php echo $v['canceledOrdersLink']; ?>">Ma dernière <em>commande annulée</em></a>
            </div>
        </div>

        <div class="block">
            <div class="top-space-points">
                <div class="bignumber"><?php echo $v['nbLoyaltyPoints']; ?></div>
                <div class="bigtext">Points fidélité</div>
            </div>
            <div class="title">Mes points</div>
            <div class="links">
                <a href="<?php echo $v['myPointsLink']; ?>">Solde de <em>mon compte</em></a>
            </div>
        </div>
    </div>

    <div class="line">
        <div class="block">
            <div class="top-space-preferences"></div>
            <div class="title">Préférences</div>
            <div class="links">
                <a href="<?php echo $v['uriWishList']; ?>"><em><?php echo $v['nbWishListItems']; ?> produits</em> dans mes favoris</a>
                <a href="<?php echo $v['uriHistory']; ?>"><em><?php echo $v['nbHistoryItemsLastDay']; ?> articles consultés</em> lors de ma dernière visite</a>
            </div>
        </div>

        <div class="block">
            <div class="top-space-information"></div>
            <div class="title">Informations</div>
            <div class="links">
                <a href="<?php echo $v['uriShippingAddress']; ?>">Mes adresses de <em>livraison</em></a>
                <a href="<?php echo $v['uriBillingAddress']; ?>">Mes adresses de <em>facturation</em></a>
                <a href="<?php echo $v['uriPaymentMethods']; ?>">Mes moyens de <em>paiement</em></a>
            </div>
        </div>
    </div>
</div>