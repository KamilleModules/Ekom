<?php


use Core\Services\A;
use Module\Ekom\Api\Layer\CartLayer;
use Module\Ekom\View\Cart\Cartoon\ItemsDescription\ItemsDescriptionRenderer;

require_once __DIR__ . "/../boot.php";
require_once __DIR__ . "/../init.php";


A::testInit();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link rel="stylesheet" href="/css/cartoon.css">
</head>
<body>
<div class="cartoon">
    <?php

    $cartModel = CartLayer::create()->getCartModel();

    $renderer = ItemsDescriptionRenderer::create()
        ->setItems($cartModel['items'])
        ->setColumns([
            'quantity',
//        'image',
//        'reference',
//        'seller',
            'label',
//        'description',
//        'originalPrice',
//        'discount',
//        'discountLabel',
            'basePrice',
            'tax',
//        'taxLabel',
            'salePrice',
//        'linePriceWithoutTax',
            'linePriceWithTax',
        ]);
    $renderer->render();
    ?>



    <table>
        <tr>
            <td>Sous-Total HT</td>
            <td><?php echo $cartModel['priceCartTotal']; ?></td>
        </tr>
        <tr>
            <td>Frais de livraison HT</td>
            <td><?php echo $cartModel['shippingShippingCostWithoutTax']; ?></td>
        </tr>
        <tr>
            <td>
                Taxe frais de livraison
            </td>
            <td><?php echo $cartModel['shippingTaxAmount']; ?></td>
        </tr>
        <tr class="nobottomborder">
            <td>Taxe frais de livraison</td>
            <td></td>
        </tr>
        <tr class="notopborder">
            <td class="indent-1">
                <?php echo $cartModel['shippingTaxGroupLabel']; ?>
            </td>
            <td><?php echo $cartModel['shippingTaxAmount']; ?></td>
        </tr>
        <tr>
            <td>Frais de livraison TTC</td>
            <td><?php echo $cartModel['shippingShippingCost']; ?></td>
        </tr>
        <tr>
            <td>Total avant coupons</td>
            <td><?php echo $cartModel['priceOrderTotal']; ?></td>
        </tr>
        <tr>
            <td>Coupons</td>
            <td><?php echo $cartModel['couponSaving']; ?></td>
        </tr>
        <tr class="nobottomborder">
            <td>Coupons</td>
            <td></td>
        </tr>
        <?php
        $nbCoupons = count($cartModel['couponDetails']);
        $i = 1;
        foreach ($cartModel['couponDetails'] as $coupon):
            $noBottomBorder = ($nbCoupons === $i) ? "" : "nobottomborder";
            ?>
            <tr class="notopborder <?php echo $noBottomBorder; ?>">
                <td class="indent-1"><?php echo $coupon["label"]; ?></td>
                <td><?php echo $coupon["saving"]; ?></td>
            </tr>
            <?php
            $i++;
        endforeach; ?>
        <tr>
            <td>Total</td>
            <td><?php echo $cartModel['priceOrderGrandTotal']; ?></td>
        </tr>
    </table>


</div>
<pre>
    <?php
    unset($cartModel['items']);
    unset($cartModel['itemsGroupedBySeller']);
    a($cartModel);
    ?>
</pre>
</body>
</html>