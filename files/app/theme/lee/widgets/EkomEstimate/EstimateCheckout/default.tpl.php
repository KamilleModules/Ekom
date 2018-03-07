<?php

use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Module\ThisApp\Ekom\View\Cart\CartWindowRenderer;
use Module\ThisApp\Ekom\View\Cart\CheckoutOrderSummaryRenderer;
use Module\ThisApp\Ekom\View\Cart\CheckoutTunnelRenderer;

KamilleThemeHelper::css("cart.css");
KamilleThemeHelper::css("table-form.css");



$c = $v['cartModel'];
?>


<div class="checkout-two-columns window2">
    <div class="left-column">
        <?php CheckoutTunnelRenderer::create()->render($v); ?>
    </div>
    <div class="right-column">
        <?php CartWindowRenderer::create()->render($c); ?>
        <?php CheckoutOrderSummaryRenderer::create()->render($v['cartModel']); ?>
    </div>
</div>