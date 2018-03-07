<?php


use Kamille\Utils\ThemeHelper\KamilleThemeHelper;

KamilleThemeHelper::css("widgets/estimate-order-thankyou.css");

?>
<div class="widget widget-estimate-order-thank-you">
    <div class="title">Merci pour votre confiance</div>
    <div class="main-text">
        Nous avons bien pris en compte votre demande de devis et nous vous en remercions.<br>
        Vous recevrez dans les plus brefs délais votre devis sur l'adresse mail associée à votre compte.
    </div>

    <div class="teaser">
        Si vous transformez ce devis en commande vous pourrez cumuler: <br>
        <span class="important"><?php echo $v['lfPoints']; ?> points LF'</span>
    </div>

    <a href="{uriUserAccountEstimate}" class="lee-gray-button goto-estimate-btn">CONSULTER MON DEVIS</a>
    <div class="image-container">
        <img src="/theme/lee/img/splash/estimate-thankyou.jpg">
    </div>
    <a href="{uriContinuePurchase}" class="lee-red-button continue-purchase-btn">CONTINUER MES ACHATS</a>
</div>
