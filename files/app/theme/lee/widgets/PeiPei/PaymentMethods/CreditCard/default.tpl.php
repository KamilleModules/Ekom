<?php


use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Module\PeiPei\CreditCardWallet\CreditCardWalletHelper;
use Module\ThisApp\PeiPei\View\AddCardAliasButtonRenderer;
use Module\ThisApp\PeiPei\View\CreditCardItemRenderer;
use Module\ThisApp\PeiPei\View\CreditCardWalletRenderer;
use Theme\LeeTheme;


KamilleThemeHelper::css("customer-all.css");
KamilleThemeHelper::css("peipei/payment-methods.css");
LeeTheme::useLib('featherlight');


$cards = $v['cards'];

?>
<div class="widget payment-method-credit-card" id="payment-method-credit-card">
    <?php echo CreditCardWalletRenderer::renderWallet($cards); ?>
</div>

<div class="bionic-marker" data-type="intent" data-value="creditCardWallet"></div>

<script>
    jqueryComponent.ready(function () {
        var api = ekomApi.inst();
        var jContext = $('#payment-method-credit-card');
        api.on('peipei.ccw.cardDeleted', function (data) {
            jContext.empty().append(data.creditCartWalletHtml);
        });
    });
</script>