<?php


use Kamille\Utils\ThemeHelper\KamilleThemeHelper;
use ListParams\ListBundle\ListBundleInterface;
use Module\Ekom\Utils\EkomPhoneUtil;
use Module\ThisApp\Ekom\View\InfoTemplateRenderer;
use Module\ThisApp\Ekom\View\PaginationTemplateRenderer;
use Module\ThisApp\Ekom\View\ProductListItemRenderer;
use Module\ThisApp\Ekom\View\SimpleProductListItemRenderer;
use Module\ThisApp\Ekom\View\SortTemplateRenderer;
use Theme\LeeTheme;

KamilleThemeHelper::css("customer-all.css");
//KamilleThemeHelper::css("customer/training-history.css");
LeeTheme::useLib("collapsibleBar");


/**
 * @var $bundle ListBundleInterface
 */
$bundle = $v['listBundle'];
//$items = $v['orderDetails'];
$items = $bundle->getListItems();
$pagination = $bundle->getPaginationFrame();
$sort = $bundle->getSortFrame();
$info = $bundle->getInfoFrame();

$l = _l();




$renderer = SimpleProductListItemRenderer::create();

$sortRenderer = SortTemplateRenderer::create();
$paginationRenderer = PaginationTemplateRenderer::create();
$infoRenderer = InfoTemplateRenderer::create();


$openId = (array_key_exists('open', $_GET)) ? (int)$_GET['open'] : 0;



?>


<div class="widget widget-order-history" id="widget-order-history">
    <div class="bar-red main-title">MES HISTORIQUES DE COMMANDE</div>


    <?php echo $infoRenderer->render($info->getArray()); ?>
    <div class="sort-element">
        <?php echo $sortRenderer->render($sort->getArray()); ?>
    </div>


    <div class="container">
        <?php foreach ($items as $m):
            $section = $m['section'];
            $id = (int)$m['id'];
            $products = $section['productsInfo'];
            $sState = "";
            if ($openId === $id) {
                $sState = "open";
            }
            ?>
            <div class="order-item <?php echo $sState; ?> collapsible-parent">
                <div class="bar-gray collapsible-bar collapse-trigger <?php echo $sState; ?>">
                    <div class="order-label f-auto collapse-trigger">COMMANDE
                        DU <?php echo $l->getLongDate(strtotime($m['date'])); ?></div>
                    <div class="order-total collapse-trigger"><?php echo $m['orderGrandTotal']; ?></div>
                    <div class="trigger collapse-trigger"></div>
                </div>
                <div class="content">
                    <div class="header">
                        N° de commande: <?php echo $m['ref']; ?><br>
                        Status: <?php echo $m['last_status']; ?><br>
                        Récapitulatif:
                    </div>
                    <div class="product-list">
                        <?php foreach ($products as $p): ?>
                            <?php $renderer->render($p); ?>
                        <?php endforeach; ?>


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
                                        <?php echo $l->getGenderAbbreviation($u['gender']); ?> <?php echo $u['last_name']; ?> <?php echo $u['first_name']; ?>
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
                                    <?php $pp = $m['payment_details']; ?>
                                    <div class="title">Moyen de paiement:</div>
                                    <div class="info">
                                        <?php if ($pp): ?>
                                            <?php echo __($m['paymentMethodName'], 'modules/PeiPei/PeiPei'); ?>
                                            <?php if ('credit_card_wallet' === $m['paymentMethodName']): ?>
                                                <?php echo $pp['label']; ?> *****<?php echo $pp['last_four_digits']; ?>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            Non enregistré
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="shipping-info">
                        <span class="label">Date de livraison estimée:</span> <span class="value">
                            <?php echo date('d/m/Y', strtotime($section['estimatedDeliveryDate'])); ?>
                        </span>
                        <br>
                        <span class="label">Transporteur:</span> <span
                                class="value"><?php echo $section['carrierLabel']; ?></span>
                    </div>
                    <div class="extra-comment">
                        Ce transporteur prendra contact avec vous pour établir un jour et une heure de passage
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>


    <div class="pagination-element">
        <?php echo $paginationRenderer->render($pagination->getArray()); ?>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function (event) {
        $(document).ready(function () {
            var jWidget = $("#widget-order-history");
            jWidget.collapsibleBar();
        });
    });
</script>