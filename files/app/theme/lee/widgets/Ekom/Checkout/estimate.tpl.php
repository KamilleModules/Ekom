<?php

use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use Module\ThisApp\Ekom\View\Cart\CartWindowRenderer;
use Module\ThisApp\Ekom\View\Cart\CheckoutOrderSummaryRenderer;
use Module\ThisApp\Ekom\View\Cart\CheckoutTunnelRenderer;
use Module\ThisApp\Ekom\View\Cart\EstimateCartWindowRenderer;
use Module\ThisApp\Ekom\View\Cart\EstimateCheckoutTunnelRenderer;

KamilleThemeHelper::css("cart.css");
KamilleThemeHelper::css("table-form.css");


$c = $v['cartModel'];
?>


<div class="checkout-two-columns window2">
    <div class="left-column">
        <?php EstimateCheckoutTunnelRenderer::create()->render($v); ?>
    </div>
    <div class="right-column">
        <?php EstimateCartWindowRenderer::create()->render($c); ?>
        <?php CheckoutOrderSummaryRenderer::create()->render($v['cartModel']); ?>
    </div>
</div>