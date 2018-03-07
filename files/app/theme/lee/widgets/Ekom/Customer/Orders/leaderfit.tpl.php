<?php


use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use ListParams\ListBundle\ListBundleInterface;
use Module\Ekom\Utils\E;
use Module\Ekom\Utils\EkomPhoneUtil;
use Module\Ekom\View\Cart\Cartoon\TotalRecap\TotalRecapRenderer;
use Module\Ekom\View\Cart\Cartoon\TotalRecap\WithPresetsTotalRecapRenderer;
use Module\ThisApp\Ekom\Helper\CartHelper;
use Module\ThisApp\Ekom\View\Helper\OrdersHelper;
use Module\ThisApp\Ekom\View\Hybrid\Slice\PaginationHybridWidget;
use Module\ThisApp\Ekom\View\Hybrid\Sort\ProductSortHybridWidget;
use Module\ThisApp\Ekom\View\InfoTemplateRenderer;
use Module\ThisApp\Ekom\View\PaginationTemplateRenderer;
use Module\ThisApp\Ekom\View\Payment\RepaymentScheduleRenderer;
use Module\ThisApp\Ekom\View\ProductListItemRenderer;
use Module\ThisApp\Ekom\View\SimpleProductListItemRenderer;
use Module\ThisApp\Ekom\View\SortTemplateRenderer;
use Module\ThisApp\ThisAppConfig;
use Theme\LeeTheme;

KamilleThemeHelper::css("customer-all.css");
//KamilleThemeHelper::css("customer/training-history.css");
LeeTheme::useLib("collapsibleBar");


$bundle = $v['bundle'];
$items = $bundle['general']['items'];

$renderer = SimpleProductListItemRenderer::create();

$sortRenderer = SortTemplateRenderer::create();
$paginationRenderer = PaginationTemplateRenderer::create();
$infoRenderer = InfoTemplateRenderer::create();


$openId = (array_key_exists('open', $_GET)) ? (int)$_GET['open'] : 0;
$localys = _l();

?>


<div class="widget widget-order-history" id="widget-order-history">
    <div class="bar-red main-title">MES HISTORIQUES DE COMMANDE</div>
    <?php if ($items): ?>


    <?php if (array_key_exists('sort', $bundle)): ?>
        <div class="sort-element">
            <?php ProductSortHybridWidget::create()->render($bundle); ?>
        </div>
    <?php endif; ?>


    <div class="container">

        <?php foreach ($items as $m):
