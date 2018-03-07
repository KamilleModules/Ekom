<?php

use Kamille\Utils\ThemeHelper\KamilleThemeHelper;


KamilleThemeHelper::css("widgets/widget-order-thank-you.css");

?>
<div class="success-message widget-order-thank-you window">


    <div class="head-title">Merci pour votre confiance</div>
    <div class="text">
        Nous avons bien pris en compte votre commande et nous vous en remercions.<br>
        Celle-ci sera prise en compte et traitée dans les plus brefs délais.
    </div>


    <?php if('transfer' === $v['paymentMethod']): ?>
    <div style="margin-top: 10px">
        N'oubliez pas de nous faire parvenir votre virement rapidement.
    </div>
    <?php endif; ?>

    <div class="points-info">
        Grâce à votre commande vous avez cumulé:
        <b><?php echo $v['lfPoints']; ?> points LF'</b>
    </div>

    <a href="<?php echo $v['uriMyAccount']; ?>" class="lee-button lee-red-button page-button">
        RETOUR À LA PAGE MON COMPTE
    </a>
</div>