//            az($m);
            $paymentDetails = $m['order_details']['payment_method_details'];
            $paymentMethodName = $m['order_details']['payment_method_name'];
            $cart = $m['order_details']['cartModel'];
            $shippingDetails = $cart['shippingDetails'];
            $total = $cart['priceOrderGrandTotal'];
            $estimatedDeliveryDate = null;
            if (array_key_exists("estimated_delivery_date", $shippingDetails)) {
                $estimatedDeliveryDate = $shippingDetails['estimated_delivery_date'];
            }
            $estimatedDeliveryText = array_key_exists("estimated_delivery_text", $shippingDetails) ? $shippingDetails['estimated_delivery_text'] : null;

            $id = (int)$m['id'];
            $products = $cart['items'];
            $sState = "";
            if ($openId === $id) {
                $sState = "open";
            }
            $repaymentSchedule = null;


            ?>
            <div class="order-item <?php echo $sState; ?> collapsible-parent">
                <div class="bar-gray collapsible-bar collapse-trigger <?php echo $sState; ?>">
                    <div class="order-label f-auto collapse-trigger">COMMANDE
                        DU <?php echo $localys->getLongDate(strtotime($m['date'])); ?></div>
                    <div class="order-total collapse-trigger"><?php echo $total; ?></div>
                    <div class="trigger collapse-trigger"></div>
                </div>
                <div class="content">
                    <div class="header">
                        <div>
                            N° de commande: <?php echo $m['reference']; ?>
                            <?php if (array_key_exists("debug", $_GET)): ?>
                                <span class="grayed-out">#<?php echo $m['id']; ?></span>
                            <?php endif; ?>
                        </div>
                        <?php OrdersHelper::renderUserOrderStatusBar($m['status_history']); ?>
                        Récapitulatif:
                    </div>
                    <div class="product-list">
                        <?php foreach ($products as $p): ?>
                            <?php $renderer->render($p); ?>
                        <?php endforeach; ?>
                    </div>
                    <div class="cartoon-total-recap">
                        <?php
                        $totalRecap = WithPresetsTotalRecapRenderer::create()
                            ->setCartModel($cart)
                            ->setPreset();
                        $totalRecap->render();

                        ?>
                    </div>
                    <div class="addresses">
                        <table>
                            <tr>
                                <td>
                                    <?php $a = $m['shipping_address']; ?>
                                    <div class="title">Adresse de livraison:</div>
                                    <div class="info">
                                        <?php echo $a['fName']; ?><br>
                                        <?php echo $a['address']; ?><br>
                                        <?php echo $a['postcode']; ?> <?php echo $a['city']; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php $b = $m['billing_address']; ?>
                                    <div class="title">Adresse de facturation:</div>
                                    <div class="info">
                                        <?php echo $b['fName']; ?><br>
                                        <?php echo $b['address']; ?><br>
                                        <?php echo $b['postcode']; ?> <?php echo $b['city']; ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?php $u = $m['user_info']; ?>
                                    <div class="title">Contact:</div>
                                    <div class="info">
                                        <?php echo $localys->getGenderAbbreviation($u['gender']); ?> <?php echo $u['last_name']; ?> <?php echo $u['first_name']; ?>
                                        <br>
                                        <?php
                                        if ('' !== trim($u['mobile'])) {
                                            echo $u['mobile'];
                                        } else {
                                            $phoneInfo = EkomPhoneUtil::getPhoneInfo($u['phone']);
                                            echo $phoneInfo[1];
                                        }
                                        ?><br>
                                        <?php echo $u['email']; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="title">Moyen de paiement:</div>
                                    <div class="info">
                                        <?php if (ThisAppConfig::PAYMENT_METHOD_CREDIT_CARD === $paymentMethodName): ?>
                                            <?php
                                            $paymentMode = $paymentDetails['credit_card_payment_mode'];
                                            $creditCard = $paymentDetails['credit_card'];
                                            ?>
                                            Carte de crédit<br>
                                            <?php echo $creditCard["type"]; ?> - ************<?php echo $creditCard['last_four_digits']; ?>
                                            <br>
                                            Expire le <?php echo $creditCard["expiration_date"]; ?>
                                            <br>
                                            Paiement en
                                            <?php if ('1x' === $paymentMode): ?>
                                                une fois
                                            <?php elseif ('3x' === $paymentMode): ?>
                                                3 fois sans frais
                                            <?php elseif ('4x' === $paymentMode): ?>
                                                4 fois sans frais
                                            <?php endif; ?>

                                            <?php if ("3x" === $paymentMode || "4x" === $paymentMode): ?>
                                                <?php $repaymentSchedule = $paymentDetails['repayment_schedule']; ?>
                                            <?php endif; ?>


                                        <?php elseif (ThisAppConfig::PAYMENT_METHOD_TRANSFER === $paymentMethodName): ?>
                                            Virement
                                        <?php else: ?>
                                            Non enregistré
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="repayment-schedule">
                        <?php if (null !== $repaymentSchedule): ?>
                            <?php RepaymentScheduleRenderer::render($repaymentSchedule); ?>
                        <?php endif; ?>
                    </div>
                    <?php if (true === $cart['shippingIsApplied']): ?>
                        <div class="shipping-info">
                            <span class="label">Date de livraison estimée:</span>
                            <span class="value"><?php echo CartHelper::getEstimatedDeliveryDate($estimatedDeliveryDate); ?></span>
                            <br>

                        </div>
                    <?php endif; ?>
                    <!--                    <div class="extra-comment">-->
                    <!--                        Ce transporteur prendra contact avec vous pour établir un jour et une heure de passage-->
                    <!--                    </div>-->
                </div>
            </div>
        <?php endforeach; ?>


        <?php if (array_key_exists("slice", $bundle)): ?>
            <div class="pagination-element">
                <?php PaginationHybridWidget::create()->render($bundle['slice']); ?>
            </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    jqueryComponent.ready(function () {
        var jWidget = $("#widget-order-history");
        jWidget.collapsibleBar();

    });
</script>